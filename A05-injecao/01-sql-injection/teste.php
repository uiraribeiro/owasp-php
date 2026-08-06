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

// Testes SQL Injection
$pdoVulneravel = \Vulneravel\criarBancoDeTeste();
$pdoCorrigido = \Corrigido\criarBancoDeTeste();

// Teste legítimo em ambas
$resultadoVulneravel = \Vulneravel\login($pdoVulneravel, 'admin', 'senhaForte123');
$resultadoCorrigido = \Corrigido\login($pdoCorrigido, 'admin', 'senhaForte123');

verificar(
    "Vulnerável: login legítimo retorna usuário admin",
    $resultadoVulneravel !== null && $resultadoVulneravel['usuario'] === 'admin'
);

verificar(
    "Corrigido: login legítimo retorna usuário admin",
    $resultadoCorrigido !== null && $resultadoCorrigido['usuario'] === 'admin'
);

// Teste SQL Injection: bypass de autenticação
$resultadoInjection = \Vulneravel\login($pdoVulneravel, "admin' -- ", 'senhaQualquer');
verificar(
    "Vulnerável: SQL Injection permite bypass de autenticação (retorna dados sem senha correta)",
    $resultadoInjection !== null && $resultadoInjection['usuario'] === 'admin'
);

$resultadoProtegido = \Corrigido\login($pdoCorrigido, "admin' -- ", 'senhaQualquer');
verificar(
    "Corrigido: SQL Injection é bloqueado (retorna null para entrada maliciosa)",
    $resultadoProtegido === null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
