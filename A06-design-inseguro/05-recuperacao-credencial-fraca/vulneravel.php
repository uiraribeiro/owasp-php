<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Recuperação de Credencial Fraca (Cenário 1 - OWASP) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é permitir que um ÚNICO fator (pergunta de segurança)
 * seja suficiente para recuperar/resetar a senha. Perguntas de segurança podem ser
 * descobertas via engenharia social, redes sociais, ou pesquisa pública.
 * A decisão arquitetural incorreta foi não exigir SEGUNDO FATOR (token de e-mail validado).
 * Isso viola o princípio de "múltiplos fatores de autenticação para operações sensíveis".
 */

function recuperarSenha(string $respostaFornecida, string $respostaCorretaArmazenada, ?string $tokenEmailValidado): bool {
    // VULNERÁVEL: apenas um fator (pergunta de segurança)
    // Se o atacante conseguir descobrir a resposta (engenharia social),
    // consegue resetar a senha SEM acesso ao e-mail da vítima
    return $respostaFornecida === $respostaCorretaArmazenada;
}

function demo(): void {
    echo "=== VULNERÁVEL: Recuperação com um único fator (pergunta de segurança) ===\n";

    $respostaCorreta = "miau";  // Resposta à pergunta: "Qual é o nome do seu primeiro gato?"
    $tokenEmail = "token_abc123";

    // Atacante descobre a resposta por engenharia social
    $respostaAtacante = "miau";

    // Mesmo SEM token de e-mail, consegue resetar a senha (FALHA!)
    if (recuperarSenha($respostaAtacante, $respostaCorreta, null)) {
        echo "PROBLEMA: Atacante conseguiu resetar senha SEM acesso ao e-mail!\n";
        echo "Pergunta de segurança sozinha NÃO é suficiente (falha de design)!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
