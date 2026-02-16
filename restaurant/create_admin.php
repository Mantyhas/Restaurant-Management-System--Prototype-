<?php
include 'includes/db.php';
$hash = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (name, email, password_hash, role) VALUES ('Adminas', 'admin@test.lt', ?, 'manager')";
$stmt = $pdo->prepare($sql);
$stmt->execute([$hash]);
echo "✅ Sukurta vadybininko paskyra (admin@test.lt / admin123)";

