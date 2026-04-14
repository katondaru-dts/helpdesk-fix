<?php
$db = new \PDO("mysql:host=db;dbname=helpdesk_v2", "root", "root_password");
$stmt = $db->query("SELECT id, name, email, role_id FROM users");
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    echo $row['id'] . " | " . $row['name'] . " | " . $row['email'] . " | Role: " . $row['role_id'] . "\n";
}
