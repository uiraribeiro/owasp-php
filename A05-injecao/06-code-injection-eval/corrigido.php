<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * Code Injection / Expression Language Injection - CORREÇÃO
 * Nunca usar eval() ou construções similares com entrada do usuário.
 * Processar templates com um conjunto restrito de padrões conhecidos,
 * permitindo apenas nomes de variáveis simples, sem chamadas de função.
 */

function avaliarTemplate(string $template, array $variaveis): string
{
    // CORRIGIDO: regex estrita que só aceita nomes de variável simples
    // Padrão: {{ nomeSimples }}, onde nomeSimples é [a-zA-Z_][a-zA-Z0-9_]*
    $resultado = preg_replace_callback(
        '/\{\{\s*([a-zA-Z_][a-zA-Z0-9_]*)\s*\}\}/',
        function ($matches) use ($variaveis) {
            $nomeDaVariavel = $matches[1];
            // Apenas substituir pelo valor da variável, NUNCA executar código
            return $variaveis[$nomeDaVariavel] ?? '';
        },
        $template
    );
    return $resultado;
}

function demo(): void
{
    echo "=== Demonstração: Code Injection (CORRIGIDO) ===\n";

    // Teste legítimo
    echo "\n1. Template legítimo com variável:\n";
    $resultado = avaliarTemplate("Olá {{nome}}", ['nome' => 'João']);
    echo "   Resultado: {$resultado}\n";
    echo "   Nota: A versão corrigida aceita {{nome}} (sem $) porque a regex valida nomes de variável\n";

    // Code Injection - bloqueado
    echo "\n2. Code Injection - bloqueado:\n";
    $template = "Teste: {{ system('echo INJETADO') }}";
    echo "   Template: {$template}\n";
    $resultado = avaliarTemplate($template, []);
    echo "   Resultado: {$resultado}\n";
    if (!str_contains($resultado, "INJETADO")) {
        echo "   ✓ Código malicioso não foi executado, padrão rejeitado\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
