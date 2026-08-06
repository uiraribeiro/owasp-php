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

// Cenário 1: Ataque de força bruta em andamento
$agora = 1700000000;
$timestampsAtaque = [];
for ($i = 1; $i <= 15; $i++) {
    $timestampsAtaque[] = $agora - (50 - $i);
}

// Teste 1: Vulnerável não detecta força bruta
$alertaVulneravel = \Vulneravel\avaliarEventosSeguranca($timestampsAtaque, $agora);

verificar(
    'Vulnerável não dispara alerta mesmo com 15 tentativas (FALHA DE DETECÇÃO)',
    $alertaVulneravel['alerta_disparado'] === false
);

// Teste 2: Corrigido detecta força bruta
$alertaCorrigido = \Corrigido\avaliarEventosSeguranca($timestampsAtaque, $agora);

verificar(
    'Corrigido dispara alerta para 15 tentativas em 60s',
    $alertaCorrigido['alerta_disparado'] === true
);

// Teste 3: Alerta do corrigido menciona força bruta
verificar(
    'Corrigido menciona força bruta na descrição',
    str_contains($alertaCorrigido['motivo'], 'forca bruta')
);

// Teste 4: Alerta do corrigido menciona o número de tentativas
verificar(
    'Corrigido menciona o número de tentativas',
    str_contains($alertaCorrigido['motivo'], '15')
);

// Cenário 2: Atividade normal (2 tentativas falhas recentes)
$timestampsNormal = [$agora - 30, $agora - 10];

// Teste 5: Vulnerável não alerta (como esperado, nunca alerta)
$alertaVulneravel2 = \Vulneravel\avaliarEventosSeguranca($timestampsNormal, $agora);

verificar(
    'Vulnerável nunca dispara alerta (mesmo em caso normal)',
    $alertaVulneravel2['alerta_disparado'] === false
);

// Teste 6: Corrigido não alerta para atividade normal (sem falso positivo)
$alertaCorrigido2 = \Corrigido\avaliarEventosSeguranca($timestampsNormal, $agora);

verificar(
    'Corrigido não alerta para 2 tentativas (dentro da norma)',
    $alertaCorrigido2['alerta_disparado'] === false
);

// Teste 7: Corrigido menciona "dentro do normal" para caso legítimo
verificar(
    'Corrigido menciona "dentro do normal" para atividade legítima',
    str_contains($alertaCorrigido2['motivo'], 'dentro do normal')
);

// Cenário 3: Exatamente no limite (10 tentativas)
$timestampsLimite = [];
for ($i = 1; $i <= 10; $i++) {
    $timestampsLimite[] = $agora - (50 - $i);
}

// Teste 8: Corrigido dispara alerta quando atinge o limite
$alertaLimite = \Corrigido\avaliarEventosSeguranca($timestampsLimite, $agora);

verificar(
    'Corrigido dispara alerta ao atingir exatamente o limite (10 tentativas)',
    $alertaLimite['alerta_disparado'] === true
);

// Teste 9: Corrigido não alerta para 9 tentativas (abaixo do limite)
$timestamps9 = [];
for ($i = 1; $i <= 9; $i++) {
    $timestamps9[] = $agora - (50 - $i);
}

$alerta9 = \Corrigido\avaliarEventosSeguranca($timestamps9, $agora);

verificar(
    'Corrigido não alerta para 9 tentativas (abaixo do limite)',
    $alerta9['alerta_disparado'] === false
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
