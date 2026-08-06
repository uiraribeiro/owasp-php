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

// Teste 1: Vulnerável permite que usuário comum exclua
$usuarioComum = ['role' => 'user'];
verificar(
    'Vulnerável permite user excluir (demonstra o bug)',
    \Vulneravel\podeExcluirRelatorio($usuarioComum) === true
);

// Teste 2: Corrigido bloqueia usuário comum
verificar(
    'Corrigido bloqueia user de excluir',
    \Corrigido\podeExcluirRelatorio($usuarioComum) === false
);

// Teste 3: Vulnerável permite que admin exclua
$usuarioAdmin = ['role' => 'admin'];
verificar(
    'Vulnerável permite admin excluir',
    \Vulneravel\podeExcluirRelatorio($usuarioAdmin) === true
);

// Teste 4: Corrigido permite que admin exclua
verificar(
    'Corrigido permite admin excluir (caso legítimo)',
    \Corrigido\podeExcluirRelatorio($usuarioAdmin) === true
);

// Teste 5: Vulnerável bloqueia guest
$usuarioGuest = ['role' => 'guest'];
verificar(
    'Vulnerável bloqueia guest',
    \Vulneravel\podeExcluirRelatorio($usuarioGuest) === false
);

// Teste 6: Corrigido bloqueia guest
verificar(
    'Corrigido bloqueia guest',
    \Corrigido\podeExcluirRelatorio($usuarioGuest) === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
