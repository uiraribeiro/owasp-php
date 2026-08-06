<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validação e Tratamento de Exceções
 *
 * A função valida entrada, trata exceções possíveis
 * e sempre retorna um resultado seguro e controlado.
 * Nunca deixa exceções não tratadas escaparem.
 */

function processarPedido(array $pedido): array {
    try {
        // Extrai e valida a quantidade
        $quantidade = $pedido['quantidade'] ?? 0;

        // Validação explícita
        if ($quantidade <= 0) {
            throw new \InvalidArgumentException('quantidade invalida');
        }

        // Operação segura agora que quantidade foi validado
        $precoUnitario = 100 / $quantidade;
        return ['status' => 'ok', 'preco_unitario' => $precoUnitario];
    } catch (\Throwable $e) {
        // CORRIGIDO: todas as exceções são capturadas
        // Função sempre retorna um resultado estruturado
        return ['status' => 'erro', 'mensagem' => 'Nao foi possivel processar o pedido.'];
    }
}

function demo(): void {
    echo "=== CORRIGIDO: Tratamento robusto de exceções ===\n";

    // Caso legítimo
    echo "Processando pedido legítimo...\n";
    $resultado = processarPedido(['quantidade' => 4]);
    echo "Resultado: " . json_encode($resultado) . "\n";

    // Caso que seria exceção - agora tratado
    echo "\nProcessando pedido com quantidade zero (tratado graciosamente)...\n";
    $resultado = processarPedido(['quantidade' => 0]);
    echo "Resultado: " . json_encode($resultado) . "\n";
    echo "(OK: Exceção capturada e convertida em resposta estruturada)\n";
}

if (debug_backtrace() === []) {
    demo();
}
