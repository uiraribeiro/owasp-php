<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Credenciais Padrão de Fábrica - A02:2025 Security Misconfiguration
 *
 * Sistema ainda aceita credenciais de fábrica hardcoded (admin/admin123).
 * Atacante pode fazer login com credenciais conhecidas sem necessidade de quebra de senha.
 */

function autenticarAdmin(string $usuario, string $senha): bool {
    // VULNERÁVEL: credenciais padrão de fábrica ainda ativas
    return $usuario === 'admin' && $senha === 'admin123';
}

function demo(): void {
    echo "=== VULNERÁVEL: Credenciais padrão de fábrica ===\n";

    if (autenticarAdmin('admin', 'admin123')) {
        echo "Login bem-sucedido com credenciais padrão (PROBLEMA!)\n";
    } else {
        echo "Login negado\n";
    }

    if (!autenticarAdmin('admin', 'senha_forte_personalizada')) {
        echo "Senha customizada é rejeitada (esperado, mas credencial padrão ainda funciona)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
