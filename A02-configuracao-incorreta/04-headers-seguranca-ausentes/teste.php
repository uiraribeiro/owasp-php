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

// Teste 1: Vulnerável retorna array vazio
$headersVulneravel = \Vulneravel\gerarHeadersSeguranca();
verificar(
    'Vulnerável retorna array vazio (sem headers)',
    empty($headersVulneravel)
);

// Teste 2: Vulnerável não tem X-Content-Type-Options
verificar(
    'Vulnerável não tem header X-Content-Type-Options',
    !array_key_exists('X-Content-Type-Options', $headersVulneravel)
);

// Teste 3: Vulnerável não tem X-Frame-Options
verificar(
    'Vulnerável não tem header X-Frame-Options',
    !array_key_exists('X-Frame-Options', $headersVulneravel)
);

// Teste 4: Vulnerável não tem Content-Security-Policy
verificar(
    'Vulnerável não tem header Content-Security-Policy',
    !array_key_exists('Content-Security-Policy', $headersVulneravel)
);

// Teste 5: Vulnerável não tem Strict-Transport-Security
verificar(
    'Vulnerável não tem header Strict-Transport-Security',
    !array_key_exists('Strict-Transport-Security', $headersVulneravel)
);

// Teste 6: Corrigido retorna array com headers
$headersCorrigido = \Corrigido\gerarHeadersSeguranca();
verificar(
    'Corrigido retorna array com headers',
    !empty($headersCorrigido) && is_array($headersCorrigido)
);

// Teste 7: Corrigido tem X-Content-Type-Options correto
verificar(
    'Corrigido tem X-Content-Type-Options = nosniff',
    isset($headersCorrigido['X-Content-Type-Options']) &&
    $headersCorrigido['X-Content-Type-Options'] === 'nosniff'
);

// Teste 8: Corrigido tem X-Frame-Options correto
verificar(
    'Corrigido tem X-Frame-Options = DENY',
    isset($headersCorrigido['X-Frame-Options']) &&
    $headersCorrigido['X-Frame-Options'] === 'DENY'
);

// Teste 9: Corrigido tem Content-Security-Policy correto
verificar(
    'Corrigido tem Content-Security-Policy correto',
    isset($headersCorrigido['Content-Security-Policy']) &&
    $headersCorrigido['Content-Security-Policy'] === "default-src 'self'"
);

// Teste 10: Corrigido tem Strict-Transport-Security correto
verificar(
    'Corrigido tem Strict-Transport-Security correto',
    isset($headersCorrigido['Strict-Transport-Security']) &&
    $headersCorrigido['Strict-Transport-Security'] === 'max-age=63072000; includeSubDomains'
);

// Teste 11: Corrigido tem exatamente 4 headers
verificar(
    'Corrigido tem exatamente 4 headers de segurança',
    count($headersCorrigido) === 4
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
