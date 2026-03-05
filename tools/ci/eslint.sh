#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

TARGETS=(js/nav.js js/page.js)

log() {
  echo "[CI:eslint] $*"
}

fail() {
  echo "[CI:eslint] FAIL: $*" >&2
}

for file in "${TARGETS[@]}"; do
  if [[ ! -f "$file" ]]; then
    fail "target missing: $file"
    log "Next step: restore the file or update TARGETS in tools/ci/eslint.sh."
    exit 1
  fi
done

log "Targets: ${TARGETS[*]}"

if [[ "${CI:-}" == "true" ]]; then
  if npx --yes eslint@8.57.1 \
    --no-eslintrc \
    --env browser \
    --env es2021 \
    --parser-options=ecmaVersion:2022,sourceType:script \
    "${TARGETS[@]}"; then
    log "OK (eslint@8.57.1)"
    exit 0
  fi

  rc=$?
  fail "npx eslint@8.57.1 failed (exit=$rc)"
  log "Next step: inspect output above, then run 'CI=true bash tools/ci/eslint.sh' locally to reproduce."
  exit "$rc"
fi

if command -v eslint >/dev/null 2>&1; then
  if eslint \
    --no-eslintrc \
    --env browser \
    --env es2021 \
    --parser-options=ecmaVersion:2022,sourceType:script \
    "${TARGETS[@]}"; then
    log "OK (local eslint)"
    exit 0
  fi

  rc=$?
  fail "local eslint failed (exit=$rc)"
  log "Next step: fix lint errors above or use node syntax fallback via 'node --check <file>'."
  exit "$rc"
fi

log "Local eslint unavailable; fallback to node --check"
if ! command -v node >/dev/null 2>&1; then
  fail "node binary not found; cannot run fallback syntax check."
  log "Next step: install Node.js or provide eslint in PATH."
  exit 1
fi

node_fail_count=0
for file in "${TARGETS[@]}"; do
  if ! node_output="$(node --check "$file" 2>&1)"; then
    node_fail_count=$((node_fail_count + 1))
    fail "node --check failed for $file"
    printf '%s\n' "$node_output" >&2
  fi
done

if [[ "$node_fail_count" -gt 0 ]]; then
  log "Next step: run 'node --check <file>' for each failed file and fix syntax issues."
  exit 1
fi

log "Fallback OK (node --check)"
