# OWASP A04:2025 - Cryptographic Failures (Falhas Criptográficas)

Material didático em PHP sobre falhas criptográficas segundo a classificação OWASP Top 10 2025.

Referência: [https://owasp.org/Top10/2025/A04_2025-Cryptographic_Failures/](https://owasp.org/Top10/2025/A04_2025-Cryptographic_Failures/)

---

## Checklist de Verificação - Code Review

Use este checklist ao revisar código que lida com dados criptográficos, senhas e transmissão de dados sensíveis:

- [ ] **01-algoritmos-fracos**: O código usa algoritmos modernos e seguros (AES-256, ChaCha20) e NÃO usa algoritmos deprecated (DES, RC4, MD5 para criptografia)? (ver `/01-algoritmos-fracos/`)

- [ ] **02-aleatoriedade-insuficiente**: Tokens, IVs, salts e valores sensíveis são gerados com `random_bytes()` (CSPRNG) e NÃO com `mt_rand()` ou `rand()`? (ver `/02-aleatoriedade-insuficiente/`)

- [ ] **03-gestao-chaves**: Chaves criptográficas e secrets são gerenciados via variáveis de ambiente/vault/secrets manager e NÃO estão hardcoded no código-fonte? (ver `/03-gestao-chaves/`)

- [ ] **04-transporte-texto-claro**: Dados sensíveis (credenciais, tokens, PII) são transmitidos via HTTPS POST no corpo da requisição e NÃO em HTTP ou na query string? (ver `/04-transporte-texto-claro/`)

- [ ] **05-hash-sem-salt**: Senhas são armazenadas com `password_hash()`/bcrypt (com salt e custo adaptativo) e NÃO com `md5()`, `sha1()` ou algoritmos sem salt? (ver `/05-hash-sem-salt/`)

- [ ] **06-iv-problematico**: IVs (Initialization Vectors) são aleatórios, gerados a cada criptografia, e NÃO são fixos/zerados/reutilizados? (ver `/06-iv-problematico/`)

- [ ] **07-certificado-nao-validado**: Requisições HTTPS validam certificados TLS (`SSL_VERIFYPEER => true` e `SSL_VERIFYHOST => 2`) e NÃO desabilitam essa validação? (ver `/07-certificado-nao-validado/`)

- [ ] **08-modo-criptografia-inadequado**: O código usa modos de criptografia seguros (CBC com IV aleatório, GCM, ChaCha20-Poly1305) e NÃO usa ECB ou modos determinísticos? (ver `/08-modo-criptografia-inadequado/`)

---

## Estrutura do Material

Cada subpasta (`01-` até `08-`) contém:

- **vulneravel.php**: Demonstração da falha criptográfica com código vulnerável
- **corrigido.php**: Versão segura do mesmo código
- **teste.php**: Testes automatizados que comprovam a vulnerabilidade e a correção

Execute `php teste.php` em qualquer subpasta para validar os exemplos.

---

## Executar Todos os Testes

```bash
for dir in 01-algoritmos-fracos 02-aleatoriedade-insuficiente 03-gestao-chaves \
          04-transporte-texto-claro 05-hash-sem-salt 06-iv-problematico \
          07-certificado-nao-validado 08-modo-criptografia-inadequado; do
    echo "=== Testando $dir ==="
    php "./$dir/teste.php" || exit 1
done
echo "Todos os testes passaram!"
```

---

## Recursos Adicionais

- [OWASP - Cryptographic Failures](https://owasp.org/Top10/2025/A04_2025-Cryptographic_Failures/)
- [PHP: password_hash() - Manual](https://www.php.net/manual/en/function.password-hash.php)
- [PHP: openssl_encrypt() - Manual](https://www.php.net/manual/en/function.openssl-encrypt.php)
- [PHP: random_bytes() - Manual](https://www.php.net/manual/en/function.random-bytes.php)
- [OWASP - Secure Coding Practices](https://owasp.org/www-project-secure-coding-practices/)
