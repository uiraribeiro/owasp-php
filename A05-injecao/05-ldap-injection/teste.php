<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void
{
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

// Teste legítimo
$filtroVulneravel = \Vulneravel\montarFiltroLdap("joao");
$filtroCorrigido = \Corrigido\montarFiltroLdap("joao");

verificar(
    "Vulnerável: filtro legítimo é montado corretamente",
    str_contains($filtroVulneravel, "joao")
);

verificar(
    "Corrigido: filtro legítimo é montado corretamente",
    str_contains($filtroCorrigido, "joao")
);

// Teste LDAP Injection
$injectionPayload = "*)(uid=*))(|(uid=*";
$filtroInjectionVulneravel = \Vulneravel\montarFiltroLdap($injectionPayload);
$filtroInjectionCorrigido = \Corrigido\montarFiltroLdap($injectionPayload);

verificar(
    "Vulnerável: injection payload contém * e parênteses desescapados",
    str_contains($filtroInjectionVulneravel, "*)(uid=*))(|(uid=*")
);

// No filtro corrigido, verificar que não há * ou parênteses crus do payload
// O payload deve ter sido escapado: * vira \2a, ( vira \28, ) vira \29
verificar(
    "Corrigido: * foi escapado para \\2a",
    str_contains($filtroInjectionCorrigido, "\\2a")
);

verificar(
    "Corrigido: ( foi escapado para \\28",
    str_contains($filtroInjectionCorrigido, "\\28")
);

verificar(
    "Corrigido: ) foi escapado para \\29",
    str_contains($filtroInjectionCorrigido, "\\29")
);

verificar(
    "Corrigido: injection payload NÃO contém * cru do atacante",
    !str_contains($filtroInjectionCorrigido, "*)(uid=*))(|(uid=*")
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
