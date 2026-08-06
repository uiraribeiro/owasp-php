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

$respostaCorreta = "miau";
$tokenEmailValido = "token_abc123";

// Teste 1: Vulnerável permite reset com APENAS pergunta correta (sem token)
$resultadoVulneravel1 = \Vulneravel\recuperarSenha($respostaCorreta, $respostaCorreta, null);
verificar(
    'Vulnerável: permite reset com resposta correta + token null (falha de design)',
    $resultadoVulneravel1 === true
);

// Teste 2: Vulnerável permite reset mesmo com pergunta errada se ignora token
$respostaErrada = "dog";
$resultadoVulneravel2 = \Vulneravel\recuperarSenha($respostaErrada, $respostaCorreta, null);
verificar(
    'Vulnerável: nega reset com resposta errada (como esperado)',
    $resultadoVulneravel2 === false
);

// Teste 3: Corrigido nega reset com resposta correta mas token null
$resultadoCorrigido1 = \Corrigido\recuperarSenha($respostaCorreta, $respostaCorreta, null);
verificar(
    'Corrigido: nega reset com resposta correta mas token null (exige 2 fatores)',
    $resultadoCorrigido1 === false
);

// Teste 4: Corrigido nega reset com resposta errada mesmo com token
$resultadoCorrigido2 = \Corrigido\recuperarSenha($respostaErrada, $respostaCorreta, $tokenEmailValido);
verificar(
    'Corrigido: nega reset com resposta errada (mesmo com token)',
    $resultadoCorrigido2 === false
);

// Teste 5: Corrigido permite reset com AMBOS os fatores (resposta + token)
$resultadoCorrigido3 = \Corrigido\recuperarSenha($respostaCorreta, $respostaCorreta, $tokenEmailValido);
verificar(
    'Corrigido: permite reset com resposta correta + token válido',
    $resultadoCorrigido3 === true
);

// Teste 6: Corrigido nega reset se token é empty string (não é válido)
$resultadoCorrigido4 = \Corrigido\recuperarSenha($respostaCorreta, $respostaCorreta, '');
verificar(
    'Corrigido: nega reset com token vazio (token deve ser não-null)',
    $resultadoCorrigido4 === false
);

// Teste 7: Vulnerável ignora completamente o token (falta de design)
$resultadoVulneravel3 = \Vulneravel\recuperarSenha($respostaCorreta, $respostaCorreta, $tokenEmailValido);
verificar(
    'Vulnerável: não importa se token é passado ou não (falta de design)',
    $resultadoVulneravel3 === true
);

// Teste 8: Vulnerável ignora token mesmo com resposta errada se código não verificar
// (esse teste confirma que Vulnerável só checa resposta, ignora token completamente)
$resultadoVulneravel4 = \Vulneravel\recuperarSenha($respostaCorreta, $respostaCorreta, null);
verificar(
    'Vulnerável: resposta correta + token null = permitido (design inseguro)',
    $resultadoVulneravel4 === true
);

echo "\nRESULTADO: {$verificacoesOk}/{$totalVerificacoes} verificacoes passaram\n";
exit($verificacoesOk === $totalVerificacoes ? 0 : 1);
