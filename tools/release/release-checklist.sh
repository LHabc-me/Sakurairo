#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "${ROOT_DIR}"

SCRIPT_NAME="$(basename "$0")"

log() {
  echo "[release-checklist] $*"
}

warn() {
  echo "[release-checklist] WARN: $*" >&2
}

die() {
  echo "[release-checklist] ERROR: $*" >&2
  exit 1
}

usage() {
  cat <<'EOF'
Usage:
  tools/release/release-checklist.sh precheck --version X.Y.Z [options]
  tools/release/release-checklist.sh notes --version X.Y.Z [options]
  tools/release/release-checklist.sh tag --version X.Y.Z [--execute] [options]
  tools/release/release-checklist.sh postcheck

Subcommands:
  precheck   Verify CI / clean tree / version / tag state.
  notes      Generate a release notes template file.
  tag        Print (default) or execute tag+push commands.
  postcheck  Run smoke and key doc/link checks.
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

latest_semver_tag() {
  git tag -l 'v[0-9]*.[0-9]*.[0-9]*' --sort=-v:refname | head -n 1
}

assert_clean_worktree() {
  local dirty
  dirty="$(git status --porcelain --untracked-files=all)"
  if [[ -n "${dirty}" ]]; then
    die "working tree is not clean. commit/stash/remove changes before release."
  fi
}

assert_main_branch() {
  local branch
  branch="$(git rev-parse --abbrev-ref HEAD)"
  if [[ "${branch}" != "main" ]]; then
    die "release should run on main branch, current: ${branch}"
  fi
}

assert_tag_available() {
  local tag="${1:?missing tag}"
  local remote="${2:?missing remote}"
  local allow_manual_remote_check="${3:-0}"

  if git rev-parse -q --verify "refs/tags/${tag}" >/dev/null; then
    die "tag ${tag} already exists locally"
  fi

  local remote_hit=""
  if remote_hit="$(git ls-remote --tags "${remote}" "refs/tags/${tag}" 2>/dev/null)"; then
    if [[ -n "${remote_hit}" ]]; then
      die "tag ${tag} already exists on remote ${remote}"
    fi
  else
    if [[ "${allow_manual_remote_check}" == "1" ]]; then
      warn "cannot verify remote tag state automatically; please verify ${remote}:${tag} manually"
    else
      die "failed to verify remote tag state. pass --allow-manual-tag-check to bypass."
    fi
  fi
}

assert_ci_green() {
  local allow_manual_ci="${1:-0}"
  local workflow_name="Theme CI (PR3)"
  local head_sha
  head_sha="$(git rev-parse HEAD)"

  if ! command -v gh >/dev/null 2>&1; then
    if [[ "${allow_manual_ci}" == "1" ]]; then
      warn "gh CLI not found; verify CI manually for commit ${head_sha}"
      return 0
    fi
    die "gh CLI not found. install gh or pass --allow-manual-ci."
  fi

  local line
  line="$(gh run list \
    --workflow "${workflow_name}" \
    --branch main \
    --limit 20 \
    --json headSha,status,conclusion,url \
    --jq ".[] | select(.headSha == \"${head_sha}\") | [.status,.conclusion,.url] | @tsv" \
    2>/dev/null | head -n 1 || true)"

  if [[ -z "${line}" ]]; then
    if [[ "${allow_manual_ci}" == "1" ]]; then
      warn "no CI run found via gh for ${head_sha}; verify manually in GitHub Actions"
      return 0
    fi
    die "cannot find CI run for ${head_sha}. pass --allow-manual-ci to bypass."
  fi

  local status conclusion url
  IFS=$'\t' read -r status conclusion url <<<"${line}"

  if [[ "${status}" != "completed" || "${conclusion}" != "success" ]]; then
    die "CI is not green for ${head_sha}. status=${status}, conclusion=${conclusion}, url=${url}"
  fi

  log "CI check passed: ${url}"
}

precheck_cmd() {
  local version=""
  local remote="origin"
  local allow_manual_ci=0
  local allow_manual_tag_check=0
  local allow_dirty=0
  local allow_non_main=0

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --version)
        version="$(normalize_version "${2:-}")"
        shift 2
        ;;
      --remote)
        remote="${2:-}"
        shift 2
        ;;
      --allow-manual-ci)
        allow_manual_ci=1
        shift
        ;;
      --allow-manual-tag-check)
        allow_manual_tag_check=1
        shift
        ;;
      --allow-dirty)
        allow_dirty=1
        shift
        ;;
      --allow-non-main)
        allow_non_main=1
        shift
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        die "unknown option for precheck: $1"
        ;;
    esac
  done

  [[ -n "${version}" ]] || die "--version is required"
  assert_semver "${version}"

  if [[ "${allow_non_main}" != "1" ]]; then
    assert_main_branch
  else
    warn "skip main-branch check (--allow-non-main)"
  fi

  if [[ "${allow_dirty}" != "1" ]]; then
    assert_clean_worktree
  else
    warn "skip clean-worktree check (--allow-dirty)"
  fi

  local detected_style_version
  detected_style_version="$(style_version)"
  [[ -n "${detected_style_version}" ]] || die "cannot read Version from style.css"

  if [[ "${detected_style_version}" != "${version}" ]]; then
    die "style.css Version (${detected_style_version}) does not match target ${version}"
  fi
  log "version check passed: ${version}"

  local latest_tag
  latest_tag="$(latest_semver_tag || true)"
  if [[ -n "${latest_tag}" ]]; then
    local latest_version
    latest_version="$(normalize_version "${latest_tag}")"
    if [[ "$(printf '%s\n%s\n' "${latest_version}" "${version}" | sort -V | tail -n 1)" != "${version}" ]]; then
      die "target ${version} is not newer than latest tag ${latest_tag}"
    fi
    log "latest tag: ${latest_tag}"
  fi

  local target_tag="v${version}"
  assert_tag_available "${target_tag}" "${remote}" "${allow_manual_tag_check}"
  log "tag check passed: ${target_tag} is available"

  assert_ci_green "${allow_manual_ci}"

  log "precheck passed"
}

notes_cmd() {
  local version=""
  local from_tag=""
  local output=""

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --version)
        version="$(normalize_version "${2:-}")"
        shift 2
        ;;
      --from-tag)
        from_tag="${2:-}"
        shift 2
        ;;
      --output)
        output="${2:-}"
        shift 2
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        die "unknown option for notes: $1"
        ;;
    esac
  done

  [[ -n "${version}" ]] || die "--version is required"
  assert_semver "${version}"

  if [[ -z "${output}" ]]; then
    output=".tmp_release_v${version}.md"
  fi

  if [[ -z "${from_tag}" ]]; then
    from_tag="$(latest_semver_tag || true)"
  fi

  local commit_lines=""
  local compare_range=""
  if [[ -n "${from_tag}" ]]; then
    compare_range="${from_tag}..HEAD"
    commit_lines="$(git log --pretty='- %s (%h)' "${from_tag}..HEAD" --reverse || true)"
  else
    compare_range="HEAD (last 20 commits)"
    commit_lines="$(git log --pretty='- %s (%h)' -n 20 --reverse || true)"
  fi
  [[ -n "${commit_lines}" ]] || commit_lines="- (no commits collected)"

  cat >"${output}" <<EOF
# Release v${version}

- Release date: $(date +%F)
- Base tag: ${from_tag:-N/A}
- Compare range: ${compare_range}

## Highlights
- 

## Fixes
- 

## Breaking Changes
- None / N/A

## Commit Summary
${commit_lines}

## Verification
- [ ] CI green
- [ ] \`bash tools/release/release-checklist.sh postcheck\`

## Rollback Plan
- Follow \`docs/release-playbook.md\` -> "Hotfix / Rollback"
EOF

  log "release notes template generated: ${output}"
}

tag_cmd() {
  local version=""
  local remote="origin"
  local execute=0
  local message=""
  local allow_manual_tag_check=0

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --version)
        version="$(normalize_version "${2:-}")"
        shift 2
        ;;
      --remote)
        remote="${2:-}"
        shift 2
        ;;
      --message)
        message="${2:-}"
        shift 2
        ;;
      --execute)
        execute=1
        shift
        ;;
      --allow-manual-tag-check)
        allow_manual_tag_check=1
        shift
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        die "unknown option for tag: $1"
        ;;
    esac
  done

  [[ -n "${version}" ]] || die "--version is required"
  assert_semver "${version}"

  local tag="v${version}"
  assert_tag_available "${tag}" "${remote}" "${allow_manual_tag_check}"
  [[ -n "${message}" ]] || message="chore(release): v${version}"

  if [[ "${execute}" != "1" ]]; then
    log "dry-run mode (no side effects)"
    log "would run: git tag -a ${tag} -m \"${message}\""
    log "would run: git push ${remote} ${tag}"
    return 0
  fi

  git tag -a "${tag}" -m "${message}"
  git push "${remote}" "${tag}"
  log "tag published: ${tag}"
}

postcheck_cmd() {
  [[ -f tools/ci/smoke.sh ]] || die "missing tools/ci/smoke.sh"
  bash tools/ci/smoke.sh

  [[ -f docs/release-playbook.md ]] || die "missing docs/release-playbook.md"
  rg -q "docs/release-playbook.md" README.md || die "README.md must point to docs/release-playbook.md"
  rg -q "tools/release/release-checklist.sh precheck" docs/release-playbook.md || die "playbook missing precheck command"
  rg -q "tools/release/release-checklist.sh tag --version" docs/release-playbook.md || die "playbook missing tag command"
  rg -q "tools/release/release-checklist.sh postcheck" docs/release-playbook.md || die "playbook missing postcheck command"

  log "postcheck passed"
}

main() {
  local subcommand="${1:-}"
  if [[ -z "${subcommand}" ]]; then
    usage
    exit 1
  fi
  shift || true

  case "${subcommand}" in
    precheck)
      precheck_cmd "$@"
      ;;
    notes)
      notes_cmd "$@"
      ;;
    tag)
      tag_cmd "$@"
      ;;
    postcheck)
      postcheck_cmd "$@"
      ;;
    -h|--help|help)
      usage
      ;;
    *)
      die "unknown subcommand: ${subcommand}"
      ;;
  esac
}

main "$@"
