<?php
declare(strict_types=1);

/*
 * Endpoint HTTP fino sobre vulneravel.php, simulando um formulário de
 * login tradicional (POST usuario/senha) para poder apontar o sqlmap
 * contra um alvo real via --data. Não faz parte da suite rápida de
 * testes (teste.php).
 *
 * Uso: php -S 127.0.0.1:8901, depois POST /endpoint-vulneravel.php
 * com corpo "usuario=admin&senha=x"
 */

require __DIR__ . '/vulneravel.php';

$pdo = Vulneravel\criarBancoDeTeste();

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

$resultado = Vulneravel\login($pdo, $usuario, $senha);

header('Content-Type: text/plain; charset=utf-8');

if ($resultado !== null) {
    echo "LOGIN_OK usuario=" . $resultado['usuario'];
} else {
    echo "LOGIN_FALHOU";
}
