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

// Preparar banco de testes
$pdoVulneravel = \Vulneravel\criarBancoDeTeste();
$pdoCorrigido = \Corrigido\criarBancoDeTeste();

// Teste 1: Query válida em ambas deve retornar sucesso
$r1 = \Vulneravel\executarConsulta($pdoVulneravel, 'SELECT * FROM produtos');
$r2 = \Corrigido\executarConsulta($pdoCorrigido, 'SELECT * FROM produtos');
verificar(
    'Query válida retorna sucesso em ambas',
    $r1['erro'] === false && $r2['erro'] === false
);

// Teste 2: Query inválida - Vulnerável expõe detalhes técnicos
$r1 = \Vulneravel\executarConsulta($pdoVulneravel, 'SELECT * FROM tabela_inexistente');
$temVazamentoTecnico = str_contains(strtolower($r1['detalhe']), 'no such table') ||
                        str_contains(strtolower($r1['detalhe']), 'table') ||
                        str_contains(strtolower($r1['detalhe']), 'sqlstate');
verificar(
    'Vulnerável expõe detalhes técnicos (vazamento de estrutura do banco)',
    $r1['erro'] === true && $temVazamentoTecnico
);

// Teste 3: Query inválida - Corrigido retorna mensagem genérica
$r2 = \Corrigido\executarConsulta($pdoCorrigido, 'SELECT * FROM tabela_inexistente');
$mensagemGenericaOk = $r2['erro'] === true && $r2['detalhe'] === 'Nao foi possivel processar a consulta.';
verificar(
    'Corrigido retorna mensagem genérica (sem exposição técnica)',
    $mensagemGenericaOk
);

// Teste 4: Corrigido não expõe "no such table" ou "sqlstate"
$naoExpoeTecnico = !str_contains(strtolower($r2['detalhe']), 'no such table') &&
                   !str_contains(strtolower($r2['detalhe']), 'sqlstate');
verificar(
    'Corrigido não contém referências técnicas como "no such table" ou "sqlstate"',
    $naoExpoeTecnico
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
