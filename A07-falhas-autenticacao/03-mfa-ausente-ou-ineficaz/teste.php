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

// Teste 1: Vulnerável permite login com MFA habilitado mas sem código MFA
$vulnMfaBypass = \Vulneravel\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    true,  // MFA habilitado
    null,  // Sem código MFA
    '123456'
);
verificar(
    'Vulnerável permite login com MFA habilitado mas SEM código MFA (bypass)',
    $vulnMfaBypass === true
);

// Teste 2: Corrigido bloqueia login com MFA habilitado mas sem código MFA
$corrMfaRequired = \Corrigido\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    true,  // MFA habilitado
    null,  // Sem código MFA
    '123456'
);
verificar(
    'Corrigido bloqueia login com MFA habilitado e sem código MFA',
    $corrMfaRequired === false
);

// Teste 3: Corrigido bloqueia com código MFA incorreto
$corrMfaWrong = \Corrigido\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    true,  // MFA habilitado
    '000000',  // Código incorreto
    '123456'
);
verificar(
    'Corrigido bloqueia com código MFA incorreto',
    $corrMfaWrong === false
);

// Teste 4: Corrigido permite com MFA habilitado E código correto
$corrMfaOk = \Corrigido\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    true,  // MFA habilitado
    '123456',  // Código correto
    '123456'
);
verificar(
    'Corrigido permite com MFA habilitado e código MFA correto (caso legítimo)',
    $corrMfaOk === true
);

// Teste 5: Vulnerável permite sem MFA mesmo que código seja incorreto
$vulnSemMfa = \Vulneravel\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    false,  // MFA não habilitado
    'qualquer_coisa',
    '123456'
);
verificar(
    'Vulnerável permite login sem MFA (comportamento esperado)',
    $vulnSemMfa === true
);

// Teste 6: Corrigido permite sem MFA habilitado
$corrSemMfa = \Corrigido\autenticarComMfa(
    'senha_correta',
    'senha_correta',
    false,  // MFA não habilitado
    null,
    ''
);
verificar(
    'Corrigido permite login sem MFA habilitado (caso legítimo)',
    $corrSemMfa === true
);

// Teste 7: Corrigido bloqueia com senha incorreta (mesmo com MFA correto)
$corrSenhaErrada = \Corrigido\autenticarComMfa(
    'senha_errada',
    'senha_correta',
    true,
    '123456',
    '123456'
);
verificar(
    'Corrigido bloqueia com senha incorreta',
    $corrSenhaErrada === false
);

// Teste 8: Vulnerável bloqueia com senha errada (mesmo sem MFA correto)
$vulnSenhaErrada = \Vulneravel\autenticarComMfa(
    'senha_errada',
    'senha_correta',
    true,
    null,
    '123456'
);
verificar(
    'Vulnerável bloqueia com senha incorreta',
    $vulnSenhaErrada === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
