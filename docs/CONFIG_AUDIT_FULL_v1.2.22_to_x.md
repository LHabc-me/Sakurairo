# Customizer 全量配置审阅与修复报告（v1.2.22 → x）

## 审阅范围
- 文件：`inc/customizer.php`
- 目标：全量检查配置项命名、可读性、控件类型一致性，并修复可定位问题。

## 审阅方法（细粒度）
1. 逐项扫描 `settings` 与 `type` 定义，统计总量与重复项。
2. 检查布尔语义配置（命名后缀/默认值）是否统一为 `checkbox`。
3. 检查带 `choices` 的枚举项是否落到 `select/radio`。
4. 检查中英混杂与“QQ/二维码”等词汇在本地化流程中的退化风险。
5. 检查重复配置名（`settings` 冲突）与可读性较差标签。

## 审阅统计
- `settings` 总数：152
- `settings` 唯一数：152（已消除重复）
- 控件类型分布（定义层）：
  - checkbox: 50
  - slider: 22
  - text: 26
  - select: 13
  - image: 13
  - color: 10
  - code: 4
  - radio_image: 5
  - radio: 2
  - 其他：custom/sortable/dropdown_pages/textarea

## 本次修复项
### 1) 配置名唯一性修复
- `nav_menu_notice`（重复）→ 评论区提示项更名为 `comment_area_notice`
- `global_default_font`（重复）→ 小组件联动项更名为 `global_default_font_widgets`

### 2) 标签可读性与可区分性修复
- `post_list_ticket_type` 标签从 `Article Area Card Design` 改为 `Article Area Ticket Layout`
- `article_meta_show_in_head` 标签从 `Article Area Meta Displays` 改为 `Article Page Meta Displays`
- 打赏区双图片标签拆分：
  - `Reward Image` → `Reward Image A/B`
  - `Reward Image Link` → `Reward Image Link A/B`

### 3) 中英混杂/QQ 退化修复
- 本地化词典补充：`qq`、`qrcode`、`wechat`
- 增加文案归一清洗：`QQQQ -> QQ`、`二维码二维码 -> 二维码`
- 避免“已有中文文本被英文降级”的保护逻辑保持生效。

### 4) 控件类型一致性防护（注册层兜底）
在字段注册前增加统一审计归一逻辑：
- `switch/toggle` 一律归一为 `checkbox`
- 若字段带 `choices` 且原类型是 `text/textarea/number/url/code`，自动归一为 `select`
- 布尔语义命名（如 `_switch/_enable/_auto/...`）或布尔默认值时，强制归一为 `checkbox`

> 说明：该兜底不会破坏既有正确配置，主要用于拦截历史迁移字段与后续新增字段的人为偏差。

## 语法校验
已执行：

```bash
docker run --rm -v /home/lhabc/clawd/dev/Sakurairo-fork:/app -w /app php:8.2-cli php -l inc/customizer.php
```

结果：`No syntax errors detected in inc/customizer.php`

## 结论
- 已完成全量配置级别审阅（定义层 + 注册层兜底）。
- 已修复可确认问题：重复配置名、部分标签歧义、QQ/二维码退化风险、类型一致性兜底。
- 后续新增字段可继续复用当前兜底规则，确保“枚举用 select/radio、布尔用 checkbox”。
