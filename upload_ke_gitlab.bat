@echo off
title Upload ke GitLab
echo.
echo ===================================================
echo   Mengunggah Pembaruan Project ke GitLab
echo ===================================================
echo.

:: 1. Menambahkan semua file yang berubah
echo [1/3] Menambahkan file yang berubah ke Git...
git add .
echo Berhasil.
echo.

:: 2. Meminta input untuk pesan commit dari pengguna
set /p commit_msg="Masukkan deskripsi untuk update ini (lalu tekan Enter): "

:: Jika pengguna hanya menekan Enter (kosong), gunakan pesan default
if "%commit_msg%"=="" set commit_msg="Update project otomatis"

echo.
echo [2/3] Menyimpan perubahan (git commit)...
git commit -m "%commit_msg%"
echo Berhasil.
echo.

:: 3. Mengunggah ke GitLab
echo [3/3] Mengunggah ke server GitLab (git push)...
git push
echo.

echo ===================================================
echo   SELESAI! Semua perubahan sudah terunggah.
echo ===================================================
echo.
pause
