#!/usr/bin/env bash
# Export recepsionis_db (struktur + data). MAMP: port MySQL sering 8889.
# Usage: ./scripts/export_mysql_recepsionis.sh [PORT]

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PORT="${1:-8889}"
OUT="${ROOT}/exports/recepsionis_dump_$(date +%Y%m%d_%H%M%S).sql"

MYSQldump=""
for c in \
  "/Applications/MAMP/Library/bin/mysql80/bin/mysqldump" \
  "/Applications/MAMP/Library/bin/mysql57/bin/mysqldump"; do
  if [[ -x "$c" ]]; then MYSQldump="$c"; break; fi
done
[[ -n "$MYSQldump" ]] || { echo "mysqldump tidak ditemukan."; exit 1; }

mkdir -p "${ROOT}/exports"
# Ganti collation MySQL 8 → unicode (MariaDB / MySQL lama tidak punya utf8mb4_0900_ai_ci)
"$MYSQldump" -h127.0.0.1 -P"$PORT" -uroot -proot \
  --single-transaction --routines --triggers --add-drop-table \
  recepsionis_db | sed 's/utf8mb4_0900_ai_ci/utf8mb4_unicode_ci/g' > "$OUT"
echo "OK: $OUT"
