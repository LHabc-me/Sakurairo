#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT_DIR"

usage() {
  cat <<'EOF'
Usage: bash tools/maintenance/workspace-hygiene-check.sh [--strict]

Non-destructive workspace hygiene check:
- Detect root temp drafts like .tmp_pr_*.md / .tmp_release_*.md / .tmp_*.md
- Detect tracked temp artifacts accidentally added to Git history
- Verify .gitignore contains expected temp ignore rules

Options:
  --strict   exit with code 1 when hygiene issues are detected
  -h, --help show this help message
EOF
}

strict_mode=0
for arg in "$@"; do
  case "$arg" in
    --strict) strict_mode=1 ;;
    -h|--help)
      usage
      exit 0
      ;;
    *)
      echo "[ERROR] Unknown argument: $arg" >&2
      usage >&2
      exit 2
      ;;
  esac
done

find_temp_files() {
  find . -maxdepth 1 -type f \
    \( -name '.tmp_pr_*.md' -o -name '.tmp_release_*.md' -o -name '.tmp_*.md' \) \
    -printf '%f\n' | sort -u
}

check_ignore_rules() {
  local missing=0
  local rule
  for rule in '/.tmp_pr_*.md' '/.tmp_release_*.md' '/.tmp_*.md'; do
    if ! grep -qFx "$rule" .gitignore; then
      echo "[WARN] Missing .gitignore rule: $rule"
      missing=1
    fi
  done
  return "$missing"
}

tracked_temp_files="$(git ls-files | awk '/^\.tmp_.*\.md$/ { print }')"
root_temp_files="$(find_temp_files)"

has_issue=0

echo "[INFO] Workspace hygiene check (non-destructive)"
echo "[INFO] Root directory: $ROOT_DIR"

if [[ -n "$root_temp_files" ]]; then
  echo "[WARN] Root temp drafts detected:"
  while IFS= read -r file; do
    [[ -n "$file" ]] && echo "  - $file"
  done <<< "$root_temp_files"
  echo "[HINT] Keep local drafts if needed, but avoid staging/committing them."
  has_issue=1
else
  echo "[OK] No root temp drafts detected."
fi

if [[ -n "$tracked_temp_files" ]]; then
  echo "[WARN] Tracked temp artifacts found in Git index/history:"
  while IFS= read -r file; do
    [[ -n "$file" ]] && echo "  - $file"
  done <<< "$tracked_temp_files"
  has_issue=1
else
  echo "[OK] No tracked temp artifacts found."
fi

if check_ignore_rules; then
  echo "[OK] .gitignore temp governance rules are present."
else
  has_issue=1
fi

if [[ "$strict_mode" -eq 1 && "$has_issue" -eq 1 ]]; then
  echo "[FAIL] Hygiene issues detected in strict mode."
  exit 1
fi

echo "[DONE] Workspace hygiene check completed."
