<?php
declare(strict_types=1);

namespace Corrigido;

/**
 * Correção: Validar XML e Desabilitar Entidades Externas
 *
 * XML é validado antes do parse para detectar declarações perigosas.
 * Parser é configurado com LIBXML_NONET para desabilitar acesso à rede e arquivos.
 */

function analisarXmlDeUsuario(string $xmlString): ?string {
    // CORRIGIDO: valida XML antes de processar para detectar XXE

    // Detecção de padrões perigosos: DOCTYPE e ENTITY
    if (str_contains(strtoupper($xmlString), '<!DOCTYPE') ||
        str_contains(strtoupper($xmlString), '<!ENTITY')) {
        // XML contém declarações perigosas, rejeita
        return null;
    }

    try {
        // Parse XML com LIBXML_NONET (sem LIBXML_NOENT para evitar expansão de entidades)
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NONET);
        if ($xml === false) {
            return null;
        }
        // Retorna o texto do elemento raiz
        return (string) $xml;
    } catch (\Throwable $e) {
        return null;
    }
}

function demo(): void {
    echo "=== CORRIGIDO: XXE bloqueado por validação prévia ===\n";

    // XML malicioso com XXE (bloqueado)
    $xmlMalicioso = '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/hostname">]><root>&xxe;</root>';

    $resultado = analisarXmlDeUsuario($xmlMalicioso);
    if ($resultado === null) {
        echo "XML malicioso rejeitado (bloqueado!)\n";
    }

    // XML legítimo (processado normalmente)
    $xmlLegitimo = '<root>dados sensíveis</root>';
    $resultadoLegitimo = analisarXmlDeUsuario($xmlLegitimo);
    if ($resultadoLegitimo !== null) {
        echo "XML legítimo processado com segurança: {$resultadoLegitimo}\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
