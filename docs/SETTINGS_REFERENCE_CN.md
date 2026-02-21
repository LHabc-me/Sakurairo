# Shinonomeiro 配置结构参考（中文）

> 对应文件：`inc/customizer.php`（配置项质量清理版）

## 面板结构（9 大域）

1. 用户信息（`iro_user_profile`）
2. 注入与自定义代码（`iro_custom_code`）
3. 首页展示（`iro_home_display`）
4. 文章阅读（`iro_article_reading`）
5. 评论互动（`iro_comment_interaction`）
6. 性能加速（`iro_performance`）
7. 第三方服务（`iro_third_party`）
8. 账号安全（`iro_account_security`）
9. 开发维护（`iro_dev_maintenance`）

---

## 1) 用户信息

- **头像与身份展示**（`iro_cover_logo`）
  - 关键项：`personal_avatar`、`text_logo_options`、`text_logo_text`
  - 说明：控制首页头像、身份文案、特效字展示。
- **社交与资料（Legacy）**（`iro_legacy_group_user_profile`）
  - 关键项：`github`、`telegram`、`wechat_id`、`qq_id`
  - 说明：统一社交链接与对外展示资料。

## 2) 注入与自定义代码

- **代码注入（Legacy）**（`iro_legacy_group_custom_code`）
  - 关键项：`site_custom_style`、`site_header_insert`、`footer_addition`
  - 说明：统一管理 CSS / 页头 / 页脚注入类配置。

## 3) 首页展示

- 导航栏（`iro_nav`）
- 主题配色（`iro_color`）
- 首屏外观（`iro_cover_display`）
- 首屏信息栏（`iro_cover_info`）
- 首屏其他（`iro_cover_other`）
- 首页模块布局（`iro_homepages_sections`）
- 展示区（`iro_display_aera`）
- 文章卡片区（`iro_article_aera`）
- 前台背景（`iro_front`）
- 小组件面板（`iro_widgets`）

> 本次已将 `iro_legacy_group_home_display` 合并到 `iro_cover_other`，减少单独层级。

## 4) 文章阅读

- 页面通用（`iro_pages_common`）
- 文章页（`iro_pages_post`）
- 文章扩展（`iro_pages_extra`）

## 5) 评论互动

- 评论区（`iro_pages_comment`）

> 本次已将 `iro_legacy_group_comment_media` 合并到 `iro_pages_comment`。

## 6) 性能加速

- 粒子与动效（`iro_particles`）
- 加载与翻页（`iro_global_others`）

> 本次已将 `iro_legacy_group_performance` 合并到 `iro_global_others`。

## 7) 第三方服务

- 外部服务接入（`iro_legacy_group_third_party_services`）
  - 关键项：`aplayer_server`、`steam_id`、`bilibili_id`、`chatgpt_endpoint`

## 8) 账号安全

- 登录与安全（`iro_legacy_group_auth_admin`）
  - 关键项：`custom_login_switch`、`captcha_select`、`turnstile_site_key`

## 9) 开发维护

- 页脚信息（`iro_footer`）
- 站点基础与维护（`iro_legacy_group_dev_maintenance`）

---

## 字段规范（已落地）

- 布尔值：统一使用 `checkbox`。
- 长文本：按语义使用 `textarea` / `code`。
  - `code`：CSS/JS/HTML/模板/脚本等可执行或结构化代码。
  - `textarea`：说明文案、模板正文、提示词、JSON 文本等。

## Legacy 动态项说明策略

- 每个动态项 description 由 `iro_customizer_legacy_description_from_key()` 生成。
- 说明文案按 key 前缀映射到真实功能域（社交、验证码、统计、注入、图床、更新/CDN 等）。
- 不再使用“用于兼容历史配置”类泛化描述。