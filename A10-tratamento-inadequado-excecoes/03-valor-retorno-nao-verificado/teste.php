<?php
declare(strict_types=1);

require __DIR__ . '/vulneravel.php';
require __DIR__ . '/corrigido.php';

$totalVerificacoes = 0;
$verificacoesOk = 0;

function verificar(string $descricao, bool $condicao): void {
    global $totalVerificacoes, $verificacoesOk;
    $totalVerificacoes++;
    if ($condicao) {
        $verificacoesOk++;
        echo "[OK] {$descricao}\n";
    } else {
        echo "[FALHA] {$descricao}\n";
    }
}

// Teste 1: Vulnerável com caminho inválido (falha silenciosa)
$caminhoInvalido = sys_get_temp_dir() . '/pasta-nao-existe-' . uniqid() . '/config.txt';
$resultado1 = \Vulneravel\salvarConfiguracao($caminhoInvalido, 'teste');
$falhasilenciosa = $resultado1['status'] === 'sucesso' && !file_exists($caminhoInvalido);
verificar(
    'Vulnerável reporta sucesso mas arquivo não foi criado (falha silenciosa)',
    $falhasilenciosa
);

// Teste 2: Corrigido com caminho inválido (detecta erro)
$caminhoInvalido2 = sys_get_temp_dir() . '/pasta-nao-existe-' . uniqid() . '/config.txt';
$resultado2 = \Corrigido\salvarConfiguracao($caminhoInvalido2, 'teste');
verificar(
    'Corrigido retorna status=erro quando caminho é inválido',
    $resultado2['status'] === 'erro'
);

// Teste 3: Ambos com caminho válido (sucesso legítimo)
$caminhoValido = sys_get_temp_dir() . '/teste_a10_' . uniqid() . '.txt';
$resultado3 = \Vulneravel\salvarConfiguracao($caminhoValido, 'teste1');
$resultado4 = \Corrigido\salvarConfiguracao($caminhoValido . '.v2', 'teste2');

$ambosSuccesso = $resultado3['status'] === 'sucesso' && $resultado4['status'] === 'sucesso';
$ambosExistem = file_exists($caminhoValido) && file_exists($caminhoValido . '.v2');
verificar(
    'Ambos retornam sucesso e criam arquivo quando caminho é válido',
    $ambosSuccesso && $ambosExistem
);

// Teste 4: Corrigido tem mensagem de erro descritiva
$caminhoInvalido3 = sys_get_temp_dir() . '/pasta-nao-existe-' . uniqid() . '/config.txt';
$resultado5 = \Corrigido\salvarConfiguracao($caminhoInvalido3, 'teste');
verificar(
    'Corrigido fornece mensagem de erro descritiva',
    $resultado5['status'] === 'erro' && isset($resultado5['mensagem'])
);

// Limpeza
if (file_exists($caminhoValido)) {
    unlink($caminhoValido);
}
if (file_exists($caminhoValido . '.v2')) {
    unlink($caminhoValido . '.v2');
}

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
