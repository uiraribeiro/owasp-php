<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Uso inadequado de IV (Initialization Vector) - IV fixo/zerado
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: IV é fixo (todos zeros) ao invés de aleatório.
 * Mesmo texto plano produz o mesmo criptograma sempre (modo CBC).
 * Vaza padrões e é vulnerável a ataques de análise de frequência.
 */

function criptografarCbc(string $texto, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // NUNCA USE IV FIXO!
    // Usa IV zerado (todos zeros) - PÉSSIMA PRÁTICA
    $iv = str_repeat("\0", 16);

    // Criptografa com AES-256-CBC
    $textoCifrado = openssl_encrypt($texto, 'aes-256-cbc', $chaveNorm, OPENSSL_RAW_DATA, $iv);

    // Retorna apenas o criptograma (IV não é prefixado pois é fixo)
    return $textoCifrado;
}

function descriptografarCbc(string $textoCifrado, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // Usa o MESMO IV fixo
    $iv = str_repeat("\0", 16);

    // Decriptografa
    $texto = openssl_decrypt($textoCifrado, 'aes-256-cbc', $chaveNorm, OPENSSL_RAW_DATA, $iv);
    return $texto === false ? '' : $texto;
}

function demo(): void
{
    echo "=== Criptografia CBC VULNERÁVEL (IV fixo) ===\n";
    $chave = 'chave-teste';
    $texto = 'Dados sensiveis';

    $cifra1 = criptografarCbc($texto, $chave);
    $cifra2 = criptografarCbc($texto, $chave);

    echo "Texto plano: {$texto}\n";
    echo "Cifra 1 (hex): " . bin2hex($cifra1) . "\n";
    echo "Cifra 2 (hex): " . bin2hex($cifra2) . "\n";
    echo "Cifras sao identicas? " . (bin2hex($cifra1) === bin2hex($cifra2) ? 'SIM (BUG!)' : 'NAO') . "\n";
    echo "IV fixo: " . bin2hex(str_repeat("\0", 16)) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
