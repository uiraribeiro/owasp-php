<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

/**
 * A03:2025 - Software Supply Chain Failures
 * 06: Função Obsoleta / Perigosa (CWE-446, CWE-447)
 *
 * Este teste verifica se a aplicação usa funções perigosas
 * como extract() que causam Variable Overwrite e Variable Injection.
 * extract() ainda existe em PHP mas é marcada como perigosa e deprecada
 * em contextos de segurança, pois permite manipulação não controlada
 * de escopo local.
 */

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void {
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

// Teste 1: Versão vulnerável é explorada via extract injection
$resultadoVulneravel = \Vulneravel\verificarAcesso("isAdmin=1");
verificar(
    'Vulnerável retorna true (variável $isAdmin foi sobrescrita por extract)',
    $resultadoVulneravel === true
);

// Teste 2: Versão vulnerável com query string vazia
$resultadoVulneravelVazio = \Vulneravel\verificarAcesso("");
verificar(
    'Vulnerável retorna false com query string vazia',
    $resultadoVulneravelVazio === false
);

// Teste 3: Versão vulnerável com tentativa de manipulação via query string
$resultadoVulneravelHack = \Vulneravel\verificarAcesso("isAdmin=0&username=admin");
verificar(
    'Vulnerável retorna false quando isAdmin=0 (suscetível à lógica de query string)',
    $resultadoVulneravelHack === false
);

// Teste 4: Versão corrigida bloqueia injection mesmo com isAdmin=1
$resultadoCorrigido = \Corrigido\verificarAcesso("isAdmin=1");
verificar(
    'Corrigido retorna false (variável $isAdmin não foi sobrescrita, sem extract)',
    $resultadoCorrigido === false
);

// Teste 5: Versão corrigida não é afetada por tentativas de injection com múltiplos parâmetros
$resultadoCorrigidoHack = \Corrigido\verificarAcesso("isAdmin=1&username=admin&isAdmin=true");
verificar(
    'Corrigido retorna false mesmo com múltiplas injeções (sem extract)',
    $resultadoCorrigidoHack === false
);

// Teste 6: Verificar que a versão vulnerável pode ser explorada
// (diferença comportamental entre vulnerável e corrigido é clara)
$diferencaBehavior = $resultadoVulneravel !== $resultadoCorrigido;
verificar(
    'Há diferença de comportamento entre vulnerável e corrigido',
    $diferencaBehavior === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
