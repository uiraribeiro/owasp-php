<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Falta de Controle de Acesso em API (A01:2025 - Broken Access Control)
 *
 * O servidor verifica apenas autenticação (se $usuario não é vazio)
 * para todos os métodos HTTP, inclusive DELETE e PUT que alteram estado.
 * Um usuário comum autenticado pode deletar qualquer recurso.
 */

function tratarRequisicaoApi(string $metodo, array $usuario): array {
    // VULNERÁVEL: aceita DELETE/PUT apenas com autenticação genérica
    if (empty($usuario)) {
        return [
            'status' => 401,
            'mensagem' => 'Não autenticado',
        ];
    }

    // Qualquer método para qualquer usuário autenticado
    if ($metodo === 'DELETE' || $metodo === 'PUT') {
        return [
            'status' => 200,
            'mensagem' => "Operação {$metodo} realizada com sucesso",
        ];
    }

    if ($metodo === 'GET') {
        return [
            'status' => 200,
            'mensagem' => 'Dados retornados com sucesso',
        ];
    }

    return [
        'status' => 400,
        'mensagem' => 'Método não suportado',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Falta de Controle em API ===\n";

    $usuarioComum = ['id' => 1, 'role' => 'user'];

    $respostaDelete = tratarRequisicaoApi('DELETE', $usuarioComum);
    echo "Usuário comum DELETE: " . json_encode($respostaDelete) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
