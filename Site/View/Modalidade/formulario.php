<?php
session_start();
if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: ../Administrador/Login.php');
    exit();
}


$formAction = $formAction ?? ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
$mensagem = $mensagem ?? '';
$erroListagem = $erroListagem ?? '';
$modalidades = $modalidades ?? [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Modalidade</title>

</head>  
<body>

<div class="form-container">
    <h2>Cadastrar Modalidade</h2>

    <?php if (!empty($mensagem)): ?>
        <div class="msg"><?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (!empty($erroListagem)): ?>
        <div class="msg"><?= htmlspecialchars($erroListagem, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" method="POST">
        <div class="form-group">
            <label for="nome">Nome da Modalidade:</label>
            <input type="text" id="nome" name="nome" required placeholder="Ex: Futebol, Basquete">
        </div>

        <div class="form-group">
            <label for="quantidade">Máximo de Participantes:</label>
            <input type="number" id="quantidade" name="quantidade" min="1" required placeholder="Ex: 11">
        </div>

        <div class="form-group">
            <label for="genero">Gênero:</label>
            <select id="genero" name="genero" required>
                <option value="">Selecione...</option>
                <option value="masculino">Masculino</option>
                <option value="feminino">Feminino</option>
                <option value="misto">Misto</option>
            </select>
        </div>

        <button type="submit">Salvar Modalidade</button>
        <a href="../Administrador/inicio.php">Voltar</a>
    </form>

<?php
   require __DIR__ . '/listar.php';
?>

</div>
</body>
</html>