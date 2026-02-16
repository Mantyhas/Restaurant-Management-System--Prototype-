<?php
include '../includes/header.php';
include '../includes/db.php';
include_once '../includes/auth.php';

require_admin();

// =====================
// INFO ŽINUTĖ
// =====================
$info = "";

// =====================
// NAUJO PATIEKALO PRIDĖJIMAS
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_dish'])) {
    $title = trim($_POST['title']);
    $price = (float)$_POST['price'];
    $category = $_POST['category'];
    $description = trim($_POST['description']);

    if ($title && $price > 0 && in_array($category, ['pagrindinis','desertas','gerimai'])) {
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (title, price, category, description)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$title, $price, $category, $description]);
        $info = "Patiekalas sėkmingai pridėtas!";
    } else {
        $info = "Patikrinkite duomenis.";
    }
}

// =====================
// REDAGAVIMO: IŠSAUGOTI MATOMUMĄ
// =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_visibility'])) {
    $visible = $_POST['visible'] ?? [];

    // ištrinti senus
    $pdo->query("DELETE FROM menu_visibility");

    // pridėti naujus
    $stmt = $pdo->prepare("INSERT INTO menu_visibility (item_id) VALUES (?)");

    foreach ($visible as $id) {
        $stmt->execute([$id]);
    }

    $info = "Meniu atnaujintas!";
}

// =====================
// GAUTI VISUS PATIEKALUS
// =====================
$items = $pdo->query("
    SELECT * FROM menu_items ORDER BY category, title
")->fetchAll(PDO::FETCH_ASSOC);

// GAUTI MATOMUS PATIEKALUS
$visible_items = $pdo->query("SELECT item_id FROM menu_visibility")
                     ->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="page-container">

    <h2 class="page-title">Meniu valdymas (ADMIN)</h2>

    <?php if ($info): ?>
        <p style="color:green; font-size:18px; text-align:center;">
            <?= htmlspecialchars($info) ?>
        </p>
    <?php endif; ?>


    <!-- ======================================= -->
    <!--          PRIDĖTI PATIEKALĄ              -->
    <!-- ======================================= -->

    <div class="form-box" style="width:500px; margin-bottom:40px;">
        <h3>Pridėti naują patiekalą</h3>

        <form method="post">
            <label>Pavadinimas:</label>
            <input type="text" name="title" required>

            <label>Kaina (€):</label>
            <input type="number" name="price" step="0.01" min="0" required>

            <label>Kategorija:</label>
            <select name="category" required>
                <option value="pagrindinis">Pagrindinis</option>
                <option value="desertas">Desertas</option>
                <option value="gerimai">Gėrimas</option>
            </select>

            <label>Aprašymas:</label>
            <textarea name="description" rows="3"></textarea>

            <button type="submit" name="add_dish">Išsaugoti</button>
        </form>
    </div>


    <!-- ======================================= -->
    <!--          REDAGUOTI MATOMUMĄ             -->
    <!-- ======================================= -->

    <h3 style="text-align:center;">Redaguoti matomus patiekalus</h3>

    <form method="post" class="menu-edit-wrapper">

        <ul class="edit-dish-list">

            <?php foreach ($items as $dish): ?>
                <?php $isVisible = in_array($dish['id'], $visible_items); ?>

                <li>
                    <span class="edit-dish-name"><?= htmlspecialchars($dish['title']) ?></span>
                    <span class="edit-dish-price"><?= number_format($dish['price'], 2) ?> €</span>

                    <input type="checkbox"
                           class="edit-dish-checkbox"
                           name="visible[]"
                           value="<?= $dish['id'] ?>"
                           <?= $isVisible ? 'checked' : '' ?>>
                </li>

            <?php endforeach; ?>

        </ul>

        <button type="submit" name="save_visibility" style="margin-top:20px;">
            Išsaugoti matomumą
        </button>

    </form>

</div>

<?php include '../includes/footer.php'; ?>

