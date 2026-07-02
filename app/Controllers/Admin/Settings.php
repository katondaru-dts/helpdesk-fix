<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\AuditLogModel;

class Settings extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAllSettings();

        // Ensure defaults if any are missing from DB
        $data = [
            'pageTitle'  => 'Parameter Dasar',
            'activePage' => 'admin-settings',
            'settings'   => $settings,
        ];

        return view('admin/settings/index', $data);
    }

    public function save()
    {
        $settingModel = new SettingModel();
        $auditLog     = new AuditLogModel();

        // Validation Rules
        $rules = [
            'max_failed_attempts'              => 'required|in_list[1,2,3,4,5]',
            'lockout_duration'                 => 'required|in_list[10,20,30,40,50,60]',
            'min_password_strength'            => 'required|in_list[Lemah,Sedang,Kuat]',
            'web_session_timeout'              => 'required|integer|greater_than[0]',
            'password_lifetime_type'           => 'permit_empty|in_list[1,2,3,6,12,Kustom]',
            'password_lifetime_custom'         => 'permit_empty|integer|greater_than[0]',
            'password_expiration_warning_days' => 'permit_empty|integer|greater_than_equal_to[0]',
        ];

        $messages = [
            'max_failed_attempts' => [
                'required' => 'Upaya masuk gagal maksimum harus dipilih.',
                'in_list'  => 'Upaya masuk gagal harus bernilai antara 1 hingga 5 kali.'
            ],
            'lockout_duration' => [
                'required' => 'Durasi penguncian harus dipilih.',
                'in_list'  => 'Durasi penguncian harus bernilai antara 10 hingga 60 menit.'
            ],
            'min_password_strength' => [
                'required' => 'Kekuatan kata sandi minimum harus dipilih.',
                'in_list'  => 'Kekuatan kata sandi minimum tidak valid.'
            ],
            'web_session_timeout' => [
                'required'     => 'Durasi kedaluwarsa login web wajib diisi.',
                'integer'      => 'Durasi kedaluwarsa login web harus berupa angka.',
                'greater_than' => 'Durasi kedaluwarsa login web harus lebih besar dari 0.'
            ],
            'password_lifetime_type' => [
                'in_list' => 'Pilihan masa berlaku kata sandi tidak valid.'
            ],
            'password_lifetime_custom' => [
                'integer'      => 'Masa berlaku kustom harus berupa angka.',
                'greater_than' => 'Masa berlaku kustom harus lebih besar dari 0.'
            ],
            'password_expiration_warning_days' => [
                'integer'                => 'Hari peringatan harus berupa angka.',
                'greater_than_equal_to'  => 'Hari peringatan tidak boleh kurang dari 0.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        // Get values
        $maxFailedAttempts         = $this->request->getPost('max_failed_attempts');
        $lockoutDuration           = $this->request->getPost('lockout_duration');
        $minPasswordStrength       = $this->request->getPost('min_password_strength');
        $enableMaxPasswordLifetime = $this->request->getPost('enable_max_password_lifetime') ? '1' : '0';
        $webSessionTimeout         = $this->request->getPost('web_session_timeout');

        $passwordLifetimeType      = $this->request->getPost('password_lifetime_type') ?: '1';
        $passwordLifetimeCustom    = $this->request->getPost('password_lifetime_custom') ?: '1';
        $passwordWarningDays       = $this->request->getPost('password_expiration_warning_days') ?: '14';

        $db = \Config\Database::connect();
        $db->transStart();

        $settingModel->setSetting('max_failed_attempts', $maxFailedAttempts);
        $settingModel->setSetting('lockout_duration', $lockoutDuration);
        $settingModel->setSetting('min_password_strength', $minPasswordStrength);
        $settingModel->setSetting('enable_max_password_lifetime', $enableMaxPasswordLifetime);
        $settingModel->setSetting('web_session_timeout', $webSessionTimeout);

        $settingModel->setSetting('password_lifetime_type', $passwordLifetimeType);
        $settingModel->setSetting('password_lifetime_custom', $passwordLifetimeCustom);
        $settingModel->setSetting('password_expiration_warning_days', $passwordWarningDays);

        $db->transComplete();

        if ($db->transStatus() !== false) {
            $userId = session()->get('id') ?? 1;
            $auditLog->logAction('UPDATE_SETTINGS', 'settings', 'system', [
                'max_failed_attempts'          => $maxFailedAttempts,
                'lockout_duration'             => $lockoutDuration,
                'min_password_strength'        => $minPasswordStrength,
                'enable_max_password_lifetime' => $enableMaxPasswordLifetime,
                'web_session_timeout'          => $webSessionTimeout,
                'password_lifetime_type'       => $passwordLifetimeType,
                'password_lifetime_custom'     => $passwordLifetimeCustom,
                'password_expiration_warning_days' => $passwordWarningDays,
            ]);

            return redirect()->to('/admin/security')->with('success', 'Parameter dasar berhasil disimpan.')->with('activeTab', 'settings');
        }

        return redirect()->back()->withInput()->with('error', 'Gagal menyimpan parameter dasar.');
    }
}
