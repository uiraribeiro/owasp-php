<?php
declare(strict_types=1);

/**
 * A03:2025 - Software Supply Chain Failures
 * 03: Fonte Não Confiável
 *
 * Este teste verifica se as dependências são baixadas apenas de fontes
 * oficiais e seguras (HTTPS). Usar HTTP ou repositórios privados/não
 * verificados permite que um atacante faça man-in-the-middle attacks
 * e injete código malicioso nas dependências.
 *
 * Melhorias práticas:
 * - Usar apenas Packagist.org (repositório oficial) com HTTPS
 * - Se precisar de repositórios privados, usar Git ou Gitlab com SSH
 * - Verificar certificados SSL
 * - Usar composer.lock para reproducibilidade
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

// Teste 1: Verificar se versão vulnerável tem repositories customizadas
$temRepositorioNaoSeguro = false;
if (isset($composerVulneravel['repositories'])) {
    foreach ($composerVulneravel['repositories'] as $repo) {
        // Verificar se é um repository com URL HTTP (não HTTPS)
        if (is_array($repo) && isset($repo['url'])) {
            if (str_starts_with($repo['url'], 'http://')) {
                $temRepositorioNaoSeguro = true;
                break;
            }
        }
    }
}

verificar(
    'composer.vulneravel.json contém URL HTTP (não segura) em repositories',
    $temRepositorioNaoSeguro === true
);

// Teste 2: Verificar se versão vulnerável desabilita packagist.org
$desabilitaPackagist = false;
if (isset($composerVulneravel['repositories'])) {
    foreach ($composerVulneravel['repositories'] as $repo) {
        if (is_array($repo) && isset($repo['packagist.org'])) {
            if ($repo['packagist.org'] === false) {
                $desabilitaPackagist = true;
                break;
            }
        }
    }
}

verificar(
    'composer.vulneravel.json desabilita packagist.org (forçando repositórios customizados)',
    $desabilitaPackagist === true
);

// Teste 3: Verificar se versão corrigida NÃO tem repositories customizadas
$naoTemRepositorioCustomizado = true;
if (isset($composerCorrigido['repositories'])) {
    // Se existe, checar que não é uma lista vazia ou contém apenas URLs HTTPS seguras
    foreach ($composerCorrigido['repositories'] as $repo) {
        if (is_array($repo) && isset($repo['url'])) {
            if (str_starts_with($repo['url'], 'http://')) {
                $naoTemRepositorioCustomizado = false;
                break;
            }
        }
        if (is_array($repo) && isset($repo['packagist.org']) && $repo['packagist.org'] === false) {
            $naoTemRepositorioCustomizado = false;
            break;
        }
    }
}

verificar(
    'composer.corrigido.json não contém repositories customizadas perigosas',
    $naoTemRepositorioCustomizado === true
);

// Teste 4: Verificar que repositórios corrigidos (se existem) usam HTTPS ou Git+SSH
$repositoriosCorrigidosSeguros = true;
if (isset($composerCorrigido['repositories'])) {
    foreach ($composerCorrigido['repositories'] as $repo) {
        if (is_array($repo) && isset($repo['url'])) {
            $url = $repo['url'];
            // URLs seguras: HTTPS, git://, SSH
            if (!str_starts_with($url, 'https://') &&
                !str_starts_with($url, 'git@') &&
                !str_starts_with($url, 'git://')) {
                $repositoriosCorrigidosSeguros = false;
                break;
            }
        }
    }
}

verificar(
    'Se composer.corrigido possui repositories, usa apenas HTTPS/Git+SSH',
    $repositoriosCorrigidosSeguros === true
);

// Teste 5: Verificar que repositório vulnerável não usa Packagist oficial
$foraPackagistOficial = false;
if (isset($composerVulneravel['repositories'])) {
    foreach ($composerVulneravel['repositories'] as $repo) {
        if (is_array($repo) && isset($repo['url'])) {
            // Qualquer URL que não seja a do Packagist oficial é considerada "fora"
            if (!str_contains($repo['url'], 'packagist.org')) {
                $foraPackagistOficial = true;
            }
        }
    }
}

verificar(
    'composer.vulneravel.json usa fonte customizada, não Packagist oficial',
    $foraPackagistOficial === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
