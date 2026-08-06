<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * Command Injection (OS Injection) - FALHA A05:2025 Injection
 * Concatenação de entrada do usuário em comandos do sistema operacional
 * permite execução arbitrária de comandos, mesmo sem intenção do desenvolvedor.
 */

function montarComandoConversaoImagem(string $nomeArquivo): string
{
    // VULNERÁVEL: concatenação direta do nome de arquivo no comando
    // Um atacante pode injetar ; ou && ou | para executar comandos extras
    return "convert {$nomeArquivo} saida.png";
}

function demo(): void
{
    echo "=== Demonstração: Command Injection (VULNERÁVEL) ===\n";

    // Teste legítimo
    echo "\n1. Comando legítimo:\n";
    $comando = montarComandoConversaoImagem("imagem.jpg");
    echo "   Comando: {$comando}\n";

    // Command Injection
    echo "\n2. Command Injection - concatenação de comando:\n";
    $nomemalicioso = "foto.jpg; echo INJETADO";
    echo "   Input: {$nomemalicioso}\n";
    $comando = montarComandoConversaoImagem($nomemalicioso);
    echo "   Comando: {$comando}\n";
    if (str_contains($comando, "; echo INJETADO")) {
        echo "   ⚠️  VULNERÁVEL! O ; não está escapado, permitindo comando encadeado\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
