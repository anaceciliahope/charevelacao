<?php
/**
 * DIAGNÓSTICO SEGURO DO ENVIO PELO RESEND
 *
 * Não exibe a API Key completa (apenas uma versão mascarada) e NÃO envia
 * e-mail se a chave não existir. Mostra o código HTTP e a mensagem de erro
 * retornada pelo Resend, sem expor a chave.
 *
 * Acesso: http://localhost:8080/chadebebe/diagnostico_resend.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/admin_config.php';
require_once __DIR__ . '/resend.php';

function mascarar_chave(string $chave): string
{
    $tam = strlen($chave);
    if ($tam <= 8) {
        return '***';
    }
    return substr($chave, 0, 3) . str_repeat('*', max(4, $tam - 7)) . substr($chave, -4);
}

$apiKey = obter_resend_api_key();
$getenvDireto = getenv('RESEND_API_KEY');

if ($apiKey === '') {
    $fonte = 'nenhuma';
} elseif (is_string($getenvDireto) && trim($getenvDireto) !== '') {
    $fonte = 'variável de ambiente (getenv)';
} else {
    $fonte = 'arquivo .env (fallback manual)';
}

$resultado = [
    'php'            => PHP_VERSION,
    'curl'           => function_exists('curl_init') ? 'disponivel' : 'indisponivel',
    'api_key'        => $apiKey === '' ? 'NAO_CONFIGURADA' : 'encontrada (' . mascarar_chave($apiKey) . ')',
    'fonte_da_chave' => $fonte,
    'destino'        => defined('CONFIG_EMAILS') ? implode(', ', CONFIG_EMAILS) : 'seuemail@exemplo.com',
    'remetente'      => defined('CONFIG_REMETENTE') ? CONFIG_REMETENTE : 'onboarding@resend.dev',
    'teste_http'     => null,
    'resend_msg'     => null,
];

if ($apiKey !== '' && function_exists('curl_init')) {
    $destino = defined('CONFIG_EMAILS') ? CONFIG_EMAILS[0] : 'seuemail@exemplo.com';
    $res = resend_enviar(
        $destino,
        '[Diagnóstico] Teste de envio Resend',
        "Este é um e-mail de teste enviado pelo modo de diagnóstico do site Chá de Bebê."
    );
    $resultado['teste_http'] = $res['http'];
    $resultado['resend_msg'] = $res['msg'];
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
