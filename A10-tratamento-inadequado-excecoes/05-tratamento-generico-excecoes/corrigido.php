<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Tratamento Específico de Diferentes Tipos de Exceção
 *
 * Cada tipo de exceção é capturado e tratado de forma apropriada,
 * permitindo decisões corretas (retry, bloqueio, etc).
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
    } catch (SaldoInsuficienteException $e) {
        // CORRIGIDO: trata falta de saldo especificamente
        // (não deve fazer retry automático)
        return ['status' => 'recusado', 'motivo' => 'saldo insuficiente'];
    } catch (ErroDeConexaoException $e) {
        // CORRIGIDO: trata erro de rede especificamente
        // (pode tentar novamente após delay)
        return ['status' => 'tentar_novamente', 'motivo' => 'falha temporaria de rede'];
    }
}

function demo(): void {
    echo "=== CORRIGIDO: Tratamento específico de cada tipo de exceção ===\n";

    // Caso 1: Erro de conexão (temporário)
    $r1 = processarPagamento(50.0, 100.0, true);
    echo "Erro de conexão retorna: status=" . $r1['status'] . " (pode fazer retry)\n";

    // Caso 2: Saldo insuficiente (permanente)
    $r2 = processarPagamento(150.0, 100.0, false);
    echo "Saldo insuficiente retorna: status=" . $r2['status'] . " (não faz retry)\n";

    echo "(OK: cada situação tem tratamento apropriado)\n";
}

if (debug_backtrace() === []) {
    demo();
}
