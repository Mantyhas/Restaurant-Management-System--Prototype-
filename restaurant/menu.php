<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Ar vartotojas yra ADMIN (manager)
$isMenuManager = is_admin();

// Pasirinktas restoranas
$restaurant_id = isset($_GET['restaurant']) ? (int)$_GET['restaurant'] : null;

// Redaguojama kategorija
$editCategory = ($isMenuManager && isset($_GET['edit'])) ? $_GET['edit'] : null;
$allowedCats = ['gerimai', 'pagrindinis', 'desertas'];
if (!in_array($editCategory, $allowedCats)) {
    $editCategory = null;
}

// Restoranų sąrašas
$restaurants = $pdo->query("SELECT id, name FROM restaurants ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Hidden map (kokie patiekalai nerodomi)
$hiddenMap = [];
if ($restaurant_id) {
    $stmt = $pdo->prepare("SELECT menu_item_id FROM menu_item_hidden WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) {
        $hiddenMap[(int)$id] = true;
    }
}

// POST — išsaugoti pakeitimus
$message = "";

if ($isMenuManager && $restaurant_id && $_SERVER['REQUEST_METHOD'] === 'POST') {

    // Išsaugoti matomumą
    if (isset($_POST['save_visibility']) && isset($_POST['category'])) {
        $cat = $_POST['category'];

        if (in_array($cat, $allowedCats)) {

            // gauti visus id
            $stmt = $pdo->prepare("SELECT id FROM menu_items WHERE category = ?");
            $stmt->execute([$cat]);
            $allIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $visible = isset($_POST['visible']) ? array_map('intval', $_POST['visible']) : [];

            // trinti buvusius
            $del = $pdo->prepare("
                DELETE h FROM menu_item_hidden h
                JOIN menu_items m ON h.menu_item_id = m.id
                WHERE h.restaurant_id = ? AND m.category = ?
            ");
            $del->execute([$restaurant_id, $cat]);

            // įrašyti paslėptus
            $hidden = array_diff($allIds, $visible);
            if (!empty($hidden)) {
                $ins = $pdo->prepare("
                    INSERT INTO menu_item_hidden (menu_item_id, restaurant_id)
                    VALUES (?, ?)
                ");
                foreach ($hidden as $mid) {
                    $ins->execute([$mid, $restaurant_id]);
                }
            }

            $message = "Meniu atnaujintas.";
        }
    }

    // Pridėti naują patiekalą
    if (isset($_POST['add_dish'])) {
        $cat = $_POST['category'];
        $title = trim($_POST['title']);
        $price = (float)$_POST['price'];
        $description = trim($_POST['description']);

        if ($title && $price > 0 && in_array($cat, $allowedCats)) {
            $stmt = $pdo->prepare("
                INSERT INTO menu_items (title, price, category, description)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$title, $price, $cat, $description]);

            $message = "Patiekalas pridėtas!";
        } else {
            $message = "Neteisingi duomenys.";
        }
    }

    // atnaujinti map
    $hiddenMap = [];
    $stmt = $pdo->prepare("SELECT menu_item_id FROM menu_item_hidden WHERE restaurant_id = ?");
    $stmt->execute([$restaurant_id]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $id) {
        $hiddenMap[(int)$id] = true;
    }
}

// Visi patiekalai
$dishes = $pdo->query("
    SELECT id, title, price, category, description
    FROM menu_items
    ORDER BY category, title
")->fetchAll(PDO::FETCH_ASSOC);

// Skirstymas
$grouped = [];
foreach ($dishes as $d) {
    $grouped[$d['category']][] = $d;
}

$categoriesOrder = [
    'gerimai'     => 'Gėrimai',
    'pagrindinis' => 'Pagrindiniai',
    'desertas'    => 'Desertai'
];
?>

<h2 class="page-title">Meniu</h2>

<?php if ($message): ?>
<p class="reservation-success" style="font-size:16px;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>


<!-- Restoranų tabai -->
<div class="menu-rest-tabs">
<?php foreach ($restaurants as $rest): ?>
    <a href="menu.php?restaurant=<?= $rest['id'] ?><?= $editCategory ? '&edit='.$editCategory : '' ?>"
       class="btn <?= ($restaurant_id === (int)$rest['id']) ? 'btn-active' : 'btn-outline' ?>">
       <?= htmlspecialchars($rest['name']) ?>
    </a>
<?php endforeach; ?>
</div>


<!-- 3 stulpelių meniu -->
<div class="menu-columns">

<?php foreach ($categoriesOrder as $catKey => $catLabel): ?>
    <?php
    $colDishes = $grouped[$catKey] ?? [];
    $isEditingThis = ($editCategory === $catKey && $isMenuManager && $restaurant_id);
    ?>

    <div class="menu-column">

        <div class="menu-col-header"><?= htmlspecialchars($catLabel) ?></div>

        <div class="menu-col-body">

        <?php if ($isEditingThis): ?>
            <!-- REDAGAVIMAS -->
            <form method="post">
                <input type="hidden" name="category" value="<?= $catKey ?>">
                <input type="hidden" name="save_visibility" value="1">

                <ul class="edit-dish-list">

                <?php foreach ($colDishes as $dish): ?>
                    <?php $id = (int)$dish['id']; ?>
                    <?php $checked = !isset($hiddenMap[$id]); ?>

                    <li class="edit-row">
                        <span class="name"><?= htmlspecialchars($dish['title']) ?></span>
                        <span class="price"><?= number_format($dish['price'],2) ?> €</span>

                        <input type="checkbox"
                               class="check"
                               name="visible[]"
                               value="<?= $id ?>"
                               <?= $checked ? 'checked' : '' ?>>
                    </li>
                <?php endforeach; ?>

                </ul>

                <button class="btn btn-small" style="margin-top:12px;">Išsaugoti</button>
            </form>

            <hr>

            <!-- Naujo patiekalo pridėjimas -->
            <form method="post" style="margin-top:15px;">
                <input type="hidden" name="category" value="<?= $catKey ?>">
                <input type="hidden" name="add_dish" value="1">

                <label>Pavadinimas</label>
                <input type="text" name="title" required>

                <label>Kaina (€)</label>
                <input type="number" min="0" step="0.01" name="price" required>

                <label>Aprašymas</label>
                <textarea name="description" rows="2" style="width:100%;"></textarea>

                <button class="btn btn-small" style="margin-top:10px;">Pridėti</button>
            </form>

        <?php else: ?>

            <ul class="dish-list">
            <?php foreach ($colDishes as $dish): ?>
                <?php if ($restaurant_id && isset($hiddenMap[$dish['id']])) continue; ?>
                <li>
                    <span><?= htmlspecialchars($dish['title']) ?></span>
                    <span><?= number_format($dish['price'],2) ?> €</span>
                </li>
            <?php endforeach; ?>
            </ul>

        <?php endif; ?>

        </div>

        <div class="menu-col-footer">
            <?php if ($isMenuManager && $restaurant_id): ?>
                <?php if ($isEditingThis): ?>
                    <a class="btn btn-small btn-secondary"
                       href="menu.php?restaurant=<?= $restaurant_id ?>">Baigti</a>
                <?php else: ?>
                    <a class="btn btn-small"
                       href="menu.php?restaurant=<?= $restaurant_id ?>&edit=<?= $catKey ?>">Redaguoti</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    </div>

<?php endforeach; ?>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

