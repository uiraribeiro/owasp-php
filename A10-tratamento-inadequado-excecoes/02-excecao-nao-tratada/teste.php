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

// Teste 1: Vulnerável lança exceção não tratada com quantidade 0
$excecaoLancada = false;
try {
    \Vulneravel\processarPedido(['quantidade' => 0]);
} catch (\Throwable $e) {
    $excecaoLancada = true;
}
verificar(
    'Vulnerável lança exceção não tratada (divisão por zero)',
    $excecaoLancada === true
);

// Teste 2: Corrigido não lança exceção, retorna erro estruturado
$excecaoNaoLancada = true;
$resultado = null;
try {
    $resultado = \Corrigido\processarPedido(['quantidade' => 0]);
} catch (\Throwable $e) {
    $excecaoNaoLancada = false;
}
verificar(
    'Corrigido não lança exceção (captura internamente)',
    $excecaoNaoLancada === true && $resultado !== null
);

// Teste 3: Corrigido retorna status 'erro' quando há problema
verificar(
    'Corrigido retorna status=erro quando quantidade é zero',
    $resultado['status'] === 'erro'
);

// Teste 4: Vulnerável com quantidade válida funciona
$resultado1 = \Vulneravel\processarPedido(['quantidade' => 4]);
verificar(
    'Vulnerável retorna sucesso com quantidade válida',
    $resultado1['status'] === 'ok' && (float)$resultado1['preco_unitario'] === 25.0
);

// Teste 5: Corrigido com quantidade válida funciona
$resultado2 = \Corrigido\processarPedido(['quantidade' => 4]);
verificar(
    'Corrigido retorna sucesso com quantidade válida',
    $resultado2['status'] === 'ok' && (float)$resultado2['preco_unitario'] === 25.0
);

// Teste 6: Corrigido trata quantidade negativa ou ausente
$resultado3 = \Corrigido\processarPedido(['quantidade' => -5]);
$resultado4 = \Corrigido\processarPedido([]);
verificar(
    'Corrigido trata quantidade negativa e ausente como erro',
    $resultado3['status'] === 'erro' && $resultado4['status'] === 'erro'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
