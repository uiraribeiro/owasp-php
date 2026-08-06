<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Listagem de Diretórios Habilitada - A02:2025 Security Misconfiguration
 *
 * Servidor web lista todos os arquivos do diretório quando não há index.
 * Atacante descobre arquivos de backup, configuração, credenciais ou fontes sensíveis.
 */

function tratarRequisicaoArquivo(string $caminhoSolicitado, array $arquivosNoDiretorio, bool $temIndex): array {
    // Simula acesso a um diretório (caminho termina com /)
    if (!$temIndex) {
        // VULNERÁVEL: lista todos os arquivos do diretório, incluindo sensíveis
        return [
            'status' => 200,
            'corpo' => implode(', ', $arquivosNoDiretorio),
        ];
    }

    return [
        'status' => 200,
        'corpo' => 'Conteúdo de index.html',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Listagem de diretório expõe arquivos sensíveis ===\n";

    $arquivos = ['index.html', '.env', 'config.php.bak', 'backup.sql'];
    $resposta = tratarRequisicaoArquivo('/uploads/', $arquivos, false);

    echo "Status: {$resposta['status']}\n";
    echo "Corpo: {$resposta['corpo']}\n";
    if (str_contains($resposta['corpo'], '.env')) {
        echo "Arquivo .env exposto (PROBLEMA!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
