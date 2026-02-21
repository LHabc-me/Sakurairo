# 主题设置导入/导出（JSON）

本文档说明 Shinonomeiro 主题在后台通过 JSON 文件导入/导出 `shinonomeiro_options` 的使用方法、安全策略与回滚建议。

## 入口位置

WordPress 后台：`工具` → `Shinonomeiro Settings`

> 页面 slug：`tools.php?page=iro-settings-transfer`

## 功能说明

### 1) 导出

- 点击 **Download JSON** 按钮
- 系统会导出当前主题设置（`shinonomeiro_options`）为 JSON 文件下载
- 文件名示例：`shinonomeiro-options-20260221-130000.json`

### 2) 导入

- 选择 `.json` 文件
- 点击 **Upload and Import**
- 系统会校验并导入允许的键，完成后显示成功/失败提示

## 导入安全与校验策略

导入动作使用 `admin_post` 处理，并进行以下校验：

1. **权限校验**：要求 `manage_options`
2. **Nonce 校验**：`check_admin_referer('iro_settings_import', 'iro_settings_import_nonce')`
3. **文件合法性检查**：上传错误码、空文件检查
4. **JSON 语法检查**：`json_decode` + `json_last_error`
5. **JSON 根结构检查**：要求根节点必须是对象（解码后为关联数组）
6. **允许键白名单过滤**：仅保留允许的主题配置键并写入 `shinonomeiro_options`

允许键来源（并集）：
- 当前已存储的 `shinonomeiro_options` 键
- `inc/customizer.php` 中声明的 `iro_key`
- `inc/customizer-migrated-fields.php` 中的 legacy 键
- `opt/options/theme-options.php` 中声明的 `id`

> 说明：导入仅更新主题选项数组，不会写入任意新的 WordPress option 名称，避免 option 污染。

## 兼容性说明

- 导入写入目标仍为 `shinonomeiro_options`，并同步 `$GLOBALS['iro_options']` 与 `theme_mod`，不改变现有读取语义（`iro_opt` 逻辑不变）
- one-time legacy import 机制（`iro_run_one_time_legacy_import` 及相关入口）未被移除或覆盖，互不冲突

## 风险提示

- 导入会覆盖同名键对应的当前值（未包含在 JSON 中的键保留现值）
- 若 JSON 来源不可信，可能导致主题显示异常（虽然已做键白名单）
- 建议仅导入自己导出的备份文件

## 回滚建议

1. **优先回滚**：先执行一次导出，保留“导入前”备份
2. 若导入后异常：
   - 直接导入之前备份的 JSON 文件进行恢复
3. 极端情况下：
   - 可在数据库手动恢复 `shinonomeiro_options` 记录

## 变更文件

- `inc/admin-settings-transfer.php`（新增）
- `functions.php`（加载导入/导出工具页）
