<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Transação com Rollback em Caso de Falha
 *
 * Se alguma etapa da transação falha, o estado anterior é restaurado.
 * Assim, ou a transferência completa ou nada muda (atomicidade).
 */

function transferir(array $contas, string $origem, string $destino, float $valor, bool $interromperAposDebito): array {
    // Valida se há saldo
    if ($contas[$origem] < $valor) {
        return ['status' => 'erro', 'contas' => $contas];
    }

    // Salva estado anterior (para rollback)
    $saldoOrigemAntes = $contas[$origem];

    // Etapa 1: Débito
    $contas[$origem] -= $valor;

    // CORRIGIDO: se falha aqui, faz rollback do débito
    if ($interromperAposDebito) {
        $contas[$origem] = $saldoOrigemAntes; // Rollback: restaura saldo original
        return ['status' => 'erro', 'contas' => $contas, 'mensagem' => 'transacao revertida'];
    }

    // Etapa 2: Crédito (agora seguro, pois se chegar aqui vai completar)
    $contas[$destino] += $valor;

    return ['status' => 'sucesso', 'contas' => $contas];
}

function demo(): void {
    echo "=== CORRIGIDO: Transação com rollback em caso de falha ===\n";

    $contas = ['A' => 100.0, 'B' => 50.0];
    echo "Estado inicial: A=" . $contas['A'] . ", B=" . $contas['B'] . ", Total=" . ($contas['A'] + $contas['B']) . "\n";

    // Transação que é interrompida após débito
    $resultado = transferir($contas, 'A', 'B', 30.0, true);
    $total = $resultado['contas']['A'] + $resultado['contas']['B'];
    echo "Após interrupção (com rollback): A=" . $resultado['contas']['A'] . ", B=" . $resultado['contas']['B'] . ", Total={$total}\n";
    echo "(OK: Total continua 150! Rollback restaurou o estado.)\n";
}

if (debug_backtrace() === []) {
    demo();
}
