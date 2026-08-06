<?php
declare(strict_types=1);

$raiz = realpath(__DIR__);

/**
 * Visualizar código-fonte de um arquivo do projeto.
 * Allow-list de extensão + verificação de que o caminho real continua
 * dentro da raiz do projeto (mesma técnica ensinada em
 * A08-falhas-integridade/06-inclusao-caminho-nao-controlado/corrigido.php).
 */
if (isset($_GET['ver'])) {
    $caminhoReal = realpath($raiz . '/' . $_GET['ver']);
    $extensao = $caminhoReal !== false ? strtolower(pathinfo($caminhoReal, PATHINFO_EXTENSION)) : '';

    if (
        $caminhoReal === false
        || strpos($caminhoReal, $raiz . DIRECTORY_SEPARATOR) !== 0
        || !in_array($extensao, ['php', 'json', 'md', 'sh'], true)
    ) {
        http_response_code(400);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Arquivo invalido.";
        exit;
    }

    if ($extensao === 'php') {
        highlight_file($caminhoReal);
    } else {
        header('Content-Type: text/plain; charset=utf-8');
        readfile($caminhoReal);
    }
    exit;
}

/** Roda ./run-tests.sh e mostra a saída completa. */
if (isset($_GET['acao']) && $_GET['acao'] === 'rodar-tudo') {
    header('Content-Type: text/plain; charset=utf-8');
    $descritores = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $processo = proc_open('bash run-tests.sh', $descritores, $pipes, $raiz);
    echo stream_get_contents($pipes[1]);
    echo stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($processo);
    exit;
}

function listarCategorias(string $raiz): array
{
    $dirs = glob($raiz . '/A*', GLOB_ONLYDIR) ?: [];
    sort($dirs);
    return $dirs;
}

function listarSubpastas(string $categoriaDir): array
{
    $dirs = glob($categoriaDir . '/[0-9]*', GLOB_ONLYDIR) ?: [];
    sort($dirs);
    return $dirs;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>OWASP Top 10:2025 — Exemplos em PHP</title>
<style>
    body { font-family: -apple-system, sans-serif; max-width: 900px; margin: 2rem auto; padding: 0 1rem; line-height: 1.5; }
    h1 { font-size: 1.4rem; }
    h2 { font-size: 1.1rem; border-bottom: 1px solid #ccc; padding-bottom: .25rem; margin-top: 2rem; }
    ul { padding-left: 1.2rem; }
    li { margin-bottom: .3rem; }
    a { text-decoration: none; color: #0b5fff; }
    a:hover { text-decoration: underline; }
    code { background: #f0f0f0; padding: .1rem .3rem; border-radius: 3px; }
    .topo { background: #f7f7f7; border: 1px solid #ddd; border-radius: 6px; padding: 1rem; margin-bottom: 1.5rem; }
    .sub { color: #666; font-size: .9rem; }
</style>
</head>
<body>

<h1>OWASP Top 10:2025 — Exemplos práticos em PHP</h1>

<div class="topo">
    <p><a href="?acao=rodar-tudo" target="_blank"><strong>▶ Rodar todos os testes</strong></a> (<code>run-tests.sh</code>, ~70 verificações)</p>
    <p><a href="?ver=README.md" target="_blank">📄 Ler README completo</a></p>
    <p><a href="?ver=A05-injecao/sqlmap-checklist-geral.md" target="_blank">🛡 Checklist geral de sqlmap</a></p>
    <p><a href="?ver=A05-injecao/sqlmap-manual-parametros.md" target="_blank">📘 Manual de parâmetros do sqlmap</a></p>
</div>

<?php foreach (listarCategorias($raiz) as $categoriaDir):
    $nomeCategoria = basename($categoriaDir);
    $checklistRel = $nomeCategoria . '/checklist.md';
    ?>
    <h2><?= htmlspecialchars($nomeCategoria) ?></h2>
    <?php if (is_file($categoriaDir . '/checklist.md')): ?>
        <p class="sub"><a href="?ver=<?= urlencode($checklistRel) ?>" target="_blank">checklist.md desta categoria</a></p>
    <?php endif; ?>
    <ul>
    <?php foreach (listarSubpastas($categoriaDir) as $subDir):
        $nomeSub = basename($subDir);
        $rel = $nomeCategoria . '/' . $nomeSub;
        ?>
        <li>
            <strong><?= htmlspecialchars($nomeSub) ?></strong>
            <?php if (is_file($subDir . '/teste.php')): ?>
                — <a href="<?= htmlspecialchars($rel) ?>/teste.php" target="_blank">rodar teste</a>
            <?php endif; ?>
            <?php if (is_file($subDir . '/vulneravel.php')): ?>
                — <a href="?ver=<?= urlencode($rel . '/vulneravel.php') ?>" target="_blank">ver vulnerável</a>
            <?php elseif (is_file($subDir . '/composer.vulneravel.json')): ?>
                — <a href="?ver=<?= urlencode($rel . '/composer.vulneravel.json') ?>" target="_blank">ver composer vulnerável</a>
            <?php endif; ?>
            <?php if (is_file($subDir . '/corrigido.php')): ?>
                — <a href="?ver=<?= urlencode($rel . '/corrigido.php') ?>" target="_blank">ver corrigido</a>
            <?php elseif (is_file($subDir . '/composer.corrigido.json')): ?>
                — <a href="?ver=<?= urlencode($rel . '/composer.corrigido.json') ?>" target="_blank">ver composer corrigido</a>
            <?php endif; ?>
            <?php if (is_file($subDir . '/SQLMAP.md')): ?>
                — <a href="?ver=<?= urlencode($rel . '/SQLMAP.md') ?>" target="_blank">SQLMAP.md</a>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endforeach; ?>

</body>
</html>
