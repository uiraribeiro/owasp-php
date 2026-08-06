<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Uso de modo de criptografia inadequado (ECB - Electronic Code Book)
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: ECB é determinístico - blocos de 16 bytes idênticos no texto plano
 * geram blocos idênticos no criptograma. Vaza padrões (ECB Penguin Problem).
 * Cada bloco é criptografado independentemente.
 */

function criptografarEcb(string $texto, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // NUNCA USE ECB!
    // ECB é determinístico: blocos iguais produzem criptogramas iguais
    $textoCifrado = openssl_encrypt($texto, 'aes-256-ecb', $chaveNorm, OPENSSL_RAW_DATA);

    if ($textoCifrado === false) {
        throw new \RuntimeException('Falha ao criptografar com ECB');
    }

    return $textoCifrado;
}

function demo(): void
{
    echo "=== Criptografia ECB VULNERÁVEL (modo inadequado) ===\n";

    // Demonstração do ECB Penguin Problem
    // Texto com blocos repetidos (16 bytes cada)
    $chave = 'chave-teste';
    $bloco = 'AAAAAAAAAAAAAAAA'; // 16 A's = 1 bloco de 16 bytes
    $texto = $bloco . $bloco; // 2 blocos idênticos = 32 bytes

    echo "Texto (hex): " . bin2hex($texto) . "\n";
    echo "Bloco 1: " . bin2hex($bloco) . "\n";
    echo "Bloco 2: " . bin2hex($bloco) . "\n";

    $cifra = criptografarEcb($texto, $chave);

    echo "Cifra (hex): " . bin2hex($cifra) . "\n";
    echo "\nBlocos da cifra:\n";
    echo "Bloco 1 (bytes 0-15):   " . bin2hex(substr($cifra, 0, 16)) . "\n";
    echo "Bloco 2 (bytes 16-31):  " . bin2hex(substr($cifra, 16, 16)) . "\n";
    echo "\nSe os blocos da cifra forem IDENTICOS, vaza padrão (ECB problem)!\n";
}

if (debug_backtrace() === []) {
    demo();
}
