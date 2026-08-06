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

$chaveSecreta = 'chave-secreta-servidor';

// Teste 1: Vulnerável aceita token forjado com role=admin
$headerForjado = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
$payloadForjado = base64_encode(json_encode(['usuario' => 'alice', 'role' => 'admin']));
$tokenForjado = "{$headerForjado}.{$payloadForjado}.assinatura_falsa_12345";

$claimsVulneravel = \Vulneravel\validarTokenERetornarClaims($tokenForjado, $chaveSecreta);
verificar(
    'Vulnerável aceita token forjado com role=admin',
    $claimsVulneravel !== null && $claimsVulneravel['role'] === 'admin'
);

// Teste 2: Corrigido rejeita token forjado
$claimsCorrigido = \Corrigido\validarTokenERetornarClaims($tokenForjado, $chaveSecreta);
verificar(
    'Corrigido rejeita token forjado (assinatura inválida)',
    $claimsCorrigido === null
);

// Teste 3: Corrigido aceita token gerado corretamente
$tokenLegitimo = \Corrigido\criarToken(['usuario' => 'alice', 'role' => 'user'], $chaveSecreta);
$claimsLegitimos = \Corrigido\validarTokenERetornarClaims($tokenLegitimo, $chaveSecreta);
verificar(
    'Corrigido aceita token legítimo com assinatura válida',
    $claimsLegitimos !== null && $claimsLegitimos['role'] === 'user'
);

// Teste 4: Vulnerável também aceita token legítimo
$claimsVulneravelLegitimo = \Vulneravel\validarTokenERetornarClaims($tokenLegitimo, $chaveSecreta);
verificar(
    'Vulnerável aceita token legítimo (sem validar assinatura funciona)',
    $claimsVulneravelLegitimo !== null && $claimsVulneravelLegitimo['role'] === 'user'
);

// Teste 5: Corrigido rejeita token com assinatura modificada
$partes = explode('.', $tokenLegitimo);
$tokenModificado = $partes[0] . '.' . $partes[1] . '.assinatura_modificada';
$claimsModificado = \Corrigido\validarTokenERetornarClaims($tokenModificado, $chaveSecreta);
verificar(
    'Corrigido rejeita token com assinatura modificada',
    $claimsModificado === null
);

// Teste 6: Corrigido rejeita token com payload modificado
$novoPayload = base64_encode(json_encode(['usuario' => 'bob', 'role' => 'admin']));
$tokenPayloadModificado = $partes[0] . '.' . $novoPayload . '.' . $partes[2];
$claimsPayloadModificado = \Corrigido\validarTokenERetornarClaims($tokenPayloadModificado, $chaveSecreta);
verificar(
    'Corrigido rejeita token com payload modificado',
    $claimsPayloadModificado === null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
