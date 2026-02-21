# Phase2 M2 Legacy Bridge Grouping Rules

This document explains how M2 fallback controls are grouped in Customizer.

## Source

- Source key list: `inc/customizer-migrated-fields.php`
- Source mapping baseline: `docs/PHASE2_FIELD_MAPPING.md` (`migration_batch = M2` rows)

## Why grouping

Instead of placing all migrated fallback keys in one long section, M2 now splits keys into 7 groups under `iro_global`.

## Group definitions

1. `iro_legacy_group_site_basics`
   - Theme/site basics and SEO
   - Prefix examples: `favicon_`, `iro_meta_`, `theme_`

2. `iro_legacy_group_search_loading`
   - Search and page loading behavior
   - Prefix examples: `search_`, `preload_`, `poi_`, `pjax_`, `page_lazyload`

3. `iro_legacy_group_cover_social`
   - Cover media, social cards and profile links
   - Prefix examples: `cover_`, `social_`, `wechat_`, `qq_`, social account keys (`github`, `twitter`, ...)

4. `iro_legacy_group_third_party_services`
   - External platform/API integrations
   - Prefix examples: `aplayer_`, `bangumi_`, `bilibili_`, `chatgpt_`, `statistics_`

5. `iro_legacy_group_comment_media`
   - Comment, emoticon, media upload and notify related keys
   - Prefix examples: `comment_`, `smilies_`, `img_`, `lsky_`, `mail_`

6. `iro_legacy_group_auth_admin`
   - Login/captcha/admin panel related keys
   - Prefix examples: `login_`, `captcha_`, `turnstile_`, `admin_`

7. `iro_legacy_group_dev_runtime`
   - Runtime/CDN/debug/update and uncategorized fallback
   - Prefix examples: `code_highlight_`, `lib_cdn_`, `iro_update_`, `vision_resource_`

## Matching rule

- Keys are matched by first prefix hit in configured order.
- If no rule matches, key goes to `iro_legacy_group_dev_runtime`.
- Control generation remains auto-derived from current option value type (`switch` / `number` / `textarea` / `text`).
