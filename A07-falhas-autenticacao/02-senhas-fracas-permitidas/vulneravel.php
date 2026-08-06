<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Senhas Fracas Permitidas - A07:2025 Authentication Failures
 *
 * O servidor aceita senhas muito curtas sem validar qualidade.
 * Um atacante pode usar força bruta ou dicionário para quebrar senhas fracas
 * como "1234", "senha" ou "admin" rapidamente.
 */

function validarNovaSenha(string $senha, string $usuario): bool {
    // VULNERÁVEL: apenas verifica comprimento mínimo insuficiente (4 caracteres)
    // aceita senhas como "1234", "admin", "pass", etc.
    return strlen($senha) >= 4;
}

function demo(): void {
    echo "=== VULNERÁVEL: Senhas fracas são aceitas ===\n";

    $senhasTesteVulneravel = [
        ['senha' => '1234', 'usuario' => 'joao', 'esperado' => true],
        ['senha' => 'pass', 'usuario' => 'maria', 'esperado' => true],
        ['senha' => 'admin', 'usuario' => 'root', 'esperado' => true],
    ];

    foreach ($senhasTesteVulneravel as $teste) {
        $valida = validarNovaSenha($teste['senha'], $teste['usuario']);
        echo "Senha '{$teste['senha']}' para {$teste['usuario']}: " . ($valida ? 'ACEITA' : 'REJEITADA') . " (PROBLEMA!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
