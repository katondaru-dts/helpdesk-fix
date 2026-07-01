<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * ProfileCompleteFilter
 *
 * Filter ini memastikan bahwa setiap user yang sudah login
 * memiliki data profil yang lengkap (nomor telepon & jenis kelamin).
 *
 * Perilaku:
 * - Jika user adalah user baru SSO (is_new_sso_user=true di session),
 *   akan di-redirect ke /profile/complete (halaman wajib isi profil).
 * - Jika user lama tapi data kosong, set session flag show_profile_popup=true
 *   agar popup muncul di layout (tidak memblokir akses).
 *
 * Filter ini hanya aktif untuk route yang terdaftar di Routes.php.
 * Route /profile/complete, /profile/save-complete, /logout dikecualikan.
 */
class ProfileCompleteFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Hanya cek jika user sudah login
        if (!session()->get('isLoggedIn')) {
            return; // Biarkan AuthFilter yang handle redirect ke login
        }

        $session = session();

        // ── Hanya berlaku untuk role User (role_id=3) ──
        // Admin, Staff, Operator, Teknisi tidak perlu wajib mengisi profil
        if ($session->get('role_id') != 3) {
            return;
        }

        // Jika user baru via SSO (belum pernah isi profil), paksa redirect ke halaman pengisian
        if ($session->get('is_new_sso_user')) {
            return redirect()->to('/profile/complete');
        }

        // Jika profil tidak lengkap: tampilkan popup di halaman lain.
        // Halaman /profile & /profile/complete dikecualikan karena di sana user
        // sudah berada di form pengisian — popup akan mengganggu.
        $path = ltrim($request->getUri()->getPath(), '/');
        $onProfilePage = ($path === 'profile' || $path === 'profile/complete');

        if ($session->get('profile_incomplete') === true) {
            if ($onProfilePage) {
                // User sudah di halaman form — hapus flag agar popup tidak muncul di sini.
                $session->remove('show_profile_popup');
            } else {
                // Ingatkan kembali di halaman lain selama data belum dilengkapi.
                $session->set('show_profile_popup', true);
            }
        } else {
            // Profil sudah lengkap — pastikan tidak ada flag popup tersisa.
            $session->remove('show_profile_popup');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
