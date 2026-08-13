<?php
/**
 * Conexão com o banco de dados (PDO) e criação automática das tabelas.
 */
require_once __DIR__ . '/admin_config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        // Garante que o banco exista antes de conectar
        $tmp = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $tmp->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $tmp = null;

        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function criar_tabelas(): void
{
    $pdo = db();

    $pdo->exec("CREATE TABLE IF NOT EXISTS config (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome_evento VARCHAR(255) NOT NULL DEFAULT 'Chá de Bebê & Revelação',
        data_evento DATE NULL,
        horario VARCHAR(50) NOT NULL DEFAULT '12:00',
        local VARCHAR(255) NOT NULL DEFAULT '',
        endereco VARCHAR(255) NOT NULL DEFAULT '',
        mensagem TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS confirmacoes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        resposta VARCHAR(100) NOT NULL,
        pessoas INT NOT NULL DEFAULT 0,
        tamanho_fralda VARCHAR(100) DEFAULT NULL,
        palpite VARCHAR(100) DEFAULT NULL,
        data_hora DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Semeia a configuração padrão quando a tabela está vazia
    $count = (int)$pdo->query('SELECT COUNT(*) FROM config')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('INSERT INTO config (nome_evento, data_evento, horario, local, endereco, mensagem) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            'Chá de Bebê & Revelação',
            '2026-09-27',
            '12:00',
            'Casa do Casal',
            'Rua Retiro das Aves, nº 90A - Capim Rasteiro, Contagem - MG',
            'Nosso maior presente está a caminho! E queremos dividir esse momento tão especial com as pessoas que amamos.'
        ]);
    }
}

function obter_config(): array
{
    criar_tabelas();
    $config = db()->query('SELECT * FROM config ORDER BY id LIMIT 1')->fetch();
    if (!$config) {
        return [
            'nome_evento' => 'Chá de Bebê & Revelação',
            'data_evento' => '2026-09-27',
            'horario'     => '12:00',
            'local'       => 'Casa do Casal',
            'endereco'    => 'Rua Retiro das Aves, nº 90A - Capim Rasteiro, Contagem - MG',
            'mensagem'    => 'Nosso maior presente está a caminho! E queremos dividir esse momento tão especial com as pessoas que amamos.'
        ];
    }
    return $config;
}

function formatar_data_pt(?string $data): string
{
    if (!$data) {
        return '';
    }
    $meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
              7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
    $t = strtotime($data);
    return date('j', $t) . ' de ' . $meses[(int)date('n', $t)] . ', ' . date('Y', $t);
}

function formatar_data_completa_pt(?string $data): string
{
    if (!$data) {
        return '';
    }
    $dias = [0 => 'Domingo', 1 => 'Segunda-feira', 2 => 'Terça-feira', 3 => 'Quarta-feira', 4 => 'Quinta-feira', 5 => 'Sexta-feira', 6 => 'Sábado'];
    $meses = [1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
              7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'];
    $t = strtotime($data);
    return $dias[(int)date('w', $t)] . ', ' . date('j', $t) . ' de ' . $meses[(int)date('n', $t)] . ' de ' . date('Y', $t);
}
