<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Armazenar Decisões de Segurança no Servidor
 *
 * O cookie do cliente contém APENAS um ID de sessão opaco e aleatório.
 * Dados sensíveis (role, permissões) são armazenados exclusivamente no servidor,
 * indexados por esse ID de sessão. O cliente não pode falsificar dados sensíveis,
 * só pode editar seu próprio cookie que é apenas um identificador.
 */

function usuarioEhAdmin(string $idSessao, array $sessoesArmazenadasNoServidor): bool {
    // CORRIGIDO: consultamos o armazenamento confiável do servidor, indexado pela sessão
    $sessao = $sessoesArmazenadasNoServidor[$idSessao] ?? null;

    if ($sessao === null) {
        return false;
    }

    return ($sessao['role'] ?? '') === 'admin';
}

function demo(): void {
    echo "=== CORRIGIDO: Decisão de admin baseada em server-side session ===\n";

    // Simulação do armazenamento server-side de sessões
    $sessoesArmazenadasNoServidor = [
        'sessao_usuario_normal' => ['role' => 'user'],
        'sessao_usuario_admin' => ['role' => 'admin'],
    ];

    $resultado = usuarioEhAdmin('sessao_usuario_normal', $sessoesArmazenadasNoServidor);
    echo "Sessão user (server-side role=user): admin? " . ($resultado ? 'SIM' : 'NÃO') . "\n";

    $resultado = usuarioEhAdmin('sessao_usuario_admin', $sessoesArmazenadasNoServidor);
    echo "Sessão admin (server-side role=admin): admin? " . ($resultado ? 'SIM' : 'NÃO') . "\n";

    // Mesmo que atacante edite o cookie para "sessao_usuario_admin", se não tiver
    // acesso ao armazenamento do servidor, não consegue criar essa sessão lá.
    // O servidor rejeita sessões inexistentes.
    $resultado = usuarioEhAdmin('sessao_fake_do_atacante', $sessoesArmazenadasNoServidor);
    echo "Sessão fake do atacante: admin? " . ($resultado ? 'SIM' : 'NÃO (bloqueado!)') . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
