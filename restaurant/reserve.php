<?php
include 'includes/header.php';
include 'includes/db.php';
include_once 'includes/auth.php';
require_login();

/*
|--------------------------------------------------------------------------
| 1. NUMATYTOS FUNKCIJOS LAISVAM NAUDOJIMUI
|--------------------------------------------------------------------------
*/
function default_status($t, $reserved) {
    return in_array($t, $reserved) ? "table-busy" : "table-free";
}

function default_link($t, $r, $d, $ti, $reserved) {
    if (in_array($t, $reserved)) return ""; // užimtas = nepaspaudžiamas
    return "href='reserve.php?book=$t&restaurant=$r&date=$d&time=$ti'";
}

/*
|--------------------------------------------------------------------------
| 2. PERŽIŪROS REŽIMAS (view_reservation)
|--------------------------------------------------------------------------
*/
$isViewing      = isset($_GET['view_reservation']);
$isRestSelected = isset($_GET['restaurant']);
$isTableSelected = isset($_GET['book']);

$statusFunc = "default_status";
$linkFunc   = "default_link";

if ($isViewing) {

    $resId = (int)$_GET['view_reservation'];

    // Gauti rezervacijos duomenis
    $stmt = $pdo->prepare("
        SELECT restaurant_id, table_number, datetime
        FROM reservations
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$resId, $_SESSION['user_id']]);
    $reservation = $stmt->fetch();

    if ($reservation) {
        $restaurant_id = $reservation['restaurant_id'];
        $selectedTable = $reservation['table_number'];

        // Išskaidyti datą ir laiką
        [$date, $full] = explode(" ", $reservation['datetime']);
        $time = substr($full, 0, 5);

        // Priverstinai įjungiam žemėlapį
        $isRestSelected = true;

        // Speciali funkcija peržiūrai – visada rodo table-selected
        function view_status($t, $reserved) {
    global $selectedTable;

    if ($t == $selectedTable) {
        return "table-selected";   // tavo rezervuotas – ryškiai pilkas + bold
    }

    return "table-busy";   // visi kiti – taip pat pilki ir nepaspaudžiami
}
        function view_link($t,$r,$d,$ti,$reserved) {
            return ""; // Peržiūros režime nieko nespausim
        }

        $statusFunc = "view_status";
        $linkFunc   = "view_link";
    }
}

?>

<h2 style="text-align:center;margin-top:30px;">
    <?= $isViewing ? "Jūsų rezervacijos peržiūra" : "Rezervuoti staliuką" ?>
</h2>

<?php if (!$isRestSelected && !$isTableSelected): ?>
<!-- -------------------------------------------------------
     1. DATOS IR LAIKO PASIRINKIMAS
------------------------------------------------------------ -->
<div class="form-box">
<form method="post">
    <label>Pasirinkite datą:</label>
    <input type="date" name="date" required>

    <label>Pasirinkite laiką:</label>
    <select name="time" required>
        <option value="">Pasirinkite laiką</option>
        <?php
        $times = [
            "10:00","10:30","11:00","11:30",
            "12:00","12:30","13:00","13:30",
            "14:00","14:30","15:00","15:30",
            "16:00","16:30","17:00","17:30",
            "18:00","18:30","19:00","19:30","20:00"
        ];
        foreach ($times as $t) echo "<option value='$t'>$t</option>";
        ?>
    </select>

    <button type="submit" name="check">Tikrinti laisvus restoranus</button>
</form>
</div>
<?php endif; ?>


<?php
/* -------------------------------------------------------
   2. RESTORANŲ SĄRAŠAS PAGAL DATĄ IR LAIKĄ
-------------------------------------------------------- */
if (isset($_POST['check'])) {

    $date = $_POST['date'];
    $time = $_POST['time'];
    $datetime = "$date $time:00";

    echo "<h3 style='text-align:center;'>Laisvi restoranai ($datetime)</h3>";

    $query = "
        SELECT r.id, r.name 
        FROM restaurants r
        WHERE EXISTS (
            SELECT * FROM tables t
            WHERE t.restaurant_id = r.id
            AND t.table_number NOT IN (
                SELECT table_number FROM reservations
                WHERE restaurant_id = r.id AND datetime = ?
            )
        )
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$datetime]);
    $restaurants = $stmt->fetchAll();

    if (!$restaurants) {
        echo "<p class='no-restaurants'>Nėra laisvų restoranų šiuo metu.</p>";
    } else {
        echo "<div class='restaurant-list'>";
        foreach ($restaurants as $r) {
            echo "
                <a class='restaurant-card'
                href='reserve.php?restaurant={$r['id']}&date=$date&time=$time'>
                    {$r['name']}
                </a>
            ";
        }
        echo "</div>";
    }
}


/* -------------------------------------------------------
   3. RESTORANO STALIUKŲ ŽEMĖLAPIS
-------------------------------------------------------- */
if ($isRestSelected) {

    // Jei ne peržiūros režimas – paimti GET duomenis
    if (!$isViewing) {
        $restaurant_id = $_GET['restaurant'];
        $date = $_GET['date'];
        $time = $_GET['time'];
    }

    // Restorano pavadinimas
    $stmtName = $pdo->prepare("SELECT name FROM restaurants WHERE id = ?");
    $stmtName->execute([$restaurant_id]);
    $restName = $stmtName->fetchColumn();

    // Užimti stalai
    $stmt = $pdo->prepare("
        SELECT table_number
        FROM reservations
        WHERE restaurant_id = ? AND datetime = ?
    ");
    $stmt->execute([$restaurant_id, "$date $time:00"]);
    $reservedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<h3 style='text-align:center;margin-top:30px;'>
            Restoranas: <span style='color:#ff2626; font-weight:bold;'>$restName</span><br>
            <span style='font-size:16px;'>$date $time</span>
          </h3>";

    // Funkcijos sutrumpinimui
    $status = $statusFunc;
    $link   = $linkFunc;

    ?>

    <div class="floorplan">

        <div class="table-row">
            <a <?= $link(1,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(1,$reservedTables) ?>">Stalas 1</a>
            <a <?= $link(2,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(2,$reservedTables) ?>">Stalas 2</a>
            <a <?= $link(3,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(3,$reservedTables) ?>">Stalas 3</a>
        </div>

        <div class="table-row">
            <a <?= $link(4,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(4,$reservedTables) ?>">Stalas 4</a>
            <a <?= $link(5,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(5,$reservedTables) ?>">Stalas 5</a>
            <a <?= $link(6,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(6,$reservedTables) ?>">Stalas 6</a>
        </div>

        <div class="table-row">
            <a <?= $link(7,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(7,$reservedTables) ?>">Stalas 7</a>
            <a <?= $link(8,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(8,$reservedTables) ?>">Stalas 8</a>
            <a <?= $link(9,$restaurant_id,$date,$time,$reservedTables) ?> class="table-item <?= $status(9,$reservedTables) ?>">Stalas 9</a>
        </div>

        <div class="blocks">
            <div class="wc-block">WC 🚻</div>
            <div class="kitchen-block">Virtuvė 🍳</div>
        </div>

        <div class="entrance">← Įėjimas</div>

    </div>

<?php
}


/* -------------------------------------------------------
   4. REZERVACIJOS SAUGOJIMAS
-------------------------------------------------------- */
if ($isTableSelected) {

    $table = $_GET['book'];
    $restaurant_id = $_GET['restaurant'];
    $date = $_GET['date'];
    $time = $_GET['time'];

    $datetime = "$date $time:00";

    // leidžiam max 5 patvirtintas rezervacijas
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id=? AND status='patvirtinta'");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetchColumn();

    $status = ($count >= 5) ? 'patvirtinta' : 'pateikta';

    $insert = $pdo->prepare("
        INSERT INTO reservations (user_id, restaurant_id, table_number, datetime, status)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->execute([$_SESSION['user_id'], $restaurant_id, $table, $datetime, $status]);

    echo "<p class='reservation-success'>
            Rezervacija atlikta! Statusas: <b>$status</b>
          </p>";
}

include 'includes/footer.php';
?>

