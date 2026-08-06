<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Gestão SEGURA de chaves criptográficas via variáveis de ambiente
 * OWASP A04:2025 - Cryptographic Failures
 * CORRIGIDO: Chave é lida de variável de ambiente no runtime.
 * Permite rotação sem alterar código. Nunca exposta em repositório.
 */

function obterChaveCriptografia(): string
{
    // USE VARIÁVEIS DE AMBIENTE PARA CHAVES SECRETAS
    // Configurado no: arquivo .env (não commitado), CI/CD secrets,
    // container environment, vault manager, etc.
    $chave = getenv('APP_ENCRYPTION_KEY');

    if ($chave === false || $chave === '') {
        throw new \RuntimeException(
            'APP_ENCRYPTION_KEY não configurada. ' .
            'Configure a variável de ambiente antes de rodar a aplicação.'
        );
    }

    return $chave;
}

function demo(): void
{
    echo "=== Gestão de Chaves SEGURA (variável de ambiente) ===\n";
    try {
        echo "Tentando obter chave de APP_ENCRYPTION_KEY...\n";
        $chave = obterChaveCriptografia();
        echo "Chave obtida com sucesso: " . substr($chave, 0, 10) . "...\n";
    } catch (\RuntimeException $e) {
        echo "Erro esperado: " . $e->getMessage() . "\n";
        echo "Configure APP_ENCRYPTION_KEY antes de usar\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
