<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Transmissão SEGURA de dados sensíveis (HTTPS + corpo da requisição)
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: Usa HTTPS (criptografado) e coloca credenciais no corpo (POST).
 * Credenciais não aparecem em logs de URL, histórico do navegador, referer headers.
 */

function montarRequisicaoLogin(string $host, string $usuario, string $senha): array
{
    // USE HTTPS PARA QUALQUER DADO SENSÍVEL!
    // USE POST COM CORPO PARA CREDENCIAIS, NUNCA QUERY STRING!
    return [
        'metodo' => 'POST',
        'url' => "https://{$host}/login",
        'corpo' => [
            'usuario' => $usuario,
            'senha' => $senha,
        ],
    ];
}

function demo(): void
{
    echo "=== Requisição de Login SEGURA ===\n";
    $req = montarRequisicaoLogin('api.exemplo.com', 'joao@email.com', 'senha123');

    echo "Método: " . $req['metodo'] . "\n";
    echo "URL: " . $req['url'] . "\n";
    echo "Corpo: " . json_encode($req['corpo']) . "\n";
    echo "\nBENEFÍCIOS:\n";
    echo "1. HTTPS criptografa tudo em trânsito\n";
    echo "2. Credenciais no corpo - não aparecem em logs de URL\n";
    echo "3. POST é padrão para dados sensíveis\n";
}

if (debug_backtrace() === []) {
    demo();
}
