<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Exigir SRI (Subresource Integrity) para Scripts Externos
 *
 * O sistema OBRIGA o hash de integridade (SRI) para qualquer script externo.
 * O navegador verifica que o conteúdo baixado bate com o hash esperado,
 * rejeitando qualquer versão comprometida. Isso protege contra CDN
 * comprometidos e ataques MITM.
 */

function gerarTagScriptExterno(string $urlScript, ?string $hashIntegridadeSri): string {
    // CORRIGIDO: obriga SRI, rejeitando tags sem integridade
    if ($hashIntegridadeSri === null || $hashIntegridadeSri === '') {
        throw new \InvalidArgumentException(
            'Não é permitido incluir script externo sem hash de integridade (SRI). ' .
            'Forneça um hash válido como "sha384-..." ou "sha512-..."'
        );
    }

    return "<script src=\"{$urlScript}\" integrity=\"{$hashIntegridadeSri}\" crossorigin=\"anonymous\"></script>";
}

function demo(): void {
    echo "=== CORRIGIDO: Script externo com SRI obrigatório ===\n";

    $url = "https://cdn-terceiro.exemplo.com/biblioteca.js";
    $hashSri = "sha384-abc123xyz789def456ghi789jkl012";

    try {
        $tag = gerarTagScriptExterno($url, $hashSri);
        echo "Tag gerada: {$tag}\n";

        if (str_contains($tag, 'integrity=') && str_contains($tag, 'crossorigin=')) {
            echo "OK! Tag contém SRI e crossorigin\n";
        }
    } catch (\InvalidArgumentException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }

    // Tentar sem SRI
    try {
        $tag = gerarTagScriptExterno($url, null);
        echo "Erro: tag foi gerada sem SRI (não deveria)\n";
    } catch (\InvalidArgumentException $e) {
        echo "OK! Rejeitada tentativa de gerar script sem SRI\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
