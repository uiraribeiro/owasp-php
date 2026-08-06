<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Decisão de Design Correta para Armazenamento de Senhas
 *
 * A decisão arquitetural correta é usar uma FUNÇÃO DE HASH IRREVERSÍVEL (bcrypt, argon2, etc).
 * Dessa forma, a senha NUNCA pode ser recuperada, mesmo com acesso ao banco de dados.
 * A autenticação funciona por comparação de hashes, não por recuperação da senha original.
 */

function armazenarCredencial(string $senha): string {
    // CORRIGIDO: usa hash IRREVERSÍVEL via bcrypt
    // Não há função de decode; a verificação é feita com password_verify()
    return password_hash($senha, PASSWORD_BCRYPT);
}

function verificarCredencial(string $senhaFornecida, string $hashArmazenado): bool {
    return password_verify($senhaFornecida, $hashArmazenado);
}

function demo(): void {
    echo "=== CORRIGIDO: Armazenamento com hash irreversível ===\n";

    $senhaOriginal = "minha_senha_super_secreta_123";
    $hashArmazenado = armazenarCredencial($senhaOriginal);

    echo "Senha original: {$senhaOriginal}\n";
    echo "Hash armazenado no banco: {$hashArmazenado}\n";
    echo "Tamanho do hash faz reverter impraticável (além de criptograficamente impossível)\n";

    // Verificação legítima durante login
    $senhaFornecidaNoLogin = "minha_senha_super_secreta_123";
    if (verificarCredencial($senhaFornecidaNoLogin, $hashArmazenado)) {
        echo "Login bem-sucedido: senha foi verificada contra o hash (sem recuperação)\n";
    }

    // Tentativa com senha errada
    $senhaErrada = "senha_incorreta";
    if (!verificarCredencial($senhaErrada, $hashArmazenado)) {
        echo "Login rejeitado: hash não bate com a senha fornecida\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
