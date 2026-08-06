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

// Teste 1: Vulnerável aprova grande quantidade sem depósito (falha crítica)
$resultadoVulneravel1 = \Vulneravel\reservarIngressos(5000, false);
verificar(
    'Vulnerável: aprova 5000 ingressos sem depósito (falha de design)',
    $resultadoVulneravel1['aprovado'] === true
);

// Teste 2: Vulnerável aprova qualquer quantidade
$resultadoVulneravel2 = \Vulneravel\reservarIngressos(99999, false);
verificar(
    'Vulnerável: aprova 99999 ingressos sem qualquer limite',
    $resultadoVulneravel2['aprovado'] === true
);

// Teste 3: Corrigido aprova quantidade pequena sem depósito (caso legítimo)
$resultadoCorrigido1 = \Corrigido\reservarIngressos(2, false);
verificar(
    'Corrigido: aprova 2 ingressos sem depósito (limite permitido)',
    $resultadoCorrigido1['aprovado'] === true
);

// Teste 4: Corrigido aprova até 4 ingressos sem depósito
$resultadoCorrigido2 = \Corrigido\reservarIngressos(4, false);
verificar(
    'Corrigido: aprova 4 ingressos sem depósito (limite máximo)',
    $resultadoCorrigido2['aprovado'] === true
);

// Teste 5: Corrigido nega 5 ingressos sem depósito
$resultadoCorrigido3 = \Corrigido\reservarIngressos(5, false);
verificar(
    'Corrigido: nega 5 ingressos sem depósito (excede limite)',
    $resultadoCorrigido3['aprovado'] === false
);

// Teste 6: Corrigido nega quantidade grande (5000) sem depósito
$resultadoCorrigido4 = \Corrigido\reservarIngressos(5000, false);
verificar(
    'Corrigido: nega 5000 ingressos sem depósito (quantidade grande)',
    $resultadoCorrigido4['aprovado'] === false
);

// Teste 7: Corrigido aprova quantidade grande COM depósito
$resultadoCorrigido5 = \Corrigido\reservarIngressos(5000, true);
verificar(
    'Corrigido: aprova 5000 ingressos COM depósito',
    $resultadoCorrigido5['aprovado'] === true
);

// Teste 8: Corrigido aprova quantidade gigante COM depósito
$resultadoCorrigido6 = \Corrigido\reservarIngressos(999999, true);
verificar(
    'Corrigido: aprova quantidade grande COM depósito validado',
    $resultadoCorrigido6['aprovado'] === true
);

// Teste 9: Corrigido nega quantidade negativa
$resultadoCorrigido7 = \Corrigido\reservarIngressos(-5, false);
verificar(
    'Corrigido: nega quantidade negativa',
    $resultadoCorrigido7['aprovado'] === false
);

// Teste 10: Corrigido nega quantidade zero
$resultadoCorrigido8 = \Corrigido\reservarIngressos(0, false);
verificar(
    'Corrigido: nega quantidade zero',
    $resultadoCorrigido8['aprovado'] === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
