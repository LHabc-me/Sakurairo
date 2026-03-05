#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "${ROOT_DIR}"

log() {
  echo "[post-release-verify] $*"
}

warn() {
  echo "[post-release-verify] WARN: $*" >&2
}

die() {
  echo "[post-release-verify] ERROR: $*" >&2
  exit 1
}

usage() {
  cat <<'EOF'
Usage:
  tools/release/post-release-verification.sh --version X.Y.Z [options]

Options:
  --version X.Y.Z               Target release version (required).
  --tag vX.Y.Z                  Target tag (default: v<version>).
  --repo owner/repo             GitHub repository (default: infer from git remote).
  --remote origin               Git remote used to infer repo (default: origin).
  --workflow "Theme CI (PR3)"   Workflow name to validate latest run (repeatable).
  --timeout 15                  curl timeout seconds (default: 15).
  --allow-manual-link-check     Allow fallback when link checks cannot run automatically.
  --allow-manual-ci             Allow fallback when CI checks cannot run automatically.
  --dry-run                     Print planned checks only; do not perform network checks.
  -h, --help                    Show this help.
EOF
}

normalize_version() {
  local raw="${1:-}"
  echo "${raw#v}"
}

assert_semver() {
  local version="${1:-}"
  if ! [[ "${version}" =~ ^[0-9]+\.[0-9]+\.[0-9]+([.-][0-9A-Za-z]+)*$ ]]; then
    die "invalid version '${version}' (expected semver like 1.2.83)"
  fi
}

style_version() {
  awk -F':[[:space:]]*' '/^Version:/{print $2; exit}' style.css | tr -d '\r'
}

infer_repo() {
  local remote="${1:?missing remote}"
  local remote_url
  remote_url="$(git remote get-url "${remote}" 2>/dev/null || true)"
  [[ -n "${remote_url}" ]] || die "cannot infer repository from remote '${remote}'"

  local repo=""
  if [[ "${remote_url}" =~ github\.com[:/]([^/]+/[^/.]+)(\.git)?$ ]]; then
    repo="${BASH_REMATCH[1]}"
  fi
  [[ -n "${repo}" ]] || die "unsupported remote URL for repo inference: ${remote_url}"

  echo "${repo}"
}

check_file_presence() {
  local file="$1"
  [[ -f "${file}" ]] || die "missing required file: ${file}"
  log "file exists: ${file}"
}

check_theme_consistency() {
  local version="${1:?missing version}"
  check_file_presence "README.md"
  check_file_presence "style.css"
  check_file_presence "functions.php"
  check_file_presence "docs/release-playbook.md"

  local detected_version
  detected_version="$(style_version)"
  [[ -n "${detected_version}" ]] || die "cannot read Version from style.css"

  if [[ "${detected_version}" != "${version}" ]]; then
    die "style.css Version (${detected_version}) does not match target ${version}"
  fi
  log "version check passed: style.css=${detected_version}"

  if command -v rg >/dev/null 2>&1; then
    if ! rg -q "define\('IRO_VERSION',[[:space:]]*wp_get_theme\(\)->get\('Version'\)\);" functions.php; then
      die "functions.php missing dynamic IRO_VERSION binding to wp_get_theme()->get('Version')"
    fi
  else
    if ! grep -q "define('IRO_VERSION', wp_get_theme()->get('Version'));" functions.php; then
      die "functions.php missing dynamic IRO_VERSION binding to wp_get_theme()->get('Version')"
    fi
  fi
  log "version source check passed: IRO_VERSION binds to theme header version"
}

assert_readme_keywords() {
  local repo="${1:?missing repo}"
  local release_url="https://github.com/${repo}/releases"

  if command -v rg >/dev/null 2>&1; then
    rg -q "docs/release-playbook.md" README.md || die "README.md must include docs/release-playbook.md entry"
    rg -q "${release_url}" README.md || die "README.md must include release URL: ${release_url}"
  else
    grep -q "docs/release-playbook.md" README.md || die "README.md must include docs/release-playbook.md entry"
    grep -q "${release_url}" README.md || die "README.md must include release URL: ${release_url}"
  fi
  log "README key links found"
}

assert_link_reachable() {
  local url="${1:?missing url}"
  local timeout="${2:?missing timeout}"
  local allow_manual="${3:-0}"
  local dry_run="${4:-0}"

  if [[ "${dry_run}" == "1" ]]; then
    log "dry-run: would check URL -> ${url}"
    return 0
  fi

  if ! command -v curl >/dev/null 2>&1; then
    if [[ "${allow_manual}" == "1" ]]; then
      warn "curl not found; please verify manually: ${url}"
      return 0
    fi
    die "curl not found. install curl or pass --allow-manual-link-check."
  fi

  if curl -fsSIL --max-time "${timeout}" "${url}" >/dev/null 2>&1; then
    log "link reachable: ${url}"
    return 0
  fi

  if [[ "${allow_manual}" == "1" ]]; then
    warn "failed to verify URL automatically; please verify manually: ${url}"
    return 0
  fi
  die "link is not reachable: ${url}"
}

check_links() {
  local repo="${1:?missing repo}"
  local tag="${2:?missing tag}"
  local timeout="${3:?missing timeout}"
  local allow_manual="${4:-0}"
  local dry_run="${5:-0}"

  local release_url="https://github.com/${repo}/releases"
  local tag_url="https://github.com/${repo}/releases/tag/${tag}"
  local tag_readme_url="https://raw.githubusercontent.com/${repo}/refs/tags/${tag}/README.md"

  assert_link_reachable "${release_url}" "${timeout}" "${allow_manual}" "${dry_run}"
  assert_link_reachable "${tag_url}" "${timeout}" "${allow_manual}" "${dry_run}"
  assert_link_reachable "${tag_readme_url}" "${timeout}" "${allow_manual}" "${dry_run}"
}

check_ci_latest() {
  local allow_manual_ci="${1:-0}"
  local dry_run="${2:-0}"
  shift 2 || true
  local workflows=("$@")

  if [[ "${#workflows[@]}" -eq 0 ]]; then
    workflows=("Theme CI (PR3)")
  fi

  if [[ "${dry_run}" == "1" ]]; then
    for wf in "${workflows[@]}"; do
      log "dry-run: would check latest workflow run status -> ${wf}"
    done
    return 0
  fi

  if ! command -v gh >/dev/null 2>&1; then
    if [[ "${allow_manual_ci}" == "1" ]]; then
      warn "gh CLI not found; please verify latest workflow status manually"
      return 0
    fi
    die "gh CLI not found. install gh or pass --allow-manual-ci."
  fi

  local wf
  for wf in "${workflows[@]}"; do
    local line
    line="$(gh run list \
      --workflow "${wf}" \
      --branch main \
      --limit 1 \
      --json status,conclusion,url,headSha \
      --jq ".[] | [.status,.conclusion,.url,.headSha] | @tsv" \
      2>/dev/null | head -n 1 || true)"

    if [[ -z "${line}" ]]; then
      if [[ "${allow_manual_ci}" == "1" ]]; then
        warn "no workflow run found via gh for '${wf}'; please verify manually"
        continue
      fi
      die "cannot fetch latest workflow run for '${wf}'"
    fi

    local status conclusion url head_sha
    IFS=$'\t' read -r status conclusion url head_sha <<<"${line}"

    if [[ "${status}" != "completed" || "${conclusion}" != "success" ]]; then
      die "workflow '${wf}' not green. status=${status}, conclusion=${conclusion}, url=${url}"
    fi
    log "workflow green: ${wf} (${head_sha}) -> ${url}"
  done
}

main() {
  local version=""
  local tag=""
  local repo=""
  local remote="origin"
  local timeout=15
  local allow_manual_link_check=0
  local allow_manual_ci=0
  local dry_run=0
  local workflows=()

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --version)
        version="$(normalize_version "${2:-}")"
        shift 2
        ;;
      --tag)
        tag="${2:-}"
        shift 2
        ;;
      --repo)
        repo="${2:-}"
        shift 2
        ;;
      --remote)
        remote="${2:-}"
        shift 2
        ;;
      --workflow)
        workflows+=("${2:-}")
        shift 2
        ;;
      --timeout)
        timeout="${2:-15}"
        shift 2
        ;;
      --allow-manual-link-check)
        allow_manual_link_check=1
        shift
        ;;
      --allow-manual-ci)
        allow_manual_ci=1
        shift
        ;;
      --dry-run)
        dry_run=1
        shift
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        die "unknown option: $1"
        ;;
    esac
  done

  [[ -n "${version}" ]] || die "--version is required"
  assert_semver "${version}"

  if [[ -z "${tag}" ]]; then
    tag="v${version}"
  fi

  if [[ -z "${repo}" ]]; then
    repo="$(infer_repo "${remote}")"
  fi
  [[ "${repo}" =~ ^[^/]+/[^/]+$ ]] || die "invalid repo format: ${repo} (expected owner/repo)"

  log "start verification: version=${version}, tag=${tag}, repo=${repo}"
  assert_readme_keywords "${repo}"
  check_theme_consistency "${version}"
  check_links "${repo}" "${tag}" "${timeout}" "${allow_manual_link_check}" "${dry_run}"
  check_ci_latest "${allow_manual_ci}" "${dry_run}" "${workflows[@]}"
  log "verification passed"
}

main "$@"
