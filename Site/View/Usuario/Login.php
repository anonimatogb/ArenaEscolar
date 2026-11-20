
<?php
session_start();

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rm = $_POST['rm'];
    $senha = $_POST['senha'] ?? '';
   
    $usuarioController = new UsuarioController($pdo);

    if ($senha !== '') {
        $usuario = $usuarioController->login($rm, $senha);

        if ($usuario) {
            $_SESSION['id_usuario'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome'];
            $_SESSION['rm'] = $usuario['rm'];
            $_SESSION['tipo'] = $usuario['tipo'];
            $_SESSION['id_sala'] = $usuario['id_sala'];

            header('Location: ../../../index.php');
            exit();
        } else {
            $erro = 'RM ou senha incorretos.';
        }
    } else {
        $aluno = $usuarioController->buscarAlunoPorRM($rm);

        if ($aluno) {
            $_SESSION['id_usuario'] = $aluno['id'];
            $_SESSION['nome'] = $aluno['nome'];
            $_SESSION['rm'] = $aluno['rm'];
            $_SESSION['tipo'] = 'aluno';
            $_SESSION['id_sala'] = $aluno['id_sala'];

            header('Location: ../../../index.php');
            exit();
        } else {
            $erro = 'Aluno não encontrado.';
        }
    }
}  
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>

<body>

    <form action="Login.php" method="POST">
        <label for="rm">RM:</label>
        <input type="text" id="rm" name="rm" required><br><br>

        <label for="senha">Senha (somente representantes):</label>
        <input type="password" id="senha" name="senha"><br><br>

        <input type="submit" value="Entrar">
        <a href="../Administrador/login.php">Sou professor</a>
        <a href="../../../index.php">Voltar para a página inicial</a>
    </form>

    <?php if ($erro !== ''): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>  

</body>

</html>
