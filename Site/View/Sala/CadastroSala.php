
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../Usuario/Login.php');
    exit();
}   

require_once dirname(__DIR__, 3) . '/Db/database.php';
require_once __DIR__ . '/../../Controller/SalaController.php';

$salaController = new SalaController($pdo);
$salas = $salaController->listarSalas();
?>  

<!DOCTYPE html>  
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salas</title>
</head>
<body>

    <h1>Salas cadastradas</h1>

    <?php if (!empty($salas)): ?>

        <?php foreach ($salas as $sala): ?>
            <p>
                <?= htmlspecialchars($sala['nome']) ?>
                - Divisão <?= htmlspecialchars($sala['divisao']) ?>
            </p>
        <?php endforeach; ?>

    <?php else: ?>

        <p>Nenhuma sala encontrada.</p>

    <?php endif; ?>

    <a href="../../../index.php">Voltar</a>

</body>
</html>
