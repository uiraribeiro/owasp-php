<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Perda de Informação Relevante (CWE-221/223) - A09:2025 Security Logging and Alerting Failures
 *
 * O log não registra detalhes críticos para investigação: QUEM tentou, DE ONDE, e QUANDO.
 * Sem essas informações, é impossível investigar mais tarde quem foi o atacante
 * ou reconstruir uma sequência de eventos suspeitos.
 */

function registrarTentativaLogin(string $usuario, string $enderecoIp, int $timestamp, bool $sucesso): string {
    // VULNERÁVEL: perde informações críticas (usuário, IP, timestamp)
    return $sucesso ? "Login ok\n" : "Login falhou\n";
}

function demo(): void {
    echo "=== VULNERÁVEL: Log sem informações relevantes ===\n";

    $log1 = registrarTentativaLogin('joao.silva', '203.0.113.42', 1700000000, false);
    $log2 = registrarTentativaLogin('maria.santos', '198.51.100.105', 1700000060, true);

    echo "Logs resultantes:\n";
    echo "---\n";
    echo $log1;
    echo $log2;
    echo "---\n";
    echo "Problema: não sabemos QUEM tentou, DE ONDE, nem QUANDO. Impossível investigar depois!\n";
}

if (debug_backtrace() === []) {
    demo();
}
