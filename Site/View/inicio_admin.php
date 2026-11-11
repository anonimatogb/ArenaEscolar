
<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['tipo'] != 2) {
    if ($_SESSION['tipo'] == 0) {
        header('Location: inicio.php');
    } else {
        header('Location: inicio_sala.php');
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo | Arena Escolar</title>
</head>
<body>

    <nav>
        <ul>
            <h2>Bem-vindo, Professor <?php echo htmlspecialchars($_SESSION['nome']); ?>!</h2>

            <li><a href="inicio_admin.php">Início</a></li>
            <li><a href="perfil.php">Perfil</a></li>
            <li><a href="logout.php">Sair</a></li>
        </ul>
    </nav>

    <main>

        <section>
            <h2>Gerenciamento de salas</h2>
            <p>Cadastre e gerencie as salas, alunos e representantes.</p>
        </section>

        <section>
            <h2>Modalidades esportivas</h2>
            <p>Cadastre e gerencie as modalidades das competições.</p>
        </section>

        <section>
            <h2>Competições</h2>
            <p>Crie competições nos formatos mata-mata, grupos ou todos contra todos.</p>
        </section>

        <section>
            <h2>Sorteios e confrontos</h2>
            <p>Realize sorteios automáticos ou escolha manualmente os confrontos.</p>
        </section>

        <section>
            <h2>Gerenciamento de jogos</h2>
            <p>Organize partidas, datas, horários e participantes.</p>
        </section>

        <section>
            <h2>Placares e classificações</h2>
            <p>Registre resultados, vencedores e classificações.</p>
        </section>

        <section>
            <h2>Calendário</h2>
            <p>Consulte os jogos anteriores e futuros.</p>
        </section>

    </main>

</body>
</html>
