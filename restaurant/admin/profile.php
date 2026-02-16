<?php
include '../includes/header.php';
include '../includes/db.php';
include_once '../includes/auth.php';

require_login();

echo "<h2>Administratoriaus paskyra</h2>";
echo "<p>Sveiki, <b>" . htmlspecialchars($_SESSION['name']) . "</b>!</p>";
echo "<p>Jūsų rolė: <b>" . htmlspecialchars($_SESSION['role']) . "</b></p>";

echo "<p><a href='dashboard.php'>Grįžti į valdymo skydelį</a></p>";
echo "<p><a href='../logout.php'>Atsijungti</a></p>";

include '../includes/footer.php';
?>

