<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Verificação de Assinatura com openssl_verify()
 *
 * Antes de aplicar uma atualização, o sistema verifica que a assinatura
 * criptográfica bate com a chave pública confiável. Assim, apenas
 * atualizações genuínas (assinadas pela chave privada correspondente)
 * são aceitas. MITM/malware não consegue forjar uma assinatura válida.
 */

function aplicarAtualizacao(string $conteudoBaixado, ?string $assinaturaBase64, string $chavePublicaConfiavel): bool {
    // CORRIGIDO: verifica a assinatura criptográfica antes de aceitar
    if ($assinaturaBase64 === null) {
        return false;
    }

    $assinatura = base64_decode($assinaturaBase64, true);
    if ($assinatura === false) {
        return false;
    }

    $resultado = openssl_verify($conteudoBaixado, $assinatura, $chavePublicaConfiavel, OPENSSL_ALGO_SHA256);
    return $resultado === 1;
}

function demo(): void {
    echo "=== CORRIGIDO: Atualização com verificação de assinatura ===\n";

    // Gerar par de chaves RSA confiável
    $config = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
    $chavePrivada = openssl_pkey_new($config);

    // Exportar chave privada e pública
    openssl_pkey_export($chavePrivada, $chavePrivadaPem);
    $detalhesChave = openssl_pkey_get_details($chavePrivada);
    $chavePublica = $detalhesChave['key'];

    // Assinar conteúdo legítimo
    $conteudoLegitimo = "versao-2.0-firmware-genuino";
    openssl_sign($conteudoLegitimo, $assinatura, $chavePrivada, OPENSSL_ALGO_SHA256);
    $assinaturaBase64 = base64_encode($assinatura);

    $resultado = aplicarAtualizacao($conteudoLegitimo, $assinaturaBase64, $chavePublica);

    if ($resultado) {
        echo "OK! Atualização legítima foi aceita (assinatura válida)\n";
    } else {
        echo "Erro: Atualização legítima foi rejeitada\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
