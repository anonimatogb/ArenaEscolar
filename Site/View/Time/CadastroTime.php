
<?php
session_start();

if (
    !isset($_SESSION['id_usuario']) ||
    ($_SESSION['tipo'] ?? '') !== 'representante'
) {
    header('Location: ../Usuario/Login.php');
    exit();
}

require_once dirname(__DIR__, 3) . '/DB/database.php';
require_once dirname(__DIR__, 2) . '/Controller/TimeController.php';

$controller = new TimeController($pdo);

$idUsuario = (int) $_SESSION['id_usuario'];

$mensagem = '';
$sucesso = false;
$modalidades = [];
$alunos = [];
$modalidadeSelecionada = null;
$idModalidadeSelecionada = null;

// Busca modalidades disponíveis para a sala
try {
    $modalidades = $controller->listarModalidadesDisponiveis($idUsuario);
} catch (Exception $e) {
    $mensagem = $e->getMessage();
}

// Recebe o cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idModalidade = filter_input(
        INPUT_POST,
        'id_modalidade',
        FILTER_VALIDATE_INT
    );

    $alunosSelecionados = $_POST['alunos'] ?? [];

    if (!is_array($alunosSelecionados)) {
        $alunosSelecionados = [];
    }

    try {
        if (!$idModalidade) {
            throw new Exception('Selecione uma modalidade válida.');
        }

        $controller->cadastrarTime(
            $idUsuario,
            $idModalidade,
            $alunosSelecionados
        );

        $mensagem = 'Time cadastrado com sucesso!';
        $sucesso = true;

        // Atualiza as modalidades após o cadastro
        $modalidades = $controller->listarModalidadesDisponiveis($idUsuario);

    } catch (Exception $e) {
        $mensagem = $e->getMessage();
    }
}

// Recebe a modalidade selecionada para carregar os alunos
if (!$sucesso && isset($_GET['id_modalidade'])) {

    $idModalidadeSelecionada = filter_var(
        $_GET['id_modalidade'],
        FILTER_VALIDATE_INT
    );

    try {
        if (!$idModalidadeSelecionada) {
            throw new Exception('Modalidade inválida.');
        }

        foreach ($modalidades as $modalidade) {
            if ((int) $modalidade['id'] === $idModalidadeSelecionada) {
                $modalidadeSelecionada = $modalidade;
                break;
            }
        }

        if (!$modalidadeSelecionada) {
            throw new Exception(
                'Essa modalidade já foi cadastrada ou não está disponível.'
            );
        }

        $alunos = $controller->listarAlunosDisponiveis(
            $idUsuario,
            $idModalidadeSelecionada
        );

    } catch (Exception $e) {
        $mensagem = $e->getMessage();
        $idModalidadeSelecionada = null;
        $modalidadeSelecionada = null;
        $alunos = [];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Time</title>
    <link rel="stylesheet" href="../../Style/common.css">
    <link rel="stylesheet" href="../../Style/Time.css">
</head>

<body>

    <h1>Cadastro de Time</h1>

    <?php if ($mensagem): ?>
        <p>
            <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <h2>1. Escolha a modalidade</h2>

    <form action="CadastroTime.php" method="GET">

        <label for="id_modalidade">Modalidade:</label>

        <select
            name="id_modalidade"
            id="id_modalidade"
            onchange="this.form.submit()"
            required
        >
            <option value="">Selecione uma modalidade</option>

            <?php foreach ($modalidades as $modalidade): ?>

                <option
                    value="<?= (int) $modalidade['id'] ?>"
                    <?= $idModalidadeSelecionada == $modalidade['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($modalidade['nome']) ?>
                    - <?= htmlspecialchars($modalidade['genero']) ?>
                </option>

            <?php endforeach; ?>

        </select>

        <noscript>
            <button type="submit">Selecionar modalidade</button>
        </noscript>

    </form>

    <?php if ($modalidadeSelecionada): ?>

        <h2>2. Selecione os alunos</h2>

        <p>
            Modalidade:
            <strong>
                <?= htmlspecialchars($modalidadeSelecionada['nome']) ?>
            </strong>
        </p>

        <p>
            Gênero:
            <?= htmlspecialchars($modalidadeSelecionada['genero']) ?>
        </p>

        <p>
            Quantidade necessária:
            <strong>
                <?= (int) $modalidadeSelecionada['quantidade'] ?> alunos
            </strong>
        </p>

        <?php if (count($alunos) > 0): ?>

            <form action="CadastroTime.php" method="POST">

                <input
                    type="hidden"
                    name="id_modalidade"
                    value="<?= (int) $modalidadeSelecionada['id'] ?>"
                >

                <?php foreach ($alunos as $aluno): ?>

                    <div>
                        <label>
                            <input
                                type="checkbox"
                                name="alunos[]"
                                value="<?= (int) $aluno['id'] ?>"
                            >

                            <?= htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?>

                            (RM:
                            <?= htmlspecialchars($aluno['rm'], ENT_QUOTES, 'UTF-8') ?>)
                        </label>
                    </div>

                <?php endforeach; ?>

                <br>

                <button type="submit">Cadastrar Time</button>

            </form>

        <?php else: ?>

            <p>Não existem alunos disponíveis para essa modalidade.</p>

        <?php endif; ?>

    <?php endif; ?>

    <br>

    <a href="../Representante/inicio.php">Voltar</a>

</body>
</html>
