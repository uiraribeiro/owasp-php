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
$resultadoVulneravel = \Vulneravel\avaliarTemplate("Ola {{\$nome}}", ['nome' => 'Maria']);
$resultadoCorrigido = \Corrigido\avaliarTemplate("Ola {{nome}}", ['nome' => 'Maria']);

verificar(
    "Vulnerável: template legítimo é processado corretamente",
    str_contains($resultadoVulneravel, "Maria")
);

verificar(
    "Corrigido: template legítimo é processado corretamente",
    str_contains($resultadoCorrigido, "Maria")
);

// Teste Code Injection com system()
$templateMalicioso = 'Resultado: {{ system("echo INJETADO") }}';

// Capturar output da versão vulnerável
ob_start();
try {
    $resultadoInjectionVulneravel = \Vulneravel\avaliarTemplate($templateMalicioso, []);
    $outputVulneravel = ob_get_clean();
} catch (\Throwable $e) {
    $outputVulneravel = '';
    $resultadoInjectionVulneravel = '';
    ob_end_clean();
}

// Capturar output da versão corrigida
ob_start();
$resultadoInjectionCorrigido = \Corrigido\avaliarTemplate($templateMalicioso, []);
$outputCorrigido = ob_get_clean();

verificar(
    "Vulnerável: code injection permite execução (output capturado contém INJETADO)",
    str_contains($outputVulneravel, "INJETADO") || str_contains($resultadoInjectionVulneravel, "INJETADO")
);

verificar(
    "Corrigido: code injection é bloqueado (template não foi processado/executado)",
    str_contains($resultadoInjectionCorrigido, '{{ system("echo INJETADO") }}')
);

// Teste com padrão simples que deveria funcionar (variável legítima)
$templateSimples = "Valor: {{valor}}";
$resultadoSimples = \Corrigido\avaliarTemplate($templateSimples, ['valor' => 'correto']);

verificar(
    "Corrigido: variável simples ainda funciona",
    str_contains($resultadoSimples, "correto")
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
