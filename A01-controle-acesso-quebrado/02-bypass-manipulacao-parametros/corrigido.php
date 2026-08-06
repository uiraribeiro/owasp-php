<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validar Dados Críticos Sempre no Servidor
 *
 * O servidor mantém um catálogo de preços imutável e valida cada item
 * usando apenas o ID do produto e a quantidade enviada pelo cliente.
 * O preço vem sempre do catálogo do servidor, nunca do cliente.
 */

// Catálogo de preços do servidor (imutável)
const CATALOGO = [
    1 => 100.0,
    2 => 50.0,
    3 => 25.5,
];

function calcularTotalCompra(array $carrinho): float {
    $total = 0.0;

    // CORRIGIDO: valida apenas produto_id e qtd; busca preço no catálogo
    foreach ($carrinho as $item) {
        if (!isset($item['produto_id'], $item['qtd'])) {
            continue;
        }

        $idProduto = $item['produto_id'];

        // Verifica se produto existe no catálogo
        if (!isset(CATALOGO[$idProduto])) {
            continue;
        }

        // Usa preço do servidor, NUNCA do cliente
        $precoReal = CATALOGO[$idProduto];
        $qtd = (int) $item['qtd'];

        if ($qtd > 0) {
            $total += $precoReal * $qtd;
        }
    }

    return $total;
}

function demo(): void {
    echo "=== CORRIGIDO: Preço Validado no Servidor ===\n";

    $carrinhoLegitimo = [
        ['produto_id' => 1, 'qtd' => 2],
        ['produto_id' => 2, 'qtd' => 1],
    ];

    $carrinhoFraudado = [
        ['produto_id' => 1, 'preco' => 0.01, 'qtd' => 2],  // Preço ignorado
        ['produto_id' => 2, 'preco' => 0.01, 'qtd' => 1],  // Preço ignorado
    ];

    echo "Carrinho legítimo total: R$ " . number_format(calcularTotalCompra($carrinhoLegitimo), 2, ',', '.') . "\n";
    echo "Carrinho fraudado (preço ignorado) total: R$ " . number_format(calcularTotalCompra($carrinhoFraudado), 2, ',', '.') . " (bloqueado!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
