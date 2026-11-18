
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location.href='Login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início</title>
</head>
<body> 
    
    <h1>Bem-vindo ao Arena Escolar!</h1>

    <p>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>!</p>

</body>
</html>
