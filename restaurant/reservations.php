<?php
include 'includes/header.php';
include 'includes/db.php';
include_once 'includes/auth.php';

// Tik prisijungusiems
require_login();

$user_id = $_SESSION['user_id'];

// Gauti rezervacijas (su rezervacijos ID)
$sql = "
    SELECT 
        res.id,
        r.name AS restaurant_name,
        res.restaurant_id,
        res.table_number,
        res.datetime,
        res.status
    FROM reservations res
    JOIN restaurants r ON res.restaurant_id = r.id
    WHERE res.user_id = ?
    ORDER BY res.datetime DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<div class="menu-edit-wrapper" style="margin-top:40px;">

    <h2 class="page-title" style="text-align:center; margin-bottom:25px;">
        Mano rezervacijos
    </h2>

    <?php if ($reservations): ?>
        
        <table class="menu-edit-table">
            <tr>
                <th>Restoranas</th>
                <th>Staliuko Nr.</th>
                <th>Data ir laikas</th>
                <th>Statusas</th>
            </tr>

            <?php foreach ($reservations as $row): ?>
                <?php
                    $color = match ($row['status']) {
                        'patvirtinta' => 'green',
                        'atmesta' => 'red',
                        default => '#6f4e37'
                    };

                    // Nuoroda į rezervacijos peržiūrą
                    $link = "reserve.php?view_reservation=" . $row['id'];
                ?>

                <tr onclick="window.location='<?= $link ?>'" style="cursor:pointer;">
                    <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
                    <td><?= htmlspecialchars($row['table_number']) ?></td>
                    <td><?= htmlspecialchars($row['datetime']) ?></td>
                    <td style="color:<?= $color ?>; font-weight:bold;">
                        <?= htmlspecialchars($row['status']) ?>
                    </td>
                </tr>

            <?php endforeach; ?>

        </table>

    <?php else: ?>
        <p style="text-align:center;">Jūs dar neturite jokių rezervacijų.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

