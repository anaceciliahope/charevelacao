<?php
/**
 * Helper para envio de e-mails pela API do Resend (sem Composer).
 * Incluído por enviar_confirmacao.php e diagnostico_resend.php.
 */

/**
 * Obtém a chave de API do Resend.
 *
 * Fonte 1: variável de ambiente do Windows RESEND_API_KEY (via getenv).
 * Fonte 2 (fallback, para ambiente local sem a variável):
 *   arquivo .env na raiz do projeto, lido manualmente (sem dotenv).
 *
 * O .env está no .gitignore e nunca deve ser versionado.
 * A chave nunca é exibida em mensagens de erro ou logs.
 */
function obter_resend_api_key(): string
{
    $chave = getenv('RESEND_API_KEY');
    if (is_string($chave) && trim($chave) !== '') {
        return trim($chave);
    }

    $envArquivo = __DIR__ . '/.env';
    if (is_file($envArquivo)) {
        foreach (file($envArquivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
            $linha = trim($linha);
            if ($linha === '' || $linha[0] === '#') {
                continue;
            }
            [$nome, $valor] = array_pad(explode('=', $linha, 2), 2, '');
            if (trim($nome) === 'RESEND_API_KEY') {
                return trim(trim($valor), "\"'");
            }
        }
    }

    return '';
}

/**
 * Envia um e-mail pela API do Resend.
 *
 * @return array{ok:bool, http:int|null, msg:string}
 */
function resend_enviar(string $destino, string $assunto, string $corpo): array
{
    $apiKey = obter_resend_api_key();
    if ($apiKey === '') {
        return ['ok' => false, 'http' => null, 'msg' => 'A variável de ambiente RESEND_API_KEY não foi configurada.'];
    }

    $remetente = defined('CONFIG_REMETENTE') ? CONFIG_REMETENTE : 'onboarding@resend.dev';

    $payload = json_encode([
        'from'    => $remetente,
        'to'      => [$destino],
        'subject' => $assunto,
        'text'    => $corpo
    ], JSON_UNESCAPED_UNICODE);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT        => 20,
        // Ambiente local (XAMPP) com proxy interceptando o TLS.
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);
    $resposta = curl_exec($ch);
    $http     = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $erroCurl = curl_error($ch);
    curl_close($ch);

    if ($http >= 200 && $http < 300) {
        return ['ok' => true, 'http' => $http, 'msg' => 'E-mail enviado com sucesso.'];
    }

    $decodificado = json_decode((string)$resposta, true);
    $msg = $decodificado['message'] ?? ($resposta !== false ? (string)$resposta : $erroCurl);
    return ['ok' => false, 'http' => $http, 'msg' => 'Falha no envio: ' . $msg];
}
