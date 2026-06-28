<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use ZipArchive;

class BackupController extends Controller
{
    /**
     * Mengambil disk storage lokal yang digunakan.
     */
    protected function getDisk()
    {
        return Storage::disk('local');
    }

    /**
     * Mengambil nama folder backup sesuai konfigurasi Spatie atau default.
     */
    protected function getBackupName()
    {
        return config('backup.backup.name') ?? 'backup-sekolah-pgsql';
    }

    /**
     * 1. Tampilkan Halaman Utama / Daftar File Backup
     */
    public function index()
    {
        $disk = $this->getDisk();
        $backupName = $this->getBackupName();

        // Pastikan folder backup sudah terbentuk di storage lokal
        if (!$disk->exists($backupName)) {
            $disk->makeDirectory($backupName);
        }

        $files = $disk->allFiles($backupName);

        $backupList = [];
        foreach ($files as $file) {
            if (substr($file, -4) == '.zip') {
                $backupList[] = [
                    'file_name' => str_replace($backupName . '/', '', $file),
                    'file_size' => round($disk->size($file) / 1024 / 1024, 2) . ' MB',
                    'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
                    'full_path' => $file
                ];
            }
        }

        // Urutkan dari yang paling baru di atas
        $backupList = array_reverse($backupList);

        return view('master.backup.index', compact('backupList'));
    }

    /**
     * 2. Proses Membuat Backup Baru (Mencakup DB + Semua File Foto/Dokumen)
     */
    public function create()
    {
        try {
            // Kita hapus '--only-db' => true agar file foto di storage ikut terangkut masuk ke dalam ZIP
            Artisan::call('backup:run'); 
            
            return redirect()->back()->with('success', 'Database dan seluruh file foto berhasil di-backup!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * 3. Download File Backup .zip ke Komputer/Laptop
     */
    public function download($fileName)
    {
        $backupName = $this->getBackupName();
        $path = $backupName . '/' . $fileName;

        if ($this->getDisk()->exists($path)) {
            return $this->getDisk()->download($path);
        }

        return redirect()->back()->with('error', 'File backup tidak ditemukan.');
    }

    /**
     * 4. Handler Utama: Menerima Upload File .zip & Jalankan Proses Restore
     */
    public function uploadRestore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:51200', // Batas maksimal 50 MB
        ]);

        if ($request->file('backup_file')->isValid()) {
            $file = $request->file('backup_file');
            
            // Panggil fungsi eksekusi inti di bawah
            $result = $this->executePostgresRestore($file->path());

            if ($result === true) {
                return redirect()->back()->with('success', 'Database dan file foto berhasil di-restore! Seluruh menu telah diperbarui.');
            } else {
                return redirect()->back()->with('error', $result);
            }
        }

        return redirect()->back()->with('error', 'File upload tidak valid.');
    }

    /**
     * 5. Fungsi Inti: Ekstraksi ZIP, Restore File Foto, Kosongkan DB Lama & Suntik SQL Baru
     */
    private function executePostgresRestore($zipPath)
    {
        $zip = new ZipArchive;
        $extractPath = storage_path('app/backup-extract-temp');
        
        // Ekstrak paket backup ZIP ke folder temporer
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return 'Gagal mengekstrak file zip.';
        }

        // --- PROSES A: RESTORE FILE FOTO & DOKUMEN ---
        // Mencari struktur folder storage tiruan bawaan Spatie Backup di dalam zip
        $extractedStoragePath = $extractPath . '/storage/app/public';
        
        if (File::exists($extractedStoragePath)) {
            $destinationPath = storage_path('app/public');
            // Salin (overwrite) seluruh folder foto/aset hasil ekstrak ke folder storage utama aplikasi
            File::copyDirectory($extractedStoragePath, $destinationPath);
        }

        // --- PROSES B: RESTORE DATABASE POSTGRESQL ---
        // Cari file .sql secara fleksibel (di sub-folder db-dumps maupun di root folder ekstrak)
        $sqlFile = null;
        if (file_exists($extractPath . '/db-dumps')) {
            $extractedFiles = glob($extractPath . '/db-dumps/*.sql');
            if (!empty($extractedFiles)) {
                $sqlFile = $extractedFiles[0];
            }
        }
        
        if (!$sqlFile) {
            $extractedFiles = glob($extractPath . '/*.sql');
            if (!empty($extractedFiles)) {
                $sqlFile = $extractedFiles[0];
            }
        }

        if (!$sqlFile) {
            $this->cleanTempFolder($extractPath);
            return 'File database .sql tidak ditemukan di dalam paket ZIP.';
        }
        
        // Ambil kredensial database PostgreSQL aktif dari konfigurasi Laravel (.env)
        $dbPassword = config('database.connections.pgsql.password');
        $dbHost = config('database.connections.pgsql.host');
        $dbUser = config('database.connections.pgsql.username');
        $dbName = config('database.connections.pgsql.database');

        // LANGKAH CRITICAL: Hancurkan skema public lama dan buat baru agar database bersih dari tabel-tabel lama.
        // Ini menghindari error "relation already exists" atau konflik data unik.
        $dropCommand = sprintf(
            'PGPASSWORD="%s" psql -h "%s" -U "%s" -d "%s" -c "DROP SCHEMA public CASCADE; CREATE SCHEMA public;"',
            $dbPassword, $dbHost, $dbUser, $dbName
        );
        $dropProcess = Process::fromShellCommandline($dropCommand);
        $dropProcess->run();

        // SUNTIK DATA: Jalankan eksekusi file .sql baru ke database kosong menggunakan parameter --file
        $command = sprintf(
            'PGPASSWORD="%s" psql -h "%s" -U "%s" -d "%s" --file="%s" 2>&1',
            $dbPassword, $dbHost, $dbUser, $dbName, $sqlFile
        );

        $process = Process::fromShellCommandline($command);
        $process->run();

        // Bersihkan folder temporer hasil ekstraksi agar tidak menimbun sampah storage
        $this->cleanTempFolder($extractPath);

        // DETEKSI ERROR FATAL: Jika sistem psql melempar sinyal tidak sukses atau ada error fatal database
        if (!$process->isSuccessful() || str_contains($process->getOutput(), 'FATAL:')) {
            dd([
                'STATUS' => 'POSTGRESQL MENOLAK PROSES RESTORE SANGAT FATAL',
                'PESAN_ERROR_MURNI' => $process->getOutput() ?: $process->getErrorOutput(),
                'HOST_YANG_DIPAKAI' => $dbHost,
                'USER_YANG_DIPAKAI' => $dbUser,
                'DATABASE_YANG_DIPAKAI' => $dbName,
                'PERINTAH_SISTEM' => $command
            ]);
        }

        // Hapus cache aplikasi total agar menu, session lama, atau view ter-refresh instan
        Cache::flush();
        Artisan::call('cache:clear');

        return true;
    }

    /**
     * 6. Fungsi Pembersih Folder Temporer Menggunakan Laravel File Helper
     */
    private function cleanTempFolder($path)
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }
    }
}