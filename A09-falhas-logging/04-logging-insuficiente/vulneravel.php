<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Logging Insuficiente (CWE-778) - A09:2025 Security Logging and Alerting Failures
 *
 * Apenas eventos irrelevantes são registrados. Eventos críticos para segurança
 * (login falhou, senha alterada, permissão elevada, usuário excluído) são IGNORADOS.
 * Resultado: violações de dados ficam sem detecção por meses ou anos.
 */

function tratarEventoSeguranca(string $tipoEvento, array $logsAcumulados): array {
    // VULNERÁVEL: só "loga" um evento totalmente irrelevante para segurança
    if ($tipoEvento === 'pagina_visitada') {
        $logsAcumulados[] = $tipoEvento;
    }
    // Eventos críticos como 'login_falhou', 'senha_alterada', 'permissao_elevada', 'usuario_excluido'
    // são COMPLETAMENTE IGNORADOS
    return $logsAcumulados;
}

function demo(): void {
    echo "=== VULNERÁVEL: Logging insuficiente (eventos críticos ignorados) ===\n";

    $logs = [];
    $logs = tratarEventoSeguranca('login_falhou', $logs);
    $logs = tratarEventoSeguranca('login_falhou', $logs);
    $logs = tratarEventoSeguranca('permissao_elevada', $logs);
    $logs = tratarEventoSeguranca('usuario_excluido', $logs);
    $logs = tratarEventoSeguranca('pagina_visitada', $logs);

    echo "Total de eventos capturados: " . count($logs) . "\n";
    echo "Eventos no log: " . json_encode($logs) . "\n";
    echo "PROBLEMA: eventos críticos de segurança foram ignorados! Impossível detectar invasão.\n";
}

if (debug_backtrace() === []) {
    demo();
}
