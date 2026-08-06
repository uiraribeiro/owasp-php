<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Não usar extract()
 *
 * A forma corrigida evita extract() completamente.
 * Parse a query string em um array isolado e acesse os valores explicitamente.
 *
 * Isso previne Variable Injection e Variable Overwrite attacks.
 */

function verificarAcesso(string $queryString): bool {
    $isAdmin = false;

    // CORRIGIDO: Não usar extract()
    // A query string é parseada em um array isolado $dados
    parse_str($queryString, $dados);

    // Acesso explícito e seguro aos valores
    $parametroIsAdmin = $dados['isAdmin'] ?? null;

    // A variável local $isAdmin não foi modificada, então permanece false
    return $isAdmin;
}

function demo(): void {
    echo "=== CORRIGIDO: Sem extract(), acesso explícito aos dados ===\n";

    $resultado = verificarAcesso("isAdmin=1");

    if ($resultado) {
        echo "Usuário se elevou para admin\n";
    } else {
        echo "Acesso bloqueado (seguro!). Usuário permanece não-admin\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
