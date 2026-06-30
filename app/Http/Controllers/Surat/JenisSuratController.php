<?php

namespace App\Http\Controllers\Surat;

use App\Http\Controllers\Controller;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use App\Exports\JenisSuratTemplateExport;
use App\Imports\JenisSuratImport;
use Maatwebsite\Excel\Facades\Excel;

class JenisSuratController extends Controller
{
    public function index()
    {
        $jenisSurat = JenisSurat::latest()->get();
        return view('surat.jenis_surat.index', compact('jenisSurat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_klasifikasi' => 'required|string|unique:jenis_surat,kode_klasifikasi',
            'nama_jenis' => 'required|string|max:255',
            'format_nomor' => 'required|string',
        ]);

        JenisSurat::create($request->all());

        return redirect()->back()->with('success', 'Jenis surat berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisSurat::findOrFail($id);

        $request->validate([
            'kode_klasifikasi' => 'required|string|unique:jenis_surat,kode_klasifikasi,' . $id,
            'nama_jenis' => 'required|string|max:255',
            'format_nomor' => 'required|string',
        ]);

        $jenis->update($request->all());

        return redirect()->back()->with('success', 'Jenis surat berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jenis = JenisSurat::findOrFail($id);
        $jenis->delete();

        return redirect()->back()->with('success', 'Jenis surat berhasil dihapus!');
    }

    public function downloadTemplate()
    {
        return Excel::download(new JenisSuratTemplateExport, 'template_jenis_surat.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new JenisSuratImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Data klasifikasi surat berhasil di-import!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors($errorMessages)->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunduh data. Pastikan format kolom sesuai template.');
        }
    }
}