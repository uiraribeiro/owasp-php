<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validar Assinatura do JWT
 *
 * Recalcula o HMAC da parte "header.payload" com a chave secreta
 * e compara com a assinatura recebida usando hash_equals() para
 * evitar timing attacks. Se a assinatura não bater, rejeita o token.
 */

function criarToken(array $claims, string $chaveSecreta): string {
    // Header: HS256
    $header = ['alg' => 'HS256', 'typ' => 'JWT'];
    $headerEncoded = base64_encode(json_encode($header));

    // Payload
    $payloadEncoded = base64_encode(json_encode($claims));

    // Signature
    $mensagem = "{$headerEncoded}.{$payloadEncoded}";
    $assinatura = hash_hmac('sha256', $mensagem, $chaveSecreta, true);
    $assinaturaEncoded = base64_encode($assinatura);

    return "{$mensagem}.{$assinaturaEncoded}";
}

function validarTokenERetornarClaims(string $token, string $chaveSecreta): ?array {
    $partes = explode('.', $token);

    if (count($partes) !== 3) {
        return null;
    }

    $headerEncoded = $partes[0];
    $payloadEncoded = $partes[1];
    $assinaturaRecebida = $partes[2];

    // Decodifica header e payload
    $headerJson = base64_decode($headerEncoded, true);
    $payloadJson = base64_decode($payloadEncoded, true);

    if (!$headerJson || !$payloadJson) {
        return null;
    }

    $header = json_decode($headerJson, true);
    if (!is_array($header) || ($header['alg'] ?? '') !== 'HS256') {
        return null;
    }

    // CORRIGIDO: valida a assinatura recalculando o HMAC
    $mensagem = "{$headerEncoded}.{$payloadEncoded}";
    $assinaturaEsperada = hash_hmac('sha256', $mensagem, $chaveSecreta, true);
    $assinaturaEsperadaEncoded = base64_encode($assinaturaEsperada);

    // Usa hash_equals para evitar timing attack
    if (!hash_equals($assinaturaEsperadaEncoded, $assinaturaRecebida)) {
        return null;  // Assinatura inválida
    }

    $claims = json_decode($payloadJson, true);
    if (!is_array($claims)) {
        return null;
    }

    return $claims;
}

function demo(): void {
    echo "=== CORRIGIDO: JWT com Validação de Assinatura ===\n";

    $chave = 'chave-secreta-servidor';

    // Token legítimo
    $tokenLegitimo = criarToken(['usuario' => 'alice', 'role' => 'user'], $chave);
    $claimsLegitimos = validarTokenERetornarClaims($tokenLegitimo, $chave);
    echo "Token legítimo validado: " . json_encode($claimsLegitimos) . "\n";

    // Token forjado manualmente (sem saber a chave)
    $headerForjado = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payloadForjado = base64_encode(json_encode(['usuario' => 'alice', 'role' => 'admin']));
    $tokenForjado = "{$headerForjado}.{$payloadForjado}.assinatura_falsa_qualquer_coisa";

    $claimsForjados = validarTokenERetornarClaims($tokenForjado, $chave);
    echo "Token forjado (assinatura inválida): " . ($claimsForjados === null ? "NULL (bloqueado!)" : json_encode($claimsForjados)) . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
