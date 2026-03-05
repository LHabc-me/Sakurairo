#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

log() {
  echo "[CI:smoke] $*"
}

fail() {
  echo "[CI:smoke] FAIL: $1" >&2
  if [[ -n "${2:-}" ]]; then
    echo "[CI:smoke] Next step: $2" >&2
  fi
  exit 1
}

search_file() {
  local pattern="$1"
  local file="$2"
  if command -v rg >/dev/null 2>&1; then
    rg -q "$pattern" "$file"
  else
    grep -qE "$pattern" "$file"
  fi
}

require_file() {
  local file="$1"
  [[ -f "$file" ]] || fail "required file missing: $file" "restore file from main or update CI checks if path intentionally changed."
}

require_pattern() {
  local pattern="$1"
  local file="$2"
  local label="$3"
  if ! search_file "$pattern" "$file"; then
    fail "$label not found in $file (pattern: $pattern)" "check recent refactor and update marker/loader references."
  fi
}

log "Checking module files"
require_file "functions.php"
require_file "inc/modules/http.php"
require_file "inc/modules/ajax.php"
require_file "inc/modules/admin.php"

log "Checking module loaders"
require_pattern "inc/modules/http.php" "functions.php" "module loader"
require_pattern "inc/modules/ajax.php" "functions.php" "module loader"
require_pattern "inc/modules/admin.php" "functions.php" "module loader"

log "Checking frontend stability contract"
require_file "js/nav.js"
require_file "js/page.js"
if ! node_output="$(node --check js/nav.js 2>&1)"; then
  printf '%s\n' "$node_output" >&2
  fail "node --check failed for js/nav.js" "run 'node --check js/nav.js' to inspect syntax detail."
fi
if ! node_output="$(node --check js/page.js 2>&1)"; then
  printf '%s\n' "$node_output" >&2
  fail "node --check failed for js/page.js" "run 'node --check js/page.js' to inspect syntax detail."
fi
require_pattern "__iroNavLifecycle|createIroNavLifecycle" "js/nav.js" "nav lifecycle marker"
require_pattern "init\(\) \{" "js/nav.js" "lifecycle init() marker"
require_pattern "destroy\(\) \{" "js/nav.js" "lifecycle destroy() marker"
require_pattern "rebind\(\) \{" "js/nav.js" "lifecycle rebind() marker"

log "OK"
