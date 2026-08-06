# A10:2025 - Mishandling of Exceptional Conditions (Tratamento Inadequado de Condições Excepcionais)

Referência: [OWASP Top 10 2025 - A10](https://owasp.org/Top10/2025/A10_2025-Mishandling_of_Exceptional_Conditions/)

O tratamento inadequado de exceções e condições de erro é uma vulnerabilidade crítica que pode levar à exposição de informações sensíveis, negação de serviço, ou comportamento impredizível da aplicação. Uma exceção não tratada ou um erro silencioso pode deixar o sistema em estado inconsistente ou permitir acesso não autorizado.

## Checklist de Verificação de Segurança

- [ ] **01 - Erro com Dados Sensíveis**: Mensagens de erro devolvidas ao usuário/cliente da API nunca expõem detalhes técnicos internos (nomes de tabela, stack trace, versão de biblioteca, mensagens SQL)? (ver `01-erro-com-dados-sensiveis/`)

- [ ] **02 - Exceção Não Tratada**: Todas as operações que podem lançar exceções estão envolvidas em try/catch ou validação prévia? Exceções nunca escapam não tratadas da função? (ver `02-excecao-nao-tratada/`)

- [ ] **03 - Valor de Retorno Não Verificado**: Valores de retorno de funções críticas (file_put_contents, fopen, mysqli_query, etc) são sempre verificados antes de prosseguir? Falhas silenciosas são detectadas? (ver `03-valor-retorno-nao-verificado/`)

- [ ] **04 - Caso Default Ausente**: Switches e estruturas de controle críticas sempre têm case default explícito? O default é fail-safe (nega acesso/falha segura) e não fail-open (permite por padrão)? (ver `04-caso-default-ausente/`)

- [ ] **05 - Tratamento Genérico de Exceções**: Diferentes tipos de exceção (timeout vs. permissão negada, recurso não encontrado vs. erro de servidor) são capturados e tratados de forma específica, não genérica? Cada tipo tem resposta apropriada? (ver `05-tratamento-generico-excecoes/`)

- [ ] **06 - Falha Não-Segura (Fail-Open)**: Quando um serviço externo ou validação externa falha/retorna null, o comportamento padrão é NEGAR acesso/operação (fail-safe), não permitir (fail-open)? (ver `06-falha-nao-segura-fail-open/`)

- [ ] **07 - Transação Não-Atômica Sem Rollback**: Operações multi-etapa críticas (transferências de dinheiro, atualizações de múltiplos registros) implementam rollback ou restauração de estado se falham no meio? Estado inconsistente é impossível? (ver `07-transacao-nao-atomica-sem-rollback/`)

- [ ] **08 - Dados Sensíveis em Debug**: Modo debug (quando ativado) nunca expõe senhas, tokens, números de cartão de crédito ou chaves de API? Campos sensíveis são redacted/mascarados? (ver `08-dados-sensiveis-em-debug/`)
