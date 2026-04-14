---
description: Prosedur standar deploy perubahan file ke Docker container (bypass bind mount Windows)
---

// turbo-all

## Latar Belakang

Docker Desktop di Windows menggunakan WSL2 yang memiliki masalah **bind mount caching** — perubahan file di Windows tidak selalu langsung terbaca oleh container Linux. Oleh karena itu, setiap perubahan file PHP/View/Config **harus di-push manual ke container** menggunakan metode berikut.

## Prosedur Deploy Perubahan ke Container

### 1. Pastikan container berjalan
```
docker ps
```
Pastikan `helpdesk-app`, `helpdesk-web`, dan `helpdesk-db` statusnya `Up`.

### 2. Buat skrip bash sementara di `C:\temp\helpdesk-tmp\`
Buat file `.sh` yang berisi perintah `cat << 'EOF' > /path/di/container` dengan isi file baru yang ingin di-deploy.

Contoh untuk `app/Views/tickets/index.php`:
```sh
cat << 'EOF' > /var/www/html/app/Views/tickets/index.php
[ISI FILE PHP]
EOF
echo "DONE"
```

### 3. Jalankan skrip ke container via CMD (bukan PowerShell)
Gunakan `cmd /c` karena PowerShell tidak mendukung stdin redirect (`<`):
```
cmd /c "docker exec -i helpdesk-app sh < C:\temp\helpdesk-tmp\nama_skrip.sh"
```

Output `DONE` berarti berhasil.

### 4. Verifikasi isi file di dalam container
```
docker exec helpdesk-app grep -n "kata_kunci" /var/www/html/path/ke/file.php
```

### 5. Jangan gunakan PowerShell untuk redirect stdin
❌ **Salah:** `docker exec -i helpdesk-app sh < file.sh` (di PowerShell)
✅ **Benar:** `cmd /c "docker exec -i helpdesk-app sh < file.sh"`

## Catatan Penting

- Selalu gunakan **`cmd /c`** untuk stdin redirect ke Docker di Windows
- File PHP di `C:\Projects\helpdesk-v2\` adalah **sumber kebenaran** — selalu edit file lokal terlebih dulu, lalu push ke container
- Jangan mengandalkan bind mount Docker Desktop untuk sinkronisasi otomatis
- Jika container di-restart (`docker restart helpdesk-app`), bind mount akan **menimpa kembali** file container dengan versi lokal — jadi pastikan file lokal selalu up-to-date
