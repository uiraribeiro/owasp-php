<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * ORM Injection - CORREÇÃO
 * Não expor nenhum parâmetro de construção SQL livre.
 * Usar métodos específicos que aceitam apenas valores de campos específicos,
 * nunca condições SQL arbitrárias, garantindo prepared statements internamente.
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
     * CORRIGIDO: método específico que só aceita um nome de usuário
     * Usa prepared statement internamente, nunca expõe construção SQL
     */
    public function buscarPorNomeUsuario(string $nomeUsuario): array
    {
        // CORRIGIDO: prepared statement com parâmetro vinculado
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario');
        $stmt->execute(['usuario' => $nomeUsuario]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}

function demo(): void
{
    echo "=== Demonstração: ORM Injection (CORRIGIDO) ===\n";

    $pdo = criarBancoDeTeste();
    $repo = new RepositorioUsuarios($pdo);

    // Teste legítimo
    echo "\n1. Busca legítima por usuário específico:\n";
    $usuarios = $repo->buscarPorNomeUsuario('admin');
    echo "   Resultado: " . count($usuarios) . " usuário(s) encontrado(s)\n";

    // Tentativa de injection é bloqueada (não há método que aceite filtros livres)
    echo "\n2. Injeção bloqueada - método específico não expõe SQL:\n";
    echo "   Não existe método buscarPorFiltroRaw(), precisa usar buscarPorNomeUsuario()\n";
    $usuarios = $repo->buscarPorNomeUsuario('1=1');
    echo "   Resultado: " . count($usuarios) . " usuário(s) encontrado(s)\n";
    if (count($usuarios) === 0) {
        echo "   ✓ Nenhum usuário com nome '1=1', injeção foi neutralizada\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
