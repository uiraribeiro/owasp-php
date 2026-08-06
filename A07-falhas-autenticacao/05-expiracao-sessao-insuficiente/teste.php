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

$agora = 1690000000;

// Teste 1: Vulnerável sempre considera válida (mesmo com 20 horas de idade)
$vulnAntiga = \Vulneravel\sessaoEhValida(
    $agora - (20 * 3600),  // 20 horas atrás
    $agora - 60,  // 1 minuto atrás
    $agora
);
verificar(
    'Vulnerável considera válida sessão com 20 horas de idade (nunca expira)',
    $vulnAntiga === true
);

// Teste 2: Corrigido bloqueia sessão com 20 horas de idade (excede 8 horas máximo)
$corrAntiga = \Corrigido\sessaoEhValida(
    $agora - (20 * 3600),  // 20 horas atrás
    $agora - 60,  // 1 minuto atrás
    $agora
);
verificar(
    'Corrigido bloqueia sessão com 20 horas de idade',
    $corrAntiga === false
);

// Teste 3: Corrigido permite sessão com 5 horas de idade e último acesso recente
$corrValida = \Corrigido\sessaoEhValida(
    $agora - (5 * 3600),  // 5 horas atrás
    $agora - 600,  // 10 minutos atrás
    $agora
);
verificar(
    'Corrigido permite sessão dentro dos limites (5 horas, acesso há 10 min)',
    $corrValida === true
);

// Teste 4: Corrigido bloqueia por inatividade (45 minutos sem acesso > 30 min limite)
$corrInativa = \Corrigido\sessaoEhValida(
    $agora - (2 * 3600),  // 2 horas atrás
    $agora - (45 * 60),  // 45 minutos atrás (inativo)
    $agora
);
verificar(
    'Corrigido bloqueia por inatividade (sem acesso há 45 minutos)',
    $corrInativa === false
);

// Teste 5: Corrigido permite sessão bem recente (1 hora, acesso há 5 min)
$corrNova = \Corrigido\sessaoEhValida(
    $agora - 3600,  // 1 hora atrás
    $agora - 300,  // 5 minutos atrás
    $agora
);
verificar(
    'Corrigido permite sessão nova e com acesso recente (caso legítimo)',
    $corrNova === true
);

// Teste 6: Corrigido bloqueia em exatamente 8 horas + 1 segundo
$corrExato8h = \Corrigido\sessaoEhValida(
    $agora - (8 * 3600 + 1),  // Exatamente 8 horas + 1 segundo
    $agora - 60,
    $agora
);
verificar(
    'Corrigido bloqueia ao ultrapassar limite de 8 horas',
    $corrExato8h === false
);

// Teste 7: Corrigido permite em exatamente 8 horas (ainda válida)
$corrUltimo8h = \Corrigido\sessaoEhValida(
    $agora - (8 * 3600),  // Exatamente 8 horas
    $agora - 60,
    $agora
);
verificar(
    'Corrigido permite sessão com exatamente 8 horas (limite no limite)',
    $corrUltimo8h === true
);

// Teste 8: Corrigido bloqueia em exatamente 30 minutos + 1 segundo de inatividade
$corrUltimo30min = \Corrigido\sessaoEhValida(
    $agora - 3600,  // 1 hora atrás
    $agora - (30 * 60 + 1),  // 30 min + 1 seg inativo
    $agora
);
verificar(
    'Corrigido bloqueia ao ultrapassar 30 minutos de inatividade',
    $corrUltimo30min === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
