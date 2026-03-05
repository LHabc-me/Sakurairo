#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

log() {
  echo "[CI:php-lint] $*"
}

log "Scanning PHP files"
if ! command -v php >/dev/null 2>&1; then
  log "php binary not found locally; skip (CI workflow provides PHP)"
  exit 0
fi

if command -v rg >/dev/null 2>&1; then
  mapfile -t php_files < <(rg --files -g '*.php' -g '!inc/kirki/**')
else
  mapfile -t php_files < <(find . -type f -name '*.php' -not -path './inc/kirki/*' | sed 's|^\./||' | sort)
fi

if [[ ${#php_files[@]} -eq 0 ]]; then
  log "No PHP files found"
  exit 0
fi

pass_count=0
fail_count=0
declare -a failed_files=()

for file in "${php_files[@]}"; do
  if lint_output="$(php -l "$file" 2>&1)"; then
    pass_count=$((pass_count + 1))
    continue
  fi

  fail_count=$((fail_count + 1))
  failed_files+=("$file")
  line_hint="$(printf '%s\n' "$lint_output" | grep -Eo 'on line [0-9]+' | head -n1 || true)"
  echo "[CI:php-lint] FAIL: $file ${line_hint:+($line_hint)}" >&2
  printf '%s\n' "$lint_output" >&2
done

if [[ "$fail_count" -gt 0 ]]; then
  log "Summary: pass=$pass_count fail=$fail_count total=${#php_files[@]}"
  log "Failed files: ${failed_files[*]}"
  log "Next step: run 'php -l <file>' for each failed file and fix syntax errors before push."
  exit 1
fi

log "OK (total=${#php_files[@]})"
