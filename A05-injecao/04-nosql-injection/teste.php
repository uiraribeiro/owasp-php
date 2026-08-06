<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void
{
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

$usuarios = [
    ['usuario' => 'admin', 'senha' => 'senhaForte123'],
    ['usuario' => 'user', 'senha' => 'senha456'],
];

// Teste legítimo em ambas
$resultadoVulneravel = \Vulneravel\login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaForte123']);
$resultadoCorrigido = \Corrigido\login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaForte123']);

verificar(
    "Vulnerável: login legítimo retorna usuário admin",
    $resultadoVulneravel !== null && $resultadoVulneravel['usuario'] === 'admin'
);

verificar(
    "Corrigido: login legítimo retorna usuário admin",
    $resultadoCorrigido !== null && $resultadoCorrigido['usuario'] === 'admin'
);

// Teste NoSQL Injection: bypass com $ne
$filtroInjeção = ['usuario' => 'admin', 'senha' => ['$ne' => '']];
$resultadoInjeçãoVulnerável = \Vulneravel\login($usuarios, $filtroInjeção);
$resultadoInjeçãoCorrigido = \Corrigido\login($usuarios, $filtroInjeção);

verificar(
    "Vulnerável: NoSQL Injection com \$ne permite bypass (retorna admin sem senha correta)",
    $resultadoInjeçãoVulnerável !== null && $resultadoInjeçãoVulnerável['usuario'] === 'admin'
);

verificar(
    "Corrigido: NoSQL Injection é bloqueado (retorna null para operador \$ne)",
    $resultadoInjeçãoCorrigido === null
);

// Teste login incorreto em ambas
$resultadoErro = \Vulneravel\login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaErrada']);
$resultadoErroCorrigido = \Corrigido\login($usuarios, ['usuario' => 'admin', 'senha' => 'senhaErrada']);

verificar(
    "Vulnerável: login com senha errada retorna null",
    $resultadoErro === null
);

verificar(
    "Corrigido: login com senha errada retorna null",
    $resultadoErroCorrigido === null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
