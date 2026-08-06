<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Recuperação de Credencial com Múltiplos Fatores
 *
 * A decisão de design correta exige MÍNIMO 2 FATORES para operações sensíveis:
 * 1. Resposta correta à pergunta de segurança (algo que só o dono sabe)
 * 2. Validação via token enviado ao e-mail/SMS cadastrado (algo que só o dono pode acessar)
 * Mesmo que um atacante descubra a resposta da pergunta via engenharia social,
 * sem acesso ao e-mail da vítima não consegue completar a recuperação.
 */

function recuperarSenha(string $respostaFornecida, string $respostaCorretaArmazenada, ?string $tokenEmailValidado): bool {
    // CORRIGIDO: exige DOIS fatores
    // A resposta deve estar correta E deve haver token de e-mail validado

    // Fator 1: Pergunta de segurança
    if ($respostaFornecida !== $respostaCorretaArmazenada) {
        return false;
    }

    // Fator 2: Token de e-mail (não pode ser null ou vazio)
    if ($tokenEmailValidado === null || $tokenEmailValidado === '') {
        return false;
    }

    // Ambos os fatores passaram
    return true;
}

function enviarTokenPorEmail(string $email): string {
    // Simula envio de token para e-mail (em produção seria de verdade)
    // Retorna um token que seria recebido pelo e-mail do usuário
    return "token_" . bin2hex(random_bytes(16));
}

function demo(): void {
    echo "=== CORRIGIDO: Recuperação com múltiplos fatores ===\n";

    $respostaCorreta = "miau";
    $emailUsuario = "user@example.com";

    // Cenário 1: Atacante sabe a resposta mas não tem acesso ao e-mail
    echo "\nCenário 1: Atacante com resposta correta mas sem acesso ao e-mail\n";
    $respostaAtacante = "miau";
    $resultadoAtaque = recuperarSenha($respostaAtacante, $respostaCorreta, null);
    if (!$resultadoAtaque) {
        echo "OK: Reset negado (fator 2 ausente)\n";
    }

    // Cenário 2: Dono legítimo com resposta correta E token de e-mail
    echo "\nCenário 2: Dono legítimo com ambos os fatores\n";
    $tokenValido = enviarTokenPorEmail($emailUsuario);
    $resultadoLegitimo = recuperarSenha($respostaCorreta, $respostaCorreta, $tokenValido);
    if ($resultadoLegitimo) {
        echo "OK: Reset permitido (ambos os fatores validados)\n";
    }

    // Cenário 3: Atacante tenta com resposta errada + token fakeado
    echo "\nCenário 3: Atacante com resposta errada\n";
    $respostaErrada = "dog";
    $resultadoErro = recuperarSenha($respostaErrada, $respostaCorreta, $tokenValido);
    if (!$resultadoErro) {
        echo "OK: Reset negado (resposta de segurança incorreta)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
