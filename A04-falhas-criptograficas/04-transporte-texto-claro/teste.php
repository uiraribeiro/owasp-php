<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void
{
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

// Testes
$host = 'api.exemplo.com';
$usuario = 'joao@email.com';
$senha = 'senha123';

// Teste 1: Vulnerável usa HTTP
$req_vuln = \Vulneravel\montarRequisicaoLogin($host, $usuario, $senha);
verificar(
    'Requisição vulnerável usa HTTP (não HTTPS)',
    strpos($req_vuln['url'], 'http://') === 0
);

// Teste 2: Vulnerável coloca senha na query string (URL)
verificar(
    'Requisição vulnerável coloca senha=... na URL',
    strpos($req_vuln['url'], 'senha=') !== false
);

// Teste 3: Vulnerável coloca usuario na query string (URL)
verificar(
    'Requisição vulnerável coloca usuario=... na URL',
    strpos($req_vuln['url'], 'usuario=') !== false
);

// Teste 4: Vulnerável usa GET
verificar(
    'Requisição vulnerável usa método GET',
    $req_vuln['metodo'] === 'GET'
);

// Teste 5: Corrigido usa HTTPS
$req_corr = \Corrigido\montarRequisicaoLogin($host, $usuario, $senha);
verificar(
    'Requisição corrigida usa HTTPS',
    strpos($req_corr['url'], 'https://') === 0
);

// Teste 6: Corrigido NÃO coloca senha na URL
verificar(
    'Requisição corrigida NÃO coloca senha na URL',
    strpos($req_corr['url'], 'senha=') === false
);

// Teste 7: Corrigido NÃO coloca usuario na URL
verificar(
    'Requisição corrigida NÃO coloca usuario na URL',
    strpos($req_corr['url'], 'usuario=') === false
);

// Teste 8: Corrigido coloca credenciais no corpo
verificar(
    'Requisição corrigida tem credenciais no corpo',
    is_array($req_corr['corpo']) &&
    isset($req_corr['corpo']['usuario']) &&
    isset($req_corr['corpo']['senha']) &&
    $req_corr['corpo']['usuario'] === $usuario &&
    $req_corr['corpo']['senha'] === $senha
);

// Teste 9: Corrigido usa POST
verificar(
    'Requisição corrigida usa método POST',
    $req_corr['metodo'] === 'POST'
);

// Teste 10: URL corrigida é apenas https://host/login (sem query string)
verificar(
    'URL corrigida é apenas https://host/login (sem credenciais na URL)',
    $req_corr['url'] === "https://{$host}/login"
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
