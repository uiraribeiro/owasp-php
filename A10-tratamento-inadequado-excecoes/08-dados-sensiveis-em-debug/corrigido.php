<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Filtro de Dados Sensíveis em Modo Debug
 *
 * Define lista de campos sensíveis que devem ser redacted (mascarados)
 * antes de exibir em modo debug. Campos não-sensíveis continuam visíveis
 * para fins de depuração, mas dados críticos estão protegidos.
 */

function depurarRequisicao(array $dadosRequisicao, bool $modoDebug): ?string {
    if (!$modoDebug) {
        return null;
    }

    // CORRIGIDO: define quais campos são sensíveis
    $camposSensiveis = ['senha', 'cartao_credito', 'token'];

    // Cria cópia dos dados para filtrar
    $dadosFiltrados = $dadosRequisicao;

    // Redacts campos sensíveis
    foreach ($camposSensiveis as $campo) {
        if (isset($dadosFiltrados[$campo])) {
            $dadosFiltrados[$campo] = '[REDACTED]';
        }
    }

    // Retorna debug info com dados sensíveis mascarados
    return print_r($dadosFiltrados, true);
}

function demo(): void {
    echo "=== CORRIGIDO: Debug com filtro de dados sensíveis ===\n";

    $dados = [
        'usuario' => 'joao',
        'email' => 'joao@example.com',
        'senha' => 'MinhaSenha123',
        'cartao_credito' => '4111111111111111',
        'token' => 'abc123xyz789'
    ];

    $saida = depurarRequisicao($dados, true);
    echo "Saída de debug (filtrada):\n{$saida}\n";
    echo "(OK: dados sensíveis mascarados, dados não-sensíveis visíveis para debug)\n";
}

if (debug_backtrace() === []) {
    demo();
}
