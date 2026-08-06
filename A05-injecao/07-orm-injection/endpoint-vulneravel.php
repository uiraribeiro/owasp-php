<?php
declare(strict_types=1);

/*
 * Endpoint HTTP fino sobre vulneravel.php, só para poder apontar o sqlmap
 * contra um alvo real. Simula uma tela de busca de usuário onde o
 * desenvolvedor monta a condição SQL a partir de um campo de busca livre
 * e repassa para o método vulnerável buscarPorFiltroRaw().
 *
 * Uso: php -S 127.0.0.1:8899, depois GET /endpoint-vulneravel.php?nome=admin
 */

require __DIR__ . '/vulneravel.php';

$pdo = Vulneravel\criarBancoDeTeste();
$repositorio = new Vulneravel\RepositorioUsuarios($pdo);

$nome = $_GET['nome'] ?? '';
$condicao = "usuario = '{$nome}'";
$usuarios = $repositorio->buscarPorFiltroRaw($condicao);

header('Content-Type: text/plain; charset=utf-8');

echo "TOTAL_USUARIOS=" . count($usuarios);
if (count($usuarios) > 0) {
    echo " NOMES=" . implode(',', array_column($usuarios, 'usuario'));
}
