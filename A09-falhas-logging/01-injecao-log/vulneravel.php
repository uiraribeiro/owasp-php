<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Log Injection (CWE-117) - A09:2025 Security Logging and Alerting Failures
 *
 * A função grava entrada do usuário diretamente no log sem escapar quebras de linha.
 * Um atacante pode injetar \n seguido de linhas falsas que parecem eventos legítimos,
 * enganando administradores que analisam os logs, ou automatizando scripts de parsing.
 */

function registrarLog(string $mensagem, string $entradaUsuario): string {
    // VULNERÁVEL: escreve o input do usuário direto, permitindo injeção de quebras de linha
    return "[LOG] {$mensagem}: {$entradaUsuario}\n";
}

function demo(): void {
    echo "=== VULNERÁVEL: Log Injection (CWE-117) ===\n";

    $entradaAtacante = "joao\n[LOG] ADMIN_LOGIN_SUCESSO usuario=atacante ip=127.0.0.1";
    $logVulneravel = registrarLog("usuario tentou login", $entradaAtacante);

    echo "Log resultante:\n";
    echo "---\n";
    echo $logVulneravel;
    echo "---\n";
    echo "Observe: o atacante injetou uma quebra de linha e uma linha falsa de log que parece legítima (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
