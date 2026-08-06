<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Aplicar o Princípio do Privilégio Mínimo com Allow-List Explícita
 *
 * Usa abordagem "deny-closed" (nega por padrão, apenas 'admin' é liberado).
 * Qualquer nova role deve ser explicitamente adicionada à lista de permissões.
 */

function podeExcluirRelatorio(array $usuario): bool {
    // CORRIGIDO: allow-list explícita (apenas admin pode excluir)
    return $usuario['role'] === 'admin';
}

function demo(): void {
    echo "=== CORRIGIDO: Privilégio Mínimo com Allow-List ===\n";

    $usuarioGuest = ['role' => 'guest'];
    $usuarioComum = ['role' => 'user'];
    $usuarioAdmin = ['role' => 'admin'];

    echo "Guest pode excluir? " . (podeExcluirRelatorio($usuarioGuest) ? 'SIM' : 'NÃO') . "\n";
    echo "User comum pode excluir? " . (podeExcluirRelatorio($usuarioComum) ? 'SIM' : 'NÃO') . " (bloqueado!)\n";
    echo "Admin pode excluir? " . (podeExcluirRelatorio($usuarioAdmin) ? 'SIM' : 'NÃO') . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
