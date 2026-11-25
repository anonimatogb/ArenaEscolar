
<?php
session_start();

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';

$erro = '';
$etapaSenha = false;
$rmInformado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rm = is_string($_POST['rm'] ?? null) ? trim($_POST['rm']) : '';
    $rmInformado = $rm;
    $usuarioController = new UsuarioController($pdo);

    if ($rm === '') {
        $erro = 'Informe seu RM.';
    } elseif (($_POST['etapa'] ?? '') === 'senha') {
        $etapaSenha = true;
        $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
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
            $erro = 'Senha incorreta. No primeiro acesso, crie uma senha com pelo menos 4 caracteres.';
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
        }

        if ($usuarioController->buscarRepresentantePorRM($rm)) {
            $etapaSenha = true;
        } else {
            $erro = 'Não encontramos uma conta com esse RM.';
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
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Usuario.css">
</head>

<body>

    <h1>Login</h1>

    <form action="Login.php" method="POST">
        <?php if ($etapaSenha): ?>
            <input type="hidden" name="etapa" value="senha">
            <input
                type="hidden"
                name="rm"
                value="<?= htmlspecialchars($rmInformado, ENT_QUOTES, 'UTF-8') ?>"
            >

            <p>RM: <strong><?= htmlspecialchars($rmInformado, ENT_QUOTES, 'UTF-8') ?></strong></p>

            <label for="senha">Senha:</label>
            <input
                type="password"
                id="senha"
                name="senha"
                required
                minlength="4"
                autocomplete="current-password"
                autofocus
            ><br><br>

            <input type="submit" value="Entrar">
            <a href="Login.php">Voltar</a>
        <?php else: ?>
            <input type="hidden" name="etapa" value="identificar">
            <label for="rm">RM:</label>
            <input
                type="text"
                id="rm"
                name="rm"
                value="<?= htmlspecialchars($rmInformado, ENT_QUOTES, 'UTF-8') ?>"
                required
                autocomplete="username"
                autofocus
            ><br><br>

            <input type="submit" value="Continuar">
        <?php endif; ?>

        <a href="../Administrador/login.php">Sou professor</a>
        <a href="../../../index.php">Voltar para a página inicial</a>
    </form>

    <?php if ($erro !== ''): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>  

</body>

</html>
