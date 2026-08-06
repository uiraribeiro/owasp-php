<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * NoSQL Injection - CORREÇÃO
 * Validar rigorosamente o tipo de cada parâmetro esperado.
 * Campos críticos como senha DEVEM ser strings simples, nunca arrays ou objetos.
 * Rejeitar qualquer filtro que contenha estruturas inesperadas.
 */

function login(array $colecaoUsuarios, array $filtro): ?array
{
    // CORRIGIDO: validar que usuario e senha são OBRIGATORIAMENTE strings
    // Rejeitar qualquer operador NoSQL ou estrutura complexa
    if (!is_string($filtro['usuario'] ?? null) || !is_string($filtro['senha'] ?? null)) {
        return null; // Rejeita filtro inválido
    }

    foreach ($colecaoUsuarios as $usuario) {
        if ($usuario['usuario'] === $filtro['usuario'] && $usuario['senha'] === $filtro['senha']) {
            return $usuario;
        }
    }
    return null;
}

function demo(): void
{
    echo "=== Demonstração: NoSQL Injection (CORRIGIDO) ===\n";

    $usuarios = [
        ['usuario' => 'admin', 'senha' => 'senhaForte123'],
        ['usuario' => 'user', 'senha' => 'senha456'],
    ];

    // Teste legítimo
    echo "\n1. Login legítimo com senha correta:\n";
    $resultado = login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaForte123']);
    echo "   Resultado: " . ($resultado ? "Usuário: " . $resultado['usuario'] : "Falha") . "\n";

    // NoSQL Injection: bloqueado
    echo "\n2. NoSQL Injection - bloqueado por validação de tipo:\n";
    echo "   Filtro: {'usuario': 'admin', 'senha': {'\$ne': ''}}\n";
    $filtroInjection = ['usuario' => 'admin', 'senha' => ['$ne' => '']];
    $resultado = login($usuarios, $filtroInjection);
    echo "   Resultado: " . ($resultado ? "VULNERÁVEL! Bypass conseguiu." : "Bloqueado corretamente") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
