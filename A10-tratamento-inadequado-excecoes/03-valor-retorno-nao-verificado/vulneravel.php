<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Valor de Retorno Não Verificado (CWE-252) - A10:2025 Mishandling of Exceptional Conditions
 *
 * A função chama file_put_contents() mas nunca verifica seu retorno.
 * Se a operação falhar (diretório inexistente, sem permissão), a função
 * falsamente reporta sucesso, causando falha silenciosa perigosa.
 */

function salvarConfiguracao(string $caminho, string $conteudo): array {
    // VULNERÁVEL: @ suprime warnings, mas nunca verifica o retorno
    // file_put_contents() retorna false em caso de falha
    @file_put_contents($caminho, $conteudo);
    // Sempre reporta sucesso, mesmo se a escrita falhou
    return ['status' => 'sucesso'];
}

function demo(): void {
    echo "=== VULNERÁVEL: Valor de retorno não verificado ===\n";

    // Tenta salvar em caminho inválido
    $caminhoInvalido = sys_get_temp_dir() . '/pasta-inexistente-' . uniqid() . '/config.txt';
    $resultado = salvarConfiguracao($caminhoInvalido, 'dados importantes');
    echo "Salvar em caminho inválido: " . $resultado['status'] . "\n";
    echo "(PROBLEMA: diz 'sucesso' mas o arquivo NÃO foi salvo)\n";

    // Verifica se arquivo realmente existe
    if (!file_exists($caminhoInvalido)) {
        echo "Confirmado: arquivo NÃO existe (falha silenciosa não detectada!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
