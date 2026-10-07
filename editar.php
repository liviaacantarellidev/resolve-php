
<?php
require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID do chamado inválido.');
}

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = :id");
$stmt->execute([':id' => $id]);

$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    exit('Chamado não encontrado.');
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $prioridade = $_POST['prioridade'] ?? '';
    $status = $_POST['status'] ?? '';

    $prioridadesValidas = ['Baixa', 'Normal', 'Alta', 'Urgente'];
    $statusValidos = ['Aberto', 'Em andamento', 'Resolvido'];

    if ($cliente === '' || $assunto === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (
        !in_array($prioridade, $prioridadesValidas, true) ||
        !in_array($status, $statusValidos, true)
    ) {
        $erro = 'Prioridade ou status inválido.';
    } else {
        $sql = "UPDATE tickets
                SET cliente = :cliente,
                    assunto = :assunto,
                    prioridade = :prioridade,
                    status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':cliente' => $cliente,
            ':assunto' => $assunto,
            ':prioridade' => $prioridade,
            ':status' => $status,
            ':id' => $id
        ]);

        header('Location: index.php');
        exit;
    }

    $ticket['cliente'] = $cliente;
    $ticket['assunto'] = $assunto;
    $ticket['prioridade'] = $prioridade;
    $ticket['status'] = $status;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar chamado | RESOLVE.</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

<header class="header">
    <a href="index.php" class="logo">RESOLVE.</a>
    <span class="header-label">Support workspace / PHP</span>
</header>

<main class="container">

    <div class="page-header">
        <div>
            <span class="eyebrow">Gerenciamento de chamados</span>
            <h1>Editar chamado #<?= (int) $ticket['id'] ?></h1>
            <p class="subtitle">
                Atualize as informações desta solicitação.
            </p>
        </div>
    </div>

    <div class="form-card">

        <?php if ($erro): ?>
            <div class="error">
                <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="cliente">Cliente</label>

                <input
                    class="form-control"
                    type="text"
                    id="cliente"
                    name="cliente"
                    maxlength="100"
                    value="<?= htmlspecialchars($ticket['cliente']) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="assunto">Assunto</label>

                <input
                    class="form-control"
                    type="text"
                    id="assunto"
                    name="assunto"
                    maxlength="255"
                    value="<?= htmlspecialchars($ticket['assunto']) ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="prioridade">Prioridade</label>

                <select
                    class="form-control"
                    id="prioridade"
                    name="prioridade"
                >
                    <?php foreach (['Baixa', 'Normal', 'Alta', 'Urgente'] as $opcao): ?>
                        <option
                            value="<?= $opcao ?>"
                            <?= $ticket['prioridade'] === $opcao ? 'selected' : '' ?>
                        >
                            <?= $opcao ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>

                <select
                    class="form-control"
                    id="status"
                    name="status"
                >
                    <?php foreach (['Aberto', 'Em andamento', 'Resolvido'] as $opcao): ?>
                        <option
                            value="<?= $opcao ?>"
                            <?= $ticket['status'] === $opcao ? 'selected' : '' ?>
                        >
                            <?= $opcao ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    Salvar alterações
                </button>

                <a href="index.php" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>

        </form>
    </div>

</main>

</body>
</html>
