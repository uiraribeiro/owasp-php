<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Verificar Autorização no Servidor Para Cada Rota
 *
 * A proteção não é apenas no menu (frontend), mas no próprio tratamento
 * da rota no servidor. Antes de retornar conteúdo sensível, verifica
 * se o usuário está autenticado E tem a role necessária.
 */

function tratarRota(string $rota, ?array $usuario): string {
    // Rota pública
    if ($rota === '/home') {
        return 'Página inicial - conteúdo público';
    }

    // Rota de admin
    if ($rota === '/admin/painel') {
        // CORRIGIDO: valida autenticação E role antes de retornar conteúdo
        if ($usuario === null || $usuario['role'] !== 'admin') {
            return 'Acesso negado: privilégios insuficientes';
        }
        return 'Painel de Administração - conteúdo altamente sensível';
    }

    return 'Rota não encontrada';
}

function demo(): void {
    echo "=== CORRIGIDO: Forced Browsing Bloqueado ===\n";

    $usuarioComum = ['id' => 1, 'role' => 'user'];
    $usuarioAdmin = ['id' => 2, 'role' => 'admin'];

    echo "Usuário comum acessando /home: " . tratarRota('/home', $usuarioComum) . "\n";
    echo "Usuário comum acessando /admin/painel: " . tratarRota('/admin/painel', $usuarioComum) . " (bloqueado!)\n";
    echo "Admin acessando /admin/painel: " . tratarRota('/admin/painel', $usuarioAdmin) . " (permitido)\n";
}

if (debug_backtrace() === []) {
    demo();
}
