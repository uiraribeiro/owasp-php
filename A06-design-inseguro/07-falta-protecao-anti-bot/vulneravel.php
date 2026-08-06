<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Falta de Proteção Anti-Bot (Cenário 3 - OWASP) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é a inexistência de proteção contra bots/scalpers.
 * A arquitetura não monitora:
 * - Frequência de requisições do mesmo cliente
 * - Padrões de comportamento suspeito (muitas tentativas em pouco tempo)
 * Um bot pode disparar centenas de requisições por segundo, comprando
 * todo o estoque antes que clientes legítimos tenham chance.
 */

function processarCompra(string $idCliente, int $quantidade, array $historicoRequisicoesRecentes): array {
    // VULNERÁVEL: sem proteção contra bots
    // Aceita qualquer requisição sem verificar padrão de comportamento
    // Falha de DESIGN: não há rate limiting ou detecção de bot
    return [
        'aprovado' => true,
        'motivo' => 'ok',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem proteção anti-bot ===\n";

    // Simula um bot disparando requisições
    $agora = time();
    $historicoBot = [
        $agora,
        $agora - 1,
        $agora - 2,
        $agora - 3,
        $agora - 4,
        $agora - 5,
        $agora - 6,
        $agora - 7,
        $agora - 8,
        $agora - 9,
    ];

    $resultado = processarCompra('bot_attacker_123', 1, $historicoBot);

    if ($resultado['aprovado']) {
        echo "PROBLEMA: Bot conseguiu fazer compra mesmo com 10 requisições em 9 segundos!\n";
        echo "Falha de DESIGN: sem proteção anti-bot, scalper limpa o estoque\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
