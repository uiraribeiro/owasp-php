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

// Teste 1: Vulnerável bloqueia /api/v1/perfil sem autenticação
$vulnV1Bloqueado = \Vulneravel\tratarRequisicao('/api/v1/perfil', null);
verificar(
    'Vulnerável bloqueia /api/v1/perfil sem autenticação',
    $vulnV1Bloqueado['status'] === 401
);

// Teste 2: Vulnerável permite /api/legacy/perfil SEM autenticação (BYPASS)
$vulnLegacyBypass = \Vulneravel\tratarRequisicao('/api/legacy/perfil', null);
verificar(
    'Vulnerável permite /api/legacy/perfil sem autenticação (bypass!)',
    $vulnLegacyBypass['status'] === 200
);

// Teste 3: Corrigido bloqueia /api/v1/perfil sem autenticação
$corrV1Bloqueado = \Corrigido\tratarRequisicao('/api/v1/perfil', null);
verificar(
    'Corrigido bloqueia /api/v1/perfil sem autenticação',
    $corrV1Bloqueado['status'] === 401
);

// Teste 4: Corrigido bloqueia /api/legacy/perfil SEM autenticação (protegido!)
$corrLegacyBloqueado = \Corrigido\tratarRequisicao('/api/legacy/perfil', null);
verificar(
    'Corrigido bloqueia /api/legacy/perfil sem autenticação',
    $corrLegacyBloqueado['status'] === 401
);

// Teste 5: Vulnerável permite /api/v1/perfil COM autenticação
$sessao = ['usuario' => 'joao', 'email' => 'joao@example.com'];
$vulnV1Permitido = \Vulneravel\tratarRequisicao('/api/v1/perfil', $sessao);
verificar(
    'Vulnerável permite /api/v1/perfil com autenticação',
    $vulnV1Permitido['status'] === 200
);

// Teste 6: Vulnerável permite /api/legacy/perfil COM autenticação
$vulnLegacyPermitido = \Vulneravel\tratarRequisicao('/api/legacy/perfil', $sessao);
verificar(
    'Vulnerável permite /api/legacy/perfil com autenticação',
    $vulnLegacyPermitido['status'] === 200
);

// Teste 7: Corrigido permite /api/v1/perfil COM autenticação
$corrV1Permitido = \Corrigido\tratarRequisicao('/api/v1/perfil', $sessao);
verificar(
    'Corrigido permite /api/v1/perfil com autenticação (caso legítimo)',
    $corrV1Permitido['status'] === 200
);

// Teste 8: Corrigido permite /api/legacy/perfil COM autenticação
$corrLegacyPermitido = \Corrigido\tratarRequisicao('/api/legacy/perfil', $sessao);
verificar(
    'Corrigido permite /api/legacy/perfil com autenticação (caso legítimo)',
    $corrLegacyPermitido['status'] === 200
);

// Teste 9: Ambos retornam 404 para rota inexistente
$vulnNotFound = \Vulneravel\tratarRequisicao('/api/inexistente', $sessao);
$corrNotFound = \Corrigido\tratarRequisicao('/api/inexistente', $sessao);
verificar(
    'Ambos retornam 404 para rota inexistente',
    $vulnNotFound['status'] === 404 && $corrNotFound['status'] === 404
);

// Teste 10: Corrigido retorna corpo com erro quando não autenticado
verificar(
    'Corrigido retorna erro quando não autenticado',
    isset($corrV1Bloqueado['corpo']['erro']) && $corrV1Bloqueado['corpo']['erro'] === 'não autenticado'
);

// Teste 11: Dados retornados em autenticação bem-sucedida são corretos
verificar(
    'Corrigido retorna dados do usuário autenticado em /api/v1/perfil',
    $corrV1Permitido['corpo']['usuario'] === 'joao' && $corrV1Permitido['corpo']['email'] === 'joao@example.com'
);

// Teste 12: Dados retornados em autenticação bem-sucedida são corretos na legacy
verificar(
    'Corrigido retorna dados do usuário autenticado em /api/legacy/perfil',
    $corrLegacyPermitido['corpo']['usuario'] === 'joao' && $corrLegacyPermitido['corpo']['email'] === 'joao@example.com'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
