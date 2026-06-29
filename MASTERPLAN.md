# 📋 MASTERPLAN — Helpdesk V2

> Dokumen ini menggambarkan arsitektur, fitur, dan teknologi dari aplikasi **Helpdesk V2** yang telah diimplementasikan menggunakan framework **CodeIgniter 4**.

---

## 📌 Ringkasan Aplikasi

**Helpdesk V2** adalah sistem tiket IT Support berbasis web yang memungkinkan pengguna melaporkan gangguan/masalah teknis, dan memungkinkan tim IT Support untuk mengelola, merespons, serta menyelesaikan laporan tersebut secara terstruktur.

| Item | Detail |
|------|--------|
| **Framework** | CodeIgniter 4 (PHP 8.2) |
| **Database** | MySQL / MariaDB |
| **Storage** | **MinIO Object Storage** (S3 Compatible) |
| **AI Engine** | **Google Gemini AI** (Generative AI Integration) |
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
│   │   ├── tickets/            # index, create (with tips, popular articles, AI CTA), detail
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
| 2 | **IT Support** | Dapat melihat semua tiket, merespons, assign, dan update status (Kecuali CLOSED) |
| 3 | **User** | Hanya dapat membuat dan melihat tiket milik sendiri |
| 4 | **Operator** | Akses operasional, dapat melihat laporan dan statistik |

---

## 🗃️ Skema Database

### Tabel Utama

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna (name, email, password, role_id, dept_id, **profile_pic**, gender, phone, is_active, **auth_provider**, **login_attempts**, **lockout_time**) |
| `roles` | Data role (code, name, permissions JSON, **is_staff**, **is_technician**, **role_updated_at**) |
| `departments` | Departemen organisasi (name, code, is_active) |
| `categories` | Kategori tiket (name, description, is_active) |
| `tickets` | Data tiket (id, **email_token**, title, description, **drive_link (TEXT)**, photo, photo2, status, priority, reporter_id, assigned_to, cat_id, **sla_deadline**, **sla_paused_at**) |
| `ticket_history` | Riwayat perubahan tiket (ticket_id, changed_by, old_status, new_status, changed_at, **notes**) |
| `ticket_messages` | Balasan/komentar pada tiket (ticket_id, sender_id, message, is_internal, **photo**, sent_at, **source** [web/email]) |
| `ticket_ratings` | Rating & feedback setelah tiket selesai (ticket_id, rated_by, rating, feedback, rated_at) |
| `ticket_assignees` | Multi-assign teknisi pada tiket (ticket_id, user_id, assigned_by, assigned_at) — pivot many-to-many |
| `kb_articles` | Artikel pusat bantuan (title, content, slug, cat_id, author_id, view_count, **md_key**, status) |
| `kb_categories` | Kategori artikel KB (name, slug, description, is_active) |
| `audit_logs` | Log aktivitas admin (user_id, action, target_table, target_id, details, ip_address) |

---

## 🔗 Routing Aplikasi

### Publik (Tanpa Login)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/` | Redirect ke halaman login |
| GET | `/login` | Form login |
| POST | `/login` | Proses login (dengan Rate Limiter) |
| GET | `/auth/googleLogin` | Redirect ke halaman autentikasi Google SSO |
| GET | `/auth/googleCallback` | Verifikasi token balik dari Google & proses session |
| GET | `/register` | Form registrasi (Dinonaktifkan dari UI) |
| POST | `/register` | Proses registrasi (Dinonaktifkan dari UI) |
| GET | `/logout` | Logout |

### Terproteksi (Perlu Login — Filter: `auth`)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/dashboard` | Dashboard (admin atau user) |
| GET | `/tickets` | Daftar tiket |
# 📋 MASTERPLAN — Helpdesk V2

> Dokumen ini menggambarkan arsitektur, fitur, dan teknologi dari aplikasi **Helpdesk V2** yang telah diimplementasikan menggunakan framework **CodeIgniter 4**.

---

## 📌 Ringkasan Aplikasi

**Helpdesk V2** adalah sistem tiket IT Support berbasis web yang memungkinkan pengguna melaporkan gangguan/masalah teknis, dan memungkinkan tim IT Support untuk mengelola, merespons, serta menyelesaikan laporan tersebut secara terstruktur.

| Item | Detail |
|------|--------|
| **Framework** | CodeIgniter 4 (PHP 8.2) |
| **Database** | MySQL / MariaDB |
| **Storage** | **MinIO Object Storage** (S3 Compatible) |
| **AI Engine** | **Google Gemini AI** (Generative AI Integration) |
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
│   │   ├── tickets/            # index, create (with tips, popular articles, AI CTA), detail
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
| 2 | **IT Support** | Dapat melihat semua tiket, merespons, assign, dan update status (Kecuali CLOSED) |
| 3 | **User** | Hanya dapat membuat dan melihat tiket milik sendiri |
| 4 | **Operator** | Akses operasional, dapat melihat laporan dan statistik |

---

## 🗃️ Skema Database

### Tabel Utama

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna (name, email, password, role_id, dept_id, **profile_pic**, gender, phone, is_active, **auth_provider**, **login_attempts**, **lockout_time**) |
| `roles` | Data role (code, name, permissions JSON, **is_staff**, **is_technician**, **role_updated_at**) |
| `departments` | Departemen organisasi (name, code, is_active) |
| `categories` | Kategori tiket (name, description, is_active) |
| `tickets` | Data tiket (id, **email_token**, title, description, **drive_link (TEXT)**, photo, photo2, status, priority, reporter_id, assigned_to, cat_id, **sla_deadline**, **sla_paused_at**) |
| `ticket_history` | Riwayat perubahan tiket (ticket_id, changed_by, old_status, new_status, changed_at, **notes**) |
| `ticket_messages` | Balasan/komentar pada tiket (ticket_id, sender_id, message, is_internal, **photo**, sent_at, **source** [web/email]) |
| `ticket_ratings` | Rating & feedback setelah tiket selesai (ticket_id, rated_by, rating, feedback, rated_at) |
| `ticket_assignees` | Multi-assign teknisi pada tiket (ticket_id, user_id, assigned_by, assigned_at) — pivot many-to-many |
| `kb_articles` | Artikel pusat bantuan (title, content, slug, cat_id, author_id, view_count, **md_key**, status) |
| `kb_categories` | Kategori artikel KB (name, slug, description, is_active) |
| `audit_logs` | Log aktivitas admin (user_id, action, target_table, target_id, details, ip_address) |

---

## 🔗 Routing Aplikasi

### Publik (Tanpa Login)
| Method | URL | Fungsi |
|--------|-----|--------|
| GET | `/` | Redirect ke halaman login |
| GET | `/login` | Form login |
| POST | `/login` | Proses login (dengan Rate Limiter) |
| GET | `/auth/googleLogin` | Redirect ke halaman autentikasi Google SSO |
| GET | `/auth/googleCallback` | Verifikasi token balik dari Google & proses session |
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
| POST | `/tickets/reply/{id}` | Balas tiket (Mendukung upload foto bukti pengecekkan) |
| POST | `/tickets/status/{id}` | Update status tiket |
| POST | `/tickets/assign/{id}` | Assign tiket ke support |
| GET | `/tickets/export` | Export tiket ke CSV |
| GET | `/tickets/print` | Cetak laporan tiket khusus untuk pelapor (user) |
| POST | `/tickets/delete/{id}` | Hapus tiket (khusus Administrator) |
| GET | `/notifications` | Halaman notifikasi personal (Filter & Pagination) |
| GET | `/notifications/mark-read/{id}` | Tandai satu notifikasi sebagai dibaca & buka tiket |
| GET | `/notifications/mark-all-read` | Tandai semua notifikasi milik user sebagai dibaca |
| POST | `/notifications/bulk-mark-read` | Tandai beberapa notifikasi yang dipilih sebagai dibaca |
| GET | `/notifications/unread-count` | API untuk cek jumlah notifikasi (Polling) |
| GET | `/profile` | Profil pengguna |
| POST | `/profile/update` | Update data profil |
| POST | `/profile/change-password` | Ganti password |
| POST | `/profile/update-photo` | Update foto profil (WhatsApp Style via MinIO) |
| POST | `/profile/delete-photo` | Hapus foto profil dari storage MinIO |
| GET | `/knowledge-base` | Daftar artikel pusat bantuan |
| GET | `/knowledge-base/search` | Pencarian artikel (dengan query) |
| GET | `/knowledge-base/{slug}` | Baca isi artikel |
| POST | `/ai/chat` | API Tanya AI (Integrasi Gemini) |
| POST | `/tickets/rate/{id}` | Beri rating & feedback tiket yang sudah selesai |

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
| POST | `/tickets/bulk-update-status` | Update status massal untuk banyak tiket |
| GET | `/admin/audit-logs` | Log aktivitas admin |
| GET | `/admin/knowledge-base` | Manajemen artikel Knowledge Base |
| POST | `/admin/knowledge-base/store` | Simpan artikel baru ke MinIO |
| POST | `/admin/knowledge-base/{id}/update` | Update artikel |
| POST | `/admin/knowledge-base/{id}/delete` | Hapus artikel & file di MinIO |
| POST | `/admin/knowledge-base/reembed-all` | Sinkronisasi ulang vektor AI |

---

Aplikasi ini telah mengimplementasikan berbagai fitur utama terkait autentikasi, manajemen tiket (termasuk upload dokumentasi), notifikasi, SLA, profil pengguna, dan administrasi (khusus Superadmin).

**Fitur Terbaru (v2.32.1):**
- [x] **Cetak Laporan Tiket User** — Menambahkan fitur cetak laporan tiket bagi pengguna (role USER). Pengguna dapat mencetak daftar tiket yang mereka buat dalam bentuk laporan cetak (print-friendly HTML) yang menghormati filter pencarian, status, prioritas, kategori, dan rentang tanggal.

**Fitur Terbaru (v2.32.0):**
- [x] **Email Reply Sync** — Sinkronisasi balasan email ke tiket secara otomatis. User dapat membalas email notifikasi (balasan tiket, perubahan status) langsung dari Gmail/Outlook dan balasannya akan masuk ke percakapan tiket di aplikasi. Sistem menggunakan token referensi `[REF:HDXXXX]` yang disisipkan di Subject email dan IMAP polling via Spark CLI command `cron:fetch-email-replies`. Konfigurasi via `.env` (IMAP_HOST, IMAP_PORT, IMAP_USER, IMAP_PASS, CRON_SECRET).

**Fitur Terbaru (v2.31.2):**
- [x] **Penyederhanaan UI Login** — Memperbarui logo login, menghapus informasi statis yang tidak perlu, dan menyembunyikan form login manual secara default (kini hanya dapat diakses melalui link rahasia `?login=admin`).

---

## 🚀 Roadmap Pengembangan (Enterprise Features)

Berikut adalah daftar rencana pengembangan ke depan untuk menaikkan skala Helpdesk v2 menjadi standar *Enterprise*:

1. ~~**Integrasi SSO (Single Sign-On) via Google Workspace**~~ ✅ *(Sudah diimplementasikan penuh)*
   - Login terpusat menggunakan ekosistem email kampus (`@unmer.ac.id` dan `@student.unmer.ac.id`).
2. ~~**Knowledge Base (Self-Service Pusat Bantuan)**~~ ✅ *(Sudah diimplementasikan penuh)*
   - **Rating System**: Implementasi sistem bintang dan feedback untuk mengukur kepuasan pengguna (*User Satisfaction Score*).
   - **Mobile Optimization**: Perbaikan UI modal preview foto dan bottom-sheet menu profil untuk pengalaman mobile yang lebih mulus.

*Terakhir diperbarui: 29 Juni 2026 | Versi: 2.32.1 (Fitur Cetak Laporan Tiket untuk Role User)*
