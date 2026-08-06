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

// Teste 1: Vulnerável expõe a senha em texto puro
$logVulneravel = \Vulneravel\registrarRequisicaoLogin('joao', 'MinhaSenhaSecreta123');

verificar(
    'Vulnerável contém a senha em texto puro (VAZAMENTO)',
    str_contains($logVulneravel, 'MinhaSenhaSecreta123')
);

// Teste 2: Corrigido não expõe a senha
$logCorrigido = \Corrigido\registrarRequisicaoLogin('joao', 'MinhaSenhaSecreta123');

verificar(
    'Corrigido não contém a senha em lugar nenhum',
    !str_contains($logCorrigido, 'MinhaSenhaSecreta123')
);

// Teste 3: Corrigido redata com [REDACTED]
verificar(
    'Corrigido contém marcador [REDACTED]',
    str_contains($logCorrigido, '[REDACTED]')
);

// Teste 4: Corrigido mantém o nome de usuário (dado não-sensível)
verificar(
    'Corrigido mantém nome de usuário',
    str_contains($logCorrigido, 'joao')
);

// Teste 5: Diferentes senhas também são redatadas
$logOutraSenh = \Corrigido\registrarRequisicaoLogin('maria', 'OutraSenha456');

verificar(
    'Corrigido redata outras senhas também',
    !str_contains($logOutraSenh, 'OutraSenha456') && str_contains($logOutraSenh, '[REDACTED]')
);

// Teste 6: Vulnerável expõe diferentes senhas também
$logOutraSenhVulneravel = \Vulneravel\registrarRequisicaoLogin('maria', 'OutraSenha456');

verificar(
    'Vulnerável expõe qualquer senha',
    str_contains($logOutraSenhVulneravel, 'OutraSenha456')
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
