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

// XML malicioso com XXE
$xmlMalicioso = '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/hostname">]><root>&xxe;</root>';

// XML legítimo simples
$xmlLegitimo = '<root>ola mundo</root>';

// Teste 1: Vulnerável processa XML malicioso (não rejeita)
$resultadoVulneravel = \Vulneravel\analisarXmlDeUsuario($xmlMalicioso);
verificar(
    'Vulnerável processa XML malicioso com XXE (não rejeita)',
    $resultadoVulneravel !== null
);

// Teste 2: Vulnerável processa XML legítimo
$resultadoVulneravelLegitimo = \Vulneravel\analisarXmlDeUsuario($xmlLegitimo);
verificar(
    'Vulnerável processa XML legítimo',
    $resultadoVulneravelLegitimo !== null &&
    str_contains($resultadoVulneravelLegitimo, 'ola mundo')
);

// Teste 3: Corrigido rejeita XML malicioso com DOCTYPE
$resultadoCorrigido = \Corrigido\analisarXmlDeUsuario($xmlMalicioso);
verificar(
    'Corrigido rejeita XML malicioso com DOCTYPE/ENTITY',
    $resultadoCorrigido === null
);

// Teste 4: Corrigido processa XML legítimo normalmente
$resultadoCorrigidoLegitimo = \Corrigido\analisarXmlDeUsuario($xmlLegitimo);
verificar(
    'Corrigido processa XML legítimo',
    $resultadoCorrigidoLegitimo !== null &&
    str_contains($resultadoCorrigidoLegitimo, 'ola mundo')
);

// Teste 5: Corrigido rejeita XML com <!ENTITY em minúsculas
$xmlComEntity = '<?xml version="1.0"?><!entity test "value"><root>test</root>';
$resultadoEntity = \Corrigido\analisarXmlDeUsuario($xmlComEntity);
verificar(
    'Corrigido rejeita XML com <!ENTITY (case-insensitive)',
    $resultadoEntity === null
);

// Teste 6: Corrigido rejeita XML com <!DOCTYPE em minúsculas
$xmlComDoctype = '<?xml version="1.0"?><!doctype root><root>test</root>';
$resultadoDoctype = \Corrigido\analisarXmlDeUsuario($xmlComDoctype);
verificar(
    'Corrigido rejeita XML com <!DOCTYPE (case-insensitive)',
    $resultadoDoctype === null
);

// Teste 7: XML vazio/inválido retorna null em ambos
$xmlInvalido = 'nao eh xml';
$resultadoVulneravelInvalido = \Vulneravel\analisarXmlDeUsuario($xmlInvalido);
$resultadoCorrigidoInvalido = \Corrigido\analisarXmlDeUsuario($xmlInvalido);
verificar(
    'Ambos rejeitam XML inválido',
    $resultadoVulneravelInvalido === null &&
    $resultadoCorrigidoInvalido === null
);

// Teste 8: Corrigido com XML multilinha legítimo
$xmlMultilinha = "<?xml version=\"1.0\"?>\n<root>\n  <item>teste</item>\n</root>";
$resultadoMultilinha = \Corrigido\analisarXmlDeUsuario($xmlMultilinha);
verificar(
    'Corrigido processa XML multilinha legítimo',
    $resultadoMultilinha !== null
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
