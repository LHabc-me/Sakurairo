# Release Playbook

本文件是仓库唯一有效的发版/热修/回滚入口。

## 0. Branch Policy (PR + 线性历史)

- 所有变更必须经 PR 合并到 `main`，禁止直接 push `main`。
- 维持线性历史：合并前先 `rebase` 到最新 `origin/main`，避免 merge commit。
- 推荐合并方式：`Rebase and merge`（或仓库已启用的线性策略等价方式）。

## 1. 标准发版流程

说明：先通过 PR 完成代码与版本变更，合并到 `main` 后再执行打 tag。

### 1.1 发版前检查（必做）

```bash
git checkout main
git pull --ff-only origin main
bash tools/release/release-checklist.sh precheck --version X.Y.Z
```

`precheck` 会检查：

- CI 是否绿灯（默认通过 GitHub CLI 自动检查）
- 工作区是否干净
- `style.css` 版本号是否与目标版本一致
- 目标 tag 是否已存在（本地/远端）

### 1.2 发版动作

1) 生成 release notes 模板：

```bash
bash tools/release/release-checklist.sh notes --version X.Y.Z --output .tmp_release_vX.Y.Z.md
```

2) 打 tag（默认 dry-run；实际执行需 `--execute`）：

```bash
bash tools/release/release-checklist.sh tag --version X.Y.Z
bash tools/release/release-checklist.sh tag --version X.Y.Z --execute
```

### 1.3 发版后验证

```bash
bash tools/release/release-checklist.sh postcheck
bash tools/release/post-release-verification.sh --version X.Y.Z
```

`postcheck` 会执行：

- `tools/ci/smoke.sh` 关键 smoke
- 发版入口文档/README 的关键链接与命令检查

`post-release-verification.sh` 会执行：

- release/tag/README 关键链接可达校验
- 关键 CI 最近一次运行状态校验（默认 `Theme CI (PR3)`）
- 主题关键文件存在性与版本一致性校验（`style.css` / `functions.php` / `README.md` / `docs/release-playbook.md`）

### 1.4 发布后 30 分钟检查项

- `T+5m`：确认 Release 页面与 tag 页面已可访问，README 在 tag 版本下可访问。
- `T+10m`：确认关键 CI 最新状态为 `completed + success`。
- `T+15m`：确认主题关键文件仍完整，`style.css` 版本与目标版本一致。
- `T+30m`：执行一次自动化复检并存档输出：

```bash
bash tools/release/post-release-verification.sh --version X.Y.Z
```

## 2. Hotfix / Rollback

### 2.1 紧急回滚最短路径（推荐）

最短恢复路径分两层：

1) 线上先恢复到上一个稳定 Release 包（最快止血）
2) 仓库走回滚 PR（保持审计与分支保护）

回滚 PR 命令模板（单提交）：

```bash
git fetch origin
git checkout -b rollback/<incident>-$(date +%Y%m%d-%H%M) origin/main
git revert --no-edit <bad_commit_sha>
git push -u origin HEAD
gh pr create \
  --base main \
  --title "rollback: revert <bad_commit_sha>" \
  --body "Emergency rollback for incident <id>."
```

回滚 PR 命令模板（连续提交区间）：

```bash
git fetch origin
git checkout -b rollback/<incident>-$(date +%Y%m%d-%H%M) origin/main
git revert --no-edit <oldest_bad_sha>^..<newest_bad_sha>
git push -u origin HEAD
gh pr create \
  --base main \
  --title "rollback: revert <oldest_bad_sha>..<newest_bad_sha>" \
  --body "Emergency rollback for incident <id>."
```

### 2.2 Hotfix 标准路径

```bash
git fetch origin
git checkout -b hotfix/<issue>-vX.Y.Z origin/main
# implement fix + tests
git add -A
git commit -m "fix(hotfix): <summary>"
git push -u origin HEAD
gh pr create --base main --title "fix(hotfix): <summary>" --body "<impact + test>"
```

合并前保持线性历史：

```bash
git fetch origin
git rebase origin/main
```

hotfix 合并后再按第 1 节流程发补丁版（`X.Y.Z+1`）。

### 2.3 回滚演练步骤（默认 dry-run）

1) 演练“回滚 PR 合并提交”模板：

```bash
bash tools/release/rollback-drill.sh revert-pr \
  --incident <incident_id> \
  --bad-sha <merge_commit_sha>
```

2) 演练“回滚 tag”模板：

```bash
bash tools/release/rollback-drill.sh revert-tag --tag vX.Y.Z
```

3) 演练“创建 hotfix 分支”模板：

```bash
bash tools/release/rollback-drill.sh hotfix-branch \
  --issue <issue_slug> \
  --version X.Y.Z
```

如需真实执行，将上述命令追加 `--execute`；演练默认仅打印将执行的命令。

## 3. 常用无副作用命令

```bash
# dry-run 查看将要打的 tag 命令
bash tools/release/release-checklist.sh tag --version X.Y.Z

# 在非 main 或脏工作区演练 precheck（仅演练，不可作为正式发版依据）
bash tools/release/release-checklist.sh precheck \
  --version X.Y.Z \
  --allow-non-main \
  --allow-dirty \
  --allow-manual-ci \
  --allow-manual-tag-check

# dry-run 演练回滚模板（不会执行任何变更）
bash tools/release/rollback-drill.sh revert-pr \
  --incident test-drill \
  --bad-sha deadbeef

# dry-run 发布后验证（不进行网络校验）
bash tools/release/post-release-verification.sh --version X.Y.Z --dry-run
```
