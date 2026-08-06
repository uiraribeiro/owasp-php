# A06:2025 - Insecure Design (Design Inseguro)

Confira a vulnerabilidade oficial em: https://owasp.org/Top10/2025/A06_2025-Insecure_Design/

## O que é Insecure Design?

**A06 é diferente de todas as outras categorias do OWASP Top 10.**

Enquanto A01 até A05, A07 até A10 tratam de **BUGS DE IMPLEMENTAÇÃO** (código escrito errado), **A06 trata de FALHAS DE ARQUITETURA E DESIGN** — decisões tomadas *antes* de escrever qualquer linha de código.

A06 não é sobre validar mal uma entrada ou não escapar HTML. É sobre a decisão arquitetural de **como os dados fluem, quem pode acessá-los, quais limites existem, e se há proteção contra abuso**.

### Exemplos de Insecure Design:
- Permitir que senhas sejam reversíveis (design incorreto: "vamos poder recuperar se o usuário esquecer")
- Distribuir permissões sem seguir o princípio do menor privilégio (um 'editor' nunca deveria deletar usuários)
- Não isolar dados confiáveis de input do atacante (misturar ambos no mesmo array)
- Lógica de negócio sem limites (comprar 10.000 ingressos de uma vez sem pagar)
- Nenhuma proteção contra bots/scalpers

**Threat Modeling, Design Review e Security Patterns** são ferramentas essenciais para evitar A06. Não há "fix" de código que resolva uma falha de design; é preciso **redesenhar**.

---

## Checklist de Design Seguro

Use esta checklist durante **code review de arquitetura** e **design review** antes de implementar features:

- [ ] **Armazenamento de Credenciais**: Senhas são armazenadas de forma que NUNCA seja possível recuperar o valor original, nem por administradores do sistema? Usa-se hash irreversível (bcrypt, argon2) em vez de criptografia reversível?
  → Ver: `01-armazenamento-credenciais-desprotegido/`

- [ ] **Privilégios por Role**: Cada role (admin, editor, leitor) tem APENAS as permissões que precisa? Foi aplicado o princípio do menor privilégio? Permissões sensíveis (deletar usuários, gerenciar pagamentos) existem APENAS em roles autorizados via allow-list explícita?
  → Ver: `02-gerenciamento-privilegio-impropio/`

- [ ] **Validação de Uploads**: Existe whitelist de extensões permitidas? Os magic bytes (assinatura binária) do arquivo são verificados para confirmar que o conteúdo real bate com a extensão? Um arquivo .php disfarçado de .jpg seria rejeitado?
  → Ver: `03-upload-arquivo-perigoso/`

- [ ] **Isolamento de Fronteiras de Confiança**: Dados confiáveis (do servidor, autenticados) estão claramente separados de input do usuário? Input malicioso pode sobrescrever campos críticos como 'isAdmin' ou 'usuário_id'?
  → Ver: `04-fronteira-confianca-violada/`

- [ ] **Recuperação de Credencial**: A recuperação de senha exige MÚLTIPLOS FATORES (ex: pergunta de segurança + token de e-mail)? Uma única resposta de pergunta (facilmente descoberta via engenharia social) é suficiente?
  → Ver: `05-recuperacao-credencial-fraca/`

- [ ] **Limites na Lógica de Negócio**: Existem limites máximos de quantidade por requisição? Compras grandes exigem depósito/pagamento antecipado? Um atacante conseguiria reservar/comprar todo o estoque de uma vez?
  → Ver: `06-logica-negocio-sem-limite/`

- [ ] **Proteção Anti-Bot**: Há rate limiting e detecção de padrão de bot? Uma aplicação monitora frequência de requisições do mesmo cliente? Bots que disparam centenas de requisições por segundo são bloqueados?
  → Ver: `07-falta-protecao-anti-bot/`

---

## Como Usar Este Material

1. **Para aprender**: Execute `php teste.php` em cada subpasta para ver os testes rodando.
2. **Para ensinar**: Leia os comentários de cada `vulneravel.php` para entender a falha de design; compare com `corrigido.php`.
3. **Para fazer code review**: Use a checklist acima ao revisar arquitetura ou design de features.
4. **Para aplicar**: Pergunte-se "quais falhas de design existem neste projeto?" antes de revisar bugs de implementação.

---

## Referências

- OWASP Top 10 2025 - A06: https://owasp.org/Top10/2025/A06_2025-Insecure_Design/
- OWASP Design Principles: https://cheatsheetseries.owasp.org/cheatsheets/Secure_SDLC_Cheat_Sheet.html
- Threat Modeling: https://cheatsheetseries.owasp.org/cheatsheets/Threat_Modeling_Cheat_Sheet.html
