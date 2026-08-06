<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Armazenamento de Credenciais Desprotegido (CWE-256) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é a decisão arquitetural de usar criptografia REVERSÍVEL
 * para armazenar senhas, com o objetivo de "poder recuperá-las depois se o usuário
 * esquecer". Isso viola o princípio fundamental de segurança: senhas nunca devem
 * ser recuperáveis, nem mesmo por administradores. A falha não é na implementação
 * do base64_encode, mas na decisão de design que justificou seu uso.
 */

function armazenarCredencial(string $senha): string {
    // VULNERÁVEL: usa codificação REVERSÍVEL
    // Qualquer pessoa com acesso ao banco pode fazer base64_decode() e obter a senha original
    return base64_encode($senha);
}

function demo(): void {
    echo "=== VULNERÁVEL: Armazenamento com criptografia reversível ===\n";

    $senhaOriginal = "minha_senha_super_secreta_123";
    $armazenado = armazenarCredencial($senhaOriginal);

    echo "Senha original: {$senhaOriginal}\n";
    echo "Armazenado no 'banco': {$armazenado}\n";

    $recuperada = base64_decode($armazenado);
    echo "Recuperada por um atacante com acesso ao banco: {$recuperada}\n";
    echo "Senhas REVERSÍVEIS são uma falha de DESIGN grave!\n";
}

if (debug_backtrace() === []) {
    demo();
}
