# 配置重构映射（旧位置 -> 新位置）

> 范围：`inc/customizer.php` 中原生 section + 历史迁移 key 分组。  
> 原则：只调整菜单信息架构，不改存储 key。

## A. 原生 Section 映射

| 旧面板/分组 | 新面板/分组 |
|---|---|
| `iro_global/iro_nav` | `iro_home_display/iro_nav` |
| `iro_global/iro_color` | `iro_home_display/iro_color` |
| `iro_cover/iro_cover_logo` | `iro_user_profile/iro_cover_logo` |
| `iro_cover/iro_cover_display` | `iro_home_display/iro_cover_display` |
| `iro_cover/iro_cover_info` | `iro_home_display/iro_cover_info` |
| `iro_cover/iro_cover_other` | `iro_home_display/iro_cover_other` |
| `iro_homepage/iro_homepages_sections` | `iro_home_display/iro_homepages_sections` |
| `iro_homepage/iro_display_aera` | `iro_home_display/iro_display_aera` |
| `iro_homepage/iro_article_aera` | `iro_home_display/iro_article_aera` |
| `iro_global/iro_front` | `iro_home_display/iro_front` |
| `iro_global/iro_widgets` | `iro_home_display/iro_widgets` |
| `iro_global/iro_particles` | `iro_performance/iro_particles` |
| `iro_global/iro_global_others` | `iro_performance/iro_global_others` |
| `iro_global/iro_footer` | `iro_dev_maintenance/iro_footer` |
| `iro_pages/iro_pages_common` | `iro_article_reading/iro_pages_common` |
| `iro_pages/iro_pages_post` | `iro_article_reading/iro_pages_post` |
| `iro_pages/iro_pages_extra` | `iro_article_reading/iro_pages_extra` |
| `iro_pages/iro_pages_comment` | `iro_comment_interaction/iro_pages_comment` |

---

## B. 历史 key 分组映射

### B1. 用户信息（`iro_legacy_group_user_profile`）
- 旧：`iro_legacy_group_cover_social`
- 新：`iro_user_profile/iro_legacy_group_user_profile`
- 典型 key：
  - `github`, `telegram`, `wechat_id`, `qq_id`, `email_name`
  - `social_area`, `social_display_icon`, `diysocialicons`, `unlisted_avatar`

### B2. 注入与自定义代码（`iro_legacy_group_custom_code`）
- 旧：分散在 `iro_legacy_group_site_basics` + `iro_legacy_group_dev_runtime`
- 新：`iro_custom_code/iro_legacy_group_custom_code`
- 典型 key：
  - `site_custom_style`
  - `site_header_insert`
  - `footer_addition`

### B3. 首页扩展展示（`iro_legacy_group_home_display`）
- 旧：`iro_legacy_group_cover_social`
- 新：`iro_home_display/iro_legacy_group_home_display`
- 典型 key：`cover_video*`, `random_graphs_*`, `cache_cover`, `post_cover_*`, `exhibition`

### B4. 性能加速（`iro_legacy_group_performance`）
- 旧：`iro_legacy_group_search_loading`
- 新：`iro_performance/iro_legacy_group_performance`
- 典型 key：`search_*`, `preload_*`, `poi_pjax`, `pjax_keep_loading`, `page_lazyload*`

### B5. 第三方服务（`iro_legacy_group_third_party_services`）
- 旧：`iro_legacy_group_third_party_services`
- 新：`iro_third_party/iro_legacy_group_third_party_services`

### B6. 评论互动（`iro_legacy_group_comment_media`）
- 旧：`iro_legacy_group_comment_media`
- 新：`iro_comment_interaction/iro_legacy_group_comment_media`

### B7. 账号安全（`iro_legacy_group_auth_admin`）
- 旧：`iro_legacy_group_auth_admin`
- 新：`iro_account_security/iro_legacy_group_auth_admin`

### B8. 开发维护（`iro_legacy_group_dev_maintenance`）
- 旧：`iro_legacy_group_site_basics` + `iro_legacy_group_dev_runtime`
- 新：`iro_dev_maintenance/iro_legacy_group_dev_maintenance`
- 典型 key：`favicon_link`, `iro_meta_*`, `iro_update_*`, `dev_mode`, `lib_cdn_path`

---

## C. 重点迁移项（本次必查）

1. **用户头像 / 用户 Github 地址**
   - `personal_avatar`：`iro_user_profile/iro_cover_logo`
   - `github`：`iro_user_profile/iro_legacy_group_user_profile`
   - 结果：身份信息归并到同一任务域“用户信息”。

2. **CSS 注入 / HTML 注入**
   - `site_custom_style`
   - `site_header_insert`
   - `footer_addition`
   - 新位置统一到：`iro_custom_code/iro_legacy_group_custom_code`

---

## D. 兼容性说明

- 所有字段继续使用原有 `iro_key`/`iro_subkey`。
- 不新增迁移脚本，不改读取入口；仅更改 Customizer 展示结构。
- 历史 key 自动分桶逻辑保留，默认兜底组为 `iro_legacy_group_dev_maintenance`。
