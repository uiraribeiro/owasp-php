# A03:2025 - Software Supply Chain Failures (Falhas na Cadeia de Suprimentos de Software)

## Sobre esta categoria

A03:2025 - Software Supply Chain Failures aborda vulnerabilidades relacionadas à gestão de dependências de software e ao build/deployment chain. Diferente de outras categorias que focam em falhas de código, esta categoria trata sobre COMO e DE ONDE os componentes são obtidos, atualizados e executados durante a instalação.

Para mais informações, consulte: https://owasp.org/Top10/2025/A03_2025-Software_Supply_Chain_Failures/

## Nota importante

A maioria das verificações nesta categoria **não é sobre código PHP**, mas sobre **configuração e gestão de dependências** via Composer.

As verificações envolvem:
- `composer audit` — verifica dependências contra base de dados de CVE
- `composer outdated` — lista pacotes desatualizados
- `composer show` — inspeciona dependências instaladas
- Revisão manual de `composer.json` e `composer.lock`
- Ferramentas externas: GitHub Dependabot, Snyk, FOSSA
- Analisadores de repositório Git (para detect artefatos suspeitos)

## Checklist de Segurança

- [ ] **Versões vulneráveis conhecidas**: Todas as dependências declaradas em `composer.json` estão em versões sem vulnerabilidades conhecidas? Execute `composer audit` regularmente para verificar contra banco de dados de CVE. **(ver `01-versao-vulneravel-conhecida/`)**

- [ ] **Componentes abandonados**: Nenhuma dependência é de pacotes descontinuados ou não mantidos há meses/anos? Verifique no Packagist o status de manutenção (último release, problemas abertos). Se encontrado, busque alternativa ativamente mantida. **(ver `02-componente-nao-mantido/`)**

- [ ] **Fontes confiáveis**: Todas as dependências vêm de repositórios oficiais e verificados (Packagist.org via HTTPS)? Evite URLs HTTP e repositórios privados não autenticados. Se usar repositórios privados, use Git+SSH com autenticação. **(ver `03-fonte-nao-confiavel/`)**

- [ ] **Scripts de instalação controlados**: A configuração `allow-plugins` em `composer.json` usa uma allow-list explícita (array) de plugins confiáveis, nunca `true` (booleano global)? Isso previne que plugins malicioso rodem código arbitrário durante `composer install`. **(ver `04-scripts-instalacao-nao-confiaveis/`)**

- [ ] **Versões fixadas**: As constraints de versão em `composer.json` são específicas e semânticas (ex: `^1.5`, `~2.1`)? Evite `*` ou `dev-*` que permitem qualquer versão. Use `composer.lock` em produção para garantir reproducibilidade. **(ver `05-dependencia-sem-versao-fixada/`)**

- [ ] **Funções obsoletas/perigosas**: O código PHP não usa `extract()` sem controle, `parse_str()` com 1 argumento, ou outras funções marcadas como perigosas (CWE-446, CWE-447)? Isso previne Variable Injection e Variable Overwrite. **(ver `06-funcao-obsoleta/`)**

## Como usar este material didático

1. **Examine os exemplos**: Cada subpasta contém um ou dois exemplos fictícios (composer.json ou PHP) com a vulnerabilidade e a versão corrigida.

2. **Rode os testes**: Execute `php teste.php` em cada subpasta para ver as verificações passando.

3. **Entenda a lógica**: Os testes usam "bases de dados" fictícias e hardcoded (listas de pacotes abandonados, versões vulneráveis) — em produção, essas verificações usariam APIs reais (Packagist, GitHub, Snyk, etc).

4. **Aplique em seu projeto**: Use os conceitos para auditar seu próprio `composer.json` e dependências.
