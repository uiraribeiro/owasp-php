<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Replay / Captura de Token (CWE-294) - A07:2025 Authentication Failures
 *
 * O servidor valida um token (ex: de redefinição de senha) mas nunca marca
 * como "já usado". Um atacante que captura este token pode usá-lo repetidamente,
 * indefinidamente, para redefinir senhas ou fazer outras ações sensíveis.
 */

function validarEUsarToken(string $token, string $tokenEsperado, array $tokensJaUsados): array {
    // VULNERÁVEL: valida o token mas NUNCA o marca como usado
    // o mesmo token pode ser usado múltiplas vezes (replay attack)
    $valido = ($token === $tokenEsperado);

    return [
        'valido' => $valido,
        'tokensJaUsados' => $tokensJaUsados,  // nunca adiciona o token à lista
    ];
}

function demo(): void {
    echo "=== VULNERÁVEL: Token pode ser usado múltiplas vezes ===\n";

    $token = 'reset-password-abc123def456';
    $tokenEsperado = 'reset-password-abc123def456';
    $tokensJaUsados = [];

    // Primeira tentativa: token válido
    $resultado1 = validarEUsarToken($token, $tokenEsperado, $tokensJaUsados);
    echo "1ª uso do token: " . ($resultado1['valido'] ? 'VÁLIDO' : 'INVÁLIDO') . "\n";

    // Atacante tenta reusar o mesmo token
    $resultado2 = validarEUsarToken($token, $tokenEsperado, $resultado1['tokensJaUsados']);
    echo "2ª uso do mesmo token (replay): " . ($resultado2['valido'] ? 'VÁLIDO' : 'INVÁLIDO') . " (PROBLEMA!)\n";

    echo "Atacante pode redefinir senha quantas vezes quiser com este token capturado\n";
}

if (debug_backtrace() === []) {
    demo();
}
