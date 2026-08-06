<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * LDAP Injection - FALHA A05:2025 Injection
 * Concatenar entrada do usuário em filtros LDAP sem escape permite
 * que atacantes alterem a lógica da consulta LDAP e acessem/modifiquem dados.
 */

function montarFiltroLdap(string $usuario): string
{
    // VULNERÁVEL: concatenação direta do usuário no filtro LDAP
    // Um atacante pode injetar estruturas LDAP complexas alterando o significado
    return "(&(uid={$usuario})(objectClass=person))";
}

function demo(): void
{
    echo "=== Demonstração: LDAP Injection (VULNERÁVEL) ===\n";

    // Teste legítimo
    echo "\n1. Filtro legítimo:\n";
    $filtro = montarFiltroLdap("joao");
    echo "   Filtro: {$filtro}\n";

    // LDAP Injection
    echo "\n2. LDAP Injection - alteração de lógica:\n";
    $usuarioMalicioso = "*)(uid=*))(|(uid=*";
    echo "   Input: {$usuarioMalicioso}\n";
    $filtro = montarFiltroLdap($usuarioMalicioso);
    echo "   Filtro: {$filtro}\n";
    if (str_contains($filtro, "*)(uid=*))(|(uid=*")) {
        echo "   ⚠️  VULNERÁVEL! Estrutura LDAP foi alterada, filtro agora pode casar com qualquer usuário\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
