# Helpdesk IT - Universitas Merdeka Malang

Sistem Helpdesk berbasis CodeIgniter 4 untuk menangani pelaporan gangguan IT, manajemen tiket, dan laporan statistik.

## Fitur Utama

- **Manajemen Tiket** — Buat, lihat, update status, assign teknisi, balas tiket
- **Upload Foto** — Upload foto dokumentasi via MinIO Object Storage
- **Laporan & Statistik** — Filter tanggal, statistik ringkasan, export Excel/PDF/Cetak
- **Notifikasi** — Notifikasi in-app, Telegram, dan Email
- **AI Assistant** — Chatbot berbasis Gemini API untuk bantuan IT
- **Knowledge Base** — Artikel solusi IT yang bisa dicari
- **SSO Google** — Login dengan akun Google @unmer.ac.id

## Laporan (Kolom Dokumentasi)

Di halaman Laporan & Statistik (`admin/reports`), setiap tiket memiliki kolom **Link Dokumentasi** yang bisa diisi manual oleh operator/teknisi/admin. Mekanismenya:

1. **Auto-fill dari foto**: Jika tiket memiliki foto (via MinIO), URL presigned akan otomatis terisi di kolom input link — tinggal klik **Simpan** untuk menyimpannya ke database.
2. **Input manual**: Operator/teknisi/admin bisa memasukkan tautan Google Drive atau link lainnya langsung di kolom input.
3. **Fallback display**: Jika `drive_link` kosong saat ditampilkan di export (Excel/PDF), sistem akan menampilkan URL foto sebagai fallback.

Link yang tersimpan akan muncul di semua format laporan:
- Tabel web (dengan tombol buka link)
- Export Excel
- Export PDF
- Cetak (print)

## Persyaratan Server

- PHP 8.1+
- MySQL/MariaDB
- MinIO Object Storage
- Ekstensi: intl, mbstring, json, mysqlnd, libcurl

## Instalasi

1. Clone repository
2. `composer install`
3. Copy `env` ke `.env` dan sesuaikan konfigurasi (database, MinIO, Google OAuth, Telegram, Email)
4. `php spark migrate`
5. `php spark db:seed` (jika ada seeder)

## Konfigurasi `.env`

```env
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

# Database
database.default.hostname = localhost
database.default.database = helpdesk_v2
database.default.username = root
database.default.password =

# MinIO
MINIO_ENDPOINT = 'localhost:9000'
MINIO_USE_SSL = false
MINIO_ACCESS_KEY = 'your-access-key'
MINIO_SECRET_KEY = 'your-secret-key'
MINIO_BUCKET_DEV = 'devhelpdesk'
MINIO_FOLDER_DEV = 'Documentation'
```

## Lisensi

Hak Cipta © Universitas Merdeka Malang
