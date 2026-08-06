<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Usar JSON em lugar de unserialize()
 *
 * JSON nunca instancia objetos PHP arbitrários. Dados estruturados
 * vindo de fora são sempre desserializados como arrays associativos,
 * eliminando completamente a superfície de ataque de object injection.
 */

class ExecutorPerigoso {
    public string $comandoRegistrado = '';
    public function __wakeup(): void {
        global $comandoCapturadoViaWakeup;
        $comandoCapturadoViaWakeup = $this->comandoRegistrado;
    }
}

function carregarPreferenciasUsuario(string $dadosSerializados): mixed {
    // CORRIGIDO: usa JSON em lugar de unserialize()
    return json_decode($dadosSerializados, true);
}

function demo(): void {
    echo "=== CORRIGIDO: Desserialização com JSON (segura) ===\n";

    $dados = ['tema' => 'escuro', 'idioma' => 'pt-BR'];
    $jsonEncodado = json_encode($dados);

    echo "Dados JSON: {$jsonEncodado}\n";

    global $comandoCapturadoViaWakeup;
    $comandoCapturadoViaWakeup = null;

    $dadosDesserializados = carregarPreferenciasUsuario($jsonEncodado);

    echo "Dados desserializados: " . json_encode($dadosDesserializados) . "\n";

    if ($comandoCapturadoViaWakeup === null) {
        echo "OK! JSON não instancia nenhum objeto. Nenhum magic method foi disparado.\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
