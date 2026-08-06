<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Exceção Não Tratada - A10:2025 Mishandling of Exceptional Conditions
 *
 * A função tenta processar um pedido sem validar dados de entrada.
 * Se `quantidade` for 0, DivisionByZeroError é lançado SEM tratamento,
 * causando crash não controlado e possível negação de serviço.
 */

function processarPedido(array $pedido): array {
    // VULNERÁVEL: nenhuma validação, nenhum try/catch
    // Se quantidade for 0, ocorre divisão por zero não tratada
    $precoUnitario = 100 / $pedido['quantidade'];
    return ['status' => 'ok', 'preco_unitario' => $precoUnitario];
}

function demo(): void {
    echo "=== VULNERÁVEL: Exceção não tratada ===\n";

    // Caso legítimo
    echo "Processando pedido legítimo...\n";
    $resultado = processarPedido(['quantidade' => 4]);
    echo "Resultado: " . json_encode($resultado) . "\n";

    // Caso que causa exceção não tratada
    echo "\nProcessando pedido com quantidade zero (vai causar erro)...\n";
    try {
        $resultado = processarPedido(['quantidade' => 0]);
        echo "Resultado: " . json_encode($resultado) . "\n";
    } catch (\Throwable $e) {
        echo "ERRO NÃO TRATADO NA FUNÇÃO: " . $e::class . " - " . $e->getMessage() . "\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
