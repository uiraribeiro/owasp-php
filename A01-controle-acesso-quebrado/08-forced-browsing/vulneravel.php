<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Forced Browsing (A01:2025 - Broken Access Control)
 *
 * A única "proteção" é não mostrar links de admin no menu para usuários comuns.
 * Mas se alguém acessa a URL diretamente (/admin/painel), o servidor retorna
 * o conteúdo SEM verificar a role. Segurança por obscuridade, não por verificação real.
 */

function tratarRota(string $rota, ?array $usuario): string {
    // Rota pública
    if ($rota === '/home') {
        return 'Página inicial - conteúdo público';
    }

    // Rota de admin
    if ($rota === '/admin/painel') {
        // VULNERÁVEL: sem verificação de role
        // A única "proteção" é que o link não aparece no menu para usuários comuns
        return 'Painel de Administração - conteúdo altamente sensível';
    }

    return 'Rota não encontrada';
}

function demo(): void {
    echo "=== VULNERÁVEL: Forced Browsing ===\n";

    $usuarioComum = ['id' => 1, 'role' => 'user'];

    echo "Usuário comum acessando /home: " . tratarRota('/home', $usuarioComum) . "\n";
    echo "Usuário comum acessando /admin/painel: " . tratarRota('/admin/painel', $usuarioComum) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
