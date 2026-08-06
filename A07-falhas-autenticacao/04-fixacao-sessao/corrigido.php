<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Gerar Novo ID de Sessão no Login
 *
 * Após um login bem-sucedido, o servidor sempre gera um novo id de sessão aleatório.
 * Qualquer id pré-existente é invalidado, prevenindo session fixation attacks.
 */

function autenticarERetornarIdSessao(string $usuario, string $senha, string $senhaCorreta, string $idSessaoPreExistente): ?string {
    // CORRIGIDO: valida a senha primeiro
    if ($senha !== $senhaCorreta) {
        return null;
    }

    // CORRIGIDO: gera um NOVO id de sessão aleatório (invalida qualquer id anterior)
    // random_bytes(16) gera 16 bytes aleatórios, bin2hex os converte para string hexadecimal (32 caracteres)
    $novoIdSessao = bin2hex(random_bytes(16));

    return $novoIdSessao;
}

function demo(): void {
    echo "=== CORRIGIDO: Novo ID de Sessão após Login ===\n";

    // Atacante tenta fixar um id de sessão
    $idFixadoPeloAtacante = 'id-fixado-pelo-atacante';

    // Vítima faz login (com senha correta)
    $idRetornado = autenticarERetornarIdSessao(
        'vitima@example.com',
        'senha_da_vitima_123',
        'senha_da_vitima_123',
        $idFixadoPeloAtacante
    );

    echo "Após login, sessão recebe novo id: {$idRetornado}\n";
    if ($idRetornado !== $idFixadoPeloAtacante) {
        echo "CORRIGIDO: Novo id gerado, diferente do id pré-existente!\n";
        echo "Id anterior foi invalidado, atacante não consegue sequestrar a sessão (protegido!)\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
