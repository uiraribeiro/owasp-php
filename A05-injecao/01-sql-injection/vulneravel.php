<?php
declare(strict_types=1);

namespace Vulneravel;

/*
 * SQL Injection - FALHA A05:2025 Injection
 * Concatenação direta de entrada do usuário na query SQL permite
 * que um atacante altere a lógica da consulta e acesse/modifique dados.
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

    echo "=== Demonstração: SQL Injection (VULNERÁVEL) ===\n";

    // Teste legítimo
    echo "\n1. Login legítimo com senha correta:\n";
    $resultado = login($pdo, 'admin', 'senhaForte123');
    echo "   Resultado: " . ($resultado ? "Usuário autenticado: " . $resultado['usuario'] : "Falha") . "\n";

    // SQL Injection: bypass de autenticação
    echo "\n2. SQL Injection - bypass de autenticação:\n";
    echo "   Input: admin' -- \n";
    $resultado = login($pdo, "admin' -- ", 'senhaQualquer');
    echo "   Resultado: " . ($resultado ? "VULNERÁVEL! Usuário autenticado sem senha correta: " . $resultado['usuario'] : "Bloqueado") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
