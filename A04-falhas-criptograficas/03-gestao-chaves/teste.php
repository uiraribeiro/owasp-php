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
// Teste 1: Vulnerável sempre retorna a mesma chave
$chave1_vuln = \Vulneravel\obterChaveCriptografia();
$chave2_vuln = \Vulneravel\obterChaveCriptografia();
$chave3_vuln = \Vulneravel\obterChaveCriptografia();

verificar(
    'Chave vulnerável é IDÊNTICA em todas as chamadas (hardcoded)',
    $chave1_vuln === $chave2_vuln && $chave2_vuln === $chave3_vuln
);

verificar(
    'Chave vulnerável contém a string literal esperada',
    $chave1_vuln === "minha-chave-secreta-fixa-123456"
);

// Teste 2: Corrigido lança exception quando APP_ENCRYPTION_KEY não está configurada
$exception_lancada = false;
$exception_msg = '';
try {
    \Corrigido\obterChaveCriptografia();
} catch (\RuntimeException $e) {
    $exception_lancada = true;
    $exception_msg = $e->getMessage();
}

verificar(
    'Corrigido lança RuntimeException quando variável não configurada',
    $exception_lancada
);

verificar(
    'Mensagem de exception menciona APP_ENCRYPTION_KEY',
    strpos($exception_msg, 'APP_ENCRYPTION_KEY') !== false
);

// Teste 3: Corrigido retorna o valor da variável de ambiente quando configurada
putenv('APP_ENCRYPTION_KEY=chave-de-teste-segura-1234567890');
$chave_corr = \Corrigido\obterChaveCriptografia();

verificar(
    'Corrigido retorna valor da variável de ambiente',
    $chave_corr === 'chave-de-teste-segura-1234567890'
);

// Teste 4: Corrigido permite rotação de chave (apenas mudando env)
putenv('APP_ENCRYPTION_KEY=chave-nova-rotacionada-abcdefg');
$chave_corr_nova = \Corrigido\obterChaveCriptografia();

verificar(
    'Corrigido consegue rotacionar chave (novo valor do env)',
    $chave_corr_nova === 'chave-nova-rotacionada-abcdefg' && $chave_corr_nova !== $chave_corr
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
