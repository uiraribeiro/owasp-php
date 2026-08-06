<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Mensagens de Erro com Informações Sensíveis - A02:2025 Security Misconfiguration
 *
 * Stack traces completos e mensagens de erro detalhadas são expostas a usuários.
 * Atacante pode descobrir caminhos do servidor, banco de dados, senhas hardcoded no erro.
 */

function tratarErro(\Throwable $erro, string $ambiente): string {
    // VULNERÁVEL: sempre expõe a mensagem completa e stack trace, independente do ambiente
    return $erro->getMessage() . "\n" . $erro->getTraceAsString();
}

function demo(): void {
    echo "=== VULNERÁVEL: Stack trace completo exposto ===\n";

    try {
        throw new \RuntimeException('Falha ao conectar em /var/www/config/database.php com senha=SegredoDB123');
    } catch (\Throwable $e) {
        $mensagem = tratarErro($e, 'producao');
        echo $mensagem . "\n";
        if (str_contains($mensagem, 'SegredoDB123')) {
            echo "\nSenha exposta no erro (PROBLEMA!)\n";
        }
    }
}

if (debug_backtrace() === []) {
    demo();
}
