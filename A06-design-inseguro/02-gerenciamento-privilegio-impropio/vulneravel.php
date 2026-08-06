<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Gerenciamento de Privilégio Impróprio (CWE-269) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é na distribuição de permissões por role. A arquitetura
 * agrupa o role 'editor' com permissões que ele NUNCA deveria ter (excluir usuários,
 * gerenciar pagamentos). A falha não é um bug de verificação, é a decisão de design
 * incorreta de quais poderes cada role deve ter. Isso viola o princípio do menor privilégio.
 */

function obterPermissoes(string $role): array {
    // VULNERÁVEL: role 'editor' tem permissões demais por DESIGN
    // Um editor de conteúdo deveria NUNCA ter acesso a excluir usuários ou mexer em pagamentos
    if ($role === 'editor') {
        return [
            'editar_conteudo',
            'publicar_conteudo',
            'excluir_usuarios',      // FALHA DE DESIGN: isso não deveria ser permissão de editor!
            'gerenciar_pagamentos',  // FALHA DE DESIGN: isso não deveria ser permissão de editor!
        ];
    }

    if ($role === 'admin') {
        return [
            'editar_conteudo',
            'publicar_conteudo',
            'excluir_usuarios',
            'gerenciar_pagamentos',
        ];
    }

    if ($role === 'leitor') {
        return ['visualizar_conteudo'];
    }

    return [];
}

function demo(): void {
    echo "=== VULNERÁVEL: Privilégios excessivos para role 'editor' ===\n";

    $permsEditor = obterPermissoes('editor');
    echo "Permissões do 'editor': " . json_encode($permsEditor) . "\n";

    if (in_array('excluir_usuarios', $permsEditor)) {
        echo "PROBLEMA: Editor pode excluir usuários (falha de design!)!\n";
    }

    if (in_array('gerenciar_pagamentos', $permsEditor)) {
        echo "PROBLEMA: Editor pode gerenciar pagamentos (falha de design!)!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
