<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Falha Segura com Fail-Safe (Fail-Closed)
 *
 * Quando um serviço externo de verificação falha (retorna null),
 * a função assume acesso NEGADO por padrão (fail-safe).
 * Qualquer falha no serviço bloqueia acesso até confirmação.
 */

function verificarPermissao(?bool $resultadoChecagemExterna): bool {
    // CORRIGIDO: se serviço falha (null), retorna false (acesso NEGADO)
    // Fail-safe/fail-closed: nega acesso quando não consegue verificar
    // Só permite quando obtém confirmação explícita
    return $resultadoChecagemExterna ?? false;
}

function demo(): void {
    echo "=== CORRIGIDO: Falha segura com fail-safe ===\n";

    // Serviço respondendo normalmente
    echo "Serviço responde true: " . (verificarPermissao(true) ? 'permitido' : 'negado') . "\n";
    echo "Serviço responde false: " . (verificarPermissao(false) ? 'permitido' : 'negado') . "\n";

    // Serviço falha (null)
    echo "Serviço falha (null): " . (verificarPermissao(null) ? 'permitido' : 'negado') . "\n";
    echo "(OK: acesso NEGADO até conseguir verificar com segurança)\n";
}

if (debug_backtrace() === []) {
    demo();
}
