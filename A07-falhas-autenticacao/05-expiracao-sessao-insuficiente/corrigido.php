<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Expiração de Sessão em Múltiplos Níveis
 *
 * Implementa dois limites de tempo:
 * 1. Máximo absoluto: 8 horas desde criação (sessão nunca dura mais que isso)
 * 2. Inatividade: 30 minutos sem acesso (sessão expira se usuário desaparecer)
 */

function sessaoEhValida(int $criadaEm, int $ultimoAcesso, int $agora): bool {
    // CORRIGIDO: dois limites de expiração

    // 1. Tempo máximo absoluto desde criação: 8 horas (28800 segundos)
    $tempoMaximoAbsoluto = 8 * 3600;  // 28800 segundos
    $idadeAbsoluta = $agora - $criadaEm;

    if ($idadeAbsoluta > $tempoMaximoAbsoluto) {
        return false;  // Sessão expirou por idade máxima
    }

    // 2. Tempo máximo de inatividade: 30 minutos (1800 segundos)
    $tempoMaximoInatividade = 30 * 60;  // 1800 segundos
    $tempoSemAcesso = $agora - $ultimoAcesso;

    if ($tempoSemAcesso > $tempoMaximoInatividade) {
        return false;  // Sessão expirou por inatividade
    }

    // Ambos os limites foram respeitados
    return true;
}

function demo(): void {
    echo "=== CORRIGIDO: Expiração em múltiplos níveis ===\n";

    // Cenário 1: Sessão muito antiga (20 horas)
    $agora = 1690000000;
    $criadaEm = $agora - (20 * 3600);  // 20 horas atrás
    $ultimoAcesso = $agora - 60;  // 1 minuto atrás

    $valida = sessaoEhValida($criadaEm, $ultimoAcesso, $agora);
    echo "Sessão criada há 20 horas, último acesso há 1 minuto: " . ($valida ? 'VÁLIDA' : 'EXPIRADA') . " (expirou!)\n";

    // Cenário 2: Sessão dentro dos limites
    $criadaEm2 = $agora - (5 * 3600);  // 5 horas atrás
    $ultimoAcesso2 = $agora - 600;  // 10 minutos atrás

    $valida2 = sessaoEhValida($criadaEm2, $ultimoAcesso2, $agora);
    echo "Sessão criada há 5 horas, último acesso há 10 minutos: " . ($valida2 ? 'VÁLIDA' : 'EXPIRADA') . " (ainda válida)\n";

    // Cenário 3: Sessão inativa por muito tempo
    $criadaEm3 = $agora - (2 * 3600);  // 2 horas atrás
    $ultimoAcesso3 = $agora - (45 * 60);  // 45 minutos atrás

    $valida3 = sessaoEhValida($criadaEm3, $ultimoAcesso3, $agora);
    echo "Sessão criada há 2 horas, último acesso há 45 minutos (inativo): " . ($valida3 ? 'VÁLIDA' : 'EXPIRADA') . " (expirou por inatividade!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
