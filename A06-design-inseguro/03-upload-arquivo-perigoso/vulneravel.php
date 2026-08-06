<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Upload de Arquivo Perigoso (CWE-434) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é a falta de validação de uploads. A arquitetura
 * não define NENHUM critério de aceitação: nem whitelist de extensões,
 * nem verificação de magic bytes (assinatura binária) do arquivo.
 * Isso permite uploads de .php, .exe, ou qualquer outro tipo perigoso.
 * Atacante envia exploit.php e o servidor aceita sem questionar.
 */

function validarUpload(string $nomeArquivo, string $conteudo): bool {
    // VULNERÁVEL: praticamente nenhuma validação
    // Aceita qualquer arquivo só porque o nome não está vazio
    // Não verifica extensão, não verifica conteúdo real (magic bytes)
    return $nomeArquivo !== '';
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem validação de uploads ===\n";

    $testesInseguros = [
        ['exploit.php', '<?php system($_GET["c"]); ?>'],
        ['malware.exe', 'MZ\x90\x00...'],
        ['shell.phtml', '<?php echo "hacked"; ?>'],
    ];

    foreach ($testesInseguros as [$nome, $conteudo]) {
        if (validarUpload($nome, $conteudo)) {
            echo "PROBLEMA: Aceitou {$nome} sem verificação (arquivo perigoso!)\n";
        }
    }
}

if (debug_backtrace() === []) {
    demo();
}
