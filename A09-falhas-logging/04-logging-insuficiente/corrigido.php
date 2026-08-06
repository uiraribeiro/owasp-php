<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Registrar eventos críticos de segurança
 *
 * Define uma lista de eventos de segurança relevantes e garante que todos eles
 * sejam capturados. Dessa forma, tentativas de login falhas, elevações de privilégio
 * e outras ações suspeitas ficam registradas para investigação.
 */

function tratarEventoSeguranca(string $tipoEvento, array $logsAcumulados): array {
    // CORRIGIDO: define eventos críticos para segurança e os registra
    $eventosDeSeguranca = [
        'login_falhou',
        'login_sucesso',
        'senha_alterada',
        'permissao_elevada',
        'usuario_excluido'
    ];

    if (in_array($tipoEvento, $eventosDeSeguranca, true)) {
        $logsAcumulados[] = $tipoEvento;
    }

    return $logsAcumulados;
}

function demo(): void {
    echo "=== CORRIGIDO: Logging de eventos críticos de segurança ===\n";

    $logs = [];
    $logs = tratarEventoSeguranca('login_falhou', $logs);
    $logs = tratarEventoSeguranca('login_falhou', $logs);
    $logs = tratarEventoSeguranca('permissao_elevada', $logs);
    $logs = tratarEventoSeguranca('usuario_excluido', $logs);
    $logs = tratarEventoSeguranca('pagina_visitada', $logs);

    echo "Total de eventos capturados: " . count($logs) . "\n";
    echo "Eventos no log: " . json_encode($logs) . "\n";
    echo "Agora todos os eventos críticos foram capturados. Possível investigar!\n";
}

if (debug_backtrace() === []) {
    demo();
}
