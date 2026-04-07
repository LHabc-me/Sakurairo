from __future__ import annotations

import argparse
import re
import sys
from pathlib import Path


ROOT_DIR = Path(__file__).resolve().parents[2]
STYLE_FILE = ROOT_DIR / "style.css"
PLUGIN_FILE = ROOT_DIR / "plugins" / "shinonomeiro-headless-bridge" / "shinonomeiro-headless-bridge.php"


def read_text(path: Path) -> str:
    return path.read_text(encoding="utf-8")


def match_or_fail(pattern: str, content: str, message: str) -> str:
    match = re.search(pattern, content, flags=re.MULTILINE)
    if not match:
        raise SystemExit(message)
    return match.group(1).strip()


def main() -> int:
    parser = argparse.ArgumentParser(description="校验主题、插件和 tag 版本一致性。")
    parser.add_argument("tag_name", nargs="?", default="", help="可选，Git tag，例如 v1.2.87")
    args = parser.parse_args()

    style_content = read_text(STYLE_FILE)
    plugin_content = read_text(PLUGIN_FILE)

    theme_version = match_or_fail(r"^Version:\s*(.+)$", style_content, "未能从 style.css 读取主题版本")
    plugin_version = match_or_fail(r"^ \* Version:\s*(.+)$", plugin_content, "未能从插件主文件读取插件版本")
    plugin_constant_version = match_or_fail(
        r"^define\('SHINONOMEIRO_HEADLESS_BRIDGE_VERSION', '(.+)'\);$",
        plugin_content,
        "未能从插件常量读取插件版本",
    )

    if theme_version != plugin_version:
        raise SystemExit(f"主题版本({theme_version})与插件头版本({plugin_version})不一致")

    if plugin_version != plugin_constant_version:
        raise SystemExit(f"插件头版本({plugin_version})与插件常量版本({plugin_constant_version})不一致")

    if args.tag_name:
        normalized_tag = args.tag_name.removeprefix("v")
        if normalized_tag != theme_version:
            raise SystemExit(f"Git tag({args.tag_name})与主题/插件版本({theme_version})不一致")

    print("版本校验通过:")
    print(f"  Theme  : {theme_version}")
    print(f"  Plugin : {plugin_version}")
    if args.tag_name:
        print(f"  Tag    : {args.tag_name}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
