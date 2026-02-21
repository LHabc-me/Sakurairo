#!/usr/bin/env python3
import re
from pathlib import Path
from typing import Any, List, Tuple, Dict

ROOT = Path(__file__).resolve().parents[1]
THEME_OPTIONS = ROOT / 'opt/options/theme-options.php'
CUSTOMIZER = ROOT / 'inc/customizer.php'
DOC_INV = ROOT / 'docs/PHASE2_FIELD_INVENTORY.md'
DOC_MAP = ROOT / 'docs/PHASE2_FIELD_MAPPING.md'


class Parser:
    def __init__(self, s: str):
        self.s = s
        self.n = len(s)
        self.i = 0

    def skip_ws(self):
        while self.i < self.n:
            if self.s[self.i].isspace():
                self.i += 1
                continue
            if self.s.startswith('//', self.i):
                j = self.s.find('\n', self.i)
                self.i = self.n if j == -1 else j + 1
                continue
            if self.s.startswith('/*', self.i):
                j = self.s.find('*/', self.i + 2)
                self.i = self.n if j == -1 else j + 2
                continue
            break

    def startswith(self, t: str):
        return self.s.startswith(t, self.i)

    def parse_string(self):
        q = self.s[self.i]
        self.i += 1
        out = []
        while self.i < self.n:
            c = self.s[self.i]
            if c == '\\':
                if self.i + 1 < self.n:
                    out.append(self.s[self.i:self.i+2])
                    self.i += 2
                    continue
            if c == q:
                self.i += 1
                break
            out.append(c)
            self.i += 1
        return ''.join(out)

    def parse_bare_expr(self, stop_chars=(',', ')', ']')):
        start = self.i
        depth_p = depth_b = depth_c = 0
        in_str = None
        while self.i < self.n:
            c = self.s[self.i]
            if in_str:
                if c == '\\':
                    self.i += 2
                    continue
                if c == in_str:
                    in_str = None
                self.i += 1
                continue
            if c in ('"', "'"):
                in_str = c
                self.i += 1
                continue
            if c == '(':
                depth_p += 1
            elif c == ')':
                if depth_p == 0 and depth_b == 0 and depth_c == 0 and ')' in stop_chars:
                    break
                depth_p = max(0, depth_p - 1)
            elif c == '[':
                depth_b += 1
            elif c == ']':
                if depth_p == 0 and depth_b == 0 and depth_c == 0 and ']' in stop_chars:
                    break
                depth_b = max(0, depth_b - 1)
            elif c == '{':
                depth_c += 1
            elif c == '}':
                depth_c = max(0, depth_c - 1)
            elif c == ',' and depth_p == depth_b == depth_c == 0 and ',' in stop_chars:
                break
            self.i += 1
        return self.s[start:self.i].strip()

    def parse_value(self):
        self.skip_ws()
        if self.i >= self.n:
            return None
        if self.startswith('array('):
            self.i += len('array(')
            return self.parse_array(')')
        if self.s[self.i] == '[':
            self.i += 1
            return self.parse_array(']')
        if self.s[self.i] in ('"', "'"):
            return self.parse_string()
        expr = self.parse_bare_expr()
        if re.fullmatch(r'-?\d+', expr or ''):
            return int(expr)
        if re.fullmatch(r'-?\d+\.\d+', expr or ''):
            return float(expr)
        if expr.lower() == 'true':
            return True
        if expr.lower() == 'false':
            return False
        return expr

    def parse_array(self, closer: str):
        items = []
        keyed = False
        while self.i < self.n:
            self.skip_ws()
            if self.i < self.n and self.s[self.i] == closer:
                self.i += 1
                break
            k_or_v = self.parse_value()
            self.skip_ws()
            if self.startswith('=>'):
                keyed = True
                self.i += 2
                v = self.parse_value()
                items.append((k_or_v, v))
            else:
                items.append(k_or_v)
            self.skip_ws()
            if self.i < self.n and self.s[self.i] == ',':
                self.i += 1
                continue
            if self.i < self.n and self.s[self.i] == closer:
                self.i += 1
                break
        if keyed:
            d = {}
            for kv in items:
                if isinstance(kv, tuple) and len(kv) == 2:
                    d[str(kv[0])] = kv[1]
            return d
        return items


def parse_sections_from_create_section(text: str) -> List[Dict[str, Any]]:
    sections = []
    idx = 0
    while True:
        m = re.search(r'Shinonomeiro_CSF::createSection\s*\(', text[idx:])
        if not m:
            break
        start = idx + m.end()
        p = Parser(text)
        p.i = start
        _first = p.parse_value()  # $prefix
        p.skip_ws()
        if p.i < p.n and p.s[p.i] == ',':
            p.i += 1
        arr = p.parse_value()
        if isinstance(arr, dict):
            sections.append(arr)
        idx = p.i
    return sections


def parse_customizer_sections(text: str):
    m = re.search(r'\$sections\s*=\s*\[', text)
    if not m:
        return []
    p = Parser(text)
    p.i = m.end() - 1
    return p.parse_value()


def walk_csf_fields(section: Dict[str, Any], section_name: str):
    out = []
    fields = section.get('fields', [])
    if not isinstance(fields, list):
        return out

    def rec(field_list, parent=''):
        if not isinstance(field_list, list):
            return
        for f in field_list:
            if not isinstance(f, dict):
                continue
            key = str(f.get('id', ''))
            if key:
                full = f"{parent}.{key}" if parent else key
                out.append({
                    'section': section_name,
                    'key': full,
                    'type': str(f.get('type', '')),
                    'title': str(f.get('title', '')),
                    'default': str(f.get('default', '')),
                    'dependency': str(f.get('dependency', '')),
                })
                child_parent = full
            else:
                child_parent = parent
            if 'fields' in f:
                rec(f.get('fields'), child_parent)

    rec(fields)
    return out


def build():
    t_text = THEME_OPTIONS.read_text(encoding='utf-8', errors='ignore')
    c_text = CUSTOMIZER.read_text(encoding='utf-8', errors='ignore')

    csf_sections = parse_sections_from_create_section(t_text)
    csf_rows = []
    for s in csf_sections:
        sec = str(s.get('title', s.get('name', s.get('id', s.get('parent', '')))))
        csf_rows.extend(walk_csf_fields(s, sec))

    customizer_sections = parse_customizer_sections(c_text)
    cust_rows = []
    section_panel = {}
    if isinstance(customizer_sections, list):
        for sec in customizer_sections:
            if not isinstance(sec, dict):
                continue
            sid = str(sec.get('id', ''))
            section_panel[sid] = str(sec.get('panel', ''))
            fields = sec.get('fields', [])
            if not isinstance(fields, list):
                continue
            for f in fields:
                if not isinstance(f, dict):
                    continue
                setting_id = str(f.get('settings', ''))
                if not setting_id:
                    continue
                cust_rows.append({
                    'setting_id': setting_id,
                    'sanitize': str(f.get('sanitize_callback', '')),
                    'section': sid,
                    'panel': str(sec.get('panel', '')),
                    'iro_key': str(f.get('iro_key', '')),
                    'iro_subkey': str(f.get('iro_subkey', '')),
                    'type': str(f.get('type', '')),
                })

    # Inventory doc
    lines = [
        '# PHASE2 Field Inventory',
        '',
        'Generated by `tools/export-phase2-inventory.py`.',
        '',
        f'- CSF sections parsed: **{len(csf_sections)}**',
        f'- CSF fields parsed (with key/id): **{len(csf_rows)}**',
        f'- Customizer settings parsed: **{len(cust_rows)}**',
        '',
        '## 1) CSF Sections / Fields (opt/options/theme-options.php)',
        '',
        '| # | section | key | type | title | default | dependency |',
        '|---|---|---|---|---|---|---|',
    ]
    for i, r in enumerate(csf_rows, 1):
        vals = [str(i), r['section'], r['key'], r['type'], r['title'], r['default'], r['dependency']]
        vals = [v.replace('\n', ' ').replace('|', '\\|')[:180] for v in vals]
        lines.append('| ' + ' | '.join(vals) + ' |')

    lines += [
        '',
        '## 2) Existing Customizer Settings / Controls (inc/customizer.php)',
        '',
        '| # | setting_id | sanitize | section | panel | type | iro_key | iro_subkey |',
        '|---|---|---|---|---|---|---|---|',
    ]
    for i, r in enumerate(cust_rows, 1):
        vals = [str(i), r['setting_id'], r['sanitize'], r['section'], r['panel'], r['type'], r['iro_key'], r['iro_subkey']]
        vals = [v.replace('\n', ' ').replace('|', '\\|')[:180] for v in vals]
        lines.append('| ' + ' | '.join(vals) + ' |')

    DOC_INV.write_text('\n'.join(lines) + '\n', encoding='utf-8')

    # mapping doc
    map_by_old = {}
    for r in cust_rows:
        k = r['iro_key']
        if not k:
            continue
        map_by_old.setdefault(k, []).append(r)

    mapping_lines = [
        '# PHASE2 Field Mapping',
        '',
        'Generated by `tools/export-phase2-inventory.py` as Phase2 M1 baseline mapping.',
        '',
        '| # | old_key (CSF) | customizer setting_id | target panel/section | migration_batch | note |',
        '|---|---|---|---|---|---|',
    ]

    seen = set()
    idx = 1
    for r in csf_rows:
        old_key = r['key'].split('.')[0]
        if old_key in seen:
            continue
        seen.add(old_key)
        hits = map_by_old.get(old_key, [])
        if hits:
            for h in hits:
                panel_section = f"{h['panel']}/{h['section']}"
                mapping_lines.append(f"| {idx} | {old_key} | {h['setting_id']} | {panel_section} | M1-existing | via iro_key |")
                idx += 1
        else:
            mapping_lines.append(f"| {idx} | {old_key} |  | TBD/TBD | M2+ | not found in current customizer |")
            idx += 1

    DOC_MAP.write_text('\n'.join(mapping_lines) + '\n', encoding='utf-8')

    print(f'Wrote: {DOC_INV}')
    print(f'Wrote: {DOC_MAP}')
    print(f'CSF fields: {len(csf_rows)}, Customizer settings: {len(cust_rows)}')


if __name__ == '__main__':
    build()
