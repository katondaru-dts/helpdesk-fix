<?php
try {
    $pdo = new PDO("mysql:host=db;dbname=helpdesk", "helpdesk", "helpdesk123");
    $stmt = $pdo->query("SELECT email, name FROM users");
    while($row = $stmt->fetch()) { echo $row['email'] . " | " . $row['name'] . "\n"; }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
