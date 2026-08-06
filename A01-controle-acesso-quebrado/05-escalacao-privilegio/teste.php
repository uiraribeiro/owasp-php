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

$baseDeUsuarios = [
    ['nome' => 'alice', 'role' => 'user'],
    ['nome' => 'bob', 'role' => 'admin'],
];

// Teste 1: Vulnerável aceita role=admin do cliente
$loginFraudado = ['usuario' => 'alice', 'role' => 'admin'];
$sessaoVulneravel = \Vulneravel\autenticar($loginFraudado, $baseDeUsuarios);
verificar(
    'Vulnerável aceita role=admin do cliente (escalação de privilégio)',
    $sessaoVulneravel['autenticado'] === true && $sessaoVulneravel['role'] === 'admin'
);

// Teste 2: Corrigido ignora role=admin do cliente e usa role do banco
$sessaoCorrigida = \Corrigido\autenticar($loginFraudado, $baseDeUsuarios);
verificar(
    'Corrigido usa role do banco (user) mesmo com role=admin do cliente',
    $sessaoCorrigida['autenticado'] === true && $sessaoCorrigida['role'] === 'user'
);

// Teste 3: Vulnerável com login legítimo (alice sem role no payload)
$loginLegitimo = ['usuario' => 'alice'];
$sessaoVulneravelLegitima = \Vulneravel\autenticar($loginLegitimo, $baseDeUsuarios);
verificar(
    'Vulnerável com login legítimo retorna role=user do banco',
    $sessaoVulneravelLegitima['autenticado'] === true && $sessaoVulneravelLegitima['role'] === 'user'
);

// Teste 4: Corrigido com login legítimo
$sessaoCorrigidaLegitima = \Corrigido\autenticar($loginLegitimo, $baseDeUsuarios);
verificar(
    'Corrigido com login legítimo retorna role=user (caso legítimo)',
    $sessaoCorrigidaLegitima['autenticado'] === true && $sessaoCorrigidaLegitima['role'] === 'user'
);

// Teste 5: Admin legítimo
$loginAdmin = ['usuario' => 'bob', 'role' => 'user'];  // Tenta enviar role=user mesmo sendo admin
$sessaoVulneravelAdmin = \Vulneravel\autenticar($loginAdmin, $baseDeUsuarios);
verificar(
    'Vulnerável com admin enviando role=user aceita o role=user do cliente (péssimo!)',
    $sessaoVulneravelAdmin['autenticado'] === true && $sessaoVulneravelAdmin['role'] === 'user'
);

// Teste 6: Corrigido força admin a ter role=admin
$sessaoCorrigidaAdmin = \Corrigido\autenticar($loginAdmin, $baseDeUsuarios);
verificar(
    'Corrigido força bob a ter role=admin (do banco)',
    $sessaoCorrigidaAdmin['autenticado'] === true && $sessaoCorrigidaAdmin['role'] === 'admin'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
