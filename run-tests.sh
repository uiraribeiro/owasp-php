#!/usr/bin/env bash
# Roda teste.php em todas as pastas de exemplo e mostra um resumo geral.
# Uso: ./run-tests.sh

set -uo pipefail
cd "$(dirname "$0")"

total=0
ok=0
falhas=()

for teste in */*/teste.php; do
    [ -f "$teste" ] || continue
    pasta=$(dirname "$teste")
    total=$((total + 1))
    echo "==> $pasta"
    if php "$teste"; then
        ok=$((ok + 1))
    else
        falhas+=("$pasta")
    fi
    echo
done

echo "================================================"
echo "RESUMO GERAL: $ok/$total pastas passaram"
if [ ${#falhas[@]} -gt 0 ]; then
    echo "Pastas com falha:"
    for f in "${falhas[@]}"; do
        echo "  - $f"
    done
    exit 1
fi
exit 0
