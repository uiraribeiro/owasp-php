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

// Teste 1: Vulnerável permite DELETE de usuário comum
$respostaVulneravel = \Vulneravel\tratarRequisicaoApi('DELETE', $usuarioComum);
verificar(
    'Vulnerável permite DELETE por usuário comum (status 200)',
    $respostaVulneravel['status'] === 200
);

// Teste 2: Corrigido bloqueia DELETE de usuário comum
$respostaCorrigida = \Corrigido\tratarRequisicaoApi('DELETE', $usuarioComum);
verificar(
    'Corrigido bloqueia DELETE por usuário comum (status 403)',
    $respostaCorrigida['status'] === 403
);

// Teste 3: Corrigido permite DELETE de admin
$respostaCorrigidaAdmin = \Corrigido\tratarRequisicaoApi('DELETE', $usuarioAdmin);
verificar(
    'Corrigido permite DELETE por admin (status 200)',
    $respostaCorrigidaAdmin['status'] === 200
);

// Teste 4: Vulnerável permite PUT de usuário comum
$respostaPutVulneravel = \Vulneravel\tratarRequisicaoApi('PUT', $usuarioComum);
verificar(
    'Vulnerável permite PUT por usuário comum (status 200)',
    $respostaPutVulneravel['status'] === 200
);

// Teste 5: Corrigido bloqueia PUT de usuário comum
$respostaPutCorrigida = \Corrigido\tratarRequisicaoApi('PUT', $usuarioComum);
verificar(
    'Corrigido bloqueia PUT por usuário comum (status 403)',
    $respostaPutCorrigida['status'] === 403
);

// Teste 6: Corrigido permite GET de usuário comum (leitura ok)
$respostaGetComum = \Corrigido\tratarRequisicaoApi('GET', $usuarioComum);
verificar(
    'Corrigido permite GET por qualquer usuário autenticado (status 200)',
    $respostaGetComum['status'] === 200
);

// Teste 7: Ambos bloqueiam não autenticado
$naoAutenticado = [];
$respostaVulneravel401 = \Vulneravel\tratarRequisicaoApi('DELETE', $naoAutenticado);
$respostaCorrigida401 = \Corrigido\tratarRequisicaoApi('DELETE', $naoAutenticado);
verificar(
    'Ambos bloqueiam DELETE não autenticado (status 401)',
    $respostaVulneravel401['status'] === 401 && $respostaCorrigida401['status'] === 401
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
