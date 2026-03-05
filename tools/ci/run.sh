#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

CI_ENABLE_SECURITY_SMOKE="${CI_ENABLE_SECURITY_SMOKE:-false}"

log() {
  echo "[CI:run] $*"
}

suggest_for_step() {
  local step="$1"
  case "$step" in
    "PHP lint")
      log "Next step: run 'bash tools/ci/php-lint.sh' locally and fix the reported file/line syntax errors."
      ;;
    "JS lint")
      log "Next step: run 'bash tools/ci/eslint.sh' and inspect the file-level parser output."
      ;;
    "Smoke")
      log "Next step: run 'bash tools/ci/smoke.sh' and address missing files/markers first."
      ;;
    "Security AJAX smoke")
      log "Next step: verify AJAX_URL/nonce env vars and local WordPress test endpoint before retry."
      ;;
  esac
}

run_step() {
  local step="$1"
  shift

  log "---- $step ----"
  if "$@"; then
    log "PASS: $step"
    return 0
  fi

  local rc=$?
  log "FAIL: $step (exit=$rc)"
  suggest_for_step "$step"
  return "$rc"
}

run_step "PHP lint" bash tools/ci/php-lint.sh
run_step "JS lint" bash tools/ci/eslint.sh
run_step "Smoke" bash tools/ci/smoke.sh

if [[ "$CI_ENABLE_SECURITY_SMOKE" == "true" ]]; then
  run_step "Security AJAX smoke" bash tools/security-ajax-smoke.sh
else
  log "SKIP: Security AJAX smoke (set CI_ENABLE_SECURITY_SMOKE=true to enable)"
  log "Hint: optionally provide AJAX_URL, LOW_PRIV_COOKIE, LOW_PRIV_NONCE, LINK_NONCE for richer coverage."
fi

log "All enabled checks passed."
