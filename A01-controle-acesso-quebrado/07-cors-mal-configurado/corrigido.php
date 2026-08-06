<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: CORS Configurado com Allow-List de Domínios Confiáveis
 *
 * O servidor mantém uma lista explícita de domínios confiáveis.
 * Somente requisições dessas origens recebem os headers CORS com credentials.
 * Requisições de origem desconhecida são bloqueadas.
 */

// Allow-list de domínios confiáveis
const DOMINIOS_CONFIADOS = [
    'https://meusite.com',
    'https://app.meusite.com',
    'https://localhost:3000',
];

function gerarCabecalhosCors(string $origemRequisicao): array {
    // CORRIGIDO: valida contra allow-list
    if (!in_array($origemRequisicao, DOMINIOS_CONFIADOS, true)) {
        // Origem não confiável: não inclui headers CORS
        return [];
    }

    // Apenas domínios confiáveis recebem CORS com credentials
    return [
        'Access-Control-Allow-Origin' => $origemRequisicao,
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
    ];
}

function demo(): void {
    echo "=== CORRIGIDO: CORS com Allow-List ===\n";

    $origemLegitima = 'https://meusite.com';
    $origemMaliciosa = 'https://atacante.com';

    $headersLegitimos = gerarCabecalhosCors($origemLegitima);
    echo "Headers para origem confiável: " . json_encode($headersLegitimos) . "\n";

    $headersMaliciosos = gerarCabecalhosCors($origemMaliciosa);
    echo "Headers para origem maliciosa: " . json_encode($headersMaliciosos) . " (bloqueado! vazio)\n";
}

if (debug_backtrace() === []) {
    demo();
}
