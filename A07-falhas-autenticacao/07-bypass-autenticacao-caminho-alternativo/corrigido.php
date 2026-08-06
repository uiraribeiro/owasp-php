<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Autenticação Centralizada para Todos os Caminhos
 *
 * Independentemente da rota (nova ou legacy), a autenticação é validada
 * por uma única função central. Nenhum caminho alternativo consegue contornar.
 */

function tratarRequisicao(string $rota, ?array $sessaoAutenticada): array {
    // CORRIGIDO: verificação centralizada de autenticação ANTES de tudo
    // Todas as rotas precisam passar por aqui
    if ($sessaoAutenticada === null) {
        return [
            'status' => 401,
            'corpo' => ['erro' => 'não autenticado'],
        ];
    }

    // Após verificar autenticação, roteia para a lógica específica
    if ($rota === '/api/v1/perfil') {
        return [
            'status' => 200,
            'corpo' => ['usuario' => $sessaoAutenticada['usuario'], 'email' => $sessaoAutenticada['email']],
        ];
    }

    if ($rota === '/api/legacy/perfil') {
        // CORRIGIDO: mesma exigência de autenticação que v1
        // a verificação já aconteceu no início, então aqui podemos confiar que está autenticado
        return [
            'status' => 200,
            'corpo' => ['usuario' => $sessaoAutenticada['usuario'], 'email' => $sessaoAutenticada['email']],
        ];
    }

    return [
        'status' => 404,
        'corpo' => ['erro' => 'rota não encontrada'],
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Autenticação centralizada ===\n";

    // Sem autenticação, rota v1 bloqueia
    $resultaNovaBloqueia = tratarRequisicao('/api/v1/perfil', null);
    echo "Sem autenticação, /api/v1/perfil: " . $resultaNovaBloqueia['status'] . " (bloqueado)\n";

    // Agora rota legacy também bloqueia
    $resultaLegacyProtegida = tratarRequisicao('/api/legacy/perfil', null);
    echo "Sem autenticação, /api/legacy/perfil: " . $resultaLegacyProtegida['status'] . " (bloqueado!)\n";

    // Com autenticação, ambas funcionam
    $sessao = ['usuario' => 'joao', 'email' => 'joao@example.com'];
    $resultaNovaOk = tratarRequisicao('/api/v1/perfil', $sessao);
    $resultaLegacyOk = tratarRequisicao('/api/legacy/perfil', $sessao);
    echo "Com autenticação, /api/v1/perfil: " . $resultaNovaOk['status'] . " (permitido)\n";
    echo "Com autenticação, /api/legacy/perfil: " . $resultaLegacyOk['status'] . " (permitido)\n";
}

if (debug_backtrace() === []) {
    demo();
}
