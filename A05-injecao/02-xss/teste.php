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
$htmlVulneravel = \Vulneravel\renderizarComentario("Muito bom!");
$htmlCorrigido = \Corrigido\renderizarComentario("Muito bom!");

verificar(
    "Vulnerável: comentário legítimo é renderizado",
    str_contains($htmlVulneravel, "Muito bom!")
);

verificar(
    "Corrigido: comentário legítimo é renderizado",
    str_contains($htmlCorrigido, "Muito bom!")
);

// Teste XSS
$xssPayload = '<script>alert(1)</script>';
$htmlXSSVulneravel = \Vulneravel\renderizarComentario($xssPayload);
$htmlXSSCorrigido = \Corrigido\renderizarComentario($xssPayload);

verificar(
    "Vulnerável: XSS payload contém <script> literal (injetável)",
    str_contains($htmlXSSVulneravel, '<script>')
);

verificar(
    "Corrigido: XSS payload NÃO contém <script> literal",
    !str_contains($htmlXSSCorrigido, '<script>')
);

verificar(
    "Corrigido: XSS payload está escapado como &lt;script&gt;",
    str_contains($htmlXSSCorrigido, '&lt;script&gt;')
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
