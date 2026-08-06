#!/usr/bin/env bash
# Sobe um servidor PHP embutido servindo endpoint-vulneravel.php e
# endpoint-corrigido.php (login via POST), roda o sqlmap contra os dois
# usando --data (em vez de query string na URL) e imprime um resumo.
#
# Requer sqlmap instalado (https://sqlmap.org). Não faz parte do
# ./run-tests.sh rápido do projeto.
#
# Uso: ./testar-com-sqlmap.sh

set -uo pipefail
cd "$(dirname "$0")"

if ! command -v sqlmap &> /dev/null; then
    echo "ERRO: sqlmap nao encontrado. Instale com 'brew install sqlmap' (macOS) ou veja https://sqlmap.org"
    exit 1
fi

PORTA=8901

php -S "127.0.0.1:${PORTA}" > /tmp/servidor-sqlmap-08.log 2>&1 &
PID_SERVIDOR=$!
sleep 1

SQLMAP_OPTS="--batch --level=3 --risk=2 --technique=BE --dbms=SQLite --flush-session"

LOG_VULNERAVEL=$(mktemp)
LOG_CORRIGIDO=$(mktemp)
trap 'kill $PID_SERVIDOR 2>/dev/null; rm -f "$LOG_VULNERAVEL" "$LOG_CORRIGIDO"' EXIT

echo "================================================================"
echo "1) Testando endpoint VULNERAVEL via POST (espera-se: injetavel)"
echo "================================================================"
sqlmap -u "http://127.0.0.1:${PORTA}/endpoint-vulneravel.php" --data="usuario=admin&senha=x" \
    -p usuario ${SQLMAP_OPTS} | tee "$LOG_VULNERAVEL"

echo
echo "================================================================"
echo "2) Testando endpoint CORRIGIDO via POST (espera-se: NAO injetavel)"
echo "================================================================"
sqlmap -u "http://127.0.0.1:${PORTA}/endpoint-corrigido.php" --data="usuario=admin&senha=x" \
    -p usuario ${SQLMAP_OPTS} | tee "$LOG_CORRIGIDO"

if grep -q "is vulnerable" "$LOG_VULNERAVEL"; then
    RESULTADO_VULNERAVEL="INJETAVEL (como esperado)"
else
    RESULTADO_VULNERAVEL="NAO detectado como injetavel (inesperado!)"
fi

if grep -q "do not appear to be injectable" "$LOG_CORRIGIDO"; then
    RESULTADO_CORRIGIDO="NAO injetavel (como esperado)"
else
    RESULTADO_CORRIGIDO="pareceu injetavel (inesperado! revisar corrigido.php)"
fi

echo
echo "================================================================"
echo "RESUMO"
echo "================================================================"
echo "Endpoint vulneravel (POST): ${RESULTADO_VULNERAVEL}"
echo "Endpoint corrigido (POST):  ${RESULTADO_CORRIGIDO}"
echo
echo "Para extrair os dados de verdade, rode manualmente:"
echo "  php -S 127.0.0.1:${PORTA} &"
echo "  sqlmap -u \"http://127.0.0.1:${PORTA}/endpoint-vulneravel.php\" --data=\"usuario=admin&senha=x\" \\"
echo "    -p usuario ${SQLMAP_OPTS} --dump -T usuarios"
