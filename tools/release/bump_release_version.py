from __future__ import annotations

import re
import sys
from pathlib import Path


ROOT_DIR = Path(__file__).resolve().parents[2]
STYLE_FILE = ROOT_DIR / "style.css"
PLUGIN_FILE = ROOT_DIR / "plugins" / "shinonomeiro-headless-bridge" / "shinonomeiro-headless-bridge.php"


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def write_text(path: Path, content: str) -> None:
    path.write_text(content, encoding="utf-8")


def parse_version(version: str) -> tuple[int, int, int]:
    match = re.fullmatch(r"(\d+)\.(\d+)\.(\d+)", version.strip())
    if not match:
        raise SystemExit(f"版本号必须为 X.Y.Z 数字格式: {version}")
    return tuple(int(part) for part in match.groups())


def main() -> int:
    style_content = read_text(STYLE_FILE)
    plugin_content = read_text(PLUGIN_FILE)

    style_match = re.search(r"^Version:\s*(.+)$", style_content, flags=re.MULTILINE)
    if not style_match:
        raise SystemExit("未能读取当前主题版本")

    current_version = style_match.group(1).strip()
    major, minor, patch = parse_version(current_version)
    next_version = f"{major}.{minor}.{patch + 1}"

    updated_style, style_count = re.subn(
        r"^Version:\s*.+$",
        f"Version: {next_version}",
        style_content,
        count=1,
        flags=re.MULTILINE,
    )
    if style_count != 1:
        raise SystemExit("style.css 版本号更新失败")

    updated_plugin, header_count = re.subn(
        r"^ \* Version:\s*.+$",
        f" * Version: {next_version}",
        plugin_content,
        count=1,
        flags=re.MULTILINE,
    )
    updated_plugin, constant_count = re.subn(
        r"^define\('SHINONOMEIRO_HEADLESS_BRIDGE_VERSION', '.*?'\);$",
        f"define('SHINONOMEIRO_HEADLESS_BRIDGE_VERSION', '{next_version}');",
        updated_plugin,
        count=1,
        flags=re.MULTILINE,
    )
    if header_count != 1 or constant_count != 1:
        raise SystemExit("插件版本号更新失败")

    write_text(STYLE_FILE, updated_style)
    write_text(PLUGIN_FILE, updated_plugin)

    print(f"CURRENT_VERSION={current_version}")
    print(f"NEXT_VERSION={next_version}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
