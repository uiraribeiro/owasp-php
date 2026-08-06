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

// Teste 1: Vulnerável aceita preço fraudado (muito baixo)
$carrinhoFraudado = [
    ['produto_id' => 1, 'preco' => 0.01, 'qtd' => 2],
    ['produto_id' => 2, 'preco' => 0.01, 'qtd' => 1],
];
$totalVulneravel = \Vulneravel\calcularTotalCompra($carrinhoFraudado);
verificar(
    'Vulnerável calcula total baseado em preço fraudado (0.02 esperado)',
    abs($totalVulneravel - 0.03) < 0.01  // 0.01*2 + 0.01*1 = 0.03
);

// Teste 2: Corrigido calcula preço real (ignora preço do cliente)
$totalCorrigido = \Corrigido\calcularTotalCompra($carrinhoFraudado);
$precoRealEsperado = 100.0 * 2 + 50.0 * 1; // 250.0
verificar(
    'Corrigido calcula baseado no catálogo (R$250 esperado)',
    abs($totalCorrigido - $precoRealEsperado) < 0.01
);

// Teste 3: Corrigido com carrinho legítimo (sem campo preco)
$carrinhoLegitimo = [
    ['produto_id' => 1, 'qtd' => 2],
    ['produto_id' => 2, 'qtd' => 1],
];
$totalLegitimo = \Corrigido\calcularTotalCompra($carrinhoLegitimo);
verificar(
    'Corrigido funciona com carrinho legítimo (sem preço no cliente)',
    abs($totalLegitimo - $precoRealEsperado) < 0.01
);

// Teste 4: Vulnerável com carrinho legítimo (também funciona)
$totalVulneravelLegitimo = \Vulneravel\calcularTotalCompra($carrinhoLegitimo);
verificar(
    'Vulnerável retorna 0 quando campo preco não está presente',
    $totalVulneravelLegitimo === 0.0
);

// Teste 5: Corrigido rejeita produto inválido
$carrinhoInvalido = [
    ['produto_id' => 999, 'qtd' => 1],  // Produto não existe
];
$totalInvalido = \Corrigido\calcularTotalCompra($carrinhoInvalido);
verificar(
    'Corrigido rejeita produto não catalogado',
    $totalInvalido === 0.0
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
