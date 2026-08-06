<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Desabilitação da validação de certificado TLS em requisições HTTPS
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: CURLOPT_SSL_VERIFYPEER => false desabilita validação.
 * Permite man-in-the-middle attacks mesmo usando HTTPS.
 * Um ataque que intercepta a conexão consegue se fazer passar pela API.
 */

function criarOpcoesCurl(string $url): array
{
    // NUNCA DESABILITE SSL_VERIFYPEER!
    // Isto permite ataques man-in-the-middle mesmo com HTTPS
    return [
        'CURLOPT_URL' => $url,
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_TIMEOUT' => 30,
        CURLOPT_SSL_VERIFYPEER => false,  // VULNERÁVEL!
        CURLOPT_SSL_VERIFYHOST => 0,      // VULNERÁVEL!
    ];
}

function demo(): void
{
    echo "=== Opções cURL VULNERÁVEL (certificado não validado) ===\n";
    $url = 'https://api.exemplo.com/dados';
    $opcoes = criarOpcoesCurl($url);

    echo "URL: {$url}\n";
    echo "SSL_VERIFYPEER: " . ($opcoes[CURLOPT_SSL_VERIFYPEER] ? 'true' : 'false') . " (BUG!)\n";
    echo "SSL_VERIFYHOST: " . $opcoes[CURLOPT_SSL_VERIFYHOST] . " (BUG!)\n";
    echo "\nVulnerável a man-in-the-middle attack mesmo usando HTTPS!\n";
}

if (debug_backtrace() === []) {
    demo();
}
