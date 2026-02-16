<?php
include 'includes/header.php';
include 'includes/db.php';
include_once 'includes/auth.php';

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$name, $email, $hash]);
        echo "<p style='color:green; text-align:center;'>Registracija sėkminga! Dabar galite prisijungti.</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red; text-align:center;'>El. paštas jau naudojamas.</p>";
    }
}
?>

<div class="page-container">

    <div class="form-box">
        <h2 class="page-title" style="margin-bottom: 20px;">Registracija</h2>

        <form method="post">
            <label>Vardas:</label>
            <input type="text" name="name" required>

            <label>El. paštas:</label>
            <input type="email" name="email" required>

            <label>Slaptažodis:</label>
            <input type="password" name="password" required>

            <button type="submit" name="submit">Registruotis</button>

            <p style="text-align:center; margin-top:10px;">
                Jau turite paskyrą?
                <a href="login.php">Prisijunkite</a>
            </p>
        </form>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

