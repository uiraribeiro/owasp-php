<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Tratamento Genérico de Exceções Diferentes (CWE-396/397) - A10:2025 Mishandling of Exceptional Conditions
 *
 * Exceções de diferentes naturezas (falta de saldo vs. erro de rede) são capturadas
 * genericamente com o mesmo tratamento. Perde-se a informação crítica de qual foi o problema,
 * impedindo decisões apropriadas (retry automático, bloqueio, etc).
 */

class SaldoInsuficienteException extends \Exception {}
class ErroDeConexaoException extends \Exception {}

function processarPagamento(float $valor, float $saldoDisponivel, bool $simularErroDeConexao): array {
    try {
        // Simula erro de conexão (temporário, poderia tentar novamente)
        if ($simularErroDeConexao) {
            throw new ErroDeConexaoException('timeout na rede');
        }

        // Simula falta de saldo (permanente, não deve tentar automaticamente)
        if ($valor > $saldoDisponivel) {
            throw new SaldoInsuficienteException('saldo insuficiente');
        }

        return ['status' => 'aprovado'];
    } catch (\Exception $e) {
        // VULNERÁVEL: captura QUALQUER exception genericamente
        // Perdem-se as informações críticas sobre o tipo de erro
        // Não é possível diferenciar "retry" vs "falha permanente"
        return ['status' => 'erro'];
    }
}

function demo(): void {
    echo "=== VULNERÁVEL: Tratamento genérico de exceções diferentes ===\n";

    // Caso 1: Erro de conexão (temporário)
    $r1 = processarPagamento(50.0, 100.0, true);
    echo "Erro de conexão retorna: status=" . $r1['status'] . "\n";

    // Caso 2: Saldo insuficiente (permanente)
    $r2 = processarPagamento(150.0, 100.0, false);
    echo "Saldo insuficiente retorna: status=" . $r2['status'] . "\n";

    echo "(PROBLEMA: ambos retornam o MESMO status, diferença crítica se perdeu!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
