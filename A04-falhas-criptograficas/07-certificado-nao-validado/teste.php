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

// Testes (SEM fazer requisições HTTP reais)
// Apenas verificação estática das opções retornadas

$url = 'https://api.exemplo.com/dados';

// Teste 1: Vulnerável tem SSL_VERIFYPEER desabilitado
$opcoes_vuln = \Vulneravel\criarOpcoesCurl($url);
verificar(
    'Opções vulneráveis têm SSL_VERIFYPEER = false',
    $opcoes_vuln[CURLOPT_SSL_VERIFYPEER] === false
);

// Teste 2: Vulnerável tem SSL_VERIFYHOST desabilitado
verificar(
    'Opções vulneráveis têm SSL_VERIFYHOST = 0',
    $opcoes_vuln[CURLOPT_SSL_VERIFYHOST] === 0
);

// Teste 3: Corrigido tem SSL_VERIFYPEER habilitado
$opcoes_corr = \Corrigido\criarOpcoesCurl($url);
verificar(
    'Opções corrigidas têm SSL_VERIFYPEER = true',
    $opcoes_corr[CURLOPT_SSL_VERIFYPEER] === true
);

// Teste 4: Corrigido tem SSL_VERIFYHOST correto
verificar(
    'Opções corrigidas têm SSL_VERIFYHOST = 2',
    $opcoes_corr[CURLOPT_SSL_VERIFYHOST] === 2
);

// Teste 5: Ambos têm URL configurada
verificar(
    'Opções vulneráveis têm URL configurada',
    isset($opcoes_vuln['CURLOPT_URL']) && $opcoes_vuln['CURLOPT_URL'] === $url
);

verificar(
    'Opções corrigidas têm URL configurada',
    isset($opcoes_corr['CURLOPT_URL']) && $opcoes_corr['CURLOPT_URL'] === $url
);

// Teste 6: Corrigido tem CAINFO configurado (opcional mas recomendado)
verificar(
    'Opções corrigidas têm CAINFO configurado (CA bundle)',
    isset($opcoes_corr[CURLOPT_CAINFO])
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
