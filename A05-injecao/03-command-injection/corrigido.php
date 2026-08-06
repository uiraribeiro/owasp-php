<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * Command Injection - CORREÇÃO
 * Usar escapeshellarg() para envolver cada argumento do usuário
 * em aspas simples escapadas, prevenindo que metacaracteres (;, &, |, etc)
 * sejam interpretados como separadores de comando.
 */

function montarComandoConversaoImagem(string $nomeArquivo): string
{
    // CORRIGIDO: usar escapeshellarg para proteger o argumento
    return "convert " . escapeshellarg($nomeArquivo) . " saida.png";
}

function demo(): void
{
    echo "=== Demonstração: Command Injection (CORRIGIDO) ===\n";

    // Teste legítimo
    echo "\n1. Comando legítimo:\n";
    $comando = montarComandoConversaoImagem("imagem.jpg");
    echo "   Comando: {$comando}\n";

    // Command Injection - bloqueado
    echo "\n2. Command Injection - bloqueado:\n";
    $nomemalicioso = "foto.jpg; echo INJETADO";
    echo "   Input: {$nomemalicioso}\n";
    $comando = montarComandoConversaoImagem($nomemalicioso);
    echo "   Comando: {$comando}\n";
    if (str_contains($comando, "'foto.jpg; echo INJETADO'")) {
        echo "   ✓ Nome inteiro está entre aspas simples, ; é literal\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
