<?php
session_start();

if (!isset($_SESSION['id_usuario']) || ($_SESSION['tipo'] ?? '') !== 'ADM') {
    header('Location: Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';
require_once __DIR__ . '/../../Controller/SalaController.php';
require_once __DIR__ . '/../../Controller/JogoController.php';
require_once __DIR__ . '/../../Model/ModalidadeM.php';

$usuarioController = new UsuarioController($pdo);
$salaController = new SalaController($pdo);
$jogoController = new JogoController($pdo);
$mensagem = '';
$tipoMensagem = 'success';
$ancora = 'inicio';
$postValue = static function (string $key): string {
    return is_string($_POST[$key] ?? null) ? $_POST[$key] : '';
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = is_string($_POST['acao'] ?? null) ? $_POST['acao'] : '';
    $nome = is_string($_POST['nome'] ?? null) ? trim($_POST['nome']) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

    switch ($acao) {
        case 'cadastrar_representante':
            $rm = is_string($_POST['rm'] ?? null) ? trim($_POST['rm']) : '';
            $idSalaEnviado = $_POST['id_sala'] ?? null;
            $idSala = is_string($idSalaEnviado)
                ? filter_var($idSalaEnviado, FILTER_VALIDATE_INT)
                : false;
            $genero = is_string($_POST['genero'] ?? null) ? $_POST['genero'] : '';
            $ancora = 'representantes';

            if (
                $nome === '' ||
                $rm === '' ||
                $idSala === false ||
                $idSala < 1 ||
                !in_array($genero, ['masculino', 'feminino'], true) ||
                strlen($senha) < 8
            ) {
                $mensagem = 'Preencha os dados corretamente. A senha deve ter pelo menos 8 caracteres.';
                $tipoMensagem = 'error';
            } elseif ($usuarioController->cadastrarRepresentante(
                $nome,
                $rm,
                $idSala,
                $genero,
                $senha
            )) {
                $mensagem = 'Representante cadastrado com sucesso.';
            } else {
                $mensagem = 'Não foi possível cadastrar. Verifique o RM e o limite de representantes da sala.';
                $tipoMensagem = 'error';
            }
            break;

        case 'cadastrar_administrador':
            $ancora = 'administradores';
            if ($nome === '' || $senha === '') {
                $mensagem = 'Preencha o nome de usuário e a senha.';
                $tipoMensagem = 'error';
            } elseif ($usuarioController->cadastrarAdministrador($nome, $senha)) {
                $mensagem = 'Administrador cadastrado com sucesso.';
            } else {
                $mensagem = 'Não foi possível cadastrar o administrador.';
                $tipoMensagem = 'error';
            }
            break;

        case 'cadastrar_modalidade':
            $ancora = 'modalidades';
            $quantidadeEnviada = $_POST['quantidade'] ?? null;
            $quantidade = is_string($quantidadeEnviada)
                ? filter_var($quantidadeEnviada, FILTER_VALIDATE_INT)
                : false;
            $genero = is_string($_POST['genero'] ?? null) ? $_POST['genero'] : '';

            if (
                $nome === '' ||
                $quantidade === false ||
                $quantidade < 1 ||
                !in_array($genero, ['Masculino', 'Feminino', 'Misto'], true)
            ) {
                $mensagem = 'Preencha os dados da modalidade corretamente.';
                $tipoMensagem = 'error';
                break;
            }

            try {
                $modalidade = new Modalidade($pdo);
                $modalidade->nome = $nome;
                $modalidade->quantidade = $quantidade;
                $modalidade->genero = $genero;

                if ($modalidade->existeComMesmasCaracteristicas()) {
                    $mensagem = 'Já existe uma modalidade com esse nome e gênero.';
                    $tipoMensagem = 'error';
                } elseif ($modalidade->salvar()) {
                    $mensagem = 'Modalidade cadastrada com sucesso.';
                } else {
                    $mensagem = 'Não foi possível cadastrar a modalidade.';
                    $tipoMensagem = 'error';
                }
            } catch (PDOException $e) {
                error_log('Erro ao cadastrar modalidade pelo painel ADM: ' . $e->getMessage());
                $mensagem = 'Não foi possível cadastrar a modalidade no banco de dados.';
                $tipoMensagem = 'error';
            }
            break;

        case 'agendar_jogo':
            $ancora = 'jogos';
            $mensagem = $jogoController->cadastrar([
                'id_modalidade' => $postValue('id_modalidade'),
                'id_salaA' => $postValue('id_salaA'),
                'id_salaB' => $postValue('id_salaB'),
                'dia' => $postValue('dia'),
                'horario' => $postValue('horario'),
                'quadra' => $postValue('quadra'),
            ]);
            $tipoMensagem = strpos($mensagem, 'sucesso') !== false ? 'success' : 'error';
            break;

        case 'registrar_placar':
            $ancora = 'jogos';
            $mensagem = $jogoController->registrarPlacar([
                'id_jogo' => $postValue('id_jogo'),
                'gols_pts_a' => $postValue('gols_pts_a'),
                'gols_pts_b' => $postValue('gols_pts_b'),
            ]);
            $tipoMensagem = strpos($mensagem, 'sucesso') !== false ? 'success' : 'error';
            break;

        default:
            $mensagem = 'Ação inválida.';
            $tipoMensagem = 'error';
    }

    $_SESSION['painel_adm_flash'] = [
        'mensagem' => $mensagem,
        'tipo' => $tipoMensagem,
    ];
    header('Location: inicio.php#' . $ancora);
    exit();
}

$flash = $_SESSION['painel_adm_flash'] ?? null;
unset($_SESSION['painel_adm_flash']);

$salas = $salaController->listarSalas();
$salasJogo = $jogoController->listarSalas();
$modalidades = $jogoController->listarModalidades();
$jogos = $jogoController->listar();
$escape = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do professor</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Dashboard.css">
</head>
<body class="dashboard-page">
    <header class="dashboard-header">
        <div>
            <p class="dashboard-eyebrow">Arena Escolar</p>
            <h1>Painel do professor</h1>
            <p>Bem-vindo, <?= $escape($_SESSION['nome'] ?? '') ?>.</p>
        </div>
        <nav class="dashboard-nav" aria-label="Navegação do painel">
            <a href="../../../index.php">Ver site</a>
            <a href="../Usuario/sair.php">Sair</a>
        </nav>
    </header>

    <main class="dashboard-main">
        <?php if (is_array($flash)): ?>
            <p class="dashboard-flash <?= $escape($flash['tipo']) ?>" role="status">
                <?= $escape($flash['mensagem']) ?>
            </p>
        <?php endif; ?>

        <div class="dashboard-grid">
            <section class="dashboard-card" id="representantes">
                <h2>Cadastrar representante</h2>
                <form action="inicio.php#representantes" method="POST">
                    <input type="hidden" name="acao" value="cadastrar_representante">
                    <label for="rep_nome">Nome</label>
                    <input id="rep_nome" name="nome" type="text" maxlength="150" required>
                    <label for="rep_rm">RM</label>
                    <input id="rep_rm" name="rm" type="text" maxlength="30" required>
                    <label for="rep_senha">Senha</label>
                    <input id="rep_senha" name="senha" type="password" minlength="8" required>
                    <label for="rep_sala">Sala</label>
                    <select id="rep_sala" name="id_sala" required>
                        <option value="">Selecione uma sala</option>
                        <?php foreach ($salas as $sala): ?>
                            <option value="<?= (int) $sala['id'] ?>"><?= $escape($sala['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="rep_genero">Gênero</label>
                    <select id="rep_genero" name="genero" required>
                        <option value="masculino">Masculino</option>
                        <option value="feminino">Feminino</option>
                    </select>
                    <button type="submit">Cadastrar representante</button>
                </form>
            </section>

            <section class="dashboard-card" id="administradores">
                <h2>Cadastrar professor</h2>
                <form action="inicio.php#administradores" method="POST">
                    <input type="hidden" name="acao" value="cadastrar_administrador">
                    <label for="adm_nome">Nome de usuário</label>
                    <input id="adm_nome" name="nome" type="text" maxlength="255" required>
                    <label for="adm_senha">Senha</label>
                    <input id="adm_senha" name="senha" type="password" required>
                    <button type="submit">Cadastrar professor</button>
                </form>
            </section>

            <section class="dashboard-card" id="modalidades">
                <h2>Cadastrar modalidade</h2>
                <form action="inicio.php#modalidades" method="POST">
                    <input type="hidden" name="acao" value="cadastrar_modalidade">
                    <label for="modalidade_nome">Nome da modalidade</label>
                    <input id="modalidade_nome" name="nome" type="text" maxlength="100" required>
                    <label for="modalidade_quantidade">Máximo de participantes</label>
                    <input id="modalidade_quantidade" name="quantidade" type="number" min="1" required>
                    <label for="modalidade_genero">Gênero</label>
                    <select id="modalidade_genero" name="genero" required>
                        <option value="">Selecione</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                        <option value="Misto">Misto</option>
                    </select>
                    <button type="submit">Salvar modalidade</button>
                </form>
                <h3>Modalidades cadastradas</h3>
                <?php if ($modalidades === []): ?>
                    <p>Nenhuma modalidade cadastrada.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($modalidades as $modalidade): ?>
                            <li>
                                <?= $escape($modalidade['nome']) ?>
                                (<?= $escape($modalidade['genero']) ?>) —
                                <?= (int) $modalidade['max_jogadores'] ?> participantes
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <section class="dashboard-card" id="salas">
                <h2>Salas cadastradas</h2>
                <?php if ($salas === []): ?>
                    <p>Nenhuma sala cadastrada.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($salas as $sala): ?>
                            <li><?= $escape($sala['nome']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <a class="dashboard-secondary-link" href="../Sala/CadastroSala.php">Abrir consulta de salas</a>
            </section>

            <section class="dashboard-card dashboard-card-wide" id="jogos">
                <h2>Agendar confronto</h2>
                <?php if ($modalidades === []): ?>
                    <p>Cadastre uma modalidade antes de agendar jogos.</p>
                <?php elseif (count($salasJogo) < 2): ?>
                    <p>É necessário cadastrar pelo menos duas salas.</p>
                <?php else: ?>
                    <form class="dashboard-form-grid" action="inicio.php#jogos" method="POST">
                        <input type="hidden" name="acao" value="agendar_jogo">
                        <div>
                            <label for="jogo_modalidade">Modalidade</label>
                            <select id="jogo_modalidade" name="id_modalidade" required>
                                <option value="">Selecione</option>
                                <?php foreach ($modalidades as $modalidade): ?>
                                    <option value="<?= (int) $modalidade['id'] ?>">
                                        <?= $escape($modalidade['nome'] . ' - ' . $modalidade['genero']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="jogo_sala_a">Primeira sala</label>
                            <select id="jogo_sala_a" name="id_salaA" required>
                                <option value="">Selecione</option>
                                <?php foreach ($salasJogo as $sala): ?>
                                    <option value="<?= (int) $sala['id'] ?>"><?= $escape($sala['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="jogo_sala_b">Segunda sala</label>
                            <select id="jogo_sala_b" name="id_salaB" required>
                                <option value="">Selecione</option>
                                <?php foreach ($salasJogo as $sala): ?>
                                    <option value="<?= (int) $sala['id'] ?>"><?= $escape($sala['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="jogo_dia">Dia</label>
                            <input id="jogo_dia" name="dia" type="date" required>
                        </div>
                        <div>
                            <label for="jogo_horario">Horário</label>
                            <input id="jogo_horario" name="horario" type="time" required>
                        </div>
                        <div>
                            <label for="jogo_quadra">Quadra</label>
                            <input id="jogo_quadra" name="quadra" type="text" maxlength="100" required>
                        </div>
                        <button type="submit">Agendar confronto</button>
                    </form>
                <?php endif; ?>

                <h2>Jogos e lançamento de placares</h2>
                <?php if ($jogos === []): ?>
                    <p>Nenhum jogo cadastrado.</p>
                <?php else: ?>
                    <?php foreach ($jogos as $jogo): ?>
                        <article class="dashboard-game">
                            <p>
                                <strong><?= $escape($jogo['modalidade']) ?> (<?= $escape($jogo['genero']) ?>)</strong>
                                — <?= $escape(date('d/m/Y', strtotime($jogo['dia']))) ?>
                                <?= $jogo['horario'] ? $escape(substr($jogo['horario'], 0, 5)) : '' ?>
                                — <?= $escape($jogo['quadra']) ?>
                                — <?= $escape($jogo['status']) ?>
                            </p>
                            <?php if ($jogo['status'] === 'finalizado'): ?>
                                <p>Placar atual: <?= (int) $jogo['gols_pts_a'] ?> x <?= (int) $jogo['gols_pts_b'] ?></p>
                            <?php endif; ?>
                            <form class="dashboard-score-form" action="inicio.php#jogos" method="POST">
                                <input type="hidden" name="acao" value="registrar_placar">
                                <input type="hidden" name="id_jogo" value="<?= (int) $jogo['id'] ?>">
                                <label>
                                    <?= $escape($jogo['sala_a']) ?>
                                    <input type="number" name="gols_pts_a" min="0"
                                        value="<?= $jogo['gols_pts_a'] === null ? '' : (int) $jogo['gols_pts_a'] ?>"
                                        required>
                                </label>
                                <span aria-hidden="true">x</span>
                                <label>
                                    <?= $escape($jogo['sala_b']) ?>
                                    <input type="number" name="gols_pts_b" min="0"
                                        value="<?= $jogo['gols_pts_b'] === null ? '' : (int) $jogo['gols_pts_b'] ?>"
                                        required>
                                </label>
                                <button type="submit">
                                    <?= $jogo['status'] === 'finalizado' ? 'Atualizar placar' : 'Salvar placar' ?>
                                </button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
