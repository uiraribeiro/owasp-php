<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Inclusão de Caminho Não Controlado (Path Traversal / LFI) - A08:2025
 *
 * O sistema monta um caminho de arquivo diretamente com input do usuário,
 * sem NENHUMA validação. Um atacante pode enviar "../../../etc/passwd"
 * ou "../../config/database.php" para sair do diretório pretendido e
 * acessar/incluir arquivos arbitrários do servidor.
 */

function carregarModulo(string $nomeModulo): ?string {
    // VULNERÁVEL: monta o caminho direto, sem validação
    // Um atacante pode usar ".." para fazer path traversal
    return "modulos/{$nomeModulo}.php";
}

function demo(): void {
    echo "=== VULNERÁVEL: Path Traversal (sem validação) ===\n";

    $nomeModuloLegitimo = "relatorios";
    $caminhoLegitimo = carregarModulo($nomeModuloLegitimo);
    echo "Módulo legítimo: {$caminhoLegitimo}\n";

    // Atacante tenta sair do diretório
    $nomeModuloMalicioso = "../../config/database";
    $caminhoMalicioso = carregarModulo($nomeModuloMalicioso);
    echo "Atacante tenta path traversal: {$caminhoMalicioso}\n";

    if (str_contains($caminhoMalicioso, '..')) {
        echo "PROBLEMA! Caminho contém '..' - path traversal possível!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
