<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Dados Sensíveis Expostos em Modo Debug (CWE-215) - A10:2025 Mishandling of Exceptional Conditions
 *
 * Em modo debug (que pode acidentalmente continuar ativo em produção),
 * a função despeja TODOS os campos da requisição usando print_r(),
 * incluindo senhas, números de cartão de crédito e tokens de segurança.
 */

function depurarRequisicao(array $dadosRequisicao, bool $modoDebug): ?string {
    if (!$modoDebug) {
        return null;
    }

    // VULNERÁVEL: despeja tudo sem filtrar dados sensíveis
    // print_r() mostra TODOS os campos, inclusive senha, cartão de crédito, token
    return print_r($dadosRequisicao, true);
}

function demo(): void {
    echo "=== VULNERÁVEL: Exposição de dados sensíveis em debug ===\n";

    $dados = [
        'usuario' => 'joao',
        'email' => 'joao@example.com',
        'senha' => 'MinhaSenha123',
        'cartao_credito' => '4111111111111111',
        'token' => 'abc123xyz789'
    ];

    $saida = depurarRequisicao($dados, true);
    echo "Saída de debug:\n{$saida}\n";
    echo "(PROBLEMA: senha, cartão de crédito e token expostos em debug!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
