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

$senhaOriginal = "teste_seguro_abc123";

// Teste 1: Vulnerável usa codificação reversível
$armazVulneravel = \Vulneravel\armazenarCredencial($senhaOriginal);
$recuperada = base64_decode($armazVulneravel);
verificar(
    'Vulnerável: senha armazenada pode ser base64_decode() para recuperar original',
    $recuperada === $senhaOriginal
);

// Teste 2: Corrigido usa hash irreversível
$hashCorrigido = \Corrigido\armazenarCredencial($senhaOriginal);
$tentativaReversao = base64_decode($hashCorrigido, true);
verificar(
    'Corrigido: hash não é base64 válido (não é reversível por decode simples)',
    $tentativaReversao === false
);

// Teste 3: Corrigido permite verificação com senha correta
$verificacao = \Corrigido\verificarCredencial($senhaOriginal, $hashCorrigido);
verificar(
    'Corrigido: password_verify() retorna true para senha correta',
    $verificacao === true
);

// Teste 4: Corrigido rejeita senha errada
$senhaErrada = "senha_errada";
$verificacaoErrada = \Corrigido\verificarCredencial($senhaErrada, $hashCorrigido);
verificar(
    'Corrigido: password_verify() retorna false para senha errada',
    $verificacaoErrada === false
);

// Teste 5: Hash bcrypt é diferente a cada chamada (salt aleatório)
$hash1 = \Corrigido\armazenarCredencial($senhaOriginal);
$hash2 = \Corrigido\armazenarCredencial($senhaOriginal);
verificar(
    'Corrigido: dois hashes da mesma senha são diferentes (salt aleatório)',
    $hash1 !== $hash2
);

// Teste 6: Ambos hashes podem verificar a mesma senha
$verificacao1 = \Corrigido\verificarCredencial($senhaOriginal, $hash1);
$verificacao2 = \Corrigido\verificarCredencial($senhaOriginal, $hash2);
verificar(
    'Corrigido: password_verify() funciona mesmo com hashes diferentes',
    $verificacao1 === true && $verificacao2 === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
