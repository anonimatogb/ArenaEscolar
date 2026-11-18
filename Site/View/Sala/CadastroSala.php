
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit();
}   

include_once '../../../Db/database.php';
include_once '../../Controller/SalaController.php';

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

    <a href="../admin/inicioadmin.php">Voltar</a>

</body>
</html>
