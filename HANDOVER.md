# DOKUMEN HANDOVER & PANDUAN DEPLOYMENT
## Proyek: E-Voting Pilketos (Laravel 10)
**Tanggal Handover:** 13 Oktober 2026  
**Repository Git:** `https://github.com/naufal1122/evoting-pilketos.git`  
**Branch Aktif & Tersinkron:** `main` dan `upgrade-laravel-10` (Commit: `d425a48`)

---

## 1. RINGKASAN PROGRESS & PERUBAHAN SISTEM (CHRONOLOGICAL PROGRESS)

### A. Upgrade Framework & Lingkungan Kerja
- Berhasil upgrade dari **Laravel 7 ke Laravel 10** dengan **PHP 8.1+**.
- Database migrated ke MySQL (`evoting_2026`).
- Standarisasi PSR-4 Seeder (`database/seeders/DatabaseSeeder.php`, `AdminUserSeeder.php`).
- Seluruh 18 Automated Unit & Feature Test lulus 100% (`php artisan test`).

### B. Arsitektur & Refaktorisasi Kode (Pemisahan Controller & Service Layer)
Sistem telah dipisahkan agar Controller hanya menangani request/response (orchestration), sementara transaksi database dan domain logic berada di Service layer:
1. **`app/Services/VotingService.php`**:
   - Menangani proses voting dengan transaksi database `DB::transaction`.
   - Menggunakan **Pessimistic Row Locking** (`User::lockForUpdate()`) untuk mencegah *race condition* / double-voting.
   - Pengecekan status jadwal pemilihan dan penutupan otomatis.
   - Respon kiosk countdown 3 detik auto-logout.
2. **`app/Services/PemilihService.php`**:
   - Menangani registrasi siswa manual & import Excel (`Maatwebsite/Laravel-Excel`).
   - Format template Excel dibuat simpel: cukup kolom **Nama** dan **Kelas**. Username otomatis di-generate dari gabungan 2 kata pertama nama siswa (contoh: "Dewa Naufal Arkan" -> `dewanaufal`).
   - Filter pemilih berdasarkan kelas dan pencarian real-time via AJAX.
3. **`app/Services/PaslonService.php`**:
   - CRUD data calon ketua OSIS.
   - Sanitasi upload nama file foto ketua dan pembersihan berkas lama saat update/delete.
   - Menonaktifkan atribut wakil paslon (field wakil dibuat nullable di migrasi DB).
4. **`app/Services/HasilVoteService.php`**:
   - Perhitungan rekapitulasi suara paslon, persentase kehadiran, dan golput.
   - Logika penutupan resmi pemilihan dan fitur reset data suara.
5. **Controllers (PascalCase)**:
   - [`AdminController.php`](file:///c:/laragon/www/evoting-pilketos/app/Http/Controllers/AdminController.php)
   - [`PemilihController.php`](file:///c:/laragon/www/evoting-pilketos/app/Http/Controllers/PemilihController.php) (menggantikan legacy `siswaController.php`).

### C. Standarisasi Naming Blade & View (kebab-case)
Semua blade view dan partials diubah ke konvensi standar kebab-case:
- `resources/views/admin/berita-acara.blade.php` (Cetak berita acara resmi dengan persentase partisipasi & golput).
- `resources/views/admin/live-count.blade.php` (Mode proyektor live real-time suara tanpa grafik yang berat).
- `resources/views/admin/hasil-vote.blade.php`
- `resources/views/admin/list-siswa.blade.php` & `partials/_list-siswa-table.blade.php`
- `resources/views/admin/register-siswa.blade.php`
- `resources/views/admin/import-siswa.blade.php`
- `resources/views/siswa/home.blade.php` & `detail.blade.php`

### D. Tampilan UI / UX (Neo-Skeuomorphism & Mobile Responsive)
- **Desain Neo-Skeuomorphism**: File styling global di [`public/css/neo-skeuo.css`](file:///c:/laragon/www/evoting-pilketos/public/css/neo-skeuo.css) memberikan efek pencahayaan bayangan timbul lembut (*raised*) dan tombol yang cekung saat ditekan (*sunken/pressed state*).
- **Favicon**: Diperbarui menggunakan [`public/img/logoss.png`](file:///c:/laragon/www/evoting-pilketos/public/img/logoss.png).
- **Penyesuaian Paslon Single (Tanpa Wakil)**: Semua form input, tabel, card pemilihan, dan detail paslon difokuskan ke Calon Ketua OSIS.
- **Tampilan HP (Smartphone Ready)**:
  - Form login responsif full width di mobile.
  - Card kandidat menyusun 1 kolom rapi (*single column stack*) di layar smartphone dengan ukuran foto proporsional.
  - Halaman detail paslon dilengkapi *floating action buttons* di bagian bawah (*thumb-friendly*).
  - Anti-spam voting: Tombol vote langsung di-disable dengan indikator status *"Menyimpan..."*.

---

## 2. KREDENSIAL DEFAULT

- **Akun Administrator**:
  - **Username:** `admin`
  - **Password:** `admin` (atau NIS sesuai konfigurasi seeder `AdminUserSeeder`)
- **Format Akun Siswa (Pemilih)**:
  - **Username:** 2 kata pertama nama (lowercase tanpa spasi, misal: `dewanaufal`)
  - **Password / NIS:** Nomor Induk Siswa (digunakan untuk login siswa di `/login`)

---

## 3. TUTORIAL INSTALASI & SETUP DARI AWAL (LOCAL LARAGON)

### Kebutuhan Sistem:
- PHP >= 8.1 (dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `zip`)
- Composer >= 2.x
- MySQL >= 5.7 / MariaDB >= 10.3

### Langkah-langkah di Lokal:
```bash
# 1. Clone repositori
git clone https://github.com/naufal1122/evoting-pilketos.git
cd evoting-pilketos

# 2. Install dependensi composer
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate Application Key
php artisan key:generate

# 5. Konfigurasi Database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306 (atau 3309 jika port Laragon kustom)
# DB_DATABASE=evoting_2026
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan seeder admin
php artisan migrate
php artisan db:seed

# 7. Jalankan unit test untuk memastikan semua fitur aman
php artisan test
```

---

## 4. TUTORIAL DEPLOYMENT KE HOSTING (CPANEL / RUMAHWEB)

### Metode A: Via SSH / Terminal cPanel (Disarankan)

1. **Clone ke Folder Domain/Subdomain**:
   Buka menu **Terminal** di cPanel:
   ```bash
   cd public_html
   # atau jika subdomain: cd voting.namasekolah.sch.id
   git clone https://github.com/naufal1122/evoting-pilketos.git .
   ```

2. **Install Composer**:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Buat Database & User di cPanel**:
   - Masuk ke menu **MySQL Databases** di cPanel.
   - Buat database baru (misal: `user_evoting`).
   - Buat user database baru dan hubungkan ke database dengan centang **ALL PRIVILEGES**.

4. **Konfigurasi `.env`**:
   Salin `.env.example` ke `.env` lalu edit via cPanel File Manager:
   ```env
   APP_NAME="E-Voting Pilketos"
   APP_ENV=production
   APP_KEY=base64:co9248TJ7m3eswPl21hPkezwgyz1XLq946u5NsanNhE=
   APP_DEBUG=false
   APP_URL=https://voting.namasekolah.sch.id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=user_evoting
   DB_USERNAME=user_dbuser
   DB_PASSWORD=password_db_anda
   ```

5. **Migrasi & Seeder Database**:
   Jalankan di Terminal cPanel:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Arahkan Document Root ke Folder `/public`**:
   - Buka menu **Domains** di cPanel.
   - Ubah Document Root domain/subdomain agar mengarah ke:  
     `/public_html/public` (atau `/voting.namasekolah.sch.id/public`).
   > *Catatan Keamanan*: Jangan arahkan root ke folder induk project demi keamanan file `.env`.

7. **Optimasi Performa Produksi (Wajib untuk 300 Pemilih)**:
   Jalankan perintah ini di Terminal cPanel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 5. REKOMENDASI OPERASIONAL HARI-H (UNTUK 300 PEMILIH)

1. **Jadwal Bergilir**:
   Jika menggunakan paket Shared Hosting (Rumahweb Unlimited M), disarankan membagi jadwal voting per 1–2 kelas bergantian (sekitar 30–50 siswa aktif per waktu) untuk menghindari batas *Entry Process* / Resource Limit (Error 508).
2. **Koneksi Jaringan**:
   Jika pemilih menggunakan Wi-Fi sekolah, pastikan Access Point sanggup menampung jumlah perangkat konkuren.
3. **Tutup Pemilihan**:
   Admin dapat mengontrol status buka/tutup pemilihan sewaktu-waktu di panel pengaturan dashboard atau otomatis mengikuti jadwal timer pemilihan.
