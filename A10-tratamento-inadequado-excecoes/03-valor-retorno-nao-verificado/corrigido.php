<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Verificação de Valor de Retorno
 *
 * Antes de reportar sucesso, verifica o valor retornado por file_put_contents().
 * Se retornar false, reporta erro apropriadamente.
 */

function salvarConfiguracao(string $caminho, string $conteudo): array {
    // CORRIGIDO: verifica o valor de retorno
    $resultado = @file_put_contents($caminho, $conteudo);

    if ($resultado === false) {
        // Detecta e reporta a falha
        return ['status' => 'erro', 'mensagem' => 'Falha ao salvar arquivo de configuracao'];
    }

    return ['status' => 'sucesso'];
}

function demo(): void {
    echo "=== CORRIGIDO: Valor de retorno verificado ===\n";

    // Tenta salvar em caminho inválido
    $caminhoInvalido = sys_get_temp_dir() . '/pasta-inexistente-' . uniqid() . '/config.txt';
    $resultado = salvarConfiguracao($caminhoInvalido, 'dados importantes');
    echo "Salvar em caminho inválido: " . $resultado['status'] . "\n";
    echo "(OK: detecta e reporta erro apropriadamente)\n";

    // Tenta salvar em caminho válido
    $caminhoValido = sys_get_temp_dir() . '/teste_a10_' . uniqid() . '.txt';
    $resultado = salvarConfiguracao($caminhoValido, 'dados importantes');
    echo "\nSalvar em caminho válido: " . $resultado['status'] . "\n";

    if (file_exists($caminhoValido)) {
        echo "(OK: arquivo foi criado com sucesso)\n";
        unlink($caminhoValido); // Limpeza
    }
}

if (debug_backtrace() === []) {
    demo();
}
