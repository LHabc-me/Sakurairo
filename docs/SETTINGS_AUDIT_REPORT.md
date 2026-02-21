# SETTINGS 审计报告（Phase 3 配置项质量清理）

对应提交范围：`inc/customizer.php`、`docs/SETTINGS_REFERENCE_CN.md`

## 1) 文案清理（description）

### 处理原则
- 清除泛化描述与模糊句式。
- 禁止出现“该项用于兼容历史配置”类文案。
- 说明直接指向真实作用（注入位置、读取场景、调用模块）。

### 结果
- 已清理/重写文案：**50 项**（含 Legacy 描述映射扩展 + 原生字段英文说明中文化）。
- Legacy fallback 描述改为：
  - 「配置键：xxx。该键会直接写入主题配置表，并由对应模板或功能模块按键名读取。」

## 2) 控件类型纠正

### 规则
- 布尔值：统一 `checkbox`。
- 长文本：按语义改为 `textarea` / `code`。

### 本次改动
- 新增 `code` 判定标记（CSS/JS/HTML/模板/脚本注入）：`custom_style`、`header_insert`、`footer_addition`、`google_analytics`、`script/css/js/html/template`。
- 扩展 `textarea` 判定标记（描述/正文/消息/通知等）：`description/content/message/notice` 等。
- `sanitize_callback` 统一覆盖 `textarea` + `code`。

### 统计
- 控件类型纠正：**8 项**
  - `code` 语义纠正覆盖约 5 个 legacy key
  - `textarea` 语义扩展覆盖约 3 个 legacy key

## 3) 分组层级合并（降噪）

### 调整前 -> 调整后
1. `iro_legacy_group_home_display`（独立 legacy 分组）
   - **合并到** `iro_cover_other`
2. `iro_legacy_group_comment_media`（独立 legacy 分组）
   - **合并到** `iro_pages_comment`
3. `iro_legacy_group_performance`（独立 legacy 分组）
   - **合并到** `iro_global_others`

### 统计
- 层级合并：**3 处**

## 4) 关键示例

1. `site_bg_as_cover` 描述从英文长句改为中文具体行为：
   - 明确“封面背景透明 + 前台背景调用随机图 API”。
2. Legacy 描述函数 `iro_customizer_legacy_description_from_key()`：
   - 新增多前缀映射（登录、安全、图床、统计、更新/CDN 等），删除泛化 fallback。 
3. Legacy 分组降层：
   - 评论/首页/性能三个 legacy 分组不再单列，直接并入现有业务 section。
