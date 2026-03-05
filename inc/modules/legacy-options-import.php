<?php

if (!function_exists('iro_run_one_time_legacy_import')) {
    function iro_run_one_time_legacy_import() {
        $legacy = get_option(IRO_LEGACY_OPTIONS_KEY);
        if (!is_array($legacy) || empty($legacy)) {
            return false;
        }

        $current = get_option(IRO_OPTIONS_KEY);
        $current = is_array($current) ? $current : array();
        $merged = array_replace($legacy, $current);

        update_option(IRO_OPTIONS_KEY, $merged);
        $GLOBALS['iro_options'] = $merged;

        if (defined('IRO_OPTIONS_THEME_MOD_KEY') && IRO_OPTIONS_THEME_MOD_KEY) {
            set_theme_mod(IRO_OPTIONS_THEME_MOD_KEY, $merged);
        }

        return true;
    }
}

if (!function_exists('iro_maybe_run_legacy_import_via_flag')) {
    function iro_maybe_run_legacy_import_via_flag() {
        if (!defined('IRO_ENABLE_LEGACY_IMPORT') || true !== IRO_ENABLE_LEGACY_IMPORT) {
            return;
        }
        iro_run_one_time_legacy_import();
    }
    add_action('after_setup_theme', 'iro_maybe_run_legacy_import_via_flag', 5);
}

if (!function_exists('iro_maybe_run_legacy_import_via_admin_action')) {
    function iro_maybe_run_legacy_import_via_admin_action() {
        if (!is_admin() || !current_user_can('manage_options')) {
            return;
        }
        if (!isset($_GET['iro_migrate_legacy_options']) || '1' !== $_GET['iro_migrate_legacy_options']) {
            return;
        }
        check_admin_referer('iro_migrate_legacy_options');
        $ok = iro_run_one_time_legacy_import();
        wp_safe_redirect(admin_url('customize.php?iro_legacy_import=' . ($ok ? 'done' : 'skipped')));
        exit;
    }
    add_action('admin_init', 'iro_maybe_run_legacy_import_via_admin_action');
}
