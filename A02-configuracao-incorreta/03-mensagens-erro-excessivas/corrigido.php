<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Mensagens de Erro Genéricas em Produção
 *
 * Em produção, mensagens são genéricas sem detalhes técnicos.
 * Logs detalhados são gravados no servidor para diagnóstico interno apenas.
 */

function tratarErro(\Throwable $erro, string $ambiente): string {
    if ($ambiente === 'producao') {
        // CORRIGIDO: retorna mensagem genérica que não expõe informações sensíveis
        return 'Ocorreu um erro interno. Tente novamente mais tarde.';
    } else {
        // Em desenvolvimento, mantém o stack trace completo para debugging
        return $erro->getMessage() . "\n" . $erro->getTraceAsString();
    }
}

function demo(): void {
    echo "=== CORRIGIDO: Mensagens de erro seguras em produção ===\n";

    try {
        throw new \RuntimeException('Falha ao conectar em /var/www/config/database.php com senha=SegredoDB123');
    } catch (\Throwable $e) {
        // Em produção
        $mensagemProd = tratarErro($e, 'producao');
        echo "Produção:\n{$mensagemProd}\n";
        if (!str_contains($mensagemProd, 'SegredoDB123')) {
            echo "Senha não exposta (protegido!)\n";
        }

        // Em desenvolvimento
        $mensagemDev = tratarErro($e, 'desenvolvimento');
        echo "\nDesenvolvimento:\n{$mensagemDev}\n";
        if (str_contains($mensagemDev, 'SegredoDB123')) {
            echo "Detalhes completos disponíveis para debugging\n";
        }
    }
}

if (debug_backtrace() === []) {
    demo();
}
