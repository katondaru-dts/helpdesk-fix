## ✅ Fitur yang Telah Diimplementasikan

### 🔐 Autentikasi & Keamanan
- [x] Login dengan email & password (password di-hash dengan `password_hash`)
- [x] **SSO (Single Sign-On) via Google Workspace** — Login SSO diintegrasikan dengan efisiensi tinggi (eliminasi network call ganda, parsing `id_token` JWT, 1x DB JOIN query) dan meminimalkan antrean blocking session.
- [x] Registrasi akun baru (Menu disembunyikan dari halaman login, registrasi diproses otomatis saat SSO login pertama kali)
- [x] Logout
- [x] **Rate Limiter** — Membatasi 5 upaya login per menit per IP
- [x] **AuthFilter** — Melindungi semua route yang membutuhkan login
- [x] **AdminFilter** — Membatasi akses menu admin hanya untuk Superadmin
- [x] CSRF Protection (bawaan CI4)
- [x] Halaman error 429 (Too Many Requests)

### 📊 Dashboard (Dashboard Status & Kinerja Layanan)
- [x] **Vivid Design System** — Implementasi warna pastel solid pada kartu statistik untuk identifikasi visual cepat.
- [x] **Defined Donut Charts** — Grafik donat memiliki garis luar tipis (*outline*) dan jalur putih untuk tampilan yang lebih tajam dan profesional.
- [x] **Highlight Antrean Tiket**: Menampilkan widget "**Tiket Masuk Terbaru**" (khusus status `OPEN`) dengan indikator jumlah total antrean untuk pemantauan real-time.
- [x] **Tata Letak Simetris Presisi** — Implementasi sistem tinggi terkunci (*fixed height*) pada seluruh panel di baris ketiga agar garis bawah setiap panel sejajar secara sempurna (simetris).
- [x] **Optimalisasi Ruang Kategori** — Statistik kategori tiket kini disusun dalam tata letak 3 kolom per baris untuk efisiensi visual.
- [x] **Modern Scrollbars** — Penambahan scroll vertikal mandiri dengan desain minimalis pada panel *Urgent*, *Tiket Masuk Terbaru*, *Kategori*, dan *Belum Diassign* untuk efisiensi ruang.
- [x] **Interaktivitas Grafik (Chart.js)**:
    - **Area Chart Bergradasi**: Grafik kinerja teknisi menggunakan gradien warna lembut dan kurva mulus (*smooth curves*).
    - **Multi-Colored Bar Chart**: Grafik waktu respons menggunakan palet warna variatif untuk setiap kategori (Biru, Hijau, Oranye, dsb) guna memberikan pembedaan visual yang lebih cepat.
    - **Rounded Bar Chart**: Grafik waktu respons dengan sudut membulat dan tooltip bertema gelap.
- [x] **Permission-Based UI**:
    - Kartu **Audit Log** disembunyikan otomatis untuk role Teknisi.
    - Kartu **User Aktif** disembunyikan otomatis untuk role Teknisi.
- [x] **Navigasi Cepat** — Kartu *User Aktif* dan *Laporan* dapat diklik untuk akses instan ke halaman manajemen terkait.
- [x] **Spinning Refresh Buttons** — Tombol refresh di seluruh bagian dashboard memiliki animasi putaran saat diklik.

### 🎫 Manajemen Tiket
- [x] **Auto-Assign Workflow** — Tiket otomatis ditugaskan kepada teknisi yang mengubah status menjadi **IN_PROGRESS** (jika tiket belum memiliki penanggung jawab).
- [x] Daftar semua tiket (admin/support) atau tiket sendiri (user)
- [x] Filter tiket berdasarkan status, prioritas, kategori, departemen, **teknisi yang menangani**
- [x] Buat tiket baru (with title, description, category, priority, drive_link)
- [x] Detail tiket dengan riwayat perubahan
- [x] Balas tiket (komentar/respons support)
- [x] Update status tiket (OPEN → IN_PROGRESS → RESOLVED → CLOSED)
- [x] **Export tiket ke CSV/Excel** — Menyertakan deskripsi dan Link Dokumentasi.
- [x] **Perbaikan Tampilan Riwayat & Balasan** — Tampilan kini membedakan antara entri perubahan status dan balasan pesan secara visual, serta dilengkapi dengan **Scrollbar Vertikal Custom** (max-height 800px) untuk mencegah halaman memanjang saat obrolan tiket lebih dari ~10-15 balasan.
- [x] **Penghapusan Tiket Menyeluruh** — Notifikasi yang terkait dengan tiket akan ikut terhapus secara otomatis ketika tiket tersebut dihapus untuk mencegah data notifikasi tertinggal (*orphan data*).

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
- [x] **Telegram Integration Enhancement** — Menambahkan informasi teknisi yang ditugaskan pada setiap notifikasi Telegram dan penambahan trigger notifikasi Telegram baru khusus saat tiket di-assign.
- [x] **Automated Reply Notifications** — Notifikasi otomatis dikirim saat ada balasan pesan:
    - Staf membalas -> Reporter mendapatkan notifikasi.
    - Reporter membalas -> Staf yang ditugaskan (atau seluruh tim jika belum diassign) mendapatkan notifikasi.
- [x] **Email Notifikasi Perubahan Status** — Email HTML otomatis dikirim ke User (role_id=3) setiap kali status tiket diubah oleh staf, **kecuali status CLOSED**. Setiap status memiliki warna template berbeda (merah untuk OPEN, kuning untuk IN_PROGRESS, ungu untuk PENDING, hijau khusus untuk RESOLVED).
- [x] **Omnipresent Admin Notifications** — Seluruh Administrator (Role 1) kini selalu menerima notifikasi untuk setiap balasan baru tanpa terkecuali, menjamin pemantauan penuh (*oversight*) terhadap seluruh antrean tiket.
- [x] **Deleted Ticket Graceful Error** — Mencegah error layar *blank* (404/Whoops) jika pengguna mengakses notifikasi dari tiket yang sebelumnya sudah dihapus dari server.
- [x] **New Ticket Alerts for IT Support** — Tim IT Support kini otomatis mendapatkan notifikasi setiap ada tiket baru yang masuk.
- [x] **Notification Badge (Sidebar)** — Menu "Notifikasi" pada sidebar kini menampilkan angka (badge merah) yang menunjukkan jumlah pesan atau aktivitas baru yang belum dibaca.
- [x] **Manual & Bulk Mark Read** — Pengguna dapat menandai notifikasi sebagai terbaca secara manual (per item), secara massal (*bulk action*), atau sekaligus seluruhnya (*Mark all as read*).
- [x] **Notification Filters** — Tersedia filter untuk memisahkan notifikasi "Belum Dibaca" dan "Sudah Dibaca".
- [x] **Pagination Notifikasi** — Daftar notifikasi kini mendukung pembagian halaman (20 item per halaman) untuk performa yang lebih baik.
- [x] **Real-time Live Refresh** — Halaman notifikasi akan diperbarui secara otomatis saat ada pesan baru masuk tanpa perlu refresh manual. Terdapat proteksi agar tidak mengganggu pengguna yang sedang memilih item.
- [x] **Notification Badge (Browser Tab)** — Judul tab browser menampilkan angka notifikasi belum dibaca dengan format `(N) Nama Halaman`, memudahkan pengguna memantau notifikasi meskipun tab tidak aktif.
- [x] **Notification Bell (Topbar)** — Ikon bell ditambahkan di pojok kanan atas topbar. Berubah menjadi kuning (`bi-bell-fill`) with badge merah saat ada notifikasi belum dibaca. Mendukung tampilan `99+` untuk lebih dari 99 notifikasi.
- [x] **Simplified User Dropdown** — Nama akun di Topbar kini memiliki menu dropdown minimalis yang memunculkan opsi "Keluar" saat kursor diarahkan (*hover*), memberikan akses cepat untuk logout dengan tampilan bersih.
- [x] **Notifikasi Suara (*Web Audio API*)** — Pengaturan notifikasi suara dengan opsi (Default, Bell, Beep, Chime) yang dapat diuji coba di halaman Profil dan berjalan ringan secara *offline*.
- [x] **Native Desktop Notification** — Pop-up OS asli untuk notifikasi masuk.
- [x] **Fast Polling teroptimasi** — *Background worker* mengecek data memori setiap 5 detik (dipercepat dari 10 detik) tanpa menyebabkan penguncian (*session-locking*), menjadikan website sangat ringan dan responsif.

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
- [x] **Pembatasan Ekspor Berdasarkan Izin (`Ekspor Data`)** — Tombol Cetak, Excel, dan PDF hanya ditampilkan jika role memiliki izin `Ekspor Data`. Jika tidak, tombol disembunyikan dari UI dan akses langsung ke URL ekspor pun diblokir di sisi server (Controller). Berlaku dinamis: jika izin diaktifkan kembali, seluruh fungsi langsung kembali aktif.

#### Audit Log
- [x] Rekam semua aktivitas admin (CREATE, UPDATE, DELETE, TOGGLE_STATUS)
- [x] Data: Waktu, User, IP Address, Tabel, ID Target, Detail perubahan
- [x] Pagination halaman log dengan desain antarmuka premium dan indikator aktif.