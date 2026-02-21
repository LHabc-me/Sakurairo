# Shinonomeiro 配置结构参考（重构版）

> 本文对应 `inc/customizer.php` 当前信息架构。  
> 目标：按用户任务心智组织配置，不改变底层 `iro_options` 的 key 语义。

## 面板总览（9 大任务域）

1. **用户信息**（`iro_user_profile`）
2. **注入与自定义代码**（`iro_custom_code`）
3. **首页展示**（`iro_home_display`）
4. **文章阅读**（`iro_article_reading`）
5. **评论互动**（`iro_comment_interaction`）
6. **性能加速**（`iro_performance`）
7. **第三方服务**（`iro_third_party`）
8. **账号安全**（`iro_account_security`）
9. **开发维护**（`iro_dev_maintenance`）

---

## 1) 用户信息

### 1.1 头像与身份展示（`iro_cover_logo`）
- 示例项：`personal_avatar`、`text_logo_options`、`text_logo_text`
- 用途：管理首页头像、身份文案、特效字展示。

### 1.2 社交与资料（`iro_legacy_group_user_profile`）
- 示例项：`github`、`telegram`、`wechat_id`、`qq_id`、`email_name`
- 用途：统一社交地址与对外资料，避免身份信息分散。

---

## 2) 注入与自定义代码

### 2.1 代码注入（`iro_legacy_group_custom_code`）
- 示例项：`site_custom_style`（CSS）、`site_header_insert`（页头注入）、`footer_addition`（页脚注入）
- 用途：集中管理所有“手写注入类”配置。

---

## 3) 首页展示

### 3.1 导航栏（`iro_nav`）
- 示例项：`choice_of_nav_style`、`nav_menu_font`、`nav_user_menu`

### 3.2 主题配色（`iro_color`）
- 示例项：`theme_skin`、`theme_skin_dark`、`extract_theme_skin_from_cover`

### 3.3 首屏外观（`iro_cover_display`）
- 示例项：`cover_switch`、`cover_full_screen`、`cover_animation`

### 3.4 首屏信息栏（`iro_cover_info`）
- 示例项：`infor_bar`、`signature_text`、`signature_typing_json`

### 3.5 首屏其他（`iro_cover_other`）
- 示例项：`site_bg_as_cover`、`wave_effects`、`drop_down_arrow`

### 3.6 首页模块布局（`iro_homepages_sections`）
- 示例项：`homepage_components`、`static_page_id`、`post_area_title`

### 3.7 展示区（`iro_display_aera`）
- 示例项：`capsule_components`、`stat_announcement_text`

### 3.8 文章卡片区（`iro_article_aera`）
- 示例项：`post_list_design`、`article_meta_displays`、`post_title_font_size`

### 3.9 前台背景（`iro_front`）
- 示例项：`reception_background_img1`、`reception_background_blur`

### 3.10 小组件面板（`iro_widgets`）
- 示例项：`style_menu_radius`、`widget_daynight`、`global_font_2`

### 3.11 首页扩展展示（`iro_legacy_group_home_display`）
- 示例项：`cover_video`、`random_graphs_link`、`post_cover_options`

---

## 4) 文章阅读

### 4.1 页面通用（`iro_pages_common`）
- 示例项：`entry_content_style`、`page_title_animation`

### 4.2 文章页（`iro_pages_post`）
- 示例项：`article_title_font_size`、`article_auto_toc`、`inline_code_background_color`

### 4.3 文章扩展（`iro_pages_extra`）
- 示例项：`article_function`、`article_lincenses`、`author_profile_avatar`

---

## 5) 评论互动

### 5.1 评论区（`iro_pages_comment`）
- 示例项：`comment_area`、`comment_placeholder_text`、`smilies_list`

### 5.2 评论与媒体（`iro_legacy_group_comment_media`）
- 示例项：`comment_private_message`、`img_upload_api`、`mail_notify`

---

## 6) 性能加速

### 6.1 粒子与动效（`iro_particles`）
- 示例项：`sakura_falling_effects`、`particles_effects`、`particles_json`

### 6.2 加载与翻页（`iro_global_others`）
- 示例项：`nprogress_on`、`smoothscroll_option`、`page_auto_load`

### 6.3 搜索与加载策略（`iro_legacy_group_performance`）
- 示例项：`search_filter`、`poi_pjax`、`preload_animation`、`page_lazyload`

---

## 7) 第三方服务

### 7.1 外部服务接入（`iro_legacy_group_third_party_services`）
- 示例项：`aplayer_server`、`steam_id`、`bilibili_id`、`chatgpt_endpoint`

---

## 8) 账号安全

### 8.1 登录与安全（`iro_legacy_group_auth_admin`）
- 示例项：`custom_login_switch`、`captcha_select`、`turnstile_site_key`

---

## 9) 开发维护

### 9.1 页脚信息（`iro_footer`）
- 示例项：`footer_info`、`footer_yiyan`、`hide_theme_info_switch`

### 9.2 站点基础与维护（`iro_legacy_group_dev_maintenance`）
- 示例项：`favicon_link`、`iro_meta_description`、`iro_update_source`、`dev_mode`

---

## 字段类型规范（本次重构约束）

- **布尔开关统一为 `checkbox`**。
- **长文本统一用 `textarea` / `code`**（按语义自动区分）。
- 历史 key 继续使用原始 `iro_key` 保存，读取逻辑保持兼容。

## 备注

- 历史项的 key 分桶与旧位置迁移清单见：`docs/SETTINGS_REFACTOR_MAPPING.md`。
