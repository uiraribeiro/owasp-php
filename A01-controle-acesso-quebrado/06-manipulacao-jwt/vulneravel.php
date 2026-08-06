<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Manipulação de Metadados JWT (A01:2025 - Broken Access Control)
 *
 * O servidor decodifica o JWT e retorna os claims SEM validar a assinatura.
 * Um atacante pode forjar um token com claims falsos (role=admin) e o servidor
 * aceita sem verificar se a assinatura é válida.
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

    // VULNERÁVEL: não valida a assinatura, apenas decodifica o payload
    if (count($partes) !== 3) {
        return null;
    }

    $headerEncoded = $partes[0];
    $payloadEncoded = $partes[1];
    // $assinatura = $partes[2];  // Ignorada!

    $payloadJson = base64_decode($payloadEncoded, true);
    if (!$payloadJson) {
        return null;
    }

    $claims = json_decode($payloadJson, true);
    if (!is_array($claims)) {
        return null;
    }

    // VULNERÁVEL: retorna os claims sem verificar se a assinatura é válida
    return $claims;
}

function demo(): void {
    echo "=== VULNERÁVEL: JWT sem Validação de Assinatura ===\n";

    $chave = 'chave-secreta-servidor';

    // Token legítimo
    $tokenLegitimo = criarToken(['usuario' => 'alice', 'role' => 'user'], $chave);
    echo "Token legítimo: " . substr($tokenLegitimo, 0, 40) . "...\n";

    // Token forjado manualmente (sem saber a chave)
    $headerForjado = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
    $payloadForjado = base64_encode(json_encode(['usuario' => 'alice', 'role' => 'admin']));
    $tokenForjado = "{$headerForjado}.{$payloadForjado}.assinatura_falsa_qualquer_coisa";

    $claimsForjados = validarTokenERetornarClaims($tokenForjado, $chave);
    echo "Token forjado decodificado: " . json_encode($claimsForjados) . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
