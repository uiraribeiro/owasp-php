<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Validação SEGURA de certificado TLS em requisições HTTPS
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: CURLOPT_SSL_VERIFYPEER => true e CURLOPT_SSL_VERIFYHOST => 2.
 * Valida que o certificado é válido e pertence ao host esperado.
 * Protege contra man-in-the-middle attacks.
 */

function criarOpcoesCurl(string $url): array
{
    // SEMPRE VALIDE CERTIFICADOS TLS!
    // SSL_VERIFYPEER => true: valida a cadeia de certificados
    // SSL_VERIFYHOST => 2: valida que o certificado é do host correto
    return [
        'CURLOPT_URL' => $url,
        'CURLOPT_RETURNTRANSFER' => true,
        'CURLOPT_TIMEOUT' => 30,
        CURLOPT_SSL_VERIFYPEER => true,   // SEGURO
        CURLOPT_SSL_VERIFYHOST => 2,      // SEGURO (valida host name)
        CURLOPT_CAINFO => '/etc/ssl/certs/ca-bundle.crt', // Caminho do CA bundle
    ];
}

function demo(): void
{
    echo "=== Opções cURL SEGURA (certificado validado) ===\n";
    $url = 'https://api.exemplo.com/dados';
    $opcoes = criarOpcoesCurl($url);

    echo "URL: {$url}\n";
    echo "SSL_VERIFYPEER: " . ($opcoes[CURLOPT_SSL_VERIFYPEER] ? 'true' : 'false') . " (seguro!)\n";
    echo "SSL_VERIFYHOST: " . $opcoes[CURLOPT_SSL_VERIFYHOST] . " (seguro!)\n";
    echo "\nProtegido contra man-in-the-middle attacks\n";
    echo "Certificado é validado contra o host\n";
}

if (debug_backtrace() === []) {
    demo();
}
