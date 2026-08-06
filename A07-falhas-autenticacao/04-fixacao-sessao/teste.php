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

$idFixado = 'id-fixado-pelo-atacante-xyz123';

// Teste 1: Vulnerável retorna exatamente o mesmo id fixado
$idVulneravel = \Vulneravel\autenticarERetornarIdSessao(
    'usuario',
    'senha_correta',
    'senha_correta',
    $idFixado
);
verificar(
    'Vulnerável retorna o mesmo id de sessão pré-existente (session fixation)',
    $idVulneravel === $idFixado
);

// Teste 2: Corrigido retorna um id DIFERENTE do id fixado
$idCorrigido = \Corrigido\autenticarERetornarIdSessao(
    'usuario',
    'senha_correta',
    'senha_correta',
    $idFixado
);
verificar(
    'Corrigido retorna um novo id diferente do pré-existente',
    $idCorrigido !== $idFixado
);

// Teste 3: Corrigido gera id novo a cada login
$idCorrigido2 = \Corrigido\autenticarERetornarIdSessao(
    'usuario',
    'senha_correta',
    'senha_correta',
    $idFixado
);
verificar(
    'Corrigido gera um novo id em cada login (ids são diferentes entre logins)',
    $idCorrigido !== $idCorrigido2
);

// Teste 4: Corrigido gera id com comprimento adequado (random_bytes(16) = 32 hex chars)
verificar(
    'Corrigido gera id de sessão com comprimento adequado (32 caracteres hex)',
    strlen($idCorrigido) === 32
);

// Teste 5: Corrigido retorna null se senha estiver incorreta
$idIncorreto = \Corrigido\autenticarERetornarIdSessao(
    'usuario',
    'senha_errada',
    'senha_correta',
    $idFixado
);
verificar(
    'Corrigido retorna null se senha estiver incorreta',
    $idIncorreto === null
);

// Teste 6: Vulnerável retorna null se senha estiver incorreta
$idVulneravelIncorreto = \Vulneravel\autenticarERetornarIdSessao(
    'usuario',
    'senha_errada',
    'senha_correta',
    $idFixado
);
verificar(
    'Vulnerável retorna null se senha estiver incorreta (comportamento esperado)',
    $idVulneravelIncorreto === null
);

// Teste 7: Corrigido com diferentes ids fixados sempre retorna ids diferentes
$idFixado2 = 'outro-id-fixado-abc';
$idCorrigido3 = \Corrigido\autenticarERetornarIdSessao(
    'usuario2',
    'senha2',
    'senha2',
    $idFixado2
);
verificar(
    'Corrigido gera ids únicos mesmo com diferentes ids fixados',
    $idCorrigido3 !== $idFixado2 && $idCorrigido3 !== $idCorrigido && ctype_xdigit($idCorrigido3)
);

// Teste 8: Novo id é alfanumérico hexadecimal
verificar(
    'Corrigido gera id com caracteres hexadecimais válidos',
    ctype_xdigit($idCorrigido)
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
