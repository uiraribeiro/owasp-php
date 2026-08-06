<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Design com Proteção Anti-Bot
 *
 * A decisão de design correta implementa RATE LIMITING e detecção de comportamento de bot:
 * 1. Monitora o histórico de requisições recentes (últimos X segundos)
 * 2. Se houver muitas requisições em pouco tempo, rejeita como suspeito (padrão de bot)
 * 3. Clientes legítimos (1-2 requisições por intervalo) funcionam normalmente
 * 4. Bots que disparam dezenas de requisições por segundo são bloqueados
 * Dessa forma, clientes reais têm chance de comprar antes que bots limpem o estoque.
 */

function processarCompra(string $idCliente, int $quantidade, array $historicoRequisicoesRecentes, int $agora): array {
    // CORRIGIDO: proteção anti-bot com rate limiting

    // Limite: máximo de 3 requisições nos últimos 10 segundos
    $limiteRequisicoes = 3;
    $janelaDeTempoSegundos = 10;
    $thresholdDeteccaoBot = $limiteRequisicoes;

    // Contar quantas requisições deste cliente aconteceram no intervalo recente
    $requisicoesRecentes = 0;
    foreach ($historicoRequisicoesRecentes as $timestamp) {
        if ($agora - $timestamp <= $janelaDeTempoSegundos) {
            $requisicoesRecentes++;
        }
    }

    // Verificação: muitas requisições em pouco tempo = padrão de bot
    if ($requisicoesRecentes > $thresholdDeteccaoBot) {
        return [
            'aprovado' => false,
            'motivo' => sprintf(
                'Muitas requisições recentes detectadas (%d em %ds). Aguarde antes de tentar novamente.',
                $requisicoesRecentes,
                $janelaDeTempoSegundos
            ),
        ];
    }

    // Cliente legítimo com requisições normais
    return [
        'aprovado' => true,
        'motivo' => 'Compra processada com sucesso',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Proteção anti-bot com rate limiting ===\n";

    $agora = time();

    // Teste 1: Bot com muitas requisições em pouco tempo
    echo "\nTeste 1: Bot disparando requisições\n";
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

    $resultadoBot = processarCompra('bot_attacker', 1, $historicoBot, $agora);
    echo "Bot com 10 requisições em 9 segundos: " . ($resultadoBot['aprovado'] ? "APROVADO" : "NEGADO") . "\n";
    echo "Motivo: {$resultadoBot['motivo']}\n";

    // Teste 2: Cliente legítimo com histórico vazio
    echo "\nTeste 2: Cliente legítimo (primeira compra)\n";
    $resultadoLegitimo1 = processarCompra('cliente_real_123', 1, [], $agora);
    echo "Primeira compra: " . ($resultadoLegitimo1['aprovado'] ? "APROVADO" : "NEGADO") . "\n";

    // Teste 3: Cliente legítimo com uma requisição antiga
    echo "\nTeste 3: Cliente legítimo (histórico antigo)\n";
    $historicoAntigo = [$agora - 60];  // Requisição de 60 segundos atrás
    $resultadoLegitimo2 = processarCompra('cliente_real_456', 1, $historicoAntigo, $agora);
    echo "Segunda compra (histórico > 10s): " . ($resultadoLegitimo2['aprovado'] ? "APROVADO" : "NEGADO") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
