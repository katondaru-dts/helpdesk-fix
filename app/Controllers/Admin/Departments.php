<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\AuditLogModel;

class Departments extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->getFlashdata('success')) {
            $session->setFlashdata('success', $session->getFlashdata('success'));
        }
        if ($session->getFlashdata('error')) {
            $session->setFlashdata('error', $session->getFlashdata('error'));
        }

        return redirect()->to('/admin/security?activeTab=depts');
    }

    public function save()
    {
        $deptModel = new DepartmentModel();
        $auditLog  = new AuditLogModel();
        $id   = $this->request->getPost('id');
        $name = $this->request->getPost('name');

        if ($id) {
            $data = ['name' => $name];
            $deptModel->update($id, $data);
            $auditLog->logAction('UPDATE', 'departments', $id, $data);
            return redirect()->to('/admin/departments')->with('success', 'Departemen diperbarui.');
        } else {
            $data = ['name' => $name, 'is_active' => 1];
            $deptModel->insert($data);
            $auditLog->logAction('CREATE', 'departments', $deptModel->getInsertID(), $data);
            return redirect()->to('/admin/departments')->with('success', 'Departemen ditambahkan.');
        }
    }

    public function toggleStatus()
    {
        $id      = $this->request->getPost('id');
        $current = $this->request->getPost('current');

        if ($id != 1) {
            $deptModel = new DepartmentModel();
            $auditLog  = new AuditLogModel();
            $newStatus = $current == 1 ? 0 : 1;
            $deptModel->update($id, ['is_active' => $newStatus]);
            $auditLog->logAction('TOGGLE_STATUS', 'departments', $id, ['is_active' => $newStatus]);
            return redirect()->to('/admin/departments')->with('success', 'Status diubah.');
        }
        return redirect()->to('/admin/departments')->with('error', 'Departemen utama tidak dapat dinonaktifkan.');
    }

    public function delete()
    {
        $id    = $this->request->getPost('id');
        $db    = \Config\Database::connect();
        $count = $db->query("SELECT COUNT(*) as c FROM users WHERE dept_id = ?", [$id])->getRow()->c;

        if ($count == 0) {
            $deptModel = new DepartmentModel();
            $auditLog  = new AuditLogModel();
            $deptModel->delete($id);
            $auditLog->logAction('DELETE', 'departments', $id);
            return redirect()->to('/admin/departments')->with('success', 'Departemen dihapus.');
        }
        return redirect()->to('/admin/departments')->with('error', 'Tidak dapat menghapus departemen yang masih memiliki user.');
    }
}
