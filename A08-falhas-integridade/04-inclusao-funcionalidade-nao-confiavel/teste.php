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

$urlExterna = "https://cdn-terceiro.exemplo.com/lib.js";
$hashSriValido = "sha384-abc123xyz789def456ghi789jkl012";

// Teste 1: Vulnerável NÃO inclui SRI
$tag = \Vulneravel\gerarTagScriptExterno($urlExterna, $hashSriValido);
verificar(
    'Vulnerável: tag NOT contém "integrity=" (sem proteção SRI)',
    !str_contains($tag, 'integrity=')
);

// Teste 2: Corrigido gera tag com SRI
try {
    $tag = \Corrigido\gerarTagScriptExterno($urlExterna, $hashSriValido);
    $temSri = str_contains($tag, 'integrity=') && str_contains($tag, $hashSriValido);
    $temCrossorigin = str_contains($tag, 'crossorigin=');

    verificar(
        'Corrigido: tag contém atributo integrity com hash esperado',
        $temSri
    );

    verificar(
        'Corrigido: tag contém atributo crossorigin="anonymous"',
        $temCrossorigin
    );
} catch (\Exception $e) {
    echo "[FALHA] Corrigido lançou exceção inesperada: " . $e->getMessage() . "\n";
}

// Teste 3: Corrigido rejeita sem SRI (null)
$excecaoLancada = false;
try {
    $tag = \Corrigido\gerarTagScriptExterno($urlExterna, null);
} catch (\InvalidArgumentException $e) {
    $excecaoLancada = true;
}

verificar(
    'Corrigido: rejeita script sem SRI com InvalidArgumentException',
    $excecaoLancada
);

// Teste 4: Corrigido rejeita com SRI vazio
$excecaoLancada = false;
try {
    $tag = \Corrigido\gerarTagScriptExterno($urlExterna, '');
} catch (\InvalidArgumentException $e) {
    $excecaoLancada = true;
}

verificar(
    'Corrigido: rejeita script com SRI vazio',
    $excecaoLancada
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
