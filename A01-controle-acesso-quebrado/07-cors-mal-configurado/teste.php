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

$origemLegitima = 'https://meusite.com';
$origemMaliciosa = 'https://atacante.com';

// Teste 1: Vulnerável permite origem maliciosa
$headersVulneravel = \Vulneravel\gerarCabecalhosCors($origemMaliciosa);
verificar(
    'Vulnerável reflete origem maliciosa no header CORS',
    isset($headersVulneravel['Access-Control-Allow-Origin']) &&
    $headersVulneravel['Access-Control-Allow-Origin'] === $origemMaliciosa
);

// Teste 2: Vulnerável permite credentials para qualquer origem
verificar(
    'Vulnerável permite credentials=true para qualquer origem',
    isset($headersVulneravel['Access-Control-Allow-Credentials']) &&
    $headersVulneravel['Access-Control-Allow-Credentials'] === 'true'
);

// Teste 3: Corrigido bloqueia origem maliciosa
$headersCorrigido = \Corrigido\gerarCabecalhosCors($origemMaliciosa);
verificar(
    'Corrigido não inclui headers CORS para origem maliciosa (array vazio)',
    empty($headersCorrigido)
);

// Teste 4: Corrigido permite origem legítima
$headersCorrigidoLegitimo = \Corrigido\gerarCabecalhosCors($origemLegitima);
verificar(
    'Corrigido permite origem confiável (inclui CORS headers)',
    isset($headersCorrigidoLegitimo['Access-Control-Allow-Origin']) &&
    $headersCorrigidoLegitimo['Access-Control-Allow-Origin'] === $origemLegitima
);

// Teste 5: Corrigido permite credentials só para origem confiável
verificar(
    'Corrigido permite credentials=true APENAS para origem confiável',
    isset($headersCorrigidoLegitimo['Access-Control-Allow-Credentials']) &&
    $headersCorrigidoLegitimo['Access-Control-Allow-Credentials'] === 'true'
);

// Teste 6: Corrigido bloqueia origin genérica/wildcard
$headerWildcard = \Corrigido\gerarCabecalhosCors('*');
verificar(
    'Corrigido bloqueia origem wildcard (*)',
    empty($headerWildcard)
);

// Teste 7: Corrigido permite segunda origem confiável
$origemConfiada2 = 'https://app.meusite.com';
$headersConfiada2 = \Corrigido\gerarCabecalhosCors($origemConfiada2);
verificar(
    'Corrigido permite segunda origem confiável da allow-list',
    isset($headersConfiada2['Access-Control-Allow-Origin']) &&
    $headersConfiada2['Access-Control-Allow-Origin'] === $origemConfiada2
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
