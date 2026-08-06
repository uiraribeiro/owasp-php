<?php
declare(strict_types=1);

/**
 * A03:2025 - Software Supply Chain Failures
 * 05: Dependência Sem Versão Fixada
 *
 * Este teste verifica se as dependências usam constraint de versão
 * aberta e indeterminística, como "*" ou "dev-*".
 *
 * Constraint "*" significa "qualquer versão" - cada máquina pode
 * instalar versões diferentes, impossibilitando reproducibilidade
 * e aumentando risco de compatibilidade/segurança.
 *
 * Solução: usar semver específico (^1.5, ~2.1, >=1.5 <2.0)
 * E usar composer.lock em produção para garantir versões fixas.
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

// Carregar os dois composer.json
$composerVulneravel = json_decode(
    file_get_contents(__DIR__ . '/composer.vulneravel.json'),
    true
);
$composerCorrigido = json_decode(
    file_get_contents(__DIR__ . '/composer.corrigido.json'),
    true
);

verificar(
    'composer.vulneravel.json foi carregado com sucesso',
    is_array($composerVulneravel) && isset($composerVulneravel['require'])
);

verificar(
    'composer.corrigido.json foi carregado com sucesso',
    is_array($composerCorrigido) && isset($composerCorrigido['require'])
);

// Teste 1: Verificar se versão vulnerável usa constraint "*"
$usaConstraintAberta = false;
foreach ($composerVulneravel['require'] as $pacote => $versao) {
    if ($versao === '*') {
        $usaConstraintAberta = true;
        break;
    }
}

verificar(
    'composer.vulneravel.json usa constraint "*" (indeterminística)',
    $usaConstraintAberta === true
);

// Teste 2: Verificar que a constraint vulnerável é exatamente "*"
$versaoVulneravelAberta = false;
if (isset($composerVulneravel['require']['acme/pagamentos'])) {
    $versao = $composerVulneravel['require']['acme/pagamentos'];
    if ($versao === '*') {
        $versaoVulneravelAberta = true;
    }
}

verificar(
    'Versão vulnerável de acme/pagamentos é exatamente "*"',
    $versaoVulneravelAberta === true
);

// Teste 3: Verificar que versão corrigida NÃO usa constraint "*"
$naoUsaConstraintAberta = true;
foreach ($composerCorrigido['require'] as $pacote => $versao) {
    if ($versao === '*') {
        $naoUsaConstraintAberta = false;
        break;
    }
}

verificar(
    'composer.corrigido.json não usa constraint "*"',
    $naoUsaConstraintAberta === true
);

// Teste 4: Verificar que versão corrigida não usa "dev-*"
$naoUsaDevVersion = true;
foreach ($composerCorrigido['require'] as $pacote => $versao) {
    if (str_starts_with($versao, 'dev-')) {
        $naoUsaDevVersion = false;
        break;
    }
}

verificar(
    'composer.corrigido.json não usa "dev-*" (branch de desenvolvimento)',
    $naoUsaDevVersion === true
);

// Teste 5: Verificar que versão corrigida usa semver específico
$usaSemverEspecifico = false;
if (isset($composerCorrigido['require']['acme/pagamentos'])) {
    $versao = $composerCorrigido['require']['acme/pagamentos'];
    // Semver específico começa com ^, ~, >=, ==, etc
    if (preg_match('/^(\^|~|>=|==|>|\d)/', $versao)) {
        $usaSemverEspecifico = true;
    }
}

verificar(
    'composer.corrigido.json usa constraint semver específica (^1.5)',
    $usaSemverEspecifico === true
);

// Teste 6: Verificar stability mínima
$stabilityVulneravelDev = false;
if (isset($composerVulneravel['minimum-stability'])) {
    if ($composerVulneravel['minimum-stability'] === 'dev') {
        $stabilityVulneravelDev = true;
    }
}

verificar(
    'composer.vulneravel.json tem minimum-stability "dev" (instala versões beta/alpha)',
    $stabilityVulneravelDev === true
);

$stabilityCorrigidoStable = false;
if (isset($composerCorrigido['minimum-stability'])) {
    if ($composerCorrigido['minimum-stability'] === 'stable') {
        $stabilityCorrigidoStable = true;
    }
}

verificar(
    'composer.corrigido.json tem minimum-stability "stable"',
    $stabilityCorrigidoStable === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
