<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Recursos e Funcionalidades Desnecessárias Expostas - A02:2025 Security Misconfiguration
 *
 * Endpoints de debug (phpinfo, profiler, console) estão ativos em produção.
 * Atacante pode acessar informações sensíveis sobre versão PHP, extensões, caminho do servidor.
 */

function tratarRota(string $rota, string $ambiente): array {
    // VULNERÁVEL: endpoints de debug sempre ativados, independente do ambiente
    if ($rota === '/debug/phpinfo') {
        return [
            'status' => 200,
            'corpo' => 'PHP Version: 8.5.8 | OS: Linux | Extensions: PDO, XML, JSON, OpenSSL',
        ];
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
    echo "=== VULNERÁVEL: Endpoints de debug expostos em produção ===\n";

    $resposta = tratarRota('/debug/phpinfo', 'producao');
    echo "Status: {$resposta['status']}\n";
    echo "Corpo: {$resposta['corpo']}\n";
    if ($resposta['status'] === 200) {
        echo "Informações sensíveis expostas em produção (PROBLEMA!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
