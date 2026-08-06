<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Escapar caracteres de quebra de linha em dados de entrada
 *
 * Neutraliza \r e \n do input antes de gravar no log, garantindo que
 * nenhuma entrada do usuário possa criar múltiplas "linhas de log" falsas.
 */

function registrarLog(string $mensagem, string $entradaUsuario): string {
    // CORRIGIDO: escapa caracteres de quebra de linha
    $entradaEscapada = str_replace(["\r", "\n"], ['\\r', '\\n'], $entradaUsuario);
    return "[LOG] {$mensagem}: {$entradaEscapada}\n";
}

function demo(): void {
    echo "=== CORRIGIDO: Log Injection com escape de quebras de linha ===\n";

    $entradaAtacante = "joao\n[LOG] ADMIN_LOGIN_SUCESSO usuario=atacante ip=127.0.0.1";
    $logCorrigido = registrarLog("usuario tentou login", $entradaAtacante);

    echo "Log resultante:\n";
    echo "---\n";
    echo $logCorrigido;
    echo "---\n";
    echo "Observe: a injeção foi neutralizada. A quebra de linha real foi escapada para o literal \\n (seguro!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
