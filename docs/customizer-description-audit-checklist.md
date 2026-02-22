# Customizer Description 真值审阅清单（临时）

- 分支：`feat/customizer-description-truth-audit`
- 范围文件：`inc/customizer.php`、`inc/customizer-migrated-fields.php`
- 字段总量（`settings`）：345（其中显式定义 152 + legacy 迁移 193）
- 当前显式 `description` 定义：66
- 模板化风险来源：`iro_customizer_legacy_description_from_key()` 的兜底句式（“这个选项会影响……不确定怎么改时建议先保持默认值。”）

## 审阅策略
1. 先审 legacy 键（193 条），全部改为“按 key 精确描述”，不再使用统一兜底模板。
2. 每条简介参考 key 名称 + 调用位置（`tpl/`,`inc/`,`js/`,`style.css`）撰写。
3. 不改 setting key / default / type / 逻辑，仅改 description 文案。

## 待处理清单导出方式
- 迁移键来源：`inc/customizer-migrated-fields.php`
- 调用位置追踪命令：
  - `rg -n "<key>" inc tpl js style.css`

> 详细 key->调用位置->新简介 抽样核对记录见：`docs/customizer-description-sample-verify.md`。
