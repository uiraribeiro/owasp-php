<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Dados Sensíveis Gravados no Log (CWE-532) - A09:2025 Security Logging and Alerting Failures
 *
 * Senhas, tokens, chaves e outros dados sensíveis são gravados em TEXTO PURO no log.
 * Qualquer pessoa com acesso ao arquivo/sistema de logs (administrador, terceiro,
 * atacante que conseguiu acesso ao servidor) vê todas as credenciais em texto claro.
 */

function registrarRequisicaoLogin(string $usuario, string $senha): string {
    // VULNERÁVEL: grava a senha em texto puro, vazamento garantido
    return "login usuario={$usuario} senha={$senha}\n";
}

function demo(): void {
    echo "=== VULNERÁVEL: Dados sensíveis em texto puro no log ===\n";

    $logVulneravel = registrarRequisicaoLogin('joao', 'MinhaSenhaSecreta123');

    echo "Log resultante:\n";
    echo "---\n";
    echo $logVulneravel;
    echo "---\n";
    echo "PROBLEMA GRAVE: a senha está em texto puro. Qualquer acesso aos logs = vazamento de credenciais!\n";
}

if (debug_backtrace() === []) {
    demo();
}
