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

$token = 'reset-abc123def456xyz789';
$tokenEsperado = 'reset-abc123def456xyz789';

// Teste 1: Vulnerável permite uso do token na primeira vez
$resultado1Vuln = \Vulneravel\validarEUsarToken($token, $tokenEsperado, []);
verificar(
    'Vulnerável permite uso válido do token na primeira vez',
    $resultado1Vuln['valido'] === true
);

// Teste 2: Vulnerável permite REUSAR o mesmo token (replay attack)
$resultado2Vuln = \Vulneravel\validarEUsarToken(
    $token,
    $tokenEsperado,
    $resultado1Vuln['tokensJaUsados']
);
verificar(
    'Vulnerável permite reusar o mesmo token (replay funciona!)',
    $resultado2Vuln['valido'] === true
);

// Teste 3: Corrigido permite uso do token na primeira vez
$resultado1Corr = \Corrigido\validarEUsarToken($token, $tokenEsperado, []);
verificar(
    'Corrigido permite uso válido do token na primeira vez',
    $resultado1Corr['valido'] === true
);

// Teste 4: Corrigido bloqueia reutilização do mesmo token (replay attack)
$resultado2Corr = \Corrigido\validarEUsarToken(
    $token,
    $tokenEsperado,
    $resultado1Corr['tokensJaUsados']
);
verificar(
    'Corrigido bloqueia reutilização do token (replay bloqueado!)',
    $resultado2Corr['valido'] === false
);

// Teste 5: Corrigido adiciona token à lista após primeiro uso
verificar(
    'Corrigido adiciona token à lista de tokens usados',
    in_array($token, $resultado1Corr['tokensJaUsados'], true)
);

// Teste 6: Vulnerável não adiciona token à lista
verificar(
    'Vulnerável não adiciona token à lista de tokens usados',
    !in_array($token, $resultado1Vuln['tokensJaUsados'], true)
);

// Teste 7: Corrigido bloqueia token inválido
$resultadoInvalidoCorr = \Corrigido\validarEUsarToken(
    'token-errado',
    $tokenEsperado,
    []
);
verificar(
    'Corrigido bloqueia token inválido',
    $resultadoInvalidoCorr['valido'] === false
);

// Teste 8: Vulnerável bloqueia token inválido (comportamento esperado)
$resultadoInvalidoVuln = \Vulneravel\validarEUsarToken(
    'token-errado',
    $tokenEsperado,
    []
);
verificar(
    'Vulnerável bloqueia token inválido (esperado)',
    $resultadoInvalidoVuln['valido'] === false
);

// Teste 9: Corrigido com 3 tokens já usados
$tokensUsados = ['token1', 'token2', 'token3'];
$resultadoComTrês = \Corrigido\validarEUsarToken(
    $token,
    $tokenEsperado,
    $tokensUsados
);
verificar(
    'Corrigido funciona com lista de tokens já usados',
    $resultadoComTrês['valido'] === true && count($resultadoComTrês['tokensJaUsados']) === 4
);

// Teste 10: Terceira tentativa de reutilizar também falha
$resultado3Corr = \Corrigido\validarEUsarToken(
    $token,
    $tokenEsperado,
    $resultado2Corr['tokensJaUsados']
);
verificar(
    'Corrigido continua bloqueando na terceira tentativa (consistente)',
    $resultado3Corr['valido'] === false
);

// Teste 11: Vulnerável permite reusar indefinidamente
$resultado3Vuln = \Vulneravel\validarEUsarToken(
    $token,
    $tokenEsperado,
    $resultado2Vuln['tokensJaUsados']
);
verificar(
    'Vulnerável continua permitindo na terceira tentativa',
    $resultado3Vuln['valido'] === true
);

// Teste 12: Lista de tokens usados é retornada corretamente
verificar(
    'Corrigido retorna lista de tokens atualizada',
    is_array($resultado1Corr['tokensJaUsados']) && is_array($resultado2Corr['tokensJaUsados'])
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
