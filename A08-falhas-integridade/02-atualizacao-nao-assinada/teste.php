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

// Gerar par de chaves RSA confiável
$config = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
$chavePrivadaConfiavel = openssl_pkey_new($config);
openssl_pkey_export($chavePrivadaConfiavel, $chavePrivadaPem);
$detalhesChaveConfiavel = openssl_pkey_get_details($chavePrivadaConfiavel);
$chavePublicaConfiavel = $detalhesChaveConfiavel['key'];

// Gerar par de chaves do "atacante"
$chavePrivadaAtacante = openssl_pkey_new($config);
openssl_pkey_export($chavePrivadaAtacante, $chavePrivadaAtacantePem);

// Conteúdo legítimo e malicioso
$conteudoLegitimo = "versao-2.0-firmware-legitimo";
$conteudoMalicioso = "versao-2.0-firmware-MALICIOSO";

// Assinar conteúdo legítimo com chave confiável
openssl_sign($conteudoLegitimo, $assinaturaLegitima, $chavePrivadaConfiavel, OPENSSL_ALGO_SHA256);
$assinaturaLegitimaBase64 = base64_encode($assinaturaLegitima);

// Assinar conteúdo malicioso com chave do atacante
openssl_sign($conteudoMalicioso, $assinaturaDoAtacante, $chavePrivadaAtacante, OPENSSL_ALGO_SHA256);
$assinaturaDoAtacanteBase64 = base64_encode($assinaturaDoAtacante);

// Teste 1: Vulnerável sempre aceita, mesmo sem assinatura
$resultado = \Vulneravel\aplicarAtualizacao($conteudoMalicioso, null, $chavePublicaConfiavel);
verificar(
    'Vulnerável: aceita atualização maliciosa sem assinatura (PROBLEMA!)',
    $resultado === true
);

// Teste 2: Vulnerável aceita qualquer coisa mesmo com assinatura inválida
$resultado = \Vulneravel\aplicarAtualizacao($conteudoMalicioso, "assinatura-falsa-base64", $chavePublicaConfiavel);
verificar(
    'Vulnerável: aceita qualquer atualização sem verificar assinatura',
    $resultado === true
);

// Teste 3: Corrigido rejeita atualização sem assinatura
$resultado = \Corrigido\aplicarAtualizacao($conteudoMalicioso, null, $chavePublicaConfiavel);
verificar(
    'Corrigido: rejeita atualização sem assinatura',
    $resultado === false
);

// Teste 4: Corrigido rejeita conteúdo malicioso com assinatura do atacante
$resultado = \Corrigido\aplicarAtualizacao($conteudoMalicioso, $assinaturaDoAtacanteBase64, $chavePublicaConfiavel);
verificar(
    'Corrigido: rejeita conteúdo malicioso assinado por chave não confiável',
    $resultado === false
);

// Teste 5: Corrigido rejeita assinatura base64 inválida
$resultado = \Corrigido\aplicarAtualizacao($conteudoLegitimo, "assinatura-base64-invalida!!!", $chavePublicaConfiavel);
verificar(
    'Corrigido: rejeita base64 inválido',
    $resultado === false
);

// Teste 6: Corrigido aceita atualização legítima corretamente assinada
$resultado = \Corrigido\aplicarAtualizacao($conteudoLegitimo, $assinaturaLegitimaBase64, $chavePublicaConfiavel);
verificar(
    'Corrigido: aceita atualização legítima com assinatura válida (caso correto)',
    $resultado === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
