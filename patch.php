<?php
$f = 'app/Models/TicketModel.php';
$c = file_get_contents($f);
$c = str_replace(
    'public function getFilteredTickets($filters = [], $isStaff = false, $userId = null)',
    'public function getFilteredTickets($filters = [], $isStaff = false, $userId = null, $hasManagementPerm = true)',
    $c
);
$c = str_replace(
    "if (!\$isStaff && \$userId !== null) {\n            \$builder->where('tickets.reporter_id', \$userId);\n        }",
    "if (!\$isStaff && \$userId !== null) {\n            \$builder->where('tickets.reporter_id', \$userId);\n        } else if (\$isStaff && function_exists('is_technician') && is_technician() && !\$hasManagementPerm && \$userId !== null) {\n            \$builder->groupStart()\n                ->where('tickets.reporter_id', \$userId)\n                ->orWhere('tickets.assigned_to', \$userId)\n                ->orWhere('tickets.id IN (SELECT ticket_id FROM ticket_assignees WHERE user_id = ' . (int)\$userId . ')')\n                ->orWhere('(tickets.assigned_to IS NULL AND tickets.id NOT IN (SELECT ticket_id FROM ticket_assignees))')\n                ->groupEnd();\n        }",
    $c
);
file_put_contents($f, $c);

$f2 = 'app/Controllers/Tickets.php';
$c2 = file_get_contents($f2);

// Fix index and export
$c2 = str_replace(
    '$query = $ticketModel->getFilteredTickets($filters, $isStaff, $userId);',
    '$rolePerms = $session->get(\'permissions\') ?: [];
        $specialPerms = $session->get(\'user_permissions\') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));
        $hasManagementPerm = in_array(\'Full Access\', $userPerms) || in_array(\'Tugaskan Support\', $userPerms) || is_admin();
        $query = $ticketModel->getFilteredTickets($filters, $isStaff, $userId, $hasManagementPerm);',
    $c2
);
$c2 = str_replace(
    '$tickets = $ticketModel->getFilteredTickets($filters, $isStaff, $userId)->findAll();',
    '$rolePerms = $session->get(\'permissions\') ?: [];
        $specialPerms = $session->get(\'user_permissions\') ?: [];
        $userPerms = array_unique(array_merge($rolePerms, $specialPerms));
        $hasManagementPerm = in_array(\'Full Access\', $userPerms) || in_array(\'Tugaskan Support\', $userPerms) || is_admin();
        $tickets = $ticketModel->getFilteredTickets($filters, $isStaff, $userId, $hasManagementPerm)->findAll();',
    $c2
);

// Fix export
$c2 = preg_replace(
    '/public function export\(\)\s*\{\s*\$ticketModel = new TicketModel\(\);/s',
    "public function export()\n    {\n        if (!has_permission('Ekspor Data')) {\n            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');\n        }\n        \$ticketModel = new TicketModel();",
    $c2
);

// Fix create and store
$c2 = preg_replace(
    '/public function create\(\)\s*\{\s*\$catModel = new CategoryModel\(\);/s',
    "public function create()\n    {\n        if (!has_permission('Buat Tiket')) {\n            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');\n        }\n        \$catModel = new CategoryModel();",
    $c2
);

$c2 = preg_replace(
    '/public function store\(\)\s*\{\s*\$session = session\(\);\s*\$ticketModel = new TicketModel\(\);/s',
    "public function store()\n    {\n        if (!has_permission('Buat Tiket')) {\n            return redirect()->to('/tickets')->with('error', 'Akses ditolak.');\n        }\n        \$session = session();\n        \$ticketModel = new TicketModel();",
    $c2
);

// Fix reply
$c2 = str_replace(
    'public function reply($id)
    {
        $messageModel = new TicketMessageModel();
        $ticketModel = new TicketModel();
        $message = $this->request->getPost(\'message\');
        $isInternal = $this->request->getPost(\'is_internal\') ? 1 : 0;
        $session = session();',
    'public function reply($id)
    {
        $messageModel = new TicketMessageModel();
        $ticketModel = new TicketModel();
        
        $ticket = $ticketModel->find($id);
        if (!$ticket) return redirect()->back()->with(\'error\', \'Tiket tidak ditemukan.\');

        $session = session();
        $isStaff = is_staff();
        
        $canReply = false;
        if (!$isStaff && $ticket[\'reporter_id\'] == $session->get(\'id\')) {
            $canReply = true;
        } else if ($isStaff) {
            $userPerms = array_unique(array_merge($session->get(\'permissions\') ?: [], $session->get(\'user_permissions\') ?: []));
            if (has_permission(\'Tambah Solusi\') || in_array(\'Full Access\', $userPerms) || is_admin()) {
                $canReply = true;
            }
        }

        if (!$canReply && $session->get(\'role_id\') != 1) {
            return redirect()->back()->with(\'error\', \'Akses ditolak. Anda tidak memiliki izin untuk memberikan solusi.\');
        }

        $message = $this->request->getPost(\'message\');
        $isInternal = $this->request->getPost(\'is_internal\') ? 1 : 0;',
    $c2
);

// Fix updateStatus permissions checking for assigned techs
$c2 = str_replace(
    'if ($hasAssignees && !in_array($session->get(\'role_id\'), [1, 4])) {',
    '$rolePerms = $session->get(\'permissions\') ?: [];
            $specialPerms = $session->get(\'user_permissions\') ?: [];
            $userPerms = array_unique(array_merge($rolePerms, $specialPerms));
            $canUpdateAny = in_array(\'Update Status Tiket\', $userPerms) || in_array(\'Full Access\', $userPerms) || is_admin();

            if (is_staff() && !$canUpdateAny && $session->get(\'role_id\') != 4) {
                 return redirect()->back()->with(\'error\', \'Akses ditolak. Anda tidak memiliki izin untuk mengubah status tiket.\');
            }

            if ($hasAssignees && !in_array($session->get(\'role_id\'), [1, 4]) && !$canUpdateAny) {',
    $c2
);

// Fix hasManagementPerm inside detail()
$c2 = str_replace(
    '$hasManagementPerm = in_array(\'Full Access\', $userPerms) || in_array(\'Update Status Tiket\', $userPerms) || in_array(\'Tugaskan Support\', $userPerms);',
    '$hasManagementPerm = in_array(\'Full Access\', $userPerms) || in_array(\'Tugaskan Support\', $userPerms) || is_admin();',
    $c2
);

file_put_contents($f2, $c2);
echo "Patched.";
