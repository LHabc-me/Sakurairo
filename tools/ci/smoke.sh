#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

echo "[CI:smoke] checking module files"
test -f functions.php
test -f inc/modules/http.php
test -f inc/modules/ajax.php
test -f inc/modules/admin.php

echo "[CI:smoke] checking module loaders"
if command -v rg >/dev/null 2>&1; then
  rg -q "inc/modules/http.php" functions.php
  rg -q "inc/modules/ajax.php" functions.php
  rg -q "inc/modules/admin.php" functions.php
else
  grep -q "inc/modules/http.php" functions.php
  grep -q "inc/modules/ajax.php" functions.php
  grep -q "inc/modules/admin.php" functions.php
fi

echo "[CI:smoke] running frontend stability script"
bash tools/check-pr2-frontend-stability.sh

echo "[CI:smoke] OK"
