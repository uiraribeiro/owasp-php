<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Hash de senhas com salt e algoritmo seguro (bcrypt)
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: password_hash() com PASSWORD_BCRYPT usa salt aleatório
 * e fator de custo adaptativo. Cada hash é único mesmo para mesma senha.
 */

function hashSenha(string $senha): string
{
    // USE password_hash() COM PASSWORD_BCRYPT!
    // - Incorpora salt aleatório automaticamente
    // - Custo adaptativo (fica mais lento com o tempo)
    // - Cada hash é único mesmo para mesma senha
    // - Praticamente impossível fazer rainbow tables
    return password_hash($senha, PASSWORD_BCRYPT);
}

function verificarSenha(string $senha, string $hash): bool
{
    // USE password_verify() PARA VERIFICAÇÃO!
    // - Valida automaticamente sem expor implementação
    return password_verify($senha, $hash);
}

function demo(): void
{
    echo "=== Hash de Senha SEGURA (bcrypt) ===\n";
    $senha = '123456';
    $hash1 = hashSenha($senha);
    $hash2 = hashSenha($senha);

    echo "Senha: {$senha}\n";
    echo "Hash 1: {$hash1}\n";
    echo "Hash 2: {$hash2}\n";
    echo "Hashes identicos? " . ($hash1 === $hash2 ? 'SIM (BUG!)' : 'NAO (correto, salt aleatório)') . "\n";

    echo "\nVerificação com password_verify():\n";
    echo "Hash 1 bate com senha? " . (verificarSenha($senha, $hash1) ? 'SIM' : 'NAO') . "\n";
    echo "Hash 2 bate com senha? " . (verificarSenha($senha, $hash2) ? 'SIM' : 'NAO') . "\n";
    echo "\nCada hash é único, impossível rainbow tables.\n";
    echo "Custo adaptativo: fica mais lento com o tempo.\n";
}

if (debug_backtrace() === []) {
    demo();
}
