#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
THEME_DIR_NAME="Shinonomeiro"
OUT_DIR="${ROOT_DIR}/dist"
OUT_ZIP="${OUT_DIR}/${THEME_DIR_NAME}.zip"

mkdir -p "${OUT_DIR}"
rm -f "${OUT_ZIP}"

export ROOT_DIR OUT_ZIP THEME_DIR_NAME

python3 - <<'PY'
import os
import zipfile
from pathlib import Path

root = Path(os.environ['ROOT_DIR'])
out_zip = Path(os.environ['OUT_ZIP'])
theme_dir_name = os.environ['THEME_DIR_NAME']

exclude_dirs = {'.git', '.github', 'dist', 'node_modules'}
exclude_suffix = {'.log'}

with zipfile.ZipFile(out_zip, 'w', zipfile.ZIP_DEFLATED) as zf:
    for p in root.rglob('*'):
        rel = p.relative_to(root)
        parts = set(rel.parts)
        if parts & exclude_dirs:
            continue
        if p.is_file() and p.suffix in exclude_suffix:
            continue
        if p.is_file():
            arcname = Path(theme_dir_name) / rel
            zf.write(p, arcname.as_posix())

print(f'Created: {out_zip}')
PY
