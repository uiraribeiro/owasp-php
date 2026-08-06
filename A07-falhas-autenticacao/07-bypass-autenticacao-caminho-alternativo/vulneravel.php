<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Bypass de Autenticação em Caminho Alternativo (CWE-288) - A07:2025 Authentication Failures
 *
 * A aplicação implementa autenticação em uma rota nova (/api/v1/perfil), mas mantém
 * uma rota antiga (/api/legacy/perfil) "por compatibilidade" que não verifica autenticação.
 * Um atacante descobre esse caminho alternativo e contorna completamente a autenticação.
 */

function tratarRequisicao(string $rota, ?array $sessaoAutenticada): array {
    // VULNERÁVEL: apenas a rota nova tem checagem de autenticação
    // a rota legacy foi esquecida, permitindo bypass

    if ($rota === '/api/v1/perfil') {
        // Rota nova com autenticação
        if ($sessaoAutenticada === null) {
            return [
                'status' => 401,
                'corpo' => ['erro' => 'não autenticado'],
            ];
        }
        return [
            'status' => 200,
            'corpo' => ['usuario' => $sessaoAutenticada['usuario'], 'email' => $sessaoAutenticada['email']],
        ];
    }

    if ($rota === '/api/legacy/perfil') {
        // VULNERÁVEL: rota legacy sem verificação de autenticação!
        // retorna sempre 200, mesmo sem autenticação
        return [
            'status' => 200,
            'corpo' => ['usuario' => 'usuario_padrao', 'email' => 'user@example.com'],
        ];
    }

    return [
        'status' => 404,
        'corpo' => ['erro' => 'rota não encontrada'],
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Bypass via rota legacy ===\n";

    // Sem autenticação, rota nova bloqueia
    $resultaNovaBloqueia = tratarRequisicao('/api/v1/perfil', null);
    echo "Sem autenticação, /api/v1/perfil: " . $resultaNovaBloqueia['status'] . " (bloqueado)\n";

    // Mas rota legacy não bloqueia!
    $resultaLegacyBypass = tratarRequisicao('/api/legacy/perfil', null);
    echo "Sem autenticação, /api/legacy/perfil: " . $resultaLegacyBypass['status'] . " (PROBLEMA! contorno!)\n";
    echo "Atacante conseguiu acessar dados sem autenticação via rota alternativa\n";
}

if (debug_backtrace() === []) {
    demo();
}
