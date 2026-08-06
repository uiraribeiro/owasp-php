# OWASP Top 10 2025 — Exemplos Práticos em PHP

Material didático com exemplos **vulnerável vs. corrigido** em PHP puro (sem
dependências externas, sem Composer) para os seguintes itens do
[OWASP Top 10:2025](https://owasp.org/Top10/2025/):

- **A01:2025 — Broken Access Control** (`A01-controle-acesso-quebrado/`)
- **A02:2025 — Security Misconfiguration** (`A02-configuracao-incorreta/`)
- **A03:2025 — Software Supply Chain Failures** (`A03-falhas-cadeia-suprimentos/`)
- **A04:2025 — Cryptographic Failures** (`A04-falhas-criptograficas/`)
- **A05:2025 — Injection** (`A05-injecao/`)
- **A06:2025 — Insecure Design** (`A06-design-inseguro/`)
- **A07:2025 — Authentication Failures** (`A07-falhas-autenticacao/`)
- **A08:2025 — Software or Data Integrity Failures** (`A08-falhas-integridade/`)
- **A09:2025 — Security Logging and Alerting Failures** (`A09-falhas-logging/`)
- **A10:2025 — Mishandling of Exceptional Conditions** (`A10-tratamento-inadequado-excecoes/`)

Cobertura completa das 10 categorias do OWASP Top 10:2025.

## Livro completo (PDF)

`livro/livro-owasp-top10-2025-php.pdf` — 240 páginas cobrindo as 10
categorias + 2 apêndices (sqlmap e Docker), com o código real de cada
exemplo, explicações, instruções de teste e checklists detalhados. Fonte
em AsciiDoc em `livro/` (editável no AsciidocFX). Veja `livro/README.md`.

## Requisitos

- PHP 8.1+ (testado com PHP 8.5.8)
- Extensões padrão: `PDO`, `pdo_sqlite`, `openssl` (todas vêm habilitadas na
  maioria das instalações padrão do PHP)
- Nenhuma dependência externa, sem Composer, sem servidor web ou banco de
  dados externo necessário

## Estrutura

Cada categoria (A01, A04, A05) tem uma subpasta por tipo específico de falha
listado na página oficial da OWASP daquela categoria. Cada subpasta contém
exatamente 3 arquivos:

- `vulneravel.php` — implementação com a falha, comentada explicando o problema
- `corrigido.php` — implementação corrigida, comentada explicando a correção
- `teste.php` — script standalone que **prova** que o `vulneravel.php` é de
  fato explorável e que o `corrigido.php` bloqueia o mesmo ataque

Cada categoria também tem um `checklist.md` com perguntas de verificação para
usar em code review / pentest.

**Exceção:** em `A03-falhas-cadeia-suprimentos/`, 5 das 6 subpastas (`01` a
`05`) não usam `vulneravel.php`/`corrigido.php` — usam `composer.vulneravel.json` /
`composer.corrigido.json` + `teste.php`, porque ali o "problema" é a escolha
de dependências no `composer.json`, não um input malicioso em tempo de
execução. Veja também `A03-falhas-cadeia-suprimentos/composer-audit-real.md`
para como auditar dependências de verdade com `composer audit` (real, requer
internet — os exemplos desta pasta são propositalmente offline/fictícios
para serem determinísticos).

## Rodando via Docker (não precisa ter PHP instalado)

```bash
docker compose up -d --build
```

Depois abra **http://localhost:8080** no navegador — tem um índice
(`index.php`) listando as 10 categorias, com link para cada exemplo:

- **rodar teste** — executa o `teste.php` daquela pasta direto no navegador
- **ver vulnerável** / **ver corrigido** — mostra o código-fonte com
  syntax highlighting
- **▶ Rodar todos os testes** no topo da página — roda o `run-tests.sh`
  inteiro e mostra a saída completa (70/70 esperado)

Para parar o container: `docker compose down`. O `docker-compose.yml` monta
a pasta do projeto como volume, então editar qualquer `.php` reflete na
hora, sem rebuild — só é preciso rebuildar (`--build`) se mexer no
`Dockerfile`.

## Como rodar (sem Docker, direto com PHP local)

Rodar um exemplo específico:

```bash
php A01-controle-acesso-quebrado/03-idor/teste.php
```

Ver a demonstração de uso isolada de um arquivo (sem rodar o teste):

```bash
php A01-controle-acesso-quebrado/03-idor/vulneravel.php
php A01-controle-acesso-quebrado/03-idor/corrigido.php
```

Rodar **todos** os testes do projeto de uma vez e ver o resumo geral:

```bash
./run-tests.sh
```

## Cobertura

### A01 — Controle de Acesso Quebrado (9 exemplos)
Violação de privilégio mínimo, bypass de parâmetros, IDOR, falta de controle
em API por método HTTP, escalação de privilégio via role do cliente,
manipulação de JWT sem verificar assinatura, CORS mal configurado, forced
browsing, e enforcement só no client-side.

### A02 — Configuração Incorreta de Segurança (6 exemplos)
Credenciais padrão de fábrica ainda ativas, recursos/endpoints de debug
expostos em produção, mensagens de erro excessivas (stack trace vazando
dados sensíveis), headers de segurança ausentes, XXE (rejeição de
DOCTYPE/ENTITY em XML), e listagem de diretório expondo arquivos sensíveis.

### A03 — Falhas na Cadeia de Suprimentos de Software (6 exemplos)
Dependência fixada em versão com CVE conhecida, componente não mantido,
fonte de pacotes não confiável (repositório HTTP/Packagist desabilitado),
scripts/plugins de instalação sem allow-list, dependência sem versão
fixada (`*`), e uso de função obsoleta/perigosa (`extract()` sem controle
de prefixo, causando variable overwrite — CWE-447). Os 5 primeiros
exemplos usam pares de `composer.json` fictícios em vez de código PHP
(veja a exceção de estrutura acima).

### A04 — Falhas Criptográficas (8 exemplos)
Algoritmo fraco (RC4), aleatoriedade insuficiente (`mt_rand`), chave
hardcoded, transporte em texto claro (HTTP + credenciais na URL), hash sem
salt (MD5), IV fixo/reutilizado, certificado TLS não validado, e modo de
cifra inadequado (ECB).

### A05 — Injeção (8 exemplos)
SQL Injection, XSS, Command Injection, NoSQL Injection (operadores tipo
`$ne`), LDAP Injection, Code Injection via `eval()` (equivalente PHP de
EL/OGNL Injection), ORM Injection (condição SQL livre), e SQL Injection
em formulário de login via POST (mesma falha do primeiro exemplo, mas
testada como um formulário real enviaria os dados).

**sqlmap real:** `01-sql-injection/` (GET), `07-orm-injection/` (GET) e
`08-login-formulario-post/` (POST) têm também
`endpoint-vulneravel.php`/`endpoint-corrigido.php` (alvo HTTP real) e
`./testar-com-sqlmap.sh` (sobe servidor + roda sqlmap contra os dois e
compara). Veja o `SQLMAP.md` de cada pasta para os comandos exatos,
`A05-injecao/sqlmap-checklist-geral.md` para um checklist de como usar
sqlmap em qualquer aplicação PHP, e
`A05-injecao/sqlmap-manual-parametros.md` para o manual detalhado de
parâmetros (o que faz cada opção, `--level`, `--risk`, `--technique`,
etc.).

### A06 — Design Inseguro (7 exemplos)
Armazenamento de credenciais de forma reversível (base64 em vez de hash
irreversível), gerenciamento impróprio de privilégios (role agrupando
permissões demais), upload de arquivo sem allow-list/magic bytes, violação
de fronteira de confiança (mistura de dados confiáveis com input do
usuário), recuperação de credencial fraca (só pergunta de segurança),
lógica de negócio sem limite (reserva em massa sem depósito), e falta de
proteção anti-bot (sem rate limiting).

### A07 — Falhas de Autenticação (8 exemplos)
Ataques automatizados sem limite de tentativas (credential stuffing),
senhas fracas permitidas, MFA ausente/ineficaz, fixação de sessão (session
fixation), expiração de sessão insuficiente, id de sessão em local
inseguro (URL em vez de cookie protegido), bypass de autenticação via
caminho alternativo (rota legada sem checagem), e replay/captura de token
(token de uso único reutilizável).

### A08 — Falhas de Integridade de Software ou Dados (6 exemplos)
Desserialização insegura (`unserialize()` em dados não confiáveis — object
injection), atualização de software sem verificação de assinatura,
biblioteca externa sem checagem de hash, inclusão de script de terceiro
sem SRI, decisão de segurança baseada em cookie do cliente, e inclusão de
caminho não controlado (path traversal).

### A09 — Falhas de Logging e Alerta de Segurança (5 exemplos)
Injeção em log (log forging via quebra de linha), perda de informação
relevante (log sem usuário/IP/timestamp), dados sensíveis no log (senha em
texto puro), logging insuficiente (eventos críticos de segurança
ignorados), e falta de alerta para padrões anômalos (força bruta sem
detecção).

### A10 — Tratamento Inadequado de Condições Excepcionais (8 exemplos)
Erro expondo dados sensíveis (mensagem de banco crua), exceção não
tratada (escapando sem controle), valor de retorno não verificado
(falha silenciosa), caso `default` ausente em switch (fail-open),
tratamento genérico de exceções (perde distinção entre erros), falha
insegura por padrão (fail-open em vez de fail-closed), transação não
atômica sem rollback (dinheiro perdido numa falha no meio do caminho), e
dados sensíveis expostos em modo debug.

## Aviso

Este material é para fins educacionais (curso/treinamento). Os exemplos
`vulneravel.php` demonstram falhas reais de segurança de forma isolada e
controlada — **nunca** use esse código como referência de implementação, e
não rode contra sistemas de terceiros sem autorização.
