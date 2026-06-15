Berikut adalah dokumentasi panduan lengkap proses instalasi (*deployment*) aplikasi **SIAS AKADEMIK** di PC Server menggunakan **Windows + WSL 2 + Docker Desktop**, lengkap dari instalasi dependensi, migrasi database, hingga data Wilayah Indonesia.

Anda bisa langsung menyalin (*copy-paste*) seluruh teks di bawah ini ke dalam file **`README.md`** proyek Anda.

---

```markdown
# 🚀 Panduan Deployment SIAS AKADEMIK (PC Server - WSL 2 & Docker)

Panduan ini memuat langkah-langkah lengkap untuk memasang dan menjalankan aplikasi **SIAS AKADEMIK** di lingkungan PC Server lokal (On-Premise) menggunakan Windows, WSL 2, Docker Desktop, dan Laravel Sail.

---

## 📋 Prasyarat di PC Server
Sebelum memulai, pastikan PC Server (Windows 10/11) telah terpasang perangkat lunak berikut:
1. **WSL 2 (Windows Subsystem for Linux)** dengan distro **Ubuntu**.
2. **Docker Desktop** (Pastikan opsi *WSL Integration* untuk distro Ubuntu telah aktif di setelan Docker).
3. **Git** (Opsional, untuk klon repositori).

---

## 🛠️ Langkah-Langkah Instalasi

### 1. Persiapan Source Code di WSL
Buka terminal **Ubuntu WSL** di PC Server, lalu buat direktori proyek dan masuk ke dalamnya:
```bash
git clone <URL_REPOSITORI_ANDA> ~/sias-akademik
cd ~/sias-akademik

```

### 2. Konfigurasi Environment (`.env`)

Salin file konfigurasi `.env.example` menjadi `.env`:

```bash
cp .env.example .env

```

Buka file `.env` menggunakan teks editor (`nano .env`) dan sesuaikan beberapa nilai kunci berikut untuk mode produksi lokal:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://<IP_ADDRESS_PC_SERVER_ANDA>

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=sias_akademik
DB_USERNAME=sail
DB_PASSWORD=password_aman_anda

```

### 3. Instalasi Vendor/Dependency Awal

Jika folder `vendor` belum tersedia, unduh *dependency* PHP menggunakan kontainer Docker pembantu sementara:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs --no-scripts

```

*(Catatan: Sesuaikan teks `php83` dengan versi PHP yang Anda gunakan).*

### 4. Menyalakan Docker Container (Laravel Sail)

Nyalakan seluruh layanan Docker (Web Server, Database, dll) di latar belakang (*detached mode*):

```bash
./vendor/bin/sail up -d

```

### 5. Inisialisasi Aplikasi & Generate Key

Generate kunci pengaman aplikasi Laravel Anda:

```bash
./vendor/bin/sail artisan key:generate

```

---

## 🗄️ Migrasi Database & Data Wilayah Indonesia

Ikuti urutan perintah berikut di dalam terminal WSL untuk mengonfigurasi database terstruktur beserta seluruh data regional Indonesia:

### 1. Migrasi Struktur Tabel Utama

Buat seluruh struktur tabel inti sistem (User, Role, Semester, Kontak, dll):

```bash
./vendor/bin/sail artisan migrate

```

### 2. Publikasi & Migrasi Tabel Wilayah Indonesia

Publikasikan aset/database bawaan dari package Wilayah Indonesia (`aziswap/laravel-indonesia`), lalu jalankan kembali migrasinya:

```bash
./vendor/bin/sail artisan indonesia:publish
./vendor/bin/sail artisan migrate

```

### 3. Proses Seeding (Pengisian Data)

Masukkan data Wilayah Indonesia (Provinsi, Kabupaten, Kecamatan, Desa) beserta data master bawaan aplikasi Anda:

```bash
# Mengisi database dengan >80.000 data wilayah se-Indonesia (Memakan waktu beberapa menit)
./vendor/bin/sail artisan indonesia:seed

# Mengisi data master bawaan seperti Akun Admin default & Master Role
./vendor/bin/sail artisan db:seed

```

---

## ⚡ Optimasi Mode Produksi (Production)

Jalankan rangkaian perintah berikut untuk mengunci cache aplikasi demi performa terbaik dan melakukan *build* pada aset antarmuka (Vite & Tailwind CSS):

```bash
# Kunci cache konfigurasi, rute, dan tampilan view
./vendor/bin/sail artisan config:cache
./vendor/bin/sail artisan route:cache
./vendor/bin/sail artisan view:cache

# Install & kompilasi aset front-end
./vendor/bin/sail npm install
./vendor/bin/sail npm run build

```

---

## 🌐 Membuka Akses ke Jaringan Lokal (Wi-Fi / LAN)

Agar PC/Laptop lain yang berada di jaringan lokal dapat mengakses sistem ini, Anda harus membuka jalur port jaringan di Windows Firewall PC Server.

1. Buka **PowerShell (Run as Administrator)** di PC Server Windows.
2. Jalankan perintah berikut untuk mengizinkan lalu lintas data masuk melalui **Port 80**:
```powershell
New-NetFirewallRule -DisplayName "SIAS AKADEMIK WSL Port 80" -Direction Inbound -LocalPort 80 -Protocol TCP -Action Allow

```


3. Cari tahu IP lokal PC Server Anda dengan mengetik `ipconfig` di Command Prompt Windows (Contoh fiktif: `192.168.1.50`).

### 📱 Cara Mengakses Aplikasi

Buka browser di perangkat lain yang tersambung ke Wi-Fi/LAN yang sama dengan PC Server, lalu ketik:

```text
http://<IP_ADDRESS_PC_SERVER_ANDA>
Contoh: [http://192.168.1.50](http://192.168.1.50)

```

---

🔧 *Catatan: Jika ada pembaruan kode di kemudian hari, pastikan untuk selalu menjalankan `./vendor/bin/sail artisan view:clear` dan `./vendor/bin/sail npm run build` ulang di terminal WSL server.*

```

```