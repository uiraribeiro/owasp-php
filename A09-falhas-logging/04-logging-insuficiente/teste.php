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

// Simular sequência de eventos críticos
$eventosGraves = ['login_falhou', 'login_falhou', 'permissao_elevada', 'usuario_excluido'];

// Teste 1: Vulnerável não registra eventos críticos
$logsVulneravel = [];
foreach ($eventosGraves as $evento) {
    $logsVulneravel = \Vulneravel\tratarEventoSeguranca($evento, $logsVulneravel);
}

verificar(
    'Vulnerável não captura eventos críticos (count === 0)',
    count($logsVulneravel) === 0
);

// Teste 2: Corrigido registra todos os eventos críticos
$logsCorrigido = [];
foreach ($eventosGraves as $evento) {
    $logsCorrigido = \Corrigido\tratarEventoSeguranca($evento, $logsCorrigido);
}

verificar(
    'Corrigido captura todos os eventos críticos (count === 4)',
    count($logsCorrigido) === 4
);

// Teste 3: Corrigido contém o evento 'login_falhou' duas vezes
verificar(
    'Corrigido tem duas entradas de login_falhou',
    count(array_filter($logsCorrigido, fn($e) => $e === 'login_falhou')) === 2
);

// Teste 4: Corrigido contém 'permissao_elevada'
verificar(
    'Corrigido contém permissao_elevada',
    in_array('permissao_elevada', $logsCorrigido, true)
);

// Teste 5: Corrigido contém 'usuario_excluido'
verificar(
    'Corrigido contém usuario_excluido',
    in_array('usuario_excluido', $logsCorrigido, true)
);

// Teste 6: Vulnerável captura 'pagina_visitada' (irrelevante), Corrigido ignora (não-crítico)
$logsVulneravel2 = \Vulneravel\tratarEventoSeguranca('pagina_visitada', []);
$logsCorrigido2 = \Corrigido\tratarEventoSeguranca('pagina_visitada', []);

verificar(
    'Vulnerável captura pagina_visitada (a única coisa que capta)',
    count($logsVulneravel2) === 1 && in_array('pagina_visitada', $logsVulneravel2)
);

// Teste 7 (renumerado): Corrigido ignora eventos não-críticos
verificar(
    'Corrigido ignora pagina_visitada (não é crítico)',
    count($logsCorrigido2) === 0
);

// Teste 8: Corrigido com evento não-crítico mesclado
$logsMisto = [];
$logsMisto = \Corrigido\tratarEventoSeguranca('login_falhou', $logsMisto);
$logsMisto = \Corrigido\tratarEventoSeguranca('pagina_visitada', $logsMisto);
$logsMisto = \Corrigido\tratarEventoSeguranca('senha_alterada', $logsMisto);

verificar(
    'Corrigido captura críticos e ignora não-críticos (count === 2)',
    count($logsMisto) === 2 && in_array('login_falhou', $logsMisto) && in_array('senha_alterada', $logsMisto)
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
