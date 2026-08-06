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

// Teste 1: Vulnerável permite path traversal com ".."
$caminhoVulneravel = \Vulneravel\carregarModulo("../../../etc/passwd");
verificar(
    'Vulnerável: gera caminho com ".." (path traversal possível)',
    str_contains($caminhoVulneravel, '..')
);

// Teste 2: Vulnerável permite path traversal com múltiplos níveis
$caminhoVulneravel2 = \Vulneravel\carregarModulo("../../config/database");
verificar(
    'Vulnerável: gera caminho para "../.." (sai do diretório)',
    str_contains($caminhoVulneravel2, '..')
);

// Teste 3: Vulnerável permite "/", incluindo nomes de diretórios
$caminhoVulneravel3 = \Vulneravel\carregarModulo("subdir/modulo");
verificar(
    'Vulnerável: gera caminho com "/" (inclusão de subdiretórios arbitrários)',
    str_contains($caminhoVulneravel3, '/')
);

// Teste 4: Corrigido rejeita path traversal ("..")
$caminhoCorrigido = \Corrigido\carregarModulo("../../../etc/passwd");
verificar(
    'Corrigido: rejeita path traversal ".." (retorna null)',
    $caminhoCorrigido === null
);

// Teste 5: Corrigido rejeita "/" no caminho
$caminhoCorrigido2 = \Corrigido\carregarModulo("subdir/arquivo");
verificar(
    'Corrigido: rejeita "/" (nenhuma inclusão de subdiretórios)',
    $caminhoCorrigido2 === null
);

// Teste 6: Corrigido aceita nome legítimo (alfanumérico + underscore)
$caminhoCorrigido3 = \Corrigido\carregarModulo("relatorios_usuarios");
verificar(
    'Corrigido: aceita nome legítimo "relatorios_usuarios"',
    $caminhoCorrigido3 === "modulos/relatorios_usuarios.php"
);

// Teste 7: Corrigido aceita número
$caminhoCorrigido4 = \Corrigido\carregarModulo("modulo123");
verificar(
    'Corrigido: aceita números no nome',
    $caminhoCorrigido4 === "modulos/modulo123.php"
);

// Teste 8: Corrigido rejeita ponto
$caminhoCorrigido5 = \Corrigido\carregarModulo("modulo.js");
verificar(
    'Corrigido: rejeita "." (nem mesmo para extensões)',
    $caminhoCorrigido5 === null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
