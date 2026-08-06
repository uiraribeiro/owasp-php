<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Atualização Não Assinada - A08:2025 Software or Data Integrity Failures
 *
 * O sistema sempre aplica uma atualização de software baixada, sem verificar
 * a assinatura criptográfica. Um atacante MITM pode substituir o arquivo
 * por uma versão maliciosa e o servidor aplica sem questionar.
 */

function aplicarAtualizacao(string $conteudoBaixado, ?string $assinaturaBase64, string $chavePublicaConfiavel): bool {
    // VULNERÁVEL: sempre retorna true, sem verificar assinatura nenhuma
    return true;
}

function demo(): void {
    echo "=== VULNERÁVEL: Atualização sem verificação de assinatura ===\n";

    $conteudoMalicioso = "versao-2.0-firmware-COMPROMETIDO";
    $assinaturaDoAtacante = "qualquer-coisa-aleatoria";

    $resultado = aplicarAtualizacao($conteudoMalicioso, $assinaturaDoAtacante, "chave-publica-qualquer");

    if ($resultado) {
        echo "PROBLEMA! Atualização maliciosa foi aceita sem validação de assinatura!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
