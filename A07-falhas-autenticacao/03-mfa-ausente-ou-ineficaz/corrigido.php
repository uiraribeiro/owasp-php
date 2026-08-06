<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: MFA Obrigatório Para Contas Ativadas
 *
 * Quando a conta tem MFA habilitado, o servidor exige AND valida o código de segundo fator.
 * Sem o código correto, login é negado mesmo que a senha esteja correta.
 */

function autenticarComMfa(string $senha, string $senhaCorreta, bool $mfaHabilitadoNaConta, ?string $codigoMfaFornecido, string $codigoMfaCorreto): bool {
    // CORRIGIDO: valida a senha primeiro
    if ($senha !== $senhaCorreta) {
        return false;
    }

    // Se MFA está habilitado, exige e valida o código
    if ($mfaHabilitadoNaConta) {
        // Código MFA não pode ser null e deve ser igual ao esperado
        // Usa hash_equals para comparação segura contra timing attacks
        return $codigoMfaFornecido !== null && hash_equals($codigoMfaCorreto, $codigoMfaFornecido);
    }

    // Se MFA não está habilitado, apenas a senha é suficiente
    return true;
}

function demo(): void {
    echo "=== CORRIGIDO: MFA é obrigatório quando habilitado ===\n";

    // Cenário 1: MFA habilitado, sem código MFA
    $resultado1 = autenticarComMfa(
        senha: 'senha_correta_123',
        senhaCorreta: 'senha_correta_123',
        mfaHabilitadoNaConta: true,
        codigoMfaFornecido: null,
        codigoMfaCorreto: '123456'
    );
    echo "Conta com MFA habilitado, senha correta, SEM código MFA: " . ($resultado1 ? 'ACEITO' : 'REJEITADO') . " (bloqueado!)\n";

    // Cenário 2: MFA habilitado, código correto
    $resultado2 = autenticarComMfa(
        senha: 'senha_correta_123',
        senhaCorreta: 'senha_correta_123',
        mfaHabilitadoNaConta: true,
        codigoMfaFornecido: '123456',
        codigoMfaCorreto: '123456'
    );
    echo "Conta com MFA habilitado, senha correta, código MFA correto: " . ($resultado2 ? 'ACEITO' : 'REJEITADO') . " (permitido, caso legítimo)\n";

    // Cenário 3: MFA não habilitado, apenas senha
    $resultado3 = autenticarComMfa(
        senha: 'senha_correta_123',
        senhaCorreta: 'senha_correta_123',
        mfaHabilitadoNaConta: false,
        codigoMfaFornecido: null,
        codigoMfaCorreto: ''
    );
    echo "Conta SEM MFA, apenas senha correta: " . ($resultado3 ? 'ACEITO' : 'REJEITADO') . " (permitido)\n";
}

if (debug_backtrace() === []) {
    demo();
}
