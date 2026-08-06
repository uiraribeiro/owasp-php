<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * LDAP Injection - CORREÇÃO
 * Implementar escape de filtro LDAP que substitui metacaracteres
 * por suas codificações hexadecimais, tornando-os literais.
 * Ordem importante: escapar \ PRIMEIRO, depois os demais.
 */

function escaparFiltroLdap(string $valor): string
{
    // IMPORTANTE: escapar barra invertida PRIMEIRO
    $valor = str_replace("\\", "\\5c", $valor);
    $valor = str_replace("*", "\\2a", $valor);
    $valor = str_replace("(", "\\28", $valor);
    $valor = str_replace(")", "\\29", $valor);
    $valor = str_replace("\x00", "\\00", $valor);
    return $valor;
}

function montarFiltroLdap(string $usuario): string
{
    // CORRIGIDO: escapar o usuário antes de inserir no filtro
    return "(&(uid=" . escaparFiltroLdap($usuario) . ")(objectClass=person))";
}

function demo(): void
{
    echo "=== Demonstração: LDAP Injection (CORRIGIDO) ===\n";

    // Teste legítimo
    echo "\n1. Filtro legítimo:\n";
    $filtro = montarFiltroLdap("joao");
    echo "   Filtro: {$filtro}\n";

    // LDAP Injection - bloqueado
    echo "\n2. LDAP Injection - bloqueado:\n";
    $usuarioMalicioso = "*)(uid=*))(|(uid=*";
    echo "   Input: {$usuarioMalicioso}\n";
    $filtro = montarFiltroLdap($usuarioMalicioso);
    echo "   Filtro: {$filtro}\n";
    if (!str_contains($filtro, "*)(uid=*))(|(uid=*")) {
        echo "   ✓ Metacaracteres foram escapados, estrutura LDAP intacta\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
