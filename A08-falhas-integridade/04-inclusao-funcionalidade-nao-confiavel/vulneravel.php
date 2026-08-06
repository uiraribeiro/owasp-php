<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Inclusão de Funcionalidade Não Confiável (CDN Comprometido) - A08:2025
 *
 * O sistema gera uma tag <script> para incluir código de um terceiro (CDN)
 * sem NENHUM atributo de integridade (SRI - Subresource Integrity).
 * Se o CDN ou a conexão for comprometida, código malicioso roda no navegador
 * do usuário com acesso aos dados da página.
 */

function gerarTagScriptExterno(string $urlScript, ?string $hashIntegridadeSri): string {
    // VULNERÁVEL: gera a tag sem verificar integridade, sem SRI
    return "<script src=\"{$urlScript}\"></script>";
}

function demo(): void {
    echo "=== VULNERÁVEL: Script externo sem SRI ===\n";

    $url = "https://cdn-terceiro.exemplo.com/biblioteca.js";
    $tag = gerarTagScriptExterno($url, "sha384-abc123xyz789");

    echo "Tag gerada: {$tag}\n";

    if (!str_contains($tag, 'integrity=')) {
        echo "PROBLEMA! Tag não contém atributo 'integrity' - sem proteção SRI!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
