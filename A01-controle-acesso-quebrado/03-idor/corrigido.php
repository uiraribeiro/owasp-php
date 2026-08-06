<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Verificar Propriedade do Objeto
 *
 * Antes de retornar um recurso, o servidor verifica se o usuário
 * logado é realmente o dono daquele objeto.
 */

function buscarPedido(array $pedidos, int $idPedidoSolicitado, int $idUsuarioLogado): ?array {
    // CORRIGIDO: verifica se o pedido existe E se o usuário é o dono
    foreach ($pedidos as $pedido) {
        if ($pedido['id'] === $idPedidoSolicitado) {
            // Verifica propriedade
            if ($pedido['dono_id'] === $idUsuarioLogado) {
                return $pedido;
            }
            // Pedido existe mas não pertence a este usuário
            return null;
        }
    }
    return null;
}

function demo(): void {
    echo "=== CORRIGIDO: IDOR com verificação de propriedade ===\n";

    $pedidos = [
        ['id' => 1, 'dono_id' => 1, 'valor' => 100.0, 'descricao' => 'Pedido do usuário 1'],
        ['id' => 2, 'dono_id' => 2, 'valor' => 250.0, 'descricao' => 'Pedido do usuário 2'],
    ];

    $pedido = buscarPedido($pedidos, 2, 1);
    if ($pedido) {
        echo "Usuário 1 conseguiu acessar pedido de outro: " . json_encode($pedido) . "\n";
    } else {
        echo "Acesso negado (bloqueado!)\n";
    }

    $pedidoProprio = buscarPedido($pedidos, 1, 1);
    if ($pedidoProprio) {
        echo "Usuário 1 pode acessar seu próprio pedido (caso legítimo)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
