<?php
declare(strict_types=1);

/**
 * A03:2025 - Software Supply Chain Failures
 * 02: Componente Não Mantido
 *
 * Este teste verifica se as dependências incluem pacotes que foram
 * abandonados pelos seus mantenedores, não recebem mais atualizações
 * de segurança e representam risco para a aplicação.
 *
 * Na vida real, você consultaria:
 * - Packagist (status "abandoned" ou "replacement")
 * - GitHub (repositório archived)
 * - NPM/Composer package registry
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
 * Base de dados fictícia de pacotes abandonados.
 * Em um cenário real, seria consultado do Packagist ou registros de pacotes.
 *
 * Estes são pacotes que não recebem mais manutenção e devem ser substituídos.
 */
$PACOTES_ABANDONADOS_CONHECIDOS = [
    'acme/log-legado',        // Descontinuado em 2023, sem patches de segurança
    'acme/cache-antigo',      // Repositório archived no GitHub
    'acme/mailer-descontinuado', // Substituído por acme/mailer-moderno
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

// Teste 1: Verificar se versão vulnerável contém pacote abandonado
$temPacoteAbandonado = false;
foreach ($composerVulneravel['require'] as $pacote => $versao) {
    if (in_array($pacote, $PACOTES_ABANDONADOS_CONHECIDOS, true)) {
        $temPacoteAbandonado = true;
        break;
    }
}

verificar(
    'composer.vulneravel.json contém acme/log-legado (pacote abandonado)',
    $temPacoteAbandonado === true
);

// Teste 2: Verificar se versão corrigida NÃO contém nenhum pacote abandonado
$naoTemPacoteAbandonado = true;
foreach ($composerCorrigido['require'] as $pacote => $versao) {
    if (in_array($pacote, $PACOTES_ABANDONADOS_CONHECIDOS, true)) {
        $naoTemPacoteAbandonado = false;
        break;
    }
}

verificar(
    'composer.corrigido.json não contém nenhum pacote da lista de abandonados',
    $naoTemPacoteAbandonado === true
);

// Teste 3: Verificar que composer.corrigido usa uma alternativa mantida
$usaAlternativaModerna = false;
if (isset($composerCorrigido['require']['acme/log-moderno'])) {
    $usaAlternativaModerna = true;
}

verificar(
    'composer.corrigido.json usa acme/log-moderno (alternativa ativamente mantida)',
    $usaAlternativaModerna === true
);

// Teste 4: Verificar que a versão corrigida usa uma constraint válida
$versionCorrigidaValida = false;
if (isset($composerCorrigido['require']['acme/log-moderno'])) {
    $versao = $composerCorrigido['require']['acme/log-moderno'];
    // Uma constraint semântica válida deve começar com ^, ~, ou ser uma versão semântica
    if (preg_match('/^(\^|~|\d)/', $versao)) {
        $versionCorrigidaValida = true;
    }
}

verificar(
    'Versão corrigida usa constraint semântica válida (^1.0)',
    $versionCorrigidaValida === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
