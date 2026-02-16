<?php
include 'includes/header.php';
?>

<div class="hero-container">
    <div class="hero-text">
        <h1 class="hero-title">Restoranų tinklas</h1>
        <p class="hero-subtitle">
            Sveiki atvykę! Čia galite peržiūrėti meniu ir rezervuoti staliuką mūsų restoranuose.
        </p>

        <div class="hero-buttons">
            <a href="menu.php" class="btn btn-large">Peržiūrėti meniu</a>
            <a href="reserve.php" class="btn btn-large btn-outline">Rezervuoti staliuką</a>
        </div>
    </div>

    <div class="hero-image">
        <!-- naudoju absoliutų kelią iš svetainės šaknies -->
        <img src="/restaurant/restoranas.jpg" alt="Restoranas" />
    </div>
</div>

<div class="feature-row">
    <div class="feature-card">
        <h3>Meniu visiems restoranams</h3>
        <p>
            Peržiūrėkite patiekalus pagal temas – gėrimai, pagrindiniai patiekalai ir desertai
            visuose tinklo restoranuose.
        </p>
    </div>
    <div class="feature-card">
        <h3>Išmanus rezervavimas</h3>
        <p>
            Pasirinkite datą ir laiką, matykite laisvus staliukus restorano salės plane ir
            rezervuokite vienu paspaudimu.
        </p>
    </div>
    <div class="feature-card">
        <h3>Lojalumo sistema</h3>
        <p>
            Po 5 patvirtintų rezervacijų jūsų kitos rezervacijos tvirtinamos automatiškai –
            daugiau laiko mėgautis maistu.
        </p>
    </div>
</div>

<?php
include 'includes/footer.php';
?>

