<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void {
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

$baseUrl = 'https://example.com/perfil';
$idSessao = 'xyz789abc123def456';

// Teste 1: Vulnerável coloca o ID de sessão na URL
$urlVulneravel = \Vulneravel\montarUrlComSessao($baseUrl, $idSessao);
verificar(
    'Vulnerável coloca ID de sessão na URL',
    str_contains($urlVulneravel, $idSessao)
);

// Teste 2: Vulnerável URL contém exatamente o pattern esperado
verificar(
    'Vulnerável URL tem formato PHPSESSID=...',
    str_contains($urlVulneravel, "PHPSESSID={$idSessao}")
);

// Teste 3: Corrigido NÃO coloca o ID de sessão na URL
$urlCorrigida = \Corrigido\montarUrlComSessao($baseUrl, $idSessao);
verificar(
    'Corrigido NÃO coloca ID de sessão na URL',
    !str_contains($urlCorrigida, $idSessao)
);

// Teste 4: Corrigido URL é apenas a base
verificar(
    'Corrigido URL é apenas a base (sem parâmetros)',
    $urlCorrigida === $baseUrl
);

// Teste 5: Vulnerável cookie não tem httponly
$cookieVulneravel = \Vulneravel\montarCookieDeSessao($idSessao);
verificar(
    'Vulnerável cookie não tem httponly (acessível via JavaScript)',
    $cookieVulneravel['httponly'] === false
);

// Teste 6: Vulnerável cookie não tem secure
verificar(
    'Vulnerável cookie não tem secure (pode ser transmitido via HTTP)',
    $cookieVulneravel['secure'] === false
);

// Teste 7: Corrigido cookie tem httponly
$cookieCorrigido = \Corrigido\montarCookieDeSessao($idSessao);
verificar(
    'Corrigido cookie tem httponly (protegido contra XSS)',
    $cookieCorrigido['httponly'] === true
);

// Teste 8: Corrigido cookie tem secure
verificar(
    'Corrigido cookie tem secure (apenas HTTPS)',
    $cookieCorrigido['secure'] === true
);

// Teste 9: Corrigido cookie tem samesite Strict
verificar(
    'Corrigido cookie tem samesite Strict (protegido contra CSRF)',
    $cookieCorrigido['samesite'] === 'Strict'
);

// Teste 10: Corrigido cookie contém o valor correto
verificar(
    'Corrigido cookie tem o valor de sessão correto',
    $cookieCorrigido['valor'] === $idSessao
);

// Teste 11: Vulnerável cookie contém o valor correto
verificar(
    'Vulnerável cookie tem o valor de sessão correto',
    $cookieVulneravel['valor'] === $idSessao
);

// Teste 12: Nome do cookie é igual em ambos
verificar(
    'Nome do cookie é PHPSESSID em ambos',
    $cookieVulneravel['nome'] === 'PHPSESSID' && $cookieCorrigido['nome'] === 'PHPSESSID'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
