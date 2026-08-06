<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Rate Limiting e Bloqueio de Contas
 *
 * Implementa um limite de tentativas falhadas. Após 5 tentativas em um curto período,
 * a conta é bloqueada temporariamente, impedindo credential stuffing e password spray.
 */

function tentarLogin(string $usuario, string $senha, string $senhaCorreta, array $tentativasAnteriores): array {
    // CORRIGIDO: verifica o número de tentativas falhas recentes
    if (\count($tentativasAnteriores) >= 5) {
        return ['permitido' => false, 'motivo' => 'muitas tentativas, tente mais tarde'];
    }

    // Apenas depois de verificar o limite, valida a senha
    if ($senha === $senhaCorreta) {
        return ['permitido' => true, 'motivo' => 'ok'];
    }
    return ['permitido' => false, 'motivo' => 'senha incorreta'];
}

function demo(): void {
    echo "=== CORRIGIDO: Com limite de tentativas (Rate Limiting) ===\n";

    $tentativasAnteriores = [
        1690000001,
        1690000002,
        1690000003,
        1690000004,
        1690000005,
    ];

    // Mesmo com a senha correta, o acesso é bloqueado por excesso de tentativas
    $resultado = tentarLogin('joao@example.com', 'senha_correta_123', 'senha_correta_123', $tentativasAnteriores);
    echo "Após 5 tentativas falhas, mesmo com senha correta: " . json_encode($resultado) . "\n";

    if (!$resultado['permitido']) {
        echo "CORRIGIDO: Login bloqueado por excesso de tentativas (proteção contra credential stuffing)\n";
    }

    // Com histórico vazio, login normal funciona
    $semTentativas = tentarLogin('joao@example.com', 'senha_correta_123', 'senha_correta_123', []);
    echo "Sem tentativas anteriores, com senha correta: " . json_encode($semTentativas) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
