#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

echo "[CI:smoke] checking module files"
test -f functions.php
test -f inc/modules/ajax.php
test -f inc/modules/admin.php

echo "[CI:smoke] checking module loaders"
rg -q "inc/modules/ajax.php" functions.php
rg -q "inc/modules/admin.php" functions.php

echo "[CI:smoke] running frontend stability script"
bash tools/check-pr2-frontend-stability.sh

echo "[CI:smoke] OK"
