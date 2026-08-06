<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * SQL Injection em formulário de login via POST - FALHA A05:2025 Injection
 * Mesma causa raiz de 01-sql-injection (concatenação direta na query),
 * mas aqui os dados chegam como um formulário HTML tradicional (POST
 * usuario/senha) — o cenário mais comum de tela de login em produção.
 */

function criarBancoDeTeste(): \PDO
{
    $pdo = new \PDO('sqlite::memory:');
    $pdo->exec("CREATE TABLE usuarios (id INTEGER PRIMARY KEY, usuario TEXT, senha_hash TEXT)");
    $pdo->exec("INSERT INTO usuarios (usuario, senha_hash) VALUES ('admin', '" . md5('senhaForte123') . "')");
    return $pdo;
}

function login(\PDO $pdo, string $usuario, string $senha): ?array
{
    $senhaHash = md5($senha);
    // VULNERÁVEL: concatenação direta de entrada do usuário na query
    $query = "SELECT * FROM usuarios WHERE usuario = '{$usuario}' AND senha_hash = '{$senhaHash}'";
    $resultado = $pdo->query($query);

    if ($resultado === false) {
        return null;
    }

    $linha = $resultado->fetch(\PDO::FETCH_ASSOC);
    return $linha ?: null;
}

function demo(): void
{
    $pdo = criarBancoDeTeste();

    echo "=== Demonstração: SQL Injection via formulário POST (VULNERÁVEL) ===\n";

    echo "\n1. Login legítimo com senha correta:\n";
    $resultado = login($pdo, 'admin', 'senhaForte123');
    echo "   Resultado: " . ($resultado ? "Usuário autenticado: " . $resultado['usuario'] : "Falha") . "\n";

    echo "\n2. SQL Injection no campo 'usuario' do formulário - bypass de login:\n";
    echo "   Input: admin' -- \n";
    $resultado = login($pdo, "admin' -- ", 'senhaQualquer');
    echo "   Resultado: " . ($resultado ? "VULNERÁVEL! Usuário autenticado sem senha correta: " . $resultado['usuario'] : "Bloqueado") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
