<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Criptografia SEGURA usando AES-256-GCM
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: AES-256-GCM é um algoritmo moderno, seguro e autenticado.
 * Usa IV aleatório de 12 bytes a cada criptografia, garantindo
 * criptogramas diferentes para o mesmo texto plano (IV + authentication tag inclusos).
 */

function criptografar(string $texto, string $chave): string
{
    // Normaliza chave para 32 bytes (256 bits)
    $chaveNorm = hash('sha256', $chave, true);

    // Gera IV aleatório de 12 bytes (necessário para GCM)
    $iv = random_bytes(12);

    // Criptografa com AES-256-GCM usando OPENSSL_RAW_DATA
    $tag = '';
    $textoCifrado = openssl_encrypt($texto, 'aes-256-gcm', $chaveNorm, OPENSSL_RAW_DATA, $iv, $tag);

    // Retorna: IV + TAG + CRIPTOGRAMA
    // Tag é necessária para autenticação e detecção de tampering
    return $iv . $tag . $textoCifrado;
}

function descriptografar(string $resultado, string $chave): string
{
    // Normaliza chave para 32 bytes (256 bits)
    $chaveNorm = hash('sha256', $chave, true);

    // Extrai componentes do resultado concatenado
    $ivSize = 12; // 12 bytes
    $tagSize = 16; // 16 bytes (para AES-GCM)

    if (strlen($resultado) < ($ivSize + $tagSize)) {
        return '';
    }

    $iv = substr($resultado, 0, $ivSize);
    $tag = substr($resultado, $ivSize, $tagSize);
    $textoCifrado = substr($resultado, $ivSize + $tagSize);

    // Decriptografa e valida a tag
    $texto = openssl_decrypt($textoCifrado, 'aes-256-gcm', $chaveNorm, OPENSSL_RAW_DATA, $iv, $tag);
    return $texto === false ? '' : $texto;
}

function demo(): void
{
    echo "=== Criptografia SEGURA (AES-256-GCM) ===\n";
    $chave = 'minha-chave-insegura'; // Mesmo para comparação
    $texto = 'Dados sensiveis';

    $cifra1 = criptografar($texto, $chave);
    $cifra2 = criptografar($texto, $chave);

    echo "Texto plano: {$texto}\n";
    echo "Cifra 1 (hex): " . bin2hex($cifra1) . "\n";
    echo "Cifra 2 (hex): " . bin2hex($cifra2) . "\n";
    echo "Cifras sao identicas? " . (bin2hex($cifra1) === bin2hex($cifra2) ? 'SIM (BUG!)' : 'NAO (correto!)') . "\n";
    echo "Decriptado 1: " . descriptografar($cifra1, $chave) . "\n";
    echo "Decriptado 2: " . descriptografar($cifra2, $chave) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
