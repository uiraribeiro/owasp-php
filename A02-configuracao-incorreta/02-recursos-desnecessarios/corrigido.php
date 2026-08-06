<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Desabilitar Recursos de Debug em Produção
 *
 * Endpoints sensíveis só estão disponíveis em desenvolvimento.
 * Em produção, retornam 404 para evitar exposição de informações sensíveis.
 */

function tratarRota(string $rota, string $ambiente): array {
    // CORRIGIDO: endpoints de debug só estão ativos em desenvolvimento
    if ($rota === '/debug/phpinfo') {
        if ($ambiente === 'desenvolvimento') {
            return [
                'status' => 200,
                'corpo' => 'PHP Version: 8.5.8 | OS: Linux | Extensions: PDO, XML, JSON, OpenSSL',
            ];
        } else {
            // Em produção, simula como se a rota não existisse
            return [
                'status' => 404,
                'corpo' => 'Rota não encontrada',
            ];
        }
    }

    if ($rota === '/') {
        return [
            'status' => 200,
            'corpo' => 'Página inicial',
        ];
    }

    return [
        'status' => 404,
        'corpo' => 'Rota não encontrada',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Debug apenas em desenvolvimento ===\n";

    // Em produção
    $respostaProd = tratarRota('/debug/phpinfo', 'producao');
    echo "Produção - Status: {$respostaProd['status']}\n";
    if ($respostaProd['status'] === 404) {
        echo "Endpoint de debug bloqueado em produção (protegido!)\n";
    }

    // Em desenvolvimento
    $respostaDev = tratarRota('/debug/phpinfo', 'desenvolvimento');
    echo "Desenvolvimento - Status: {$respostaDev['status']}\n";
    if ($respostaDev['status'] === 200) {
        echo "Endpoint de debug ativo em desenvolvimento (útil para diagnóstico)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
