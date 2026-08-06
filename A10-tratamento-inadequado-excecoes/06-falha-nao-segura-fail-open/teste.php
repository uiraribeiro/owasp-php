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

// Teste 1: Vulnerável com null retorna true (fail-open perigoso)
$r1 = \Vulneravel\verificarPermissao(null);
verificar(
    'Vulnerável retorna true quando serviço falha (fail-open perigoso)',
    $r1 === true
);

// Teste 2: Corrigido com null retorna false (fail-safe)
$r2 = \Corrigido\verificarPermissao(null);
verificar(
    'Corrigido retorna false quando serviço falha (fail-safe seguro)',
    $r2 === false
);

// Teste 3: Ambos retornam true quando serviço autoriza
$r3 = \Vulneravel\verificarPermissao(true);
$r4 = \Corrigido\verificarPermissao(true);
verificar(
    'Ambos retornam true quando serviço autoriza',
    $r3 === true && $r4 === true
);

// Teste 4: Ambos retornam false quando serviço nega
$r5 = \Vulneravel\verificarPermissao(false);
$r6 = \Corrigido\verificarPermissao(false);
verificar(
    'Ambos retornam false quando serviço nega',
    $r5 === false && $r6 === false
);

// Teste 5: Comportamento diferente apenas quando serviço falha
$diferemApenasNaFalha = (
    \Vulneravel\verificarPermissao(null) !== \Corrigido\verificarPermissao(null) &&
    \Vulneravel\verificarPermissao(true) === \Corrigido\verificarPermissao(true) &&
    \Vulneravel\verificarPermissao(false) === \Corrigido\verificarPermissao(false)
);
verificar(
    'Diferem apenas quando serviço falha (comportamento normal é idêntico)',
    $diferemApenasNaFalha
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
