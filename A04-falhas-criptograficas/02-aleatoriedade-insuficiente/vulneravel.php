<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Aleatoriedade insuficiente em tokens criptográficos
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: mt_rand() não é criptograficamente seguro. Gera apenas 6 dígitos,
 * totalizando apenas 1 milhão de combinações possíveis (10^6).
 * Facilmente quebrável por força bruta em milissegundos.
 */

function gerarTokenRecuperacaoSenha(): string
{
    // NUNCA USE mt_rand() PARA TOKENS CRIPTOGRÁFICOS
    // Espaço de busca: apenas 10^6 (1 milhão de valores)
    return str_pad((string) mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function demo(): void
{
    echo "=== Gerador de Token VULNERÁVEL ===\n";
    for ($i = 0; $i < 5; $i++) {
        $token = gerarTokenRecuperacaoSenha();
        echo "Token {$i}: {$token} (tamanho: " . strlen($token) . ")\n";
    }
    echo "Espaço de busca: 10^6 = 1,000,000 tokens possiveis\n";
    echo "Quebravel por forca bruta em MILISSEGUNDOS\n";
}

if (debug_backtrace() === []) {
    demo();
}
