<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * Cross-Site Scripting (XSS) - FALHA A05:2025 Injection
 * Renderizar entrada do usuário diretamente no HTML sem escapar
 * permite injeção de scripts maliciosos que serão executados no navegador.
 */

function renderizarComentario(string $comentario): string
{
    // VULNERÁVEL: entrada do usuário concatenada diretamente no HTML
    return "<div class=\"comentario\">{$comentario}</div>";
}

function demo(): void
{
    echo "=== Demonstração: XSS (VULNERÁVEL) ===\n";

    // Teste legítimo
    echo "\n1. Comentário legítimo:\n";
    $html = renderizarComentario("Que tutorial excelente!");
    echo "   HTML gerado: {$html}\n";

    // XSS Attack
    echo "\n2. XSS Attack - script injection:\n";
    $malicioso = '<script>alert("XSS")</script>';
    echo "   Input: {$malicioso}\n";
    $html = renderizarComentario($malicioso);
    echo "   HTML gerado: {$html}\n";
    if (str_contains($html, '<script>')) {
        echo "   ⚠️  VULNERÁVEL! Script será executado pelo navegador\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
