<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Falha Não-Segura com Fail-Open (CWE-636) - A10:2025 Mishandling of Exceptional Conditions
 *
 * Quando um serviço externo de verificação de permissão falha (retorna null),
 * a função assume acesso PERMITIDO por padrão (fail-open perigoso).
 * Qualquer falha no serviço de checagem resulta em acesso indevido.
 */

function verificarPermissao(?bool $resultadoChecagemExterna): bool {
    // VULNERÁVEL: se serviço falha (null), retorna true (acesso PERMITIDO)
    // Fail-open perigoso: assume que está tudo ok quando não consegue verificar
    return $resultadoChecagemExterna ?? true;
}

function demo(): void {
    echo "=== VULNERÁVEL: Falha não-segura com fail-open ===\n";

    // Serviço respondendo normalmente
    echo "Serviço responde true: " . (verificarPermissao(true) ? 'permitido' : 'negado') . "\n";
    echo "Serviço responde false: " . (verificarPermissao(false) ? 'permitido' : 'negado') . "\n";

    // Serviço falha (null)
    echo "Serviço falha (null): " . (verificarPermissao(null) ? 'permitido' : 'negado') . "\n";
    echo "(PROBLEMA: acesso PERMITIDO mesmo quando serviço falhou!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
