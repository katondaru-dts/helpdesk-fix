# 📋 MASTERPLAN — Helpdesk V2

> Dokumen ini menggambarkan arsitektur, fitur, dan teknologi dari aplikasi **Helpdesk V2** yang telah diimplementasikan menggunakan framework **CodeIgniter 4**.

---

## 📌 Ringkasan Aplikasi

**Helpdesk V2** adalah sistem tiket IT Support berbasis web yang memungkinkan pengguna melaporkan gangguan/masalah teknis, dan memungkinkan tim IT Support untuk mengelola, merespons, serta menyelesaikan laporan tersebut secara terstruktur.

| Item | Detail |
|------|--------|
| **Framework** | CodeIgniter 4 (PHP 8.2) |
| **Database** | MySQL / MariaDB |
| **Web Server** | Nginx |
| **Environment** | Docker (PHP-FPM + Nginx + MariaDB) |
| **Bahasa** | Indonesia |
| **Port Akses** | `http://helpdesk.unmer.ac.id:8085` |

---

## 🏗️ Arsitektur Sistem

```
Browser → Nginx (Port helpdesk.unmer.ac.id:8085) → PHP-FPM (App) → MariaDB (Port 3307)
                                     ↓
                           CodeIgniter 4 MVC
                        ┌──────────────────────┐
                        │  Controllers          │
                        │  Models               │
                        │  Views (PHP templates)│
                        │  Filters (Auth/Admin) │
                        └──────────────────────┘
```

### Struktur Direktori Utama

```
helpdesk-v2/
├── app/
│   ├── Config/
│   │   ├── Routes.php          # Definisi semua URL route
│   │   └── Filters.php         # Registrasi filter auth, admin, ratelimiter
│   ├── Controllers/
│   │   ├── Auth.php            # Login, Register, Logout
│   │   ├── Dashboard.php       # Halaman utama admin & user
│   │   ├── Tickets.php         # Manajemen tiket
│   │   ├── Notifications.php   # Halaman notifikasi
│   │   ├── Profile.php         # Profil pengguna
│   │   └── Admin/
│   │       ├── Users.php       # Manajemen user
│   │       ├── Departments.php # Manajemen departemen
│   │       ├── Categories.php  # Manajemen kategori
│   │       ├── Roles.php       # Manajemen role & izin
│   │       ├── Reports.php     # Laporan & statistik
│   │       └── AuditLogs.php   # Log aktivitas admin
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── TicketModel.php
│   │   ├── DepartmentModel.php
│   │   ├── CategoryModel.php
│   │   ├── RoleModel.php
│   │   └── AuditLogModel.php
│   ├── Filters/
│   │   ├── AuthFilter.php      # Cek status login
│   │   ├── AdminFilter.php     # Cek role admin
│   │   └── RateLimiter.php     # Pembatas upaya login (5/menit)
│   ├── Views/
│   │   ├── layouts/main.php    # Template utama (sidebar + header)
│   │   ├── auth/               # Login & Register
│   │   ├── dashboard/          # admin.php + user.php
│   │   ├── tickets/            # index, create, detail
│   │   ├── notifications/      # index.php
│   │   ├── profile/            # index.php
│   │   ├── admin/
│   │   │   ├── users/
│   │   │   ├── departments/
│   │   │   ├── categories/
│   │   │   ├── roles/
│   │   │   ├── reports/
│   │   │   └── audit_logs/
│   │   └── errors/html/        # Halaman error (404, 429, dll)
│   └── Database/
│       ├── Migrations/         # Skema tabel
│       └── Seeds/              # Data awal (admin, roles, dll)
├── public/
│   └── css/style.css           # CSS utama aplikasi
├── docker-compose.yml
├── Dockerfile.php
├── Dockerfile.nginx
├── nginx.conf
└── .env                        # Konfigurasi environment
```

---

## 👥 Sistem Peran (Role)

| Role ID | Nama | Deskripsi |
|---------|------|-----------|
| 1 | **Superadmin** | Akses penuh ke semua fitur dan menu administrasi |
| 2 | **IT Support** | Dapat melihat semua tiket, merespons, assign, dan update status |
| 3 | **User** | Hanya dapat membuat dan melihat tiket milik sendiri |
| 4 | **Operator** | Akses operasional, dapat melihat laporan dan statistik |

---

## 🗃️ Skema Database

### Tabel Utama

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna (name, email, password, role_id, dept_id, gender, phone, is_active) |
| `roles` | Data role (code, name, permissions JSON) |
| `departments` | Departemen organisasi (name, code, is_active) |
| `categories` | Kategori tiket (name, description, is_active) |
| `tickets` | Data tiket (id, title, description, drive_link, status, priority, reporter_id, assigned_to, cat_id, **sla_deadline**, **sla_paused_at**) |
| `ticket_history` | Riwayat perubahan tiket (ticket_id, changed_by, old_status, new_status, changed_at, **notes**) |
| `ticket_replies` | Balasan/komentar pada tiket |
| `ticket_ratings` | Rating kepuasan dari user (1-5 bintang) - Mencatat `rated_by` dan `rated_at` |
| `audit_logs` | Log aktivitas admin (user_id, action, target_table, target_id, details, ip_address) |

---

## 🔗 Routing Aplikasi

### Publik (Tanpa Login)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/` | Redirect ke halaman login |
| GET | `/login` | Form login |
| POST | `/login` | Proses login (dengan Rate Limiter) |
| GET | `/register` | Form registrasi (Dinonaktifkan dari UI) |
| POST | `/register` | Proses registrasi (Dinonaktifkan dari UI) |
| GET | `/logout` | Logout |

### Terproteksi (Perlu Login — Filter: `auth`)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/dashboard` | Dashboard (admin atau user) |
| GET | `/tickets` | Daftar tiket |
| GET | `/tickets/create` | Form buat tiket baru |
| POST | `/tickets/store` | Simpan tiket baru |
| GET | `/tickets/detail/{id}` | Detail tiket |
| POST | `/tickets/reply/{id}` | Balas tiket |
| POST | `/tickets/status/{id}` | Update status tiket |
| POST | `/tickets/assign/{id}` | Assign tiket ke support |
| POST | `/tickets/rate/{id}` | Beri rating tiket |
| GET | `/tickets/export` | Export tiket ke CSV |
| POST | `/tickets/delete/{id}` | Hapus tiket (khusus Administrator) |
| GET | `/notifications` | Halaman notifikasi personal |
| GET | `/notifications/all` | Riwayat seluruh aktivitas tiket (Admin/Support) |
| GET | `/profile` | Profil pengguna |
| POST | `/profile/update` | Update data profil |
| POST | `/profile/password` | Ganti password |

### Admin (Perlu Login + Role Admin — Filter: `admin`)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/admin/users` | Manajemen user |
| POST | `/admin/users/save` | Tambah/edit user |
| POST | `/admin/users/delete` | Hapus user |
| POST | `/admin/users/toggle_status` | Aktif/nonaktifkan user |
| GET | `/admin/departments` | Manajemen departemen |
| POST | `/admin/departments/save` | Tambah/edit departemen |
| GET | `/admin/categories` | Manajemen kategori |
| POST | `/admin/categories/save` | Tambah/edit kategori |
| GET | `/admin/roles` | Manajemen role & izin |
| POST | `/admin/roles/save` | Tambah/edit role |
| GET | `/admin/reports` | Laporan & statistik |
| GET | `/admin/reports/excel` | Export laporan ke CSV/Excel |
| GET | `/admin/reports/pdf` | Export laporan ke PDF |
| POST | `/admin/reports/update-link/{id}` | Simpan/update Link Dokumentasi per tiket |
| GET | `/admin/audit-logs` | Log aktivitas admin |

---

## ✅ Fitur yang Telah Diimplementasikan

### 🔐 Autentikasi & Keamanan
- [x] Login dengan email & password (password di-hash dengan `password_hash`)
- [x] Registrasi akun baru (Menu disembunyikan dari halaman login)
- [x] Logout
- [x] **Rate Limiter** — Membatasi 5 upaya login per menit per IP
- [x] **AuthFilter** — Melindungi semua route yang membutuhkan login
- [x] **AdminFilter** — Membatasi akses menu admin hanya untuk Superadmin
- [x] CSRF Protection (bawaan CI4)
- [x] Halaman error 429 (Too Many Requests)

### 📊 Dashboard
- [x] **Dashboard Admin** — 10 stat cards (Total, Open, In Progress, Selesai, User Aktif, Belum Diassign, Urgent, Laporan, Audit, Avg Rating) tersusun responsif 5 kolom.
- [x] Panel Tiket Urgent / High Priority
- [x] Panel Tiket Belum Diassign
- [x] Tombol aksi: Laporan & Semua Tiket
- [x] **Dashboard User** — 4 stat cards (tiket milik sendiri) + tabel Tiket Terbaru
- [x] **Perbaikan UI/UX** — Perbaikan konflik class CSS agar sidebar tidak menimpa konten utama (overlapping layout).

### 🎫 Manajemen Tiket
- [x] Daftar semua tiket (admin/support) atau tiket sendiri (user)
- [x] Filter tiket berdasarkan status, prioritas, kategori, departemen, **teknisi yang menangani**
- [x] Buat tiket baru (with title, description, category, priority, drive_link)
- [x] Detail tiket dengan riwayat perubahan
- [x] Balas tiket (komentar/respons support)
- [x] Update status tiket (OPEN → IN_PROGRESS → RESOLVED → CLOSED)
- [x] Assign tiket ke staf IT Support
- [x] Rating tiket setelah selesai (1-5 bintang) - Perbaikan bug mismatch schema (rated_by & rated_at)
- [x] **Export tiket ke CSV/Excel** — Menyertakan deskripsi dan Link Dokumentasi.
- [x] **Perbaikan Tampilan Riwayat & Balasan** — Tampilan kini membedakan antara entri perubahan status dan balasan pesan secara visual.

### ⏳ SLA (Service Level Agreement) & Timer
- [x] **Real-time Countdown Timer** — Hitung mundur sisa waktu pengerjaan di daftar tiket dan detail tiket.
- [x] **Konfigurasi Durasi Dinamis**:
    - **URGENT**: 2 Jam
    - **HIGH**: 5 Jam
    - **MEDIUM**: 12 Jam
    - **LOW**: 24 Jam
- [x] **Pause & Resume SLA** — Timer otomatis berhenti (Paused) saat tiket berstatus **PENDING** dan berlanjut (dengan penyesuaian deadline) saat kembali ke status aktif.
- [x] **Indikator Visual** — Warna dinamis: Hijau (Aman), Oranye (< 2 jam), Merah (Overdue).
- [x] **SLA Overdue Monitoring** — CLI Command (`php spark cron:check-sla`) untuk pengecekan tiket yang melewati batas waktu.

### 👤 Profil Pengguna
- [x] Lihat dan edit data profil (nama, email, telepon, jenis kelamin, departemen)
- [x] Ganti password (verifikasi password lama, perbaikan error 404 Form Submit)

### 🔔 Notifikasi & Aktivitas Tiket
- [x] **Global Activity Access** — IT Support (Role 2) kini dapat melihat seluruh riwayat aktivitas tiket melalui halaman "Semua Notifikasi", memudahkan pemantauan tim.
- [x] **Automated Reply Notifications** — Notifikasi otomatis dikirim saat ada balasan pesan:
    - Staf membalas -> Reporter mendapatkan notifikasi.
    - Reporter membalas -> Staf yang ditugaskan (atau seluruh tim jika belum diassign) mendapatkan notifikasi.
- [x] **New Ticket Alerts for IT Support** — Tim IT Support kini otomatis mendapatkan notifikasi setiap ada tiket baru yang masuk.
- [x] **Notification Badge (Sidebar)** — Menu "Notifikasi" pada sidebar kini menampilkan angka (badge merah) yang menunjukkan jumlah pesan atau aktivitas baru yang belum dibaca.
- [x] **Auto-Mark Read** — Notifikasi otomatis ditandai sebagai terbaca setelah halaman Notifikasi dibuka.
- [x] **Notification Badge (Browser Tab)** — Judul tab browser menampilkan angka notifikasi belum dibaca dengan format `(N) Nama Halaman`, memudahkan pengguna memantau notifikasi meskipun tab tidak aktif.
- [x] **Notification Bell (Topbar)** — Ikon bell ditambahkan di pojok kanan atas topbar. Berubah menjadi kuning (`bi-bell-fill`) dengan badge merah saat ada notifikasi belum dibaca. Mendukung tampilan `99+` untuk lebih dari 99 notifikasi.

### 🛠️ Administrasi (Superadmin Only)

#### Kelola User
- [x] Daftar semua user dengan filter role
- [x] Tambah user baru
- [x] Edit data user
- [x] Aktif / nonaktifkan user
- [x] Hapus user (dengan proteksi akun sendiri)

#### Kelola Departemen
- [x] Daftar departemen dengan jumlah user
- [x] Tambah / edit departemen
- [x] Aktif / nonaktifkan departemen
- [x] Hapus departemen (jika tidak ada user)

#### Kelola Kategori
- [x] Daftar kategori tiket
- [x] Tambah / edit / hapus kategori
- [x] Toggle status aktif/nonaktif

#### Kelola Role & Izin
- [x] Daftar role dengan jumlah user
- [x] Tambah / edit role dengan izin granular (9 jenis izin)
- [x] Hapus role (jika tidak dipakai user)
- [x] Proteksi: Role Superadmin tidak bisa diubah/dihapus

#### Laporan & Statistik
- [x] Filter laporan berdasarkan rentang tanggal
- [x] Statistik total tiket per status
- [x] Export laporan ke CSV/Excel & PDF (dengan kolom deskripsi & Link Dokumentasi)
- [x] **Link Dokumentasi Inline Edit** — Admin dapat langsung mengetik, menyimpan, dan memperbarui Link Dokumentasi langsung dari kolom tabel laporan (tanpa modal popup). Ikon buka link juga tampil otomatis jika link sudah terisi.
- [x] Fitur Cetak Laporan (Print) dengan layout baru
- [x] **Akses Laporan Dinamis** — Menu laporan dan halaman statistik kini dapat diakses oleh role apa pun (termasuk IT Support) selama mereka memiliki izin `Lihat Laporan`.
- [x] **Pagination Laporan** — Mendukung pembagian halaman pada data tiket yang panjang (10 data per halaman) dengan desain antarmuka elegan.

#### Audit Log
- [x] Rekam semua aktivitas admin (CREATE, UPDATE, DELETE, TOGGLE_STATUS)
- [x] Data: Waktu, User, IP Address, Tabel, ID Target, Detail perubahan
- [x] Pagination halaman log dengan desain antarmuka premium dan indikator aktif.

---

## 🎨 Desain & UI

- **Font**: Inter (Google Fonts)
- **Icon**: Bootstrap Icons v1.11
- **Layout**: Sidebar kiri (dark) + Konten utama (light)
- **Komponen**: Stat cards, badge status, modal dialog, tabel responsif
- **Halaman Login**: Desain premium dengan background dark blue gradient, glassmorphism card (frosted glass), logo aplikasi dalam lingkaran, geometric SVG pattern, dan animasi sparkle dekoratif
- **Sidebar Branding**: Menggunakan logo resmi aplikasi menggantikan ikon generik untuk memperkuat identitas visual.
- **Favicon**: Dukungan favicon SVG dan ICO di seluruh halaman aplikasi.
- **Warna Status Tiket**:
  - OPEN → Merah
  - IN_PROGRESS → Kuning/Oranye
  - PENDING → Abu-abu
  - RESOLVED/CLOSED → Hijau
- **Warna Prioritas**:
  - LOW → Abu-abu
  - MEDIUM → Biru
  - HIGH → Oranye
  - URGENT → Merah

---

## 🐳 Konfigurasi Docker

```yaml
Services:
  app:        PHP 8.2-FPM (helpdesk-app)
  web:        Nginx Alpine (helpdesk-web) → Port 8085
  db:         MariaDB latest (helpdesk-db) → Port 3307
  phpmyadmin: Port 8081

> [!IMPORTANT]
> Aplikasi ini menggunakan **Bind Mount Volume** (`.:/var/www/html`). Secara otomatis, perubahan di Windows menyelaraskan ke Docker. Namun, karena keterbatasan sinkronisasi ganda pada WSL2, **lock dari IDE/Editor di Windows** sering kali menyebabkan konflik (file menjadi 0 byte/blank screen) jika file sedang terbuka saat diperbarui secara eksternal. 
> 
> **Solusi:** Selalu ikuti prosedur `/deploy-to-docker` (atau push paksa via PowerShell) untuk pembaruan View/Controller. Jika terjadi layar *blank*, tutup file terkait di editor Anda terlebih dahulu sebelum mengunggah ulang (docker cp) agar *bind mount lock* terlepas.
```

### Environment Variables (`.env`)
```
app.baseURL = 'http://helpdesk.unmer.ac.id:8085/'
database.default.hostname = db
database.default.database = helpdesk_v2
database.default.username = root
database.default.password = root_password
CI_ENVIRONMENT = production
```

---

## 🔑 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Superadmin | `admin@helpdesk.id` | `071025@Unmer` |

---

## 📦 Dependensi

| Package | Versi | Fungsi |
|---------|-------|--------|
| codeigniter4/framework | ^4.6 | Core Framework |
| PHP | ^8.2 | Runtime |
| MariaDB/MySQL | latest | Database |
| Bootstrap Icons | 1.11.1 | Icon library |
| Google Fonts (Inter) | — | Typography |

---

## 🔄 Alur Kerja Tiket

Visualisasi alur kerja penanganan tiket yang terintegrasi (Swimlane):

```mermaid
flowchart TD
    %% Styling
    classDef user fill:#e1f5fe,stroke:#01579b,stroke-width:2px;
    classDef tech fill:#fff3e0,stroke:#e65100,stroke-width:2px;
    classDef decision fill:#f3e5f5,stroke:#4a148c,stroke-width:2px;
    classDef status fill:#f1f8e9,stroke:#33691e,stroke-width:2px;

    subgraph Pelapor[PENGGUNA / USER]
        direction TB
        A[Buat Laporan Gangguan]:::user
        G[Cek Hasil & Beri Rating]:::user
        B1[Beri Klarifikasi]:::user
    end

    subgraph Pelaksana[IT SUPPORT / TEKNISI]
        direction TB
        B{Verifikasi Tiket}:::decision
        C[Assign ke Staf IT]:::tech
        D[Troubleshooting & Perbaikan]:::tech
        E{Apakah Masalah Selesai?}:::decision
        F[Ubah Status: RESOLVED]:::status
    end

    Start([Mulai]) --> A
    A -->|Status: OPEN| B
    B -->|Tidak Valid| B1
    B1 -.-> A
    B -->|Valid| C
    C -->|Status: IN_PROGRESS| D
    D --> E
    E -->|Belum| D
    E -->|Ya| F
    F -->|Notifikasi Selesai| G
    G -->|Status: CLOSED| End([Selesai])
```

---

## 🛡️ Keamanan yang Diterapkan

1. **Password Hashing** — `password_hash()` dengan algoritma bcrypt (PASSWORD_DEFAULT)
2. **CSRF Protection** — Token CSRF di setiap form POST
3. **Rate Limiting** — Max 5 upaya login per menit per IP
4. **Route Protection** — Filter `auth` dan `admin` di level routing CI4
5. **Input Escaping** — Fungsi `esc()` di setiap output HTML
6. **Audit Log** — Setiap aksi admin dicatat lengkap dengan IP address
7. **SQL Injection Protection** — Query Builder CI4 otomatis memparameterisasi query
8. **Auto-Logout** — Sesi kedaluwarsa otomatis dan redirect ke halaman logout setelah 15 menit tanpa aktivitas

---

## 📁 File Penting

| File | Lokasi | Fungsi |
|------|--------|--------|
| `Routes.php` | `app/Config/` | Definisi semua URL route |
| `Filters.php` | `app/Config/` | Registrasi filter |
| `main.php` | `app/Views/layouts/` | Template layout utama |
| `style.css` | `public/css/` | CSS seluruh aplikasi |
| `.env` | Root | Konfigurasi environment |
| `docker-compose.yml` | Root | Konfigurasi Docker |
| `nginx.conf` | Root | Konfigurasi web server |

---

## 🚀 Roadmap Pengembangan (Enterprise Features)

Berikut adalah daftar rencana pengembangan ke depan untuk menaikkan skala Helpdesk v2 menjadi standar *Enterprise*:

1. **Integrasi SSO (Single Sign-On) via Google Workspace**
   - Login terpusat menggunakan ekosistem email kampus (`@unmer.ac.id`).
   - Pencocokan akun secara aman tanpa menimpa *Role* atau kehilangan riwayat tiket lama.
2. **Email-to-Ticket (Omnichannel)**
   - Konversi email masuk ke kotak pengaduan menjadi tiket baru di aplikasi secara otomatis (menggunakan API/Cron Job).
   - Mendukung balasan *threading* langsung dari antarmuka email.
3. **SLA (Service Level Agreement) & Auto-Escalation**
   - Penentuan batas waktu maksimal pengerjaan/respons berdasarkan prioritas tiket.
   - Eskalasi otomatis ke kepala bagian jika SLA dilanggar.
4. **Knowledge Base (Self-Service Pusat Bantuan)**
   - Basis data FAQ yang direkomendasikan secara cerdas kepada User saat akan melapor, bertujuan mengurangi duplikasi pelaporan yang sama (contoh: cara reset password).
5. **Asset & Inventory Management** *(Menunggu kesiapan infrastruktur internal)*
   - Mengaitkan laporan kerusakan tiket secara langsung dengan kode Inventaris Hardware yang bermasalah.
6. **Routing & Workflow Automation**
   - Aturan pelimpahan tugas bersyarat, seperti otomatis `Assign` staf ahli Jaringan jika kategori yang dilaporkan adalah koneksi Internet.

---

*Terakhir diperbarui: 15 April 2026 | Versi: 2.7.4 (Penyempurnaan ukuran logo login dan pemulihan favicon asli)*
