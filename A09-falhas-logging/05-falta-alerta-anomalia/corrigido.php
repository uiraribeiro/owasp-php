<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Implementar detecção de anomalias com sistema de alertas
 *
 * Define limites de tentativas falhas em uma janela de tempo.
 * Quando o padrão suspeito é detectado, dispara um alerta automático
 * para que o administrador investigue imediatamente.
 */

function avaliarEventosSeguranca(array $timestampsLoginFalhou, int $agora): array {
    // CORRIGIDO: implementa detecção de força bruta
    $janela = 60;  // janela de 60 segundos
    $limite = 10;  // máximo de 10 tentativas

    // Filtra tentativas dentro da janela
    $recentes = array_filter($timestampsLoginFalhou, function($timestamp) use ($agora, $janela) {
        return ($agora - $timestamp) <= $janela;
    });

    if (count($recentes) >= $limite) {
        return [
            'alerta_disparado' => true,
            'motivo' => 'possivel ataque de forca bruta: ' . count($recentes) . ' tentativas falhas nos ultimos ' . $janela . 's'
        ];
    }

    return [
        'alerta_disparado' => false,
        'motivo' => 'dentro do normal'
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Detecção de anomalias com alertas ===\n";

    // Simular 15 tentativas de login falho nos últimos 60 segundos (ATAQUE)
    $agora = 1700000000;
    $timestampsAtaque = [];
    for ($i = 1; $i <= 15; $i++) {
        $timestampsAtaque[] = $agora - (50 - $i);
    }

    $alerta = avaliarEventosSeguranca($timestampsAtaque, $agora);

    echo "Tentativas de login falho: " . count($timestampsAtaque) . "\n";
    echo "Alerta disparado? " . ($alerta['alerta_disparado'] ? 'SIM' : 'NÃO') . "\n";
    echo "Motivo: " . $alerta['motivo'] . "\n";
    echo "\n";

    // Simular 2 tentativas (NORMAL)
    $timestampsNormal = [$agora - 30, $agora - 10];
    $alertaNormal = avaliarEventosSeguranca($timestampsNormal, $agora);

    echo "Tentativas normais: " . count($timestampsNormal) . "\n";
    echo "Alerta disparado? " . ($alertaNormal['alerta_disparado'] ? 'SIM' : 'NÃO') . "\n";
    echo "Motivo: " . $alertaNormal['motivo'] . "\n";
    echo "Agora atacantes são detectados em tempo real!\n";
}

if (debug_backtrace() === []) {
    demo();
}
