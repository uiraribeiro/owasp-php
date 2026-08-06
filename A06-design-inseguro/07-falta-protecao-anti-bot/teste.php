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

// Timestamp fixo para testes determinísticos
$agora = 1000000;

// Teste 1: Vulnerável aprova compra mesmo com bot disparando requisições
$historicoBot = [
    $agora,
    $agora - 1,
    $agora - 2,
    $agora - 3,
    $agora - 4,
    $agora - 5,
    $agora - 6,
    $agora - 7,
    $agora - 8,
    $agora - 9,
];
$resultadoVulneravel = \Vulneravel\processarCompra('bot_attacker', 1, $historicoBot);
verificar(
    'Vulnerável: aprova compra mesmo com padrão de bot (10 req em 9s)',
    $resultadoVulneravel['aprovado'] === true
);

// Teste 2: Vulnerável não faz nenhuma verificação
$resultadoVulneravel2 = \Vulneravel\processarCompra('qualquer_id', 1, []);
verificar(
    'Vulnerável: aprova qualquer compra sem verificação de histórico',
    $resultadoVulneravel2['aprovado'] === true
);

// Teste 3: Corrigido nega bot com muitas requisições (> 3 em 10s)
$resultadoCorrigido1 = \Corrigido\processarCompra('bot_attacker', 1, $historicoBot, $agora);
verificar(
    'Corrigido: nega bot com 10 requisições em 9 segundos',
    $resultadoCorrigido1['aprovado'] === false
);

// Teste 4: Corrigido aprova cliente legítimo (história vazia)
$resultadoCorrigido2 = \Corrigido\processarCompra('cliente_real_1', 1, [], $agora);
verificar(
    'Corrigido: aprova cliente legítimo com história vazia',
    $resultadoCorrigido2['aprovado'] === true
);

// Teste 5: Corrigido aprova cliente com 1 requisição recente
$historico1Req = [$agora - 5];
$resultadoCorrigido3 = \Corrigido\processarCompra('cliente_real_2', 1, $historico1Req, $agora);
verificar(
    'Corrigido: aprova cliente com 1 requisição recente',
    $resultadoCorrigido3['aprovado'] === true
);

// Teste 6: Corrigido aprova cliente com 2 requisições recentes
$historico2Req = [$agora - 5, $agora - 3];
$resultadoCorrigido4 = \Corrigido\processarCompra('cliente_real_3', 1, $historico2Req, $agora);
verificar(
    'Corrigido: aprova cliente com 2 requisições recentes',
    $resultadoCorrigido4['aprovado'] === true
);

// Teste 7: Corrigido aprova cliente com 3 requisições recentes (no limite)
$historico3Req = [$agora - 7, $agora - 4, $agora - 1];
$resultadoCorrigido5 = \Corrigido\processarCompra('cliente_real_4', 1, $historico3Req, $agora);
verificar(
    'Corrigido: aprova cliente com 3 requisições recentes (no limite)',
    $resultadoCorrigido5['aprovado'] === true
);

// Teste 8: Corrigido nega cliente com 4 requisições recentes (acima do limite)
$historico4Req = [$agora - 8, $agora - 6, $agora - 3, $agora - 1];
$resultadoCorrigido6 = \Corrigido\processarCompra('cliente_suspeito', 1, $historico4Req, $agora);
verificar(
    'Corrigido: nega cliente com 4 requisições recentes (acima do limite)',
    $resultadoCorrigido6['aprovado'] === false
);

// Teste 9: Corrigido aprova cliente com requisições antigas (> 10s atrás)
$historicoAntigo = [$agora - 60, $agora - 50, $agora - 40];  // Todas antigas
$resultadoCorrigido7 = \Corrigido\processarCompra('cliente_real_5', 1, $historicoAntigo, $agora);
verificar(
    'Corrigido: aprova cliente com requisições antigas (fora da janela de 10s)',
    $resultadoCorrigido7['aprovado'] === true
);

// Teste 10: Corrigido conta apenas requisições dentro da janela de 10 segundos
$historicoMisto = [
    $agora - 5,   // Recente (dentro dos 10s)
    $agora - 8,   // Recente (dentro dos 10s)
    $agora - 25,  // Antiga (fora dos 10s, não conta)
    $agora - 50,  // Antiga (fora dos 10s, não conta)
];
$resultadoCorrigido8 = \Corrigido\processarCompra('cliente_real_6', 1, $historicoMisto, $agora);
verificar(
    'Corrigido: conta apenas requisições dentro de 10s (ignora antigas)',
    $resultadoCorrigido8['aprovado'] === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
