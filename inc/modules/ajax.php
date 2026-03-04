<?php

/**
 * AJAX guard utilities.
 */
function sakurairo_ajax_guard_fail($message, $status = 403, $error_callback = null)
{
    if (is_callable($error_callback)) {
        call_user_func($error_callback, $message, $status);
        return false;
    }

    if (wp_doing_ajax()) {
        wp_send_json_error(array('message' => $message), $status);
    }

    wp_die(esc_html($message), esc_html__('Error', 'sakurairo'), array('response' => $status));
}

function sakurairo_ajax_rate_limited($action, $rate_limit, $rate_window)
{
    if ($rate_limit <= 0 || $rate_window <= 0) {
        return false;
    }

    $identity = 'ip:' . sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    if (is_user_logged_in()) {
        $identity = 'uid:' . get_current_user_id();
    }

    $rate_key = 'sakurairo_ajax_rate_' . md5($action . '|' . $identity);
    $rate_data = get_transient($rate_key);
    $count = is_array($rate_data) && isset($rate_data['count']) ? (int) $rate_data['count'] : 0;

    if ($count >= $rate_limit) {
        return true;
    }

    set_transient(
        $rate_key,
        array('count' => $count + 1),
        $rate_window
    );

    return false;
}

function sakurairo_ajax_guard($args = array())
{
    $defaults = array(
        'action' => '',
        'nonce_action' => '',
        'nonce_field' => '_wpnonce',
        'capability' => '',
        'capability_callback' => null,
        'rate_limit' => 60,
        'rate_window' => 60,
        'error_callback' => null,
    );
    $args = wp_parse_args($args, $defaults);

    if (!empty($args['nonce_action'])) {
        $nonce = '';
        if (isset($_REQUEST[$args['nonce_field']])) {
            $nonce = sanitize_text_field(wp_unslash($_REQUEST[$args['nonce_field']]));
        }

        if (empty($nonce) || !wp_verify_nonce($nonce, $args['nonce_action'])) {
            return sakurairo_ajax_guard_fail(__('Security verification failed.', 'sakurairo'), 403, $args['error_callback']);
        }
    }

    $capability_ok = true;
    if (is_callable($args['capability_callback'])) {
        $capability_ok = (bool) call_user_func($args['capability_callback']);
    } elseif (!empty($args['capability'])) {
        $capability_ok = current_user_can($args['capability']);
    }

    if (!$capability_ok) {
        return sakurairo_ajax_guard_fail(__('Access denied.', 'sakurairo'), 403, $args['error_callback']);
    }

    if (sakurairo_ajax_rate_limited($args['action'], (int) $args['rate_limit'], (int) $args['rate_window'])) {
        return sakurairo_ajax_guard_fail(__('You are submitting too frequently. Please try again later.', 'sakurairo'), 429, $args['error_callback']);
    }

    return true;
}

/*私密评论*/
add_action('wp_ajax_nopriv_siren_private', 'siren_private');
add_action('wp_ajax_siren_private', 'siren_private');
function siren_private()
{
    if (!sakurairo_ajax_guard(array(
        'action' => 'siren_private',
        'nonce_action' => 'wp_rest',
        'nonce_field' => '_wpnonce',
        'capability_callback' => '__return_true',
        'rate_limit' => 30,
        'rate_window' => 300,
    ))) {
        return;
    }

    $comment_id = isset($_POST['p_id']) ? absint($_POST['p_id']) : 0;
    $action = isset($_POST['p_action']) ? sanitize_key(wp_unslash($_POST['p_action'])) : '';

    if (!$comment_id || $action !== 'set_private') {
        wp_die('0');
    }

    if ($action == 'set_private') {
        update_comment_meta($comment_id, '_private', 'true');
        $i_private = get_comment_meta($comment_id, '_private', true);
        echo empty($i_private) ? '是' : '否';
    }
    die;
}

// AJAX 处理函数 - 更新主题选项
add_action('wp_ajax_update_theme_option', 'update_theme_option');
function update_theme_option()
{
    if (!sakurairo_ajax_guard(array(
        'action' => 'update_theme_option',
        'nonce_action' => 'update_theme_option_nonce',
        'nonce_field' => '_wpnonce',
        'capability' => 'manage_options',
        'rate_limit' => 60,
        'rate_window' => 60,
    ))) {
        return;
    }

    if (!isset($_POST['option']) || !isset($_POST['value'])) {
        wp_die('Missing required parameters');
    }

    $option = sanitize_key(wp_unslash($_POST['option']));
    $value = sanitize_text_field(wp_unslash($_POST['value']));
    if (empty($option)) {
        wp_die('Invalid option');
    }

    iro_opt_update($option, $value);
    wp_die();
}

// AJAX 处理函数 - 写入 theme_admin_notice 元值
add_action('wp_ajax_update_theme_admin_notice_meta', 'update_theme_admin_notice_meta');
function update_theme_admin_notice_meta()
{
    if (!sakurairo_ajax_guard(array(
        'action' => 'update_theme_admin_notice_meta',
        'nonce_action' => 'update_theme_admin_notice_meta_nonce',
        'nonce_field' => '_wpnonce',
        'capability' => 'manage_options',
        'rate_limit' => 60,
        'rate_window' => 60,
    ))) {
        return;
    }

    if (!isset($_POST['user_id']) || !isset($_POST['meta_key']) || !isset($_POST['meta_value'])) {
        wp_die('Missing required parameters');
    }

    $user_id = absint($_POST['user_id']);
    $meta_key = sanitize_key(wp_unslash($_POST['meta_key']));
    $meta_value = sanitize_text_field(wp_unslash($_POST['meta_value']));
    if ($user_id !== get_current_user_id() || $meta_key !== 'theme_admin_notice') {
        wp_die('Invalid parameters');
    }

    update_user_meta($user_id, $meta_key, $meta_value);
    wp_die();
}
