<?php
declare(strict_types=1);

/**
 * A03:2025 - Software Supply Chain Failures
 * 01: Versão Vulnerável Conhecida
 *
 * Este teste verifica se as dependências declaradas em composer.json
 * incluem versões conhecidamente vulneráveis, com CVE registrados.
 *
 * Na vida real, essa verificação seria feita por:
 * - composer audit (consultando banco de dados de CVE do Packagist)
 * - GitHub Dependabot
 * - Snyk
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

/**
 * Base de dados fictícia de versões conhecidamente vulneráveis.
 * Em um cenário real, isso viria de um banco de dados de CVE externo.
 *
 * Formato: ['package/nome' => ['1.0.0', '1.1.0', '1.2.0', ...]]
 */
$BASE_VULNERABILIDADES_CONHECIDAS = [
    'acme/pagamentos' => ['1.0.0', '1.1.0', '1.2.0'],  // CVE-2024-12345 (fictício)
    'acme/autenticacao' => ['0.5.0', '0.6.0'],         // CVE-2024-12346 (fictício)
];

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

// Teste 1: Verificar se versão vulnerável contém pacote com CVE
$temVulnerabilidade = false;
foreach ($composerVulneravel['require'] as $pacote => $versao) {
    if (isset($BASE_VULNERABILIDADES_CONHECIDAS[$pacote])) {
        if (in_array($versao, $BASE_VULNERABILIDADES_CONHECIDAS[$pacote], true)) {
            $temVulnerabilidade = true;
            break;
        }
    }
}

verificar(
    'composer.vulneravel.json contém acme/pagamentos v1.2.0 (versão com CVE)',
    $temVulnerabilidade === true
);

// Teste 2: Verificar se versão corrigida NÃO contém pacote vulnerável
// A constraint "^1.5" significa >=1.5.0 e <2.0.0, excluindo as versões vulneráveis
$naoTemVulnerabilidade = true;
foreach ($composerCorrigido['require'] as $pacote => $versao) {
    if (isset($BASE_VULNERABILIDADES_CONHECIDAS[$pacote])) {
        // A constraint "^1.5" não matcheia nenhuma das versões vulneráveis listadas (1.0, 1.1, 1.2)
        if (in_array($versao, $BASE_VULNERABILIDADES_CONHECIDAS[$pacote], true)) {
            $naoTemVulnerabilidade = false;
            break;
        }
        // Também verificar que a constraint corrigida é diferente das vulneráveis
        foreach ($BASE_VULNERABILIDADES_CONHECIDAS[$pacote] as $versaoVulneravel) {
            if ($versao === $versaoVulneravel) {
                $naoTemVulnerabilidade = false;
                break;
            }
        }
    }
}

verificar(
    'composer.corrigido.json usa constraint "^1.5" (exclui versões vulneráveis)',
    $naoTemVulnerabilidade === true
);

// Teste 3: Verificar semântica de versão (constraint não é exatamente a versão vulnerável)
$versionVulneravelUsada = $composerVulneravel['require']['acme/pagamentos'];
verificar(
    'Versão vulnerável declarada é exatamente 1.2.0',
    $versionVulneravelUsada === '1.2.0'
);

$constraintCorrigidaUsada = $composerCorrigido['require']['acme/pagamentos'];
verificar(
    'Versão corrigida usa constraint semântica ^1.5',
    $constraintCorrigidaUsada === '^1.5'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
