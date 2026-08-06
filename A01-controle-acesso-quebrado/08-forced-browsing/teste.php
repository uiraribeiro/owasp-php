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

$usuarioComum = ['id' => 1, 'role' => 'user'];
$usuarioAdmin = ['id' => 2, 'role' => 'admin'];

// Teste 1: Vulnerável retorna conteúdo admin para usuário comum
$respostaVulneravel = \Vulneravel\tratarRota('/admin/painel', $usuarioComum);
verificar(
    'Vulnerável retorna conteúdo de admin para usuário comum (Forced Browsing)',
    strpos($respostaVulneravel, 'Painel de Administração') !== false
);

// Teste 2: Corrigido bloqueia usuário comum
$respostaCorrigida = \Corrigido\tratarRota('/admin/painel', $usuarioComum);
verificar(
    'Corrigido bloqueia usuário comum (retorna "Acesso negado")',
    strpos($respostaCorrigida, 'Acesso negado') !== false
);

// Teste 3: Corrigido permite admin
$respostaCorrigidaAdmin = \Corrigido\tratarRota('/admin/painel', $usuarioAdmin);
verificar(
    'Corrigido permite admin acessar painel',
    strpos($respostaCorrigidaAdmin, 'Painel de Administração') !== false
);

// Teste 4: Vulnerável permite admin também
$respostaVulneravelAdmin = \Vulneravel\tratarRota('/admin/painel', $usuarioAdmin);
verificar(
    'Vulnerável permite admin (caso legítimo)',
    strpos($respostaVulneravelAdmin, 'Painel de Administração') !== false
);

// Teste 5: Corrigido bloqueia não autenticado
$respostaNaoAutenticado = \Corrigido\tratarRota('/admin/painel', null);
verificar(
    'Corrigido bloqueia não autenticado (null)',
    strpos($respostaNaoAutenticado, 'Acesso negado') !== false
);

// Teste 6: Ambos permitem /home para usuário comum
$respostaVulneravelHome = \Vulneravel\tratarRota('/home', $usuarioComum);
$respostaCorrigidaHome = \Corrigido\tratarRota('/home', $usuarioComum);
verificar(
    'Ambos permitem rota pública /home para qualquer usuário',
    strpos($respostaVulneravelHome, 'Página inicial') !== false &&
    strpos($respostaCorrigidaHome, 'Página inicial') !== false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
