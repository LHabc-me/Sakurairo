#!/usr/bin/env python3
from pathlib import Path
import re

root = Path(__file__).resolve().parents[1]
mapping = root / 'docs' / 'PHASE2_FIELD_MAPPING.md'
text = mapping.read_text(encoding='utf-8')

rows = [ln for ln in text.splitlines() if ln.startswith('|') and re.match(r'^\|\s*\d+\s*\|', ln)]
remaining = [ln for ln in rows if 'TBD/TBD' in ln or 'M2+' in ln]

print(f'total rows: {len(rows)}')
print(f'remaining unmigrated rows: {len(remaining)}')
if remaining:
    for ln in remaining[:20]:
        print(ln)
    raise SystemExit(1)
print('Phase2 coverage check passed.')
