
<?php
require_once __DIR__ . '/config/database.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente'] ?? '');
    $assunto = trim($_POST['assunto'] ?? '');
    $prioridade = $_POST['prioridade'] ?? 'Normal';

    $prioridadesValidas = ['Baixa', 'Normal', 'Alta', 'Urgente'];

    if ($cliente === '' || $assunto === '') {
        $erro = 'Preencha todos os campos obrigatórios.';
    } elseif (!in_array($prioridade, $prioridadesValidas, true)) {
        $erro = 'Prioridade inválida.';
    } else {
        $sql = "INSERT INTO tickets
                (cliente, assunto, prioridade, status)
                VALUES (:cliente, :assunto, :prioridade, 'Aberto')";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':cliente' => $cliente,
            ':assunto' => $assunto,
            ':prioridade' => $prioridade
        ]);

        header('Location: index.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo chamado | RESOLVE.</title>
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
            <h1>Novo chamado</h1>
            <p class="subtitle">
                Registre uma nova solicitação de suporte.
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
                    placeholder="Ex.: Northstar Labs"
                    maxlength="100"
                    value="<?= htmlspecialchars($cliente ?? '') ?>"
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
                    placeholder="Descreva brevemente o problema"
                    maxlength="255"
                    value="<?= htmlspecialchars($assunto ?? '') ?>"
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
                            <?= ($prioridade ?? 'Normal') === $opcao ? 'selected' : '' ?>
                        >
                            <?= $opcao ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-accent">
                    Criar chamado
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
