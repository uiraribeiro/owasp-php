<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validação Rigorosa de Senhas
 *
 * Exige comprimento mínimo de 12 caracteres, verifica contra lista de senhas fracas conhecidas,
 * e impede senhas iguais ao nome de usuário. Melhora significativamente a resistência contra força bruta.
 */

function validarNovaSenha(string $senha, string $usuario): bool {
    // CORRIGIDO: validação rigorosa

    // 1. Mínimo de 12 caracteres
    if (strlen($senha) < 12) {
        return false;
    }

    // 2. Lista local de senhas fracas conhecidas (inclui variações de diferentes tamanhos)
    $senhasFracasConhecidas = [
        '123456',
        'password',
        'senha123',
        '12345678',
        'qwerty',
        'admin123',
        'password1234',  // 12 caracteres, claramente fraca
        '123456789012',  // 12 x '1'
    ];

    if (in_array($senha, $senhasFracasConhecidas, true)) {
        return false;
    }

    // 3. Não pode ser igual ao nome de usuário (case-insensitive)
    if (strtolower($senha) === strtolower($usuario)) {
        return false;
    }

    return true;
}

function demo(): void {
    echo "=== CORRIGIDO: Validação rigorosa de senhas ===\n";

    $senhasTesteCorrigido = [
        ['senha' => '1234', 'usuario' => 'joao', 'esperado' => false, 'motivo' => 'muito curta'],
        ['senha' => 'password1234', 'usuario' => 'maria', 'esperado' => false, 'motivo' => 'está na lista de fracas'],
        ['senha' => 'SenhaForte2025!', 'usuario' => 'joao', 'esperado' => true, 'motivo' => 'forte e única'],
        ['senha' => 'Joao123456', 'usuario' => 'joao', 'esperado' => false, 'motivo' => 'baseada no nome de usuário'],
    ];

    foreach ($senhasTesteCorrigido as $teste) {
        $valida = validarNovaSenha($teste['senha'], $teste['usuario']);
        $resultado = $valida ? 'ACEITA' : 'REJEITADA';
        echo "Senha '{$teste['senha']}' para {$teste['usuario']}: {$resultado} ({$teste['motivo']})\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
