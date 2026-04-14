---
description: Prosedur standar pembaruan MASTERPLAN.md setelah setiap perubahan aplikasi
---

// turbo-all

## Kapan Harus Update MASTERPLAN.md

Update MASTERPLAN.md setiap kali ada:
- Fitur baru yang selesai diimplementasikan
- Route baru yang ditambahkan
- Bug fix signifikan
- Perubahan arsitektur atau struktur database

## Prosedur Update

### 1. Buka file MASTERPLAN.md
```
c:\Projects\helpdesk-v2\MASTERPLAN.md
```

### 2. Update bagian yang relevan

**Tambah route baru** → Update tabel di bagian `## 🔗 Routing Aplikasi`

**Tambah fitur baru** → Tambahkan item `- [x]` di bagian `## ✅ Fitur yang Telah Diimplementasikan`

**Tambah tabel baru** → Update bagian `## 🗃️ Skema Database`

### 3. Update baris versi di baris terakhir

Format: `*Terakhir diperbarui: DD Bulan YYYY | Versi: X.X.X (Deskripsi singkat perubahan)*`

Contoh:
```
*Terakhir diperbarui: 31 Maret 2026 | Versi: 2.5.4 (Menambahkan fitur hapus tiket)*
```

Naikkan versi minor (digit ketiga) untuk perbaikan kecil, versi minor kedua (digit kedua) untuk fitur besar.

### 4. Deploy file MASTERPLAN.md ke container (jika perlu)

MASTERPLAN.md bukan file PHP, tidak perlu di-push ke container. Cukup simpan di lokal.
