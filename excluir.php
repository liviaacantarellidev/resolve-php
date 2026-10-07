
<?php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit('ID inválido.');
}

$stmt = $pdo->prepare("DELETE FROM tickets WHERE id = :id");

$stmt->execute([
    ':id' => $id
]);

header('Location: index.php');
exit;
