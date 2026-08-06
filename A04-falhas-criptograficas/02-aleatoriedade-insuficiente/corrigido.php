<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Aleatoriedade CRIPTOGRAFICAMENTE SEGURA em tokens
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: random_bytes() usa CSPRNG (Cryptographically Secure Pseudo-Random Number Generator).
 * Gera 32 bytes (256 bits) convertidos em hexadecimal = 64 caracteres.
 * Espaço de busca: 2^256 (impraticável quebrar por força bruta).
 */

function gerarTokenRecuperacaoSenha(): string
{
    // USE random_bytes() PARA TOKENS CRIPTOGRÁFICOS
    // 32 bytes = 256 bits de entropia criptográficamente segura
    // Convertido em hexadecimal = 64 caracteres
    // Espaço de busca: 16^64 = 2^256
    return bin2hex(random_bytes(32));
}

function demo(): void
{
    echo "=== Gerador de Token SEGURO ===\n";
    for ($i = 0; $i < 5; $i++) {
        $token = gerarTokenRecuperacaoSenha();
        echo "Token {$i}: {$token} (tamanho: " . strlen($token) . ")\n";
    }
    echo "Espaço de busca: 2^256 (impraticavel quebrar)\n";
    echo "Cada token é único com probabilidade de colisão negligenciável\n";
}

if (debug_backtrace() === []) {
    demo();
}
