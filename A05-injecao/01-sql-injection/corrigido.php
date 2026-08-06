<?php
declare(strict_types=1);

namespace Corrigido;

/*
 * SQL Injection - CORREÇÃO
 * Usar prepared statements com parâmetros vinculados (:nome ou ?)
 * garante que a entrada do usuário seja sempre tratada como valor literal,
 * nunca como código SQL, mesmo contendo caracteres especiais.
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
    // CORRIGIDO: prepared statement com parâmetros vinculados
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE usuario = :usuario AND senha_hash = :senha_hash');
    $stmt->execute(['usuario' => $usuario, 'senha_hash' => $senhaHash]);
    $linha = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $linha ?: null;
}

function demo(): void
{
    $pdo = criarBancoDeTeste();

    echo "=== Demonstração: SQL Injection (CORRIGIDO) ===\n";

    // Teste legítimo
    echo "\n1. Login legítimo com senha correta:\n";
    $resultado = login($pdo, 'admin', 'senhaForte123');
    echo "   Resultado: " . ($resultado ? "Usuário autenticado: " . $resultado['usuario'] : "Falha") . "\n";

    // SQL Injection: tentativa bloqueada
    echo "\n2. SQL Injection - tentativa bloqueada:\n";
    echo "   Input: admin' -- \n";
    $resultado = login($pdo, "admin' -- ", 'senhaQualquer');
    echo "   Resultado: " . ($resultado ? "VULNERÁVEL! Bypass conseguiu." : "Bloqueado corretamente") . "\n";
}

if (debug_backtrace() === []) {
    demo();
}
