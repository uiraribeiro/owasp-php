<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Design Correto do Modelo de Privilégios
 *
 * A decisão de design correta implementa o princípio do MENOR PRIVILÉGIO:
 * cada role recebe APENAS as permissões que são absolutamente necessárias
 * para sua função. Editor edita e publica conteúdo. Admin faz tudo. Leitor só lê.
 * Permissões sensíveis como gerenciar pagamentos ou deletar usuários são EXPLICITAMENTE
 * restritas apenas aos roles autorizados via allow-list.
 */

function obterPermissoes(string $role): array {
    // CORRIGIDO: cada role tem APENAS as permissões que precisa
    // Aplicado o princípio do MENOR PRIVILÉGIO

    if ($role === 'admin') {
        // Admin tem tudo (por design consciente)
        return [
            'editar_conteudo',
            'publicar_conteudo',
            'excluir_usuarios',
            'gerenciar_pagamentos',
        ];
    }

    if ($role === 'editor') {
        // Editor APENAS edita e publica conteúdo, nada mais
        return [
            'editar_conteudo',
            'publicar_conteudo',
        ];
    }

    if ($role === 'leitor') {
        // Leitor apenas consome conteúdo
        return ['visualizar_conteudo'];
    }

    return [];
}

function obterPermissoesEspeciais(string $role): array {
    // Permissões sensíveis exigem APROVAÇÃO EXPLÍCITA por role
    // Allow-list para operações críticas
    $permissoesSensveis = [
        'admin' => ['excluir_usuarios', 'gerenciar_pagamentos'],
    ];

    return $permissoesSensveis[$role] ?? [];
}

function demo(): void {
    echo "=== CORRIGIDO: Privilégios apropriados para cada role ===\n";

    $permsEditor = obterPermissoes('editor');
    echo "Permissões do 'editor': " . json_encode($permsEditor) . "\n";

    if (!in_array('excluir_usuarios', $permsEditor)) {
        echo "OK: Editor NÃO pode excluir usuários (design apropriado)\n";
    }

    if (!in_array('gerenciar_pagamentos', $permsEditor)) {
        echo "OK: Editor NÃO pode gerenciar pagamentos (design apropriado)\n";
    }

    $permsAdmin = obterPermissoes('admin');
    echo "Permissões do 'admin': " . json_encode($permsAdmin) . "\n";

    if (in_array('excluir_usuarios', $permsAdmin)) {
        echo "OK: Admin pode excluir usuários (apropriado para seu role)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
