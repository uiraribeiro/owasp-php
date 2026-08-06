<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * CORS Mal Configurado (A01:2025 - Broken Access Control)
 *
 * O servidor reflete automaticamente a origem do cliente (qualquer uma)
 * no header Access-Control-Allow-Origin E permite credentials (cookies/auth).
 * Um site malicioso pode fazer requisições cross-origin ao servidor e ler
 * dados autenticados do usuário.
 */

function gerarCabecalhosCors(string $origemRequisicao): array {
    // VULNERÁVEL: reflete qualquer origem e permite credentials
    return [
        'Access-Control-Allow-Origin' => $origemRequisicao,
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: CORS Mal Configurado ===\n";

    $origemLegitima = 'https://meusite.com';
    $origemMaliciosa = 'https://atacante.com';

    $headersLegitimos = gerarCabecalhosCors($origemLegitima);
    echo "Headers para origem legítima: " . json_encode($headersLegitimos) . "\n";

    $headersMaliciosos = gerarCabecalhosCors($origemMaliciosa);
    echo "Headers para origem maliciosa: " . json_encode($headersMaliciosos) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
