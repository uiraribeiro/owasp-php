<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Integridade de Biblioteca Externa Não Verificada - A08:2025 Software or Data Integrity Failures
 *
 * O sistema baixa uma biblioteca de um CDN e a "carrega" sem NUNCA conferir
 * o hash SHA256. Um atacante MITM pode substituir o arquivo por uma versão
 * comprometida e o servidor carrega sem questionar.
 */

function carregarBibliotecaExterna(string $conteudoBaixado, string $hashEsperadoSha256): bool {
    // VULNERÁVEL: sempre retorna true, sem verificar o hash
    return true;
}

function demo(): void {
    echo "=== VULNERÁVEL: Biblioteca externa sem verificação de hash ===\n";

    $conteudoComBackdoor = "codigo-da-biblioteca-v1.0-COM-BACKDOOR";
    $hashEsperado = hash('sha256', "codigo-da-biblioteca-v1.0");

    $resultado = carregarBibliotecaExterna($conteudoComBackdoor, $hashEsperado);

    if ($resultado) {
        echo "PROBLEMA! Biblioteca comprometida foi carregada sem validação!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
