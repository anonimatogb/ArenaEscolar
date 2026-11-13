<?php
session_start();

if (!isset($_SESSION['tipo'])) {
    $_SESSION['tipo'] = 'ADM';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arena Escolar</title>
</head>
<body>
    <?php if ($_SESSION['tipo'] === 'ADM'): ?>
        <a href="Site/View/Modalidade/modalidade.php">Cadastrar modalidade</a>
    <?php endif; ?>
</body>
</html>
