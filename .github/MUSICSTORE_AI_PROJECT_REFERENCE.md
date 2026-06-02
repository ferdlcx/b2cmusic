# 🎵 MusicStore — AI Project Reference Document
> **Sumber:** Software Requirements Specification (SRS) v1.0 — Kelompok 3 (26/05/2026)  
> **Tujuan dokumen ini:** Patokan wajib untuk AI saat membantu pengembangan proyek. Setiap keputusan teknis, fitur, dan desain HARUS merujuk ke dokumen ini. Jangan membuat asumsi di luar cakupan ini tanpa konfirmasi eksplisit dari tim.

---

## 📌 Ringkasan Proyek

| Atribut | Nilai |
|---|---|
| **Nama Sistem** | MusicStore |
| **Jenis Produk** | Website E-Commerce Toko Alat Musik |
| **Model Bisnis** | B2C (Business-to-Consumer) |
| **Versi SRS** | 1.0 |
| **Tim Pengembang** | Kelompok 3 |

**Deskripsi singkat:** Platform e-commerce berbasis web untuk toko alat musik yang memungkinkan pelanggan menelusuri, mencari, dan membeli alat musik serta aksesori secara online. Dilengkapi panel administrasi untuk manajemen produk, pesanan, dan laporan.

**Bukan termasuk scope:** Fitur lelang, marketplace multi-vendor, modul akuntansi lanjutan.

---

## 🏗️ Arsitektur & Tech Stack

### Backend
- **Runtime:** PHP 8.2+
- **Framework:** Laravel 11.x (LTS)
- **Database:** MySQL 8.0+
- **ORM:** Laravel Eloquent
- **Auth:** Laravel Sanctum (API/SPA) + Laravel Gates/Policies (RBAC)
- **Password hashing:** `Hash::make()` Laravel — bcrypt, cost factor >= 12
- **Queue/Cache (opsional):** Redis 7.x

### Frontend
- **Templating:** Blade (server-side rendering)
- **CSS:** Tailwind CSS
- **JS Interaktif:** Alpine.js atau Vue.js
- **Build Tool:** Laravel Vite
- **Design System:** Material Design 3 (mobile-first)

### Development Environment
- **OS:** Windows 10 64-bit (v21H2+)
- **Local Server:** Laragon 6.0+ (direkomendasikan) atau XAMPP 8.x
- **Web Server Lokal:** Apache 2.4 atau Nginx
- **Package Manager:** Composer 2.x (PHP), Node.js v20 LTS + npm/yarn
- **IDE:** VS Code atau PhpStorm
- **Version Control:** Git 2.x + GitHub/GitLab

### Client / Browser
| Komponen | Spesifikasi |
|---|---|
| Browser | Chrome 110+, Firefox 110+, Safari 16+, Edge 110+ |
| Platform | Desktop (Win/macOS/Linux) + Mobile (iOS 15+, Android 10+) |
| Resolusi | 320px (mobile) — 1920px+ (desktop), desain responsif wajib |
| Internet | Minimum 2 Mbps |

### Layanan Eksternal / Integrasi
| Layanan | Tujuan | Protokol |
|---|---|---|
| **Midtrans** (API v2) | Pembayaran online (VA, kartu kredit, e-wallet) | HTTPS / REST API |
| **RajaOngkir API** (v1) | Kalkulasi ongkos kirim real-time | HTTPS / REST API |
| **SendGrid / Mailgun** (v3) | Email transaksional (OTP, konfirmasi, notifikasi) | HTTPS / REST API |
| **AWS S3 / Cloudflare R2** | Penyimpanan & CDN gambar produk | HTTPS / SDK |
| **MySQL 8.0+** | Database utama | PDO via Eloquent |
| **Redis 7.x** (opsional) | Caching, session, queue | TCP via Laravel Cache |

> ⚠️ **Dependency kritis:** Kegagalan Midtrans = pemrosesan pembayaran terhenti. Kegagalan RajaOngkir = gunakan tarif flat default.

---

## 👥 Kelas Pengguna & Hak Akses

### 1. Pelanggan Tamu (Guest)
- Dapat menelusuri katalog dan melakukan checkout
- Data diinput manual, tidak ada riwayat tersimpan
- Tidak perlu login untuk browsing dan pembelian

### 2. Pelanggan Terdaftar (Customer)
- Akses ke: riwayat pesanan, wishlist, ulasan produk, checkout lebih cepat
- Karakteristik: literasi digital beragam, akses via desktop & mobile

### 3. Administrator Toko (Store Admin) — role: `admin`
- Akses ke panel administrasi back-end
- Kelola produk, pesanan, ulasan, laporan
- Karakteristik: terbiasa komputer dasar, akses jaringan internal

### 4. Super Administrator — role: `super_admin`
- Hak akses tertinggi
- Dapat: kelola akun admin, akses laporan finansial konsolidasi, konfigurasi sistem
- Satu-satunya role yang bisa menghapus akun admin lain

> ⚠️ Panel admin hanya bisa diakses role `admin` atau `super_admin`. Di produksi, akses dibatasi via IP whitelisting.

---

## ⚙️ Fitur Sistem & Kebutuhan Fungsional

### 4.1 Manajemen Akun Pengguna — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-01 | Form registrasi: nama lengkap, email, no. telepon, password (min. 8 karakter, mengandung huruf & angka) |
| FR-02 | Kirim email verifikasi otomatis setelah registrasi |
| FR-03 | Akun hanya aktif setelah email diverifikasi |
| FR-04 | Login dengan email + password |
| FR-05 | Fitur "Lupa Password" — kirim link reset ke email (berlaku 60 menit) |
| FR-06 | Kelola multiple alamat pengiriman (tambah/ubah/hapus/set default) |
| FR-07 | Edit profil: nama, nomor telepon, foto profil |
| FR-08 | Blokir login sementara 30 menit setelah 5x gagal dalam 10 menit |

---

### 4.2 Katalog & Pencarian Produk — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-09 | Struktur kategori hierarkis (contoh: Alat Musik > Gitar > Gitar Akustik) |
| FR-10 | Full-text search pada nama, deskripsi, merek — respons < 1 detik |
| FR-11 | Panel filter: rentang harga (slider), merek, kategori, rating minimum |
| FR-12 | Sorting: Relevansi, Terbaru, Terlaris, Harga Terendah/Tertinggi |
| FR-13 | Halaman detail produk: galeri foto (min. 1), nama, SKU, harga, stok, deskripsi, spesifikasi, rata-rata rating |
| FR-14 | Tampilkan produk terkait (related products) di bawah detail produk |
| FR-15 | Label "Stok Habis" + nonaktifkan tombol beli jika stok = 0 |

---

### 4.3 Keranjang Belanja — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-16 | Pengguna tamu & terdaftar bisa menambah produk ke keranjang |
| FR-17 | Isi keranjang pengguna terdaftar bertahan 30 hari (meski sesi berakhir) |
| FR-18 | Ubah kuantitas item (min. 1, maks. sesuai stok) |
| FR-19 | Hapus item individual atau kosongkan seluruh keranjang |
| FR-20 | Subtotal per item & total keseluruhan diupdate real-time |
| FR-21 | Validasi stok saat lanjut ke checkout; tampilkan peringatan jika stok kurang |

---

### 4.4 Proses Checkout & Pembayaran — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-22 | Pilih alamat tersimpan atau input alamat baru saat checkout |
| FR-23 | Integrasi RajaOngkir API: tampilkan pilihan ekspedisi (JNE, J&T, SiCepat) + estimasi biaya & waktu |
| FR-24 | Integrasi Midtrans: mendukung transfer bank virtual account, kartu kredit/debit, GoPay, OVO, Dana |
| FR-25 | Buat record pesanan dengan status awal "Menunggu Pembayaran" segera setelah konfirmasi checkout |
| FR-26 | Proses webhook Midtrans → update status pesanan otomatis |
| FR-27 | Kirim email konfirmasi pesanan dalam 5 menit setelah pembayaran dikonfirmasi |
| FR-28 | Pembayaran kadaluarsa dalam 24 jam → stok dikembalikan otomatis |

---

### 4.5 Manajemen Pesanan — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-29 | Halaman riwayat pesanan pelanggan: nomor pesanan, tanggal, total, status |
| FR-30 | Detail pesanan: item, harga, alamat, metode bayar, nomor resi |
| FR-31 | Admin update status: Menunggu Pembayaran → Diproses → Dikirim → Selesai |
| FR-32 | Admin input nomor resi → ditampilkan ke pelanggan |
| FR-33 | Ajukan retur dalam 7 hari setelah "Selesai", sertakan alasan + foto bukti |
| FR-34 | Kirim notifikasi email ke pelanggan setiap perubahan status pesanan |

**Alur status pesanan:**
```
Menunggu Pembayaran → Dibayar → Diproses → Dikirim → Selesai
         ↓                ↓           ↓
      Dibatalkan      Dibatalkan   Dibatalkan
```

---

### 4.6 Ulasan & Rating Produk — Prioritas: **Sedang (Should Have)**

| Kode | Kebutuhan |
|---|---|
| FR-35 | Hanya pembeli terverifikasi dengan pesanan "Selesai" yang bisa beri ulasan |
| FR-36 | Form ulasan: rating bintang 1-5 (wajib), judul (opsional, maks. 100 karakter), isi (wajib, maks. 1000 karakter) |
| FR-37 | Hitung & tampilkan rata-rata rating dari ulasan yang disetujui |
| FR-38 | Admin moderasi ulasan (setujui/tolak) sebelum tampil publik |
| FR-39 | Satu ulasan per pelanggan per produk per transaksi |

---

### 4.7 Panel Administrasi Toko — Prioritas: **Tinggi (Must Have)**

| Kode | Kebutuhan |
|---|---|
| FR-40 | Panel admin hanya untuk role `admin` / `super_admin`, autentikasi terpisah |
| FR-41 | CRUD produk: nama, kategori, merek, harga, diskon, stok, deskripsi, spesifikasi, upload maks. 5 foto |
| FR-42 | Kelola kategori hierarki dua level (kategori utama + sub-kategori) |
| FR-43 | Lihat & kelola pengguna terdaftar (lihat profil, aktifkan/nonaktifkan) |
| FR-44 | Laporan penjualan: filter rentang tanggal, tampilkan total pendapatan, jumlah pesanan, produk terlaris |
| FR-45 | Ekspor laporan ke CSV |
| FR-46 | Dashboard tampilkan notifikasi pesanan baru yang belum diproses |

---

## 🛡️ Kebutuhan Non-Fungsional

### Performa
| ID | Target |
|---|---|
| NFR-01 | Halaman beranda & katalog: < 3 detik (koneksi 4G / 10 Mbps) |
| NFR-02 | API pencarian: < 1 detik (cached) |
| NFR-03 | API checkout: < 5 detik (termasuk komunikasi Midtrans) |
| NFR-04 | Minimal 500 pengguna konkuren tanpa degradasi > 20% |
| NFR-05 | Payload halaman utama (tanpa media): < 200 KB (gzip) |
| NFR-06 | Uptime: min. 99,5%/bulan (maks. downtime ~3,6 jam/bulan) |

### Safety / Keamanan Operasional
| ID | Ketentuan |
|---|---|
| NFR-07 | Backup database otomatis setiap 24 jam, offsite, RTO < 4 jam |
| NFR-08 | Log akses admin + log semua operasi pengubah data kritis (produk/pesanan/user) dengan timestamp & identitas |
| NFR-09 | Tampilkan halaman maintenance informatif saat deployment (bukan raw error) |
| NFR-10 | Stok dikelola atomik dengan database transaction (mencegah race condition) |

### Keamanan Aplikasi
| ID | Ketentuan |
|---|---|
| NFR-11 | Password di-hash bcrypt, cost factor >= 12 (`Hash::make()`) |
| NFR-12 | Proteksi OWASP Top 10: SQL Injection, XSS, CSRF |
| NFR-13 | Seluruh komunikasi via HTTPS (TLS 1.2+) di produksi |
| NFR-14 | Token sesi: expire 2 jam (aktif), 30 hari ("ingat saya") + rotasi token |
| NFR-15 | Data kartu kredit TIDAK disimpan di database — eksklusif dikelola Midtrans |
| NFR-16 | Akses panel admin dibatasi via IP whitelisting di produksi |
| NFR-17 | Rate limiting pada endpoint login & register (anti brute force) |

### Atribut Kualitas
| Atribut | Target |
|---|---|
| Usability | SUS score > 68 |
| Maintainability | Code coverage unit test > 70% untuk fitur inti; prinsip MVC + SOLID |
| Portability | Deploy di Ubuntu 22.04 LTS (shared hosting / VPS) |
| Reliability | Error rate transaksi < 0.1% |
| Scalability | Minimal 2 server aplikasi horizontal |
| Testability | Unit test + feature test tersedia di Laravel |

---

## 📋 Aturan Bisnis (Business Rules)

| ID | Aturan |
|---|---|
| BR-01 | Diskon hanya berlaku dalam rentang tanggal yang ditentukan. Di luar itu, tampilkan harga normal |
| BR-02 | Pemesanan hanya bisa dilakukan jika stok > 0 |
| BR-03 | Ongkos kirim dihitung berdasarkan berat (gram) + jarak gudang ke alamat tujuan |
| BR-04 | Ulasan hanya dari verified purchase, satu kali per item per transaksi |
| BR-05 | Retur hanya diterima dalam 7 hari kalender setelah status "Selesai" |
| BR-06 | Hanya Super Admin yang bisa hapus akun admin lain dan akses laporan finansial konsolidasi |

---

## 🗄️ Skema Database (Entitas Utama)

Tabel yang wajib ada:

```
users
products
categories
product_images
orders
order_items
payments
reviews
addresses
audit_logs
```

**Aturan wajib semua tabel:**
- Kolom `created_at` dan `updated_at` harus ada (audit trail)
- Foreign key constraints wajib untuk integritas relasional
- Database dikelola via Laravel migrations + Eloquent ORM

---

## 🖥️ Halaman & Antarmuka

### Frontend (Pelanggan)
1. **Beranda** — banner carousel, produk unggulan, kategori, produk terbaru, navigasi (kategori, pencarian, keranjang, login/register)
2. **Katalog Produk** — grid/list produk, panel filter (kiri: kategori, harga, merek, rating), sorting, pagination/infinite scroll
3. **Detail Produk** — galeri foto (zoom on hover), nama, harga, stok, deskripsi, spesifikasi, tab ulasan, tombol "Tambah ke Keranjang" / "Beli Sekarang"
4. **Keranjang Belanja** — daftar item, ubah kuantitas, hapus, ringkasan harga, estimasi ongkir, lanjut checkout
5. **Checkout** — pilih/input alamat, pilih ekspedisi + estimasi biaya, ringkasan pesanan, pilih metode bayar
6. **Konfirmasi Pesanan** — nomor pesanan, detail item, total tagihan, instruksi pembayaran, link lacak
7. **Akun Pengguna** — profil, daftar alamat, riwayat pesanan, wishlist, pengaturan notifikasi

### Backend (Admin)
1. **Dashboard** — statistik harian (pesanan masuk, pendapatan, stok rendah) dalam kartu metrik + grafik
2. **Manajemen Produk** — tabel + CRUD, form upload multi-foto, rich-text deskripsi
3. **Manajemen Pesanan** — daftar dengan filter status, update status, input nomor resi
4. **Laporan Penjualan** — grafik pendapatan harian/bulanan, tabel transaksi, ekspor CSV/Excel

### Standar UI
- Responsif 320px — 1920px+
- Error message tampil **inline** di dekat field bermasalah (bukan hanya alert dialog)
- Aksi destruktif (hapus/batalkan) wajib tampilkan **dialog konfirmasi**
- **Loading state** wajib ditampilkan untuk proses > 300ms
- Desain mengacu **Material Design 3**

---

## 🔗 Antarmuka Komunikasi

- **HTTPS (TLS 1.2+)** — semua komunikasi client-server
- **RESTful API** — format JSON, standar respons: `{status, message, data}`
- **Email SMTP** — via SendGrid/Mailgun, port 587 TLS
- **Webhook Midtrans** — validasi signature wajib sebelum proses notifikasi
- **WebSocket** (opsional, Fase 2) — notifikasi real-time admin via Laravel Echo + Pusher/Soketi

---

## 🌐 Kebutuhan Internasionalisasi

- Bahasa antarmuka: **Bahasa Indonesia**
- Mata uang: **Rupiah (Rp)** — format: pemisah ribuan titik (.), desimal koma (,)
- Format tanggal: **DD/MM/YYYY**
- Zona waktu: **WIB (UTC+7)**

---

## ⚖️ Kebutuhan Legal

- Halaman **Syarat & Ketentuan** dan **Kebijakan Privasi** wajib tersedia dan bisa diakses publik
- Pengguna wajib centang checkbox persetujuan S&K saat registrasi
- Pengelolaan data pribadi mengacu: **UU PDP No. 27 Tahun 2022**
- Transaksi pembayaran mengacu: **PCI DSS v4.0** (dikelola Midtrans)
- Referensi regulasi: **UU ITE No. 11/2008** jo. UU No. 19/2016

---

## 📎 Use Cases (Ringkasan)

| ID | Nama | Aktor |
|---|---|---|
| UC-01 | Registrasi Akun | Pengunjung |
| UC-02 | Login | Pelanggan, Admin |
| UC-03 | Menelusuri Katalog | Semua Pengguna |
| UC-04 | Mencari Produk | Semua Pengguna |
| UC-05 | Mengelola Keranjang | Semua Pengguna |
| UC-06 | Melakukan Checkout | Pelanggan |
| UC-07 | Melacak Pesanan | Pelanggan |
| UC-08 | Memberikan Ulasan | Pelanggan |
| UC-09 | Mengelola Produk | Admin |
| UC-10 | Memproses Pesanan | Admin |
| UC-11 | Melihat Laporan | Admin, Super Admin |
| UC-12 | Mengelola Akun Admin | Super Admin |

---

## ❓ TBD (To Be Determined) — Keputusan yang Belum Final

| No | Referensi | Isu | Target |
|---|---|---|---|
| 1 | FR-23 | Provider ekspedisi final & tier RajaOngkir API (Free/Basic/Pro) | Sprint 3 |
| 2 | FR-24 | Konfirmasi metode pembayaran aktif di Midtrans produksi | Sprint 3 |
| 3 | NFR-16 | Rentang IP admin untuk IP whitelisting | Sebelum Go-Live |
| 4 | Bab 2.7 | Pilih email provider: SendGrid atau Mailgun | Sprint 2 - Akhir |
| 5 | Bab 2.7 | Pilih cloud storage: AWS S3 / Cloudflare R2 / GCS | Sprint 2 - Akhir |
| 6 | FR-44 | Format & layout laporan penjualan yang diinginkan pemilik toko | Sprint 4 |

---

## 🤖 Panduan untuk AI (Instruksi Penggunaan Dokumen Ini)

Dokumen ini adalah **satu-satunya sumber kebenaran (single source of truth)** untuk proyek MusicStore. Saat membantu pengembangan:

1. **Selalu rujuk dokumen ini** sebelum membuat keputusan teknis, arsitektur, atau fitur.
2. **Jangan membuat asumsi** di luar yang tertulis. Jika tidak ada di SRS, tanyakan ke tim.
3. **Kode yang dibuat harus sesuai** dengan tech stack: Laravel 11.x, PHP 8.2+, Blade, Tailwind CSS, MySQL 8.0+.
4. **Naming convention** kebutuhan fungsional menggunakan `FR-XX`, non-fungsional `NFR-XX`, business rules `BR-XX`.
5. **Cek TBD list** sebelum mengimplementasikan FR-23, FR-24, NFR-16, layanan email, dan cloud storage — keputusan belum final.
6. **Prioritas pengerjaan:** Must Have (Tinggi) > Should Have (Sedang) > Could Have (Rendah).
7. **Jika ada konflik** antara permintaan baru dan SRS ini, flagging dulu ke tim sebelum implementasi.
