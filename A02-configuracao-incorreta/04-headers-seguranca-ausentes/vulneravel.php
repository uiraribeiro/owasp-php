<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Headers de Segurança Ausentes - A02:2025 Security Misconfiguration
 *
 * Aplicação não configura headers HTTP essenciais de segurança.
 * Navegador fica vulnerável a clickjacking, MIME sniffing, injeção XSS e man-in-the-middle.
 */

function gerarHeadersSeguranca(): array {
    // VULNERÁVEL: nenhum header de segurança configurado
    return [];
}

function demo(): void {
    echo "=== VULNERÁVEL: Headers de segurança ausentes ===\n";

    $headers = gerarHeadersSeguranca();
    if (empty($headers)) {
        echo "Nenhum header de segurança configurado (PROBLEMA!)\n";
    }

    $headersEsperados = [
        'X-Content-Type-Options',
        'X-Frame-Options',
        'Content-Security-Policy',
        'Strict-Transport-Security',
    ];

    foreach ($headersEsperados as $header) {
        if (!isset($headers[$header])) {
            echo "Header {$header} ausente\n";
        }
    }
}

if (debug_backtrace() === []) {
    demo();
}
