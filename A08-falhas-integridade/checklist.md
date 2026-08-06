# A08:2025 - Software or Data Integrity Failures

Material educacional sobre falhas de integridade de software ou dados, conforme a [Categoria A08 do OWASP Top 10 2025](https://owasp.org/Top10/2025/A08_2025-Software_or_Data_Integrity_Failures/).

A integridade de dados e software é fundamental para a segurança. Se um atacante conseguir modificar dados em trânsito ou em repouso, ou substituir código/bibliotecas legítimas por versões comprometidas, todo o sistema pode ser comprometido.

## Checklist de Verificação de Código

Para proteger sua aplicação contra falhas de integridade, verifique:

- [ ] **Desserialização Insegura (CWE-502)**: A aplicação usa `unserialize()` em dados vindo de fora (cookies, uploads, APIs externas)? Se sim, deve estar usando `json_decode()` em lugar disso. Nunca desserialize dados não confiáveis diretamente. (Ver `01-deserializacao-insegura/`)

- [ ] **Atualização Sem Assinatura Criptográfica (CWE-494)**: Ao baixar e aplicar atualizações de software/firmware, a aplicação verifica a assinatura digital com `openssl_verify()` ou similar? Se não, um atacante MITM pode substituir a atualização por uma versão maliciosa. (Ver `02-atualizacao-nao-assinada/`)

- [ ] **Integridade de Bibliotecas Externas (CWE-353)**: Ao incluir bibliotecas/assets de CDN, verifica-se o hash SHA256 com `hash_equals()`? Se não, um CDN comprometido ou MITM pode servir código malicioso. (Ver `03-integridade-biblioteca-externa/`)

- [ ] **Subresource Integrity (SRI) para Scripts Externos (CWE-829/830)**: Tags `<script>` e `<link>` para recursos externos possuem o atributo `integrity` com um hash válido? Se não, o navegador não protege contra CDN comprometidos. (Ver `04-inclusao-funcionalidade-nao-confiavel/`)

- [ ] **Decisões de Segurança Baseadas em Cookies (CWE-565/784)**: A aplicação confia em valores de cookies do cliente (como `role: admin`) para tomar decisões de segurança (controle de acesso, permissões)? Se sim, isso é escalação trivial de privilégio. Armazene roles e permissões SEMPRE no servidor, não no cliente. (Ver `05-cookie-em-decisao-de-seguranca/`)

- [ ] **Path Traversal / Inclusão de Caminho Não Controlado (CWE-829)**: Ao montar caminhos de arquivo dinamicamente, usa-se uma allow-list (whitelist) de caracteres seguros com regex? Se não, um atacante pode usar `../` para acessar arquivos arbitrários do servidor. (Ver `06-inclusao-caminho-nao-controlado/`)
