
<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    !isset($_SESSION['tipo']) ||
    $_SESSION['tipo'] !== 'representante'
) {
    header('Location: ../Usuario/Login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">    

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Representante</title>
</head>

<body>
<nav>
    <ul>
        <h2>Bem-vindo, <?= htmlspecialchars($_SESSION['nome']) ?>!</h2>
        <li><a href="inicio.php">Início</a></li>
        <li><a href="aluno.php">Alunos</a></li>
        <li><a href="../time/CadastroTime.php">Times</a></li>
        <li><a href="editarrepresentante.php">Editar perfil</a></li>
        <li><a href="../../../index.php">Voltar para a página inicial</a></li>
        <li><a href="../Usuario/sair.php">Sair</a></li>
    </ul>
</nav>

    <h2>Painel do Representante</h2>

    <p>Gerenciamento da sua sala.</p>

    <p><a href="aluno.php">Cadastrar e consultar alunos da minha sala</a></p>

</body>

</html>
