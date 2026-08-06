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

// Teste 1: Vulnerável com status desconhecido retorna true (fail-open)
$statusDesconhecido = 'suspensa_por_erro_de_sistema';
$acessoVulneravel = \Vulneravel\temAcesso($statusDesconhecido);
verificar(
    'Vulnerável retorna true para status desconhecido (fail-open perigoso)',
    $acessoVulneravel === true
);

// Teste 2: Corrigido com status desconhecido retorna false (fail-safe)
$acessoCorrigido = \Corrigido\temAcesso($statusDesconhecido);
verificar(
    'Corrigido retorna false para status desconhecido (fail-safe seguro)',
    $acessoCorrigido === false
);

// Teste 3: Ambos negam acesso a 'bloqueada'
$negaBloqueada1 = \Vulneravel\temAcesso('bloqueada') === false;
$negaBloqueada2 = \Corrigido\temAcesso('bloqueada') === false;
verificar(
    'Ambos retornam false para status bloqueada',
    $negaBloqueada1 && $negaBloqueada2
);

// Teste 4: Ambos negam acesso a 'banida'
$negaBanida1 = \Vulneravel\temAcesso('banida') === false;
$negaBanida2 = \Corrigido\temAcesso('banida') === false;
verificar(
    'Ambos retornam false para status banida',
    $negaBanida1 && $negaBanida2
);

// Teste 5: Vulnerável permite qualquer outro status (fail-open)
$outrosStatusos = ['suspensa', 'ativa', 'em_analise', 'verificacao_pendente'];
$vulneravelPermiteTudo = true;
foreach ($outrosStatusos as $status) {
    if (\Vulneravel\temAcesso($status) === false) {
        $vulneravelPermiteTudo = false;
        break;
    }
}
verificar(
    'Vulnerável permite qualquer status não previsto (fail-open)',
    $vulneravelPermiteTudo
);

// Teste 6: Corrigido ativa permissão apenas para 'ativa'
$acessoAtiva = \Corrigido\temAcesso('ativa') === true;
$inegatosOutros = \Corrigido\temAcesso('suspensa') === false &&
                   \Corrigido\temAcesso('em_analise') === false;
verificar(
    'Corrigido permite apenas status ativa e nega outros',
    $acessoAtiva && $inegatosOutros
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
