#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

echo "[PR2] JS syntax check"
node --check js/nav.js
node --check js/page.js

echo "[PR2] Lifecycle marker check"
if ! rg -q "__iroNavLifecycle|createIroNavLifecycle" js/nav.js; then
  echo "nav lifecycle markers not found in js/nav.js" >&2
  exit 1
fi
if ! rg -q "init\(\) \{" js/nav.js; then
  echo "lifecycle init() marker not found in js/nav.js" >&2
  exit 1
fi
if ! rg -q "destroy\(\) \{" js/nav.js; then
  echo "lifecycle destroy() marker not found in js/nav.js" >&2
  exit 1
fi
if ! rg -q "rebind\(\) \{" js/nav.js; then
  echo "init/destroy/rebind contract markers not found in js/nav.js" >&2
  exit 1
fi

echo "[PR2] Manual regression checklist"
echo "1. 切页：在任意文章页与首页之间来回切换 3 次，观察导航动画无抖动、无重复触发。"
echo "2. 主题切换：执行一次明暗主题切换，确认导航状态与样式不被覆盖。"
echo "3. 移动端菜单：模拟宽度 < 860，连续打开/关闭菜单与目录面板，确认互斥和背景状态正常。"
echo "4. 控制台：执行以上步骤后控制台无新增 Error。"

echo "[PR2] check script finished"
