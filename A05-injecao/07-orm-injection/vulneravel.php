<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * ORM Injection / SQL Injection via ORM - FALHA A05:2025 Injection
 * Aceitar condições SQL arbitrárias mesmo quando usando uma camada ORM
 * permite injeção SQL se o desenvolvedor expuser a construção de queries.
 */

function criarBancoDeTeste(): \PDO
{
    $pdo = new \PDO('sqlite::memory:');
    $pdo->exec("CREATE TABLE usuarios (id INTEGER PRIMARY KEY, usuario TEXT, senha_hash TEXT)");
    $pdo->exec("INSERT INTO usuarios (usuario, senha_hash) VALUES ('admin', '" . md5('senhaForte123') . "')");
    $pdo->exec("INSERT INTO usuarios (usuario, senha_hash) VALUES ('user', '" . md5('senha456') . "')");
    return $pdo;
}

class RepositorioUsuarios
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * VULNERÁVEL: aceita uma condição SQL arbitrária
     */
    public function buscarPorFiltroRaw(string $condicaoSql): array
    {
        // VULNERÁVEL: concatenação direta da condição no WHERE
        $query = "SELECT * FROM usuarios WHERE {$condicaoSql}";
        $resultado = $this->pdo->query($query);

        if ($resultado === false) {
            return [];
        }

        return $resultado->fetchAll(\PDO::FETCH_ASSOC);
    }
}

function demo(): void
{
    echo "=== Demonstração: ORM Injection (VULNERÁVEL) ===\n";

    $pdo = criarBancoDeTeste();
    $repo = new RepositorioUsuarios($pdo);

    // Teste legítimo
    echo "\n1. Busca legítima por usuário específico:\n";
    $usuarios = $repo->buscarPorFiltroRaw("usuario = 'admin'");
    echo "   Resultado: " . count($usuarios) . " usuário(s) encontrado(s)\n";

    // SQL Injection: retornar todos os usuários
    echo "\n2. SQL Injection - retornar todos os usuários:\n";
    echo "   Filtro: 1=1\n";
    $usuarios = $repo->buscarPorFiltroRaw("1=1");
    echo "   Resultado: " . count($usuarios) . " usuário(s) encontrado(s)\n";
    if (count($usuarios) > 1) {
        echo "   ⚠️  VULNERÁVEL! Vazamento de dados: " . implode(', ', array_column($usuarios, 'usuario')) . "\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
