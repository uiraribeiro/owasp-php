<?php
declare(strict_types=1);

/*
 * Endpoint HTTP fino sobre corrigido.php, só para poder apontar o sqlmap
 * contra um alvo real (sqlmap testa parâmetros HTTP, não chama função PHP
 * diretamente). Não faz parte da suite rápida de testes (teste.php).
 *
 * Uso: php -S 127.0.0.1:8899, depois GET /endpoint-corrigido.php?usuario=X&senha=Y
 */

require __DIR__ . '/corrigido.php';

$pdo = Corrigido\criarBancoDeTeste();

$usuario = $_GET['usuario'] ?? '';
$senha = $_GET['senha'] ?? '';

$resultado = Corrigido\login($pdo, $usuario, $senha);

header('Content-Type: text/plain; charset=utf-8');

if ($resultado !== null) {
    echo "LOGIN_OK usuario=" . $resultado['usuario'];
} else {
    echo "LOGIN_FALHOU";
}
