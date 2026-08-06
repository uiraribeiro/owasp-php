<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Implementar Controle de Acesso por Método HTTP
 *
 * GET (leitura) requer apenas autenticação.
 * POST, PUT, DELETE (escrita) requerem autenticação E role='admin'.
 */

function tratarRequisicaoApi(string $metodo, array $usuario): array {
    // Primeiro: verifica se está autenticado
    if (empty($usuario)) {
        return [
            'status' => 401,
            'mensagem' => 'Não autenticado',
        ];
    }

    // GET: apenas autenticado é suficiente
    if ($metodo === 'GET') {
        return [
            'status' => 200,
            'mensagem' => 'Dados retornados com sucesso',
        ];
    }

    // DELETE, PUT, POST: requerem admin além de autenticado
    if ($metodo === 'DELETE' || $metodo === 'PUT' || $metodo === 'POST') {
        if ($usuario['role'] !== 'admin') {
            return [
                'status' => 403,
                'mensagem' => 'Acesso negado: privilégios insuficientes',
            ];
        }

        return [
            'status' => 200,
            'mensagem' => "Operação {$metodo} realizada com sucesso",
        ];
    }

    return [
        'status' => 400,
        'mensagem' => 'Método não suportado',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Controle de Acesso por Método ===\n";

    $usuarioComum = ['id' => 1, 'role' => 'user'];
    $usuarioAdmin = ['id' => 2, 'role' => 'admin'];

    $respostaDelete = tratarRequisicaoApi('DELETE', $usuarioComum);
    echo "Usuário comum DELETE: " . json_encode($respostaDelete) . " (bloqueado!)\n";

    $respostaDeleteAdmin = tratarRequisicaoApi('DELETE', $usuarioAdmin);
    echo "Admin DELETE: " . json_encode($respostaDeleteAdmin) . " (permitido)\n";
}

if (debug_backtrace() === []) {
    demo();
}
