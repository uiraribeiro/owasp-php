<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * NoSQL Injection - FALHA A05:2025 Injection
 * Aceitar objetos/arrays com operadores NoSQL (como MongoDB $ne, $gt, etc)
 * diretamente da entrada do usuário permite bypass de autenticação
 * e acesso não autorizado a dados.
 */

function login(array $colecaoUsuarios, array $filtro): ?array
{
    // VULNERÁVEL: aceita operadores NoSQL do atacante sem validação
    // Um atacante pode passar { "usuario": "admin", "senha": { "$ne": "" } }
    // e conseguir login sem saber a senha real

    foreach ($colecaoUsuarios as $usuario) {
        $bate = true;
        foreach ($filtro as $campo => $valor) {
            if (is_array($valor) && isset($valor['$ne'])) {
                // Operador $ne: "diferente de"
                // Bate se o valor do documento é diferente do valor especificado
                if ($usuario[$campo] === $valor['$ne']) {
                    $bate = false;
                    break;
                }
            } else {
                // Comparação de igualdade direta
                if ($usuario[$campo] !== $valor) {
                    $bate = false;
                    break;
                }
            }
        }
        if ($bate) {
            return $usuario;
        }
    }
    return null;
}

function demo(): void
{
    echo "=== Demonstração: NoSQL Injection (VULNERÁVEL) ===\n";

    $usuarios = [
        ['usuario' => 'admin', 'senha' => 'senhaForte123'],
        ['usuario' => 'user', 'senha' => 'senha456'],
    ];

    // Teste legítimo
    echo "\n1. Login legítimo com senha correta:\n";
    $resultado = login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaForte123']);
    echo "   Resultado: " . ($resultado ? "Usuário: " . $resultado['usuario'] : "Falha") . "\n";

    // NoSQL Injection: bypass usando $ne
    echo "\n2. NoSQL Injection - bypass com operador \$ne:\n";
    echo "   Filtro: {'usuario': 'admin', 'senha': {'\$ne': ''}}\n";
    $filtroInjection = ['usuario' => 'admin', 'senha' => ['$ne' => '']];
    $resultado = login($usuarios, $filtroInjection);
    echo "   Resultado: " . ($resultado ? "VULNERÁVEL! Bypass sem senha correta: " . $resultado['usuario'] : "Bloqueado") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
