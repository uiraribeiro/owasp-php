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

// Teste 1: Vulnerável aceita senha muito curta "1234"
$vuln1 = \Vulneravel\validarNovaSenha('1234', 'joao');
verificar(
    'Vulnerável aceita senha "1234" (4 caracteres)',
    $vuln1 === true
);

// Teste 2: Corrigido rejeita senha muito curta "1234"
$corr1 = \Corrigido\validarNovaSenha('1234', 'joao');
verificar(
    'Corrigido rejeita senha "1234" (menos de 12 caracteres)',
    $corr1 === false
);

// Teste 3: Vulnerável aceita senha "senha123" (8 caracteres, mas passa no >= 4)
$vuln2 = \Vulneravel\validarNovaSenha('senha123', 'maria');
verificar(
    'Vulnerável aceita "senha123" (8 caracteres)',
    $vuln2 === true
);

// Teste 4: Corrigido rejeita "senha123" por estar na lista de fracas
$corr2 = \Corrigido\validarNovaSenha('senha123', 'maria');
verificar(
    'Corrigido rejeita "senha123" (está na lista de senhas fracas)',
    $corr2 === false
);

// Teste 5: Corrigido rejeita "password1234" (12 caracteres, mas fraca/conhecida)
$corr3 = \Corrigido\validarNovaSenha('password1234', 'user');
verificar(
    'Corrigido rejeita "password1234" (fraca conhecida)',
    $corr3 === false
);

// Teste 6: Corrigido rejeita senha igual ao nome de usuário (case-insensitive)
// Usando um nome de usuário com 12 caracteres para passar no tamanho
$corr4 = \Corrigido\validarNovaSenha('abcdefghijkl', 'abcdefghijkl');
verificar(
    'Corrigido rejeita senha igual ao nome de usuário (case-insensitive)',
    $corr4 === false
);

// Teste 7: Corrigido aceita senha forte (12+ caracteres, única, não na lista)
$senhaForte = 'MinhaSenhaForte2025!@#';
$corr5 = \Corrigido\validarNovaSenha($senhaForte, 'usuario');
verificar(
    'Corrigido aceita senha forte e única (caso legítimo)',
    $corr5 === true
);

// Teste 8: Vulnerável aceita qualquer coisa >= 4 caracteres
$vuln3 = \Vulneravel\validarNovaSenha('aaaa', 'x');
verificar(
    'Vulnerável aceita "aaaa" (apenas 4 caracteres repetidos)',
    $vuln3 === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
