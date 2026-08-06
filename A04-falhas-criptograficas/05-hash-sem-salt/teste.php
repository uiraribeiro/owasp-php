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
$senha = '123456';
$senha_errada = '654321';

// Teste 1: Vulnerável produz o MESMO hash duas vezes
$hash1_vuln = \Vulneravel\hashSenha($senha);
$hash2_vuln = \Vulneravel\hashSenha($senha);

verificar(
    'MD5 vulnerável produz o MESMO hash duas vezes (sem salt)',
    $hash1_vuln === $hash2_vuln
);

// Teste 2: Vulnerável tem hash conhecido de "123456"
verificar(
    'MD5("123456") é o hash amplamente conhecido',
    $hash1_vuln === 'e10adc3949ba59abbe56e057f20f883e'
);

// Teste 3: Vulnerável consegue verificar senha correta
verificar(
    'Verificação de MD5 funciona com senha correta',
    \Vulneravel\verificarSenha($senha, $hash1_vuln) === true
);

// Teste 4: Vulnerável rejeita senha errada
verificar(
    'Verificação de MD5 rejeita senha errada',
    \Vulneravel\verificarSenha($senha_errada, $hash1_vuln) === false
);

// Teste 5: Corrigido produz hashes DIFERENTES duas vezes
$hash1_corr = \Corrigido\hashSenha($senha);
$hash2_corr = \Corrigido\hashSenha($senha);

verificar(
    'bcrypt corrigido produz hashes DIFERENTES (salt aleatório)',
    $hash1_corr !== $hash2_corr
);

// Teste 6: Corrigido consegue verificar senha correta (hash 1)
verificar(
    'password_verify() valida senha correta contra hash 1',
    \Corrigido\verificarSenha($senha, $hash1_corr) === true
);

// Teste 7: Corrigido consegue verificar senha correta (hash 2)
verificar(
    'password_verify() valida senha correta contra hash 2',
    \Corrigido\verificarSenha($senha, $hash2_corr) === true
);

// Teste 8: Corrigido rejeita senha errada
verificar(
    'password_verify() rejeita senha errada',
    \Corrigido\verificarSenha($senha_errada, $hash1_corr) === false
);

// Teste 9: Hash corrigido começa com $2y$ (prefixo bcrypt)
verificar(
    'Hash bcrypt começa com $2y$ (prefixo bcrypt)',
    strpos($hash1_corr, '$2y$') === 0 || strpos($hash1_corr, '$2a$') === 0 || strpos($hash1_corr, '$2b$') === 0
);

// Teste 10: Hash corrigido tem comprimento esperado (~60 caracteres)
verificar(
    'Hash bcrypt tem comprimento aproximado de 60 caracteres',
    strlen($hash1_corr) >= 59 && strlen($hash1_corr) <= 61
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
