<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Bypass por Manipulação de Parâmetros (A01:2025 - Broken Access Control)
 *
 * O servidor confia no campo "preco" enviado pelo cliente no carrinho.
 * Um atacante pode reduzir o preço de um produto para valores muito baixos
 * ou até zero, realizando compras fraudulentas.
 */

function calcularTotalCompra(array $carrinho): float {
    $total = 0.0;

    // VULNERÁVEL: confia no campo 'preco' vindo do cliente
    foreach ($carrinho as $item) {
        if (isset($item['preco']) && isset($item['qtd'])) {
            $total += $item['preco'] * $item['qtd'];
        }
    }

    return $total;
}

function demo(): void {
    echo "=== VULNERÁVEL: Manipulação de Parâmetros (Preço) ===\n";

    $carrinhoLegitimo = [
        ['produto_id' => 1, 'preco' => 100.0, 'qtd' => 2],
        ['produto_id' => 2, 'preco' => 50.0, 'qtd' => 1],
    ];

    $carrinhoFraudado = [
        ['produto_id' => 1, 'preco' => 0.01, 'qtd' => 2],  // Atacante mudou para 0.01
        ['produto_id' => 2, 'preco' => 0.01, 'qtd' => 1],  // Deveria ser 50.0
    ];

    echo "Carrinho legítimo total: R$ " . number_format(calcularTotalCompra($carrinhoLegitimo), 2, ',', '.') . "\n";
    echo "Carrinho fraudado total: R$ " . number_format(calcularTotalCompra($carrinhoFraudado), 2, ',', '.') . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
