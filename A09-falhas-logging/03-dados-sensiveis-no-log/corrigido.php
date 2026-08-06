<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Redattar dados sensíveis nos logs
 *
 * Nunca grava senhas, tokens, chaves ou dados privados em texto puro.
 * Usa placeholders como [REDACTED] ou registra apenas um hash não-revertível.
 */

function registrarRequisicaoLogin(string $usuario, string $senha): string {
    // CORRIGIDO: substitui a senha por um marcador seguro
    return "login usuario={$usuario} senha=[REDACTED]\n";
}

function demo(): void {
    echo "=== CORRIGIDO: Dados sensíveis redatados ===\n";

    $logCorrigido = registrarRequisicaoLogin('joao', 'MinhaSenhaSecreta123');

    echo "Log resultante:\n";
    echo "---\n";
    echo $logCorrigido;
    echo "---\n";
    echo "Seguro! O nome de usuário fica para auditoria, mas a senha é ocultada.\n";
}

if (debug_backtrace() === []) {
    demo();
}
