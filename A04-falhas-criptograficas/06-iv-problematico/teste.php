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
$chave = 'chave-teste-123';
$texto = 'Bloco de dados sensivel teste';

// Teste 1: Vulnerável (IV fixo) produz o MESMO resultado duas vezes
$cifra1_vuln = \Vulneravel\criptografarCbc($texto, $chave);
$cifra2_vuln = \Vulneravel\criptografarCbc($texto, $chave);

verificar(
    'CBC vulnerável (IV fixo) produz cifras IDENTICAS',
    bin2hex($cifra1_vuln) === bin2hex($cifra2_vuln)
);

// Teste 2: Vulnerável consegue descriptografar
$decriptado_vuln = \Vulneravel\descriptografarCbc($cifra1_vuln, $chave);
verificar(
    'CBC vulnerável consegue descriptografar',
    $decriptado_vuln === $texto
);

// Teste 3: Corrigido (IV aleatório) produz resultados DIFERENTES
$cifra1_corr = \Corrigido\criptografarCbc($texto, $chave);
$cifra2_corr = \Corrigido\criptografarCbc($texto, $chave);

verificar(
    'CBC corrigido (IV aleatório) produz cifras DIFERENTES',
    bin2hex($cifra1_corr) !== bin2hex($cifra2_corr)
);

// Teste 4: Corrigido consegue descriptografar resultado 1
$decriptado_corr1 = \Corrigido\descriptografarCbc($cifra1_corr, $chave);
verificar(
    'CBC corrigido consegue descriptografar resultado 1',
    $decriptado_corr1 === $texto
);

// Teste 5: Corrigido consegue descriptografar resultado 2
$decriptado_corr2 = \Corrigido\descriptografarCbc($cifra2_corr, $chave);
verificar(
    'CBC corrigido consegue descriptografar resultado 2',
    $decriptado_corr2 === $texto
);

// Teste 6: Corrigido tem IV prefixado (resultado é maior que vulnerável)
verificar(
    'CBC corrigido tem resultado maior (IV prefixado)',
    strlen($cifra1_corr) > strlen($cifra1_vuln)
);

// Teste 7: Verificar que os primeiros 16 bytes do corrigido são diferentes (IV aleatório)
$iv1 = substr($cifra1_corr, 0, 16);
$iv2 = substr($cifra2_corr, 0, 16);
verificar(
    'IVs prefixados são DIFERENTES (aleatórios)',
    $iv1 !== $iv2
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
