<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Insecure Direct Object Reference (IDOR) - A01:2025 Broken Access Control
 *
 * O servidor retorna um pedido apenas verificando se o ID existe,
 * sem checar se o usuário logado é o dono do pedido.
 * Atacante pode enumerar IDs de pedidos alheios e acessar dados privados.
 */

function buscarPedido(array $pedidos, int $idPedidoSolicitado, int $idUsuarioLogado): ?array {
    // VULNERÁVEL: retorna o pedido apenas pelo ID, sem verificar propriedade
    foreach ($pedidos as $pedido) {
        if ($pedido['id'] === $idPedidoSolicitado) {
            return $pedido;
        }
    }
    return null;
}

function demo(): void {
    echo "=== VULNERÁVEL: IDOR (sem verificação de propriedade) ===\n";

    $pedidos = [
        ['id' => 1, 'dono_id' => 1, 'valor' => 100.0, 'descricao' => 'Pedido do usuário 1'],
        ['id' => 2, 'dono_id' => 2, 'valor' => 250.0, 'descricao' => 'Pedido do usuário 2'],
    ];

    $pedido = buscarPedido($pedidos, 2, 1);
    if ($pedido) {
        echo "Usuário 1 conseguiu acessar pedido de outro: " . json_encode($pedido) . " (PROBLEMA!)\n";
    } else {
        echo "Acesso negado\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
