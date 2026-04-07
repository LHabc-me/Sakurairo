from __future__ import annotations

import argparse
import fnmatch
import json
import shutil
import sys
import zipfile
from pathlib import Path


ROOT_DIR = Path(__file__).resolve().parents[2]
STYLE_FILE = ROOT_DIR / "style.css"
THEME_SLUG = "Shinonomeiro"

THEME_EXCLUDE_DIRS = {
    ".git",
    ".github",
    "docs",
    "tools",
    "dist",
    "node_modules",
    ".next",
    ".codex",
}
THEME_EXCLUDE_FILES = ("*.log",)


def read_version(path: Path, pattern: str, error_message: str) -> str:
    import re

    content = path.read_text(encoding="utf-8")
    match = re.search(pattern, content, flags=re.MULTILINE)
    if not match:
        raise SystemExit(error_message)
    return match.group(1).strip()


def should_exclude(rel_path: Path, exclude_dirs: set[str], exclude_files: tuple[str, ...]) -> bool:
    if rel_path.parts and rel_path.parts[0] in exclude_dirs:
        return True
    return any(fnmatch.fnmatch(rel_path.name, pattern) for pattern in exclude_files)


def copy_tree(src: Path, dst: Path, exclude_dirs: set[str], exclude_files: tuple[str, ...]) -> None:
    for path in src.rglob("*"):
        rel = path.relative_to(src)
        if should_exclude(rel, exclude_dirs, exclude_files):
            continue
        if any(part in exclude_dirs for part in rel.parts[:-1]):
            continue
        target = dst / rel
        if path.is_dir():
            target.mkdir(parents=True, exist_ok=True)
        else:
            target.parent.mkdir(parents=True, exist_ok=True)
            shutil.copy2(path, target)


def zip_dir(stage_root: Path, src: Path, zip_path: Path) -> None:
    with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED) as zf:
        for path in src.rglob("*"):
            if path.is_file():
                zf.write(path, path.relative_to(stage_root))


def main() -> int:
    parser = argparse.ArgumentParser(description="构建主题 Release 资产。")
    parser.add_argument("output_dir", nargs="?", default=str(ROOT_DIR / "dist" / "release"))
    args = parser.parse_args()

    output_dir = Path(args.output_dir).resolve()
    stage_dir = output_dir / "stage"
    theme_stage = stage_dir / THEME_SLUG

    theme_version = read_version(STYLE_FILE, r"^Version:\s*(.+)$", "未能从 style.css 解析主题版本")

    output_dir.mkdir(parents=True, exist_ok=True)
    if stage_dir.exists():
        shutil.rmtree(stage_dir)
    theme_stage.mkdir(parents=True, exist_ok=True)

    print("构建主题发布目录...")
    copy_tree(ROOT_DIR, theme_stage, THEME_EXCLUDE_DIRS, THEME_EXCLUDE_FILES)

    theme_zip = output_dir / f"{THEME_SLUG}-v{theme_version}.zip"

    print("打包主题 ZIP...")
    zip_dir(stage_dir, theme_stage, theme_zip)

    manifest = {
        "theme": {
            "slug": THEME_SLUG,
            "version": theme_version,
            "zip": theme_zip.name,
        }
    }
    manifest_path = output_dir / "manifest.json"
    manifest_path.write_text(json.dumps(manifest, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")

    print("构建完成:")
    print(f"  主题包: {theme_zip}")
    print(f"  清单:   {manifest_path}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
