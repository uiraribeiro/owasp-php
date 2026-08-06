<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * CWE-446: Uncontrolled Modification of Free Variables
 * CWE-447: Use of Obsolete/Dangerous Function
 * A03:2025 - Software Supply Chain Failures
 *
 * A função extract() é mantida em PHP por compatibilidade, mas é
 * marcada como perigosa (deprecated em documentações de segurança).
 *
 * Quando usada sem EXTR_PREFIX_ALL ou similar, extract() sobrescreve
 * variáveis locais com valores de um array externo, causando:
 * 1. Variable Injection / Variable Overwrite
 * 2. Possibilidade de sobrescrever variáveis locais importantes
 * 3. Vulnerabilidades graves de Access Control bypass
 *
 * Exemplo: extract(['isAdmin' => 1]) sobrescreve a variável $isAdmin
 * local, causando um bypasse de controle de acesso!
 */

function verificarAcesso(string $queryString): bool {
    $isAdmin = false;

    // VULNERÁVEL: extract() sem controle de prefixo
    // Extrai todas as variáveis do array diretamente no escopo local
    // Se queryString contém "isAdmin=1", parse_str cria um array
    // e extract o sobrescreve na variável local!
    parse_str($queryString, $dados);
    extract($dados);  // PERIGOSO: sobrescreve variáveis locais sem controle

    // Note: $isAdmin pode ter sido sobrescrita por extract() com um valor string!
    // A variável $isAdmin não é mais bool false, mas string "1" ou outro valor
    return (bool) $isAdmin;
}

function demo(): void {
    echo "=== VULNERÁVEL: Função perigosa extract() com variable overwrite ===\n";

    // Simulando um usuário não-admin
    $resultado = verificarAcesso("isAdmin=1");

    if ($resultado) {
        echo "PROBLEMA! Usuário conseguiu se elevar para admin via extract injection!\n";
    } else {
        echo "Acesso como não-admin (esperado)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
