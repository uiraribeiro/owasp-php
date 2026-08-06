<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Incluir contexto completo nos logs de segurança
 *
 * Registra timestamp, usuário, IP e resultado para permitir investigação completa
 * de sequências de eventos suspeitos, reconstruindo a timeline de um ataque.
 */

function registrarTentativaLogin(string $usuario, string $enderecoIp, int $timestamp, bool $sucesso): string {
    // CORRIGIDO: inclui todas as informações críticas para investigação
    return sprintf("[%d] usuario=%s ip=%s resultado=%s\n", $timestamp, $usuario, $enderecoIp, $sucesso ? 'sucesso' : 'falha');
}

function demo(): void {
    echo "=== CORRIGIDO: Log com informações completas ===\n";

    $log1 = registrarTentativaLogin('joao.silva', '203.0.113.42', 1700000000, false);
    $log2 = registrarTentativaLogin('maria.santos', '198.51.100.105', 1700000060, true);

    echo "Logs resultantes:\n";
    echo "---\n";
    echo $log1;
    echo $log2;
    echo "---\n";
    echo "Agora temos timestamp, usuário, IP e resultado. Possível investigar completamente!\n";
}

if (debug_backtrace() === []) {
    demo();
}
