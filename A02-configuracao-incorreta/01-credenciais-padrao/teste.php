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

// Teste 1: Vulnerável aceita credencial padrão admin/admin123
$loginVulneravel = \Vulneravel\autenticarAdmin('admin', 'admin123');
verificar(
    'Vulnerável aceita credencial padrão admin/admin123 (falha de segurança)',
    $loginVulneravel === true
);

// Teste 2: Corrigido rejeita credencial padrão admin123 mesmo com hash válido
$hashAdmin123 = password_hash('admin123', PASSWORD_DEFAULT);
$loginCorrigidoComPadrao = \Corrigido\autenticarAdmin('admin', 'admin123', $hashAdmin123);
verificar(
    'Corrigido bloqueia credencial padrão admin123 (proteção ativa)',
    $loginCorrigidoComPadrao === false
);

// Teste 3: Corrigido rejeita outras senhas padrão
$hashPassword = password_hash('password', PASSWORD_DEFAULT);
$loginComPassword = \Corrigido\autenticarAdmin('admin', 'password', $hashPassword);
verificar(
    'Corrigido bloqueia senha padrão "password"',
    $loginComPassword === false
);

// Teste 4: Corrigido rejeita senha errada com hash de senha customizada
$hashPersonalizada = password_hash('SenhaForte2024!', PASSWORD_DEFAULT);
$loginSenhaErrada = \Corrigido\autenticarAdmin('admin', 'senha_errada', $hashPersonalizada);
verificar(
    'Corrigido rejeita senha incorreta',
    $loginSenhaErrada === false
);

// Teste 5: Corrigido aceita senha customizada correta
$loginSenhaCorreta = \Corrigido\autenticarAdmin('admin', 'SenhaForte2024!', $hashPersonalizada);
verificar(
    'Corrigido aceita senha customizada válida (caso legítimo)',
    $loginSenhaCorreta === true
);

// Teste 6: Função auxiliar detecta senhas padrão
$ehPadrao123456 = \Corrigido\senhaEhPadraoDeFabrica('123456');
verificar(
    'Função detecta senha padrão "123456"',
    $ehPadrao123456 === true
);

// Teste 7: Função auxiliar não marca senhas personalizadas como padrão
$ehPadraoPersonalizada = \Corrigido\senhaEhPadraoDeFabrica('MinhaSeNhaUnica9876!');
verificar(
    'Função não marca senha customizada como padrão',
    $ehPadraoPersonalizada === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
