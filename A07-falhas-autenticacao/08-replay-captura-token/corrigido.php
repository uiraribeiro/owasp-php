<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validação de Token Uma Única Vez
 *
 * Após usar um token com sucesso, ele é adicionado a uma lista de tokens consumidos.
 * Qualquer tentativa de reutilizar um token já usado falha imediatamente.
 */

function validarEUsarToken(string $token, string $tokenEsperado, array $tokensJaUsados): array {
    // CORRIGIDO: verifica se o token já foi usado
    if (in_array($token, $tokensJaUsados, true)) {
        return [
            'valido' => false,
            'tokensJaUsados' => $tokensJaUsados,
        ];
    }

    // Valida se o token é correto
    if ($token !== $tokenEsperado) {
        return [
            'valido' => false,
            'tokensJaUsados' => $tokensJaUsados,
        ];
    }

    // Token é válido e nunca foi usado antes
    // Marca o token como consumido
    $tokensJaUsados[] = $token;

    return [
        'valido' => true,
        'tokensJaUsados' => $tokensJaUsados,
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: Token pode ser usado apenas uma vez ===\n";

    $token = 'reset-password-abc123def456';
    $tokenEsperado = 'reset-password-abc123def456';
    $tokensJaUsados = [];

    // Primeira tentativa: token válido, agora é marcado como usado
    $resultado1 = validarEUsarToken($token, $tokenEsperado, $tokensJaUsados);
    echo "1ª uso do token: " . ($resultado1['valido'] ? 'VÁLIDO' : 'INVÁLIDO') . " (marcado como usado)\n";

    // Atacante tenta reusar o mesmo token
    $resultado2 = validarEUsarToken($token, $tokenEsperado, $resultado1['tokensJaUsados']);
    echo "2ª uso do mesmo token (replay): " . ($resultado2['valido'] ? 'VÁLIDO' : 'INVÁLIDO') . " (bloqueado!)\n";

    echo "Atacante não consegue reutilizar o token, proteção contra replay (protegido!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
