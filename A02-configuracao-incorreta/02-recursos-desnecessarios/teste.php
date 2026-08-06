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

// Teste 1: Vulnerável expõe debug endpoint em produção
$respostaProdVulneravel = \Vulneravel\tratarRota('/debug/phpinfo', 'producao');
verificar(
    'Vulnerável expõe /debug/phpinfo em produção com status 200 (falha)',
    $respostaProdVulneravel['status'] === 200
);

// Teste 2: Vulnerável contém informações sensíveis na resposta
verificar(
    'Vulnerável retorna informações sensíveis (PHP version, extensões)',
    str_contains($respostaProdVulneravel['corpo'], 'PHP Version') ||
    str_contains($respostaProdVulneravel['corpo'], 'Extensions')
);

// Teste 3: Corrigido bloqueia debug endpoint em produção
$respostaProdCorrigido = \Corrigido\tratarRota('/debug/phpinfo', 'producao');
verificar(
    'Corrigido retorna 404 para /debug/phpinfo em produção',
    $respostaProdCorrigido['status'] === 404
);

// Teste 4: Corrigido não expõe informações sensíveis em produção
verificar(
    'Corrigido não retorna informações sensíveis em produção',
    !str_contains($respostaProdCorrigido['corpo'], 'PHP Version') &&
    !str_contains($respostaProdCorrigido['corpo'], 'Extensions')
);

// Teste 5: Corrigido permite debug endpoint em desenvolvimento
$respostaProdCorrigidoDev = \Corrigido\tratarRota('/debug/phpinfo', 'desenvolvimento');
verificar(
    'Corrigido retorna 200 para /debug/phpinfo em desenvolvimento',
    $respostaProdCorrigidoDev['status'] === 200
);

// Teste 6: Corrigido expõe informações em desenvolvimento (útil)
verificar(
    'Corrigido retorna informações em desenvolvimento',
    str_contains($respostaProdCorrigidoDev['corpo'], 'PHP Version')
);

// Teste 7: Rota principal funciona em ambos os ambientes (vulnerável)
$rotaPrincipalVulneravel = \Vulneravel\tratarRota('/', 'producao');
verificar(
    'Vulnerável: rota principal funciona em produção',
    $rotaPrincipalVulneravel['status'] === 200
);

// Teste 8: Rota principal funciona em ambos os ambientes (corrigido)
$rotaPrincipalCorrigido = \Corrigido\tratarRota('/', 'producao');
verificar(
    'Corrigido: rota principal funciona em produção',
    $rotaPrincipalCorrigido['status'] === 200
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
