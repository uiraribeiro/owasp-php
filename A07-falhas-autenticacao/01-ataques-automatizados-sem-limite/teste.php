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

// Tentativas anteriores: 5 falhas recentes
$tentativasAnteriores = [1690000001, 1690000002, 1690000003, 1690000004, 1690000005];

// Teste 1: Vulnerável permite login após múltiplas tentativas, mesmo com senha correta
$resultVulneravel = \Vulneravel\tentarLogin('user', 'senha_correta', 'senha_correta', $tentativasAnteriores);
verificar(
    'Vulnerável permite login após 5 tentativas falhas (credential stuffing bem-sucedido)',
    $resultVulneravel['permitido'] === true
);

// Teste 2: Corrigido bloqueia login após múltiplas tentativas, mesmo com senha correta
$resultCorrigido = \Corrigido\tentarLogin('user', 'senha_correta', 'senha_correta', $tentativasAnteriores);
verificar(
    'Corrigido bloqueia login após 5 tentativas falhas',
    $resultCorrigido['permitido'] === false && $resultCorrigido['motivo'] === 'muitas tentativas, tente mais tarde'
);

// Teste 3: Vulnerável permite login sem tentativas anteriores
$resultVulneravelOk = \Vulneravel\tentarLogin('user', 'senha_correta', 'senha_correta', []);
verificar(
    'Vulnerável permite login normal sem tentativas anteriores',
    $resultVulneravelOk['permitido'] === true
);

// Teste 4: Corrigido permite login sem tentativas anteriores
$resultCorrigidoOk = \Corrigido\tentarLogin('user', 'senha_correta', 'senha_correta', []);
verificar(
    'Corrigido permite login normal sem tentativas anteriores (caso legítimo)',
    $resultCorrigidoOk['permitido'] === true
);

// Teste 5: Corrigido bloqueia mesmo com exatamente 5 tentativas
$exatamente5 = [1, 2, 3, 4, 5];
$resultBloqueia5 = \Corrigido\tentarLogin('user', 'senha_correta', 'senha_correta', $exatamente5);
verificar(
    'Corrigido bloqueia com exatamente 5 tentativas falhas',
    $resultBloqueia5['permitido'] === false
);

// Teste 6: Corrigido permite com 4 tentativas
$quatro = [1, 2, 3, 4];
$resultPermite4 = \Corrigido\tentarLogin('user', 'senha_correta', 'senha_correta', $quatro);
verificar(
    'Corrigido permite login com 4 tentativas (limite em 5)',
    $resultPermite4['permitido'] === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
