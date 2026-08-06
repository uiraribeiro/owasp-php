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
$texto = 'Dados confidenciais';

// Teste 1: Vulnerável (RC4) produz o MESMO resultado duas vezes (determinístico)
$cifra1_vuln = \Vulneravel\criptografar($texto, $chave);
$cifra2_vuln = \Vulneravel\criptografar($texto, $chave);
verificar(
    'RC4 vulnerável produz cifras IDENTICAS (mesmo IV/determinismos)',
    bin2hex($cifra1_vuln) === bin2hex($cifra2_vuln)
);

// Teste 2: Corrigido (AES-256-GCM) produz resultados DIFERENTES (IV aleatório)
$cifra1_corr = \Corrigido\criptografar($texto, $chave);
$cifra2_corr = \Corrigido\criptografar($texto, $chave);
verificar(
    'AES-256-GCM corrigido produz cifras DIFERENTES (IV aleatório)',
    bin2hex($cifra1_corr) !== bin2hex($cifra2_corr)
);

// Teste 3: Corrigido consegue descriptografar corretamente
$decriptado1 = \Corrigido\descriptografar($cifra1_corr, $chave);
verificar(
    'AES-256-GCM consegue descriptografar de volta ao texto original (1)',
    $decriptado1 === $texto
);

$decriptado2 = \Corrigido\descriptografar($cifra2_corr, $chave);
verificar(
    'AES-256-GCM consegue descriptografar de volta ao texto original (2)',
    $decriptado2 === $texto
);

// Teste 4: Vulnerável consegue descriptografar (mas usa algoritmo fraco)
$decriptado_vuln = \Vulneravel\descriptografar($cifra1_vuln, $chave);
verificar(
    'RC4 vulnerável consegue descriptografar (por enquanto)',
    $decriptado_vuln === $texto
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
