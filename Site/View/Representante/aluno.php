
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = $_POST['nome'];
    $rm = $_POST['rm'];
    $genero = $_POST['genero'];

    $resultado = $usuarioController->cadastrarAluno(
        $nome,
        $rm,
        $_SESSION['id_sala'],
        $genero
    );

    if ($resultado) {
            header('Location: aluno.php?cadastro=sucesso');
        exit();
    } else {
        echo "<script>
            window.alert('Não foi possível cadastrar. Verifique se o RM já está cadastrado.');
        </script>";
    }
}

$alunos = $usuarioController->listarAlunosPorSala($_SESSION['id_sala']);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alunos da minha sala</title>
</head>

<body>

    <h1>Alunos da minha sala</h1>

    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
        <p>Aluno cadastrado com sucesso.</p>
    <?php endif; ?>

    <h2>Cadastrar aluno</h2>

    <form action="aluno.php" method="POST">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <br><br>

        <label for="rm">RM:</label>
        <input type="number" id="rm" name="rm" required>

        <br><br>

        <label for="genero">Gênero:</label>
        <select id="genero" name="genero" required>
            <option value="masculino">Masculino</option>
            <option value="feminino">Feminino</option>
        </select>

        <br><br>

        <input type="submit" value="Cadastrar aluno">

    </form>
  
    <h2>Alunos cadastrados</h2>  

    <?php if (!empty($alunos)): ?>

        <?php foreach ($alunos as $aluno): ?>
            <p>
                Nome: <?= htmlspecialchars($aluno['nome']) ?>
                - RM: <?= htmlspecialchars($aluno['rm']) ?>
                - Gênero: <?= htmlspecialchars($aluno['genero']) ?>
                <a href="editaraluno.php?id_aluno=<?= $aluno['id']  ?>">Editar</a>
            </p>
        <?php endforeach; ?>

    <?php else: ?>

        <p>Nenhum aluno cadastrado nesta sala.</p>

    <?php endif; ?>

    <a href="inicio.php">Voltar</a>

</body>

</html>
