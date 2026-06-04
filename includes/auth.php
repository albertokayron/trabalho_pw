<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    $path_prefix = '';
    if (file_exists('login.php')) {
        $path_prefix = '';
    } elseif (file_exists('../login.php')) {
        $path_prefix = '../';
    }
    header("Location: " . $path_prefix . "login.php?error=unauthorized");
    exit;
}
?>
