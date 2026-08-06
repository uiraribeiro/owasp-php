<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Sempre Validar no Servidor, Não Confiar no Cliente
 *
 * O servidor SEMPRE valida regras críticas (limites, saldo) independente
 * do que o cliente disser. A validação do cliente é só para UX, não para segurança.
 */

// Saldo simulado do usuário (seria consultado de um banco real)
const SALDO_USUARIO = 1500.0;
const LIMITE_TRANSFERENCIA = 2000.0;

function processarTransferencia(array $dadosFormulario): array {
    $valor = (float) ($dadosFormulario['valor'] ?? 0.0);

    // CORRIGIDO: SEMPRE valida no servidor, ignora qualquer flag do cliente
    // Validação 1: valor positivo
    if ($valor <= 0.0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Valor deve ser maior que zero',
            'valor_processado' => 0.0,
        ];
    }

    // Validação 2: dentro do limite de transferência
    if ($valor > LIMITE_TRANSFERENCIA) {
        return [
            'sucesso' => false,
            'mensagem' => "Limite de transferência excedido (máximo R$ " . LIMITE_TRANSFERENCIA . ")",
            'valor_processado' => 0.0,
        ];
    }

    // Validação 3: saldo disponível
    if ($valor > SALDO_USUARIO) {
        return [
            'sucesso' => false,
            'mensagem' => 'Saldo insuficiente',
            'valor_processado' => 0.0,
        ];
    }

    // Passou em todas as validações do servidor
    return [
        'sucesso' => true,
        'mensagem' => "Transferência de R$ {$valor} processada com sucesso",
        'valor_processado' => $valor,
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Validação no Servidor ===\n";

    $transferenciaMaliciosa = ['valor' => 999999.0, 'limiteVerificadoPeloClient' => true];
    $resultado = processarTransferencia($transferenciaMaliciosa);

    echo "Transferência maliciosa: " . json_encode($resultado) . " (bloqueado!)\n";

    $transferenciaLegitima = ['valor' => 500.0];
    $resultado2 = processarTransferencia($transferenciaLegitima);
    echo "Transferência legítima: " . json_encode($resultado2) . " (permitida)\n";
}

if (debug_backtrace() === []) {
    demo();
}
