<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Transmissão de dados sensíveis em texto claro (HTTP + credenciais na URL)
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: Usa HTTP (não criptografado) e coloca credenciais na query string.
 * Credenciais aparecem em: logs do servidor, histórico do navegador,
 * proxies HTTP, caches, referer headers, etc.
 */

function montarRequisicaoLogin(string $host, string $usuario, string $senha): array
{
    // NUNCA USE HTTP PARA CREDENCIAIS!
    // NUNCA COLOQUE CREDENCIAIS NA URL!
    return [
        'metodo' => 'GET',
        'url' => "http://{$host}/login?usuario={$usuario}&senha={$senha}",
        'corpo' => null,
    ];
}

function demo(): void
{
    echo "=== Requisição de Login VULNERÁVEL ===\n";
    $req = montarRequisicaoLogin('api.exemplo.com', 'joao@email.com', 'senha123');

    echo "Método: " . $req['metodo'] . "\n";
    echo "URL: " . $req['url'] . "\n";
    echo "Corpo: " . ($req['corpo'] ?? 'vazio') . "\n";
    echo "\nPROBLEMAS:\n";
    echo "1. HTTP sem criptografia - qualquer um pode ver a senha\n";
    echo "2. Senha na query string - aparece em logs, histórico, referer header\n";
}

if (debug_backtrace() === []) {
    demo();
}
