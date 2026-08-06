# Checklist de Segurança: OWASP A01:2025 - Controle de Acesso Quebrado

Este checklist fornece diretrizes práticas para verificar vulnerabilidades de controle de acesso em suas aplicações PHP. Consulte a [documentação oficial do OWASP A01:2025](https://owasp.org/Top10/2025/A01_2025-Broken_Access_Control/) para mais informações detalhadas.

## Itens de Verificação

- [ ] Toda operação de exclusão, modificação ou acesso a dados verifica explicitamente a role/permissão do usuário no servidor, não apenas no frontend? (ver `01-violacao-privilegio-minimo`)

- [ ] Todos os parâmetros críticos que afetam preços, valores monetários ou quantidades vêm de um catálogo/banco de dados do servidor, nunca sendo aceitos diretamente do cliente? (ver `02-bypass-manipulacao-parametros`)

- [ ] Ao retornar um recurso (como um pedido ou documento), o código verifica se o usuário logado é realmente o proprietário daquele recurso? (ver `03-idor`)

- [ ] As rotas/endpoints que modificam estado (DELETE, PUT, POST) exigem não apenas autenticação, mas também autorização baseada em role/grupo do usuário? (ver `04-falta-controle-api`)

- [ ] A role/privilégio do usuário vem SEMPRE do banco de dados/sessão confiável do servidor, nunca de campos enviados pelo cliente no login ou requisições? (ver `05-escalacao-privilegio`)

- [ ] Se JWTs ou tokens são usados, a assinatura é sempre validada usando a chave secreta do servidor antes de confiar nos claims? (ver `06-manipulacao-jwt`)

- [ ] Os headers CORS (Access-Control-Allow-Origin, Access-Control-Allow-Credentials) só são liberados para uma lista explícita de domínios confiáveis? (ver `07-cors-mal-configurado`)

- [ ] Rotas administrativas ou sensíveis verificam autenticação e autorização no servidor, não dependendo apenas de "segurança por obscuridade"? (ver `08-forced-browsing`)

- [ ] Todas as validações críticas (limites, saldos, quotas) são revalidadas no servidor, independentemente de flags ou sinais enviados pelo cliente? (ver `09-client-side-enforcement`)
