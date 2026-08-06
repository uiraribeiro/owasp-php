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

// Teste 1: Vulnerável perde dinheiro quando interrompido
$contas1 = ['A' => 100.0, 'B' => 50.0];
$r1 = \Vulneravel\transferir($contas1, 'A', 'B', 30.0, true);
$totalVulneravel = $r1['contas']['A'] + $r1['contas']['B'];
verificar(
    'Vulnerável perde dinheiro (total muda de 150 para ' . $totalVulneravel . ')',
    $totalVulneravel < 150.0
);

// Teste 2: Vulnerável gera perda monetária específica (30 desaparece)
$perda = 150.0 - $totalVulneravel;
verificar(
    'Vulnerável perde exatamente o valor transferido (30)',
    abs($perda - 30.0) < 0.01
);

// Teste 3: Corrigido restaura estado com rollback
$contas2 = ['A' => 100.0, 'B' => 50.0];
$r2 = \Corrigido\transferir($contas2, 'A', 'B', 30.0, true);
$totalCorrigido = $r2['contas']['A'] + $r2['contas']['B'];
verificar(
    'Corrigido mantém total intacto com rollback (continua 150)',
    abs($totalCorrigido - 150.0) < 0.01
);

// Teste 4: Corrigido retorna ao estado original após falha
$saldoOrigemCorrigido = $r2['contas']['A'];
verificar(
    'Corrigido restaura saldo original da origem (100)',
    abs($saldoOrigemCorrigido - 100.0) < 0.01
);

// Teste 5: Ambos completam transferência sem interrupção
$contas3 = ['A' => 100.0, 'B' => 50.0];
$contas4 = ['A' => 100.0, 'B' => 50.0];
$r3 = \Vulneravel\transferir($contas3, 'A', 'B', 30.0, false);
$r4 = \Corrigido\transferir($contas4, 'A', 'B', 30.0, false);
$transferenciaOk = $r3['status'] === 'sucesso' && $r4['status'] === 'sucesso' &&
                   $r3['contas']['A'] === 70.0 && $r4['contas']['A'] === 70.0 &&
                   $r3['contas']['B'] === 80.0 && $r4['contas']['B'] === 80.0;
verificar(
    'Ambos completam transferência sem interrupção (A=70, B=80, total=150)',
    $transferenciaOk
);

// Teste 6: Corrigido retorna status 'erro' quando falha
verificar(
    'Corrigido retorna status=erro quando transação é revertida',
    $r2['status'] === 'erro'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
