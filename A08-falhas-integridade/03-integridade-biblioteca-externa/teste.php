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

// Conteúdo original e seu hash
$conteudoOriginal = "codigo-fonte-da-biblioteca-v1.0";
$hashEsperado = hash('sha256', $conteudoOriginal);

// Simular MITM que adultera o conteúdo
$conteudoAdulterado = "codigo-fonte-da-biblioteca-v1.0-COM-BACKDOOR";

// Teste 1: Vulnerável "carrega" conteúdo adulterado (PROBLEMA!)
$resultado = \Vulneravel\carregarBibliotecaExterna($conteudoAdulterado, $hashEsperado);
verificar(
    'Vulnerável: "carrega" conteúdo adulterado sem verificar hash (PROBLEMA!)',
    $resultado === true
);

// Teste 2: Corrigido rejeita conteúdo adulterado
$resultado = \Corrigido\carregarBibliotecaExterna($conteudoAdulterado, $hashEsperado);
verificar(
    'Corrigido: rejeita conteúdo adulterado (hash não bate)',
    $resultado === false
);

// Teste 3: Corrigido aceita conteúdo original com hash correto
$resultado = \Corrigido\carregarBibliotecaExterna($conteudoOriginal, $hashEsperado);
verificar(
    'Corrigido: aceita conteúdo original com hash válido (caso legítimo)',
    $resultado === true
);

// Teste 4: Corrigido rejeita com hash completamente errado
$hashErrado = hash('sha256', "outro-conteudo-qualquer");
$resultado = \Corrigido\carregarBibliotecaExterna($conteudoOriginal, $hashErrado);
verificar(
    'Corrigido: rejeita conteúdo com hash esperado incorreto',
    $resultado === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
