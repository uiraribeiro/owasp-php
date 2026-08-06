<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Client-Side Enforcement (A01:2025 - Broken Access Control)
 *
 * O servidor confia em um campo enviado pelo cliente indicando que
 * a validação foi feita no frontend ("limiteVerificadoPeloClient").
 * Um atacante pode enviar valores maliciosos ignorando a validação do cliente.
 */

function processarTransferencia(array $dadosFormulario): array {
    $valor = $dadosFormulario['valor'] ?? 0.0;
    $limiteVerificadoPeloClient = $dadosFormulario['limiteVerificadoPeloClient'] ?? false;

    // VULNERÁVEL: se o cliente disse que validou, pula a validação no servidor
    if ($limiteVerificadoPeloClient === true) {
        return [
            'sucesso' => true,
            'mensagem' => "Transferência de R$ {$valor} processada com sucesso",
            'valor_processado' => $valor,
        ];
    }

    // Só valida no servidor se cliente não validou (raramente acontece)
    if ($valor > 1000.0) {
        return [
            'sucesso' => false,
            'mensagem' => 'Limite de transferência excedido',
            'valor_processado' => 0.0,
        ];
    }

    return [
        'sucesso' => true,
        'mensagem' => "Transferência de R$ {$valor} processada com sucesso",
        'valor_processado' => $valor,
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Client-Side Enforcement ===\n";

    $transferenciaMaliciosa = ['valor' => 999999.0, 'limiteVerificadoPeloClient' => true];
    $resultado = processarTransferencia($transferenciaMaliciosa);

    echo "Transferência maliciosa: " . json_encode($resultado) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
