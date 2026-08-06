<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Uso SEGURO de IV (Initialization Vector) - IV aleatório
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: IV aleatório de 16 bytes gerado a cada criptografia.
 * Mesmo texto plano produz criptogramas DIFERENTES.
 * IV é prefixado ao resultado para descriptografia posterior.
 */

function criptografarCbc(string $texto, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // USE IV ALEATÓRIO!
    // 16 bytes aleatórios para CBC
    $iv = random_bytes(16);

    // Criptografa com AES-256-CBC
    $textoCifrado = openssl_encrypt($texto, 'aes-256-cbc', $chaveNorm, OPENSSL_RAW_DATA, $iv);

    // Retorna: IV + CRIPTOGRAMA (IV prefixado)
    // Necessário para descriptografia posterior
    return $iv . $textoCifrado;
}

function descriptografarCbc(string $resultado, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // Extrai IV dos primeiros 16 bytes
    if (strlen($resultado) < 16) {
        return '';
    }

    $iv = substr($resultado, 0, 16);
    $textoCifrado = substr($resultado, 16);

    // Decriptografa com o IV extraído
    $texto = openssl_decrypt($textoCifrado, 'aes-256-cbc', $chaveNorm, OPENSSL_RAW_DATA, $iv);
    return $texto === false ? '' : $texto;
}

function demo(): void
{
    echo "=== Criptografia CBC SEGURA (IV aleatório) ===\n";
    $chave = 'chave-teste';
    $texto = 'Dados sensiveis';

    $cifra1 = criptografarCbc($texto, $chave);
    $cifra2 = criptografarCbc($texto, $chave);

    echo "Texto plano: {$texto}\n";
    echo "Cifra 1 (hex): " . bin2hex($cifra1) . "\n";
    echo "Cifra 2 (hex): " . bin2hex($cifra2) . "\n";
    echo "Cifras sao identicas? " . (bin2hex($cifra1) === bin2hex($cifra2) ? 'SIM (BUG!)' : 'NAO (correto, IV aleatório)') . "\n";
    echo "\nIV prefixado (primeiros 16 bytes do resultado)\n";
    echo "Decriptado 1: " . descriptografarCbc($cifra1, $chave) . "\n";
    echo "Decriptado 2: " . descriptografarCbc($cifra2, $chave) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
