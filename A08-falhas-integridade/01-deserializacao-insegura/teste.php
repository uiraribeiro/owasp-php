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

// Gerar o payload malicioso (string serializada)
$executor = new \Vulneravel\ExecutorPerigoso();
$executor->comandoRegistrado = 'exec_malicioso_do_atacante';
$payloadSerializado = serialize($executor);

// Teste 1: Vulnerável instancia o objeto malicioso via unserialize()
global $comandoCapturadoViaWakeup;
$comandoCapturadoViaWakeup = null;

$resultado = \Vulneravel\carregarPreferenciasUsuario($payloadSerializado);
$objetoFoiInstanciado = $comandoCapturadoViaWakeup === 'exec_malicioso_do_atacante';

verificar(
    'Vulnerável: unserialize() instancia o objeto malicioso (__wakeup disparado)',
    $objetoFoiInstanciado
);

// Teste 2: Corrigido com JSON não instancia nada (mesmo payload como string JSON inválida)
$comandoCapturadoViaWakeup = null;
$resultado = \Corrigido\carregarPreferenciasUsuario($payloadSerializado);
$nenhumaMagiaAconteceu = $comandoCapturadoViaWakeup === null;

verificar(
    'Corrigido: json_decode() rejeita payload serializado (não instancia nada)',
    $nenhumaMagiaAconteceu
);

// Teste 3: Corrigido funciona corretamente com JSON legítimo
$jsonLegitimo = json_encode(['tema' => 'escuro', 'idioma' => 'pt-BR']);
$comandoCapturadoViaWakeup = null;
$dadosDesserializados = \Corrigido\carregarPreferenciasUsuario($jsonLegitimo);

verificar(
    'Corrigido: json_decode() funciona com dados legítimos',
    is_array($dadosDesserializados) &&
    $dadosDesserializados['tema'] === 'escuro' &&
    $dadosDesserializados['idioma'] === 'pt-BR'
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
