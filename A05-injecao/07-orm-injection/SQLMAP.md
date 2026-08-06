# Testando esta pasta com sqlmap

Este exemplo (`RepositorioUsuarios::buscarPorFiltroRaw()` recebendo uma
condição SQL livre) foi testado de verdade com
[sqlmap](https://sqlmap.org) 1.10.8.

## Rodar tudo de uma vez

```bash
./testar-com-sqlmap.sh
```

Sobe um `php -S 127.0.0.1:8900` servindo `endpoint-vulneravel.php` e
`endpoint-corrigido.php`, roda o sqlmap contra os dois, e imprime um resumo
no final.

## Comandos manuais (passo a passo)

1. Suba o servidor:

```bash
php -S 127.0.0.1:8900
```

2. sqlmap contra o endpoint **vulnerável** (parâmetro `nome`, que o
   endpoint usa para montar `"usuario = '{$nome}'"` e repassar direto pro
   `buscarPorFiltroRaw()`):

```bash
sqlmap -u "http://127.0.0.1:8900/endpoint-vulneravel.php?nome=admin" \
  -p nome --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado — injeção confirmada com apenas 5 requisições (é um
contexto de injeção simples, dentro de uma string já entre aspas):

```
Parameter: nome (GET)
    Type: boolean-based blind
    Title: AND boolean-based blind - WHERE or HAVING clause
    Payload: nome=admin' AND 1178=1178-- miLL

    Type: error-based
    Title: SQLite >= 3.9 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (JSON path)
    Payload: nome=admin' AND 2084=JSON_EXTRACT(...)-- vuGk
```

3. sqlmap contra o endpoint **corrigido** (usa `buscarPorNomeUsuario()`,
   que nem aceita condição SQL livre, só um valor via prepared statement):

```bash
sqlmap -u "http://127.0.0.1:8900/endpoint-corrigido.php?nome=admin" \
  -p nome --batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session
```

Resultado esperado: `does not seem to be injectable` / `do not appear to be
injectable` — nenhuma injeção encontrada.

## Sobre os arquivos `endpoint-*.php`

Assim como em `01-sql-injection/`, o `teste.php` rápido chama as funções
PHP direto (`buscarPorFiltroRaw()`/`buscarPorNomeUsuario()`), mas sqlmap
precisa de um alvo HTTP real — por isso os `endpoint-*.php` simulam uma
tela de "busca de usuário" onde o parâmetro `nome` (GET) alimenta a
consulta.

Veja `A05-injecao/01-sql-injection/SQLMAP.md` para a explicação de cada
opção usada no comando sqlmap (`--batch`, `--level`, `--risk`, `--technique`,
`--dbms`, `--flush-session`).
