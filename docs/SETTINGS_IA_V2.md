# SETTINGS IA V2（第二阶段：原生 + Legacy 统一）

## 目标
- Customizer 按用户心智组织：先看“我想调什么”，再看“配置来源”。
- 原生字段与 Legacy Bridge 在同一信息架构下可见，不再割裂。
- 保持兼容：**不改 key、不过滤旧值、不做破坏性迁移**。

## 分组规则
1. **按场景分组**，不按来源分组。
2. Legacy key 统一以 `legacy_{key}` 暴露为 setting id，但底层仍写回 `iro_options[{key}]`。
3. 命名规范：SEO / API / CDN / PJAX / ChatGPT 等术语保持规范大小写。
4. 关键可见项补充 description（小字简介），降低误操作成本。

## 最终结构（Panel -> Section）

### 1) 全局与基础（iro_global）
- 原生：`iro_nav`, `iro_color`, `iro_front`, `iro_widgets`, `iro_particles`, `iro_footer`, `iro_global_others`
- Legacy：
  - `iro_legacy_group_site_basics`（站点身份与 SEO）
  - `iro_legacy_group_dev_runtime`（性能、开发与更新）

### 2) 首页首屏（iro_cover）
- 原生：`iro_cover_logo`, `iro_cover_display`, `iro_cover_info`, `iro_cover_other`
- Legacy：
  - `iro_legacy_group_cover_social`（封面、社交与身份展示）

### 3) 首页内容（iro_homepage）
- 原生：`iro_homepages_sections`, `iro_display_aera`, `iro_article_aera`
- Legacy：
  - `iro_legacy_group_third_party_services`（第三方服务与数据源）

### 4) 页面与互动（iro_pages）
- 原生：`iro_pages_common`, `iro_pages_post`, `iro_pages_extra`, `iro_pages_comment`
- Legacy：
  - `iro_legacy_group_search_loading`（搜索、加载与阅读体验）
  - `iro_legacy_group_comment_media`（评论与媒体上传）
  - `iro_legacy_group_auth_admin`（账号、安全与后台）

## 误分组修正（Phase 2 必做）
- `steam*`：归入 `iro_legacy_group_third_party_services`。
- `bili* / bilibili*`：归入 `iro_legacy_group_third_party_services`。
- `admin_notify`：归入 `iro_legacy_group_auth_admin`。

## 标签友好化
- 使用 key 自动生成友好 label；缩写保留大写。
- 特殊词规则：`ChatGPT`、`Bilibili`、`Bili`、`WeChat`。

## 示例（Panel -> Section -> key）
- 全局与基础 -> 兼容迁移：站点身份与 SEO -> `legacy_iro_meta_description`, `legacy_iro_seo`
- 首页内容 -> 兼容迁移：第三方服务与数据源 -> `legacy_steam_id`, `legacy_bili`, `legacy_chatgpt_model`
- 页面与互动 -> 兼容迁移：账号、安全与后台 -> `legacy_admin_notify`, `legacy_turnstile_site_key`

## 兼容性说明
- 所有现有 `iro_key` 持久化语义保持不变。
- Legacy Bridge 仅改变可见性与分组，不改变底层存储结构。
