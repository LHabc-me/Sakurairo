<?php

if (!function_exists('iro_legacy_gallery_action')) {
    function iro_legacy_gallery_action($method) {
        require_once __DIR__ . '/../classes/gallery.php';
        $gallery = new Sakura\API\gallery();
        echo $gallery->{$method}();
        echo 'Done!';
    }
}

if (!function_exists('iro_resolve_legacy_action_redirect_url')) {
    function iro_resolve_legacy_action_redirect_url($legacy_action)
    {
        switch ($legacy_action) {
            case 'bangumi':
                return 'https://api.bgm.tv/v0/users/' . (iro_opt('bangumi_id') ?: '944883') . '/collections';

            case 'mal':
                $sort = 'status=7';
                switch ((int) iro_opt('my_anime_list_sort')) {
                    case 1: // Status and Last Updated
                        $sort = 'order=16&order2=5&status=7';
                        break;
                    case 2: // Last Updated
                        $sort = 'order=5&status=7';
                        break;
                    case 3: // Status
                        $sort = 'order=16&status=7';
                        break;
                }
                return 'https://myanimelist.net/animelist/' . (iro_opt('my_anime_list_username') ?: 'username') . '/load.json?' . $sort;

            case 'steam_library':
                return 'https://api.steampowered.com/IPlayerService/GetOwnedGames/v1/?key=' . iro_opt('steam_key') . '&steamid=' . iro_opt('steam_id') . '&include_appinfo=1&include_played_free_games=1&include_free_games=1';

            case 'playlist':
                return rest_url('sakura/v1/meting/aplayer') . '?_wpnonce=' . wp_create_nonce('wp_rest') . '&server=' . (iro_opt('aplayer_server') ?: 'netease') . '&type=playlist&id=' . (iro_opt('aplayer_playlistid') ?: '5380675133');

            case 'del_exist_theme':
                return wp_nonce_url(
                    admin_url('admin-post.php?action=iro_delete_duplicate_theme'),
                    'iro_delete_duplicate_theme'
                );
        }

        return '';
    }
}

if (!function_exists('iro_execute_legacy_action')) {
    function iro_execute_legacy_action($legacy_action)
    {
        $legacy_action = sanitize_key((string) $legacy_action);

        switch ($legacy_action) {
            case 'gallery_init':
                iro_legacy_gallery_action('init');
                return true;

            case 'gallery_webp':
                iro_legacy_gallery_action('webp');
                return true;

            case 'bangumi':
            case 'mal':
            case 'steam_library':
            case 'playlist':
            case 'del_exist_theme':
                $direct_url = iro_resolve_legacy_action_redirect_url($legacy_action);
                if ('' === $direct_url) {
                    return false;
                }
                wp_redirect($direct_url, 302);
                exit;
        }

        return false;
    }
}

if (!function_exists('iro_handle_legacy_action_admin_post')) {
    function iro_handle_legacy_action_admin_post()
    {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Access denied.', 'sakurairo'));
        }

        $legacy_action = '';
        if (isset($_REQUEST['iro_action'])) {
            $legacy_action = sanitize_key(wp_unslash($_REQUEST['iro_action']));
        }
        if ('' === $legacy_action) {
            wp_safe_redirect(admin_url());
            exit;
        }

        check_admin_referer('iro_legacy_action_' . $legacy_action);

        if (!iro_execute_legacy_action($legacy_action)) {
            wp_safe_redirect(admin_url());
            exit;
        }

        exit;
    }
    add_action('admin_post_iro_legacy_action', 'iro_handle_legacy_action_admin_post');
}

if (!function_exists('iro_action_operator')) {
    /**
     * Deprecated compatibility entry for legacy `iro_act` GET actions (DEP-002).
     *
     * @deprecated 1.2.78 Use `admin-post.php?action=iro_legacy_action` instead.
     * @return void
     */
    function iro_action_operator()
    {
        if (!isset($_GET['iro_act']) || empty($_GET['iro_act'])) {
            return;
        }

        if (!is_admin() || !current_user_can('manage_options')) {
            echo __("Access denied.", "sakurairo");
            return;
        }

        $direct_info = sanitize_key(wp_unslash($_GET['iro_act']));
        if (function_exists('iro_log_deprecated_usage')) {
            iro_log_deprecated_usage(
                'DEP-002',
                'Legacy GET route `iro_act` is deprecated; use admin-post `iro_legacy_action`.',
                $direct_info
            );
        }
        iro_execute_legacy_action($direct_info);
    }
}

iro_action_operator();
