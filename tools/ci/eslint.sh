#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

TARGETS=(js/nav.js js/page.js)

echo "[CI:eslint] targets: ${TARGETS[*]}"

if [[ "${CI:-}" == "true" ]]; then
  npx --yes eslint@8.57.1 \
    --no-eslintrc \
    --env browser \
    --env es2021 \
    --parser-options=ecmaVersion:2022,sourceType:script \
    "${TARGETS[@]}"
  echo "[CI:eslint] OK (eslint@8.57.1)"
  exit 0
fi

if command -v eslint >/dev/null 2>&1; then
  eslint \
    --no-eslintrc \
    --env browser \
    --env es2021 \
    --parser-options=ecmaVersion:2022,sourceType:script \
    "${TARGETS[@]}"
  echo "[CI:eslint] OK (local eslint)"
  exit 0
fi

echo "[CI:eslint] local eslint unavailable; fallback to node --check"
for file in "${TARGETS[@]}"; do
  node --check "$file"
done

echo "[CI:eslint] Fallback OK (node --check)"
