<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Hash de senhas sem salt e com algoritmo fraco (MD5)
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: MD5 é rápido demais (permite força bruta) e sem salt.
 * Mesmo hash para mesma senha sempre. Vulnerável a rainbow tables.
 * Exemplo: md5("123456") é sempre "e10adc3949ba59abbe56e057f20f883e"
 */

function hashSenha(string $senha): string
{
    // NUNCA USE MD5 PARA SENHAS!
    // - Extremamente rápido (miliões de hashes por segundo)
    // - Sem salt (determinístico)
    // - Conhecido/crackável (rainbow tables amplamente disponíveis)
    return md5($senha);
}

function verificarSenha(string $senha, string $hash): bool
{
    // Comparação simples (sem salt)
    return md5($senha) === $hash;
}

function demo(): void
{
    echo "=== Hash de Senha VULNERÁVEL (MD5) ===\n";
    $senha = '123456';
    $hash1 = hashSenha($senha);
    $hash2 = hashSenha($senha);

    echo "Senha: {$senha}\n";
    echo "Hash 1: {$hash1}\n";
    echo "Hash 2: {$hash2}\n";
    echo "Hashes identicos? " . ($hash1 === $hash2 ? 'SIM (falta salt)' : 'NAO') . "\n";
    echo "\nEste hash de '123456' é AMPLAMENTE CONHECIDO:\n";
    echo "e10adc3949ba59abbe56e057f20f883e\n";
    echo "\nCrackável em MILISSEGUNDOS com rainbow tables.\n";
}

if (debug_backtrace() === []) {
    demo();
}
