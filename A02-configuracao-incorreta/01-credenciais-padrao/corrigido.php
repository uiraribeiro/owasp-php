<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Remover e Bloquear Credenciais Padrão de Fábrica
 *
 * Credenciais padrão são explicitamente bloqueadas, forçando alteração obrigatória
 * na primeira autenticação e permitindo apenas senhas customizadas e seguras.
 */

function senhaEhPadraoDeFabrica(string $senha): bool {
    // Lista de senhas padrão conhecidas que NUNCA devem ser aceitas
    $senhasPadrao = [
        'admin123',
        '123456',
        'password',
        'admin',
        '12345678',
        'qwerty',
    ];

    return in_array($senha, $senhasPadrao, true);
}

function autenticarAdmin(string $usuario, string $senhaFornecida, string $hashArmazenado): bool {
    // CORRIGIDO: rejeita credenciais padrão de fábrica MESMO que o hash corresponda
    if (senhaEhPadraoDeFabrica($senhaFornecida)) {
        return false;
    }

    // Verifica se a senha fornecida combina com o hash armazenado
    return password_verify($senhaFornecida, $hashArmazenado);
}

function demo(): void {
    echo "=== CORRIGIDO: Credenciais padrão bloqueadas ===\n";

    // Hash de uma senha customizada segura
    $hashSenhaPersonalizada = password_hash('SenhaForte2024!', PASSWORD_DEFAULT);

    // Tentativa 1: Login com credencial padrão (bloqueado mesmo com hash válido)
    $hashAdmin123 = password_hash('admin123', PASSWORD_DEFAULT);
    if (!autenticarAdmin('admin', 'admin123', $hashAdmin123)) {
        echo "Credencial padrão 'admin123' bloqueada (protegido!)\n";
    }

    // Tentativa 2: Login com senha customizada (bloqueado se não combinar com hash)
    if (!autenticarAdmin('admin', 'senha_errada', $hashSenhaPersonalizada)) {
        echo "Senha incorreta rejeitada (esperado)\n";
    }

    // Tentativa 3: Login com senha customizada correta (aceito)
    if (autenticarAdmin('admin', 'SenhaForte2024!', $hashSenhaPersonalizada)) {
        echo "Senha customizada válida aceita (caso legítimo)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
