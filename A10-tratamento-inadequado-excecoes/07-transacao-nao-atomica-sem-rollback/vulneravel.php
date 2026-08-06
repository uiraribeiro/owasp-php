<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Transação Não-Atômica Sem Rollback (CWE-636) - A10:2025 Mishandling of Exceptional Conditions
 *
 * Uma transferência de dinheiro em duas etapas (débito + crédito) pode ser interrompida
 * entre o débito e o crédito, causando perda de dinheiro. Não há mecanismo de rollback
 * para restaurar o estado anterior em caso de falha parcial.
 */

function transferir(array $contas, string $origem, string $destino, float $valor, bool $interromperAposDebito): array {
    // Valida se há saldo
    if ($contas[$origem] < $valor) {
        return ['status' => 'erro', 'contas' => $contas];
    }

    // Etapa 1: Débito
    $contas[$origem] -= $valor;

    // VULNERÁVEL: pode falhar AQUI (interrupção simulada)
    // Sem rollback, o dinheiro fica perdido
    if ($interromperAposDebito) {
        return ['status' => 'interrompido', 'contas' => $contas];
    }

    // Etapa 2: Crédito (só alcançado se não houver interrupção)
    $contas[$destino] += $valor;

    return ['status' => 'sucesso', 'contas' => $contas];
}

function demo(): void {
    echo "=== VULNERÁVEL: Transação sem atomicidade/rollback ===\n";

    $contas = ['A' => 100.0, 'B' => 50.0];
    echo "Estado inicial: A=" . $contas['A'] . ", B=" . $contas['B'] . ", Total=" . ($contas['A'] + $contas['B']) . "\n";

    // Transação que é interrompida após débito
    $resultado = transferir($contas, 'A', 'B', 30.0, true);
    $total = $resultado['contas']['A'] + $resultado['contas']['B'];
    echo "Após interrupção: A=" . $resultado['contas']['A'] . ", B=" . $resultado['contas']['B'] . ", Total={$total}\n";
    echo "(PROBLEMA: Total era 150, agora é 120! Dinheiro desapareceu.)\n";
}

if (debug_backtrace() === []) {
    demo();
}
