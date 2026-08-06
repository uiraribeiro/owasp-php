<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Uso de algoritmos criptográficos inadequados (DES, modo ECB)
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: DES é um algoritmo de 56-bit considerado inseguro (espaço de chave muito pequeno).
 * Além disso, usa ECB (Electronic Code Book) que é determinístico e sem IV,
 * produzindo o mesmo criptograma para o mesmo texto plano a cada execução.
 */

function criptografar(string $texto, string $chave): string
{
    // NUNCA USE DES EM PRODUÇÃO - É INSEGURO
    // DES é stream cipher, não requer IV
    // Normaliza chave para 8 bytes (64 bits) para DES
    $chaveNorm = substr(hash('sha256', $chave, true), 0, 8);
    $textoCifrado = openssl_encrypt($texto, 'des-ede3-ecb', $chaveNorm, OPENSSL_RAW_DATA);
    if ($textoCifrado === false) {
        throw new \RuntimeException('Falha ao criptografar com DES');
    }
    return $textoCifrado;
}

function descriptografar(string $textoCifrado, string $chave): string
{
    // Decriptação com DES (igualmente fraca)
    // Normaliza chave para 8 bytes (64 bits) para DES
    $chaveNorm = substr(hash('sha256', $chave, true), 0, 8);
    $texto = openssl_decrypt($textoCifrado, 'des-ede3-ecb', $chaveNorm, OPENSSL_RAW_DATA);
    return $texto === false ? '' : $texto;
}

function demo(): void
{
    echo "=== Criptografia VULNERÁVEL (DES-ECB) ===\n";
    $chave = 'minha-chave-insegura';
    $texto = 'Dados sensiveis';

    $cifra1 = criptografar($texto, $chave);
    $cifra2 = criptografar($texto, $chave);

    echo "Texto plano: {$texto}\n";
    echo "Cifra 1 (hex): " . bin2hex($cifra1) . "\n";
    echo "Cifra 2 (hex): " . bin2hex($cifra2) . "\n";
    echo "Cifras sao identicas (PROBLEMA)? " . (bin2hex($cifra1) === bin2hex($cifra2) ? 'SIM' : 'NAO') . "\n";
    echo "Decriptado: " . descriptografar($cifra1, $chave) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
