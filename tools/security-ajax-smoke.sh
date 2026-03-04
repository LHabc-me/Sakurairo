#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

AJAX_URL="${AJAX_URL:-http://localhost/wp-admin/admin-ajax.php}"

# Optional for permission negative.
LOW_PRIV_COOKIE="${LOW_PRIV_COOKIE:-}"
LOW_PRIV_NONCE="${LOW_PRIV_NONCE:-}"

# Optional for rate-limit negative.
LINK_NONCE="${LINK_NONCE:-}"

pass_count=0
fail_count=0
skip_count=0

post_ajax() {
  local data="$1"
  local cookie="${2:-}"
  local response

  if [[ -n "$cookie" ]]; then
    response="$(curl -sS --max-time 20 -H "Cookie: $cookie" --data "$data" "$AJAX_URL" -w $'\n__STATUS__:%{http_code}')"
  else
    response="$(curl -sS --max-time 20 --data "$data" "$AJAX_URL" -w $'\n__STATUS__:%{http_code}')"
  fi

  local status="${response##*$'\n'__STATUS__:}"
  local body="${response%$'\n'__STATUS__:*}"
  printf '%s\n%s' "$status" "$body"
}

assert_case() {
  local case_name="$1"
  local status="$2"
  local body="$3"
  local expected_status="$4"
  local expected_text="$5"

  if [[ "$status" == "$expected_status" && "$body" == *"$expected_text"* ]]; then
    echo "[PASS] $case_name"
    pass_count=$((pass_count + 1))
    return
  fi

  echo "[FAIL] $case_name"
  echo "  expected status=$expected_status text=\"$expected_text\""
  echo "  actual   status=$status body=$(printf '%s' "$body" | head -c 240)"
  fail_count=$((fail_count + 1))
}

echo "[SecuritySmoke] AJAX endpoint: $AJAX_URL"

# Smoke + negative: missing nonce should be blocked by unified guard.
missing_nonce_payload="action=link_submission&siteName=test&siteUrl=https%3A%2F%2Fexample.com&siteDescription=test&siteImage=https%3A%2F%2Fexample.com%2Flogo.png&contactEmail=test%40example.com&yzm=1&timestamp=1&id=1"
missing_nonce_result="$(post_ajax "$missing_nonce_payload")"
missing_nonce_status="$(printf '%s\n' "$missing_nonce_result" | head -n1)"
missing_nonce_body="$(printf '%s\n' "$missing_nonce_result" | sed '1d')"
assert_case "link_submission missing nonce" "$missing_nonce_status" "$missing_nonce_body" "403" "Security verification failed."

# Negative: insufficient capability, requires low-priv login + nonce.
if [[ -n "$LOW_PRIV_COOKIE" && -n "$LOW_PRIV_NONCE" ]]; then
  cap_payload="action=update_theme_option&option=security_smoke_flag&value=1&_wpnonce=$LOW_PRIV_NONCE"
  cap_result="$(post_ajax "$cap_payload" "$LOW_PRIV_COOKIE")"
  cap_status="$(printf '%s\n' "$cap_result" | head -n1)"
  cap_body="$(printf '%s\n' "$cap_result" | sed '1d')"
  assert_case "update_theme_option low capability" "$cap_status" "$cap_body" "403" "Access denied."
else
  echo "[SKIP] update_theme_option low capability (need LOW_PRIV_COOKIE + LOW_PRIV_NONCE)"
  skip_count=$((skip_count + 1))
fi

# Negative: rate-limit exceeded (guard runs before captcha validation).
if [[ -n "$LINK_NONCE" ]]; then
  rate_payload="action=link_submission&link_submission_nonce=$LINK_NONCE&siteName=test&siteUrl=https%3A%2F%2Fexample.com&siteDescription=test&siteImage=https%3A%2F%2Fexample.com%2Flogo.png&contactEmail=test%40example.com&yzm=1&timestamp=1&id=1"
  post_ajax "$rate_payload" >/dev/null
  rate_result="$(post_ajax "$rate_payload")"
  rate_status="$(printf '%s\n' "$rate_result" | head -n1)"
  rate_body="$(printf '%s\n' "$rate_result" | sed '1d')"
  assert_case "link_submission rate limit" "$rate_status" "$rate_body" "429" "too frequently"
else
  echo "[SKIP] link_submission rate limit (need LINK_NONCE)"
  skip_count=$((skip_count + 1))
fi

echo "[SecuritySmoke] pass=$pass_count fail=$fail_count skip=$skip_count"
if [[ "$fail_count" -gt 0 ]]; then
  exit 1
fi
