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

// Teste 1: Vulnerável permite que 'editor' exclua usuários (falha de design)
$permsEditorVulneravel = \Vulneravel\obterPermissoes('editor');
verificar(
    'Vulnerável: editor tem permissão "excluir_usuarios" (falha de design)',
    in_array('excluir_usuarios', $permsEditorVulneravel)
);

// Teste 2: Vulnerável permite que 'editor' gerencie pagamentos (falha de design)
verificar(
    'Vulnerável: editor tem permissão "gerenciar_pagamentos" (falha de design)',
    in_array('gerenciar_pagamentos', $permsEditorVulneravel)
);

// Teste 3: Corrigido NEGA que 'editor' exclua usuários
$permsEditorCorrigido = \Corrigido\obterPermissoes('editor');
verificar(
    'Corrigido: editor NÃO tem permissão "excluir_usuarios"',
    !in_array('excluir_usuarios', $permsEditorCorrigido)
);

// Teste 4: Corrigido NEGA que 'editor' gerencie pagamentos
verificar(
    'Corrigido: editor NÃO tem permissão "gerenciar_pagamentos"',
    !in_array('gerenciar_pagamentos', $permsEditorCorrigido)
);

// Teste 5: Corrigido permite que 'editor' edite conteúdo (legítimo)
verificar(
    'Corrigido: editor TEM permissão "editar_conteudo" (apropriado)',
    in_array('editar_conteudo', $permsEditorCorrigido)
);

// Teste 6: Corrigido permite que 'editor' publique conteúdo (legítimo)
verificar(
    'Corrigido: editor TEM permissão "publicar_conteudo" (apropriado)',
    in_array('publicar_conteudo', $permsEditorCorrigido)
);

// Teste 7: Corrigido permite que 'admin' exclua usuários (apropriado)
$permsAdminCorrigido = \Corrigido\obterPermissoes('admin');
verificar(
    'Corrigido: admin TEM permissão "excluir_usuarios" (apropriado para seu role)',
    in_array('excluir_usuarios', $permsAdminCorrigido)
);

// Teste 8: Corrigido permite que 'admin' gerencie pagamentos (apropriado)
verificar(
    'Corrigido: admin TEM permissão "gerenciar_pagamentos" (apropriado para seu role)',
    in_array('gerenciar_pagamentos', $permsAdminCorrigido)
);

// Teste 9: Corrigido tem allow-list explícita para permissões sensíveis de admin
$sensiveisAdmin = \Corrigido\obterPermissoesEspeciais('admin');
verificar(
    'Corrigido: admin listado em allow-list para permissões sensíveis',
    in_array('excluir_usuarios', $sensiveisAdmin) && in_array('gerenciar_pagamentos', $sensiveisAdmin)
);

// Teste 10: Corrigido não tem allow-list para permissões sensíveis de editor
$sensiveisEditor = \Corrigido\obterPermissoesEspeciais('editor');
verificar(
    'Corrigido: editor NÃO está na allow-list para permissões sensíveis',
    empty($sensiveisEditor)
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
