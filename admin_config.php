<?php
/**
 * CONFIGURAÇÃO DO SITE
 * Edite os valores abaixo conforme necessário.
 */

/* ===== Banco de Dados (MySQL / XAMPP) ===== */
define('DB_HOST', 'localhost');
define('DB_NAME', 'chadebebe');
define('DB_USER', 'dev');
define('DB_PASS', '');

/* ===== Acesso à página de administração ===== */
define('ADMIN_PASSWORD', 'chadebebe2026');

/* ===== E-mail que recebe as confirmações (dona da festa) ===== */
define('CONFIG_EMAILS', ['anaceciliahope@gmail.com']);

/* ===== Envio de e-mails via Resend (https://resend.com) ===== */
// A API Key NÃO fica neste arquivo. Ela é lida pela função
// obter_resend_api_key() (em resend.php), que usa nesta ordem:
//   1. Variável de ambiente RESEND_API_KEY (getenv)
//   2. Arquivo .env da raiz do projeto (lido manualmente, sem Composer/dotenv)
// NUNCA coloque a chave diretamente em um arquivo PHP.

// Remetente (DOMÍNIO deve estar verificado no Resend).
// Para testes sem domínio próprio, use: onboarding@resend.dev
define('CONFIG_REMETENTE', 'onboarding@resend.dev');
