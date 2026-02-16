<?php
include_once __DIR__ . '/auth.php';

$isLogged  = is_logged_in();
$role      = $_SESSION['role'] ?? null;

// Tikras administratorius (turi role: manager)
$isAdminPanel = ($role === 'manager');

// Vadybininkas (turi role: admin)
$isMenuManager = ($role === 'admin');
?>
<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Restoranų tinklas</title>
    <link rel="stylesheet" href="/restaurant/style.css">
</head>
<body>
<header>
    <nav>
        <a href="/restaurant/index.php">Pradžia</a>
        <a href="/restaurant/menu.php">Meniu</a>
        <a href="/restaurant/reserve.php">Rezervacija</a>

        <?php if ($isLogged): ?>

            <a href="/restaurant/reservations.php">Mano rezervacijos</a>
            <a href="/restaurant/profile.php">Mano paskyra</a>

            <!-- ADMIN zona tik role = manager -->
            <?php if ($isAdminPanel): ?>
                <a href="/restaurant/admin/index.php">Admin zona</a>
            <?php endif; ?>

     

            <a href="/restaurant/logout.php">Atsijungti</a>

        <?php else: ?>
            <a href="/restaurant/login.php">Prisijungti</a>
            <a href="/restaurant/register.php">Registruotis</a>
        <?php endif; ?>
    </nav>
</header>

