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

// Teste 1: Vulnerável não registra o usuário
$logVulneravel = \Vulneravel\registrarTentativaLogin('joao.silva', '203.0.113.42', 1700000000, false);

verificar(
    'Vulnerável não contém nome de usuário (perda de informação)',
    !str_contains($logVulneravel, 'joao.silva')
);

// Teste 2: Vulnerável não registra o IP
verificar(
    'Vulnerável não contém endereço IP (perda de informação)',
    !str_contains($logVulneravel, '203.0.113.42')
);

// Teste 3: Corrigido registra o usuário
$logCorrigido = \Corrigido\registrarTentativaLogin('joao.silva', '203.0.113.42', 1700000000, false);

verificar(
    'Corrigido contém nome de usuário',
    str_contains($logCorrigido, 'joao.silva')
);

// Teste 4: Corrigido registra o IP
verificar(
    'Corrigido contém endereço IP',
    str_contains($logCorrigido, '203.0.113.42')
);

// Teste 5: Corrigido registra o timestamp
verificar(
    'Corrigido contém timestamp',
    str_contains($logCorrigido, '1700000000')
);

// Teste 6: Corrigido registra o resultado (falha/sucesso)
verificar(
    'Corrigido contém resultado da tentativa',
    str_contains($logCorrigido, 'falha')
);

// Teste 7: Corrigido com sucesso também funciona
$logSucesso = \Corrigido\registrarTentativaLogin('maria.santos', '198.51.100.105', 1700000060, true);

verificar(
    'Corrigido registra sucesso corretamente',
    str_contains($logSucesso, 'maria.santos') && str_contains($logSucesso, 'sucesso')
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
