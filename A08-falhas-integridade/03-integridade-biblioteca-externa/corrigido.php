<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Verificar Hash SHA256 com hash_equals()
 *
 * Antes de carregar uma biblioteca baixada, o sistema calcula o hash SHA256
 * do conteúdo e o compara com o hash esperado (conhecido de fonte confiável).
 * Qualquer adulteração é detectada. Use hash_equals() para evitar timing attacks.
 */

function carregarBibliotecaExterna(string $conteudoBaixado, string $hashEsperadoSha256): bool {
    // CORRIGIDO: verifica o hash SHA256 antes de aceitar
    $hashCalculado = hash('sha256', $conteudoBaixado);
    return hash_equals($hashEsperadoSha256, $hashCalculado);
}

function demo(): void {
    echo "=== CORRIGIDO: Biblioteca externa com verificação de hash ===\n";

    $conteudoOriginal = "codigo-da-biblioteca-v1.0";
    $hashEsperado = hash('sha256', $conteudoOriginal);

    $resultado = carregarBibliotecaExterna($conteudoOriginal, $hashEsperado);

    if ($resultado) {
        echo "OK! Biblioteca legítima foi carregada (hash válido)\n";
    } else {
        echo "Erro: Biblioteca legítima foi rejeitada\n";
    }

    // Simular MITM que adultera o conteúdo
    $conteudoAdulterado = "codigo-da-biblioteca-v1.0-COM-BACKDOOR";
    $resultado = carregarBibliotecaExterna($conteudoAdulterado, $hashEsperado);

    if (!$resultado) {
        echo "OK! Biblioteca adulterada foi rejeitada (hash não bate)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
