
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['tipo'] != 1) {
    if ($_SESSION['tipo'] == 0) {
        header('Location: inicio.php');
    } else {
        header('Location: inicio_admin.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Representante | Arena Escolar</title>
</head>
<body>

    <nav>
        <ul>
            <h2>Bem-vindo, <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h2>

            <li><a href="inicio_sala.php">Início</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>

    <main>

        <section>
            <h2>Gerenciamento da sala</h2>
            <p>Aqui você poderá cadastrar e gerenciar os alunos da sua sala.</p>
        </section>

        <section>
            <h2>Inscrições nas modalidades</h2>
            <p>Aqui você poderá inscrever os alunos da sua sala nas modalidades esportivas.</p>
        </section>

        <section>
            <h2>Jogos da minha sala</h2>
            <p>Aqui aparecerão os jogos e resultados da sua sala.</p>
        </section>

        <section>
            <h2>Minha participação</h2>
            <p>Você também poderá participar das competições como jogador e acompanhar seus próprios resultados.</p>
        </section>

    </main>

</body>
</html>
