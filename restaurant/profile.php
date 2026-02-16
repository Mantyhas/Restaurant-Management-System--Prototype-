<?php
include 'includes/header.php';
include 'includes/db.php';
include_once 'includes/auth.php';

require_login(); // tik prisijungusiems

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? 'Vartotojas';

// Gauti el. paštą iš DB
$stmtUser = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmtUser->execute([$user_id]);
$user = $stmtUser->fetch();
$email = $user['email'] ?? "";
?>

<!-- PROFILIO KORTELĖ -->
<div class="form-box" style="margin-top: 40px; width: 480px;">
    <h2 class="page-title" style="text-align:center; margin-bottom:20px;">
        Mano paskyra
    </h2>

    <p><strong>Vardas:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>El. paštas:</strong> <?= htmlspecialchars($email) ?></p>

    <div style="text-align:center; margin-top:25px;">
        <a href="reservations.php" class="btn btn-outline">Peržiūrėti rezervacijas</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

