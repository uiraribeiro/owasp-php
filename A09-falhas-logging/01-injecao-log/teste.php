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

// Teste 1: Vulnerável permite injeção de quebra de linha (log forging)
$entradaAtacante = "joao\n[LOG] ADMIN_LOGIN_SUCESSO usuario=atacante";
$logVulneravel = \Vulneravel\registrarLog("usuario tentou login", $entradaAtacante);

verificar(
    'Vulnerável contém quebra de linha real seguida de linha falsa',
    str_contains($logVulneravel, "\n[LOG] ADMIN_LOGIN_SUCESSO")
);

// Teste 2: Corrigido neutraliza a injeção (nenhuma quebra de linha real ali)
$logCorrigido = \Corrigido\registrarLog("usuario tentou login", $entradaAtacante);

verificar(
    'Corrigido neutraliza a injeção (nenhuma quebra de linha real no meio)',
    !str_contains($logCorrigido, "\n[LOG] ADMIN_LOGIN_SUCESSO")
);

// Teste 3: Corrigido ainda menciona "ADMIN_LOGIN_SUCESSO" mas de forma inofensiva (escapada)
verificar(
    'Corrigido mantém o texto do input mas escapado',
    str_contains($logCorrigido, "ADMIN_LOGIN_SUCESSO")
);

// Teste 4: Vulnerável com input simples sem injeção
$logSimples = \Vulneravel\registrarLog("teste", "dados normais");
verificar(
    'Vulnerável funciona para input sem malícia',
    str_contains($logSimples, "dados normais")
);

// Teste 5: Corrigido com input simples
$logSimples2 = \Corrigido\registrarLog("teste", "dados normais");
verificar(
    'Corrigido funciona para input sem malícia',
    str_contains($logSimples2, "dados normais")
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
