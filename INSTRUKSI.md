# 📖 INSTRUKSI OPERASIONAL — Helpdesk V2

Dokumen ini berisi kumpulan instruksi standar, panduan pengembangan, dan prosedur operasional untuk aplikasi **Helpdesk V2**. Dokumen ini wajib diikuti oleh pengembang dan AI Agent untuk menjaga konsistensi dan integritas sistem.

---

## 🛠️ Standar Pengembangan (Coding Standards)

### 1. Framework & Bahasa
- **Framework**: CodeIgniter 4.6+
- **PHP**: 8.2+ (Typed properties, constructor promotion jika memungkinkan)
- **Database**: Query Builder CI4 (Hindari raw SQL demi keamanan)
- **Frontend**: Bootstrap 5 + Bootstrap Icons + Vanilla JS

### 2. Penamaan (Naming Conventions)
- **Controllers**: PascalCase (Contoh: `TicketController.php`)
- **Models**: PascalCase dengan akhiran `Model` (Contoh: `UserModel.php`)
- **Views**: snake_case (Contoh: `ticket_detail.php`)
- **Routes**: kebab-case (Contoh: `/admin/manage-users`)
- **Database Tables**: snake_case jamak (Contoh: `tickets`, `users`)

### 3. Keamanan (Security First)
- Selalu gunakan `esc()` saat menampilkan data di View untuk mencegah XSS.
- Gunakan `has_permission('nama_izin')` untuk pengecekan akses di Controller/View.
- Semua form wajib memiliki `<?= csrf_field() ?>`.
- Jangan pernah menyimpan password dalam bentuk teks biasa (Gunakan `password_hash()`).

---

## 🔄 Prosedur Kerja (Workflows)

### 1. Perubahan Kode & Fitur
Setiap kali melakukan perubahan fitur atau perbaikan bug:
1. **Implementasi**: Tulis kode sesuai standar CI4.
2. **Validasi**: Tes fitur di lingkungan lokal/docker.
3. **Update Masterplan**: Jalankan instruksi di `.agent/workflows/update-masterplan.md`.
4. **Dokumentasi**: Jika ada instruksi baru yang bersifat permanen, tambahkan ke dokumen `INSTRUKSI.md` ini.

### 2. Manajemen Database
- Gunakan **Migrations** untuk setiap perubahan skema tabel (`php spark make:migration NamaMigrasi`).
- Gunakan **Seeds** untuk data awal atau data testing (`php spark make:seeder NamaSeeder`).
- Jangan mengubah database secara manual via PHPMyAdmin tanpa mencatatnya di Migration.

### 3. Sinkronisasi Docker (Windows/WSL2)
Karena masalah *bind mount caching* di Windows:
- Gunakan prosedur di `.agents/workflows/deploy-to-docker.md` untuk mendorong perubahan file ke container.
- Gunakan `cmd /c` untuk perintah yang membutuhkan redirect stdin (`<`).

---

## ⏳ Instruksi Khusus Fitur

### 1. SLA (Service Level Agreement)
- Status **PENDING** akan menghentikan (pause) perhitungan SLA.
- Status **IN_PROGRESS** akan melanjutkan (resume) perhitungan SLA.
- Gunakan command `php spark cron:check-sla` untuk memantau tiket yang *overdue*.

### 2. Notifikasi
- Setiap balasan tiket harus memicu notifikasi ke pihak terkait.
- Notifikasi yang terkait dengan tiket wajib dihapus otomatis jika tiket tersebut dihapus (Cascading delete di level aplikasi).

---

## 🤖 Instruksi untuk AI Agent (Gemini CLI)

Jika Anda (AI Agent) bekerja pada proyek ini, ikuti aturan tambahan berikut:

1. **Prinsip Operasi**: Lakukan riset menyeluruh sebelum mengubah file. Selalu cek `MASTERPLAN.md` dan `INSTRUKSI.md`.
2. **Kemandirian**: Selesaikan tugas secara tuntas dari implementasi hingga pembaruan dokumentasi tanpa diminta berulang kali.
3. **Pembaruan Masterplan**: Secara otomatis perbarui `MASTERPLAN.md` setiap kali menyelesaikan fitur besar atau perubahan struktur.
4. **Penanganan Error**: Jika terjadi layar *blank* setelah update file di Docker, ikuti prosedur "Solusi" di bagian Docker pada `MASTERPLAN.md`.
5. **Transparansi**: Jelaskan setiap perubahan teknis yang dilakukan secara ringkas namun detail.

---

*Terakhir diperbarui: 20 April 2026 | Versi Dokumen: 1.0.0*
/model