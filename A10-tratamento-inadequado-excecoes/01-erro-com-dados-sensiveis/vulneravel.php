<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Exposição de Detalhes Técnicos em Mensagens de Erro - A10:2025 Mishandling of Exceptional Conditions
 *
 * A função captura exceções de banco de dados mas retorna a mensagem de erro BRUTA
 * sem filtragem. Mensagens de PDOException expõem nomes de tabelas, estrutura SQL,
 * versão do motor de banco — informações valiosas para um atacante mapear a aplicação.
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
        // VULNERÁVEL: retorna a mensagem de erro COMPLETA do banco
        // Expõe nomes de tabelas, estrutura SQL, mensagens técnicas
        return ['erro' => true, 'detalhe' => $e->getMessage()];
    }
}

function demo(): void {
    echo "=== VULNERÁVEL: Exposição de detalhes técnicos de erro ===\n";

    $pdo = criarBancoDeTeste();

    // Query válida
    $resultado = executarConsulta($pdo, 'SELECT * FROM produtos');
    echo "Query válida - Erro: " . ($resultado['erro'] ? 'sim' : 'não') . "\n";

    // Query inválida que expõe a estrutura interna
    $resultado = executarConsulta($pdo, 'SELECT * FROM tabela_inexistente');
    echo "Query inválida - Detalhe exposto: " . $resultado['detalhe'] . "\n";
    echo "(PROBLEMA: atacante vê que a tabela não existe, mapeia estrutura do banco)\n";
}

if (debug_backtrace() === []) {
    demo();
}
