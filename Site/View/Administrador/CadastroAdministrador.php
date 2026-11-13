<?php
if (!isset($mensagem, $formularioDisponivel, $cadastrado, $model)) {
    header('Location: ../../Controller/CadastroAdministradorController.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de administrador</title>
</head>
<body>
    <?php if ($mensagem !== ''): ?>
        <p><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if ($formularioDisponivel): ?>
        <form action="" method="POST">
            <label for="nome_usuario">Nome de usuário:</label>
            <input type="text" id="nome_usuario" name="nome_usuario" maxlength="255" required>
            <br><br>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <br><br>

            <button type="submit">Cadastrar administrador</button>
        </form>
    <?php endif; ?>
</body>
</html>
