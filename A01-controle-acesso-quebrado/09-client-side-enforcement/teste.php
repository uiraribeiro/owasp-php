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

// Teste 1: Vulnerável aceita transferência acima do limite com flag do client
$transferenciaMaliciosa = ['valor' => 999999.0, 'limiteVerificadoPeloClient' => true];
$resultadoVulneravel = \Vulneravel\processarTransferencia($transferenciaMaliciosa);
verificar(
    'Vulnerável aceita transferência acima do limite se cliente disse que validou',
    $resultadoVulneravel['sucesso'] === true && $resultadoVulneravel['valor_processado'] == 999999.0
);

// Teste 2: Corrigido rejeita transferência acima do limite (2000)
$resultadoCorrigido = \Corrigido\processarTransferencia($transferenciaMaliciosa);
verificar(
    'Corrigido rejeita transferência acima do limite de R$2000',
    $resultadoCorrigido['sucesso'] === false && strpos($resultadoCorrigido['mensagem'], 'Limite') !== false
);

// Teste 3: Corrigido rejeita transferência acima do saldo (1500)
$transferenciaSaldoInsuficiente = ['valor' => 2000.0];  // Saldo é 1500, limite é 2000, então vai ser rejeitado
$resultadoSaldo = \Corrigido\processarTransferencia($transferenciaSaldoInsuficiente);
verificar(
    'Corrigido rejeita transferência acima do saldo disponível (R$1500)',
    $resultadoSaldo['sucesso'] === false && strpos($resultadoSaldo['mensagem'], 'Saldo') !== false
);

// Teste 4: Corrigido aceita transferência legítima
$transferenciaLegitima = ['valor' => 500.0];
$resultadoLegitima = \Corrigido\processarTransferencia($transferenciaLegitima);
verificar(
    'Corrigido aceita transferência legítima dentro dos limites',
    $resultadoLegitima['sucesso'] === true && $resultadoLegitima['valor_processado'] == 500.0
);

// Teste 5: Vulnerável também aceita transferência legítima
$resultadoVulneravelLegitimo = \Vulneravel\processarTransferencia($transferenciaLegitima);
verificar(
    'Vulnerável aceita transferência legítima (sem flag cliente)',
    $resultadoVulneravelLegitimo['sucesso'] === true && $resultadoVulneravelLegitimo['valor_processado'] == 500.0
);

// Teste 6: Corrigido rejeita valor negativo
$transferenciaZero = ['valor' => -100.0];
$resultadoZero = \Corrigido\processarTransferencia($transferenciaZero);
verificar(
    'Corrigido rejeita transferência com valor negativo',
    $resultadoZero['sucesso'] === false && strpos($resultadoZero['mensagem'], 'maior que zero') !== false
);

// Teste 7: Transferência no limite exato (2000) mas ainda dentro do saldo
$transferenciaLimite = ['valor' => 1400.0];
$resultadoLimite = \Corrigido\processarTransferencia($transferenciaLimite);
verificar(
    'Corrigido aceita transferência dentro do limite e saldo',
    $resultadoLimite['sucesso'] === true && $resultadoLimite['valor_processado'] == 1400.0
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
