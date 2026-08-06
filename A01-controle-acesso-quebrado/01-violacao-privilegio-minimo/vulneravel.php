<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Violação do Princípio do Privilégio Mínimo (A01:2025 - Broken Access Control)
 *
 * O código usa uma abordagem "deny-open" (nega apenas guest), permitindo que
 * usuários comuns ('user') executem ações administrativas como exclusão de relatórios.
 * Deveria usar "allow-list" explícita (apenas 'admin' pode excluir).
 */

function podeExcluirRelatorio(array $usuario): bool {
    // VULNERÁVEL: nega apenas 'guest', mas permite qualquer outra role
    return $usuario['role'] !== 'guest';
}

function demo(): void {
    echo "=== VULNERÁVEL: Violação do Privilégio Mínimo ===\n";

    $usuarioGuest = ['role' => 'guest'];
    $usuarioComum = ['role' => 'user'];
    $usuarioAdmin = ['role' => 'admin'];

    echo "Guest pode excluir? " . (podeExcluirRelatorio($usuarioGuest) ? 'SIM' : 'NÃO') . "\n";
    echo "User comum pode excluir? " . (podeExcluirRelatorio($usuarioComum) ? 'SIM' : 'NÃO') . " (PROBLEMA!)\n";
    echo "Admin pode excluir? " . (podeExcluirRelatorio($usuarioAdmin) ? 'SIM' : 'NÃO') . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
