# A09:2025 - Security Logging and Alerting Failures
## Falhas de Registro e Alerta de Segurança

**Referência oficial:** [OWASP Top 10 2025 - A09:2025](https://owasp.org/Top10/2025/A09_2025-Security_Logging_and_Alerting_Failures/)

Registro e alertas inadequados permitem que atacantes permaneçam despercebidos por meses ou anos. Este checklist ajuda a identificar falhas comuns de logging e monitoramento em aplicações PHP.

## Checklist de Verificação

- [ ] Dados de entrada do usuário são sanitizados (quebras de linha escapadas) antes de serem escritos em arquivos de log? (ver `01-injecao-log/`)
  - **O quê:** Verificar se `\r` e `\n` são escapados para evitar log injection (CWE-117)
  - **Por quê:** Um atacante pode forjar linhas falsas de log que enganam administradores

- [ ] Todos os eventos críticos de segurança são registrados (login falho, elevação de privilégio, exclusão de usuário, mudança de senha)? (ver `02-perda-informacao-relevante/`)
  - **O quê:** Verificar se timestamp, nome de usuário, IP e resultado estão presentes
  - **Por quê:** Sem contexto completo, é impossível investigar e reconstruir uma sequência de eventos

- [ ] Dados sensíveis (senhas, tokens, chaves) não são gravados em logs? (ver `03-dados-sensiveis-no-log/`)
  - **O quê:** Verificar se credenciais são redatadas com `[REDACTED]` ou similar
  - **Por quê:** Logs são frequentemente acessados por múltiplos usuários, administradores, ferramentas de monitoramento e backups

- [ ] Eventos de segurança relevantes são explicitamente listados e monitorizados? (ver `04-logging-insuficiente/`)
  - **O quê:** Verificar se existe uma whitelist de eventos críticos sendo capturada
  - **Por quê:** Violações de dados podem passar despercebidas por anos se eventos críticos não forem registrados

- [ ] Existe um mecanismo de detecção de padrões suspeitos (ex.: força bruta) com alertas automáticos? (ver `05-falta-alerta-anomalia/`)
  - **O quê:** Verificar se múltiplas tentativas de login falhas disparam alertas em tempo real
  - **Por quê:** Sem alertas, o administrador fica cego até que o dano já foi feito

## Resumo das Vulnerabilidades

| Pasta | CWE | Risco | Cenário OWASP |
|-------|-----|-------|---------------|
| 01-injecao-log | CWE-117 | Alto | Forging de linhas de log falsas |
| 02-perda-informacao-relevante | CWE-221/223 | Crítico | Impossibilidade de investigação |
| 03-dados-sensiveis-no-log | CWE-532 | Crítico | Vazamento de credenciais |
| 04-logging-insuficiente | CWE-778 | Crítico | Violações sem detecção por anos |
| 05-falta-alerta-anomalia | CWE-778 | Alto | Ataque em andamento passa despercebido |

## Boas Práticas

1. **Escapar dados de entrada em logs** - Neutralizar caracteres de controle para evitar injeção
2. **Logar contexto completo** - Sempre incluir timestamp, usuário, IP, ação e resultado
3. **Redattar credenciais** - Nunca gravar senhas, tokens ou chaves em logs
4. **Monitorar eventos críticos** - Definir uma whitelist de eventos de segurança e capturá-los todos
5. **Implementar alertas** - Detectar padrões suspeitos (força bruta, privilégios elevados) automaticamente
6. **Enviar logs para servidor centralizado** - Evitar que atacantes que comprometem o servidor deletem evidências locais
7. **Proteger acesso a logs** - Limitar quem pode ler/deletar logs (princípio do menor privilégio)
