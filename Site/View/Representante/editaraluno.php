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

$id_aluno = $_GET['id_aluno'] ?? null;

if (!$id_aluno) {
    header('Location: aluno.php');
    exit();
}

$aluno = $usuarioController->obterAlunoPorId($id_aluno);

if (!$aluno) {
    header('Location: aluno.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $rm = $_POST['rm'];
    $genero = $_POST['genero'];

    $resultado = $usuarioController->atualizarAluno($id_aluno, $nome, $rm,$genero);

    if ($resultado) {
        header('Location: aluno.php?atualizacao=sucesso');
        exit();
    } else {
        echo "<script>
            window.alert('Não foi possível atualizar. Verifique se o RM já está cadastrado.');
        </script>";
        header('Location: aluno.php?atualizacao=erro');
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Aluno</title>
</head>
<body>
    <form action="editaraluno.php" method="POST">
        <input type="hidden" name="id_aluno" value="<?= $aluno['id'] ?>">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($aluno['nome']) ?>" required>

        <br><br>

        <label for="rm">RM:</label>
        <input type="number" id="rm" name="rm" value="<?= htmlspecialchars($aluno['rm']) ?>" required>

        <br><br>

        <label for="genero">Gênero:</label>
        <select id="genero" name="genero" required>
            <option value="masculino" <?= $aluno['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
            <option value="feminino" <?= $aluno['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
        </select>

        <input type="submit" value="Atualizar aluno">
    </form>

    <a href="aluno.php">Voltar</a>
</body>
</html>