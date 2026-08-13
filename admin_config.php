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
// Chave de API do Resend (crie em resend.com -> API Keys)
define('RESEND_API_KEY', '');

// Remetente (DOMÍNIO deve estar verificado no Resend).
// Para testes sem domínio próprio, use: Onboarding <onboarding@resend.dev>
define('CONFIG_REMETENTE', 'Onboarding <onboarding@resend.dev>');
