<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Desabilitar Listagem de Diretórios
 *
 * Diretórios sem index retornam 403 Forbidden.
 * Usuários precisam conhecer URLs exatas para acessar recursos.
 */

function tratarRequisicaoArquivo(string $caminhoSolicitado, array $arquivosNoDiretorio, bool $temIndex): array {
    // Simula acesso a um diretório (caminho termina com /)
    if (!$temIndex) {
        // CORRIGIDO: bloqueia listagem de diretório, retorna 403
        return [
            'status' => 403,
            'corpo' => 'Acesso negado. Listagem de diretórios não é permitida.',
        ];
    }

    return [
        'status' => 200,
        'corpo' => 'Conteúdo de index.html',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Listagem de diretório bloqueada ===\n";

    $arquivos = ['index.html', '.env', 'config.php.bak', 'backup.sql'];
    $resposta = tratarRequisicaoArquivo('/uploads/', $arquivos, false);

    echo "Status: {$resposta['status']}\n";
    echo "Corpo: {$resposta['corpo']}\n";
    if ($resposta['status'] === 403) {
        echo "Listagem de diretório bloqueada (protegido!)\n";
    }

    // Com index.html presente
    $respostaComIndex = tratarRequisicaoArquivo('/uploads/', $arquivos, true);
    echo "\nCom index.html:\n";
    echo "Status: {$respostaComIndex['status']}\n";
    echo "Corpo: {$respostaComIndex['corpo']}\n";
}

if (debug_backtrace() === []) {
    demo();
}
