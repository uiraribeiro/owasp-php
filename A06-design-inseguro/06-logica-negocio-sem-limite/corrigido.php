<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Lógica de Negócio com Proteções
 *
 * A decisão de design correta estabelece LIMITES e REGRAS claras na lógica de negócio:
 * 1. Limite máximo de itens por requisição (ex: 4 ingressos)
 * 2. Para quantidades maiores, exigir depósito/pagamento antecipado
 * 3. Validação explícita de todas as precondições
 * Dessa forma, o atacante não consegue esvaziar o estoque sem pagar,
 * e as requisições legítimas funcionam normalmente.
 */

function reservarIngressos(int $quantidade, bool $depositoPago): array {
    // CORRIGIDO: lógica de negócio com proteções

    // Limite 1: Máximo de ingressos por requisição (sem depósito)
    $limiteSemDeposito = 4;

    // Validação: quantidade não pode ser negativa ou zero
    if ($quantidade <= 0) {
        return [
            'aprovado' => false,
            'motivo' => 'Quantidade deve ser positiva',
        ];
    }

    // Decisão: quantidade dentro do limite de conforto
    if ($quantidade <= $limiteSemDeposito) {
        return [
            'aprovado' => true,
            'motivo' => 'Reserva pequena aprovada (sem depósito exigido)',
        ];
    }

    // Quantidade grande exige depósito pago
    if ($quantidade > $limiteSemDeposito && $depositoPago === false) {
        return [
            'aprovado' => false,
            'motivo' => sprintf(
                'Quantidades maiores que %d exigem depósito pago antecipadamente',
                $limiteSemDeposito
            ),
        ];
    }

    // Grande quantidade com depósito pago é aprovada
    if ($quantidade > $limiteSemDeposito && $depositoPago === true) {
        return [
            'aprovado' => true,
            'motivo' => 'Reserva grande aprovada (depósito confirmado)',
        ];
    }

    return [
        'aprovado' => false,
        'motivo' => 'Erro na validação de reserva',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Lógica de negócio com proteções ===\n";

    // Teste 1: Pequena quantidade sem depósito (aprovada)
    $resultado1 = reservarIngressos(2, false);
    echo "Reserva de 2 ingressos sem depósito: ";
    echo $resultado1['aprovado'] ? "APROVADA" : "NEGADA";
    echo " ({$resultado1['motivo']})\n";

    // Teste 2: Grande quantidade sem depósito (negada)
    $resultado2 = reservarIngressos(5000, false);
    echo "Reserva de 5000 ingressos sem depósito: ";
    echo $resultado2['aprovado'] ? "APROVADA" : "NEGADA";
    echo " ({$resultado2['motivo']})\n";

    // Teste 3: Grande quantidade COM depósito (aprovada)
    $resultado3 = reservarIngressos(5000, true);
    echo "Reserva de 5000 ingressos COM depósito: ";
    echo $resultado3['aprovado'] ? "APROVADA" : "NEGADA";
    echo " ({$resultado3['motivo']})\n";
}

if (debug_backtrace() === []) {
    demo();
}
