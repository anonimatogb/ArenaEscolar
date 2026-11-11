<?php

$dns='mysql:host=localhost;dbname=arenaescolar;charset=utf8';
$usuario='root';
$senha='';
try {
    $pdo = new PDO($dns, $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
