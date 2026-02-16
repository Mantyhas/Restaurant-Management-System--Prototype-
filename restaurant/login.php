<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';
include_once 'includes/auth.php';

/* ==========================
   1. AUTOMATINIS PRISIJUNGIMAS (BE FORMOS)
   ========================== */

function autoLogin($email, $password, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p style='color:red;'>Vartotojas nerastas.</p>";
        return;
    }

    if (!password_verify($password, $user['password_hash'])) {
        echo "<p style='color:red;'>Neteisingas slaptažodis.</p>";
        return;
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['name'] = $user['name'];

    // Redirect based on role
    if (in_array($user['role'], ['admin', 'manager'])) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

// MYGTUKAI
if (isset($_GET['quick'])) {
    switch ($_GET['quick']) {
        case "user":
            autoLogin("Mantas@gmail.com", "Mantas", $pdo);
            break;

        case "manager":
            autoLogin("Vadybininkas@gmail.com", "Vadybininkas", $pdo);
            break;

        case "admin":
            autoLogin("admin@test.lt", "admin123", $pdo);
            break;
    }
}

/* ==========================
   2. NORMALUS FORMOS LOGIN
   ========================== */

if (isset($_SESSION['user_id'])) {
    if (in_array($_SESSION['role'], ['admin', 'manager'])) {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        echo "<p style='color:green;'>Prisijungimas sėkmingas! Nukreipiama...</p>";

        if (in_array($user['role'], ['admin', 'manager'])) {
            echo "<meta http-equiv='refresh' content='1;url=admin/dashboard.php'>";
        } else {
            echo "<meta http-equiv='refresh' content='1;url=index.php'>";
        }

    } else {
        echo "<p style='color:red;'>Neteisingas el. paštas arba slaptažodis.</p>";
    }
}
?>

<!-- ========================== -->
<!--        LOGIN LENTELE       -->
<!-- ========================== -->

<h2 style="text-align:center; margin-top:20px;">Prisijungimas</h2>

<div class="form-box">
<form method="post">

  <label>El. paštas:</label>
  <input type="email" name="email" required>

  <label>Slaptažodis:</label>
  <input type="password" name="password" required>

  <button type="submit" name="login">Prisijungti</button>

</form>
</div>

<!-- ========================== -->
<!--   GREITAS DEMONSTRAVIMAS   -->
<!-- ========================== -->

<h3 style="text-align:center; margin-top:30px;">Greitas prisijungimas</h3>

<div style="width:300px; margin:0 auto; display:flex; flex-direction:column; gap:10px;">

    <a href="login.php?quick=user" class="btn" style="text-align:center;">
        Prisijungti kaip vartotojas
    </a>

    <a href="login.php?quick=manager" class="btn" style="text-align:center;">
        Prisijungti kaip vadybininkas
    </a>

    <a href="login.php?quick=admin" class="btn" style="text-align:center;">
        Prisijungti kaip administratorius
    </a>

</div>

<?php include 'includes/footer.php'; ?>

