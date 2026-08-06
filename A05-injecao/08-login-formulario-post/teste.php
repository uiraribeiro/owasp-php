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

$pdoVulneravel = Vulneravel\criarBancoDeTeste();
$pdoCorrigido = Corrigido\criarBancoDeTeste();

// Login legítimo funciona em ambos
$loginVulneravelOk = Vulneravel\login($pdoVulneravel, 'admin', 'senhaForte123');
verificar('Vulnerável: login legítimo retorna usuário admin', $loginVulneravelOk !== null && $loginVulneravelOk['usuario'] === 'admin');

$loginCorrigidoOk = Corrigido\login($pdoCorrigido, 'admin', 'senhaForte123');
verificar('Corrigido: login legítimo retorna usuário admin', $loginCorrigidoOk !== null && $loginCorrigidoOk['usuario'] === 'admin');

// SQL Injection via campo 'usuario' do formulário (POST)
$bypassVulneravel = Vulneravel\login($pdoVulneravel, "admin' -- ", 'senhaErrada');
verificar('Vulnerável: SQL Injection no formulário permite bypass de login', $bypassVulneravel !== null && $bypassVulneravel['usuario'] === 'admin');

$bypassCorrigido = Corrigido\login($pdoCorrigido, "admin' -- ", 'senhaErrada');
verificar('Corrigido: SQL Injection no formulário é bloqueada', $bypassCorrigido === null);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
