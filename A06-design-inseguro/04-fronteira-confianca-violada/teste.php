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

$dadosConfiaveisDoServidor = [
    'isAdmin' => false,
    'usuario_id' => 42,
    'email' => 'usuario@example.com',
];

$inputDoUsuarioAtacante = [
    'isAdmin' => true,  // Tentativa maliciosa de escalar privilégio
];

// Teste 1: Vulnerável permite que atacante sobrescreva isAdmin
$sessaoVulneravel = \Vulneravel\montarSessao($dadosConfiaveisDoServidor, $inputDoUsuarioAtacante);
verificar(
    'Vulnerável: atacante consegue sobrescrever isAdmin para true',
    $sessaoVulneravel['isAdmin'] === true
);

// Teste 2: Vulnerável permite que atacante sobrescreva email
$inputAtaque2 = ['email' => 'hacker@evil.com'];
$sessaoVulneravel2 = \Vulneravel\montarSessao($dadosConfiaveisDoServidor, $inputAtaque2);
verificar(
    'Vulnerável: atacante consegue sobrescrever email',
    $sessaoVulneravel2['email'] === 'hacker@evil.com'
);

// Teste 3: Corrigido isola dados em namespaces distintos
$sessaoCorrigida = \Corrigido\montarSessao($dadosConfiaveisDoServidor, $inputDoUsuarioAtacante);
verificar(
    'Corrigido: possui namespace "servidor" para dados confiáveis',
    isset($sessaoCorrigida['servidor'])
);

// Teste 4: Corrigido isola input do usuário
verificar(
    'Corrigido: possui namespace "usuario" para input do usuário',
    isset($sessaoCorrigida['usuario'])
);

// Teste 5: Corrigido: dados confiáveis do servidor não são sobrescritos
verificar(
    'Corrigido: servidor.isAdmin permanece false (intocável)',
    $sessaoCorrigida['servidor']['isAdmin'] === false
);

// Teste 6: Corrigido: input malicioso fica isolado em 'usuario'
verificar(
    'Corrigido: usuario.isAdmin fica true (isolado, sem afetar segurança)',
    $sessaoCorrigida['usuario']['isAdmin'] === true
);

// Teste 7: Função verificarPrivilegio acessa sempre servidor
$ehAdminCorrigido = \Corrigido\verificarPrivilegio($sessaoCorrigida);
verificar(
    'Corrigido: verificarPrivilegio() retorna false (lê sempre de servidor)',
    $ehAdminCorrigido === false
);

// Teste 8: Email original do servidor é preservado
verificar(
    'Corrigido: servidor.email preservado = "usuario@example.com"',
    $sessaoCorrigida['servidor']['email'] === 'usuario@example.com'
);

// Teste 9: Usuario_id original é preservado
verificar(
    'Corrigido: servidor.usuario_id preservado = 42',
    $sessaoCorrigida['servidor']['usuario_id'] === 42
);

// Teste 10: Mesmo com múltiplos ataques, dados confiáveis permanecem seguros
$inputMultiploAtaque = [
    'isAdmin' => true,
    'usuario_id' => 999,
    'email' => 'hacker@evil.com',
];
$sessaoMultiploAtaque = \Corrigido\montarSessao($dadosConfiaveisDoServidor, $inputMultiploAtaque);
verificar(
    'Corrigido: múltiplos campos maliciosos não afetam dados do servidor',
    $sessaoMultiploAtaque['servidor']['usuario_id'] === 42
    && $sessaoMultiploAtaque['servidor']['isAdmin'] === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
