<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Caso Default Seguro com Fail-Safe
 *
 * Switch com caso default explícito que NEGA acesso para
 * qualquer status desconhecido (fail-safe/fail-closed).
 * Valor inicial também negativo, mais seguro.
 */

function temAcesso(string $statusConta): bool {
    // CORRIGIDO: lógica clara, com default negativo (fail-safe)
    switch ($statusConta) {
        case 'ativa':
            return true;
        case 'bloqueada':
        case 'banida':
            return false;
        default:
            // Qualquer status desconhecido NEGA acesso por padrão (fail-safe)
            return false;
    }
}

function demo(): void {
    echo "=== CORRIGIDO: Switch com default seguro (fail-safe) ===\n";

    // Casos conhecidos
    echo "Status 'bloqueada': " . (temAcesso('bloqueada') ? 'permitido' : 'negado') . "\n";
    echo "Status 'ativa': " . (temAcesso('ativa') ? 'permitido' : 'negado') . "\n";

    // Caso novo/inesperado
    $statusNovoDesconhecido = 'suspensa_por_erro_de_sistema';
    $acesso = temAcesso($statusNovoDesconhecido);
    echo "Status '{$statusNovoDesconhecido}': " . ($acesso ? 'permitido' : 'negado') . "\n";
    echo "(OK: acesso NEGADO a conta em estado desconhecido. Fail-safe seguro)\n";
}

if (debug_backtrace() === []) {
    demo();
}
