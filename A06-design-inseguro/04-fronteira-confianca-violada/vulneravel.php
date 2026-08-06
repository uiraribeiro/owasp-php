<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Fronteira de Confiança Violada (CWE-501) - A06:2025 Insecure Design
 *
 * A falha de DESIGN aqui é a falta de isolamento entre dados CONFIÁVEIS
 * (do servidor, autenticados) e NÃO-CONFIÁVEIS (input do atacante).
 * Ambos são misturados no mesmo array plano. Atacante que consegue enviar
 * input pode sobrescrever dados confiáveis do servidor, como 'isAdmin'.
 * A falha não é na verificação, é na ARQUITETURA que não isolou fronteiras.
 */

function montarSessao(array $dadosConfiaveisDoServidor, array $inputDoUsuario): array {
    // VULNERÁVEL: mistura dados confiáveis com input do atacante SEM ISOLAMENTO
    // Se o servidor diz ['isAdmin' => false] mas o input tem ['isAdmin' => true],
    // o array_merge vai deixar o true do atacante sobrescrever o false confiável
    return array_merge($dadosConfiaveisDoServidor, $inputDoUsuario);
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem fronteira entre dados confiáveis e input ===\n";

    $dadosConfiaveisDoServidor = [
        'isAdmin' => false,
        'usuario_id' => 42,
        'email' => 'user@example.com',
    ];

    $inputDoUsuarioAtacante = [
        'isAdmin' => true,  // Atacante tenta escalar privilégio
        'email' => 'hacker@evil.com',
    ];

    $sessaoMisturada = montarSessao($dadosConfiaveisDoServidor, $inputDoUsuarioAtacante);
    echo "Sessão montada: " . json_encode($sessaoMisturada) . "\n";

    if ($sessaoMisturada['isAdmin'] === true) {
        echo "PROBLEMA: Atacante conseguiu sobrescrever isAdmin para true (falha de design!)!\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
