<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Decisão de Segurança Baseada em Cookie do Cliente - A08:2025
 *
 * O sistema decide se um usuário é admin baseado num valor de COOKIE
 * que o próprio cliente pode editar livremente no navegador.
 * Um atacante comum pode editar "role: user" para "role: admin" e
 * escalar privilégios sem conhecer nenhuma senha.
 */

function usuarioEhAdmin(array $cookiesDoCliente): bool {
    // VULNERÁVEL: confia no valor do cookie do cliente para decisão de segurança
    return ($cookiesDoCliente['role'] ?? '') === 'admin';
}

function demo(): void {
    echo "=== VULNERÁVEL: Decisão de admin baseada em cookie ===\n";

    $cookieUsuarioNormal = ['role' => 'user'];
    $resultado = usuarioEhAdmin($cookieUsuarioNormal);
    echo "Usuário normal (cookie role=user): admin? " . ($resultado ? 'SIM' : 'NÃO') . "\n";

    // Atacante edita o próprio cookie no navegador
    $cookieDoAtacante = ['role' => 'admin'];
    $resultado = usuarioEhAdmin($cookieDoAtacante);
    echo "Atacante edita cookie para role=admin: admin? " . ($resultado ? 'SIM (PROBLEMA!)' : 'NÃO') . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
