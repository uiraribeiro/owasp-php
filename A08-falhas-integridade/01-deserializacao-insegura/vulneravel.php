<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Desserialização Insegura - A08:2025 Software or Data Integrity Failures
 *
 * O sistema usa unserialize() em dados vindo de fora (cookies, uploads, APIs).
 * Um atacante pode construir manualmente uma string serializada com um objeto
 * malicioso que dispara __wakeup() ou __destruct(), causando object injection.
 */

class ExecutorPerigoso {
    public string $comandoRegistrado = '';
    public function __wakeup(): void {
        global $comandoCapturadoViaWakeup;
        $comandoCapturadoViaWakeup = $this->comandoRegistrado;
    }
}

function carregarPreferenciasUsuario(string $dadosSerializados): mixed {
    // VULNERÁVEL: desserializa dados não confiáveis diretamente
    return unserialize($dadosSerializados);
}

function demo(): void {
    echo "=== VULNERÁVEL: Desserialização Insegura ===\n";

    $executor = new ExecutorPerigoso();
    $executor->comandoRegistrado = 'echo "Comando do atacante executado!";';
    $payloadSerializado = serialize($executor);

    echo "Payload serializado: " . bin2hex($payloadSerializado) . "\n";

    global $comandoCapturadoViaWakeup;
    $comandoCapturadoViaWakeup = null;

    $objetoDesserializado = carregarPreferenciasUsuario($payloadSerializado);

    if ($comandoCapturadoViaWakeup !== null) {
        echo "PROBLEMA! Objeto foi instanciado e __wakeup() foi disparado!\n";
        echo "Comando capturado: {$comandoCapturadoViaWakeup}\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
