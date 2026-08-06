# OWASP Top 10 2025 - A05 Injection (Injeção)

Material didático com exemplos de vulnerabilidades de injeção e suas correções. Referência oficial: [OWASP Top 10 2025 - A05 Injection](https://owasp.org/Top10/2025/A05_2025-Injection/)

## Checklist de Verificação para Code Review

Utilize este checklist ao revisar código para identificar e prevenir vulnerabilidades de injeção:

- [ ] **SQL Injection (01-sql-injection)**: Todas as queries SQL usam prepared statements com parâmetros vinculados (`:nome` ou `?`), nunca concatenação direta de input do usuário? Verificar especialmente em queries de login, busca e filtros.

- [ ] **Cross-Site Scripting (02-xss)**: Todo conteúdo renderizado no HTML que vem de entrada do usuário está escapado com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`? Isto inclui comentários, mensagens, dados de formulários.

- [ ] **Command Injection (03-command-injection)**: Argumentos de comandos do sistema operacional estão protegidos com `escapeshellarg()` ou em um array passado a `proc_open()`? Evitar `shell_exec()`, `exec()`, `system()` com input do usuário.

- [ ] **NoSQL Injection (04-nosql-injection)**: Parâmetros vindos de JSON/API estão validados quanto ao tipo esperado? Campos críticos como senhas devem ser strings simples, nunca arrays com operadores (`$ne`, `$gt`, etc).

- [ ] **LDAP Injection (05-ldap-injection)**: Filtros LDAP estão usando escape de metacaracteres (`\`, `*`, `(`, `)`, NUL)? Não confiar que a biblioteca LDAP faz escape automaticamente.

- [ ] **Code Injection / EL Injection (06-code-injection-eval)**: Usar `eval()` com entrada do usuário ou com construção dinâmica de código? Processadores de template caseiros devem ser restritos (whitelist de nomes de variável, nunca eval).

- [ ] **ORM Injection (07-orm-injection)**: A camada de persistência expõe métodos que aceitam SQL/NoSQL/LDAP arbitrários? Métodos devem ser específicos (ex: `buscarPorNomeUsuario()`, nunca `buscarPorFiltro()` genérico).

## Estrutura do Material

- **01-sql-injection/**: SQLite + PDO. Demonstra bypass de autenticação e vazamento via SQL Injection.
- **02-xss/**: String HTML. Demonstra execução de scripts e injection de eventos.
- **03-command-injection/**: Montagem de comandos shell. Demonstra encandeamento de comandos.
- **04-nosql-injection/**: Array simulando coleção NoSQL. Demonstra bypass via operadores.
- **05-ldap-injection/**: Filtros LDAP. Demonstra alteração de estrutura de query.
- **06-code-injection-eval/**: Template engine caseira. Demonstra execução de código PHP.
- **07-orm-injection/**: Classe repositório simples. Demonstra ORM Injection e correção por API restrita.

Cada pasta contém `vulneravel.php` (com falha), `corrigido.php` (com correção) e `teste.php` (com casos de teste automatizados).
