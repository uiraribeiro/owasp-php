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

// Testes
// Teste 1: Token vulnerável tem exatamente 6 caracteres
$token_vuln1 = \Vulneravel\gerarTokenRecuperacaoSenha();
verificar(
    'Token vulnerável tem exatamente 6 caracteres',
    strlen($token_vuln1) === 6
);

// Teste 2: Token vulnerável contém apenas dígitos
verificar(
    'Token vulnerável contém apenas dígitos (0-9)',
    ctype_digit($token_vuln1)
);

// Teste 3: Token vulnerável está no range 000000-999999
verificar(
    'Token vulnerável está no range 000000-999999',
    (int)$token_vuln1 >= 0 && (int)$token_vuln1 <= 999999
);

// Teste 4: Token corrigido tem 64 caracteres (32 bytes em hex)
$token_corr1 = \Corrigido\gerarTokenRecuperacaoSenha();
verificar(
    'Token corrigido tem 64 caracteres',
    strlen($token_corr1) === 64
);

// Teste 5: Token corrigido contém caracteres hexadecimais
verificar(
    'Token corrigido contém caracteres hexadecimais',
    ctype_xdigit($token_corr1)
);

// Teste 6: Dois tokens corridos são DIFERENTES (CSPRNG)
$token_corr2 = \Corrigido\gerarTokenRecuperacaoSenha();
verificar(
    'Dois tokens corrigidos são DIFERENTES',
    $token_corr1 !== $token_corr2
);

// Teste 7: Dois tokens vulneráveis também são diferentes (mt_rand é pseudo-aleatório)
// Mas o espaço é MUITO menor (10^6 vs 2^256)
$token_vuln2 = \Vulneravel\gerarTokenRecuperacaoSenha();
// Não verificamos igualdade pois é improvável em apenas 2 chamadas
// A diferença está no espaço de busca, não na aleatoriedade imediata

// Teste 8: Espaço de busca do vulnerável é pequeno (fácil de bruteforçar)
// Demonstramos gerando múltiplos tokens e verificando que todos cabem em 10^6
$tokens_vulneraveis = [];
for ($i = 0; $i < 100; $i++) {
    $tokens_vulneraveis[] = (int)\Vulneravel\gerarTokenRecuperacaoSenha();
}
$todos_em_range = array_reduce($tokens_vulneraveis, function($carry, $token) {
    return $carry && $token >= 0 && $token <= 999999;
}, true);
verificar(
    'Todos os 100 tokens vulneráveis cabem em 10^6 (pequeno espaço)',
    $todos_em_range
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
