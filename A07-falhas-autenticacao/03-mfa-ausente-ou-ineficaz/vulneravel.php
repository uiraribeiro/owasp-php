<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * MFA Ausente ou Ineficaz - A07:2025 Authentication Failures
 *
 * O servidor verifica a senha mas ignora o segundo fator de autenticação (MFA).
 * Um atacante que rouba apenas a senha consegue fazer login mesmo que a conta
 * tenha MFA habilitado, contornando completamente a segunda camada.
 */

function autenticarComMfa(string $senha, string $senhaCorreta, bool $mfaHabilitadoNaConta, ?string $codigoMfaFornecido, string $codigoMfaCorreto): bool {
    // VULNERÁVEL: ignora completamente o MFA
    // valida apenas a senha, sem checar o código MFA mesmo que esteja habilitado
    if ($senha !== $senhaCorreta) {
        return false;
    }
    // LOGIN ACEITO SEM VALIDAR MFA - PROBLEMA!
    return true;
}

function demo(): void {
    echo "=== VULNERÁVEL: MFA é ignorado ===\n";

    // Cenário: usuário com MFA habilitado, atacante tem apenas a senha
    $resultado = autenticarComMfa(
        senha: 'senha_correta_123',
        senhaCorreta: 'senha_correta_123',
        mfaHabilitadoNaConta: true,
        codigoMfaFornecido: null,  // Atacante não tem o segundo fator
        codigoMfaCorreto: '123456'
    );

    echo "Conta com MFA habilitado, senha correta, mas SEM código MFA: " . ($resultado ? 'ACEITO' : 'REJEITADO') . " (PROBLEMA!)\n";
}

if (debug_backtrace() === []) {
    demo();
}
