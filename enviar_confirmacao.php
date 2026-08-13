<?php
/**
 * Recebe a confirmação de presença (RSVP) via fetch (JSON),
 * salva no banco e envia um e-mail pelo Resend com os dados:
 * nome, resposta, acompanhantes, fralda sorteada e palpite.
 *
 * A API Key do Resend é lida pela função obter_resend_api_key() (resend.php),
 * que usa a variável de ambiente RESEND_API_KEY (getenv) e, como fallback,
 * o arquivo .env. A chave NUNCA fica neste arquivo nem em admin_config.php.
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/resend.php';
criar_tabelas();

/* ============ CONFIGURAÇÃO ============ */
$destinatarios = defined('CONFIG_EMAILS') ? CONFIG_EMAILS : ['seuemail@exemplo.com'];
/* ====================================== */

$dados = json_decode(file_get_contents('php://input'), true);
if (!is_array($dados)) {
    $dados = $_POST;
}

$nome = trim(strip_tags($dados['nome'] ?? ''));
$resposta = trim(strip_tags($dados['resposta'] ?? ''));
$pessoas = (int)($dados['pessoas'] ?? 0);
$tamanhoSorteado = trim(strip_tags($dados['tamanhoSorteado'] ?? ''));
$palpite = trim(strip_tags($dados['palpite'] ?? ''));
$confirmou = strpos($resposta, 'Sim') === 0;

if ($nome === '' || $resposta === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'msg' => 'Dados incompletos.']);
    exit;
}

$dataHora = date('d/m/Y H:i:s');

// Salvar no banco de dados
$pdo = db();
$stmt = $pdo->prepare('INSERT INTO confirmacoes (nome, resposta, pessoas, tamanho_fralda, palpite, data_hora) VALUES (?, ?, ?, ?, ?, NOW())');
$stmt->execute([
    $nome,
    $resposta,
    $pessoas,
    $tamanhoSorteado !== '' ? $tamanhoSorteado : null,
    $palpite !== '' ? $palpite : null
]);

// Montar corpo do e-mail
$nomeEvento = obter_config()['nome_evento'];
$assunto = 'Nova confirmação de presença - ' . $nomeEvento;
$corpo  = "Uma nova resposta de presença foi registrada:\n\n";
$corpo .= "Nome: " . $nome . "\n";
$corpo .= "Resposta: " . $resposta . "\n";
if ($pessoas > 0) {
    $corpo .= "Acompanhantes: " . $pessoas . "\n";
}
if ($tamanhoSorteado !== '') {
    $corpo .= "Fralda sorteada: " . $tamanhoSorteado . "\n";
}
if ($palpite !== '') {
    $corpo .= "Palpite: " . $palpite . "\n";
}
$corpo .= "\nData: " . $dataHora . "\n";

$enviado = false;
$msgEnvio = '';
foreach ($destinatarios as $destino) {
    $res = resend_enviar($destino, $assunto, $corpo);
    if ($res['ok']) {
        $enviado = true;
    } else {
        $msgEnvio = $res['msg'];
    }
}

echo json_encode([
    'ok'  => $enviado,
    'msg' => $enviado ? 'E-mail enviado com sucesso.' : $msgEnvio
]);
