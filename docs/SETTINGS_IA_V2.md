# SETTINGS IA V2（全量配置重整）

## 目标
- 用户在 Customizer 中可以看到并编辑**全部配置**（含 legacy bridge）。
- 分组按“使用场景/用户心智”组织，而非按技术来源。
- 保持兼容：不删除旧 key，仅做可见性与归类重整。

## 信息架构（IA）v2
### 站点身份
- legacy key 数量：5
- keys: `favicon_link`, `footer_addition`, `iro_meta_description`, `iro_meta_keywords`, `iro_seo`

### 外观样式
- legacy key 数量：11
- keys: `exter_font`, `gfonts_add_name`, `gfonts_api`, `load_out_svg`, `reference_exter_font`, `site_custom_style`, `site_header_insert`, `theme_commemorate_mode`, `theme_darkmode_auto`, `theme_darkmode_background_transparency`, `theme_darkmode_strategy`

### 首页展示
- legacy key 数量：28
- keys: `cover_video`, `cover_video_link`, `cover_video_live`, `cover_video_loop`, `cover_video_title`, `email_domain`, `email_name`, `exhibition`, `post_cover_options`, `qq_avatar_link`, `qq_copy_switch`, `qq_id`, `qq_qrcode`, `qq_qrcode_switch`, `qq_url`, `random_graphs_link`, `random_graphs_link_mobile`, `random_graphs_mts`, `random_graphs_options`, `social_area`, `social_area_radius`, `social_display_icon`, `sticky_pinned_content`, `wechat_copy_switch`, `wechat_id`, `wechat_qrcode`, `wechat_qrcode_switch`, `wechat_url`

### 文章与阅读
- legacy key 数量：15
- keys: `classify_display`, `code_highlight_method`, `code_highlight_prism_autoload_path`, `code_highlight_prism_line_number_all`, `code_highlight_prism_theme_dark`, `code_highlight_prism_theme_light`, `enable_theme_mathjax`, `image_category`, `lightbox`, `lightgallery_option`, `page_lazyload`, `page_lazyload_spinner`, `page_temp_title_font_size`, `pjax_keep_loading`, `poi_pjax`

### 评论互动
- legacy key 数量：9
- keys: `comment_captcha_select`, `comment_image_proxy`, `comment_location`, `comment_private_message`, `comment_useragent`, `mail_notify`, `smilies_dir`, `smilies_name`, `smilies_proxy`

### 媒体与资源
- legacy key 数量：21
- keys: `aplayer_cookie`, `aplayer_order`, `aplayer_playlistid`, `aplayer_preload`, `aplayer_server`, `aplayer_server_proxy`, `aplayer_volume`, `cache_cover`, `chevereto_api_key`, `cheverto_url`, `custom_music_api`, `image_cdn`, `img_upload_api`, `img_upload_max_size`, `imgur_client_id`, `imgur_upload_image_proxy`, `lsky_api_key`, `lsky_url`, `missing_avatars_default`, `missing_images_default`, `smms_client_id`

### 性能与缓存
- legacy key 数量：14
- keys: `cookie_version`, `core_library_basepath`, `external_vendor_lib`, `lib_cdn_path`, `live_search_comment`, `preload_animation`, `preload_animation_color1`, `preload_animation_color2`, `preload_blur`, `search_filter`, `search_for_pages`, `search_for_shuoshuo`, `shared_library_basepath`, `vision_resource_basepath`

### 账号与安全
- legacy key 数量：14
- keys: `admin_notify`, `captcha_select`, `custom_login_switch`, `hide_login_portal`, `iro_captcha_level`, `login_language_opt`, `login_logo_img`, `login_urlskip`, `turnstile_secret_key`, `turnstile_site_key`, `turnstile_theme`, `vaptcha_key`, `vaptcha_scene`, `vaptcha_vid`

### 开发与更新
- legacy key 数量：27
- keys: `admin_background`, `admin_emphasize_color`, `admin_first_class_color`, `admin_left_style`, `admin_second_class_color`, `admin_text_color`, `channel_validate_value`, `clipboard_ref`, `custom_exclude_search_results`, `custom_proxy_address_of_gravatar`, `dev_mode`, `diysocialicons`, `fontawesome_source`, `ghcard_proxy`, `gravatar_proxy`, `iro_update_channel`, `iro_update_source`, `live_search`, `only_admin_can_search_pages`, `php_notice_filter`, `post_cover`, `save_location`, `send_theme_version`, `show_location_in_manage`, `signature_typing_placeholder`, `time_zone_fix`, `unlisted_avatar`

### 第三方数据源
- legacy key 数量：43
- keys: `bangumi_cache`, `bangumi_id`, `bangumi_source`, `bili`, `bilibili_cookie`, `bilibili_id`, `chatgpt_access_token`, `chatgpt_annotations_prompt`, `chatgpt_api_request_timeout`, `chatgpt_auto_article_summarize`, `chatgpt_endpoint`, `chatgpt_exclude_ids`, `chatgpt_init_prompt`, `chatgpt_max_tokens`, `chatgpt_model`, `discord`, `douyin`, `facebook`, `friend_link_align`, `friend_link_form`, `friend_link_order`, `friend_link_sorting_mode`, `github`, `google_analytics_id`, `instagram`, `linkedin`, `my_anime_list_sort`, `my_anime_list_username`, `sina`, `statistics_api`, `statistics_format`, `steam`, `steam_cache`, `steam_covercdn`, `steam_id`, `steam_key`, `steam_store`, `telegram`, `twitter`, `wangyiyun`, `xiaohongshu`, `youtube`, `zhihu`

## 迁移规则（兼容不破坏）
1. **原生 Customizer 字段保持不变**：`settings` 不改名，不迁移存储位置。
2. **Legacy Bridge 可见化**：由 `inc/customizer-migrated-fields.php` 生成的 key，统一以 `legacy_{key}` 作为 Customizer setting id，底层仍写回 `iro_options[{key}]`。
3. **显示分组迁移，不做数据迁移**：仅调整 section 归属与命名，已有值直接透传。
4. **标签生成规则升级**：`SEO/API/CDN/PJAX/AI` 保持大写；`ChatGPT` 使用正确大小写。
5. **误分组纠正**：`steam*`、`bili* / bilibili*` 从“社交展示”迁出，归入“第三方服务”；`admin_notify` 归入“账号与安全/后台”。

## 附录 A：原生 Customizer settings 全量清单
- 共 141 项
- `area_title_font`, `area_title_text_align`, `article_auto_toc`, `article_function`, `article_lincenses`, `article_meta_background_compatible`, `article_meta_displays`, `article_meta_show_in_head`, `article_modified_time`, `article_nextpre`, `article_tag`, `article_title_font_size`, `article_title_line`, `author_profile_avatar`, `author_profile_name`, `author_profile_quote`, `avatar_radius`, `capsule_components`, `choice_of_nav_style`, `comment_area`, `comment_area_image`, `comment_placeholder_text`, `comment_submit_button_text`, `cover_animation`, `cover_animation_time`, `cover_full_screen`, `cover_half_screen_curve`, `cover_random_graphs_switch`, `cover_switch`, `drop_down_arrow`, `drop_down_arrow_color`, `drop_down_arrow_dark_color`, `drop_down_arrow_mobile`, `entry_content_style`, `exhibition_area_icon`, `exhibition_area_title`, `extract_article_highlight_from_feature`, `extract_theme_skin_from_cover`, `footer_direction`, `footer_info`, `footer_load_occupancy`, `footer_sakura`, `footer_text_font`, `footer_upyun`, `footer_yiyan`, `global_default_font`, `global_font_2`, `global_font_size`, `global_font_weight`, `hide_splash_wallpaper_switch`, `hide_theme_info_switch`, `homepage_components`, `homepage_widget_transparency`, `infor_bar`, `infor_bar_style`, `inline_code_background_color`, `inline_code_background_color_in_dark_mode`, `iro_logo`, `iro_widget_daynight`, `iro_widget_font`, `load_in_svg`, `load_nextpage_svg`, `nav_menu_cover_radius`, `nav_menu_font`, `nav_menu_notice`, `nav_menu_search`, `nav_menu_style`, `nav_text_logo_font`, `nav_text_logo_text`, `nav_user_menu`, `nprogress_on`, `page_auto_load`, `page_title_animation`, `page_title_animation_time`, `pagenav_style`, `particles_effects`, `particles_json`, `patternimg`, `personal_avatar`, `post_area_icon`, `post_area_title`, `post_cover_as_bg`, `post_list_card_radius`, `post_list_design`, `post_list_ticket_type`, `post_list_title_radius`, `post_meta_radius`, `post_title_font_size`, `random_graphs_filter`, `reception_background_blur`, `reception_background_heart_shaped`, `reception_background_img1`, `reception_background_img2`, `reception_background_img3`, `reception_background_img4`, `reception_background_img5`, `reception_background_lemon_shaped`, `reception_background_size`, `reception_background_square_shaped`, `reception_background_star_shaped`, `reception_background_transparency`, `reward_area_image1`, `reward_area_image2`, `reward_area_link`, `reward_area_link1`, `reward_area_link2`, `sakura_falling_effects`, `sakura_nav_style_distribution`, `sakura_nav_style_option_spacing`, `sakura_nav_style_style`, `sakura_widget`, `search_area_background`, `show_medal_capsules`, `show_shuoshuo_on_home_page`, `signature_font`, `signature_font_size`, `signature_radius`, `signature_text`, `signature_typing`, `signature_typing_json`, `signature_typing_marks`, `site_bg_as_cover`, `smilies_list`, `smoothscroll_option`, `stat_announcement_text`, `static_page_id`, `style_menu_font`, `style_menu_radius`, `style_menu_selection_radius`, `text_logo_color`, `text_logo_font`, `text_logo_options`, `text_logo_size`, `text_logo_text`, `theme_darkmode_img_bright`, `theme_darkmode_widget_transparency`, `theme_skin`, `theme_skin_dark`, `theme_skin_matching`, `wave_effects`, `yiyan_api`

## 附录 B：Legacy Bridge keys 全量清单
- 共 187 项
- `admin_background`, `admin_emphasize_color`, `admin_first_class_color`, `admin_left_style`, `admin_notify`, `admin_second_class_color`, `admin_text_color`, `aplayer_cookie`, `aplayer_order`, `aplayer_playlistid`, `aplayer_preload`, `aplayer_server`, `aplayer_server_proxy`, `aplayer_volume`, `bangumi_cache`, `bangumi_id`, `bangumi_source`, `bili`, `bilibili_cookie`, `bilibili_id`, `cache_cover`, `captcha_select`, `channel_validate_value`, `chatgpt_access_token`, `chatgpt_annotations_prompt`, `chatgpt_api_request_timeout`, `chatgpt_auto_article_summarize`, `chatgpt_endpoint`, `chatgpt_exclude_ids`, `chatgpt_init_prompt`, `chatgpt_max_tokens`, `chatgpt_model`, `chevereto_api_key`, `cheverto_url`, `classify_display`, `clipboard_ref`, `code_highlight_method`, `code_highlight_prism_autoload_path`, `code_highlight_prism_line_number_all`, `code_highlight_prism_theme_dark`, `code_highlight_prism_theme_light`, `comment_captcha_select`, `comment_image_proxy`, `comment_location`, `comment_private_message`, `comment_useragent`, `cookie_version`, `core_library_basepath`, `cover_video`, `cover_video_link`, `cover_video_live`, `cover_video_loop`, `cover_video_title`, `custom_exclude_search_results`, `custom_login_switch`, `custom_music_api`, `custom_proxy_address_of_gravatar`, `dev_mode`, `discord`, `diysocialicons`, `douyin`, `email_domain`, `email_name`, `enable_theme_mathjax`, `exhibition`, `exter_font`, `external_vendor_lib`, `facebook`, `favicon_link`, `fontawesome_source`, `footer_addition`, `friend_link_align`, `friend_link_form`, `friend_link_order`, `friend_link_sorting_mode`, `gfonts_add_name`, `gfonts_api`, `ghcard_proxy`, `github`, `google_analytics_id`, `gravatar_proxy`, `hide_login_portal`, `image_category`, `image_cdn`, `img_upload_api`, `img_upload_max_size`, `imgur_client_id`, `imgur_upload_image_proxy`, `instagram`, `iro_captcha_level`, `iro_meta_description`, `iro_meta_keywords`, `iro_seo`, `iro_update_channel`, `iro_update_source`, `lib_cdn_path`, `lightbox`, `lightgallery_option`, `linkedin`, `live_search`, `live_search_comment`, `load_out_svg`, `login_language_opt`, `login_logo_img`, `login_urlskip`, `lsky_api_key`, `lsky_url`, `mail_notify`, `missing_avatars_default`, `missing_images_default`, `my_anime_list_sort`, `my_anime_list_username`, `only_admin_can_search_pages`, `page_lazyload`, `page_lazyload_spinner`, `page_temp_title_font_size`, `php_notice_filter`, `pjax_keep_loading`, `poi_pjax`, `post_cover`, `post_cover_options`, `preload_animation`, `preload_animation_color1`, `preload_animation_color2`, `preload_blur`, `qq_avatar_link`, `qq_copy_switch`, `qq_id`, `qq_qrcode`, `qq_qrcode_switch`, `qq_url`, `random_graphs_link`, `random_graphs_link_mobile`, `random_graphs_mts`, `random_graphs_options`, `reference_exter_font`, `save_location`, `search_filter`, `search_for_pages`, `search_for_shuoshuo`, `send_theme_version`, `shared_library_basepath`, `show_location_in_manage`, `signature_typing_placeholder`, `sina`, `site_custom_style`, `site_header_insert`, `smilies_dir`, `smilies_name`, `smilies_proxy`, `smms_client_id`, `social_area`, `social_area_radius`, `social_display_icon`, `statistics_api`, `statistics_format`, `steam`, `steam_cache`, `steam_covercdn`, `steam_id`, `steam_key`, `steam_store`, `sticky_pinned_content`, `telegram`, `theme_commemorate_mode`, `theme_darkmode_auto`, `theme_darkmode_background_transparency`, `theme_darkmode_strategy`, `time_zone_fix`, `turnstile_secret_key`, `turnstile_site_key`, `turnstile_theme`, `twitter`, `unlisted_avatar`, `vaptcha_key`, `vaptcha_scene`, `vaptcha_vid`, `vision_resource_basepath`, `wangyiyun`, `wechat_copy_switch`, `wechat_id`, `wechat_qrcode`, `wechat_qrcode_switch`, `wechat_url`, `xiaohongshu`, `youtube`, `zhihu`