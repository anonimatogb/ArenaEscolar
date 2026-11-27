<?php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/SalaController.php';

$salaController = new SalaController($pdo);

$integrantes = $salaController->listarIntegrantesPorSala();
$salas = [];

foreach ($integrantes as $integrante) {

    $idSala = $integrante['id_sala'];

    if (!isset($salas[$idSala])) {
        $salas[$idSala] = [
            'nome' => $integrante['nome_sala'],
            'alunos' => []
        ];
    }

    if ($integrante['id_usuario'] !== null) {
        $salas[$idSala]['alunos'][] = $integrante;
    }
}


?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Usuários</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Dashboard.css?v=20261127">
</head>

<body>
    <header class="dashboard-header">
        <div>
            <p class="dashboard-eyebrow">INTERCLASS</p>
            <h1>Painel do professor</h1>
            <p>Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? '') ?>.</p>
        </div>
        <nav class="dashboard-nav" aria-label="Navegação do painel">
            <a href="../../../index.php">Ver site</a>
            <a href="inicio.php">Voltar</a>
            <a href="../Usuario/sair.php">Sair</a>
        </nav>
    </header>


    <section class="dashboard-card" id="salas">

        <h2>Cadastro De Usuários</h2>

        <?php foreach ($salas as $idSala => $sala): ?>

            <h2><?= htmlspecialchars($sala['nome']) ?></h2>

            <?php if (count($sala['alunos']) > 0): ?>

                <div class="dashboard-table-wrap">

                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>RM</th>
                                <th>Gênero</th>
                                <th>Tipo</th>
                                <th>Ação</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($sala['alunos'] as $aluno): ?>

                                <tr>
                                    <td>
                                        <?= htmlspecialchars($aluno['nome_aluno']) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($aluno['rm'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($aluno['genero'] ?? '') ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($aluno['tipo'] ?? '') ?>
                                    </td>
                                    
                                    <?php
                                  if($aluno['tipo'] === 'admin' || $aluno['tipo'] === 'representante') {
                                  echo '<td><a href="deletar.php?id_aluno=' . (int) $aluno['id_usuario'] . ' " onclick="return confirm(\'Deseja realmente deletar este aluno?\')">Deletar</a></td>';
                                  }
                                        ?>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>

            <?php else: ?>

                <p>Nenhum aluno cadastrado nesta sala.</p>

            <?php endif; ?>

        <?php endforeach; ?>

</body>

</html>