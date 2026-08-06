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

// Teste 1: Vulnerável aceita arquivo .php (falha grave)
$resultadoVulneravel = \Vulneravel\validarUpload('exploit.php', '<?php system($_GET["cmd"]); ?>');
verificar(
    'Vulnerável: aceita exploit.php (falha de design)',
    $resultadoVulneravel === true
);

// Teste 2: Vulnerável aceita arquivo .exe (falha grave)
$resultadoVulneravel2 = \Vulneravel\validarUpload('malware.exe', 'MZ\x90\x00\x03...');
verificar(
    'Vulnerável: aceita malware.exe (falha de design)',
    $resultadoVulneravel2 === true
);

// Teste 3: Corrigido rejeita arquivo .php
$resultadoCorrigido = \Corrigido\validarUpload('exploit.php', '<?php system($_GET["cmd"]); ?>');
verificar(
    'Corrigido: rejeita exploit.php (extensão não permitida)',
    $resultadoCorrigido === false
);

// Teste 4: Corrigido rejeita arquivo .exe
$resultadoCorrigido2 = \Corrigido\validarUpload('malware.exe', 'MZ\x90\x00\x03...');
verificar(
    'Corrigido: rejeita malware.exe (extensão não permitida)',
    $resultadoCorrigido2 === false
);

// Teste 5: Corrigido aceita .jpg com magic bytes corretos
$conteudoJpgValido = "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00\x01...foto jpeg válida";
$resultadoJpg = \Corrigido\validarUpload('foto.jpg', $conteudoJpgValido);
verificar(
    'Corrigido: aceita foto.jpg com magic bytes corretos',
    $resultadoJpg === true
);

// Teste 6: Corrigido aceita .png com magic bytes corretos
$conteudoPngValido = "\x89PNG\r\n\x1a\n...dados PNG válidos";
$resultadoPng = \Corrigido\validarUpload('imagem.png', $conteudoPngValido);
verificar(
    'Corrigido: aceita imagem.png com magic bytes corretos',
    $resultadoPng === true
);

// Teste 7: Corrigido aceita .pdf com magic bytes corretos
$conteudoPdfValido = "%PDF-1.4\n%estrutura PDF...";
$resultadoPdf = \Corrigido\validarUpload('documento.pdf', $conteudoPdfValido);
verificar(
    'Corrigido: aceita documento.pdf com magic bytes corretos',
    $resultadoPdf === true
);

// Teste 8: Corrigido rejeita .png com conteúdo PHP (magic bytes errados)
$conteudoPhpDisfarçado = '<?php echo "hacked"; ?>';
$resultadoDisfarçado = \Corrigido\validarUpload('disfarçado.png', $conteudoPhpDisfarçado);
verificar(
    'Corrigido: rejeita disfarçado.png com magic bytes errados',
    $resultadoDisfarçado === false
);

// Teste 9: Corrigido rejeita .jpg com conteúdo PHP
$resultadoJpgFalso = \Corrigido\validarUpload('falso.jpg', '<?php phpinfo(); ?>');
verificar(
    'Corrigido: rejeita falso.jpg com conteúdo PHP (magic bytes errados)',
    $resultadoJpgFalso === false
);

// Teste 10: Corrigido rejeita .gif com conteúdo inválido
$resultadoGifFalso = \Corrigido\validarUpload('fake.gif', 'não é um GIF válido');
verificar(
    'Corrigido: rejeita fake.gif com magic bytes errados',
    $resultadoGifFalso === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
