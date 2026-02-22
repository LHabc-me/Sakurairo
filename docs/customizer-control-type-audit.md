# Customizer 控件类型审阅（legacy 迁移项）

审阅时间：2026-02-22
范围：`inc/customizer-migrated-fields.php`（约 188 项）对照 `opt/options/theme-options.php` 原始控件类型定义。

## 结论
- 已修复：`iro_captcha_level` 恢复为 slider（0~100, step 1）
- 本轮发现 **36 个潜在风险项**：原定义是 slider/radio/image_select/switcher，但在 legacy 映射中未显式强制类型，依赖值推断，存在误判风险。

## 高优先级（建议下一批优先修）
- `post_cover_options`（radio）
- `statistics_api`（radio）
- `social_display_icon`（image_select）
- `bangumi_source`（image_select）
- `friend_link_align`（image_select）
- `iro_update_source`（image_select）
- `lib_cdn_path`（image_select）
- `theme_darkmode_background_transparency`（slider）
- `aplayer_volume`（slider）
- `social_area_radius`（slider）
- `chatgpt_max_tokens`（slider）
- `chatgpt_api_request_timeout`（slider）
- `time_zone_fix`（slider）

## 其余潜在风险项（按审阅输出）
- reference_exter_font（switcher）
- search_filter（switcher）
- random_graphs_mts（switcher）
- cache_cover（switcher）
- cover_video（switcher）
- social_area（switcher）
- my_anime_list_sort（radio）
- bangumi_cache（switcher）
- friend_link_form（switcher）
- steam_cache（switcher）
- comment_useragent（switcher）
- comment_location（switcher）
- img_upload_max_size（slider）
- admin_notify（switcher）
- login_language_opt（switcher）
- admin_left_style（image_select）
- chatgpt_auto_article_summarize（switcher）
- ghcard_proxy（switcher）
- core_library_basepath（switcher）
- shared_library_basepath（switcher）
- external_vendor_lib（switcher）
- send_theme_version（switcher）

## 备注
- 以上为“潜在风险”清单，不代表当前运行时一定错误。
- 但为了避免环境差异导致推断偏移，建议将这些 key 显式加入 `legacy_force_*` 映射。