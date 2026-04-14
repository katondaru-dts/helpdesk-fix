---
description: Prosedur standar pembaruan MASTERPLAN.md setelah setiap perubahan aplikasi
---

Setiap kali ada perubahan pada fitur, arsitektur, atau konfigurasi aplikasi, langkah-langkah berikut harus dilakukan:

1. **Analisis Perubahan**: Identifikasi fitur baru, perubahan database, atau rute (routes) yang baru saja diimplementasikan.
2. **Perbarui MASTERPLAN.md**:
    - Update bagian **Fitur yang Telah Diimplementasikan** jika ada fitur baru.
    - Update bagian **Skema Database** jika ada perubahan tabel.
    - Update bagian **Routing** jika ada URL baru.
    - Update bagian **Versi & Tanggal** di baris paling bawah.
3. **Sinkronisasi**: Pastikan deskripsi di masterplan sesuai dengan kode yang ada di server/docker.
