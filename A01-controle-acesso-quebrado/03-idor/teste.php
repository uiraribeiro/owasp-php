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

$pedidos = [
    ['id' => 1, 'dono_id' => 1, 'valor' => 100.0, 'descricao' => 'Pedido do usuário 1'],
    ['id' => 2, 'dono_id' => 2, 'valor' => 250.0, 'descricao' => 'Pedido do usuário 2'],
    ['id' => 3, 'dono_id' => 3, 'valor' => 500.0, 'descricao' => 'Pedido do usuário 3'],
];

// Teste 1: Vulnerável permite acesso a pedido de outro usuário (IDOR)
$pedidoVulneravel = \Vulneravel\buscarPedido($pedidos, 2, 1);
verificar(
    'Vulnerável retorna pedido de outro usuário (IDOR explorado)',
    $pedidoVulneravel !== null && $pedidoVulneravel['dono_id'] === 2
);

// Teste 2: Corrigido bloqueia acesso a pedido de outro usuário
$pedidoCorrigido = \Corrigido\buscarPedido($pedidos, 2, 1);
verificar(
    'Corrigido bloqueia acesso a pedido de outro usuário',
    $pedidoCorrigido === null
);

// Teste 3: Vulnerável permite acesso ao próprio pedido
$pedidoProprio = \Vulneravel\buscarPedido($pedidos, 1, 1);
verificar(
    'Vulnerável permite acesso ao próprio pedido',
    $pedidoProprio !== null && $pedidoProprio['id'] === 1
);

// Teste 4: Corrigido permite acesso ao próprio pedido
$pedidoProprioCorrigido = \Corrigido\buscarPedido($pedidos, 1, 1);
verificar(
    'Corrigido permite acesso ao próprio pedido (caso legítimo)',
    $pedidoProprioCorrigido !== null && $pedidoProprioCorrigido['id'] === 1
);

// Teste 5: Usuário 2 não consegue acessar pedido de usuário 3
$pedido3Vulneravel = \Vulneravel\buscarPedido($pedidos, 3, 2);
verificar(
    'Vulnerável permite qualquer usuário acessar qualquer pedido',
    $pedido3Vulneravel !== null && $pedido3Vulneravel['id'] === 3
);

// Teste 6: Corrigido bloqueia usuário 2 de acessar pedido 3
$pedido3Corrigido = \Corrigido\buscarPedido($pedidos, 3, 2);
verificar(
    'Corrigido bloqueia acesso cruzado entre usuários',
    $pedido3Corrigido === null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
