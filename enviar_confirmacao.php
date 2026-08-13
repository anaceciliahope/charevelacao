<?php
/**
 * Recebe a confirmação de presença (RSVP) via fetch (JSON),
 * salva no banco e envia um e-mail pelo Resend com os dados:
 * nome, resposta, acompanhantes, fralda sorteada e palpite.
 *
 * Configuração em admin_config.php:
 *   RESEND_API_KEY  -> sua chave de API do Resend
 *   CONFIG_REMETENTE-> remetente (domínio verificado no Resend)
 *   CONFIG_EMAILS   -> e-mail(s) da dona da festa
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
criar_tabelas();

/* ============ CONFIGURAÇÃO ============ */
$destinatarios = defined('CONFIG_EMAILS') ? CONFIG_EMAILS : ['seuemail@exemplo.com'];
$remetente     = defined('CONFIG_REMETENTE') ? CONFIG_REMETENTE : 'Onboarding <onboarding@resend.dev>';
$apiKey        = defined('RESEND_API_KEY') ? RESEND_API_KEY : '';
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

/**
 * Envia e-mail pela API do Resend.
 * Retorna [bool, string] com status e mensagem.
 */
function enviar_email_resend(string $apiKey, string $remetente, string $destino, string $assunto, string $corpo): array
{
    if ($apiKey === '' || $apiKey === 're_SuaChaveAqui') {
        return [false, 'Configure a RESEND_API_KEY em admin_config.php.'];
    }

    $payload = json_encode([
        'from'    => $remetente,
        'to'      => [$destino],
        'subject' => $assunto,
        'text'    => $corpo
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init('https://api.resend.com/emails');
    $opcoes = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT        => 20,
        // Ambiente local (XAMPP) costuma ter proxy que intercepta o TLS;
        // desativa a checagem do certificado para garantir o envio.
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ];

    curl_setopt_array($ch, $opcoes);

    $resposta = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erroCurl = curl_error($ch);
    curl_close($ch);

    if ($status >= 200 && $status < 300) {
        return [true, 'E-mail enviado com sucesso.'];
    }

    $erro = json_decode($resposta, true);
    $msg = $erro['message'] ?? ($resposta !== false ? $resposta : $erroCurl);
    return [false, 'Falha no envio pelo Resend (' . $status . '): ' . $msg];
}

$enviado = false;
$msgEnvio = '';
foreach ($destinatarios as $destino) {
    [$ok, $msg] = enviar_email_resend($apiKey, $remetente, $destino, $assunto, $corpo);
    if ($ok) {
        $enviado = true;
    } else {
        $msgEnvio = $msg;
    }
}

echo json_encode([
    'ok'  => $enviado,
    'msg' => $enviado ? 'E-mail enviado com sucesso.' : $msgEnvio
]);
