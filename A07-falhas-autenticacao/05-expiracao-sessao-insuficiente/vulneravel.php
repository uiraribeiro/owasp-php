<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Expiração de Sessão Insuficiente (CWE-613) - A07:2025 Authentication Failures
 *
 * O servidor nunca expira sessões. Uma sessão aberta em um computador público
 * permanece válida indefinidamente, permitindo que outro usuário a seqüestre
 * após a primeira pessoa ir embora.
 */

function sessaoEhValida(int $criadaEm, int $ultimoAcesso, int $agora): bool {
    // VULNERÁVEL: nunca expira sessão, sempre considera válida
    // ignora completamente os timestamps de criação e último acesso
    return true;
}

function demo(): void {
    echo "=== VULNERÁVEL: Sessão nunca expira ===\n";

    // Sessão criada há 20 horas
    $agora = 1690000000;
    $criadaEm = $agora - (20 * 3600);  // 20 horas atrás
    $ultimoAcesso = $agora - 60;  // 1 minuto atrás

    $valida = sessaoEhValida($criadaEm, $ultimoAcesso, $agora);
    echo "Sessão criada há 20 horas, último acesso há 1 minuto: " . ($valida ? 'VÁLIDA' : 'EXPIRADA') . " (PROBLEMA!)\n";
    echo "Em um computador público, a sessão nunca expiraria mesmo horas depois do login original\n";
}

if (debug_backtrace() === []) {
    demo();
}
