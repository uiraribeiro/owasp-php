<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Falta de Mecanismo de Alerta e Detecção de Anomalias - A09:2025 Security Logging and Alerting Failures
 *
 * Não existe nenhum sistema de monitoramento que analisa padrões suspeitos.
 * Ataque de força bruta em andamento não dispara nenhum alerta.
 * Administrador fica cego até que o dano já foi feito.
 */

function avaliarEventosSeguranca(array $timestampsLoginFalhou, int $agora): array {
    // VULNERÁVEL: NUNCA dispara alerta, não importa quantas tentativas de login falharam
    return [
        'alerta_disparado' => false,
        'motivo' => 'monitoramento nao implementado'
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem alerta de anomalia ===\n";

    // Simular 15 tentativas de login falho nos últimos 60 segundos
    $agora = 1700000000;
    $timestampsAtaque = [];
    for ($i = 1; $i <= 15; $i++) {
        $timestampsAtaque[] = $agora - (50 - $i);
    }

    $alerta = avaliarEventosSeguranca($timestampsAtaque, $agora);

    echo "Tentativas de login falho: " . count($timestampsAtaque) . "\n";
    echo "Alerta disparado? " . ($alerta['alerta_disparado'] ? 'SIM' : 'NÃO') . "\n";
    echo "Motivo: " . $alerta['motivo'] . "\n";
    echo "PROBLEMA: ataque de força bruta passa completamente despercebido!\n";
}

if (debug_backtrace() === []) {
    demo();
}
