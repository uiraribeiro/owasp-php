<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: ID de Sessão em Cookie Protegido
 *
 * O id de sessão é transmitido apenas em cookie HTTP-only, impossibilitando
 * acesso via JavaScript e exigindo HTTPS. Não aparece em URLs, logs ou histórico.
 */

function montarUrlComSessao(string $baseUrl, string $idSessao): string {
    // CORRIGIDO: URL SEM nenhum parâmetro de sessão
    // a sessão vai ser enviada apenas via cookie protegido
    return $baseUrl;
}

function montarCookieDeSessao(string $idSessao): array {
    // CORRIGIDO: cookie protegido contra acesso JavaScript e MITM
    return [
        'nome' => 'PHPSESSID',
        'valor' => $idSessao,
        'httponly' => true,  // Impossibilita acesso via JavaScript (previne XSS)
        'secure' => true,    // Apenas transmitido via HTTPS (previne MITM)
        'samesite' => 'Strict',  // Não enviado em requisições cross-site (previne CSRF)
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: ID de Sessão em Cookie Protegido ===\n";

    $baseUrl = 'https://example.com/perfil';
    $idSessao = 'abc123def456ghi789';

    $url = montarUrlComSessao($baseUrl, $idSessao);
    echo "URL (sem sessão): {$url}\n";
    echo "ID de sessão NÃO está na URL\n";

    $cookie = montarCookieDeSessao($idSessao);
    echo "\nCookie de sessão:\n";
    echo "  - httponly: " . ($cookie['httponly'] ? 'true' : 'false') . " (JS não consegue acessar)\n";
    echo "  - secure: " . ($cookie['secure'] ? 'true' : 'false') . " (apenas HTTPS)\n";
    echo "  - samesite: {$cookie['samesite']} (não enviado em requisições cross-site)\n";
    echo "\nPROTEÇÃO: ID de sessão não vaza em URLs, logs ou histórico (protegido!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
