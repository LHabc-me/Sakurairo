# 维护者工作区卫生规范（临时文件治理）

## 目标

- 减少历史临时稿件（如 `.tmp_pr_*.md`、`.tmp_release_*.md`）对工作区的噪声污染。
- 在不删除维护者本地文件的前提下，降低误提交风险。

## 治理范围

仓库根目录临时文件命名模式：

- `.tmp_pr_*.md`
- `.tmp_release_*.md`
- `.tmp_*.md`

说明：以上模式已加入根目录 `.gitignore`，用于避免新增临时稿件被意外纳入版本控制。

## 维护约定

- 临时稿件统一放在仓库根目录，并使用 `.tmp_*` 前缀。
- 临时稿件仅用于本地草稿/命令输出，不应进入正式提交。
- 需要长期留存的材料，应迁移为正式文档（例如放入 `docs/` 并使用明确文件名）。

## 非破坏性检查脚本

```bash
bash tools/maintenance/workspace-hygiene-check.sh
```

严格模式（检测到问题时返回非 0，适合本地 gate）：

```bash
bash tools/maintenance/workspace-hygiene-check.sh --strict
```

脚本行为：

- 只检查和提示，不执行删除。
- 检查根目录临时稿件是否存在。
- 检查是否有误跟踪的 `.tmp_*.md` 文件。
- 检查 `.gitignore` 是否包含治理规则。
