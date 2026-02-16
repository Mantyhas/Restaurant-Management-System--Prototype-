<?php
session_start();
include '../includes/auth.php';
include '../includes/db.php';
include '../includes/header.php';

// DEBUG — parodyti rolę
// echo "ROLE: " . $_SESSION['role'];  // gali ištrinti

// Tik "manager" (administratoriui) ir "admin" (vadybininkui)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager')) {
    echo "<p>Prieiga tik administratoriui arba vadybininkui.</p>";
    include '../includes/footer.php';
    exit;
}

// --- rezervacijų statistika ---
$total_reservations = $pdo->query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pending_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='laukiama'")->fetchColumn();
$approved_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='patvirtinta'")->fetchColumn();
$rejected_reservations = $pdo->query("SELECT COUNT(*) FROM reservations WHERE status='atmesta'")->fetchColumn();
?>

<h2 style="text-align:center;">Administratoriaus valdymo skydelis</h2>
<p style="text-align:center;">Sveiki, <b><?= htmlspecialchars($_SESSION['name']); ?></b>!</p>

<div style="max-width:500px; margin:20px auto;">
    <h3>Rezervacijų apžvalga</h3>
    <ul>
        <li>Iš viso rezervacijų: <b><?= $total_reservations ?></b></li>
        <li>Laukiama patvirtinimo: <b><?= $pending_reservations ?></b></li>
        <li>Patvirtintos: <b><?= $approved_reservations ?></b></li>
        <li>Atmestos: <b><?= $rejected_reservations ?></b></li>
    </ul>

    <h3>Veiksmai</h3>
    <ul>
        <li><a href="reservations.php">Peržiūrėti / tvarkyti rezervacijas</a></li>
        <li><a href="../logout.php">Atsijungti</a></li>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>

