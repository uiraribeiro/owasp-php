<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Isolamento Claro de Fronteiras de Confiança
 *
 * A decisão de design correta SEPARA dados confiáveis (do servidor/autenticados)
 * de dados NÃO-confiáveis (input do usuário) em NAMESPACES DISTINTOS dentro
 * da mesma estrutura. Dessa forma, mesmo que o atacante consiga enviar qualquer
 * input malicioso, ele sempre fica isolado em 'usuario', nunca toca em 'servidor'.
 * Código que precisa verificar privilégios acessa SEMPRE 'servidor.isAdmin',
 * nunca é influenciado pelo que está em 'usuario.isAdmin'.
 */

function montarSessao(array $dadosConfiaveisDoServidor, array $inputDoUsuario): array {
    // CORRIGIDO: separa em namespaces distintos
    // Dados confiáveis NUNCA são misturados com input do usuário
    return [
        'servidor' => $dadosConfiaveisDoServidor,  // Confiável, intocável
        'usuario'  => $inputDoUsuario,              // Input do usuário, isolado
    ];
}

function verificarPrivilegio(array $sessao): bool {
    // Acessa SEMPRE os dados confiáveis do servidor
    // Impossível que input do usuário interfira
    return $sessao['servidor']['isAdmin'] ?? false;
}

function obterEmailDoUsuario(array $sessao): string {
    // Pode consultar dados do usuário se necessário, mas sabendo que é input
    return $sessao['usuario']['email'] ?? 'desconhecido@localhost';
}

function demo(): void {
    echo "=== CORRIGIDO: Fronteiras claras entre dados confiáveis e input ===\n";

    $dadosConfiaveisDoServidor = [
        'isAdmin' => false,
        'usuario_id' => 42,
        'email' => 'user@example.com',
    ];

    $inputDoUsuarioAtacante = [
        'isAdmin' => true,  // Tentativa de escalar privilégio
        'email' => 'hacker@evil.com',
    ];

    $sessaoSegura = montarSessao($dadosConfiaveisDoServidor, $inputDoUsuarioAtacante);
    echo "Sessão estruturada: " . json_encode($sessaoSegura) . "\n";

    $ehAdmin = verificarPrivilegio($sessaoSegura);
    if ($ehAdmin === false) {
        echo "OK: Privilégio verificado SEMPRE nos dados confiáveis do servidor\n";
        echo "OK: Input malicioso do usuário fica isolado em 'usuario', não afeta segurança\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
