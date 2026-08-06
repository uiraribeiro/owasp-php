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

// Teste legítimo
$cmdVulneravel = \Vulneravel\montarComandoConversaoImagem("imagem.jpg");
$cmdCorrigido = \Corrigido\montarComandoConversaoImagem("imagem.jpg");

verificar(
    "Vulnerável: comando legítimo é montado corretamente",
    str_contains($cmdVulneravel, "imagem.jpg")
);

verificar(
    "Corrigido: comando legítimo é montado com escapamento",
    str_contains($cmdCorrigido, "imagem.jpg")
);

// Teste Command Injection
$injectionPayload = "foto.jpg; echo INJETADO";
$cmdInjectionVulneravel = \Vulneravel\montarComandoConversaoImagem($injectionPayload);
$cmdInjectionCorrigido = \Corrigido\montarComandoConversaoImagem($injectionPayload);

verificar(
    "Vulnerável: injection payload contém ; desescapado (permite encandeamento)",
    str_contains($cmdInjectionVulneravel, "; echo INJETADO")
);

verificar(
    "Corrigido: payload está envolvido em aspas simples (metacaracteres neutralizados)",
    str_contains($cmdInjectionCorrigido, "'foto.jpg; echo INJETADO'")
);

verificar(
    "Corrigido: comando possui 'convert' + argumento citado + 'saida.png'",
    preg_match("/convert\\s+'[^']*'\\s+saida\\.png/", $cmdInjectionCorrigido) === 1
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
