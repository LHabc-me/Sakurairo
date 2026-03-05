#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "${ROOT_DIR}"

log() {
  echo "[rollback-drill] $*"
}

die() {
  echo "[rollback-drill] ERROR: $*" >&2
  exit 1
}

usage() {
  cat <<'EOF'
Usage:
  tools/release/rollback-drill.sh revert-pr --bad-sha <sha> [options]
  tools/release/rollback-drill.sh revert-tag --tag <tag> [options]
  tools/release/rollback-drill.sh hotfix-branch --issue <issue> --version <X.Y.Z> [options]

Subcommands:
  revert-pr      Print (default) or execute rollback PR commands for a bad merge commit.
  revert-tag     Print (default) or execute local+remote tag rollback commands.
  hotfix-branch  Print (default) or execute hotfix branch creation commands.

Global options:
  --execute      Execute commands instead of dry-run printing.
  -h, --help     Show this help.

Options:
  revert-pr:
    --bad-sha <sha>      Bad merge commit SHA to revert (required).
    --incident <id>      Incident label for branch name (default: incident).
    --remote <name>      Git remote (default: origin).

  revert-tag:
    --tag <vX.Y.Z>       Tag to rollback (required).
    --remote <name>      Git remote (default: origin).

  hotfix-branch:
    --issue <issue>      Hotfix issue slug (required).
    --version <X.Y.Z>    Hotfix target version (required).
    --remote <name>      Git remote (default: origin).
EOF
}

run_or_echo() {
  local execute="${1:?missing execute flag}"
  shift
  local cmd="$*"
  if [[ "${execute}" == "1" ]]; then
    log "run: ${cmd}"
    eval "${cmd}"
  else
    log "dry-run: would run -> ${cmd}"
  fi
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

revert_pr_cmd() {
  local execute="${1:?missing execute flag}"
  shift
  local bad_sha=""
  local incident="incident"
  local remote="origin"

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --bad-sha)
        bad_sha="${2:-}"
        shift 2
        ;;
      --incident)
        incident="${2:-incident}"
        shift 2
        ;;
      --remote)
        remote="${2:-origin}"
        shift 2
        ;;
      *)
        die "unknown option for revert-pr: $1"
        ;;
    esac
  done

  [[ -n "${bad_sha}" ]] || die "--bad-sha is required for revert-pr"
  local rollback_branch="rollback/${incident}-$(date +%Y%m%d-%H%M)"

  run_or_echo "${execute}" "git fetch ${remote}"
  run_or_echo "${execute}" "git checkout -b ${rollback_branch} ${remote}/main"
  run_or_echo "${execute}" "git revert --no-edit ${bad_sha}"
  run_or_echo "${execute}" "git push -u ${remote} HEAD"
  run_or_echo "${execute}" "gh pr create --base main --title \"rollback: revert ${bad_sha}\" --body \"Emergency rollback for incident ${incident}.\""
}

revert_tag_cmd() {
  local execute="${1:?missing execute flag}"
  shift
  local tag=""
  local remote="origin"

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --tag)
        tag="${2:-}"
        shift 2
        ;;
      --remote)
        remote="${2:-origin}"
        shift 2
        ;;
      *)
        die "unknown option for revert-tag: $1"
        ;;
    esac
  done

  [[ -n "${tag}" ]] || die "--tag is required for revert-tag"

  run_or_echo "${execute}" "git push ${remote} :refs/tags/${tag}"
  run_or_echo "${execute}" "git tag -d ${tag}"
  run_or_echo "${execute}" "gh release delete ${tag} --yes"
}

hotfix_branch_cmd() {
  local execute="${1:?missing execute flag}"
  shift
  local issue=""
  local version=""
  local remote="origin"

  while [[ $# -gt 0 ]]; do
    case "$1" in
      --issue)
        issue="${2:-}"
        shift 2
        ;;
      --version)
        version="$(normalize_version "${2:-}")"
        shift 2
        ;;
      --remote)
        remote="${2:-origin}"
        shift 2
        ;;
      *)
        die "unknown option for hotfix-branch: $1"
        ;;
    esac
  done

  [[ -n "${issue}" ]] || die "--issue is required for hotfix-branch"
  [[ -n "${version}" ]] || die "--version is required for hotfix-branch"
  assert_semver "${version}"

  local hotfix_branch="hotfix/${issue}-v${version}"

  run_or_echo "${execute}" "git fetch ${remote}"
  run_or_echo "${execute}" "git checkout -b ${hotfix_branch} ${remote}/main"
  run_or_echo "${execute}" "git push -u ${remote} ${hotfix_branch}"
}

main() {
  local subcommand="${1:-}"
  local execute=0

  if [[ -z "${subcommand}" ]]; then
    usage
    exit 1
  fi
  shift || true

  if [[ "${subcommand}" == "-h" || "${subcommand}" == "--help" || "${subcommand}" == "help" ]]; then
    usage
    exit 0
  fi

  local remaining=()
  while [[ $# -gt 0 ]]; do
    case "$1" in
      --execute)
        execute=1
        shift
        ;;
      -h|--help)
        usage
        exit 0
        ;;
      *)
        remaining+=("$1")
        shift
        ;;
    esac
  done

  case "${subcommand}" in
    revert-pr)
      revert_pr_cmd "${execute}" "${remaining[@]}"
      ;;
    revert-tag)
      revert_tag_cmd "${execute}" "${remaining[@]}"
      ;;
    hotfix-branch)
      hotfix_branch_cmd "${execute}" "${remaining[@]}"
      ;;
    *)
      die "unknown subcommand: ${subcommand}"
      ;;
  esac
}

main "$@"
