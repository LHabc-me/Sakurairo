#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[PR2] Deprecated entrypoint, delegating to tools/ci/smoke.sh"
exec bash tools/ci/smoke.sh
