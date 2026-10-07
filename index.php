
<?php
require_once __DIR__ . '/config/database.php';

// Recebe os filtros da URL
$busca = trim($_GET['busca'] ?? '');
$status = $_GET['status'] ?? '';
$prioridade = $_GET['prioridade'] ?? '';

$statusValidos = ['Aberto', 'Em andamento', 'Resolvido'];
$prioridadesValidas = ['Baixa', 'Normal', 'Alta', 'Urgente'];

// Monta a consulta SQL com filtros
$sql = "SELECT * FROM tickets WHERE 1=1";
$params = [];

if ($busca !== '') {
    $sql .= " AND (cliente LIKE :cliente OR assunto LIKE :assunto)";
    $params[':cliente'] = '%' . $busca . '%';
    $params[':assunto'] = '%' . $busca . '%';
}

if (in_array($status, $statusValidos, true)) {
    $sql .= " AND status = :status";
    $params[':status'] = $status;
}

if (in_array($prioridade, $prioridadesValidas, true)) {
    $sql .= " AND prioridade = :prioridade";
    $params[':prioridade'] = $prioridade;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Indicadores gerais do sistema
$stats = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'Aberto' THEN 1 ELSE 0 END) AS abertos,
        SUM(CASE WHEN prioridade = 'Urgente'
            AND status != 'Resolvido' THEN 1 ELSE 0 END) AS urgentes
    FROM tickets
")->fetch(PDO::FETCH_ASSOC);

$totalTickets = (int) $stats['total'];
$openTickets = (int) $stats['abertos'];
$urgentTickets = (int) $stats['urgentes'];

$resultados = count($tickets);

// Proteção dos dados exibidos
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Cores das prioridades
function priorityClass($priority) {
    return match ($priority) {
        'Urgente' => 'urgent',
        'Alta' => 'high',
        'Normal' => 'normal',
        'Baixa' => 'low',
        default => 'normal'
    };
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>RESOLVE. | Help Desk</title>

    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <!-- CABEÇALHO -->
    <header class="header">
        <a href="index.php" class="logo">RESOLVE.</a>
        <span class="header-label">Support workspace / PHP</span>
    </header>

    <main class="container">

        <!-- TÍTULO -->
        <div class="page-header">
            <div>
                <span class="eyebrow">Support workspace</span>

                <h1>Visão geral dos chamados</h1>

                <p class="subtitle">
                    Gerencie e acompanhe as solicitações dos clientes.
                </p>
            </div>

            <a href="criar.php" class="btn btn-primary">
                + Novo chamado
            </a>
        </div>

        <!-- INDICADORES -->
        <section class="stats">

            <div class="stat-card">
                <div class="stat-number">
                    <?= $totalTickets ?>
                </div>

                <div class="stat-label">
                    Total de chamados
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    <?= $openTickets ?>
                </div>

                <div class="stat-label">
                    Chamados abertos
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-number">
                    <?= $urgentTickets ?>
                </div>

                <div class="stat-label">
                    Urgentes pendentes
                </div>
            </div>

        </section>

        <!-- PESQUISA E FILTROS -->
        <form
            method="GET"
            action="index.php"
            class="filters"
        >

            <div class="filter-search">
                <label for="busca">Pesquisar</label>

                <input
                    type="text"
                    id="busca"
                    name="busca"
                    class="form-control"
                    placeholder="Cliente ou assunto..."
                    value="<?= e($busca) ?>"
                >
            </div>

            <div class="filter-field">
                <label for="status">Status</label>

                <select
                    name="status"
                    id="status"
                    class="form-control"
                >
                    <option value="">Todos</option>

                    <?php foreach ($statusValidos as $opcao): ?>
                        <option
                            value="<?= e($opcao) ?>"
                            <?= $status === $opcao ? 'selected' : '' ?>
                        >
                            <?= e($opcao) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-field">
                <label for="prioridade">Prioridade</label>

                <select
                    name="prioridade"
                    id="prioridade"
                    class="form-control"
                >
                    <option value="">Todas</option>

                    <?php foreach ($prioridadesValidas as $opcao): ?>
                        <option
                            value="<?= e($opcao) ?>"
                            <?= $prioridade === $opcao ? 'selected' : '' ?>
                        >
                            <?= e($opcao) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                Filtrar
            </button>

            <a href="index.php" class="btn btn-secondary">
                Limpar
            </a>

        </form>

        <!-- TABELA DE CHAMADOS -->
        <section class="card">

            <div class="card-header">
                <h2>Todos os chamados</h2>

                <p class="subtitle">
                    <?= $resultados ?> chamados encontrados
                </p>
            </div>

            <div class="table-wrapper">

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Assunto</th>
                            <th>Prioridade</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($tickets as $ticket): ?>

                            <tr>

                                <td class="ticket-id">
                                    #<?= e($ticket['id']) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= e($ticket['cliente']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($ticket['assunto']) ?>
                                </td>

                                <td>
                                    <span class="badge <?= priorityClass($ticket['prioridade']) ?>">
                                        <?= e($ticket['prioridade']) ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge <?= $ticket['status'] === 'Resolvido' ? 'status-resolved' : 'status-badge' ?>">
                                        <?= e($ticket['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="actions">

                                        <a
                                            class="edit-link"
                                            href="editar.php?id=<?= (int) $ticket['id'] ?>"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            action="excluir.php"
                                            method="POST"
                                            onsubmit="return confirm('Deseja realmente excluir este chamado?');"
                                        >
                                            <input
                                                type="hidden"
                                                name="id"
                                                value="<?= (int) $ticket['id'] ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn btn-danger"
                                            >
                                                Excluir
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

                <?php if (empty($tickets)): ?>

                    <div class="empty-state">
                        <p>Nenhum chamado encontrado.</p>

                        <a href="index.php" class="btn btn-secondary">
                            Limpar filtros
                        </a>
                    </div>

                <?php endif; ?>

            </div>

        </section>

    </main>

</body>
</html>
