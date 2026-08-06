<?php
declare(strict_types=1);

namespace Vulneravel;

/**
 * XXE (XML External Entity) - A02:2025 Security Misconfiguration
 *
 * Parser XML processa entidades externas sem restrições.
 * Atacante pode exfiltrar arquivos locais, realizar SSRF ou negação de serviço.
 */

function analisarXmlDeUsuario(string $xmlString): ?string {
    // VULNERÁVEL: processa XML diretamente sem validação prévia de declarações perigosas
    try {
        $xml = simplexml_load_string($xmlString, 'SimpleXMLElement', LIBXML_NOENT);
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
    echo "=== VULNERÁVEL: XXE via entidades externas ===\n";

    // XML malicioso com XXE
    $xmlMalicioso = '<?xml version="1.0"?><!DOCTYPE foo [<!ENTITY xxe SYSTEM "file:///etc/hostname">]><root>&xxe;</root>';

    $resultado = analisarXmlDeUsuario($xmlMalicioso);
    if ($resultado !== null) {
        echo "XML malicioso processado (PROBLEMA!)\n";
        echo "Resultado: {$resultado}\n";
    }

    // XML legítimo
    $xmlLegitimo = '<root>dados sensíveis</root>';
    $resultadoLegitimo = analisarXmlDeUsuario($xmlLegitimo);
    if ($resultadoLegitimo !== null) {
        echo "\nXML legítimo processado (esperado): {$resultadoLegitimo}\n";
    }
}

if (debug_backtrace() === []) {
    demo();
}
