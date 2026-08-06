<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * Ataques Automatizados Sem Limite - A07:2025 Authentication Failures
 *
 * O servidor permite múltiplas tentativas de login falhadas sem nenhuma restrição.
 * Um atacante pode fazer credential stuffing ou password spray, testando milhares
 * de senhas contra uma conta sem ser bloqueado.
 */

function tentarLogin(string $usuario, string $senha, string $senhaCorreta, array $tentativasAnteriores): array {
    // VULNERÁVEL: nunca verifica o histórico de tentativas falhas
    // permite login indefinidamente, mesmo após múltiplas falhas
    if ($senha === $senhaCorreta) {
        return ['permitido' => true, 'motivo' => 'ok'];
    }
    return ['permitido' => false, 'motivo' => 'senha incorreta'];
}

function demo(): void {
    echo "=== VULNERÁVEL: Sem limite de tentativas (Credential Stuffing) ===\n";

    $tentativasAnteriores = [
        1690000001,
        1690000002,
        1690000003,
        1690000004,
        1690000005,
        1690000006,
        1690000007,
        1690000008,
        1690000009,
        1690000010,
    ];

    // Um atacante tenta com a senha correta após 10 tentativas falhas
    $resultado = tentarLogin('joao@example.com', 'senha_correta_123', 'senha_correta_123', $tentativasAnteriores);
    echo "Após 10 tentativas falhas, tentativa com senha correta: " . json_encode($resultado) . "\n";

    if ($resultado['permitido']) {
        echo "VULNERÁVEL: Login bem-sucedido mesmo após múltiplas tentativas falhas (credential stuffing funciona!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
