<?php
declare(strict_types=1);

/**
 * A03:2025 - Software Supply Chain Failures
 * 04: Scripts de Instalação Não Confiáveis
 *
 * Este teste verifica a configuração "allow-plugins" do Composer.
 * Se configurado como true (booleano), permite que QUALQUER plugin
 * execute código arbitrário durante "composer install".
 *
 * Um atacante que comprometa uma dependência pode incluir um plugin
 * malicioso que executa código no servidor durante a instalação.
 *
 * Solução: usar allow-list explícita de plugins confiáveis
 * (formato: {"nome/plugin": true})
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

// Teste 1: Verificar se versão vulnerável tem allow-plugins = true (booleano)
$allowPluginsDangerous = false;
if (isset($composerVulneravel['config']['allow-plugins'])) {
    $allowPlugins = $composerVulneravel['config']['allow-plugins'];
    // true (booleano) é perigoso - permite qualquer plugin
    if ($allowPlugins === true) {
        $allowPluginsDangerous = true;
    }
}

verificar(
    'composer.vulneravel.json tem allow-plugins = true (permite todos os plugins)',
    $allowPluginsDangerous === true
);

// Teste 2: Verificar que versão vulnerável NÃO usa allow-list explícita
$naoUsaAllowList = false;
if (isset($composerVulneravel['config']['allow-plugins'])) {
    $allowPlugins = $composerVulneravel['config']['allow-plugins'];
    // Se não é um array, não é uma allow-list
    if (!is_array($allowPlugins)) {
        $naoUsaAllowList = true;
    }
}

verificar(
    'composer.vulneravel.json não usa allow-list (configuração global perigosa)',
    $naoUsaAllowList === true
);

// Teste 3: Verificar se versão corrigida tem allow-plugins como array (allow-list)
$usaAllowList = false;
if (isset($composerCorrigido['config']['allow-plugins'])) {
    $allowPlugins = $composerCorrigido['config']['allow-plugins'];
    // Deve ser um array associativo
    if (is_array($allowPlugins)) {
        $usaAllowList = true;
    }
}

verificar(
    'composer.corrigido.json usa allow-plugins como array (allow-list explícita)',
    $usaAllowList === true
);

// Teste 4: Verificar que a allow-list corrigida contém um plugin específico
$temPluginExplicito = false;
if (isset($composerCorrigido['config']['allow-plugins'])) {
    $allowPlugins = $composerCorrigido['config']['allow-plugins'];
    if (is_array($allowPlugins)) {
        // Deve conter um ou mais plugins listados explicitamente
        if (count($allowPlugins) > 0) {
            foreach ($allowPlugins as $plugin => $enabled) {
                if ($enabled === true) {
                    $temPluginExplicito = true;
                    break;
                }
            }
        }
    }
}

verificar(
    'composer.corrigido.json lista plugins específicos (acme/plugin-oficial)',
    $temPluginExplicito === true
);

// Teste 5: Verificar que allow-list não é booleano
$allowListNaoBoleano = true;
if (isset($composerCorrigido['config']['allow-plugins'])) {
    if ($composerCorrigido['config']['allow-plugins'] === true ||
        $composerCorrigido['config']['allow-plugins'] === false) {
        $allowListNaoBoleano = false;
    }
}

verificar(
    'composer.corrigido.json não usa booleano para allow-plugins',
    $allowListNaoBoleano === true
);

// Teste 6: Verificar que há diferença clara entre vulnerável e corrigido
$temDiferenca = false;
$vulneravelAllowPlugins = $composerVulneravel['config']['allow-plugins'] ?? null;
$corrigidoAllowPlugins = $composerCorrigido['config']['allow-plugins'] ?? null;

if (is_bool($vulneravelAllowPlugins) && is_array($corrigidoAllowPlugins)) {
    $temDiferenca = true;
}

verificar(
    'Há diferença clara entre vulnerável (booleano) e corrigido (array)',
    $temDiferenca === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
