# CONFIG 全量审计（337 项）

- 审计范围：static + legacy（排除说明型字段 `nav_menu_notice`）
- 总计：337（static 150 + legacy 187）
- 审计基线文件：`inc/customizer.php` + `inc/customizer-migrated-fields.php`

| # | key | label | description | type | 是否修改 |
|---:|---|---|---|---|---|
| 1 | choice_of_nav_style | 导航菜单样式 | - | radio | 否 |
| 2 | nav_menu_style | Spirit Island Nav Style | - | select | 否 |
| 3 | nav_menu_cover_radius | 导航菜单圆角 | - | slider | 否 |
| 4 | sakura_nav_style_style | Classic Nav Style | - | select | 否 |
| 5 | sakura_nav_style_distribution | Nav Menu Options Display Method | - | select | 否 |
| 6 | sakura_nav_style_option_spacing | Menu option left and right spacing | - | slider | 否 |
| 7 | nav_menu_font | 导航菜单字体 | - | text | 否 |
| 8 | iro_logo | Navigation Menu Logo | - | image | 否 |
| 9 | nav_text_logo_text | 导航菜单文本Logo文本 | - | text | 否 |
| 10 | nav_text_logo_font | 导航菜单文本Logo字体 | - | text | 否 |
| 11 | cover_random_graphs_switch | Switch Button of Random Images | - | checkbox | 否 |
| 12 | nav_user_menu | Nav User Menu | 默认开启。启用后会在导航栏显示用户头像与用户菜单。 | checkbox | 否 |
| 13 | nav_menu_search | 导航菜单搜索 | - | checkbox | 否 |
| 14 | search_area_background | Search Area Background Image | - | image | 否 |
| 15 | extract_theme_skin_from_cover | Extract Theme Color from Cover Image | 启用后，主题主色将从首页封面图自动提取。 | checkbox | 否 |
| 16 | extract_article_highlight_from_feature | Extract Article Highlight from Featured Image | 启用后，文章页配色将从文章特色图自动提取。 | checkbox | 否 |
| 17 | theme_skin | 主题色 | - | color | 否 |
| 18 | theme_skin_matching | 主题配色 | - | color | 否 |
| 19 | theme_skin_dark | 深色模式主题色 | - | color | 否 |
| 20 | theme_darkmode_img_bright | 深色模式图片亮度 | - | slider | 否 |
| 21 | theme_darkmode_widget_transparency | 深色模式组件透明度 | - | slider | 否 |
| 22 | personal_avatar | Cover Personal Avatar | - | image | 否 |
| 23 | text_logo_options | Enable Mashiro Special Effects Text | 启用后，将使用该设置替换首页展示头像。 | checkbox | 否 |
| 24 | text_logo_text | Mashiro Special Effects Text | - | text | 否 |
| 25 | text_logo_color | Mashiro Special Effects Text Color | - | color | 否 |
| 26 | text_logo_font | Mashiro Special Effects Font | - | text | 否 |
| 27 | text_logo_size | Mashiro Special Effects Size | - | slider | 否 |
| 28 | cover_switch | 启用首屏 | - | checkbox | 否 |
| 29 | cover_full_screen | 首屏全屏显示 | - | checkbox | 否 |
| 30 | random_graphs_filter | Cover Random Images Filter | - | select | 否 |
| 31 | cover_half_screen_curve | Cover Arc Occlusion (Below) | - | checkbox | 否 |
| 32 | cover_animation | 首屏动画 | - | checkbox | 否 |
| 33 | cover_animation_time | 首屏动画时长 | - | slider | 否 |
| 34 | hide_splash_wallpaper_switch | 隐藏开屏壁纸 | 启用后，仅隐藏首页开屏壁纸与相关效果，不移除容器结构。 | checkbox | 否 |
| 35 | infor_bar | 首屏信息栏 | - | checkbox | 否 |
| 36 | infor_bar_style | 首屏信息栏样式 | - | radio | 否 |
| 37 | homepage_widget_transparency | 首屏组件透明度 | - | slider | 否 |
| 38 | avatar_radius | 首屏信息栏头像圆角 | - | slider | 否 |
| 39 | signature_radius | Cover Info Bar Rounded | - | slider | 否 |
| 40 | signature_text | 首屏签名文本 | - | text | 否 |
| 41 | cover_daily_poetry | 启用首屏今日诗词 | 开启后，首屏签名文本将替换为今日诗词展示。 | checkbox | 否 |
| 42 | cover_daily_poetry_timeout | 诗词接口超时（毫秒） | 建议保持 5000 毫秒，网络较慢时可适当调大。 | slider | 否 |
| 43 | cover_poetry_light_color | 诗词日间颜色 | - | color | 否 |
| 44 | cover_poetry_dark_color | 诗词夜间颜色 | - | color | 否 |
| 45 | cover_poetry_author_offset | 作者行偏移 | - | text | 否 |
| 46 | cover_poetry_font_size | 诗词字号 | - | text | 否 |
| 47 | cover_poetry_author_font_size | 作者行字号 | - | text | 否 |
| 48 | cover_poetry_font_family | 诗词字体族 | - | text | 否 |
| 49 | signature_font | 首屏签名字体 | - | text | 否 |
| 50 | signature_font_size | 首屏签名字号 | - | slider | 否 |
| 51 | signature_typing | Cover Signature Bar Typing Effects | - | checkbox | 否 |
| 52 | signature_typing_marks | Cover Signature Field Typing Effects Double Quotes | - | checkbox | 否 |
| 53 | signature_typing_json | Typed.js initial option | - | code | 否 |
| 54 | site_bg_as_cover | Cover and Frontend Background Integration | 启用后，封面背景设为透明，前台背景将调用封面随机图 API。 | checkbox | 否 |
| 55 | post_cover_as_bg | Post Cover As Background | 启用后，文章页将使用文章特色图作为背景。 | checkbox | 否 |
| 56 | wave_effects | Cover Wave Effects | 深色模式下会被强制关闭。 | checkbox | 否 |
| 57 | drop_down_arrow | Cover Dropdown Arrow | - | checkbox | 否 |
| 58 | drop_down_arrow_mobile | Cover Dropdown Arrow Display on Mobile Devices | - | checkbox | 否 |
| 59 | drop_down_arrow_color | Cover Dropdown Arrow Color | - | color | 否 |
| 60 | drop_down_arrow_dark_color | Cover Dropdown Arrow Color (Dark Mode) | - | color | 否 |
| 61 | legacy_random_graphs_options | 随机封面图策略 | 用于配置首页随机封面图的来源、数量与切换策略。 | text | 是 |
| 62 | legacy_random_graphs_mts | 随机封面图数量 | 用于配置首页随机封面图的来源、数量与切换策略。 | text | 是 |
| 63 | legacy_random_graphs_link | 随机封面图接口地址 | 用于配置首页随机封面图的来源、数量与切换策略。 | text | 是 |
| 64 | legacy_random_graphs_link_mobile | 随机封面图移动端接口地址 | 用于配置首页随机封面图的来源、数量与切换策略。 | text | 是 |
| 65 | legacy_cache_cover | 封面图缓存 | 用于配置封面图缓存策略与刷新行为。 | text | 是 |
| 66 | legacy_cover_video | 封面视频开关 | 用于配置 封面视频开关 相关功能。 | text | 是 |
| 67 | legacy_cover_video_loop | 封面视频循环播放 | 用于配置 封面视频循环播放 相关功能。 | checkbox | 是 |
| 68 | legacy_cover_video_live | 封面视频直播模式 | 用于配置 封面视频直播模式 相关功能。 | checkbox | 是 |
| 69 | legacy_cover_video_link | 封面视频链接 | 用于配置 封面视频链接 相关功能。 | text | 是 |
| 70 | legacy_cover_video_title | 封面视频标题 | 用于配置 封面视频标题 相关功能。 | text | 是 |
| 71 | legacy_exhibition | 展示位 | 用于配置首页展示位内容与显示规则。 | text | 是 |
| 72 | legacy_post_cover_options | 文章封面策略 | 用于配置 文章封面策略 相关功能。 | text | 是 |
| 73 | homepage_components | Homepage Components | - | sortable | 否 |
| 74 | static_page_id | Select a page | - | dropdown_pages | 否 |
| 75 | exhibition_area_icon | Display Area Icon | - | text | 否 |
| 76 | exhibition_area_title | Display Area Title | - | text | 否 |
| 77 | post_area_icon | Post Area Icon | - | text | 否 |
| 78 | post_area_title | Post Area Title | - | text | 否 |
| 79 | area_title_font | Area Title Font | - | text | 否 |
| 80 | area_title_text_align | Area Title Alignment | - | radio | 否 |
| 81 | capsule_components | Capsule Components | - | sortable | 否 |
| 82 | show_medal_capsules | Show Medal Badges Style Capsule | 启用后会显示博客里程碑铜/银/金徽章；需先解锁对应成就，才会替换对应展示卡片。 | checkbox | 否 |
| 83 | stat_announcement_text | Announcement Text | 设置公告胶囊文案。前台会自动拆分为两行，也可手动换行。 | textarea | 否 |
| 84 | article_meta_displays | Article Area Meta Displays | - | select | 否 |
| 85 | post_list_design | Article Area Card Design | - | radio | 否 |
| 86 | post_list_ticket_type | Article Area Ticket Layout | - | radio | 否 |
| 87 | article_meta_background_compatible | Article Area Card Information Meta Background Compatible | - | checkbox | 否 |
| 88 | show_shuoshuo_on_home_page | Show shuoshuo on home page | - | checkbox | 否 |
| 89 | post_meta_radius | Article Area Card Information Meta Rounded Corners | - | slider | 否 |
| 90 | post_list_title_radius | Article Area Card Title Meta Rounded Corners | - | slider | 否 |
| 91 | post_list_card_radius | Article Area Card Rounded Corners | - | slider | 否 |
| 92 | post_title_font_size | Article Area Title Font Size | - | slider | 否 |
| 93 | reception_background_img1 | Default Frontend Background | - | image | 否 |
| 94 | reception_background_transparency | Background Transparency in the Frontend | - | slider | 否 |
| 95 | reception_background_blur | Background Transparency Blur | - | checkbox | 否 |
| 96 | reception_background_size | Frontend Background Scaling Method | - | select | 否 |
| 97 | global_default_font | Global Default Font | 填写字体名称后，可在“Shinonomeiro Options -> 全局设置 -> 字体设置”中添加自定义字体。 | text | 否 |
| 98 | global_font_weight | Non-Emphasis Text Weight | - | slider | 否 |
| 99 | global_font_size | Global Font Size | - | slider | 否 |
| 100 | style_menu_radius | Widgets Panel Button Radius | - | slider | 否 |
| 101 | style_menu_selection_radius | Widgets Panel Widget Radius | - | slider | 否 |
| 102 | style_menu_font | Widgets Panel Font | - | text | 否 |
| 103 | sakura_widget | Widgets Panel WP Widget Area | - | checkbox | 否 |
| 104 | iro_widget_daynight | Widgets Panel Day&Night Switching | - | checkbox | 否 |
| 105 | iro_widget_font | Widgets Panel Font Switching | - | checkbox | 否 |
| 106 | global_default_font_widgets | Global Default Font&Widgets Panel Font Switching A | - | text | 否 |
| 107 | global_font_2 | Widgets Panel Font Switching B | - | text | 否 |
| 108 | reception_background_heart_shaped | ♡Option Switcher | - | checkbox | 否 |
| 109 | reception_background_img2 | ♡Corresponding Background | - | image | 否 |
| 110 | reception_background_star_shaped | ☆Option Switcher | - | checkbox | 否 |
| 111 | reception_background_img3 | ☆Corresponding Background | - | image | 否 |
| 112 | reception_background_square_shaped | □Option Switcher | - | checkbox | 否 |
| 113 | reception_background_img4 | □Corresponding Background | - | image | 否 |
| 114 | reception_background_lemon_shaped | 🍋Option Switcher | - | checkbox | 否 |
| 115 | reception_background_img5 | 🍋Corresponding Background | - | image | 否 |
| 116 | sakura_falling_effects | Sakura Falling Effects | - | select | 否 |
| 117 | particles_effects | Particles Effects | - | checkbox | 否 |
| 118 | particles_json | Particles JSON | 粒子参数可参考 https://vincentgarreau.com/particles.js/ 文档。 | code | 否 |
| 119 | footer_direction | Footer Content Distribution | - | select | 否 |
| 120 | footer_sakura | Footer Sakura Icon | - | checkbox | 否 |
| 121 | footer_info | Footer Info | - | code | 否 |
| 122 | footer_text_font | Footer Text Font | - | text | 否 |
| 123 | footer_load_occupancy | Footer Load Occupancy Query | - | checkbox | 否 |
| 124 | footer_upyun | Footer Upyun League Logo | - | checkbox | 否 |
| 125 | footer_yiyan | Footer Hitokoto | - | checkbox | 否 |
| 126 | yiyan_api | Hitokoto API address | - | code | 否 |
| 127 | hide_theme_info_switch | 隐藏主题信息 | 隐藏页脚中的主题署名信息块（Theme Shinonomeiro By LHabc）。 | checkbox | 否 |
| 128 | nprogress_on | NProgress Loading Progress Bar | 默认开启。页面加载时会显示顶部进度条。 | checkbox | 否 |
| 129 | smoothscroll_option | Global Smooth Scroll | 默认开启。启用后页面滚动更平滑。 | checkbox | 否 |
| 130 | pagenav_style | Pagination Mode | - | select | 否 |
| 131 | page_auto_load | Next Page Auto Load | - | select | 否 |
| 132 | load_nextpage_svg | Placeholder SVG when loading the next page | - | image | 否 |
| 133 | legacy_search_filter | 搜索筛选 | 用于配置 搜索筛选 相关功能。 | text | 是 |
| 134 | legacy_search_for_shuoshuo | 搜索说说 | 用于配置 搜索说说 相关功能。 | checkbox | 是 |
| 135 | legacy_search_for_pages | 搜索页面 | 用于配置 搜索页面 相关功能。 | checkbox | 是 |
| 136 | legacy_only_admin_can_search_pages | 仅后台可搜索页面 | 后台外观与管理页提示相关配置。 | checkbox | 是 |
| 137 | legacy_sticky_pinned_content | 置顶内容 | 用于配置 置顶内容 相关功能。 | checkbox | 是 |
| 138 | legacy_custom_exclude_search_results | 自定义排除搜索结果 | 用于配置 自定义排除搜索结果 相关功能。 | text | 是 |
| 139 | legacy_live_search | 实时搜索 | 用于配置 实时搜索 相关功能。 | checkbox | 是 |
| 140 | legacy_live_search_comment | 实时搜索评论 | 用于配置 实时搜索评论 相关功能。 | checkbox | 是 |
| 141 | legacy_preload_animation | 预加载动画 | 用于配置 预加载动画 相关功能。 | checkbox | 是 |
| 142 | legacy_preload_animation_color1 | 预加载动画颜色1 | 用于配置 预加载动画颜色1 相关功能。 | text | 是 |
| 143 | legacy_preload_animation_color2 | 预加载动画颜色2 | 用于配置 预加载动画颜色2 相关功能。 | text | 是 |
| 144 | legacy_preload_blur | 预加载模糊效果 | 用于配置 预加载模糊效果 相关功能。 | text | 是 |
| 145 | legacy_poi_pjax | 启用PJAX过渡 | 用于配置 启用PJAX过渡 相关功能。 | checkbox | 是 |
| 146 | legacy_pjax_keep_loading | PJAX保持加载动画 | 用于配置 PJAX保持加载动画 相关功能。 | checkbox | 是 |
| 147 | legacy_missing_avatars_default | 缺失头像默认图 | 用于配置 缺失头像默认图 相关功能。 | text | 是 |
| 148 | legacy_missing_images_default | 缺失图片默认图 | 用于配置 缺失图片默认图 相关功能。 | text | 是 |
| 149 | legacy_clipboard_ref | 剪贴板引用 | 用于配置 剪贴板引用 相关功能。 | checkbox | 是 |
| 150 | legacy_page_lazyload | page懒加载 | 用于配置 page懒加载 相关功能。 | checkbox | 是 |
| 151 | legacy_page_lazyload_spinner | page懒加载加载动画 | 用于配置 page懒加载加载动画 相关功能。 | text | 是 |
| 152 | entry_content_style | Page Layout Style | - | radio | 否 |
| 153 | patternimg | Page Decoration Image | - | checkbox | 否 |
| 154 | page_title_animation | Page Title Animation | - | checkbox | 否 |
| 155 | page_title_animation_time | Page Title Animation Time | - | slider | 否 |
| 156 | load_in_svg | Page Image Placeholder SVG | - | image | 否 |
| 157 | article_title_font_size | Article Page Title Font Size | 该选项仅对设置了封面的文章生效。 | slider | 否 |
| 158 | article_title_line | Article Page Title Underline Animation | - | checkbox | 否 |
| 159 | article_meta_show_in_head | Article Page Meta Displays | - | select | 否 |
| 160 | article_auto_toc | Article Page Auto Show Menu | - | checkbox | 否 |
| 161 | inline_code_background_color | Inline Code Background Color | - | color | 否 |
| 162 | inline_code_background_color_in_dark_mode | Inline Code Background Color In Dark Mode | - | color | 否 |
| 163 | article_function | Article Page Function Bar | - | checkbox | 否 |
| 164 | article_lincenses | Article License | - | select | 否 |
| 165 | reward_area_link | Reward Button Link | 点击“赞赏”按钮后将跳转到此链接。 | text | 否 |
| 166 | reward_area_image1 | Reward Image A | - | image | 否 |
| 167 | reward_area_link1 | Reward Image Link A | 点击图片后将跳转到此链接。 | text | 否 |
| 168 | reward_area_image2 | Reward Image B | - | image | 否 |
| 169 | reward_area_link2 | Reward Image Link B | 点击图片后将跳转到此链接。 | text | 否 |
| 170 | author_profile_avatar | Article Page Author Avatar | - | checkbox | 否 |
| 171 | author_profile_name | Article Page Author Name | - | checkbox | 否 |
| 172 | author_profile_quote | Article Page Author Signature | - | checkbox | 否 |
| 173 | article_modified_time | Article Last Update Time | - | checkbox | 否 |
| 174 | article_tag | Article Tag | - | checkbox | 否 |
| 175 | article_nextpre | Article Page Prev/Next Article Switcher | - | checkbox | 否 |
| 176 | comment_area | Page Comment Area Display | - | radio | 否 |
| 177 | comment_placeholder_text | Custom CommentBox Placeholder | - | text | 否 |
| 178 | comment_submit_button_text | Custom Submit Button Content | - | text | 否 |
| 179 | comment_area_image | Page Comment Area Bottom Right Background Image | - | image | 否 |
| 180 | smilies_list | Comment Area Emoticon | 请前往后台配置自定义表情包。 | select | 否 |
| 181 | comment_area_notice |  | - | custom | 否 |
| 182 | legacy_smilies_name | 表情包名称 | 评论区表情包来源与启用策略。 | text | 是 |
| 183 | legacy_smilies_dir | 表情包目录 | 评论区表情包来源与启用策略。 | text | 是 |
| 184 | legacy_smilies_proxy | 表情包代理 | 评论区表情包来源与启用策略。 | text | 是 |
| 185 | legacy_comment_useragent | 评论用户代理 | 评论区交互逻辑、提示文案与行为开关。 | text | 是 |
| 186 | legacy_comment_location | 评论地理位置 | 评论区交互逻辑、提示文案与行为开关。 | text | 是 |
| 187 | legacy_comment_private_message | 评论私信消息 | 评论区交互逻辑、提示文案与行为开关。 | checkbox | 是 |
| 188 | legacy_comment_captcha_select | 评论验证码提供商 | 验证码服务参数（开关、站点密钥、校验地址）。 | select | 是 |
| 189 | legacy_img_upload_api | 图片上传接口 | 用于配置 图片上传接口 相关功能。 | text | 是 |
| 190 | legacy_img_upload_max_size | 图片上传最大大小 | 用于配置 图片上传最大大小 相关功能。 | text | 是 |
| 191 | legacy_imgur_client_id | ImgurclientID | Imgur 图床上传接口与鉴权参数。 | text | 是 |
| 192 | legacy_imgur_upload_image_proxy | Imgur上传图片代理 | Imgur 图床上传接口与鉴权参数。 | text | 是 |
| 193 | legacy_smms_client_id | SM.MSclientID | SM.MS 图床上传接口与鉴权参数。 | text | 是 |
| 194 | legacy_chevereto_api_key | Chevereto接口密钥 | Chevereto 图床上传接口配置。 | text | 是 |
| 195 | legacy_cheverto_url | Chevereto链接 | Chevereto 图床上传接口配置。 | text | 是 |
| 196 | legacy_lsky_api_key | Lsky接口密钥 | 用于配置 Lsky接口密钥 相关功能。 | text | 是 |
| 197 | legacy_lsky_url | Lsky链接 | 用于配置 Lsky链接 相关功能。 | text | 是 |
| 198 | legacy_comment_image_proxy | 评论图片代理 | 评论区交互逻辑、提示文案与行为开关。 | text | 是 |
| 199 | legacy_mail_notify | 邮件通知 | 用于配置 邮件通知 相关功能。 | checkbox | 是 |
| 200 | legacy_unlisted_avatar | 未列出头像 | 用于配置 未列出头像 相关功能。 | text | 是 |
| 201 | legacy_social_area | 社交区域 | 社交账号链接或展示名称，用于用户资料卡与页脚社交入口。 | text | 是 |
| 202 | legacy_social_display_icon | 社交显示图标 | 社交账号链接或展示名称，用于用户资料卡与页脚社交入口。 | text | 是 |
| 203 | legacy_social_area_radius | 社交区域圆角 | 社交账号链接或展示名称，用于用户资料卡与页脚社交入口。 | text | 是 |
| 204 | legacy_wechat_qrcode_switch | 微信二维码开关 | 微信账号信息或二维码资源地址。 | checkbox | 是 |
| 205 | legacy_wechat_qrcode | 微信二维码 | 微信账号信息或二维码资源地址。 | text | 是 |
| 206 | legacy_wechat_id | 微信ID | 微信账号信息或二维码资源地址。 | text | 是 |
| 207 | legacy_wechat_copy_switch | 微信复制开关 | 微信账号信息或二维码资源地址。 | checkbox | 是 |
| 208 | legacy_wechat_url | 微信链接 | 微信账号信息或二维码资源地址。 | text | 是 |
| 209 | legacy_qq_qrcode_switch | 显示QQ二维码 | 用于设置社交卡片中的 QQ 二维码图片链接。 | checkbox | 是 |
| 210 | legacy_qq_qrcode | QQ二维码链接 | 用于设置社交卡片中的 QQ 二维码图片链接。 | text | 是 |
| 211 | legacy_qq_id | QQID | 用于设置社交卡片中展示的 QQ 账号。 | text | 是 |
| 212 | legacy_qq_copy_switch | 显示QQ一键复制按钮 | 用于设置社交卡片中展示的 QQ 账号。 | checkbox | 是 |
| 213 | legacy_qq_url | QQ链接 | 用于设置社交卡片中展示的 QQ 账号。 | text | 是 |
| 214 | legacy_wangyiyun | 网易云 | 用于配置 网易云 相关功能。 | text | 是 |
| 215 | legacy_sina | 微博 | 用于配置 微博 相关功能。 | text | 是 |
| 216 | legacy_github | GitHub | 用于配置 GitHub 相关功能。 | text | 是 |
| 217 | legacy_telegram | Telegram | 用于配置 Telegram 相关功能。 | text | 是 |
| 218 | legacy_youtube | YouTube | 用于配置 YouTube 相关功能。 | text | 是 |
| 219 | legacy_instagram | Instagram | 用于配置 Instagram 相关功能。 | text | 是 |
| 220 | legacy_douyin | 抖音 | 用于配置 抖音 相关功能。 | text | 是 |
| 221 | legacy_xiaohongshu | 小红书 | 用于配置 小红书 相关功能。 | text | 是 |
| 222 | legacy_discord | Discord | 用于配置 Discord 相关功能。 | text | 是 |
| 223 | legacy_zhihu | 知乎 | 用于配置 知乎 相关功能。 | text | 是 |
| 224 | legacy_linkedin | LinkedIn | 用于配置 LinkedIn 相关功能。 | text | 是 |
| 225 | legacy_twitter | X/Twitter | 用于配置 X/Twitter 相关功能。 | text | 是 |
| 226 | legacy_facebook | Facebook | 用于配置 Facebook 相关功能。 | text | 是 |
| 227 | legacy_email_name | 邮箱名称 | 用于配置 邮箱名称 相关功能。 | text | 是 |
| 228 | legacy_email_domain | 邮箱域名 | 用于配置 邮箱域名 相关功能。 | text | 是 |
| 229 | legacy_diysocialicons | 自定义社交图标 | 用于配置 自定义社交图标 相关功能。 | text | 是 |
| 230 | legacy_qq_avatar_link | QQ头像链接 | 用于设置社交卡片中展示的 QQ 账号。 | text | 是 |
| 231 | legacy_footer_addition | 页脚附加内容 | 注入到页脚区域的附加 HTML/脚本内容。 | code | 是 |
| 232 | legacy_site_custom_style | 站点自定义样式 | 自定义 CSS 样式代码，保存后会输出到前台全局样式。 | code | 是 |
| 233 | legacy_site_header_insert | 站点页头插入 | 注入到站点 <head> 的 HTML/脚本片段（如验证代码、统计脚本）。 | code | 是 |
| 234 | legacy_aplayer_server | APlayer服务端 | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 235 | legacy_custom_music_api | 自定义音乐接口 | 用于配置 自定义音乐接口 相关功能。 | text | 是 |
| 236 | legacy_aplayer_server_proxy | APlayer服务端代理 | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 237 | legacy_aplayer_playlistid | APlayer歌单 ID | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 238 | legacy_aplayer_order | APlayer排序 | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 239 | legacy_aplayer_preload | APlayer预加载 | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 240 | legacy_aplayer_volume | APlayer音量 | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 241 | legacy_aplayer_cookie | APlayerCookie | APlayer 播放器启用状态、歌单来源与展示选项。 | text | 是 |
| 242 | legacy_bili | bili | 用于配置 bili 相关功能。 | text | 是 |
| 243 | legacy_steam | Steam | 用于配置 Steam 相关功能。 | text | 是 |
| 244 | legacy_bangumi_source | 番剧来源 | 番剧数据源与展示组件配置。 | text | 是 |
| 245 | legacy_my_anime_list_username | 我的动漫列表用户名 | 用于配置 我的动漫列表用户名 相关功能。 | text | 是 |
| 246 | legacy_my_anime_list_sort | 我的动漫列表排序 | 用于配置 我的动漫列表排序 相关功能。 | text | 是 |
| 247 | legacy_bilibili_id | BilibiliID | Bilibili 信息展示、跳转或统计参数。 | text | 是 |
| 248 | legacy_bilibili_cookie | BilibiliCookie | Bilibili 信息展示、跳转或统计参数。 | text | 是 |
| 249 | legacy_bangumi_id | 番剧ID | 番剧数据源与展示组件配置。 | text | 是 |
| 250 | legacy_bangumi_cache | 番剧缓存 | 番剧数据源与展示组件配置。 | text | 是 |
| 251 | legacy_friend_link_align | 友链链接对齐 | 用于配置 友链链接对齐 相关功能。 | text | 是 |
| 252 | legacy_friend_link_form | 友链链接表单 | 用于配置 友链链接表单 相关功能。 | text | 是 |
| 253 | legacy_friend_link_sorting_mode | 友链链接排序模式 | 用于配置 友链链接排序模式 相关功能。 | text | 是 |
| 254 | legacy_friend_link_order | 友链链接排序 | 用于配置 友链链接排序 相关功能。 | text | 是 |
| 255 | legacy_steam_id | SteamID | Steam 玩家信息或游戏展示配置。 | text | 是 |
| 256 | legacy_steam_key | Steam密钥 | Steam 玩家信息或游戏展示配置。 | text | 是 |
| 257 | legacy_steam_covercdn | Steamcovercdn | Steam 玩家信息或游戏展示配置。 | text | 是 |
| 258 | legacy_steam_store | Steam商店 | Steam 玩家信息或游戏展示配置。 | text | 是 |
| 259 | legacy_steam_cache | Steam缓存 | Steam 玩家信息或游戏展示配置。 | text | 是 |
| 260 | legacy_chatgpt_endpoint | ChatGPT 接口地址 | 用于配置 ChatGPT 接口地址 相关功能。 | text | 是 |
| 261 | legacy_chatgpt_access_token | ChatGPT 访问令牌 | 用于配置 ChatGPT 访问令牌 相关功能。 | text | 是 |
| 262 | legacy_chatgpt_max_tokens | ChatGPT 最大 Token | 用于配置 ChatGPT 最大 Token 相关功能。 | text | 是 |
| 263 | legacy_chatgpt_model | ChatGPT 模型 | 用于配置 ChatGPT 模型 相关功能。 | text | 是 |
| 264 | legacy_chatgpt_api_request_timeout | ChatGPT 请求超时 | 用于配置 ChatGPT 请求超时 相关功能。 | text | 是 |
| 265 | legacy_chatgpt_auto_article_summarize | ChatGPT自动文章摘要 | 用于配置 ChatGPT自动文章摘要 相关功能。 | text | 是 |
| 266 | legacy_chatgpt_exclude_ids | ChatGPT排除ids | 用于配置 ChatGPT排除ids 相关功能。 | text | 是 |
| 267 | legacy_chatgpt_init_prompt | ChatGPTinitprompt | 提示词文本或模板内容。 | textarea | 是 |
| 268 | legacy_chatgpt_annotations_prompt | ChatGPT注释prompt | 提示词文本或模板内容。 | textarea | 是 |
| 269 | legacy_statistics_api | 统计接口地址 | 站点统计接口地址与请求参数。 | textarea | 是 |
| 270 | legacy_statistics_format | 统计展示格式 | 站点统计模块的展示模板或格式化文本。 | textarea | 是 |
| 271 | legacy_google_analytics_id | Google Analytics ID | Google Analytics 跟踪代码或统计 ID。 | code | 是 |
| 272 | legacy_admin_notify | 后台通知 | 后台外观与管理页提示相关配置。 | checkbox | 是 |
| 273 | legacy_custom_login_switch | 自定义登录开关 | 登录页样式、跳转与行为控制参数。 | checkbox | 是 |
| 274 | legacy_login_logo_img | 登录Logo图片 | 用于配置 登录Logo图片 相关功能。 | text | 是 |
| 275 | legacy_login_urlskip | 登录urlskip | 用于配置 登录urlskip 相关功能。 | checkbox | 是 |
| 276 | legacy_login_language_opt | 登录语言选项 | 用于配置 登录语言选项 相关功能。 | text | 是 |
| 277 | legacy_admin_background | 后台背景 | 后台外观与管理页提示相关配置。 | text | 是 |
| 278 | legacy_admin_left_style | 后台左侧样式 | 后台外观与管理页提示相关配置。 | text | 是 |
| 279 | legacy_admin_first_class_color | 后台一级分类颜色 | 后台外观与管理页提示相关配置。 | text | 是 |
| 280 | legacy_admin_second_class_color | 后台二级分类颜色 | 后台外观与管理页提示相关配置。 | text | 是 |
| 281 | legacy_admin_emphasize_color | 后台强调颜色 | 后台外观与管理页提示相关配置。 | text | 是 |
| 282 | legacy_admin_text_color | 后台文本颜色 | 后台外观与管理页提示相关配置。 | text | 是 |
| 283 | legacy_captcha_select | 登录验证码提供商 | 验证码服务参数（开关、站点密钥、校验地址）。 | select | 是 |
| 284 | legacy_vaptcha_vid | VaptchaVID | Vaptcha 人机验证服务配置。 | text | 是 |
| 285 | legacy_vaptcha_key | Vaptcha密钥 | Vaptcha 人机验证服务配置。 | text | 是 |
| 286 | legacy_vaptcha_scene | Vaptcha场景 | Vaptcha 人机验证服务配置。 | text | 是 |
| 287 | legacy_turnstile_site_key | Turnstile站点密钥 | Cloudflare Turnstile 验证参数。 | text | 是 |
| 288 | legacy_turnstile_secret_key | TurnstileSecret密钥 | Cloudflare Turnstile 验证参数。 | text | 是 |
| 289 | legacy_turnstile_theme | Turnstile主题 | Cloudflare Turnstile 验证参数。 | text | 是 |
| 290 | legacy_favicon_link | 站点图标链接 | 用于配置 站点图标链接 相关功能。 | text | 是 |
| 291 | legacy_iro_seo | 主题SEO | 用于配置 主题SEO 相关功能。 | text | 是 |
| 292 | legacy_iro_meta_keywords | 主题元信息关键词 | 用于配置 主题元信息关键词 相关功能。 | text | 是 |
| 293 | legacy_iro_meta_description | 主题元信息描述 | 站点 SEO 描述文本，用于页面 description 元信息。 | code | 是 |
| 294 | legacy_theme_darkmode_auto | 跟随系统自动切换深色模式 | 用于设置是否跟随系统外观，在深色与浅色之间自动切换。 | checkbox | 是 |
| 295 | legacy_theme_darkmode_strategy | 深色模式切换策略 | 用于设置深色模式的切换规则（手动、自动或按时间策略）。 | text | 是 |
| 296 | legacy_theme_darkmode_background_transparency | 深色模式背景透明度 | 用于配置 深色模式背景透明度 相关功能。 | text | 是 |
| 297 | legacy_theme_commemorate_mode | 纪念模式 | 用于配置 纪念模式 相关功能。 | checkbox | 是 |
| 298 | legacy_load_out_svg | 加载外部SVG图标 | 用于配置 加载外部SVG图标 相关功能。 | text | 是 |
| 299 | legacy_reference_exter_font | 引用外部字体 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 300 | legacy_exter_font | 外部字体地址 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 301 | legacy_gfonts_api | Google字体接口地址 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 302 | legacy_gfonts_add_name | Google字体追加名称 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 303 | legacy_signature_typing_placeholder | 打字签名占位文本 | 用于配置 打字签名占位文本 相关功能。 | text | 是 |
| 304 | legacy_post_cover | 文章封面 | 用于配置 文章封面 相关功能。 | text | 是 |
| 305 | legacy_page_temp_title_font_size | page临时标题字体大小 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 306 | legacy_show_location_in_manage | 后台显示评论IP归属地 | 用于设置是否在后台评论管理页显示评论者 IP 归属地。 | checkbox | 是 |
| 307 | legacy_save_location | 保存地理位置 | 用于配置 保存地理位置 相关功能。 | checkbox | 是 |
| 308 | legacy_iro_captcha_level | 主题验证码等级 | 验证码服务参数（开关、站点密钥、校验地址）。 | text | 是 |
| 309 | legacy_time_zone_fix | 时间时区修复 | 用于配置 时间时区修复 相关功能。 | checkbox | 是 |
| 310 | legacy_gravatar_proxy | Gravatar代理 | 用于配置 Gravatar代理 相关功能。 | text | 是 |
| 311 | legacy_custom_proxy_address_of_gravatar | 自定义代理地址Gravatar | 用于配置 自定义代理地址Gravatar 相关功能。 | text | 是 |
| 312 | legacy_ghcard_proxy | GitHub 卡片代理 | 用于配置 GitHub 卡片代理 相关功能。 | text | 是 |
| 313 | legacy_lightbox | 灯箱 | 用于配置 灯箱 相关功能。 | checkbox | 是 |
| 314 | legacy_lightgallery_option | 图库option | 用于配置 图库option 相关功能。 | text | 是 |
| 315 | legacy_code_highlight_method | 代码高亮引擎 | 用于选择代码块高亮引擎（Prism / 关闭）。 | select | 是 |
| 316 | legacy_code_highlight_prism_line_number_all | 代码高亮Prism行号编号all | 用于配置 代码高亮Prism行号编号all 相关功能。 | checkbox | 是 |
| 317 | legacy_code_highlight_prism_autoload_path | 代码高亮Prism自动加载路径 | 用于配置 代码高亮Prism自动加载路径 相关功能。 | text | 是 |
| 318 | legacy_code_highlight_prism_theme_light | 代码高亮Prism主题浅色 | 用于配置 代码高亮Prism主题浅色 相关功能。 | text | 是 |
| 319 | legacy_code_highlight_prism_theme_dark | 代码高亮Prism主题dark | 用于配置 代码高亮Prism主题dark 相关功能。 | text | 是 |
| 320 | legacy_enable_theme_mathjax | 启用主题MathJax | 用于配置 启用主题MathJax 相关功能。 | checkbox | 是 |
| 321 | legacy_image_cdn | 图片CDN | 用于配置 图片CDN 相关功能。 | text | 是 |
| 322 | legacy_classify_display | 分类显示 | 用于配置 分类显示 相关功能。 | text | 是 |
| 323 | legacy_image_category | 图片分类 | 用于配置 图片分类 相关功能。 | text | 是 |
| 324 | legacy_cookie_version | Cookie版本 | 用于配置 Cookie版本 相关功能。 | text | 是 |
| 325 | legacy_hide_login_portal | 隐藏登录入口 | 用于配置 隐藏登录入口 相关功能。 | checkbox | 是 |
| 326 | legacy_fontawesome_source | FontAwesome来源 | 字体家族、字体资源地址或加载方式配置。 | text | 是 |
| 327 | legacy_dev_mode | 开发模式 | 用于配置 开发模式 相关功能。 | checkbox | 是 |
| 328 | legacy_php_notice_filter | PHP 报错过滤级别 | 用于设置前台 PHP 报错显示策略（推荐“仅严重错误”）。 | select | 是 |
| 329 | legacy_iro_update_source | 主题update来源 | 主题更新通道、检查频率与源地址。 | text | 是 |
| 330 | legacy_channel_validate_value | 通道校验值 | 渠道验证参数，用于请求来源校验。 | textarea | 是 |
| 331 | legacy_iro_update_channel | 主题update通道 | 主题更新通道、检查频率与源地址。 | text | 是 |
| 332 | legacy_core_library_basepath | 核心库基础路径 | 核心前端库版本或加载来源设置。 | text | 是 |
| 333 | legacy_shared_library_basepath | 共享库基础路径 | 共享依赖库加载策略与来源设置。 | text | 是 |
| 334 | legacy_lib_cdn_path | 库CDN路径 | 第三方前端库 CDN 节点选择。 | text | 是 |
| 335 | legacy_external_vendor_lib | 外部供应方库 | 用于配置 外部供应方库 相关功能。 | text | 是 |
| 336 | legacy_vision_resource_basepath | 视觉资源资源基础路径 | 主题视觉资源 CDN 基地址与加载策略。 | text | 是 |
| 337 | legacy_send_theme_version | 发送主题版本 | 用于配置 发送主题版本 相关功能。 | text | 是 |
