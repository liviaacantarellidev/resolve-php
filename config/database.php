
<?php

$databasePath = __DIR__ . '/../database/chamados.sqlite';

try {
    $pdo = new PDO('sqlite:' . $databasePath);

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            cliente TEXT NOT NULL,
            assunto TEXT NOT NULL,
            prioridade TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Aberto',
            criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit('Erro ao conectar ao banco de dados.');
}
