<?php
declare(strict_types=1);

/*
 * Endpoint HTTP fino sobre corrigido.php, só para poder apontar o sqlmap
 * contra um alvo real. O RepositorioUsuarios corrigido nem expõe um método
 * de filtro SQL livre — só aceita um nome de usuário como valor.
 *
 * Uso: php -S 127.0.0.1:8899, depois GET /endpoint-corrigido.php?nome=admin
 */

require __DIR__ . '/corrigido.php';

$pdo = Corrigido\criarBancoDeTeste();
$repositorio = new Corrigido\RepositorioUsuarios($pdo);

$nome = $_GET['nome'] ?? '';
$usuarios = $repositorio->buscarPorNomeUsuario($nome);

header('Content-Type: text/plain; charset=utf-8');

echo "TOTAL_USUARIOS=" . count($usuarios);
if (count($usuarios) > 0) {
    echo " NOMES=" . implode(',', array_column($usuarios, 'usuario'));
}
