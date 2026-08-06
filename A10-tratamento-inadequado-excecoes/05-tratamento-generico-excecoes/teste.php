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

// Teste 1: Vulnerável retorna o MESMO status para diferentes erros
$r1 = \Vulneravel\processarPagamento(50.0, 100.0, true);   // Erro de conexão
$r2 = \Vulneravel\processarPagamento(150.0, 100.0, false); // Saldo insuficiente
$mesmoStatus = $r1['status'] === $r2['status'] && $r1['status'] === 'erro';
verificar(
    'Vulnerável retorna status=erro para ambos os tipos de erro (distinção perdida)',
    $mesmoStatus
);

// Teste 2: Corrigido retorna status DIFERENTE para cada tipo de erro
$r3 = \Corrigido\processarPagamento(50.0, 100.0, true);   // Erro de conexão
$r4 = \Corrigido\processarPagamento(150.0, 100.0, false); // Saldo insuficiente
$statusDiferentes = $r3['status'] !== $r4['status'];
verificar(
    'Corrigido retorna status diferentes para cada tipo de erro',
    $statusDiferentes
);

// Teste 3: Corrigido retorna 'tentar_novamente' para erro de conexão
$r5 = \Corrigido\processarPagamento(50.0, 100.0, true);
verificar(
    'Corrigido retorna tentar_novamente para erro de conexão',
    $r5['status'] === 'tentar_novamente'
);

// Teste 4: Corrigido retorna 'recusado' para saldo insuficiente
$r6 = \Corrigido\processarPagamento(150.0, 100.0, false);
verificar(
    'Corrigido retorna recusado para saldo insuficiente',
    $r6['status'] === 'recusado'
);

// Teste 5: Ambos retornam 'aprovado' para pagamento válido
$r7 = \Vulneravel\processarPagamento(50.0, 100.0, false);
$r8 = \Corrigido\processarPagamento(50.0, 100.0, false);
verificar(
    'Ambos retornam status=aprovado para pagamento válido',
    $r7['status'] === 'aprovado' && $r8['status'] === 'aprovado'
);

// Teste 6: Corrigido fornece motivos diferentes
$r9 = \Corrigido\processarPagamento(50.0, 100.0, true);
$r10 = \Corrigido\processarPagamento(150.0, 100.0, false);
$motivosDiferentes = $r9['motivo'] !== $r10['motivo'];
verificar(
    'Corrigido fornece motivos descritivos diferentes para cada erro',
    $motivosDiferentes
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
