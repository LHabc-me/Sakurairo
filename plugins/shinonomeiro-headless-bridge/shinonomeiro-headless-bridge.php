<?php
/**
 * Plugin Name: Shinonomeiro Headless Bridge
 * Plugin URI: https://github.com/LHabc-me/Shinonomeiro
 * GitHub URI: https://github.com/LHabc-me/Shinonomeiro
 * Update URI: https://github.com/LHabc-me/Shinonomeiro
 * GitHub Plugin URI: https://github.com/LHabc-me/Shinonomeiro
 * Primary Branch: release
 * Release Asset: true
 * Description: 为 Shinonomeiro 的 Headless 前端提供只读 REST 配置与文章扩展数据。
 * Version: 1.2.92
 * Author: LHabc
 * License: GPL-2.0-or-later
 * Text Domain: shinonomeiro-headless-bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SHINONOMEIRO_HEADLESS_BRIDGE_VERSION', '1.2.92');
define('SHINONOMEIRO_HEADLESS_BRIDGE_NAMESPACE', 'shinonomeiro-headless/v1');

final class Shinonomeiro_Headless_Bridge
{
    private const OPTIONS_KEY = 'shinonomeiro_options';
    private const LEGACY_OPTIONS_KEY = 'iro_options';

    public static function bootstrap(): void
    {
        add_action('admin_notices', [self::class, 'render_admin_notices']);
        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    public static function render_admin_notices(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $messages = [];

        if (!class_exists('WPGraphQL')) {
            $messages[] = '未检测到 WPGraphQL。Headless 前端的主内容查询将不可用，请先安装并启用该插件。';
        }

        if (!function_exists('wp_get_nav_menu_items')) {
            $messages[] = '当前 WordPress 环境缺少导航菜单能力，Headless 菜单输出将不可用。';
        }

        if (empty($messages)) {
            return;
        }

        foreach ($messages as $message) {
            echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
        }
    }

    public static function register_routes(): void
    {
        register_rest_route(
            SHINONOMEIRO_HEADLESS_BRIDGE_NAMESPACE,
            '/site',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [self::class, 'get_site_config'],
                'permission_callback' => '__return_true',
            ]
        );

        register_rest_route(
            SHINONOMEIRO_HEADLESS_BRIDGE_NAMESPACE,
            '/homepage',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [self::class, 'get_homepage_config'],
                'permission_callback' => '__return_true',
            ]
        );

        register_rest_route(
            SHINONOMEIRO_HEADLESS_BRIDGE_NAMESPACE,
            '/posts/(?P<id>\d+)/extras',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [self::class, 'get_post_extras'],
                'permission_callback' => '__return_true',
                'args' => [
                    'id' => [
                        'validate_callback' => static function ($value): bool {
                            return is_numeric($value) && (int) $value > 0;
                        },
                    ],
                ],
            ]
        );
    }

    public static function get_site_config(): WP_REST_Response
    {
        $options = self::get_theme_options();
        $response = [
            'generated_at' => gmdate(DATE_ATOM),
            'site' => [
                'name' => get_bloginfo('name'),
                'description' => get_bloginfo('description'),
                'language' => get_bloginfo('language'),
                'url' => home_url('/'),
                'icon' => get_site_icon_url() ?: self::option_string($options, 'favicon_link'),
            ],
            'branding' => [
                'logo' => self::option_string($options, 'iro_logo'),
                'text_logo' => self::normalize_text_logo($options['nav_text_logo'] ?? []),
                'favicon' => self::option_string($options, 'favicon_link'),
            ],
            'theme' => [
                'skin' => self::option_string($options, 'theme_skin', '#d97757'),
                'skin_matching' => self::option_string($options, 'theme_skin_matching', '#c35f43'),
                'skin_dark' => self::option_string($options, 'theme_skin_dark', '#3b2c2a'),
                'custom_css' => self::option_string($options, 'site_custom_style'),
            ],
            'footer' => [
                'info' => self::option_string($options, 'footer_info'),
                'show_hitokoto' => self::option_bool($options, 'footer_yiyan'),
                'show_load_stats' => self::option_bool($options, 'footer_load_occupancy', true),
                'show_upyun' => self::option_bool($options, 'footer_upyun', true),
                'show_sakura_icon' => self::option_bool($options, 'footer_sakura', true),
                'addition_html' => self::option_string($options, 'footer_addition'),
            ],
            'injections' => [
                'head_html' => self::option_string($options, 'site_header_insert'),
            ],
            'menus' => [
                'primary' => self::resolve_menu_items('primary'),
            ],
            'social_links' => self::resolve_social_links($options),
            'compat' => [
                'options_key' => self::OPTIONS_KEY,
                'legacy_options_key' => self::LEGACY_OPTIONS_KEY,
                'theme_version' => function_exists('wp_get_theme') ? wp_get_theme()->get('Version') : '',
            ],
        ];

        return new WP_REST_Response($response, 200);
    }

    public static function get_homepage_config(): WP_REST_Response
    {
        $options = self::get_theme_options();
        $static_page_id = absint($options['static_page_id'] ?? 0);
        $static_page = $static_page_id > 0 ? get_post($static_page_id) : null;
        $capsule_components = array_values(array_filter(self::option_array($options, 'capsule_components', [])));

        $response = [
            'generated_at' => gmdate(DATE_ATOM),
            'components' => array_values(array_filter(self::option_array($options, 'homepage_components', ['exhibition', 'primary']))),
            'display_area' => [
                'title' => self::option_string($options, 'exhibition_area_title', '展示区域'),
                'icon' => self::option_string($options, 'exhibition_area_icon', 'fa-regular fa-compass'),
            ],
            'post_area' => [
                'title' => self::option_string($options, 'post_area_title', '文章列表'),
                'icon' => self::option_string($options, 'post_area_icon', 'fa-regular fa-bookmark'),
            ],
            'capsule_components' => $capsule_components,
            'capsules' => self::resolve_homepage_capsules($options, $capsule_components),
            'exhibition_items' => self::resolve_exhibition_items($options),
            'static_page' => $static_page instanceof WP_Post ? [
                'id' => $static_page->ID,
                'title' => get_the_title($static_page),
                'slug' => $static_page->post_name,
                'excerpt' => has_excerpt($static_page) ? $static_page->post_excerpt : '',
                'uri' => wp_make_link_relative(get_permalink($static_page)),
            ] : null,
        ];

        return new WP_REST_Response($response, 200);
    }

    public static function get_post_extras(WP_REST_Request $request): WP_REST_Response
    {
        $post = get_post((int) $request['id']);
        if (!$post instanceof WP_Post) {
            return new WP_REST_Response(['message' => '文章不存在。'], 404);
        }

        $annotations = get_post_meta($post->ID, 'iro_chatgpt_annotations', true);
        $rendered = self::render_post_content_bundle($post);
        $response = [
            'id' => $post->ID,
            'type' => $post->post_type,
            'ai_excerpt' => (string) get_post_meta($post->ID, 'ai_summon_excerpt', true),
            'annotations' => is_array($annotations) ? array_values($annotations) : [],
            'cover' => get_the_post_thumbnail_url($post, 'full') ?: '',
            'reading' => [
                'word_count' => self::count_words($post->post_content),
                'has_toc' => self::content_has_heading_tags($post->post_content),
            ],
            'rendered' => $rendered,
            'meta' => [
                'comment_count' => (int) $post->comment_count,
                'view_count' => (int) get_post_meta($post->ID, 'views', true),
            ],
            'navigation' => [
                'previous' => self::get_adjacent_post_payload($post, 'previous'),
                'next' => self::get_adjacent_post_payload($post, 'next'),
            ],
            'compat' => [
                'comment_open' => comments_open($post),
                'ping_open' => pings_open($post),
            ],
        ];

        return new WP_REST_Response($response, 200);
    }

    private static function get_theme_options(): array
    {
        $options = get_option(self::OPTIONS_KEY);
        if (is_array($options)) {
            return $options;
        }

        $legacy = get_option(self::LEGACY_OPTIONS_KEY);
        if (is_array($legacy)) {
            return $legacy;
        }

        return [];
    }

    private static function option_string(array $options, string $key, string $default = ''): string
    {
        $value = $options[$key] ?? $default;
        return is_scalar($value) ? (string) $value : $default;
    }

    private static function option_bool(array $options, string $key, bool $default = false): bool
    {
        $value = $options[$key] ?? $default;
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
        }

        return (bool) $value;
    }

    private static function option_array(array $options, string $key, array $default = []): array
    {
        $value = $options[$key] ?? $default;
        return is_array($value) ? $value : $default;
    }

    private static function normalize_text_logo($value): array
    {
        if (!is_array($value)) {
            return [
                'text' => '',
                'font_name' => '',
            ];
        }

        return [
            'text' => isset($value['text']) ? (string) $value['text'] : '',
            'font_name' => isset($value['font_name']) ? (string) $value['font_name'] : '',
        ];
    }

    private static function resolve_menu_items(string $location): array
    {
        $locations = get_nav_menu_locations();
        $menu_id = $locations[$location] ?? 0;
        if (!$menu_id) {
            return [];
        }

        $items = wp_get_nav_menu_items($menu_id);
        if (!is_array($items)) {
            return [];
        }

        $normalized = [];
        foreach ($items as $item) {
            $normalized[] = [
                'id' => (int) $item->ID,
                'title' => html_entity_decode(wp_strip_all_tags($item->title), ENT_QUOTES, 'UTF-8'),
                'url' => (string) $item->url,
                'target' => (string) $item->target,
                'parent' => (int) $item->menu_item_parent,
                'order' => (int) $item->menu_order,
            ];
        }

        usort($normalized, static function (array $left, array $right): int {
            return $left['order'] <=> $right['order'];
        });

        return $normalized;
    }

    private static function resolve_social_links(array $options): array
    {
        $links = [];
        $common = [
            'github' => 'GitHub',
            'telegram' => 'Telegram',
            'youtube' => 'YouTube',
            'instagram' => 'Instagram',
            'zhihu' => 'Zhihu',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'facebook' => 'Facebook',
            'discord' => 'Discord',
            'xiaohongshu' => 'Xiaohongshu',
            'douyin' => 'Douyin',
            'sina' => 'Weibo',
            'wangyiyun' => 'NetEase Music',
        ];

        foreach ($common as $key => $label) {
            $url = self::option_string($options, $key);
            if ($url !== '') {
                $links[] = [
                    'id' => $key,
                    'label' => $label,
                    'url' => $url,
                ];
            }
        }

        $custom = self::option_array($options, 'diysocialicons');
        foreach ($custom as $index => $item) {
            if (!is_array($item) || empty($item['link'])) {
                continue;
            }
            $links[] = [
                'id' => 'custom-' . $index,
                'label' => isset($item['title']) ? (string) $item['title'] : ('Custom ' . $index),
                'url' => (string) $item['link'],
                'icon' => isset($item['icon']) ? (string) $item['icon'] : '',
            ];
        }

        return $links;
    }

    private static function resolve_homepage_capsules(array $options, array $components): array
    {
        if (empty($components)) {
            return [];
        }

        $stats = self::resolve_site_stats();
        $capsules = [];
        $labels = [
            'post_count' => '内容数',
            'comment_count' => '评论数',
            'view_count' => '访问数',
            'link_count' => '友链数',
            'author_count' => '作者数',
            'total_words' => '字数总计',
            'blog_days' => '博客已运行',
            'admin_online' => '最后在线',
            'announcement' => '公告',
            'random_link' => '友链推荐',
        ];
        $icons = [
            'post_count' => 'fa-regular fa-file-lines',
            'comment_count' => 'fa-regular fa-comment',
            'view_count' => 'fa-regular fa-eye',
            'link_count' => 'fa-solid fa-link',
            'author_count' => 'fa-solid fa-users',
            'total_words' => 'fa-solid fa-font',
            'blog_days' => 'fa-solid fa-calendar-days',
            'admin_online' => 'fa-solid fa-user-clock',
            'announcement' => 'fa-solid fa-bullhorn',
            'random_link' => 'fa-solid fa-link',
        ];

        foreach ($components as $component) {
            switch ($component) {
                case 'post_count':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['post_count'] ?? 0)) . ' 篇');
                    break;
                case 'comment_count':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['comment_count'] ?? 0)) . ' 条');
                    break;
                case 'view_count':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['visitor_count'] ?? 0)) . ' 次');
                    break;
                case 'link_count':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['link_count'] ?? 0)) . ' 个');
                    break;
                case 'author_count':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['author_count'] ?? 0)) . ' 位');
                    break;
                case 'total_words':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['total_words'] ?? 0)) . ' 字');
                    break;
                case 'blog_days':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], number_format((int) ($stats['blog_days'] ?? 0)) . ' 天');
                    break;
                case 'admin_online':
                    $capsules[] = self::build_stat_capsule($component, $labels[$component], $icons[$component], self::format_admin_online((int) ($stats['admin_last_online_diff'] ?? 0)));
                    break;
                case 'announcement':
                    $capsules[] = [
                        'id' => $component,
                        'type' => 'announcement',
                        'label' => self::option_string($options, 'stat_announcement_text', '最新公告'),
                        'icon' => $icons[$component],
                        'value' => '',
                    ];
                    break;
                case 'random_link':
                    $random_link = self::resolve_random_link_payload($stats['random_link'] ?? null);
                    if ($random_link !== null) {
                        $capsules[] = [
                            'id' => $component,
                            'type' => 'link',
                            'label' => $labels[$component],
                            'icon' => $icons[$component],
                            'value' => '',
                            'link' => $random_link,
                        ];
                    }
                    break;
            }
        }

        return $capsules;
    }

    private static function build_stat_capsule(string $id, string $label, string $icon, string $value): array
    {
        return [
            'id' => $id,
            'type' => 'stat',
            'label' => $label,
            'icon' => $icon,
            'value' => $value,
        ];
    }

    private static function resolve_random_link_payload($bookmark): ?array
    {
        if (!is_object($bookmark)) {
            return null;
        }

        return [
            'name' => isset($bookmark->link_name) ? (string) $bookmark->link_name : '',
            'url' => isset($bookmark->link_url) ? (string) $bookmark->link_url : '',
            'image' => isset($bookmark->link_image) ? (string) $bookmark->link_image : '',
            'description' => 'Meet my friend!',
        ];
    }

    private static function resolve_exhibition_items(array $options): array
    {
        $items = [];
        $default_base = self::option_string($options, 'vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@3.0/');

        foreach (self::option_array($options, 'exhibition', []) as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = isset($item['title']) ? trim((string) $item['title']) : '';
            $link = isset($item['link']) ? trim((string) $item['link']) : '';
            if ($title === '' || $link === '') {
                continue;
            }

            $image = isset($item['img']) ? trim((string) $item['img']) : '';
            if ($image === '') {
                $image = rtrim($default_base, '/') . '/series/exhibition1.webp';
            }

            $items[] = [
                'id' => 'exhibition-' . $index,
                'title' => $title,
                'description' => isset($item['description']) ? (string) $item['description'] : '',
                'link' => $link,
                'image' => $image,
            ];
        }

        return $items;
    }

    private static function resolve_site_stats(): array
    {
        if (function_exists('get_site_stats')) {
            $stats = get_site_stats();
            if (is_array($stats)) {
                return $stats;
            }
        }

        $cache_key = 'shinonomeiro_headless_site_stats';
        $cached = get_transient($cache_key);
        if (is_array($cached)) {
            return $cached;
        }

        $post_ids = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'orderby' => 'date',
            'order' => 'ASC',
            'no_found_rows' => true,
        ]);

        $total_words = 0;
        $visitor_count = 0;
        foreach ($post_ids as $post_id) {
            $total_words += self::count_words((string) get_post_field('post_content', $post_id));
            $visitor_count += (int) get_post_meta($post_id, 'views', true);
        }

        $first_post_id = $post_ids[0] ?? 0;
        $first_post_date = $first_post_id ? (string) get_post_field('post_date', $first_post_id) : '';
        $blog_days = 0;
        if ($first_post_date !== '') {
            $blog_days = (new DateTime())->diff(new DateTime($first_post_date))->days;
        }

        $comments = wp_count_comments();
        $bookmark_count = count(get_bookmarks(['hide_invisible' => true]));
        $random_link = get_bookmarks([
            'hide_invisible' => true,
            'orderby' => 'rand',
            'limit' => 1,
        ]);
        $count_users = count_users();

        $stats = [
            'post_count' => count($post_ids),
            'comment_count' => (int) ($comments->approved ?? 0),
            'visitor_count' => $visitor_count,
            'link_count' => $bookmark_count,
            'random_link' => !empty($random_link) ? $random_link[0] : null,
            'first_post_date' => $first_post_date,
            'blog_days' => $blog_days,
            'admin_last_online' => current_time('mysql'),
            'admin_last_online_diff' => 0,
            'author_count' => isset($count_users['avail_roles']['author']) ? (int) $count_users['avail_roles']['author'] : 0,
            'total_words' => $total_words,
        ];

        set_transient($cache_key, $stats, HOUR_IN_SECONDS);
        return $stats;
    }

    private static function format_admin_online(int $minutes): string
    {
        if ($minutes < 1) {
            return '刚刚在线';
        }

        if ($minutes < 60) {
            return $minutes . ' 分钟前';
        }

        $hours = (int) floor($minutes / 60);
        if ($hours < 24) {
            return $hours . ' 小时前';
        }

        $days = (int) floor($hours / 24);
        return $days . ' 天前';
    }

    private static function count_words(string $content): int
    {
        $text = trim(wp_strip_all_tags($content));
        if ($text === '') {
            return 0;
        }

        preg_match_all('/[\p{Han}]|[A-Za-z0-9_]+/u', $text, $matches);
        return count($matches[0]);
    }

    private static function content_has_heading_tags(string $content): bool
    {
        return (bool) preg_match('/<h[1-6][^>]*>/i', $content);
    }

    private static function render_post_content_bundle(WP_Post $post): array
    {
        $previous_post = $GLOBALS['post'] ?? null;
        $GLOBALS['post'] = $post;
        setup_postdata($post);

        try {
            return [
                'content' => apply_filters('the_content', $post->post_content),
                'excerpt' => apply_filters('the_excerpt', $post->post_excerpt),
            ];
        } finally {
            wp_reset_postdata();
            if ($previous_post instanceof WP_Post) {
                $GLOBALS['post'] = $previous_post;
                setup_postdata($previous_post);
            } else {
                unset($GLOBALS['post']);
            }
        }
    }

    private static function get_adjacent_post_payload(WP_Post $post, string $direction): ?array
    {
        $previous_post = $GLOBALS['post'] ?? null;
        $GLOBALS['post'] = $post;
        setup_postdata($post);

        $adjacent = $direction === 'previous'
            ? get_adjacent_post(false, '', true)
            : get_adjacent_post(false, '', false);

        wp_reset_postdata();
        if ($previous_post instanceof WP_Post) {
            $GLOBALS['post'] = $previous_post;
            setup_postdata($previous_post);
        } else {
            unset($GLOBALS['post']);
        }

        if (!$adjacent instanceof WP_Post) {
            return null;
        }

        return [
            'id' => $adjacent->ID,
            'title' => get_the_title($adjacent),
            'uri' => wp_make_link_relative(get_permalink($adjacent)),
        ];
    }
}

Shinonomeiro_Headless_Bridge::bootstrap();
