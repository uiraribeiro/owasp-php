<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Ausência de Caso Default Falha Aberta (CWE-478, CWE-636) - A10:2025 Mishandling of Exceptional Conditions
 *
 * Switch sem caso default com valor inicial permissivo (fail-open).
 * Qualquer status novo ou inesperado retorna TRUE, concedendo acesso
 * a uma conta em estado desconhecido (comportamento perigoso).
 */

function temAcesso(string $statusConta): bool {
    // VULNERÁVEL: valor inicial permissivo (acesso PERMITIDO por padrão)
    $acesso = true;

    switch ($statusConta) {
        case 'bloqueada':
            $acesso = false;
            break;
        case 'banida':
            $acesso = false;
            break;
        // SEM default: qualquer status novo/inesperado mantém $acesso = true (fail-open)
    }

    return $acesso;
}

function demo(): void {
    echo "=== VULNERÁVEL: Switch sem default (fail-open) ===\n";

    // Casos conhecidos
    echo "Status 'bloqueada': " . (temAcesso('bloqueada') ? 'permitido' : 'negado') . "\n";
    echo "Status 'ativa': " . (temAcesso('ativa') ? 'permitido' : 'negado') . "\n";

    // Caso novo/inesperado
    $statusNovoDesconhecido = 'suspensa_por_erro_de_sistema';
    $acesso = temAcesso($statusNovoDesconhecido);
    echo "Status '{$statusNovoDesconhecido}': " . ($acesso ? 'permitido' : 'negado') . "\n";
    echo "(PROBLEMA: acesso PERMITIDO a conta em estado desconhecido! Fail-open perigoso)\n";
}

if (debug_backtrace() === []) {
    demo();
}
