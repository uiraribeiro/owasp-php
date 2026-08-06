<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * ID de Sessão em Local Inseguro - A07:2025 Authentication Failures
 *
 * O servidor transmite o id de sessão pela URL como parâmetro GET.
 * Isso causa vazamento em: logs de servidor, cache de proxy, histórico do navegador,
 * cabeçalho Referer, links compartilhados. Um atacante pode capturar o id e sequestrar
 * a sessão.
 */

function montarUrlComSessao(string $baseUrl, string $idSessao): string {
    // VULNERÁVEL: coloca o id de sessão na URL como parâmetro
    // aparece em logs de servidor, histórico do navegador, Referer headers, etc.
    return "{$baseUrl}?PHPSESSID={$idSessao}";
}

function montarCookieDeSessao(string $idSessao): array {
    // VULNERÁVEL: retorna um cookie sem proteções (apenas para comparação)
    return [
        'nome' => 'PHPSESSID',
        'valor' => $idSessao,
        'httponly' => false,
        'secure' => false,
        'samesite' => 'None',
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: ID de Sessão na URL ===\n";

    $baseUrl = 'https://example.com/perfil';
    $idSessao = 'abc123def456ghi789';

    $url = montarUrlComSessao($baseUrl, $idSessao);
    echo "URL com sessão: {$url}\n";
    echo "PROBLEMA: ID de sessão está visível na URL!\n";
    echo "  - Aparece em logs de servidor\n";
    echo "  - Aparece no histórico do navegador\n";
    echo "  - Aparece no cabeçalho Referer quando o usuário clica num link\n";
    echo "  - Pode ser capturado por atacantes\n";
}

if (debug_backtrace() === []) {
    demo();
}
