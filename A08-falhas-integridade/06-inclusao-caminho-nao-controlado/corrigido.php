<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Whitelist de Caracteres (Allow-List)
 *
 * Antes de montar um caminho, validamos que o input contém APENAS caracteres
 * permitidos (letras, números, underscore). Assim, caracteres especiais como
 * ".", "/", e espaço são rejeitados, impossibilitando path traversal.
 * Esta é a abordagem mais segura: allow-list é melhor que deny-list.
 */

function carregarModulo(string $nomeModulo): ?string {
    // CORRIGIDO: valida com allow-list de caracteres seguros
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nomeModulo)) {
        // Rejeita qualquer nome que contenha caracteres perigosos como ".", "/", etc
        return null;
    }

    return "modulos/{$nomeModulo}.php";
}

function demo(): void {
    echo "=== CORRIGIDO: Path Traversal bloqueado com whitelist ===\n";

    $nomeModuloLegitimo = "relatorios";
    $caminhoLegitimo = carregarModulo($nomeModuloLegitimo);
    echo "Módulo legítimo (relatorios): {$caminhoLegitimo}\n";

    $nomeModuloLegitimo2 = "usuarios_gestao";
    $caminhoLegitimo2 = carregarModulo($nomeModuloLegitimo2);
    echo "Módulo legítimo (usuarios_gestao): {$caminhoLegitimo2}\n";

    // Atacante tenta sair do diretório
    $nomeModuloMalicioso = "../../config/database";
    $caminhoMalicioso = carregarModulo($nomeModuloMalicioso);
    echo "Atacante tenta path traversal: ";

    if ($caminhoMalicioso === null) {
        echo "REJEITADO (null)\n";
    } else {
        echo "{$caminhoMalicioso}\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
