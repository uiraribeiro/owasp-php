<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * Code Injection / Expression Language Injection - FALHA A05:2025 Injection
 * Usar eval() ou construções similares para processar templates com entrada do usuário
 * permite execução arbitrária de código PHP, equivalente ao OGNL injection em Java.
 */

function avaliarTemplate(string $template, array $variaveis): string
{
    // VULNERÁVEL: usar eval() para processar expressões do usuário
    // Um atacante pode injetar PHP arbitrário, ex: {{ system('comando') }}

    $resultado = $template;
    // Procura por {{...}} e avalia como código PHP
    $resultado = preg_replace_callback(
        '/\{\{(.+?)\}\}/',
        function ($matches) use ($variaveis) {
            $expr = trim($matches[1]);
            // PERIGOSO: avaliar como PHP arbitrário
            // Construir bloco de atribuição de variáveis para o eval
            $varCode = '';
            foreach ($variaveis as $k => $v) {
                $varCode .= '$' . $k . ' = ' . var_export($v, true) . ';';
            }
            // Avaliar a expressão como PHP puro
            $fullCode = $varCode . 'return ' . $expr . ';';
            return (string)eval($fullCode);
        },
        $resultado
    );
    return $resultado;
}

function demo(): void
{
    echo "=== Demonstração: Code Injection (VULNERÁVEL) ===\n";

    // Teste legítimo
    echo "\n1. Template legítimo com variável:\n";
    $resultado = avaliarTemplate("Olá {{\$nome}}", ['nome' => 'João']);
    echo "   Resultado: {$resultado}\n";

    // Code Injection
    echo "\n2. Code Injection - execução de código arbitrário:\n";
    $template = "Teste: {{ system('echo INJETADO') }}";
    echo "   Template: {$template}\n";
    ob_start();
    try {
        $resultado = avaliarTemplate($template, []);
    } catch (\Throwable $e) {
        ob_end_clean();
        echo "   Erro ao processar: " . $e->getMessage() . "\n";
        return;
    }
    $output = ob_get_clean();
    echo "   Resultado: {$resultado}\n";
    if (str_contains($output, "INJETADO")) {
        echo "   ⚠️  VULNERÁVEL! Código arbitrário foi executado\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
