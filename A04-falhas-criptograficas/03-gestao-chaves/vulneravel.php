<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Gestão inadequada de chaves criptográficas (hardcoded)
 * OWASP A04:2025 - Cryptographic Failures
 * VULNERÁVEL: Chave secreta está hardcoded no código-fonte.
 * Qualquer pessoa com acesso ao repositório/build artefato obtém a chave.
 * Impossível rotacionar sem alterar o código e fazer novo deploy.
 */

function obterChaveCriptografia(): string
{
    // NUNCA HARDCODE CHAVES SECRETAS NO CÓDIGO!
    // Está visível em: logs de git, histórico de commits,
    // artefatos de build, backups, code reviews, etc.
    return "minha-chave-secreta-fixa-123456";
}

function demo(): void
{
    echo "=== Gestão de Chaves VULNERÁVEL (hardcoded) ===\n";
    echo "Chave 1: " . obterChaveCriptografia() . "\n";
    echo "Chave 2: " . obterChaveCriptografia() . "\n";
    echo "Chave 3: " . obterChaveCriptografia() . "\n";
    echo "\nTodas as chaves são IDÊNTICAS (hardcoded)\n";
    echo "Visível em: histórico de git, logs, backups, code reviews\n";
}

if (debug_backtrace() === []) {
    demo();
}
