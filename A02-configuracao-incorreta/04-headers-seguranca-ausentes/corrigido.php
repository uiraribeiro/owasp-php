<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Configurar Headers de Segurança HTTP
 *
 * Aplicação configura todos os headers essenciais de segurança.
 * Navegador recebe proteção contra clickjacking, MIME sniffing, XSS e MITM.
 */

function gerarHeadersSeguranca(): array {
    // CORRIGIDO: configura todos os headers de segurança essenciais
    return [
        'X-Content-Type-Options' => 'nosniff',                                    // Previne MIME sniffing
        'X-Frame-Options' => 'DENY',                                             // Previne clickjacking
        'Content-Security-Policy' => "default-src 'self'",                       // Previne injeção de conteúdo
        'Strict-Transport-Security' => 'max-age=63072000; includeSubDomains',   // Força HTTPS
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Headers de segurança configurados ===\n";

    $headers = gerarHeadersSeguranca();
    if (!empty($headers)) {
        echo "Headers de segurança configurados:\n";
        foreach ($headers as $nome => $valor) {
            echo "  {$nome}: {$valor}\n";
        }
        echo "\nAplicação protegida contra ataques comuns (clickjacking, MIME sniffing, XSS)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
