# A02:2025 - Configuração Incorreta de Segurança (Security Misconfiguration)

Este material didático apresenta vulnerabilidades comuns e suas correções relacionadas a **Configuração Incorreta de Segurança**, conforme documentado no [OWASP Top 10 2025 - A02](https://owasp.org/Top10/2025/A02_2025-Security_Misconfiguration/).

Configuração incorreta de segurança ocorre quando sistemas não são adequadamente hardened, como deixar credenciais padrão ativas, expor funcionalidades de debug em produção, ou não configurar headers de segurança essenciais.

## Checklist de Verificação

Use este checklist durante code reviews, testes de segurança (pentest) ou validação de arquitetura:

- [ ] **01-credenciais-padrao** — Credenciais padrão de fábrica (admin/admin123, 123456, etc.) são explicitamente bloqueadas pelo sistema e recusadas MESMO que o hash armazenado corresponda a elas, forçando alteração obrigatória? (ver `01-credenciais-padrao/`)

- [ ] **02-recursos-desnecessarios** — Endpoints de debug, profilers, consoles e outras funcionalidades de desenvolvimento estão desabilitadas em ambiente de produção e retornam 404? (ver `02-recursos-desnecessarios/`)

- [ ] **03-mensagens-erro-excessivas** — Mensagens de erro em produção são genéricas e não expõem stack traces, caminhos de arquivo, senhas hardcoded ou outras informações sensíveis do servidor? (ver `03-mensagens-erro-excessivas/`)

- [ ] **04-headers-seguranca-ausentes** — Todos os headers HTTP de segurança essenciais estão configurados (X-Content-Type-Options, X-Frame-Options, Content-Security-Policy, Strict-Transport-Security)? (ver `04-headers-seguranca-ausentes/`)

- [ ] **05-xxe-xml-externo** — Parser XML valida declarações perigosas (DOCTYPE, ENTITY) ANTES do parse e está configurado para desabilitar entidades externas, impedindo XXE e SSRF? (ver `05-xxe-xml-externo/`)

- [ ] **06-listagem-diretorio** — Listagem de diretórios está desabilitada; diretórios sem index retornam 403 Forbidden em vez de listar arquivos, impedindo descoberta de arquivos sensíveis? (ver `06-listagem-diretorio/`)

## Como Usar Este Material

1. Acesse cada subpasta (01 até 06)
2. Estude `vulneravel.php` para entender a falha de segurança
3. Compare com `corrigido.php` para ver a solução apropriada
4. Execute `php teste.php` em cada subpasta para validar o entendimento
5. Use o checklist acima durante suas atividades de segurança

## Referências

- [OWASP Top 10 2025 - A02: Security Misconfiguration](https://owasp.org/Top10/2025/A02_2025-Security_Misconfiguration/)
- [CWE-16: Configuration](https://cwe.mitre.org/data/definitions/16.html)
- [OWASP Security Headers Project](https://secureheaders.com/)
