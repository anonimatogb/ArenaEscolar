<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    ($_SESSION['tipo'] ?? '') !== 'representante' ||
    !isset($_SESSION['id_sala'])
) {
    header('Location: ../Usuario/Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once __DIR__ . '/../../Controller/UsuarioController.php';
require_once __DIR__ . '/../../Controller/TimeController.php';

$usuarioController = new UsuarioController($pdo);
$timeController = new TimeController($pdo);
$idUsuario = (int) $_SESSION['id_usuario'];
$idSala = (int) $_SESSION['id_sala'];
$alunosSala = $usuarioController->listarAlunosPorSala($idSala);
$mensagem = '';
$tipoMensagem = 'success';
$ancora = 'inicio';
$queryRetorno = '';
$erroListagemTimes = '';

try {
    $linhasTimes = $timeController->listarTimesDaSala($idUsuario);
} catch (Exception $e) {
    $linhasTimes = [];
    $erroListagemTimes = 'Não foi possível carregar os times da sua sala.';
    error_log('Erro ao listar times do representante: ' . $e->getMessage());
}

try {
    $modalidadesParaCadastro = $timeController->listarModalidadesParaCadastroAluno($idUsuario);
} catch (Exception $e) {
    $modalidadesParaCadastro = [];
    $mensagem = $e->getMessage();
    $tipoMensagem = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = is_string($_POST['acao'] ?? null) ? $_POST['acao'] : '';
    $nome = is_string($_POST['nome'] ?? null) ? trim($_POST['nome']) : '';
    $rm = is_string($_POST['rm'] ?? null) ? trim($_POST['rm']) : '';
    $genero = is_string($_POST['genero'] ?? null) ? $_POST['genero'] : '';

    switch ($acao) {
        case 'cadastrar_aluno':
            $ancora = 'alunos';
            $modalidadesEnviadas = $_POST['modalidades'] ?? [];
            $modalidadesSelecionadas = [];

            if (is_array($modalidadesEnviadas)) {
                foreach ($modalidadesEnviadas as $idMod) {
                    if (is_string($idMod) || is_int($idMod)) {
                        $idValidado = filter_var((string) $idMod, FILTER_VALIDATE_INT);
                        if ($idValidado !== false && $idValidado > 0) {
                            $modalidadesSelecionadas[] = $idValidado;
                        }
                    }
                }
                $modalidadesSelecionadas = array_values(array_unique($modalidadesSelecionadas));
            }

            if (
                $nome === '' ||
                $rm === '' ||
                !in_array($genero, ['masculino', 'feminino'], true) ||
                $modalidadesSelecionadas === []
            ) {
                $mensagem = 'Preencha os dados do aluno e marque ao menos uma modalidade válida.';
                $tipoMensagem = 'error';
            } else {
                try {
                    // Se o seu TimeController tiver um método para várias modalidades, use-o aqui.
                    // Caso contrário, se cadastrarAlunoNaModalidade aceitar 1 modalidade por vez:
                    if (method_exists($timeController, 'cadastrarAlunoEmModalidades')) {
                        $timeController->cadastrarAlunoEmModalidades(
                            $idUsuario,
                            $nome,
                            $rm,
                            $genero,
                            $modalidadesSelecionadas
                        );
                    } else {
                        foreach ($modalidadesSelecionadas as $idMod) {
                            $timeController->cadastrarAlunoNaModalidade(
                                $idUsuario,
                                $nome,
                                $rm,
                                $genero,
                                $idMod
                            );
                        }
                    }
                    $mensagem = 'Aluno cadastrado e adicionado às modalidades selecionadas.';
                } catch (DomainException $e) {
                    $mensagem = $e->getMessage();
                    $tipoMensagem = 'error';
                } catch (PDOException $e) {
                    error_log('Erro ao cadastrar aluno na modalidade: ' . $e->getMessage());
                    $mensagem = 'Não foi possível cadastrar. Verifique se o RM já está cadastrado.';
                    $tipoMensagem = 'error';
                }
            }
            break;

        case 'atualizar_aluno':
            $ancora = 'editar-aluno';
            $idAlunoEnviado = $_POST['id_aluno'] ?? null;
            $idAluno = is_string($idAlunoEnviado)
                ? filter_var($idAlunoEnviado, FILTER_VALIDATE_INT)
                : false;
            $alunoDaSala = false;

            if ($idAluno !== false && $idAluno > 0) {
                $queryRetorno = '?editar_aluno=' . $idAluno;
                foreach ($alunosSala as $alunoSala) {
                    if (
                        (int) $alunoSala['id'] === $idAluno &&
                        ($alunoSala['tipo'] ?? '') === 'aluno'
                    ) {
                        $alunoDaSala = true;
                        break;
                    }
                }
            }

            if (
                !$alunoDaSala ||
                $nome === '' ||
                $rm === '' ||
                !in_array($genero, ['masculino', 'feminino'], true)
            ) {
                $mensagem = 'Selecione um aluno da sua sala e preencha os dados corretamente.';
                $tipoMensagem = 'error';
            } else {
                $modalidadesSelecionadas = $_POST['modalidades'] ?? [];
                if (!is_array($modalidadesSelecionadas)) {
                    $modalidadesSelecionadas = [];
                }

                try {
                    $timeController->atualizarAlunoEModalidades(
                        $idUsuario,
                        $idAluno,
                        $nome,
                        $rm,
                        $genero,
                        $modalidadesSelecionadas
                    );
                    $mensagem = 'Aluno e modalidades atualizados com sucesso.';
                } catch (DomainException $e) {
                    $mensagem = $e->getMessage();
                    $tipoMensagem = 'error';
                } catch (PDOException $e) {
                    error_log('Erro ao atualizar aluno e modalidades: ' . $e->getMessage());
                    $mensagem = 'Não foi possível atualizar. Verifique se o RM já está cadastrado.';
                    $tipoMensagem = 'error';
                }
            }
            break;

        case 'atualizar_representante':
            $ancora = 'perfil';
            $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

            if ($nome === '' || strlen($senha) < 4) {
                $mensagem = 'Informe seu nome e uma senha com pelo menos 4 caracteres.';
                $tipoMensagem = 'error';
            } elseif ($usuarioController->atualizarRepresentante($idUsuario, $nome, $senha)) {
                $_SESSION['nome'] = $nome;
                $mensagem = 'Perfil atualizado com sucesso.';
            } else {
                $mensagem = 'Não foi possível atualizar o perfil.';
                $tipoMensagem = 'error';
            }
            break;

        default:
            $mensagem = 'Ação inválida.';
            $tipoMensagem = 'error';
    }

    $_SESSION['painel_representante_flash'] = [
        'mensagem' => $mensagem,
        'tipo' => $tipoMensagem,
    ];
    header('Location: inicio.php' . $queryRetorno . '#' . $ancora);
    exit();
}

$flash = $_SESSION['painel_representante_flash'] ?? null;
unset($_SESSION['painel_representante_flash']);

$alunoParaEditar = null;
$erroEdicao = '';

if (isset($_GET['editar_aluno'])) {
    $idAlunoEnviado = $_GET['editar_aluno'];
    $idAlunoSolicitado = is_string($idAlunoEnviado)
        ? filter_var($idAlunoEnviado, FILTER_VALIDATE_INT)
        : false;

    if ($idAlunoSolicitado === false || $idAlunoSolicitado < 1) {
        $erroEdicao = 'Aluno inválido.';
    } else {
        foreach ($alunosSala as $aluno) {
            if (
                (int) $aluno['id'] === $idAlunoSolicitado &&
                ($aluno['tipo'] ?? '') === 'aluno'
            ) {
                $alunoParaEditar = $aluno;
                break;
            }
        }

        if ($alunoParaEditar === null) {
            $erroEdicao = 'Esse aluno não pertence à sua sala.';
        }
    }
}

$modalidadesDoAluno = [];
if ($alunoParaEditar !== null) {
    try {
        $modalidadesDoAluno = $timeController->listarModalidadesParaEditarAluno(
            $idUsuario,
            (int) $alunoParaEditar['id']
        );
    } catch (Exception $e) {
        $erroEdicao = 'Não foi possível carregar as modalidades deste aluno.';
        error_log('Erro ao carregar modalidades do aluno: ' . $e->getMessage());
    }
}

$timesPorModalidade = [];
foreach ($linhasTimes as $linhaTime) {
    $idModalidade = (int) $linhaTime['id_modalidade'];

    if (!isset($timesPorModalidade[$idModalidade])) {
        $timesPorModalidade[$idModalidade] = [
            'nome' => $linhaTime['modalidade'],
            'genero' => $linhaTime['genero'],
            'quantidade' => (int) $linhaTime['quantidade'],
            'alunos' => [],
        ];
    }

    if ($linhaTime['id_aluno'] !== null) {
        $timesPorModalidade[$idModalidade]['alunos'][] = [
            'nome' => $linhaTime['aluno'],
            'rm' => $linhaTime['rm'],
            'genero' => $linhaTime['genero_aluno'],
        ];
    }
}

$escape = static function ($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do representante</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Dashboard.css">
</head>
<body class="dashboard-page">
    <header class="dashboard-header">
        <div>
            <p class="dashboard-eyebrow">INTERCLASS </p>
            <h1>Painel do representante</h1>
            <p>Bem-vindo, <?= $escape($_SESSION['nome'] ?? '') ?>. Gerencie sua sala.</p>
        </div>
        <nav class="dashboard-nav" aria-label="Navegação do painel">
            <a href="editarrepresentante.php">Editar perfil</a>
            <a href="../../../index.php">Ver site</a>
            <a href="../Usuario/sair.php">Sair</a>
        </nav>
    </header>

    <main class="dashboard-main">
        <?php if (is_array($flash)): ?>
            <p class="dashboard-flash <?= $escape($flash['tipo']) ?>" role="status">
                <?= $escape($flash['mensagem']) ?>
            </p>
        <?php elseif ($mensagem !== ''): ?>
            <p class="dashboard-flash <?= $escape($tipoMensagem) ?>" role="status">
                <?= $escape($mensagem) ?>
            </p>
        <?php endif; ?>

        <div class="dashboard-grid">
            <section class="dashboard-card" id="alunos">
                <h2>Cadastrar aluno</h2>
                <?php if ($modalidadesParaCadastro !== []): ?>
                    <form action="inicio.php#alunos" method="POST">
                        <input type="hidden" name="acao" value="cadastrar_aluno">
                        <label for="novo_aluno_nome">Nome</label>
                        <input id="novo_aluno_nome" name="nome" type="text" maxlength="150" required>
                        <label for="novo_aluno_rm">RM</label>
                        <input id="novo_aluno_rm" name="rm" type="text" maxlength="30" required>
                        <label for="novo_aluno_genero">Gênero</label>
                        <select id="novo_aluno_genero" name="genero" required>
                            <option value="masculino">Masculino</option>
                            <option value="feminino">Feminino</option>
                        </select>

                        <fieldset class="dashboard-modalities">
                            <legend>Modalidades/times</legend>
                            <p>Marque uma ou mais modalidades para o aluno.</p>
                            <?php foreach ($modalidadesParaCadastro as $modalidade): ?>
                                <?php
                                $lotada = (int) $modalidade['participantes'] >= (int) $modalidade['quantidade'];
                                ?>
                                <label class="dashboard-checkbox">
                                    <input
                                        type="checkbox"
                                        name="modalidades[]"
                                        value="<?= (int) $modalidade['id'] ?>"
                                        <?= $lotada ? 'disabled' : '' ?>
                                    >
                                    <?= $escape($modalidade['nome'] . ' - ' . $modalidade['genero']) ?>
                                    (<?= (int) $modalidade['participantes'] ?>/<?= (int) $modalidade['quantidade'] ?>)
                                    <?= $lotada ? '— lotada' : '' ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>

                        <button type="submit">Cadastrar aluno nos times</button>
                    </form>
                <?php else: ?>
                    <p>Não há modalidades com vagas disponíveis para sua sala.</p>
                <?php endif; ?>

                <h2>Alunos e representantes da sala</h2>
                <?php if ($alunosSala === []): ?>
                    <p>Nenhum aluno cadastrado nesta sala.</p>
                <?php else: ?>
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
                                <?php foreach ($alunosSala as $aluno): ?>
                                    <tr>
                                        <td><?= $escape($aluno['nome']) ?></td>
                                        <td><?= $escape($aluno['rm']) ?></td>
                                        <td><?= $escape($aluno['genero']) ?></td>
                                        <td><?= $escape($aluno['tipo']) ?></td>
                                        <td>
                                            <?php if ($aluno['tipo'] === 'aluno'): ?>
                                                <a href="inicio.php?editar_aluno=<?= (int) $aluno['id'] ?>#editar-aluno">
                                                    Editar
                                                </a>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="dashboard-card" id="editar-aluno">
                <h2>Editar aluno</h2>
                <?php if ($erroEdicao !== ''): ?>
                    <p class="dashboard-inline-error"><?= $escape($erroEdicao) ?></p>
                <?php endif; ?>
                <?php if ($alunoParaEditar !== null): ?>
                    <form action="inicio.php?editar_aluno=<?= (int) $alunoParaEditar['id'] ?>#editar-aluno" method="POST">
                        <input type="hidden" name="acao" value="atualizar_aluno">
                        <input type="hidden" name="id_aluno" value="<?= (int) $alunoParaEditar['id'] ?>">
                        <label for="editar_aluno_nome">Nome</label>
                        <input
                            id="editar_aluno_nome"
                            name="nome"
                            type="text"
                            value="<?= $escape($alunoParaEditar['nome']) ?>"
                            required
                        >
                        <label for="editar_aluno_rm">RM</label>
                        <input
                            id="editar_aluno_rm"
                            name="rm"
                            type="text"
                            value="<?= $escape($alunoParaEditar['rm']) ?>"
                            required
                        >
                        <label for="editar_aluno_genero">Gênero</label>
                        <select id="editar_aluno_genero" name="genero" required>
                            <option value="masculino" <?= $alunoParaEditar['genero'] === 'masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="feminino" <?= $alunoParaEditar['genero'] === 'feminino' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                        <fieldset class="dashboard-modalities">
                            <legend>Modalidades/times</legend>
                            <p>Marque todas as modalidades em que o aluno deve participar.</p>
                            <?php foreach ($modalidadesDoAluno as $modalidade): ?>
                                <?php
                                $participa = (int) $modalidade['selecionada'] === 1;
                                $lotada = (int) $modalidade['participantes'] >= (int) $modalidade['quantidade'];
                                ?>
                                <label class="dashboard-checkbox">
                                    <input
                                        type="checkbox"
                                        name="modalidades[]"
                                        value="<?= (int) $modalidade['id'] ?>"
                                        <?= $participa ? 'checked' : '' ?>
                                        <?= !$participa && $lotada ? 'disabled' : '' ?>
                                    >
                                    <?= $escape($modalidade['nome'] . ' - ' . $modalidade['genero']) ?>
                                    (<?= (int) $modalidade['participantes'] ?>/<?= (int) $modalidade['quantidade'] ?>)
                                    <?= !$participa && $lotada ? '— lotada' : '' ?>
                                </label>
                            <?php endforeach; ?>
                        </fieldset>
                        <button type="submit">Salvar alterações</button>
                        <a class="dashboard-secondary-link" href="inicio.php#editar-aluno">Cancelar</a>
                    </form>
                <?php else: ?>
                    <p>Selecione “Editar” ao lado de um aluno para abrir o formulário aqui.</p>
                <?php endif; ?>
            </section>

            <section class="dashboard-card dashboard-card-wide" id="times-montados">
                <h2>Times da minha sala</h2>
                <p>As equipes e seus participantes são exibidos somente para a sala vinculada a este representante.</p>
                <?php if ($erroListagemTimes !== ''): ?>
                    <p class="dashboard-inline-error"><?= $escape($erroListagemTimes) ?></p>
                <?php elseif ($timesPorModalidade === []): ?>
                    <p>Nenhum time foi criado para esta sala. Cadastre um aluno em uma modalidade para começar.</p>
                <?php else: ?>
                    <div class="dashboard-rosters">
                        <?php foreach ($timesPorModalidade as $time): ?>
                            <article class="dashboard-roster">
                                <h3><?= $escape($time['nome']) ?> — <?= $escape($time['genero']) ?></h3>
                                <p>
                                    <?= count($time['alunos']) ?> de
                                    <?= (int) $time['quantidade'] ?> participantes
                                </p>
                                <?php if ($time['alunos'] === []): ?>
                                    <p>O time ainda não possui alunos vinculados.</p>
                                <?php else: ?>
                                    <ul>
                                        <?php foreach ($time['alunos'] as $alunoTime): ?>
                                            <li>
                                                <?= $escape($alunoTime['nome']) ?>
                                                (RM: <?= $escape($alunoTime['rm']) ?>,
                                                <?= $escape($alunoTime['genero']) ?>)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>