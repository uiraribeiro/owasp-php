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

$arquivos = ['index.html', '.env', 'config.php.bak', 'backup.sql', 'credentials.json'];

// Teste 1: Vulnerável retorna status 200 para diretório sem index
$respostaVulneravel = \Vulneravel\tratarRequisicaoArquivo('/uploads/', $arquivos, false);
verificar(
    'Vulnerável retorna status 200 para diretório sem index',
    $respostaVulneravel['status'] === 200
);

// Teste 2: Vulnerável lista arquivo .env no corpo
verificar(
    'Vulnerável expõe arquivo .env no corpo',
    str_contains($respostaVulneravel['corpo'], '.env')
);

// Teste 3: Vulnerável lista arquivo config.php.bak
verificar(
    'Vulnerável expõe arquivo config.php.bak',
    str_contains($respostaVulneravel['corpo'], 'config.php.bak')
);

// Teste 4: Vulnerável lista arquivo backup.sql
verificar(
    'Vulnerável expõe arquivo backup.sql',
    str_contains($respostaVulneravel['corpo'], 'backup.sql')
);

// Teste 5: Vulnerável lista múltiplos arquivos
verificar(
    'Vulnerável lista múltiplos arquivos no corpo',
    str_contains($respostaVulneravel['corpo'], '.env') &&
    str_contains($respostaVulneravel['corpo'], 'config.php.bak') &&
    str_contains($respostaVulneravel['corpo'], 'backup.sql')
);

// Teste 6: Corrigido retorna status 403 para diretório sem index
$respostaCorrigido = \Corrigido\tratarRequisicaoArquivo('/uploads/', $arquivos, false);
verificar(
    'Corrigido retorna status 403 para diretório sem index',
    $respostaCorrigido['status'] === 403
);

// Teste 7: Corrigido não expõe arquivo .env
verificar(
    'Corrigido não expõe arquivo .env',
    !str_contains($respostaCorrigido['corpo'], '.env')
);

// Teste 8: Corrigido não expõe arquivo config.php.bak
verificar(
    'Corrigido não expõe arquivo config.php.bak',
    !str_contains($respostaCorrigido['corpo'], 'config.php.bak')
);

// Teste 9: Corrigido não expõe arquivo backup.sql
verificar(
    'Corrigido não expõe arquivo backup.sql',
    !str_contains($respostaCorrigido['corpo'], 'backup.sql')
);

// Teste 10: Corrigido retorna mensagem genérica
verificar(
    'Corrigido retorna mensagem genérica sem nomes de arquivo',
    str_contains($respostaCorrigido['corpo'], 'Acesso negado') ||
    str_contains($respostaCorrigido['corpo'], 'não é permitida')
);

// Teste 11: Vulnerável com index funciona normalmente
$respostaVulneravelComIndex = \Vulneravel\tratarRequisicaoArquivo('/uploads/', $arquivos, true);
verificar(
    'Vulnerável com index.html retorna status 200',
    $respostaVulneravelComIndex['status'] === 200
);

// Teste 12: Corrigido com index funciona normalmente
$respostaCorrigidoComIndex = \Corrigido\tratarRequisicaoArquivo('/uploads/', $arquivos, true);
verificar(
    'Corrigido com index.html retorna status 200',
    $respostaCorrigidoComIndex['status'] === 200
);

// Teste 13: Corrigido com index retorna conteúdo legítimo
verificar(
    'Corrigido com index retorna conteúdo legítimo',
    str_contains($respostaCorrigidoComIndex['corpo'], 'index.html')
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
