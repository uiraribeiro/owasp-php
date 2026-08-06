<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Design Seguro com Validação de Uploads
 *
 * A decisão de design correta implementa validação em múltiplas camadas:
 * 1. WHITELIST de extensões permitidas (nunca extensões parametrizáveis)
 * 2. Verificação de MAGIC BYTES (assinatura binária) do arquivo real
 * 3. Ambas as validações devem concordar (extensão + conteúdo real)
 * Dessa forma, mesmo que um atacante renomeie um .php para .jpg,
 * o magic byte não vai bater e será rejeitado.
 */

function obterMagicBytesEsperado(string $extensao): ?string {
    // Magic bytes esperados para extensões permitidas
    $mapeamento = [
        'jpg'  => "\xFF\xD8\xFF",  // JPEG
        'jpeg' => "\xFF\xD8\xFF",  // JPEG
        'png'  => "\x89PNG",       // PNG (completo é \x89PNG\r\n\x1a\n)
        'gif'  => "GIF",           // GIF87a ou GIF89a
        'pdf'  => "%PDF",          // PDF
    ];

    return $mapeamento[$extensao] ?? null;
}

function validarUpload(string $nomeArquivo, string $conteudo): bool {
    // CORRIGIDO: validação em camadas

    // 1. Extrair extensão e converter para minúsculas
    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

    // 2. Whitelist: extensão deve estar na lista permitida
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    if (!in_array($extensao, $extensoesPermitidas)) {
        return false; // Extensão não permitida
    }

    // 3. Verificar magic bytes do conteúdo real
    $magicBytesEsperado = obterMagicBytesEsperado($extensao);
    if ($magicBytesEsperado === null) {
        return false;
    }

    // Magic bytes do arquivo podem ser incompletos em nossos testes,
    // então verificamos se o conteúdo COMEÇA com o magic byte esperado
    $conteudoInicial = substr($conteudo, 0, strlen($magicBytesEsperado));
    if ($conteudoInicial !== $magicBytesEsperado) {
        return false; // Magic bytes não batem
    }

    return true; // Passou em todas as validações
}

function demo(): void {
    echo "=== CORRIGIDO: Validação de uploads em camadas ===\n";

    // Teste 1: Arquivo .php é rejeitado (extensão não permitida)
    if (!validarUpload('exploit.php', '<?php system($_GET["c"]); ?>')) {
        echo "OK: Rejeitou exploit.php (extensão não permitida)\n";
    }

    // Teste 2: Arquivo .jpg legítimo com magic bytes corretos é aceito
    if (validarUpload('foto.jpg', "\xFF\xD8\xFF\xE0\x00\x10JFIF...")) {
        echo "OK: Aceitou foto.jpg com magic bytes corretos\n";
    }

    // Teste 3: Arquivo .png legítimo é aceito
    if (validarUpload('imagem.png', "\x89PNG\r\n\x1a\n...")) {
        echo "OK: Aceitou imagem.png com magic bytes corretos\n";
    }

    // Teste 4: Arquivo .png com conteúdo PHP é rejeitado
    if (!validarUpload('disfarçado.png', '<?php echo "hacked"; ?>')) {
        echo "OK: Rejeitou disfarçado.png com magic bytes errados\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
