<?php
include '../includes/header.php';
include '../includes/db.php';
include_once '../includes/auth.php';

// Tik adminui / vadybininkui
if (!is_manager()) {
    echo "<p>Prieiga tik administratoriui.</p>";
    include '../includes/footer.php';
    exit;
}

// --- Patvirtinimo ir atmetimo veiksmai ---
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $pdo->prepare("UPDATE reservations SET status='patvirtinta' WHERE id=?");
    $stmt->execute([$id]);
    echo "<p style='color:green;'>Rezervacija patvirtinta!</p>";
}

if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $stmt = $pdo->prepare("UPDATE reservations SET status='atmesta' WHERE id=?");
    $stmt->execute([$id]);
    echo "<p style='color:red;'>Rezervacija atmesta!</p>";
}

// --- Rezervacijų sąrašas ---
echo "<h2>Visos rezervacijos</h2>";

$stmt = $pdo->query("
    SELECT r.id, u.name AS user_name, r.restaurant, r.datetime, r.status
    FROM reservations r
    JOIN users u ON r.user_id = u.id
    ORDER BY r.datetime DESC
");

$reservations = $stmt->fetchAll();

if ($reservations && count($reservations) > 0) {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>Vartotojas</th>
            <th>Restoranas</th>
            <th>Data ir laikas</th>
            <th>Statusas</th>
            <th>Veiksmas</th>
          </tr>";

    foreach ($reservations as $row) {
        $user = htmlspecialchars($row['user_name']);
        $restaurant = htmlspecialchars($row['restaurant']);
        $datetime = htmlspecialchars($row['datetime']);
        $status = htmlspecialchars($row['status']);

        echo "<tr>
                <td>$user</td>
                <td>$restaurant</td>
                <td>$datetime</td>
                <td>$status</td>
                <td>";

        if ($status === 'pateikta') {
            echo "<a href='?approve={$row['id']}'>Patvirtinti</a> |
                  <a href='?reject={$row['id']}'>Atmesti</a>";
        } else {
            echo "—";
        }

        echo "</td></tr>";
    }

    echo "</table>";
} else {
    echo "<p>Nėra jokių rezervacijų.</p>";
}

include '../includes/footer.php';
?>

