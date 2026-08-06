<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Lógica de Negócio sem Limite (Cenário 2 - OWASP) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é a falta de LIMITES na lógica de negócio.
 * A arquitetura não define:
 * - Quantidade máxima de itens por requisição
 * - Exigência de depósito/pagamento antecipado para grandes quantidades
 * Um atacante pode reservar 10.000 ingressos de uma vez sem pagar nada,
 * esvaziando todo o estoque da próxima apresentação do cinema.
 */

function reservarIngressos(int $quantidade, bool $depositoPago): array {
    // VULNERÁVEL: sem limites
    // Aceita qualquer quantidade sem verificação
    // Não exige depósito
    // Falha de DESIGN na lógica de negócio
    return [
        'aprovado' => true,
        'motivo' => 'ok',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem limites na reserva de ingressos ===\n";

    // Atacante tenta reservar toda a sala de cinema
    $resultado = reservarIngressos(5000, false);

    if ($resultado['aprovado']) {
        echo "PROBLEMA: Reservou 5.000 ingressos SEM depósito e SEM limite!\n";
        echo "Falha de DESIGN: lógica de negócio não tem proteção (estoque esvaziado!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
