<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Sempre Buscar Role do Servidor, Nunca do Cliente
 *
 * Ignora completamente qualquer campo 'role' enviado pelo cliente.
 * A role vem sempre do banco de dados do servidor.
 */

function autenticar(array $dadosLogin, array $baseDeUsuarios): array {
    $nomeUsuario = $dadosLogin['usuario'] ?? '';

    if (!$nomeUsuario || empty($baseDeUsuarios)) {
        return [
            'autenticado' => false,
            'mensagem' => 'Falha na autenticação',
        ];
    }

    // Busca usuário na base de dados
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

    // CORRIGIDO: SEMPRE usa a role do banco de dados, NUNCA do cliente
    $role = $usuarioEncontrado['role'];

    return [
        'autenticado' => true,
        'usuario' => $nomeUsuario,
        'role' => $role,
        'mensagem' => 'Login bem-sucedido',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Escalação de Privilégio Bloqueada ===\n";

    $baseDeUsuarios = [
        ['nome' => 'alice', 'role' => 'user'],
        ['nome' => 'bob', 'role' => 'admin'],
    ];

    $loginFraudado = ['usuario' => 'alice', 'role' => 'admin'];
    $sessao = autenticar($loginFraudado, $baseDeUsuarios);

    echo "Alice enviou role=admin no login, mas recebeu: " . json_encode($sessao) . " (bloqueado!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
