<?php
declare(strict_types=1);

/*
 * Endpoint HTTP fino sobre corrigido.php, simulando o mesmo formulário
 * de login via POST, agora usando prepared statements. Não faz parte
 * da suite rápida de testes (teste.php).
 *
 * Uso: php -S 127.0.0.1:8901, depois POST /endpoint-corrigido.php
 * com corpo "usuario=admin&senha=x"
 */

require __DIR__ . '/corrigido.php';

$pdo = Corrigido\criarBancoDeTeste();

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

$resultado = Corrigido\login($pdo, $usuario, $senha);

header('Content-Type: text/plain; charset=utf-8');

if ($resultado !== null) {
    echo "LOGIN_OK usuario=" . $resultado['usuario'];
} else {
    echo "LOGIN_FALHOU";
}
