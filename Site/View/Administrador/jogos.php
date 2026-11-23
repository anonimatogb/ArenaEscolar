
<?php

session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    ($_SESSION['tipo'] ?? '') !== 'ADM'
) {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/JogoController.php';

$jogoController = new JogoController($pdo);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['acao'] ?? '') === 'placar') {
        $mensagem = $jogoController->registrarPlacar($_POST);
    } else {
        $mensagem = $jogoController->cadastrar($_POST);
    }
}

$salas = $jogoController->listarSalas();
$modalidades = $jogoController->listarModalidades();
$jogos = $jogoController->listar();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Agendar confrontos</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Administrador.css">
</head>

<body>

    <h1>Agendar confrontos</h1>

    <?php if ($mensagem !== ''): ?>
        <p>
            <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>


    <?php if (empty($modalidades)): ?>

        <p>Cadastre uma modalidade antes de agendar jogos.</p>

        <a href="../Modalidade/modalidade.php">
            Cadastrar modalidade
        </a>

    <?php elseif (empty($salas) || count($salas) < 2): ?>

        <p>É necessário cadastrar pelo menos duas salas.</p>

    <?php else: ?>

        <form action="jogos.php" method="POST">

            <h2>Novo confronto</h2>

            <label for="id_modalidade">Modalidade:</label>

            <select id="id_modalidade" name="id_modalidade" required>
                <option value="">Selecione</option>

                <?php foreach ($modalidades as $modalidade): ?>

                    <option value="<?= (int) $modalidade['id'] ?>">
                        <?= htmlspecialchars(
                            $modalidade['nome'] . ' - ' . $modalidade['genero'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <br><br>


            <label for="id_salaA">Primeira sala:</label>

            <select id="id_salaA" name="id_salaA" required>
                <option value="">Selecione</option>

                <?php foreach ($salas as $sala): ?>

                    <option value="<?= (int) $sala['id'] ?>">
                        <?= htmlspecialchars(
                            $sala['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <br><br>


            <label for="id_salaB">Segunda sala:</label>

            <select id="id_salaB" name="id_salaB" required>
                <option value="">Selecione</option>

                <?php foreach ($salas as $sala): ?>

                    <option value="<?= (int) $sala['id'] ?>">
                        <?= htmlspecialchars(
                            $sala['nome'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

            <br><br>


            <label for="dia">Dia:</label>

            <input
                type="date"
                id="dia"
                name="dia"
                required
            >

            <br><br>


            <label for="horario">Horário:</label>

            <input
                type="time"
                id="horario"
                name="horario"
                required
            >

            <br><br>

            <label for="quadra">Quadra:</label>
            <input type="text" id="quadra" name="quadra" maxlength="100" required>

            <br><br>


            <button type="submit">
                Cadastrar confronto
            </button>

        </form>

    <?php endif; ?>


    <h2>Confrontos agendados</h2>

    <?php if (empty($jogos)): ?>

        <p>Nenhum confronto agendado.</p>

    <?php else: ?>

        <ul>

            <?php foreach ($jogos as $jogo): ?>

                <li>

                    <?= htmlspecialchars(date('d/m/Y', strtotime($jogo['dia'])), ENT_QUOTES, 'UTF-8') ?>

                    às

                    <?= htmlspecialchars($jogo['horario'] ? substr($jogo['horario'], 0, 5) : '--:--', ENT_QUOTES, 'UTF-8') ?>

                    —

                    <?= htmlspecialchars($jogo['sala_a'], ENT_QUOTES, 'UTF-8') ?>

                    contra

                    <?= htmlspecialchars($jogo['sala_b'], ENT_QUOTES, 'UTF-8') ?>

                    (

                    <?= htmlspecialchars($jogo['modalidade'], ENT_QUOTES, 'UTF-8') ?>

                    -

                    <?= htmlspecialchars($jogo['genero'], ENT_QUOTES, 'UTF-8') ?>

                    )
                    — <?= htmlspecialchars($jogo['quadra'], ENT_QUOTES, 'UTF-8') ?>
                    — <?= $jogo['status'] === 'finalizado' ? 'Finalizado' : 'Agendado' ?>
                    <?php if ($jogo['status'] === 'finalizado'): ?>
                        — Placar <?= (int) $jogo['gols_pts_a'] ?> x <?= (int) $jogo['gols_pts_b'] ?>
                    <?php endif; ?>
                    <form action="jogos.php" method="POST">
                        <input type="hidden" name="acao" value="placar">
                        <input type="hidden" name="id_jogo" value="<?= (int) $jogo['id'] ?>">
                        <label>
                            Placar <?= htmlspecialchars($jogo['sala_a'], ENT_QUOTES, 'UTF-8') ?>:
                            <input type="number" name="gols_pts_a" min="0" required
                                value="<?= $jogo['status'] === 'finalizado' ? (int) $jogo['gols_pts_a'] : '' ?>">
                        </label>
                        <label>
                            Placar <?= htmlspecialchars($jogo['sala_b'], ENT_QUOTES, 'UTF-8') ?>:
                            <input type="number" name="gols_pts_b" min="0" required
                                value="<?= $jogo['status'] === 'finalizado' ? (int) $jogo['gols_pts_b'] : '' ?>">
                        </label>
                        <button type="submit">Registrar placar</button>
                    </form>

                </li>

            <?php endforeach; ?>

        </ul>

    <?php endif; ?>


    <p>
        <a href="inicio.php">Voltar ao painel do administrador</a>
    </p>

</body>
</html>
