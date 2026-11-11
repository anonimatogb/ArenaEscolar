
<?php
session_start();

require_once __DIR__ . '/../Controller/UsuarioController.php';

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

$usuarioController = new UsuarioController();

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = !empty($_POST['nome'])
        ? trim($_POST['nome'])
        : null;

    $senha = !empty($_POST['senha'])
        ? $_POST['senha']
        : null;

    $id = $_SESSION['id_usuario'];

    if ($nome === null && $senha === null) {

        $erro = 'Insira pelo menos uma informação para editar.';

    } else {

        $resultado = $usuarioController->editar($nome, $senha, $id);

        if ($resultado) {

            if ($nome !== null) {
                $_SESSION['nome'] = $nome;
            }

            $sucesso = 'Perfil atualizado com sucesso!';

        } else {

            $erro = 'Erro ao editar usuário.';
        }
    }
}

if ($_SESSION['tipo'] == 0) {
    $paginaInicio = 'inicio.php';
} elseif ($_SESSION['tipo'] == 1) {
    $paginaInicio = 'inicio_sala.php';
} else {
    $paginaInicio = 'inicio_admin.php';
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
</head>

<body>

    <header>
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h1>

        <a href="<?php echo $paginaInicio; ?>">Início</a>
        <a href="perfil.php">Perfil</a>
        <a href="logout.php">Sair</a>
    </header>

    <h2>Editar Perfil</h2>

    <?php if (!empty($erro)) { ?>
        <p><?php echo htmlspecialchars($erro); ?></p>
    <?php } ?>

    <?php if (!empty($sucesso)) { ?>
        <p><?php echo htmlspecialchars($sucesso); ?></p>
    <?php } ?>

    <form action="perfil.php" method="POST">

        <label for="nome">Nome:</label>
        <input
            type="text"
            name="nome"
            id="nome"
            placeholder="<?php echo htmlspecialchars($_SESSION['nome']); ?>"
        >

        <br><br>

        <label for="rm">RM:</label>
        <input
            type="text"
            id="rm"
            value="<?php echo htmlspecialchars($_SESSION['rm']); ?>"
            disabled
        >

        <br><br>

        <label for="senha">Nova senha:</label>
        <input
            type="password"
            name="senha"
            id="senha"
            placeholder="Digite uma nova senha"
        >

        <br><br>

        <button type="submit">Editar</button>

        <a href="<?php echo $paginaInicio; ?>">Voltar</a>

    </form>

</body>

</html>
