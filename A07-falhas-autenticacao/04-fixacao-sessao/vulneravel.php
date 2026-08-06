<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Fixação de Sessão (CWE-384) - A07:2025 Authentication Failures
 *
 * O servidor reaproveita o id de sessão pré-existente após um login bem-sucedido.
 * Um atacante pode "fixar" um id de sessão na vítima (via email com link, proxy ARP, etc),
 * e depois fazer o login em sua própria conta. Quando a vítima clica no link e faz login,
 * ambas compartilham a mesma sessão, permitindo sequestro de sessão (session hijacking).
 */

function autenticarERetornarIdSessao(string $usuario, string $senha, string $senhaCorreta, string $idSessaoPreExistente): ?string {
    // VULNERÁVEL: reaproveita o id de sessão pré-existente após login bem-sucedido
    if ($senha !== $senhaCorreta) {
        return null;
    }
    // PROBLEMA: retorna o mesmo id que já existia - permite session fixation!
    return $idSessaoPreExistente;
}

function demo(): void {
    echo "=== VULNERÁVEL: Session Fixation ===\n";

    // Atacante "fixa" um id de sessão na vítima
    $idFixadoPeloAtacante = 'id-fixado-pelo-atacante';

    // Vítima faz login (com senha correta)
    $idRetornado = autenticarERetornarIdSessao(
        'vitima@example.com',
        'senha_da_vitima_123',
        'senha_da_vitima_123',
        $idFixadoPeloAtacante
    );

    echo "Após login, sessão recebe id: {$idRetornado}\n";
    if ($idRetornado === $idFixadoPeloAtacante) {
        echo "VULNERÁVEL: Mesmo id de sessão pré-existente foi reutilizado!\n";
        echo "Atacante pode usar este id para sequestrar a sessão da vítima (PROBLEM!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
