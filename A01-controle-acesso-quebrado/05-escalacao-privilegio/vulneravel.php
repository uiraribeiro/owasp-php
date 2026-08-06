<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Escalação de Privilégio (A01:2025 - Broken Access Control)
 *
 * Ao fazer login, o servidor aceita um campo 'role' enviado pelo cliente
 * em vez de sempre buscar a role correta do banco de dados.
 * Um atacante pode enviar role='admin' e ganhar privilégios elevados.
 */

function autenticar(array $dadosLogin, array $baseDeUsuarios): array {
    $nomeUsuario = $dadosLogin['usuario'] ?? '';

    if (!$nomeUsuario || empty($baseDeUsuarios)) {
        return [
            'autenticado' => false,
            'mensagem' => 'Falha na autenticação',
        ];
    }

    // Verifica se usuário existe na base
    $usuarioEncontrado = null;
    foreach ($baseDeUsuarios as $usr) {
        if ($usr['nome'] === $nomeUsuario) {
            $usuarioEncontrado = $usr;
            break;
        }
    }

    if (!$usuarioEncontrado) {
        return [
            'autenticado' => false,
            'mensagem' => 'Usuário não encontrado',
        ];
    }

    // VULNERÁVEL: usa role vindo do dadosLogin se estiver presente
    $role = $dadosLogin['role'] ?? $usuarioEncontrado['role'];

    return [
        'autenticado' => true,
        'usuario' => $nomeUsuario,
        'role' => $role,
        'mensagem' => 'Login bem-sucedido',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Escalação de Privilégio ===\n";

    $baseDeUsuarios = [
        ['nome' => 'alice', 'role' => 'user'],
        ['nome' => 'bob', 'role' => 'admin'],
    ];

    $loginFraudado = ['usuario' => 'alice', 'role' => 'admin'];
    $sessao = autenticar($loginFraudado, $baseDeUsuarios);

    echo "Alice enviou role=admin no login: " . json_encode($sessao) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
