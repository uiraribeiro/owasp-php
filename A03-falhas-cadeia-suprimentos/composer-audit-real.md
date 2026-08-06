# `composer audit` — a ferramenta real (com internet)

Os 5 primeiros exemplos desta pasta (`01` a `05`) usam pacotes **fictícios**
(`acme/pagamentos`, `acme/log-legado`, etc.) e uma "base de vulnerabilidades
conhecidas" **hardcoded dentro do próprio `teste.php`**, de propósito: assim
o teste roda 100% offline, é determinístico e nunca fica desatualizado.

Na vida real, quem faz esse trabalho de verdade é o **Composer** (o próprio
gerenciador de pacotes do PHP), consultando a base de advisories de
segurança do ecossistema PHP (mantida pelo [FriendsOfPHP/security-advisories](https://github.com/FriendsOfPHP/security-advisories)
e agregada pelo Packagist). Isso precisa de internet — é o oposto dos
exemplos estáticos desta pasta.

## O que é

`composer audit` é um subcomando nativo do Composer (desde a versão 2.4)
que verifica se alguma dependência instalada (ou declarada no
`composer.lock`) tem uma vulnerabilidade de segurança conhecida e publicada.

## Instalação

Já vem junto do Composer — não precisa instalar nada separado. Neste
ambiente já está disponível:

```bash
composer --version
# Composer version 2.10.2
```

Se não tiver o Composer instalado: https://getcomposer.org/download/

## Como rodar

Dentro de um projeto PHP real que tenha `composer.json` (e idealmente
`composer.lock`):

```bash
# Auditoria padrão (usa os pacotes efetivamente instalados em vendor/)
composer audit

# Auditoria baseada só no composer.lock, sem precisar ter rodado composer install
composer audit --locked

# Saída em JSON (bom para integrar com CI/CD ou processar com outro script)
composer audit --format=json

# Ignorar dependências de desenvolvimento (require-dev)
composer audit --no-dev

# Tratar pacotes abandonados como falha (útil para travar o CI)
composer audit --abandoned=fail

# Resumo curto (só contagem, sem detalhar cada advisory)
composer audit --format=summary
```

## O que ele faz por baixo dos panos

1. Lê as dependências do seu `composer.lock` (ou do que está instalado em `vendor/`).
2. Consulta a API do Packagist (`https://packagist.org/api/security-advisories/`),
   que agrega o banco de dados do FriendsOfPHP/security-advisories.
3. Compara cada pacote+versão instalada contra a lista de CVEs/advisories publicados.
4. Reporta cada vulnerabilidade encontrada com severidade, CVE, e link do advisory.
5. Também reporta pacotes marcados como **abandonados** no Packagist (metadado
   separado de vulnerabilidade — um pacote pode estar sem CVE conhecida mas
   já não ser mantido).

**Requer conexão com a internet** — sem rede, o comando falha (ou usa cache
local, se disponível e não expirado).

## Diferença para os exemplos `01` a `05` desta pasta

| | Exemplos desta pasta (`01`-`05`) | `composer audit` real |
|---|---|---|
| Pacotes | Fictícios (`acme/*`) | Pacotes reais do Packagist |
| Base de vulnerabilidades | Array hardcoded no `teste.php` | API do Packagist / FriendsOfPHP |
| Precisa de internet? | Não | Sim |
| Determinístico ao longo do tempo? | Sim (nunca muda) | Não (novas CVEs aparecem, pacotes ficam abandonados) |
| Objetivo | Ensinar o CONCEITO de cada falha | Auditar um projeto REAL |

Os problemas de **fonte não confiável** (`03`), **scripts/plugins não
confiáveis** (`04`) e **dependência sem versão fixada** (`05`) não são
detectados pelo `composer audit` (ele foca em CVE conhecida e pacotes
abandonados) — para esses, a verificação continua sendo revisão manual do
`composer.json`/`composer.lock`, exatamente como os exemplos desta pasta
demonstram.

## Passo a passo prático (testando num projeto seu)

```bash
cd /caminho/do/seu/projeto-php

# se ainda não tiver rodado composer install neste projeto:
composer install --no-scripts

# rodar a auditoria
composer audit

# se quiser só o resumo, bom para rodar rapidinho em vários projetos:
composer audit --format=summary
```

Se aparecer algo como:

```
Found 2 security vulnerability advisories affecting 1 package:
--------------------------------------------------------------
Package: guzzlehttp/guzzle
CVE: CVE-2022-31091
Title: Content-Length parsing discrepancy
...
```

Isso significa que uma dependência do seu projeto tem CVE conhecida — a
correção normalmente é atualizar a versão no `composer.json` para uma faixa
que já contenha o patch (`composer update nome/pacote`) e rodar `composer
audit` de novo para confirmar que sumiu.

**Recomendação**: rode `composer audit` no seu pipeline de CI/CD a cada
build (ele retorna exit code != 0 se encontrar vulnerabilidades, então
quebra o build automaticamente).
