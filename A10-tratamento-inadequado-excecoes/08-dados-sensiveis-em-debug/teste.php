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

$dados = [
    'usuario' => 'joao',
    'email' => 'joao@example.com',
    'senha' => 'MinhaSenha123',
    'cartao_credito' => '4111111111111111',
    'token' => 'abc123xyz789'
];

// Teste 1: Vulnerável com debug desativado retorna null
$r1 = \Vulneravel\depurarRequisicao($dados, false);
verificar(
    'Ambas retornam null quando debug está desativado',
    $r1 === null && \Corrigido\depurarRequisicao($dados, false) === null
);

// Teste 2: Vulnerável expõe senha em modo debug
$r2 = \Vulneravel\depurarRequisicao($dados, true);
$temSenhaVulneravel = str_contains($r2, 'MinhaSenha123');
verificar(
    'Vulnerável expõe senha em modo debug',
    $temSenhaVulneravel === true
);

// Teste 3: Vulnerável expõe número de cartão em modo debug
$temCartaoVulneravel = str_contains($r2, '4111111111111111');
verificar(
    'Vulnerável expõe número de cartão em modo debug',
    $temCartaoVulneravel === true
);

// Teste 4: Corrigido redacts senha em modo debug
$r3 = \Corrigido\depurarRequisicao($dados, true);
$senhaRedacted = !str_contains($r3, 'MinhaSenha123') && str_contains($r3, '[REDACTED]');
verificar(
    'Corrigido redacts senha (não contém MinhaSenha123, contém [REDACTED])',
    $senhaRedacted === true
);

// Teste 5: Corrigido redacts número de cartão
$cartaoRedacted = !str_contains($r3, '4111111111111111');
verificar(
    'Corrigido redacts número de cartão (não contém 4111111111111111)',
    $cartaoRedacted === true
);

// Teste 6: Corrigido redacts token
$tokenRedacted = !str_contains($r3, 'abc123xyz789');
verificar(
    'Corrigido redacts token (não contém abc123xyz789)',
    $tokenRedacted === true
);

// Teste 7: Corrigido mantém dados não-sensíveis visíveis
$usuarioVisivel = str_contains($r3, 'joao');
verificar(
    'Corrigido mantém dados não-sensíveis visíveis (usuário joao está visível)',
    $usuarioVisivel === true
);

// Teste 8: Corrigido tem múltiplos [REDACTED] (um por campo sensível)
$redactedCount = substr_count($r3, '[REDACTED]');
verificar(
    'Corrigido tem 3 campos redacted (senha, cartao_credito, token)',
    $redactedCount === 3
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
