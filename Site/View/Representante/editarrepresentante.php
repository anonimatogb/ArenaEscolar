<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'representante' ||
    !isset($_SESSION['id_sala'])
) {
    header('Location: ../Usuario/Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

$usuarioController = new UsuarioController($pdo);

$id_representante = $_SESSION['id_usuario'];

if (!$id_representante) {
    header('Location: inicio.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['nome'] = $_POST['nome'];

    $nome = $_POST['nome'];
   
    $senha = $_POST['senha'];

    $resultado = $usuarioController->atualizarRepresentante($id_representante, $nome, $senha);

    if ($resultado) {
        header('Location: inicio.php?atualizacao=sucesso');
        exit();
    } else {
        echo "<script>
            window.alert('Não foi possível atualizar. Verifique se o RM já está cadastrado.');
        </script>";
        header('Location: inicio.php?atualizacao=erro');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Representante</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Representante.css">
</head>
<body>
    <form action="editarrepresentante.php" method="POST">
        <input type="hidden" name="id_representante" value="<?= $_SESSION['id_usuario'] ?>">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($_SESSION['nome']) ?>" required>

        <br><br>

        <label for="senha">senha:</label>
        <input type="password" id="senha" name="senha" required>

        <br><br>

        <input type="submit" value="Atualizar representante">
    </form>

    <a href="inicio.php">Voltar</a>
</body>
</html>