<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Uso de modo de criptografia SEGURO (GCM - Galois/Counter Mode)
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: GCM é não-determinístico e autenticado.
 * Blocos idênticos no texto plano geram criptogramas DIFERENTES
 * graças ao IV aleatório e autenticação.
 */

function criptografarEcb(string $texto, string $chave): string
{
    // Normaliza chave para 32 bytes
    $chaveNorm = hash('sha256', $chave, true);

    // USE GCM (ou CBC com IV aleatório)!
    // GCM é não-determinístico e autenticado
    $iv = random_bytes(12);
    $tag = '';
    $textoCifrado = openssl_encrypt($texto, 'aes-256-gcm', $chaveNorm, OPENSSL_RAW_DATA, $iv, $tag);

    if ($textoCifrado === false) {
        throw new \RuntimeException('Falha ao criptografar com GCM');
    }

    // Retorna: IV + TAG + CRIPTOGRAMA
    return $iv . $tag . $textoCifrado;
}

function demo(): void
{
    echo "=== Criptografia GCM SEGURA (modo adequado) ===\n";

    $chave = 'chave-teste';
    $bloco = 'AAAAAAAAAAAAAAAA'; // 16 A's = 1 bloco de 16 bytes
    $texto = $bloco . $bloco; // 2 blocos idênticos = 32 bytes

    echo "Texto (hex): " . bin2hex($texto) . "\n";

    // Criptografa duas vezes o mesmo texto
    $cifra1 = criptografarEcb($texto, $chave);
    $cifra2 = criptografarEcb($texto, $chave);

    echo "Cifra 1 (hex): " . bin2hex($cifra1) . "\n";
    echo "Cifra 2 (hex): " . bin2hex($cifra2) . "\n";

    echo "\nCifras são DIFERENTES mesmo para mesmo texto (IV aleatório)\n";
    echo "GCM não vaza padrões - blocos idênticos produzem criptogramas diferentes!\n";
}

if (debug_backtrace() === []) {
    demo();
}
