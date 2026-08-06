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

// Testes ORM Injection
$pdoVulneravel = \Vulneravel\criarBancoDeTeste();
$repoVulneravel = new \Vulneravel\RepositorioUsuarios($pdoVulneravel);

$pdoCorrigido = \Corrigido\criarBancoDeTeste();
$repoCorrigido = new \Corrigido\RepositorioUsuarios($pdoCorrigido);

// Teste legítimo
$usuariosVulneravel = $repoVulneravel->buscarPorFiltroRaw("usuario = 'admin'");
$usuariosCorrigido = $repoCorrigido->buscarPorNomeUsuario('admin');

verificar(
    "Vulnerável: busca por usuario específico retorna 1 resultado",
    count($usuariosVulneravel) === 1 && $usuariosVulneravel[0]['usuario'] === 'admin'
);

verificar(
    "Corrigido: busca por usuario específico retorna 1 resultado",
    count($usuariosCorrigido) === 1 && $usuariosCorrigido[0]['usuario'] === 'admin'
);

// Teste ORM Injection: 1=1 retorna todos os usuários
$usuariosInjection = $repoVulneravel->buscarPorFiltroRaw("1=1");
verificar(
    "Vulnerável: filtro '1=1' retorna TODOS os usuários (vazamento de dados)",
    count($usuariosInjection) === 2
);

verificar(
    "Vulnerável: dados vazados incluem admin e user",
    count($usuariosInjection) === 2 &&
    in_array('admin', array_column($usuariosInjection, 'usuario')) &&
    in_array('user', array_column($usuariosInjection, 'usuario'))
);

// Teste corrigido: não há método que aceite filtros livres
// Se tentarmos buscar por um nome de usuário literal '1=1', não vai encontrar nada
$usuariosCorrigidoFiltro = $repoCorrigido->buscarPorNomeUsuario('1=1');
verificar(
    "Corrigido: busca por '1=1' retorna 0 resultados (não há usuário com esse nome)",
    count($usuariosCorrigidoFiltro) === 0
);

// Teste corrigido: busca legítima funciona
$usuariosCorrigidoUser = $repoCorrigido->buscarPorNomeUsuario('user');
verificar(
    "Corrigido: busca por 'user' funciona corretamente",
    count($usuariosCorrigidoUser) === 1 && $usuariosCorrigidoUser[0]['usuario'] === 'user'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
