#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

echo "[CI:php-lint] scanning PHP files"
if ! command -v php >/dev/null 2>&1; then
  echo "[CI:php-lint] php binary not found locally; skip (CI workflow provides PHP)"
  exit 0
fi

mapfile -t php_files < <(rg --files -g '*.php' -g '!inc/kirki/**')

if [[ ${#php_files[@]} -eq 0 ]]; then
  echo "No PHP files found"
  exit 0
fi

for file in "${php_files[@]}"; do
  php -l "$file" >/dev/null
done

echo "[CI:php-lint] OK (${#php_files[@]} files)"
