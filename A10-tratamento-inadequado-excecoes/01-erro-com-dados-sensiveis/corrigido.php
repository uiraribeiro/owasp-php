<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Filtragem de Detalhes Técnicos em Mensagens de Erro
 *
 * Captura exceções e retorna uma mensagem genérica, segura,
 * nunca revelando detalhes internos (nomes de tabela, SQL, stack trace).
 * O detalhe técnico pode ser logado internamente para debug/auditoria.
 */

function criarBancoDeTeste(): \PDO {
    $pdo = new \PDO('sqlite::memory:');
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->exec('CREATE TABLE produtos (id INTEGER PRIMARY KEY, nome TEXT)');
    $pdo->exec("INSERT INTO produtos (nome) VALUES ('Produto A')");
    $pdo->exec("INSERT INTO produtos (nome) VALUES ('Produto B')");
    return $pdo;
}

function executarConsulta(\PDO $pdo, string $sql): array {
    try {
        $pdo->query($sql);
        return ['erro' => false, 'detalhe' => 'ok'];
    } catch (\PDOException $e) {
        // CORRIGIDO: retorna apenas uma mensagem genérica, segura
        // (O detalhe técnico seria logado internamente, nunca exposto ao cliente)
        return ['erro' => true, 'detalhe' => 'Nao foi possivel processar a consulta.'];
    }
}

function demo(): void {
    echo "=== CORRIGIDO: Mensagens de erro genéricas, sem exposição técnica ===\n";

    $pdo = criarBancoDeTeste();

    // Query válida
    $resultado = executarConsulta($pdo, 'SELECT * FROM produtos');
    echo "Query válida - Erro: " . ($resultado['erro'] ? 'sim' : 'não') . "\n";

    // Query inválida, mas mensagem genérica
    $resultado = executarConsulta($pdo, 'SELECT * FROM tabela_inexistente');
    echo "Query inválida - Detalhe retornado: " . $resultado['detalhe'] . "\n";
    echo "(OK: atacante não vê detalhes técnicos da estrutura)\n";
}

if (debug_backtrace() === []) {
    demo();
}
