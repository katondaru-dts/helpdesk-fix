<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\AuditLogModel;

class Categories extends BaseController
{
    public function index()
    {
        $catModel = new CategoryModel();
        $db = \Config\Database::connect();
        $cats = $db->query("SELECT c.*, (SELECT COUNT(*) FROM tickets t WHERE t.cat_id = c.id) as ticket_count FROM categories c ORDER BY c.name ASC")->getResultArray();

        $data = [
            'pageTitle'  => 'Kelola Kategori',
            'activePage' => 'category-management',
            'categories' => $cats,
        ];

        return view('admin/categories/index', $data);
    }

    public function save()
    {
        $catModel = new CategoryModel();
        $auditLog = new AuditLogModel();
        $id          = $this->request->getPost('id');
        $name        = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if ($id) {
            $data = ['name' => $name, 'description' => $description];
            $catModel->update($id, $data);
            $auditLog->logAction('UPDATE', 'categories', $id, $data);
            return redirect()->to('/admin/categories')->with('success', 'Kategori diperbarui.');
        } else {
            $data = ['name' => $name, 'description' => $description, 'is_active' => 1];
            $catModel->insert($data);
            $auditLog->logAction('CREATE', 'categories', $catModel->getInsertID(), $data);
            return redirect()->to('/admin/categories')->with('success', 'Kategori ditambahkan.');
        }
    }

    public function toggleStatus()
    {
        $id      = $this->request->getPost('id');
        $current = $this->request->getPost('current');

        $catModel = new CategoryModel();
        $auditLog = new AuditLogModel();
        $newStatus = $current == 1 ? 0 : 1;
        $catModel->update($id, ['is_active' => $newStatus]);
        $auditLog->logAction('TOGGLE_STATUS', 'categories', $id, ['is_active' => $newStatus]);
        return redirect()->to('/admin/categories')->with('success', 'Status diubah.');
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        $db = \Config\Database::connect();
        $count = $db->query("SELECT COUNT(*) as c FROM tickets WHERE cat_id = ?", [$id])->getRow()->c;

        if ($count == 0) {
            $catModel = new CategoryModel();
            $auditLog = new AuditLogModel();
            $catModel->delete($id);
            $auditLog->logAction('DELETE', 'categories', $id);
            return redirect()->to('/admin/categories')->with('success', 'Kategori dihapus.');
        }
        return redirect()->to('/admin/categories')->with('error', 'Tidak dapat menghapus kategori yang sudah terpakai di tiket.');
    }
}
