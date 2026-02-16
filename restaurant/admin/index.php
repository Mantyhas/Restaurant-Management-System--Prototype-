<?php
// AUTH privalo būti pirmas
require_once __DIR__ . '/../includes/auth.php';

// Tada DB
require_once __DIR__ . '/../includes/db.php';

// Tik tada header
require_once __DIR__ . '/../includes/header.php';

// Tik manager arba admin
if (!is_manager() && !is_admin()) {
    echo "<div class='form-box'><p>Prieiga tik administratoriui arba vadybininkui.</p></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Patvirtinti / atmesti
if (isset($_GET['approve'])) {
    $pdo->prepare("UPDATE reservations SET status='patvirtinta' WHERE id=?")
        ->execute([intval($_GET['approve'])]);
}
if (isset($_GET['reject'])) {
    $pdo->prepare("UPDATE reservations SET status='atmesta' WHERE id=?")
        ->execute([intval($_GET['reject'])]);
}

// Gauti rezervacijas
$stmt = $pdo->query("
    SELECT r.id, r.table_number, r.datetime, r.status,
           u.name AS user_name,
           res.name AS restaurant_name
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    JOIN restaurants res ON r.restaurant_id = res.id
    ORDER BY r.datetime DESC
");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="page-title">Rezervacijų valdymas</h2>

<div class="admin-table-container">
<table class="pretty-table">
    <thead>
        <tr>
            <th>Vartotojas</th>
            <th>Restoranas</th>
            <th>Staliukas</th>
            <th>Data ir laikas</th>
            <th>Statusas</th>
            <th>Veiksmai</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($reservations as $row): ?>
        <?php 
            $color = match ($row['status']) {
                'patvirtinta' => '#1f7a1f',
                'atmesta'     => '#b31d1d',
                default       => '#444'
            };
        ?>
        <tr>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
            <td><?= htmlspecialchars($row['table_number']) ?></td>
            <td><?= htmlspecialchars($row['datetime']) ?></td>
            <td style="font-weight:bold;color:<?= $color ?>;">
                <?= htmlspecialchars($row['status']) ?>
            </td>
            <td>
                <a class="btn-small" href="?approve=<?= $row['id'] ?>">Patvirtinti</a>
                <a class="btn-small btn-danger" href="?reject=<?= $row['id'] ?>">Atmesti</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

