
<?php
session_start();

if (empty($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$_SESSION = array();

session_destroy();

header('Location: login.php');
exit();
