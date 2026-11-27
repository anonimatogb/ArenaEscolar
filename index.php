<?php
session_start();

require_once __DIR__ . '/DB/database.php';
require_once __DIR__ . '/Site/Controller/JogoController.php';

$jogoController = new JogoController($pdo);
$hoje = date('Y-m-d');
$data = $_GET['data'] ?? $hoje;
$erroData = '';

if (!is_string($data)) {
    $data = '';
}

$dataValida = DateTime::createFromFormat('!Y-m-d', $data);
if (!$dataValida || $dataValida->format('Y-m-d') !== $data) {
    $data = $hoje;
    $erroData = 'Data inválida; exibindo os jogos de hoje.';
}

$jogosDoDia = $jogoController->listar($data);
$idUsuario = isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : null;
$meusJogos = $idUsuario ? $jogoController->listarMeusJogos($idUsuario) : [];
$idsJogos = array_merge(
    array_column($jogosDoDia, 'id'),
    array_column($meusJogos, 'id')
);
$escalacoes = $idUsuario ? $jogoController->listarEscalacoes($idsJogos) : [];
$ranking = $jogoController->ranking();
$posicao = 0;
$pontosAnteriores = null;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <title>Arena Escolar</title>
</head>
<style>
    .qrcode-container {
        position: relative;
        display: inline-block;
        width: 100%; /* Garante que ocupe a largura da lista */
    }

    /* Estilo igual ao dos outros links do menu */
    #btnQrCode {
        width: 100%;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, 0.15); /* Ajuste conforme a borda padrão do seu tema */
        color: #ffffff;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        transition: 0.3s;
        font-family: inherit;
        display: block;
        margin-top: 10px;
    }

    #btnQrCode:hover {
        border-color: #f39c12; /* Cor de hover padrão do seu site (exemplo laranja) ou ajuste para a sua cor */
        background-color: rgba(255, 255, 255, 0.05);
    }

    .qr-popup {
        display: none;
        position: absolute;
        top: 110%;
        right: 0;
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        z-index: 1000;
        text-align: center;
    }

    .qr-popup img {
        width: 180px;
        height: 180px;
        display: block;
        margin-bottom: 10px;
    }

    /* Estilo para o link de download dentro do popup */
    .qr-popup a {
        font-size: 14px;
        text-decoration: none;
    }

    .qr-popup a:hover {
        text-decoration: underline;
    }

    .qr-popup.ativo {
        display: block;
    }
</style>
<body>

    <main class="pagina">

        <!-- LATERAL AZUL -->
        <aside class="painel-acesso">

            <img src="CSS/img/Logo.png" alt="imagem" class="img">

            <?php if (isset($_SESSION['nome'])): ?>
                <p class="boas-vindas">
                    Bem-vindo, <?= htmlspecialchars($_SESSION['nome'], ENT_QUOTES, 'UTF-8') ?>.
                </p>
            <?php endif; ?>

           <div class="acesso">
    <h2>ACESSO</h2>

    <ul>
        <?php if (empty($_SESSION['id_usuario'])): ?>
            <li><a style="font-size: 20px;" href="Site/View/Usuario/Login.php">Entrar</a></li>
            <li><a href="Site/View/Administrador/Login.php">Entrar como professor</a></li>

        <?php elseif (($_SESSION['tipo'] ?? '') === 'ADM'): ?>
            <li><a style="font-size: 20px;" href="Site/View/Administrador/inicio.php">Painel do professor</a></li>
            <li><a style="font-size: 20px;" href="Site/View/Usuario/sair.php">Sair</a></li>

        <?php elseif (($_SESSION['tipo'] ?? '') === 'representante'): ?>
            <li><a style="font-size: 20px;" href="Site/View/Representante/inicio.php">Painel do representante</a></li>
            <li><a style="font-size: 20px;" href="Site/View/Usuario/sair.php">Sair</a></li>

        <?php elseif (($_SESSION['tipo'] ?? '') === 'aluno'): ?>
            <li><a style="font-size: 20px;" href="Site/View/Usuario/sair.php">Sair</a></li>
        <?php endif; ?>
        
        <!-- Botão do QR Code ajustado dentro de um li para seguir a estrutura -->
        <li>
            <div class="qrcode-container">
                <button id="btnQrCode">Baixar Aplicativo</button>

                <div id="qrCodePopup" class="qr-popup">
                    <img src="CSS/img/qrcode.jpeg" alt="QR Code">
                    <a href="https://github.com/anonimatogb/ArenaEscolar/releases/download/v1.0.0/interclass.apk">Clique Aqui para Baixar</a>
                </div>
            </div>
        </li>
    </ul>
</div>

            <div class="mensagem">
                <h3>ESPÍRITO DE EQUIPE</h3>
                <p>Sua turma, seus jogos, suas conquistas.</p>
                <p>Na quadra, a gente compete. Fora dela, se conecta.</p>
            </div>

            <footer>
                Feito para quem vive o esporte na escola.
            </footer>

        </aside>


        <!-- ÁREA BRANCA -->
        <section class="conteudo">

            <h2>JOGOS DO DIA</h2>
            <form method="GET" action="index.php">
                <label for="data">Filtrar por data:</label>
                <input type="date" id="data" name="data" value="<?= htmlspecialchars($data, ENT_QUOTES, 'UTF-8') ?>" required>
                <button type="submit">Buscar</button>
            </form>

            <?php if ($erroData !== ''): ?>
                <p><?= htmlspecialchars($erroData, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($jogosDoDia === []): ?>
                <p>Nenhum jogo cadastrado para esta data.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Horário / Quadra</th>
                            <th>Modalidade</th>
                            <th>Confronto / Placar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jogosDoDia as $jogo): ?>
                            <tr>
                                <td style="font-size: medium;"><?= htmlspecialchars(substr((string) $jogo['horario'], 0, 5), ENT_QUOTES, 'UTF-8') ?>
                                    / <?= htmlspecialchars($jogo['quadra'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td style="font-size: medium;"><?= htmlspecialchars($jogo['modalidade'] . ' - ' . $jogo['genero'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="confronto-cell" style="font-size: medium;">
                                    <?php
                                    $placarDisponivel = $jogo['gols_pts_a'] !== null && $jogo['gols_pts_b'] !== null;
                                    $statusJogo = $jogo['status'] === 'finalizado'
                                        ? 'Finalizado'
                                        : ($placarDisponivel ? 'Ao vivo' : 'Agendado');
                                    ?>
                                    <div class="placar-confronto">
                                        <span class="time-confronto"><?= htmlspecialchars($jogo['sala_a'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="pontos-confronto"><?= $placarDisponivel ? (int) $jogo['gols_pts_a'] : '—' ?></span>
                                        <span class="separador-placar" aria-label="contra">×</span>
                                        <span class="pontos-confronto"><?= $placarDisponivel ? (int) $jogo['gols_pts_b'] : '—' ?></span>
                                        <span class="time-confronto"><?= htmlspecialchars($jogo['sala_b'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <span class="status-jogo <?= $jogo['status'] === 'finalizado' ? 'status-finalizado' : ($placarDisponivel ? 'status-ao-vivo' : 'status-agendado') ?>">
                                        <?= $statusJogo ?>
                                    </span>
                                    <?php if ($idUsuario): ?>
                                        <?php
                                        $escalacaoA = $escalacoes[(int) $jogo['id']][(int) $jogo['id_timeA']] ?? [];
                                        $escalacaoB = $escalacoes[(int) $jogo['id']][(int) $jogo['id_timeB']] ?? [];
                                        ?>
                                        <details class="escalacoes-jogo">
                                            <summary>Ver escalações</summary>
                                            <p><strong><?= htmlspecialchars($jogo['sala_a'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                                                <?= $escalacaoA === [] ? 'Sem jogadores escalados.' : htmlspecialchars(implode(', ', $escalacaoA), ENT_QUOTES, 'UTF-8') ?></p>
                                            <p><strong><?= htmlspecialchars($jogo['sala_b'], ENT_QUOTES, 'UTF-8') ?>:</strong>
                                                <?= $escalacaoB === [] ? 'Sem jogadores escalados.' : htmlspecialchars(implode(', ', $escalacaoB), ENT_QUOTES, 'UTF-8') ?></p>
                                        </details>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <?php if ($idUsuario): ?>
                <h2>Meus jogos</h2>
                <?php if ($meusJogos === []): ?>
                    <p>Você ainda não está escalado em nenhuma partida.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($meusJogos as $jogo): ?>
                            <li>
                                <?= htmlspecialchars(date('d/m/Y', strtotime($jogo['dia'])), ENT_QUOTES, 'UTF-8') ?>
                                <?= htmlspecialchars(substr((string) $jogo['horario'], 0, 5), ENT_QUOTES, 'UTF-8') ?> —
                                <?= htmlspecialchars($jogo['modalidade'] . ' (' . $jogo['genero'] . '): ' . $jogo['sala_a'] . ' x ' . $jogo['sala_b'], ENT_QUOTES, 'UTF-8') ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            <?php endif; ?>

            <h2>RANKING DAS SALAS</h2>
            <?php if ($ranking === []): ?>
                <p>Nenhuma sala cadastrada.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Posição</th>
                            <th>Sala</th>
                            <th>Pontos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $indice => $sala): ?>
                            <?php
                            $pontos = (int) $sala['total_pontos'];
                            if ($pontosAnteriores === null || $pontos !== $pontosAnteriores) {
                                $posicao = $indice + 1;
                            }
                            $pontosAnteriores = $pontos;
                            ?>
                            <tr>
                                <td><?= $posicao ?>º</td>
                                <td><?= htmlspecialchars($sala['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= $pontos ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </section>

    </main>
    <script>
        const btnQrCode = document.getElementById('btnQrCode');
        const qrCodePopup = document.getElementById('qrCodePopup');

        btnQrCode.addEventListener('click', function(event) {
            event.stopPropagation();

            qrCodePopup.classList.toggle('ativo');
        });

        document.addEventListener('click', function(event) {
            if (!qrCodePopup.contains(event.target) && !btnQrCode.contains(event.target)) {
                qrCodePopup.classList.remove('ativo');
            }
        });
    </script>
</body>

</html>