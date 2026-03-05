<?php

/*
 * 隐藏 Dashboard
 */
/* Remove the "Dashboard" from the admin menu for non-admin users */
function remove_dashboard()
{
    global $current_user, $menu, $submenu;
    wp_get_current_user();

    if (!in_array('administrator', $current_user->roles)) {
        reset($menu);
        $page = key($menu);
        while ((__('Dashboard') != $menu[$page][0]) && next($menu)) {
            $page = key($menu);
        }
        if (__('Dashboard') == $menu[$page][0]) {
            unset($menu[$page]);
        }
        reset($menu);
        $page = key($menu);
        while (!$current_user->has_cap($menu[$page][1]) && next($menu)) {
            $page = key($menu);
        }
        if (
            preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) &&
            ('index.php' != $menu[$page][2])
        ) {
            wp_redirect(get_option('siteurl') . '/wp-admin/profile.php');
        }
    }
}
add_action('admin_menu', 'remove_dashboard');

/*
 * 评论表情修复
 */
function admin_ini()
{
    wp_enqueue_style('cus-styles-fit', get_template_directory_uri() . '/css/dashboard-emoji-fix.css');
}
add_action('admin_enqueue_scripts', 'admin_ini');

/*
 * 后台通知
 */
/**
 * 在提供权限的情况下，为管理员用户显示通知并更新 meta 值
 */
function theme_admin_notice_callback()
{
    // 判断当前用户是否为管理员
    if (!current_user_can('manage_options')) {
        return;
    }

    // 读取 meta 值
    $meta_value = get_user_meta(get_current_user_id(), 'theme_admin_notice', true);

    // 判断 meta 值是否存在
    if ($meta_value) {
        return; // 如果存在，退出函数，避免重复加载通知
    }

    // 显示通知
    $theme_name = 'Shinonomeiro';
    switch (get_user_locale()) {
        case 'zh_CN':
            $thankyou = '感谢你使用 ' . $theme_name . ' 主题！这里有一些需要你的许可的东西(*/ω＼*)';
            $dislike = '不，谢谢';
            $allow_send = '允许发送你的主题版本数据以便官方统计';
            break;

        case 'zh_TW':
            $thankyou = '感謝你使用 ' . $theme_name . ' 主題！以下是一些需要你許可的內容。';
            $dislike = '謝謝，不用了';
            $allow_send = '允許出於統計目的發送主題版本数据';
            break;

        case 'ja':
        case 'ja_JP':
            $thankyou = 'ご使用いただきありがとうございます！以下は、あなたの許可が必要なコンテンツです。';
            $dislike = 'いいえ、結構です';
            $allow_send = '統計目的のためにあなたのテーマバージョンを送信することを許可する';
            break;

        default:
            $thankyou = 'Thank you for using the ' . $theme_name . ' theme! There is something that needs your approval.';
            $dislike = 'No, thanks';
            $allow_send = 'Allow sending your theme version for statistical purposes';
            break;
    }
    ?>
                                <div class="notice notice-success" id="send-ver-tip">
                                    <p><?php echo $thankyou; ?></p>
                                    <button class="button" onclick="dismiss_notice()"><?php echo $dislike; ?></button>
                                    <button class="button" onclick="update_option()"><?php echo $allow_send; ?></button>
                                </div>
                                <script>
                                    const updateThemeOptionNonce = '<?php echo esc_js(wp_create_nonce('update_theme_option_nonce')); ?>';
                                    const updateThemeNoticeMetaNonce = '<?php echo esc_js(wp_create_nonce('update_theme_admin_notice_meta_nonce')); ?>';

                                    function dismiss_notice() {
                                        // 隐藏通知
                                        document.getElementById( "send-ver-tip" ).style.display = "none";
                                        // 写入 1 到 meta
                                        var data = new FormData();
                                        data.append( 'action', 'update_theme_admin_notice_meta' );
                                        data.append( 'user_id', '<?php echo get_current_user_id(); ?>' );
                                        data.append( 'meta_key', 'theme_admin_notice' );
                                        data.append( 'meta_value', '1' );
                                        data.append( '_wpnonce', updateThemeNoticeMetaNonce );
                                        fetch( '<?php echo admin_url('admin-ajax.php'); ?>', {
                                            method: 'POST',
                                            body: data
                                        } );
                                    }

                                    function update_option() {
                                        // 隐藏通知
                                        document.getElementById( "send-ver-tip" ).style.display = "none";
                                        // 发送 AJAX 请求
                                        var xhr = new XMLHttpRequest();
                                        xhr.open( "POST", "<?php echo admin_url('admin-ajax.php'); ?>", true );
                                        xhr.setRequestHeader( "Content-Type", "application/x-www-form-urlencoded" );
                                        xhr.send( "action=update_theme_option&option=send_theme_version&value=true&_wpnonce=" + encodeURIComponent(updateThemeOptionNonce) );

                                        // 写入 1 到 meta
                                        var data = new FormData();
                                        data.append( 'action', 'update_theme_admin_notice_meta' );
                                        data.append( 'user_id', '<?php echo get_current_user_id(); ?>' );
                                        data.append( 'meta_key', 'theme_admin_notice' );
                                        data.append( 'meta_value', '1' );
                                        data.append( '_wpnonce', updateThemeNoticeMetaNonce );
                                        fetch( '<?php echo admin_url('admin-ajax.php'); ?>', {
                                            method: 'POST',
                                            body: data
                                        } );
                                    }
                                </script>
                                <?php
}
add_action('admin_notices', 'theme_admin_notice_callback');

/*
 * 删除重复主题目录（替代 iro_act=del_exist_theme）
 */
function iro_handle_delete_duplicate_theme()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Access denied.', 'sakurairo'));
    }

    check_admin_referer('iro_delete_duplicate_theme');

    if (basename(get_template_directory()) !== 'Shinonomeiro') {
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        WP_Filesystem();
        global $wp_filesystem;
        if ($wp_filesystem) {
            $wp_filesystem->delete(get_theme_root() . '/Shinonomeiro', true);
        }
    }

    wp_safe_redirect(admin_url());
    exit;
}
add_action('admin_post_iro_delete_duplicate_theme', 'iro_handle_delete_duplicate_theme');

/**
 * 检查父主题文件夹名称是否正确
 * 如果名称不正确，尝试重命名或显示管理员警告信息
 */
function theme_folder_check_on_admin_init()
{
    // 获取当前父主题文件夹名称及路径
    $current_theme_path = get_template_directory();
    $theme_folder_name = basename($current_theme_path);
    $correct_theme_folder = 'Shinonomeiro';
    $user_locale = get_user_locale();

    // 仅管理员用户处理
    if (!current_user_can('manage_options')) {
        return;
    }

    // 当主题文件夹名称不正确时
    if ($theme_folder_name !== $correct_theme_folder) {
        $correct_theme_path = trailingslashit(dirname($current_theme_path)) . $correct_theme_folder;
        $delete_duplicate_theme_url = wp_nonce_url(
            admin_url('admin-post.php?action=iro_delete_duplicate_theme'),
            'iro_delete_duplicate_theme'
        );

        // 如果目标路径已存在
        if (file_exists($correct_theme_path)) {
            if (is_writable($correct_theme_path)) {
                $is_writable = true;
            } else {
                $is_writable = false;
            }
            add_action('admin_notices', function () use ($theme_folder_name, $correct_theme_folder, $user_locale, $is_writable, $delete_duplicate_theme_url) {
                switch ($user_locale) {
                    case 'zh_CN':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 当前父主题文件夹名称为 <code><?php echo esc_html($theme_folder_name); ?></code>，但目标名称 <code><?php echo esc_html($correct_theme_folder); ?></code> 已存在。请手动检查主题文件夹。</p>
                            <?php if ($is_writable) { ?> <br><a href="<?php echo esc_url($delete_duplicate_theme_url); ?>" class="page-title-action">点击此处立即删除重名主题</a> <?php } ?>
                        </div>
                        <?php
                        break;
                    case 'zh_TW':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 目前父主題資料夾名稱為 <code><?php echo esc_html($theme_folder_name); ?></code>，但目標名稱 <code><?php echo esc_html($correct_theme_folder); ?></code> 已存在。請手動檢查主題資料夾。</p>
                            <?php if ($is_writable) { ?> <br><a href="<?php echo esc_url($delete_duplicate_theme_url); ?>" class="page-title-action">點擊此處立即刪除重名的主題</a> <?php } ?>
                        </div>
                        <?php
                        break;
                    case 'ja':
                    case 'ja_JP':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 現在の親テーマフォルダ名は <code><?php echo esc_html($theme_folder_name); ?></code> ですが、対象の名前 <code><?php echo esc_html($correct_theme_folder); ?></code> は既に存在します。テーマフォルダを手動で確認してください。</p>
                            <?php if ($is_writable) { ?> <br><a href="<?php echo esc_url($delete_duplicate_theme_url); ?>" class="page-title-action">ここをクリックして、重複するテーマをすぐに削除します</a> <?php } ?>
                        </div>
                        <?php
                        break;
                    default:
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>Warning:</strong> The current parent theme folder name is <code><?php echo esc_html($theme_folder_name); ?></code>, but the target name <code><?php echo esc_html($correct_theme_folder); ?></code> already exists. Please manually check the theme folder.</p>
                            <?php if ($is_writable) { ?> <br><a href="<?php echo esc_url($delete_duplicate_theme_url); ?>" class="page-title-action">Click here to immediately delete the duplicate theme</a> <?php } ?>
                        </div>
                        <?php
                        break;
                }
            });
            return;
        }

        // 尝试重命名文件夹
        if (rename($current_theme_path, $correct_theme_path)) {
            switch_theme($correct_theme_folder);
        } else {
            add_action('admin_notices', function () use ($theme_folder_name, $correct_theme_folder, $user_locale) {
                switch ($user_locale) {
                    case 'zh_CN':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 当前父主题文件夹名称为 <code><?php echo esc_html($theme_folder_name); ?></code>，无法重命名为 <code><?php echo esc_html($correct_theme_folder); ?></code>。请检查文件系统权限。</p>
                        </div>
                        <?php
                        break;
                    case 'zh_TW':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 目前父主題資料夾名稱為 <code><?php echo esc_html($theme_folder_name); ?></code>，無法重新命名為 <code><?php echo esc_html($correct_theme_folder); ?></code>。請檢查檔案系統權限。</p>
                        </div>
                        <?php
                        break;
                    case 'ja':
                    case 'ja_JP':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 現在の親テーマフォルダ名は <code><?php echo esc_html($theme_folder_name); ?></code> ですが、<code><?php echo esc_html($correct_theme_folder); ?></code> にリネームできませんでした。ファイルシステムの権限を確認してください。</p>
                        </div>
                        <?php
                        break;
                    default:
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>Warning:</strong> The current parent theme folder name is <code><?php echo esc_html($theme_folder_name); ?></code>, and it cannot be renamed to <code><?php echo esc_html($correct_theme_folder); ?></code>. Please check the file system permissions.</p>
                        </div>
                        <?php
                        break;
                }
            });
        }
    }
    // 当主题文件夹名称正确时，检查目录权限
    else {
        if (!is_writable($current_theme_path)) {
            add_action('admin_notices', function () use ($current_theme_path, $user_locale) {
                switch ($user_locale) {
                    case 'zh_CN':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 当前主题目录 <code><?php echo esc_html($current_theme_path); ?></code> 不可写。请检查文件系统权限。</p>
                        </div>
                        <?php
                        break;
                    case 'zh_TW':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 目前主題目錄 <code><?php echo esc_html($current_theme_path); ?></code> 不可寫。請檢查檔案系統權限。</p>
                        </div>
                        <?php
                        break;
                    case 'ja':
                    case 'ja_JP':
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>警告：</strong> 現在のテーマディレクトリ <code><?php echo esc_html($current_theme_path); ?></code> は書き込み不可です。ファイルシステムの権限を確認してください。</p>
                        </div>
                        <?php
                        break;
                    default:
                        ?>
                        <div class="notice notice-error is-dismissible">
                            <p><strong>Warning:</strong> The current theme directory <code><?php echo esc_html($current_theme_path); ?></code> is not writable. Please check the file system permissions.</p>
                        </div>
                        <?php
                        break;
                }
            });
        }
    }
}

// 在后台初始化时执行检查
add_action('admin_init', 'theme_folder_check_on_admin_init');

// 主动resize触发wp_scripts后台排版修正，防止左侧导航栏飞出
add_action('admin_footer', function () {
    ?><script>
        document.addEventListener('DOMContentLoaded',function() {
            window.dispatchEvent(new Event("resize"));
        })
    </script>
    <?php
});

//dashboard scheme
function dash_scheme($key, $name, $col1, $col2, $col3, $base, $focus, $current, $rules = "")
{
    $hash = 'rules=' . urlencode($rules);
    if ($col1) {
        $hash .= '&color_1=' . str_replace("#", "", $col1);
    }
    if ($col2) {
        $hash .= '&color_2=' . str_replace("#", "", $col2);
    }
    if ($col3) {
        $hash .= '&color_3=' . str_replace("#", "", $col3);
    }

    wp_admin_css_color(
        $key,
        $name,
        get_template_directory_uri() . "/inc/dash-scheme.php?" . $hash,
        array($col1, $col2, $col3),
        array('base' => $base, 'focus' => $focus, 'current' => $current)
    );
}

//Shinonomeiro
dash_scheme(
    $key = "sakurairo",
    $name = "Shinonomeiro🌅",
    $col1 = iro_opt('admin_second_class_color'),
    $col2 = iro_opt('admin_first_class_color'),
    $col3 = iro_opt('admin_emphasize_color'),
    $base = "#FFF",
    $focus = "#FFF",
    $current = "#FFF",
    $rules = 'body{background-image:url(' . iro_opt('admin_background') . ');background-attachment:fixed;background-size:cover;}'
);

// WordPress Custom style @ Admin
function custom_admin_open_sans_style()
{
    require get_template_directory() . '/inc/option-scheme.php';
}
add_action('admin_head', 'custom_admin_open_sans_style');

// WordPress Custom Font @ Admin
function custom_admin_open_sans_font()
{
    echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
    echo '<style>body, #wpadminbar *:not([class="ab-icon"]), .wp-core-ui, .media-menu, .media-frame *, .media-modal *{font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
}
add_action('admin_head', 'custom_admin_open_sans_font');

// WordPress Custom Font @ Admin Frontend Toolbar
function custom_admin_open_sans_font_frontend_toolbar()
{
    if (current_user_can('manage_options') && is_admin_bar_showing()) {
        echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
        echo '<style>#wpadminbar *:not([class="ab-icon"]){font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
    }
}
add_action('wp_head', 'custom_admin_open_sans_font_frontend_toolbar');

// WordPress Custom Font @ Admin Login
function custom_admin_open_sans_font_login_page()
{
    if (stripos($_SERVER['SCRIPT_NAME'], strrchr(wp_login_url(), '/')) !== false) {
        echo '<link href="https://' . iro_opt('gfonts_api', 'fonts.googleapis.com') . '/css?family=Noto+Serif+SC&display=swap" rel="stylesheet">' . PHP_EOL;
        echo '<style>body{font-family: "Noto Serif SC", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;}</style>' . PHP_EOL;
    }
}
add_action('login_head', 'custom_admin_open_sans_font_login_page');
