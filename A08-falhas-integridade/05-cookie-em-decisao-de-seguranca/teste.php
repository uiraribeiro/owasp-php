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

// Simulação: usuário normal na base
$sessoesServer = [
    'sessao_usuario_1' => ['role' => 'user', 'id_usuario' => 1],
    'sessao_usuario_2' => ['role' => 'user', 'id_usuario' => 2],
    'sessao_admin' => ['role' => 'admin', 'id_usuario' => 0],
];

// Teste 1: Vulnerável permite escalação (usuário edita cookie para admin)
$cookieDoAtacanteEditado = ['role' => 'admin'];
$resultado = \Vulneravel\usuarioEhAdmin($cookieDoAtacanteEditado);
verificar(
    'Vulnerável: usuário normal escalou para admin editando cookie (PROBLEMA!)',
    $resultado === true
);

// Teste 2: Vulnerável permite acesso mesmo sem cookie de sessão válida
$cookieFake = ['role' => 'admin'];
$resultado = \Vulneravel\usuarioEhAdmin($cookieFake);
verificar(
    'Vulnerável: qualquer cookie com role=admin é aceito',
    $resultado === true
);

// Teste 3: Corrigido rejeita sessão fake (não existe no servidor)
$resultado = \Corrigido\usuarioEhAdmin('sessao_fake_do_atacante', $sessoesServer);
verificar(
    'Corrigido: rejeita sessão inexistente no servidor',
    $resultado === false
);

// Teste 4: Corrigido rejeita usuário normal mesmo que tente editar sessão
// (o cliente não consegue editar o armazenamento do servidor, só o cookie)
$resultado = \Corrigido\usuarioEhAdmin('sessao_usuario_1', $sessoesServer);
verificar(
    'Corrigido: rejeita usuário normal (role=user no servidor)',
    $resultado === false
);

// Teste 5: Corrigido aceita sessão admin legítima
$resultado = \Corrigido\usuarioEhAdmin('sessao_admin', $sessoesServer);
verificar(
    'Corrigido: aceita sessão admin legítima (caso correto)',
    $resultado === true
);

// Teste 6: Corrigido rejeita tentativa de usar sessão de outro usuário
// Mesmo que atacante saiba o ID de sessão de outro, a decisão vem do servidor
$resultado = \Corrigido\usuarioEhAdmin('sessao_usuario_2', $sessoesServer);
verificar(
    'Corrigido: rejeita tentativa de usar sessão de outro usuário normal',
    $resultado === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
