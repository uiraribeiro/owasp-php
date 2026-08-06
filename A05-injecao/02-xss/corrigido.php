<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * Cross-Site Scripting (XSS) - CORREÇÃO
 * Usar htmlspecialchars() com ENT_QUOTES e charset UTF-8 para escapar
 * caracteres especiais de HTML (<, >, &, ", '), tornando o input
 * inofensivo e renderizado como texto literal.
 */

function renderizarComentario(string $comentario): string
{
    // CORRIGIDO: escapar HTML com htmlspecialchars
    return "<div class=\"comentario\">" . htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8') . "</div>";
}

function demo(): void
{
    echo "=== Demonstração: XSS (CORRIGIDO) ===\n";

    // Teste legítimo
    echo "\n1. Comentário legítimo:\n";
    $html = renderizarComentario("Que tutorial excelente!");
    echo "   HTML gerado: {$html}\n";

    // XSS Attack - bloqueado
    echo "\n2. XSS Attack - bloqueado:\n";
    $malicioso = '<script>alert("XSS")</script>';
    echo "   Input: {$malicioso}\n";
    $html = renderizarComentario($malicioso);
    echo "   HTML gerado: {$html}\n";
    if (!str_contains($html, '<script>') && str_contains($html, '&lt;script&gt;')) {
        echo "   ✓ Script escapado, será exibido como texto\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
