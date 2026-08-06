# A07:2025 - Authentication Failures (Falhas de Autenticação)

Referência: [OWASP Top 10 2025 - A07:2025 Authentication Failures](https://owasp.org/Top10/2025/A07_2025-Authentication_Failures/)

Falhas de autenticação ocorrem quando não há proteção adequada contra ataques que exploram credenciais, sessões e tokens. Esta checklist ajuda a identificar e prevenir vulnerabilidades comuns.

## Checklist de Verificação

- [ ] **Ataques Automatizados e Limite de Tentativas**: Existe implementação de rate limiting e bloqueio temporário de conta após múltiplas tentativas falhadas de login para impedir credential stuffing e password spray? (ver `01-ataques-automatizados-sem-limite/`)

- [ ] **Validação Rigorosa de Senhas**: As novas senhas são validadas com requisitos de complexidade (mínimo 12 caracteres, exclusão de senhas fracas conhecidas, rejeição de senhas baseadas no nome de usuário)? (ver `02-senhas-fracas-permitidas/`)

- [ ] **Autenticação Multifatorial (MFA)**: Quando MFA está habilitado na conta, o segundo fator de autenticação é obrigatoriamente verificado, impossibilitando login apenas com a senha? (ver `03-mfa-ausente-ou-ineficaz/`)

- [ ] **Geração de Novo ID de Sessão no Login**: Após um login bem-sucedido, um novo id de sessão aleatório é gerado, invalidando qualquer id pré-existente para prevenir session fixation? (ver `04-fixacao-sessao/`)

- [ ] **Expiração de Sessão em Múltiplos Níveis**: As sessões expiram tanto por tempo máximo absoluto (ex: 8 horas desde criação) quanto por inatividade (ex: 30 minutos sem acesso)? (ver `05-expiracao-sessao-insuficiente/`)

- [ ] **ID de Sessão em Cookies Protegidos**: Os ids de sessão são transmitidos apenas em cookies com flags `httponly=true`, `secure=true` e `samesite=Strict`, nunca em URLs ou parâmetros GET? (ver `06-id-sessao-em-local-inseguro/`)

- [ ] **Autenticação Centralizada Para Todos os Caminhos**: Existe uma verificação centralizada de autenticação que se aplica a TODAS as rotas e endpoints, impedindo bypasses através de caminhos alternativos ou legados? (ver `07-bypass-autenticacao-caminho-alternativo/`)

- [ ] **Validação de Token Uma Única Vez (No Replay)**: Tokens sensíveis (redefinição de senha, confirmação de email, etc) podem ser usados apenas uma vez e são marcados como consumidos após o primeiro uso? (ver `08-replay-captura-token/`)
