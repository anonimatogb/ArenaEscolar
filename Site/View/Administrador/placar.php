<?php

session_start();

$requisicaoAjax = $_SERVER['REQUEST_METHOD'] === 'POST';
$responderJson = static function (array $dados, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    exit();
};

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    if ($requisicaoAjax) {
        $responderJson(['success' => false, 'message' => 'Acesso não autorizado.'], 403);
    }

    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/JogoController.php';

$jogoController = new JogoController($pdo);

if (!isset($_SESSION['csrf_placar'])) {
    $_SESSION['csrf_placar'] = bin2hex(random_bytes(32));
}

if ($requisicaoAjax) {
    $corpo = json_decode(file_get_contents('php://input'), true);
    if (!is_array($corpo)) {
        $responderJson(['success' => false, 'message' => 'Requisição inválida.'], 400);
    }

    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (
        !is_string($token) ||
        !is_string($_SESSION['csrf_placar']) ||
        !hash_equals($_SESSION['csrf_placar'], $token)
    ) {
        $responderJson(['success' => false, 'message' => 'Sessão expirada. Atualize a página.'], 403);
    }

    try {

        // Verifica se a ação é finalizar o jogo
        if (($corpo['acao'] ?? '') === 'finalizar') {

            $idJogoFinalizar = filter_var(
                $corpo['id_jogo'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($idJogoFinalizar === false || $idJogoFinalizar < 1) {
                throw new DomainException('Identificador de jogo inválido.');
            }

            $resultado = $jogoController->finalizar($idJogoFinalizar);

            $responderJson([
                'success' => true,
                'message' => 'Jogo finalizado com sucesso!',
                'resultado' => $resultado
            ]);
        }

        // Caso contrário, atualiza o placar normalmente
        $placar = $jogoController->alterarPlacarAoVivo($corpo);

        $responderJson([
            'success' => true,
            'placar' => $placar
        ]);
    } catch (DomainException $e) {

        $status = $e->getMessage() === 'Jogo não encontrado.' ? 404 : 422;

        $responderJson([
            'success' => false,
            'message' => $e->getMessage()
        ], $status);
    } catch (PDOException $e) {

        error_log('Erro ao processar jogo: ' . $e->getMessage());

        $responderJson([
            'success' => false,
            'message' => 'Não foi possível processar o jogo.'
        ], 500);
    }
}

$idJogo = filter_var($_GET['id_jogo'] ?? null, FILTER_VALIDATE_INT);
$erro = '';
$jogo = null;
$statusHttp = 200;

if ($idJogo === false || $idJogo < 1) {
    $erro = 'Informe um identificador de jogo válido.';
    $statusHttp = 400;
} else {
    try {
        $jogo = $jogoController->buscarPorId($idJogo);
        if ($jogo === null) {
            $erro = 'A partida solicitada não foi encontrada.';
            $statusHttp = 404;
        }
    } catch (PDOException $e) {
        error_log('Erro ao carregar partida para o placar ao vivo: ' . $e->getMessage());
        $erro = 'Não foi possível carregar os dados da partida.';
        $statusHttp = 500;
    }
}

http_response_code($statusHttp);
$escape = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
$placarA = $jogo === null ? 0 : (int) ($jogo['gols_pts_a'] ?? 0);
$placarB = $jogo === null ? 0 : (int) ($jogo['gols_pts_b'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $escape($_SESSION['csrf_placar']) ?>">
    <title>Placar ao vivo | Arena Escolar</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/LiveScore.css">
</head>

<body class="live-score-page">
    <header class="live-score-header">
        <a href="inicio.php#jogos">Voltar aos jogos</a>
        <p>INTERCLASS</p>
        <h1>CONTROLE DE PLACAR</h1>
    </header>

    <main class="live-score-main">
        <?php if ($erro !== ''): ?>
            <p class="live-score-message error" role="alert"><?= $escape($erro) ?></p>
        <?php elseif ($jogo !== null): ?>
            <section class="live-score-match" aria-label="Placar da partida">
                <p class="live-score-details">
                    <?= $escape($jogo['modalidade']) ?> (<?= $escape($jogo['genero']) ?>)
                    &middot; <?= $escape(date('d/m/Y', strtotime($jogo['dia']))) ?>
                    &middot; <?= $escape($jogo['quadra']) ?>
                </p>
                <div
                    class="live-score-teams"
                    id="scoreboard"
                    data-id-jogo="<?= (int) $jogo['id'] ?>">
                    <article class="live-score-team">
                        <h2><?= $escape($jogo['sala_a']) ?></h2>
                        <output class="live-score-value" id="score-a" aria-live="polite">
                            <?= $placarA ?>
                        </output>
                        <div class="live-score-controls" aria-label="Controles de <?= $escape($jogo['sala_a']) ?>">
                            <button
                                type="button"
                                data-lado="a"
                                data-operacao="decrementar"
                                aria-label="Diminuir placar de <?= $escape($jogo['sala_a']) ?>"
                                <?= $placarA === 0 ? 'disabled' : '' ?>>-</button>
                            <button
                                type="button"
                                data-lado="a"
                                data-operacao="incrementar"
                                aria-label="Aumentar placar de <?= $escape($jogo['sala_a']) ?>">+</button>
                        </div>
                    </article>
                    <span class="live-score-separator" aria-hidden="true">×</span>
                    <article class="live-score-team">
                        <h2><?= $escape($jogo['sala_b']) ?></h2>
                        <output class="live-score-value" id="score-b" aria-live="polite">
                            <?= $placarB ?>
                        </output>
                        <div class="live-score-controls" aria-label="Controles de <?= $escape($jogo['sala_b']) ?>">
                            <button
                                type="button"
                                data-lado="b"
                                data-operacao="decrementar"
                                aria-label="Diminuir placar de <?= $escape($jogo['sala_b']) ?>"
                                <?= $placarB === 0 ? 'disabled' : '' ?>>-</button>
                            <button
                                type="button"
                                data-lado="b"
                                data-operacao="incrementar"
                                aria-label="Aumentar placar de <?= $escape($jogo['sala_b']) ?>">+</button>
                        </div>
                    </article>
                </div>
                <p class="live-score-message" id="score-message" role="status" aria-live="polite"></p>
                <a href="finalizar.php?id_jogo=<?= $jogo['id'] ?>" class="button" role="button">Finalizar Jogo</a>
            </section>
        <?php endif; ?>
    </main>

    <?php if ($jogo !== null): ?>
        <script>
            const scoreboard = document.getElementById('scoreboard');
            const controls = Array.from(scoreboard.querySelectorAll('button[data-lado]'));
            const message = document.getElementById('score-message');
            let requestPending = false;
            

            function updateScore(lado, value) {
                document.getElementById(`score-${lado}`).textContent = String(value);
                const minusButton = scoreboard.querySelector(
                    `button[data-lado="${lado}"][data-operacao="decrementar"]`
                );
                minusButton.disabled = value === 0;
            }

            function setControlsDisabled(disabled) {
                controls.forEach((button) => {
                    if (disabled) {
                        button.disabled = true;
                        return;
                    }

                    const score = Number(
                        document.getElementById(`score-${button.dataset.lado}`).textContent
                    );
                    button.disabled = button.dataset.operacao === 'decrementar' && score === 0;
                });
            }

            controls.forEach((button) => {
                button.addEventListener('click', async () => {
                    if (requestPending) {
                        return;
                    }

                    requestPending = true;
                    setControlsDisabled(true);
                    message.textContent = '';
                    message.classList.remove('error');

                    try {
                        const response = await fetch('placar.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                id_jogo: Number(scoreboard.dataset.idJogo),
                                lado: button.dataset.lado,
                                operacao: button.dataset.operacao
                            })
                        });
                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(result.message || 'Não foi possível atualizar o placar.');
                        }

                        updateScore('a', result.placar.gols_pts_a);
                        updateScore('b', result.placar.gols_pts_b);
                        message.textContent = 'Placar atualizado.';
                    } catch (error) {
                        message.textContent = error.message || 'Falha de comunicação ao atualizar o placar.';
                        message.classList.add('error');
                    } finally {
                        requestPending = false;
                        setControlsDisabled(false);
                    }
                });
            });
            const finalizarButton = document.querySelector('button[data-operacao="finalizar"]');

            finalizarButton.addEventListener('click', async () => {

                if (requestPending) {
                    return;
                }

                const confirmar = confirm(
                    'Tem certeza que deseja finalizar este jogo? Os pontos serão adicionados à classificação das salas.'
                );

                if (!confirmar) {
                    return;
                }

                requestPending = true;
                setControlsDisabled(true);
                finalizarButton.disabled = true;

                message.textContent = 'Finalizando jogo...';
                message.classList.remove('error');

                try {

                    const response = await fetch('placar.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            acao: 'finalizar',
                            id_jogo: Number(scoreboard.dataset.idJogo)
                        })
                    });

                    const result = await response.json();

                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Não foi possível finalizar o jogo.');
                    }

                    message.textContent = result.message;

                    finalizarButton.textContent = 'Jogo Finalizado';

                    // Impede novas alterações no placar
                    controls.forEach((button) => {
                        button.disabled = true;
                    });

                } catch (error) {

                    message.textContent = error.message || 'Falha ao finalizar o jogo.';
                    message.classList.add('error');

                    finalizarButton.disabled = false;
                    setControlsDisabled(false);

                } finally {
                    requestPending = false;
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>