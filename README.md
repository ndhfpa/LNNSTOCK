# LNNSTOCK — Warehouse Management System (WMS)
> **Tagline:** *"Manage Smarter, Move Better."*  
> **Klien / Studi Kasus:** PT Riyana Cipta Pangan Indonesia (Produsen Happy Jelly, Happy Time, Cookies Time — Cirebon)  
> **Institusi:** STMIK IKMI Cirebon &bull; Tugas Akhir Kelompok Pemrograman Web Dasar &bull; Semester 3

---

## 1. Profil Tim Pengembang & Pembagian Tugas (Role Ownership)

Setiap berkas kode di dalam proyek ini dibungkus dengan komentar kepemilikan baku:
- **PHP / JS:** `// bagian <nama anggota> start` ... `// bagian <nama anggota> end`
- **CSS:** `/* bagian <nama anggota> start */` ... `/* bagian <nama anggota> end */`
- **HTML / View:** `<!-- bagian <nama anggota> start -->` ... `<!-- bagian <nama anggota> end -->`
- **SQL:** `-- bagian <nama anggota> start` ... `-- bagian <nama anggota> end`

| Nama Anggota | Modul & Tanggung Jawab | Berkas Utama |
| :--- | :--- | :--- |
| **Nadhifatul Hilmy NS** | **Manajemen Depo & Denah Rak Multi-Tier** | `models/Depo.php`, `models/Rak.php`, `controllers/DepoController.php`, `controllers/RakController.php`, `views/depo/*`, `views/rak/*`, `assets/css/modul-depo.css`, `assets/js/depo.js` |
| **Lutfi Firmansyah** | **Katalog Produk FMCG & Buffer Stock** | `models/Produk.php`, `controllers/ProdukController.php`, `views/produk/*`, `assets/css/modul-produk.css`, `assets/js/produk.js`, `uploads/produk/` |
| **Nurul Khoyimah** | **Monitoring Kedaluwarsa (FEFO) & Mutasi Antar-Depo** | `models/Batch.php`, `models/Mutasi.php`, `controllers/BatchController.php`, `controllers/MutasiController.php`, `views/kedaluwarsa/*`, `views/mutasi/*`, `assets/css/modul-mutasi.css`, `assets/js/mutasi.js`, `assets/js/kedaluwarsa.js` |
| **Bagian Bersama** *(Tim Teknis)* | **Arsitektur Inti, Auth, Dashboard, Router & Layout** | `core/*` (`Database.php`, `Model.php`, `Controller.php`, `AuthGuard.php`, `helpers.php`), `models/User.php`, `controllers/AuthController.php`, `controllers/DashboardController.php`, `views/layouts/*`, `views/auth/login.php`, `views/dashboard/*`, `public/index.php`, `assets/css/style.css`, `assets/js/app.js`, `database.sql`, `README.md` |

---

## 2. Landasan Arsitektur: Native MVC & Konsep OOP

Aplikasi dibangun murni menggunakan **PHP Native 8.x** tanpa dependensi framework (tanpa Laravel/CI) dan tanpa library CSS/JS (tanpa Bootstrap/Tailwind/jQuery) untuk memenuhi standar pembelajaran fundamental semester 3.

```
       [ Pengguna / Browser ]
                 |
                 v
         public/index.php (Front Controller & Whitelist Router)
                 |
       +---------+---------+
       |                   |
       v                   v
[ AuthGuard.php ]   [ Controller (Abstract) ]
                           |
            +--------------+--------------+
            |                             |
            v                             v
     [ Model (PDO) ]               [ View (HTML5) ]
            |                             |
            v                             v
  [ MySQL: db_lnnstock ]           [ Layar Browser ]
```

### Penjelasan 3 Pilar OOP pada Kode LNNSTOCK:
1. **Inheritance (Pewarisan):**
   - Seluruh model (`User`, `Depo`, `Rak`, `Produk`, `Batch`, `Mutasi`) mewarisi kelas induk `core/Model.php` (`class Produk extends Model`).
   - Seluruh controller mewarisi kelas abstrak `core/Controller.php` (`class ProdukController extends Controller`), sehingga dapat menggunakan fungsi bersama seperti `view()`, `redirect()`, `flash()`, dan `verifyCsrf()`.
2. **Encapsulation (Pembungkusan / Enkapsulasi):**
   - Properti koneksi `$db` di kelas `Model` dideklarasikan `protected` agar terlindungi dari modifikasi liar luar kelas.
   - Properti instans PDO di `Database.php` bersifat `private static ?PDO $instance` dan hanya dapat diakses melalui metode publik `Database::connect()`.
3. **Abstraction (Abstraksi):**
   - Kelas `core/Controller.php` dan `core/Model.php` dideklarasikan sebagai `abstract class`, mendefinisikan kerangka perilaku standar yang harus dipatuhi oleh setiap modul turunan tanpa membebani rincian teknis di tingkat atas.

---

## 3. Struktur Direktori Proyek

```
LNNSTOCK/
├── _arsip_percobaan1/         # Arsip percobaan lama (bersih & aman)
├── core/
│   ├── Database.php          # Koneksi Singleton PDO (ERRMODE_EXCEPTION, Emulate = false)
│   ├── Model.php             # Abstract class induk seluruh model
│   ├── Controller.php        # Abstract class induk seluruh controller (view, csrf, flash)
│   ├── AuthGuard.php         # Penjaga gerbang sesi autentikasi pengguna
│   ├── Permission.php        # Matriks hak akses berbasis role (RBAC)
│   └── helpers.php           # Sanitasi e(), format_date_id(), base_url() dinamis, flash()
├── models/
│   ├── User.php              # Akun & otentikasi BCRYPT, CRUD pengguna
│   ├── Depo.php              # Fasilitas gudang & kapasitas
│   ├── Rak.php               # Slot rak multi-tier & kalkulasi okupansi
│   ├── Produk.php            # Master SKU FMCG & status buffer stock
│   ├── Batch.php             # Lot stok berbasis kedaluwarsa FEFO & FIFO
│   └── Mutasi.php            # Transaksi mutasi antar-depo ACID
├── controllers/
│   ├── AuthController.php      # Login dan Logout POST + CSRF
│   ├── DashboardController.php # KPI logistik & ringkasan operasional
│   ├── DepoController.php      # CRUD fasilitas gudang
│   ├── RakController.php       # CRUD dan visualisasi matriks rak
│   ├── ProdukController.php    # CRUD master produk & upload gambar
│   ├── BatchController.php     # CRUD batch stok & filter FEFO
│   ├── MutasiController.php    # Penerbitan SPM & eksekusi mutasi stok
│   ├── PenggunaController.php  # CRUD manajemen pengguna (khusus Kepala Gudang)
│   └── ProfilController.php    # Pengaturan akun sendiri (nama lengkap & ganti sandi)
├── views/
│   ├── layouts/              # header.php, sidebar.php, topbar.php, footer.php
│   ├── auth/login.php        # Halaman masuk sistem & kartu akun uji
│   ├── dashboard/index.php   # Tampilan dashboard utama
│   ├── depo/                 # index.php, create.php, edit.php
│   ├── rak/                  # index.php, create.php, edit.php
│   ├── produk/               # index.php, create.php, edit.php
│   ├── kedaluwarsa/          # index.php, create.php, edit.php
│   ├── mutasi/               # index.php, create.php
│   ├── pengguna/             # index.php, create.php, edit.php
│   └── profil/               # index.php (Akun Saya)
├── public/
│   ├── index.php             # Router utama whitelist
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css         # Tema krem #F2EEE3, card, button, modal, tabel
│   │   │   ├── modul-depo.css    # Grid matriks rak & strip okupansi
│   │   │   ├── modul-produk.css  # Thumbnail SKU & chip kategori
│   │   │   └── modul-mutasi.css  # Live stock simulator & FEFO banner
│   │   ├── js/
│   │   │   ├── app.js            # Mobile drawer, modal hapus universal, toast auto-fade
│   │   │   ├── depo.js           # Live search depo, filter tab zona, modal detail rak
│   │   │   ├── produk.js         # Live filter produk & peringatan buffer kritis
│   │   │   ├── mutasi.js         # Real-time Live Stock Simulator antar-depo
│   │   │   └── kedaluwarsa.js    # Perhitungan sisa hari FEFO & auto-calc expired
│   │   └── img/
│   │       └── logo-lnnstock.svg # Logo resmi vektor LNNSTOCK
│   └── uploads/
│       └── produk/           # Direktori penyimpanan foto produk
├── database.sql              # Skema tabel & seed data tanggal relatif (CURDATE)
└── README.md                 # Panduan instalasi, pengujian & demonstrasi
```

---

## 4. Panduan Instalasi & Menjalankan Aplikasi

### A. Konfigurasi Database (MySQL / MariaDB)
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin/`) atau terminal MySQL.
2. Buat database atau langsung impor berkas `database.sql`:
   ```bash
   mysql -u root -p < database.sql
   ```
   *Seluruh tabel (`users`, `depo`, `rak`, `produk`, `batch_stok`, `mutasi`) dan data seed otomatis dibuat dengan tanggal relatif `CURDATE()`.*

3. Konfigurasi kredensial koneksi terdapat di `core/Database.php`:
   - **Host:** `127.0.0.1`
   - **Database:** `db_lnnstock`
   - **User:** `root`
   - **Password:** `''` *(kosong standar XAMPP)*

### B. Menjalankan Server Web
Aplikasi mendukung 2 metode eksekusi:

* **Opsi 1: Lewat XAMPP (Apache)**
  1. Letakkan folder proyek di `C:/xampp/htdocs/LNNSTOCK` (atau `C:/xampp3/htdocs/LNNSTOCK`).
  2. Buka browser ke alamat:  
     **`http://localhost/LNNSTOCK/public/`**

* **Opsi 2: Lewat PHP Built-in Server**
  1. Buka terminal di folder root `LNNSTOCK`:
     ```bash
     php -S localhost:8000 -t public
     ```
  2. Buka browser ke alamat:  
     **`http://localhost:8000`**

### C. Akun Login Bawaan & Akun Uji (3 Role)

Sistem telah dilengkapi 3 akun bawaan dengan kata sandi ter-enkripsi BCRYPT (`database.sql` dan database aktif):

| Username | Password | Nama Lengkap | Role / Hak Akses | Keterangan |
| :--- | :--- | :--- | :--- | :--- |
| `admin01` | `Admin123!` | **Agung Prayitno** | **Kepala Gudang** | Hak penuh: seluruh modul + Kelola Pengguna |
| `staffGD2` | `LnnstockGD2` | **Budi Santoso** | **Petugas Gudang** | Operasional fisik: Depo, Rak, Produk, Kedaluwarsa (Read, Create, Edit) |
| `staffDB3` | `LnnstockDB3` | **Citra Dewi** | **Staff Distribusi** | Logistik keluar/masuk: Mutasi (Read, Create, Edit), Produk & FEFO (Read) |

*Ketiga akun di atas juga ditampilkan langsung pada kartu bantuan di halaman Login untuk kemudahan pengujian.*

---

## 5. Matriks Hak Akses Berbasis Role (RBAC) & Manajemen Akses

Sistem menggunakan kontrol akses tersentralisasi pada `core/Permission.php` dan divalidasi langsung di router `public/index.php` setelah `AuthGuard::check()`. Jika peran pengguna tidak memiliki izin ke halaman/aksi yang dituju, sistem otomatis menetapkan pesan flash error *"Anda tidak punya akses ke halaman tersebut."* (tampil sebagai notifikasi toast) dan me-redirect ke `?page=dashboard`.

| Modul / Halaman | Aksi (`action`) | Kepala Gudang | Petugas Gudang | Staff Distribusi |
| :--- | :--- | :---: | :---: | :---: |
| **Autentikasi (Login / Logout)** | `index`, `login`, `logout` | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| **Dashboard** | `index` | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| **Edit Profil ("Akun Saya")** | `index`, `update_profil`, `update_password` | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| **Fasilitas Depo** | `index`, `create`, `edit` | :white_check_mark: | :white_check_mark: | :x: |
| | `delete` | :white_check_mark: | :x: | :x: |
| **Denah & Matriks Rak** | `index`, `create`, `edit` | :white_check_mark: | :white_check_mark: | :x: |
| | `delete` | :white_check_mark: | :x: | :x: |
| **Katalog Master Produk** | `index` | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| | `create`, `edit` | :white_check_mark: | :white_check_mark: | :x: |
| | `delete` | :white_check_mark: | :x: | :x: |
| **Monitoring Kedaluwarsa (FEFO)** | `index` | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| | `create`, `edit` | :white_check_mark: | :white_check_mark: | :x: |
| | `delete` | :white_check_mark: | :x: | :x: |
| **Mutasi Antar-Depo** | `index`, `create`, `edit` | :white_check_mark: | :x: | :white_check_mark: |
| | `selesai` / `complete`, `batal` / `cancel`, `delete` | :white_check_mark: | :x: | :x: |
| **Kelola Pengguna** (`?page=pengguna`) | `index`, `create`, `edit`, `delete` | :white_check_mark: | :x: | :x: |

> [!NOTE]
> Menu sidebar, tombol aksi (**+ Tambah**, **Edit**, **Hapus**, **Setujui**, **Batalkan**), dan kartu navigasi cepat disembunyikan secara kondisional menggunakan `Permission::can(...)` sesuai hak akses pengguna yang sedang aktif.

---

## 6. Panduan Fitur Revisi Terbaru

### A. Edit Profil ("Akun Saya")
1. Pengguna dari semua role yang sudah login dapat mengklik **chip profil nama pengguna** di topbar kanan atas untuk membuka halaman `?page=profil`.
2. **Ubah Nama Lengkap:** Masukkan nama baru lalu klik Simpan. Nama di sesi dan tampilan topbar akan langsung diperbarui secara instan.
3. **Ganti Kata Sandi:** Wajib memasukkan kata sandi lama (diverifikasi dengan `password_verify`), kata sandi baru (minimal 8 karakter), dan konfirmasi kata sandi baru.
4. **Proteksi:** Username dan Role bersifat informatif (*readonly*) dan tidak dapat diubah oleh pengguna sendiri.

### B. Kelola Pengguna (`?page=pengguna`)
Khusus role **Kepala Gudang**, tersedia menu baru **Pengguna Sistem** di navigasi sidebar:
- **Tabel Daftar Pengguna:** Menampilkan username, nama lengkap, badge role dengan kode warna, serta kolom aksi edit dan hapus.
- **Pencarian Live:** Saring pengguna secara instan tanpa reload browser.
- **Tambah / Edit Pengguna:** Validasi username unik, dropdown 3 role, kata sandi minimal 8 karakter (wajib saat tambah, opsional saat edit bila tidak ingin diganti).
- **Proteksi Integritas Data:**
  - Akun yang sedang login tidak dapat menghapus dirinya sendiri.
  - Sistem melarang penghapusan atau penurunan role jika akun tersebut merupakan satu-satunya Kepala Gudang yang tersisa.
  - Pengguna yang sudah pernah menerbitkan dokumen mutasi stok dilindungi dari penghapusan (akan muncul peringatan ramah).

### C. Ikon Mata Sandi (Show/Hide Password)
Setiap formulir yang memuat input kata sandi (Halaman Login, Form Pengguna, dan Form Ganti Sandi Profil) telah dilengkapi tombol ikon mata interaktif berbasis SVG murni:
- Klik ikon mata untuk melihat karakter sandi (`type="text"`).
- Klik kembali untuk menyembunyikan sandi (`type="password"`).
- Dijalankan dengan JavaScript murni (`public/assets/js/app.js`) tanpa library eksternal.

---

## 7. Fitur Unggulan Sistem LNNSTOCK

1. **Dashboard KPI Real-Time:** 4 kartu metrik utama, sapaan dinamis waktu, panel peringatan dini kedaluwarsa, dan status okupansi seluruh fasilitas gudang.
2. **Denah Rak & Matriks Multi-Tier:** Visualisasi kartu rak berbasis CSS Grid dengan status warna (Tersedia hijau, Optimal kuning, Penuh merah, Maintenance abu-abu), tab filter per zona, serta modal interaktif rincian lot barang.
3. **Katalog Master FMCG & Buffer Stock:** Monitoring buffer minimum otomatis (Aman, Menipis, Kritis) tanpa input stok manual (stok dihitung agregat dari batch fisik), dilengkapi upload gambar tervalidasi MIME type `finfo`.
4. **Monitoring Kedaluwarsa Berbasis FEFO:** Penomoran lot, notifikasi batch kritis (&le; 30 hari atau lewat batas), dan tombol pintas alokasi mutasi.
5. **Live Stock Simulator pada Mutasi Antar-Depo:** Form SPM interaktif yang mengkalkulasi dampak kuota depo asal dan depo tujuan secara real-time di sisi peramban sebelum transaksi disetujui.
6. **Integritas Transaksi ACID:** Eksekusi penyelesaian mutasi menerapkan `beginTransaction()`, `SELECT ... FOR UPDATE`, serta `commit()`/`rollBack()` untuk menjamin keakuratan stok antar-gudang tanpa *race condition*.
7. **Keamanan Berlapis:** Sanitasi output `e()` terhadap XSS, verifikasi token CSRF `hash_equals()` pada seluruh request mutasi/hapus via POST, password hashing BCRYPT, dan penanganan ramah SQLSTATE 23000 saat penghapusan data berelasi.

---

## 8. Skenario "Alur Demo 5 Menit" (Panduan Presentasi Dosen)

Urutan demonstrasi yang runut dan mengesankan untuk dipresentasikan dalam waktu singkat:

1. **Menit 1: Login & Dashboard Eksekutif (`?page=dashboard`)**
   - Tunjukkan tampilan login bersih, masuk dengan `admin01` / `Admin123!`.
   - Jelaskan sapaan ramah sesuai waktu, 4 kartu KPI, badge lonceng di topbar, dan sidebar terang krem `#F2EEE3` yang sesuai prototype industri.
2. **Menit 2: Manajemen Fasilitas & Denah Rak (`?page=rak&depo=3`)**
   - Masuk ke menu **Denah Rak**. Pilih Gudang Produk Jadi Cirebon (`GD003`).
   - Tunjukkan kartu rak dengan indikator warna (Rak `A-01` merah karena &gt;90% penuh, Rak `A-02` kuning, Rak `B-02` hijau).
   - Klik kartu **Rak A-01** untuk memunculkan **Modal Detail Layout Rak** yang menampilkan rincian batch Happy Jelly Strawberry beserta masa simpannya.
3. **Menit 3: Katalog Produk & Peringatan Buffer (`?page=produk`)**
   - Buka menu **Katalog Produk**. Tunjukkan banner merah peringatan otomatis: stok *Happy Time Vanilla Roll* berada di bawah buffer minimum.
   - Uji kotak pencarian langsung (ketik `Jelly` atau `Vanilla`), tabel menyaring baris secara instan tanpa memuat ulang halaman.
4. **Menit 4: Monitoring Kedaluwarsa FEFO (`?page=kedaluwarsa`)**
   - Buka menu **Monitoring Kedaluwarsa**. Tunjukkan kartu prinsip FEFO & FIFO.
   - Perhatikan baris batch teratas yang mendekati kedaluwarsa (&le; 18 hari).
   - Klik tombol **"Alokasikan ke Mutasi"** pada banner merah untuk langsung mengalirkan batch tersebut ke form mutasi.
5. **Menit 5: Mutasi Stok & Live Simulator (`?page=mutasi&action=create`)**
   - Di form mutasi, pilih depo penerima (misal: *GD004 Gudang Distribusi*).
   - Ketik jumlah karton (misal: `100` atau klik tombol **Maks**).
   - Tunjukkan **Live Stock Simulator** di bagian bawah yang angkanya langsung berganti real-time memperlihatkan sisa stok depo asal dan kuota depo tujuan.
   - Klik **"Simpan & Terbitkan Surat Jalan"**, lalu di tabel riwayat klik tombol hijau **"Setujui"**.
   - Kembali ke Dashboard / Denah Rak: tunjukkan stok dan okupansi rak telah berubah secara otomatis berkat transaksi database ACID!
   - Akhiri sesi dengan mengklik tombol **"Keluar Sesi"** di pojok kiri bawah.

