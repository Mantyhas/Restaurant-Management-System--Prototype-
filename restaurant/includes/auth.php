<?php
// Užtikriname kad sesija paleista tik vieną kartą
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* --- LOGIN BŪSENA --- */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/* --- ROLĖS --- */
function is_manager() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'manager';
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/* --- APSAUGOS --- */
function require_login() {
    if (!is_logged_in()) {
        header("Location: /restaurant/login.php");
        exit;
    }
}

function require_manager() {
    if (!is_manager()) {
        echo "<p>Prieiga tik vadybininkui.</p>";
        exit;
    }
}

function require_manager_or_admin() {
    if (!is_manager() && !is_admin()) {
        echo "<p>Prieiga tik vadybininkui arba administratoriui.</p>";
        exit;
    }
}
?>

