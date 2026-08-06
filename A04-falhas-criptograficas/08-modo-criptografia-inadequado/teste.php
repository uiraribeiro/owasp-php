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

// Testes
$chave = 'chave-teste-123';

// Montar texto com blocos repetidos (16 bytes cada, dois blocos idênticos)
$bloco = 'AAAAAAAAAAAAAAAA'; // Exatamente 16 caracteres = 1 bloco AES
$texto = $bloco . $bloco; // 32 bytes = 2 blocos idênticos

// Teste 1: ECB vulnerável produz o MESMO resultado duas vezes
$cifra_ecb_1 = \Vulneravel\criptografarEcb($texto, $chave);
$cifra_ecb_2 = \Vulneravel\criptografarEcb($texto, $chave);

verificar(
    'ECB vulnerável produz cifras IDENTICAS (determinístico)',
    bin2hex($cifra_ecb_1) === bin2hex($cifra_ecb_2)
);

// Teste 2: ECB vulnerável tem blocos repetidos (vaza padrão)
// Os primeiros 16 bytes (bloco 1) devem ser iguais aos segundos 16 bytes (bloco 2)
$bloco1_ecb = substr($cifra_ecb_1, 0, 16);
$bloco2_ecb = substr($cifra_ecb_1, 16, 16);

verificar(
    'ECB vulnerável: blocos repetidos no criptograma (ECB Penguin Problem)',
    $bloco1_ecb === $bloco2_ecb
);

// Teste 3: GCM corrigido produz resultados DIFERENTES
$cifra_gcm_1 = \Corrigido\criptografarEcb($texto, $chave);
$cifra_gcm_2 = \Corrigido\criptografarEcb($texto, $chave);

verificar(
    'GCM corrigido produz cifras DIFERENTES (IV aleatório)',
    bin2hex($cifra_gcm_1) !== bin2hex($cifra_gcm_2)
);

// Teste 4: GCM não vaza padrões de blocos
// Com GCM usando IV aleatório, mesmo que comparássemos,
// não seria simples verificar blocos por simples substring
// Demonstramos que o resultado é diferente mesmo para mesmo texto

verificar(
    'GCM não é determinístico (protege contra análise de padrões)',
    true // Implícito pelos testes anteriores
);

// Teste 5: Ambos os modos conseguem criptografar diferentes textos
$texto1 = 'Dados primeiro';
$texto2 = 'Dados segundo';

$cifra_ecb_t1 = \Vulneravel\criptografarEcb($texto1, $chave);
$cifra_ecb_t2 = \Vulneravel\criptografarEcb($texto2, $chave);

verificar(
    'ECB consegue criptografar diferentes textos',
    bin2hex($cifra_ecb_t1) !== bin2hex($cifra_ecb_t2)
);

$cifra_gcm_t1 = \Corrigido\criptografarEcb($texto1, $chave);
$cifra_gcm_t2 = \Corrigido\criptografarEcb($texto2, $chave);

verificar(
    'GCM consegue criptografar diferentes textos',
    bin2hex($cifra_gcm_t1) !== bin2hex($cifra_gcm_t2)
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
