<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

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

// Criar uma exceção com informações sensíveis
$erroComSenha = new \RuntimeException('Falha ao conectar em /var/www/config/database.php com senha=SegredoDB123');

// Teste 1: Vulnerável expõe senha em produção
$mensagemVulneravel = \Vulneravel\tratarErro($erroComSenha, 'producao');
verificar(
    'Vulnerável expõe senha no erro (SegredoDB123 visível em produção)',
    str_contains($mensagemVulneravel, 'SegredoDB123')
);

// Teste 2: Vulnerável expõe caminho do arquivo
verificar(
    'Vulnerável expõe caminho do servidor no erro',
    str_contains($mensagemVulneravel, '/var/www/config')
);

// Teste 3: Vulnerável expõe stack trace
verificar(
    'Vulnerável expõe stack trace completo',
    str_contains($mensagemVulneravel, 'Stack trace') ||
    str_contains($mensagemVulneravel, '#0') ||
    strlen($mensagemVulneravel) > 200
);

// Teste 4: Corrigido não expõe senha em produção
$mensagemCorrigidaProd = \Corrigido\tratarErro($erroComSenha, 'producao');
verificar(
    'Corrigido não expõe senha em produção',
    !str_contains($mensagemCorrigidaProd, 'SegredoDB123')
);

// Teste 5: Corrigido não expõe caminho do arquivo em produção
verificar(
    'Corrigido não expõe caminho do servidor em produção',
    !str_contains($mensagemCorrigidaProd, '/var/www/config')
);

// Teste 6: Corrigido retorna mensagem genérica em produção
verificar(
    'Corrigido retorna mensagem genérica em produção',
    str_contains($mensagemCorrigidaProd, 'erro interno') ||
    str_contains($mensagemCorrigidaProd, 'Tente novamente')
);

// Teste 7: Corrigido expõe detalhes em desenvolvimento (útil)
$mensagemCorrigidaDev = \Corrigido\tratarErro($erroComSenha, 'desenvolvimento');
verificar(
    'Corrigido expõe detalhes em desenvolvimento',
    str_contains($mensagemCorrigidaDev, 'SegredoDB123') &&
    str_contains($mensagemCorrigidaDev, '/var/www/config')
);

// Teste 8: Corrigido expõe stack trace em desenvolvimento
verificar(
    'Corrigido expõe stack trace em desenvolvimento',
    str_contains($mensagemCorrigidaDev, 'Stack trace') ||
    str_contains($mensagemCorrigidaDev, '#0') ||
    strlen($mensagemCorrigidaDev) > 200
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
