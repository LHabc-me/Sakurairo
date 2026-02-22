<?php
/**
 * Shinonomeiro Theme Customizer.
 * Use Kirki
 * https://github.com/themeum/kirki
 * @package Shinonomeiro
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 载入Kirki
if ( ! class_exists( 'Kirki' ) ) {
	require_once __DIR__ . '/kirki/kirki.php';
	new \Kirki\Pro\Init();

	define( 'KIRKI_NO_OUTPUT', true );
	define( 'KIRKI_NO_GUTENBERG_OUTPUT', true );
}

load_textdomain( 'Shinonomeiro_C', __DIR__ . '/lang/' . get_locale() . '.mo' );

// 面板部分
// 面板：每个面板至少包含 id、title，可选description（可选priority 将自动分配，描述不填自动为空）
$panels = [
    [
        'id'          => 'iro_user_profile',
        'title'       => esc_html__( '用户信息', 'Shinonomeiro_C' ),
        'description' => esc_html__( '头像、签名、社交账号与身份信息。', 'Shinonomeiro_C' ),
        'priority'    => 10,
    ],
    [
        'id'          => 'iro_custom_code',
        'title'       => esc_html__( '注入与自定义代码', 'Shinonomeiro_C' ),
        'description' => esc_html__( 'CSS、HTML 与脚本注入等自定义能力。', 'Shinonomeiro_C' ),
        'priority'    => 20,
    ],
    [
        'id'          => 'iro_home_display',
        'title'       => esc_html__( '首页展示', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首页首屏、导航、模块布局与视觉样式。', 'Shinonomeiro_C' ),
        'priority'    => 30,
    ],
    [
        'id'          => 'iro_article_reading',
        'title'       => esc_html__( '文章阅读', 'Shinonomeiro_C' ),
        'description' => esc_html__( '文章页展示、阅读辅助与版权扩展。', 'Shinonomeiro_C' ),
        'priority'    => 40,
    ],
    [
        'id'          => 'iro_comment_interaction',
        'title'       => esc_html__( '评论互动', 'Shinonomeiro_C' ),
        'description' => esc_html__( '评论区交互、表情与评论增强能力。', 'Shinonomeiro_C' ),
        'priority'    => 50,
    ],
    [
        'id'          => 'iro_performance',
        'title'       => esc_html__( '性能加速', 'Shinonomeiro_C' ),
        'description' => esc_html__( '加载策略、预加载、动画与性能体验优化。', 'Shinonomeiro_C' ),
        'priority'    => 60,
    ],
    [
        'id'          => 'iro_third_party',
        'title'       => esc_html__( '第三方服务', 'Shinonomeiro_C' ),
        'description' => esc_html__( '音乐、番剧、统计与外部数据服务接入。', 'Shinonomeiro_C' ),
        'priority'    => 70,
    ],
    [
        'id'          => 'iro_account_security',
        'title'       => esc_html__( '账号安全', 'Shinonomeiro_C' ),
        'description' => esc_html__( '登录、验证码与后台安全相关配置。', 'Shinonomeiro_C' ),
        'priority'    => 80,
    ],
    [
        'id'          => 'iro_dev_maintenance',
        'title'       => esc_html__( '开发维护', 'Shinonomeiro_C' ),
        'description' => esc_html__( '站点基础、更新通道、调试与维护工具。', 'Shinonomeiro_C' ),
        'priority'    => 90,
    ],
];

// 所有可以传递的参数列表（按 Themeum/Kirki 官方文档）
$allowed_params = [
	'tab',              // 所属section中的选项卡，
	'active_callback',  // 回调函数，决定该字段是否显示
	'capability',       // 所需权限
	'choices',          // 可选项，适用于下拉、单选、复选等类型
	'default',          // 默认值
	'description',      // 描述信息
	'fields',           // 用于 repeater 等控件，定义子字段
	'js_vars',          // 用于 postMessage 实时预览的 JS 配置
	'label',            // 字段标签（必填，未设置则默认空字符串）
	'multiple',         // 允许多选时使用
	'option_name',      // 当保存到 option 时指定 option 名称
	'option_type',      // 保存类型，'theme_mod' 或 'option'
	'output',           // 自动输出前端 CSS 的配置数组
	'partial_refresh',  // 部分刷新设置
	'preset',           // 预设值（如预设色板）
	'priority',         // 排序权重（必填，未填写将自动赋值）
	'sanitize_callback',// 数据过滤函数
	'section',          // 所属区块 ID（必填）
	'settings',         // 设置项 ID（必填）
	'tooltip',          // 字段提示信息
	'transport',        // 数据传输方式，如 'refresh' 或 'postMessage'，未设置的请设置iro_key，将请求php端渲染
	'iro_key',          // Shinonomeiro options键，
	                    // 使用的选项将实时上报更改信息，以进行复杂更改的渲染，
						// 同时也不用设置默认值，直接从iro_options中获取当前值
						// 也可以不设置回调，默认更新至 iro_options[iro_key]
	'iro_subkey'        // key的子键
];

$vision_resource_basepath = iro_opt('vision_resource_basepath', 'https://s.nmxc.ltd/sakurairo_vision/@3.0/');


if ( ! function_exists( 'iro_customizer_friendly_label_from_key' ) ) {
	function iro_customizer_friendly_label_from_key( $key ) {
		$raw_key = (string) $key;
		$explicit_label_map = [
			'random_graphs_mts' => esc_html__( '随机封面图数量', 'Shinonomeiro_C' ),
			'random_graphs_link' => esc_html__( '随机封面图接口地址', 'Shinonomeiro_C' ),
			'random_graphs_link_mobile' => esc_html__( '随机封面图移动端接口地址', 'Shinonomeiro_C' ),
			'random_graphs_options' => esc_html__( '随机封面图策略', 'Shinonomeiro_C' ),
			'cache_cover' => esc_html__( '封面图缓存', 'Shinonomeiro_C' ),
			'signature_typing_placeholder' => esc_html__( '打字签名占位文本', 'Shinonomeiro_C' ),
			'cover_video' => esc_html__( '封面视频开关', 'Shinonomeiro_C' ),
			'cover_video_loop' => esc_html__( '封面视频循环播放', 'Shinonomeiro_C' ),
			'cover_video_live' => esc_html__( '封面视频直播模式', 'Shinonomeiro_C' ),
			'cover_video_link' => esc_html__( '封面视频链接', 'Shinonomeiro_C' ),
			'cover_video_title' => esc_html__( '封面视频标题', 'Shinonomeiro_C' ),
			'statistics_api' => esc_html__( '填写统计接口地址。', 'Shinonomeiro_C' ),
			'statistics_format' => esc_html__( '设置统计展示格式模板。', 'Shinonomeiro_C' ),
			'google_analytics_id' => esc_html__( '填写 Google Analytics 测量 ID。', 'Shinonomeiro_C' ),
			'chatgpt_endpoint' => esc_html__( '填写 ChatGPT/OpenAI 兼容接口地址。', 'Shinonomeiro_C' ),
			'chatgpt_access_token' => esc_html__( '填写 ChatGPT 接口访问令牌。', 'Shinonomeiro_C' ),
			'chatgpt_model' => esc_html__( '设置默认调用模型名称。', 'Shinonomeiro_C' ),
			'chatgpt_max_tokens' => esc_html__( '设置单次生成最大 Token。', 'Shinonomeiro_C' ),
			'chatgpt_api_request_timeout' => esc_html__( '设置 ChatGPT 接口请求超时（秒）。', 'Shinonomeiro_C' ),
			'theme_darkmode_auto' => esc_html__( '跟随系统自动切换深色模式', 'Shinonomeiro_C' ),
			'nav_quick_theme_toggle' => esc_html__( '显示深色模式快捷按钮', 'Shinonomeiro_C' ),
			'nav_quick_lang_toggle' => esc_html__( '显示语言切换快捷按钮', 'Shinonomeiro_C' ),
			'theme_darkmode_strategy' => esc_html__( '深色模式切换策略', 'Shinonomeiro_C' ),
			'theme_darkmode_background_transparency' => esc_html__( '深色模式背景透明度', 'Shinonomeiro_C' ),
			'theme_commemorate_mode' => esc_html__( '纪念模式', 'Shinonomeiro_C' ),
			'load_out_svg' => esc_html__( '加载外部SVG图标', 'Shinonomeiro_C' ),
			'reference_exter_font' => esc_html__( '引用外部字体', 'Shinonomeiro_C' ),
			'exter_font' => esc_html__( '外部字体地址', 'Shinonomeiro_C' ),
			'gfonts_api' => esc_html__( 'Google字体接口地址', 'Shinonomeiro_C' ),
			'gfonts_add_name' => esc_html__( 'Google字体追加名称', 'Shinonomeiro_C' ),
			'preload_animation_color1' => esc_html__( '预加载动画颜色1', 'Shinonomeiro_C' ),
			'preload_animation_color2' => esc_html__( '预加载动画颜色2', 'Shinonomeiro_C' ),
			'preload_blur' => esc_html__( '预加载模糊效果', 'Shinonomeiro_C' ),
			'poi_pjax' => esc_html__( '启用PJAX过渡', 'Shinonomeiro_C' ),
			'pjax_keep_loading' => esc_html__( 'PJAX保持加载动画', 'Shinonomeiro_C' ),
			'missing_avatars_default' => esc_html__( '缺失头像默认图', 'Shinonomeiro_C' ),
			'missing_images_default' => esc_html__( '缺失图片默认图', 'Shinonomeiro_C' ),
			'captcha_select' => esc_html__( '选择登录页验证码方案。', 'Shinonomeiro_C' ),
			'comment_captcha_select' => esc_html__( '选择评论区验证码方案。', 'Shinonomeiro_C' ),
			'code_highlight_method' => esc_html__( '代码高亮引擎', 'Shinonomeiro_C' ),
			'php_notice_filter' => esc_html__( 'PHP 报错过滤级别', 'Shinonomeiro_C' ),
			'iro_update_channel' => esc_html__( '主题更新通道', 'Shinonomeiro_C' ),
			'show_location_in_manage' => esc_html__( '后台显示评论IP归属地', 'Shinonomeiro_C' ),
			'qq_qrcode' => esc_html__( '填写 QQ 二维码图片地址。', 'Shinonomeiro_C' ),
			'qq_qrcode_switch' => esc_html__( '控制是否显示 QQ 二维码入口。', 'Shinonomeiro_C' ),
			'qq' => esc_html__( 'QQ号', 'Shinonomeiro_C' ),
			'qq_copy_switch' => esc_html__( '启用后显示 QQ 号一键复制按钮。', 'Shinonomeiro_C' ),
		];
		if ( isset( $explicit_label_map[ $raw_key ] ) ) {
			return $explicit_label_map[ $raw_key ];
		}

		$token_map = [
			'iro' => '主题', 'meta' => '元信息', 'seo' => 'SEO', 'theme' => '主题', 'darkmode' => '深色模式',
			'background' => '背景', 'transparency' => '透明度', 'commemorate' => '纪念模式', 'load' => '加载',
			'out' => '外部', 'svg' => 'SVG', 'reference' => '引用', 'exter' => '外部', 'font' => '字体', 'favicon' => '站点图标', 'keywords' => '关键词', 'description' => '描述',
			'gfonts' => 'Google 字体', 'api' => '接口', 'add' => '添加', 'name' => '名称',
			'unlisted' => '未列出', 'avatar' => '头像', 'footer' => '页脚', 'addition' => '附加内容',
			'aplayer' => 'APlayer', 'server' => '服务端', 'custom' => '自定义', 'music' => '音乐',
			'proxy' => '代理', 'playlistid' => '歌单 ID', 'order' => '排序', 'preload' => '预加载', 'volume' => '音量',
			'cookie' => 'Cookie', 'search' => '搜索', 'filter' => '筛选', 'for' => '', 'shuoshuo' => '说说', 'pages' => '页面',
			'only' => '仅', 'admin' => '后台', 'can' => '可', 'sticky' => '置顶', 'pinned' => '置顶', 'content' => '内容',
			'exclude' => '排除', 'results' => '结果', 'live' => '实时', 'comment' => '评论',
			'animation' => '动画', 'color' => '颜色', 'color1' => '颜色1', 'color2' => '颜色2', 'blur' => '模糊',
			'poi' => '过渡', 'pjax' => 'PJAX', 'keep' => '保持', 'loading' => '加载态', 'missing' => '缺失', 'avatars' => '头像',
			'images' => '图片', 'default' => '默认', 'signature' => '签名', 'typing' => '打字效果', 'placeholder' => '占位文本',
			'random' => '随机', 'graphs' => '封面图', 'options' => '策略', 'mts' => '数量', 'link' => '链接', 'mobile' => '移动端',
			'cache' => '缓存', 'cover' => '封面', 'video' => '视频', 'loop' => '循环', 'title' => '标题',
			'social' => '社交', 'area' => '区域', 'display' => '显示', 'icon' => '图标', 'radius' => '圆角',
			'wechat' => '微信', 'qrcode' => '二维码', 'id' => 'ID', 'qq' => 'QQ', 'url' => '链接', 'copy' => '复制', 'switch' => '开关',
			'wangyiyun' => '网易云', 'sina' => '微博', 'github' => 'GitHub', 'telegram' => 'Telegram', 'steam' => 'Steam',
			'youtube' => 'YouTube', 'instagram' => 'Instagram', 'douyin' => '抖音', 'xiaohongshu' => '小红书',
			'discord' => 'Discord', 'zhihu' => '知乎', 'linkedin' => 'LinkedIn', 'twitter' => 'X/Twitter', 'facebook' => 'Facebook',
			'email' => '邮箱', 'domain' => '域名', 'diysocialicons' => '自定义社交图标', 'exhibition' => '展示位',
			'post' => '文章', 'clipboard' => '剪贴板', 'ref' => '引用', 'lazyload' => '懒加载', 'spinner' => '加载动画',
			'temp' => '临时', 'bangumi' => '番剧', 'source' => '来源', 'my' => '我的', 'anime' => '动漫', 'list' => '列表',
			'username' => '用户名', 'sort' => '排序', 'bilibili' => 'Bilibili', 'friend' => '友链', 'align' => '对齐',
			'form' => '表单', 'sorting' => '排序', 'mode' => '模式', 'key' => '密钥', 'store' => '商店',
			'smilies' => '表情包', 'dir' => '目录', 'useragent' => '用户代理', 'location' => '地理位置', 'show' => '显示',
			'save' => '保存', 'private' => '私信', 'message' => '消息', 'captcha' => '验证码', 'select' => '选择',
			'img' => '图片', 'upload' => '上传', 'max' => '最大', 'size' => '大小', 'imgur' => 'Imgur', 'smms' => 'SM.MS',
			'chevereto' => 'Chevereto', 'cheverto' => 'Chevereto', 'lsky' => 'Lsky', 'mail' => '邮件', 'notify' => '通知',
			'login' => '登录', 'logo' => 'Logo', 'vaptcha' => 'Vaptcha', 'vid' => 'VID', 'scene' => '场景',
			'turnstile' => 'Turnstile', 'site' => '站点', 'secret' => 'Secret', 'skip' => '跳过', 'language' => '语言', 'opt' => '选项',
			'left' => '左侧', 'first' => '一级', 'class' => '分类', 'second' => '二级', 'emphasize' => '强调', 'text' => '文本',
			'chatgpt' => 'ChatGPT', 'endpoint' => '接口', 'access' => '访问', 'token' => '令牌', 'request' => '请求', 'timeout' => '超时',
			'auto' => '自动', 'article' => '文章', 'summarize' => '摘要', 'annotations' => '注释', 'statistics' => '统计', 'analytics' => '分析',
			'level' => '等级', 'style' => '样式', 'header' => '页头', 'insert' => '插入', 'time' => '时间', 'zone' => '时区',
			'fix' => '修复', 'gravatar' => 'Gravatar', 'address' => '地址', 'of' => '', 'ghcard' => 'GitHub 卡片',
			'lightbox' => '灯箱', 'lightgallery' => '图库', 'code' => '代码', 'highlight' => '高亮', 'method' => '方式',
			'prism' => 'Prism', 'line' => '行号', 'number' => '编号', 'autoload' => '自动加载', 'path' => '路径',
			'light' => '浅色', 'enable' => '启用', 'mathjax' => 'MathJax', 'cdn' => 'CDN', 'classify' => '分类',
			'image' => '图片', 'category' => '分类', 'version' => '版本', 'hide' => '隐藏', 'portal' => '入口',
			'fontawesome' => 'FontAwesome', 'dev' => '开发', 'php' => 'PHP', 'notice' => '通知', 'iro_update' => '更新',
			'channel' => '通道', 'validate' => '校验', 'value' => '值', 'core' => '核心', 'library' => '库',
			'basepath' => '基础路径', 'shared' => '共享', 'lib' => '库', 'external' => '外部', 'vendor' => '供应方',
			'vision' => '视觉资源', 'resource' => '资源', 'send' => '发送'
		];

		$tokens = array_values( array_filter( explode( '_', strtolower( $raw_key ) ), static function( $token ) {
			return '' !== $token;
		} ) );
		$cn_parts = [];
		foreach ( $tokens as $token ) {
			if ( isset( $token_map[ $token ] ) ) {
				$mapped = $token_map[ $token ];
			} else {
				$mapped = preg_match( '/^[a-z0-9-]+$/', $token ) ? $token : strtoupper( $token );
			}
			if ( '' === trim( (string) $mapped ) ) {
				continue;
			}
			$last = end( $cn_parts );
			if ( false !== $last && $last === $mapped ) {
				continue;
			}
			$cn_parts[] = $mapped;
		}
		$label = trim( implode( '', $cn_parts ) );
		if ( '' !== $label ) {
			$label = str_replace( '模式模式', '模式', $label );
			$label = str_replace( '置顶置顶', '置顶', $label );
			$label = str_replace( 'Google字体', 'Google字体', $label );
			return $label;
		}

		return $raw_key;
	}
}

if ( ! function_exists( 'iro_customizer_localize_ui_text' ) ) {
	function iro_customizer_localize_ui_text( $text ) {
		$text = is_string( $text ) ? trim( $text ) : '';
		if ( '' === $text ) {
			return $text;
		}
		if ( ! preg_match( '/[A-Za-z]/', $text ) ) {
			return $text;
		}
		// 已经包含中文时，避免把“QQ二维码链接”这类文本误降级为“qq”
		if ( preg_match( '/[\x{4e00}-\x{9fff}]/u', $text ) ) {
			return $text;
		}

		$phrase_map = [
			'Nav Menu Style' => '导航菜单样式',
			'Cover Info Bar' => '首屏信息栏',
			'Cover Signature Field Text' => '首屏签名文本',
			'Cover Signature Field Text Font' => '首屏签名字体',
			'Cover Signature Field Text Font Size' => '首屏签名字号',
			'Dark Mode Theme Color' => '深色模式主题色',
			'Dark Mode Image Brightness' => '深色模式图片亮度',
			'Dark Mode Component Transparency' => '深色模式组件透明度',
			'Theme Color' => '主题色',
			'Matching Color' => '主题配色',
			'Cover Full Screen' => '首屏全屏显示',
			'Cover Animation Time' => '首屏动画时长',
			'Cover Widget Transparency' => '首屏组件透明度',
			'Avatar Radius' => '头像圆角',
			'Background Image' => '背景图片',
			'Google Analytics ID' => 'Google Analytics ID',
			'ChatGPT' => 'ChatGPT',
			'APlayer' => 'APlayer',
			'Bilibili' => 'Bilibili',
			'Steam' => 'Steam',
			'Turnstile' => 'Cloudflare Turnstile',
			'Logo' => 'Logo',
			'URL' => '链接',
		];
		if ( isset( $phrase_map[ $text ] ) ) {
			return $phrase_map[ $text ];
		}

		$word_map = [
			'nav' => '导航', 'menu' => '菜单', 'style' => '样式', 'cover' => '首屏', 'info' => '信息', 'bar' => '栏',
			'signature' => '签名', 'field' => '', 'text' => '文本', 'font' => '字体', 'size' => '字号', 'dark' => '深色',
			'mode' => '模式', 'theme' => '主题', 'color' => '颜色', 'image' => '图片', 'brightness' => '亮度',
			'component' => '组件', 'transparency' => '透明度', 'full' => '全屏', 'screen' => '显示', 'animation' => '动画',
			'time' => '时长', 'widget' => '组件', 'radius' => '圆角', 'avatar' => '头像', 'background' => '背景',
			'google' => 'Google', 'analytics' => 'Analytics', 'chatgpt' => 'ChatGPT', 'player' => '播放器',
			'captcha' => '验证码', 'login' => '登录', 'comment' => '评论', 'switch' => '开关', 'enable' => '启用',
			'auto' => '自动', 'load' => '加载', 'lazy' => '懒', 'search' => '搜索', 'default' => '默认',
			'video' => '视频', 'loop' => '循环', 'live' => '直播', 'link' => '链接', 'title' => '标题',
			'left' => '左侧', 'right' => '右侧', 'first' => '一级', 'second' => '二级', 'class' => '分类',
			'admin' => '后台', 'logo' => 'Logo', 'api' => 'API', 'cdn' => 'CDN', 'json' => 'JSON', 'pjax' => 'PJAX',
			'prism' => 'Prism', 'mathjax' => 'MathJax', 'fontawesome' => 'FontAwesome',
		];
		$parts = preg_split( '/[^A-Za-z0-9]+/', strtolower( $text ) );
		$zh_parts = [];
		$unknown_word_count = 0;
		foreach ( $parts as $part ) {
			if ( '' === $part ) {
				continue;
			}
			if ( isset( $word_map[ $part ] ) ) {
				$zh_parts[] = $word_map[ $part ];
				continue;
			}
			if ( preg_match( '/^[a-z0-9]+$/', $part ) ) {
				$unknown_word_count++;
			}
			$zh_parts[] = $part;
		}
		if ( $unknown_word_count > 0 ) {
			// 避免退化成“首屏dropdownarrow”这类中英混杂：未完整映射时保留原文。
			return $text;
		}
		$localized = implode( '', array_filter( $zh_parts, static function( $v ) {
			return '' !== trim( (string) $v );
		} ) );
		$localized = str_replace( '模式模式', '模式', $localized );
		$localized = str_replace( '首屏首屏', '首屏', $localized );
		return '' === $localized ? $text : $localized;
	}
}

if ( ! function_exists( 'iro_customizer_sanitize_json_or_text' ) ) {
	function iro_customizer_sanitize_json_or_text( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}
		$value = is_string( $value ) ? trim( $value ) : '';
		if ( '' === $value ) {
			return '';
		}
		$decoded = json_decode( $value, true );
		if ( JSON_ERROR_NONE === json_last_error() ) {
			return $decoded;
		}
		return $value;
	}
}

if ( ! function_exists( 'iro_customizer_legacy_description_from_key' ) ) {
	function iro_customizer_legacy_description_from_key( $legacy_key ) {
		$description_map = [
			'favicon_link' => esc_html__( '设置浏览器标签页显示的站点图标地址。', 'Shinonomeiro_C' ),
			'iro_seo' => esc_html__( '控制主题内置 SEO 元信息输出开关。', 'Shinonomeiro_C' ),
			'iro_meta_keywords' => esc_html__( '填写首页与文章页可复用的关键词元信息。', 'Shinonomeiro_C' ),
			'iro_meta_description' => esc_html__( '填写默认页面描述元信息，未单独设置摘要时会优先使用。', 'Shinonomeiro_C' ),
			'theme_darkmode_auto' => esc_html__( '启用后按访客系统外观自动切换明暗模式。', 'Shinonomeiro_C' ),
			'nav_quick_theme_toggle' => esc_html__( '控制导航快捷区中“深色模式切换”按钮是否显示。', 'Shinonomeiro_C' ),
			'nav_quick_lang_toggle' => esc_html__( '控制导航快捷区中“语言切换”按钮是否显示。', 'Shinonomeiro_C' ),
			'theme_darkmode_strategy' => esc_html__( '设置深色模式触发策略（手动、跟随系统或定时）。', 'Shinonomeiro_C' ),
			'theme_darkmode_background_transparency' => esc_html__( '调整深色模式下背景层透明度，影响页面整体明暗对比。', 'Shinonomeiro_C' ),
			'theme_commemorate_mode' => esc_html__( '开启纪念日主题色模式，在指定日期切换为纪念样式。', 'Shinonomeiro_C' ),
			'load_out_svg' => esc_html__( '控制外链图标 SVG 资源是否加载。', 'Shinonomeiro_C' ),
			'reference_exter_font' => esc_html__( '设置外部字体引用方式（本地/远程）。', 'Shinonomeiro_C' ),
			'exter_font' => esc_html__( '填写外部字体 CSS 地址，用于替换默认字体源。', 'Shinonomeiro_C' ),
			'gfonts_api' => esc_html__( '设置 Google Fonts API 基础地址或代理地址。', 'Shinonomeiro_C' ),
			'gfonts_add_name' => esc_html__( '补充要额外加载的 Google 字体名称。', 'Shinonomeiro_C' ),
			'unlisted_avatar' => esc_html__( '设置评论区未知邮箱头像的兜底图片。', 'Shinonomeiro_C' ),
			'footer_addition' => esc_html__( '在全站页脚插入自定义 HTML 或脚本片段。', 'Shinonomeiro_C' ),
			'aplayer_server' => esc_html__( '设置 APlayer 的音乐源类型（如 netease / tencent / kugou）。', 'Shinonomeiro_C' ),
			'custom_music_api' => esc_html__( '填写自定义音乐 API 地址，用于替换默认 Meting 接口。', 'Shinonomeiro_C' ),
			'aplayer_server_proxy' => esc_html__( '填写 APlayer 代理地址，用于转发音乐接口请求。', 'Shinonomeiro_C' ),
			'aplayer_playlistid' => esc_html__( '填写默认加载的歌单 ID。', 'Shinonomeiro_C' ),
			'aplayer_order' => esc_html__( '设置播放列表顺序（顺序/随机）。', 'Shinonomeiro_C' ),
			'aplayer_preload' => esc_html__( '设置音频预加载策略（none/metadata/auto）。', 'Shinonomeiro_C' ),
			'aplayer_volume' => esc_html__( '设置播放器初始音量。', 'Shinonomeiro_C' ),
			'aplayer_cookie' => esc_html__( '填写请求音乐接口时附带的 Cookie。', 'Shinonomeiro_C' ),
			'search_filter' => esc_html__( '设置站内搜索的关键词过滤规则。', 'Shinonomeiro_C' ),
			'search_for_shuoshuo' => esc_html__( '决定搜索结果是否包含说说（短文）内容。', 'Shinonomeiro_C' ),
			'search_for_pages' => esc_html__( '决定搜索结果是否包含独立页面。', 'Shinonomeiro_C' ),
			'only_admin_can_search_pages' => esc_html__( '开启后仅管理员可在搜索中检索独立页面。', 'Shinonomeiro_C' ),
			'sticky_pinned_content' => esc_html__( '设置首页置顶内容 ID 或规则。', 'Shinonomeiro_C' ),
			'custom_exclude_search_results' => esc_html__( '填写需要从搜索结果排除的文章/页面 ID。', 'Shinonomeiro_C' ),
			'live_search' => esc_html__( '启用导航栏即时搜索建议。', 'Shinonomeiro_C' ),
			'live_search_comment' => esc_html__( '设置即时搜索下方的提示说明文案。', 'Shinonomeiro_C' ),
			'preload_animation' => esc_html__( '选择页面预加载动画样式。', 'Shinonomeiro_C' ),
			'preload_animation_color1' => esc_html__( '设置预加载动画主色。', 'Shinonomeiro_C' ),
			'preload_animation_color2' => esc_html__( '设置预加载动画辅助色。', 'Shinonomeiro_C' ),
			'preload_blur' => esc_html__( '设置预加载遮罩模糊强度。', 'Shinonomeiro_C' ),
			'poi_pjax' => esc_html__( '启用 PJAX 无刷新跳转。', 'Shinonomeiro_C' ),
			'pjax_keep_loading' => esc_html__( '启用后在 PJAX 切页时持续显示加载指示。', 'Shinonomeiro_C' ),
			'missing_avatars_default' => esc_html__( '设置用户未上传头像时的默认头像。', 'Shinonomeiro_C' ),
			'missing_images_default' => esc_html__( '设置文章无特色图时的默认封面。', 'Shinonomeiro_C' ),
			'signature_typing_placeholder' => esc_html__( '设置签名打字机效果的占位文本。', 'Shinonomeiro_C' ),
			'random_graphs_options' => esc_html__( '配置首页随机封面图来源策略。', 'Shinonomeiro_C' ),
			'random_graphs_mts' => esc_html__( '设置随机封面图切换间隔（毫秒）。', 'Shinonomeiro_C' ),
			'random_graphs_link' => esc_html__( '填写桌面端随机封面图 API 地址。', 'Shinonomeiro_C' ),
			'random_graphs_link_mobile' => esc_html__( '填写移动端随机封面图 API 地址。', 'Shinonomeiro_C' ),
			'cache_cover' => esc_html__( '设置封面图缓存开关与刷新策略。', 'Shinonomeiro_C' ),
			'cover_video' => esc_html__( '启用首页封面视频模式。', 'Shinonomeiro_C' ),
			'cover_video_loop' => esc_html__( '设置封面视频是否循环播放。', 'Shinonomeiro_C' ),
			'cover_video_live' => esc_html__( '设置封面视频按直播流方式加载。', 'Shinonomeiro_C' ),
			'cover_video_link' => esc_html__( '填写首页封面视频地址。', 'Shinonomeiro_C' ),
			'cover_video_title' => esc_html__( '设置封面视频区域显示标题。', 'Shinonomeiro_C' ),
			'social_area' => esc_html__( '控制首页社交图标区域是否显示。', 'Shinonomeiro_C' ),
			'social_display_icon' => esc_html__( '选择社交区域的图标风格与展示方式。', 'Shinonomeiro_C' ),
			'social_area_radius' => esc_html__( '调整社交图标区域容器圆角大小。', 'Shinonomeiro_C' ),
			'wechat_qrcode_switch' => esc_html__( '控制是否显示微信二维码入口。', 'Shinonomeiro_C' ),
			'wechat_qrcode' => esc_html__( '填写微信二维码图片地址。', 'Shinonomeiro_C' ),
			'wechat_id' => esc_html__( '填写微信号文本。', 'Shinonomeiro_C' ),
			'wechat_copy_switch' => esc_html__( '启用后显示微信号一键复制按钮。', 'Shinonomeiro_C' ),
			'wechat_url' => esc_html__( '填写微信外链地址（有值时优先跳转链接）。', 'Shinonomeiro_C' ),
			'qq_qrcode_switch' => esc_html__( '控制是否显示 QQ 二维码入口。', 'Shinonomeiro_C' ),
			'qq_qrcode' => esc_html__( '填写 QQ 二维码图片地址。', 'Shinonomeiro_C' ),
			'qq_id' => esc_html__( '填写 QQ 号文本。', 'Shinonomeiro_C' ),
			'qq_copy_switch' => esc_html__( '启用后显示 QQ 号一键复制按钮。', 'Shinonomeiro_C' ),
			'qq_url' => esc_html__( '填写 QQ 外链地址（有值时优先跳转链接）。', 'Shinonomeiro_C' ),
			'bili' => esc_html__( '填写 bili 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'wangyiyun' => esc_html__( '填写 wangyiyun 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'sina' => esc_html__( '填写 sina 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'github' => esc_html__( '填写 github 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'telegram' => esc_html__( '填写 telegram 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'steam' => esc_html__( '填写 steam 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'youtube' => esc_html__( '填写 youtube 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'instagram' => esc_html__( '填写 instagram 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'douyin' => esc_html__( '填写 douyin 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'xiaohongshu' => esc_html__( '填写 xiaohongshu 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'discord' => esc_html__( '填写 discord 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'zhihu' => esc_html__( '填写 zhihu 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'linkedin' => esc_html__( '填写 linkedin 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'twitter' => esc_html__( '填写 twitter 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'facebook' => esc_html__( '填写 facebook 社交入口链接，控制首页社交图标跳转。', 'Shinonomeiro_C' ),
			'email_name' => esc_html__( '设置社交卡片展示邮箱的名称与域名。', 'Shinonomeiro_C' ),
			'email_domain' => esc_html__( '设置社交卡片展示邮箱的名称与域名。', 'Shinonomeiro_C' ),
			'diysocialicons' => esc_html__( '配置自定义社交图标列表（图标、链接与顺序）。', 'Shinonomeiro_C' ),
			'exhibition' => esc_html__( '设置首页展示区内容（如卡片、公告或自定义模块）。', 'Shinonomeiro_C' ),
			'post_cover_options' => esc_html__( '设置文章封面图启用策略与优先级。', 'Shinonomeiro_C' ),
			'post_cover' => esc_html__( '指定文章默认封面图地址。', 'Shinonomeiro_C' ),
			'clipboard_ref' => esc_html__( '复制文章内容时附加来源信息（版权引用尾注）。', 'Shinonomeiro_C' ),
			'page_lazyload' => esc_html__( '启用页面元素懒加载，减少首屏加载压力。', 'Shinonomeiro_C' ),
			'page_lazyload_spinner' => esc_html__( '设置懒加载占位动画样式。', 'Shinonomeiro_C' ),
			'page_temp_title_font_size' => esc_html__( '调整页面临时标题字体大小。', 'Shinonomeiro_C' ),
			'bangumi_source' => esc_html__( '选择番剧数据来源（B站 / Bangumi / MyAnimeList）。', 'Shinonomeiro_C' ),
			'my_anime_list_username' => esc_html__( '填写 MyAnimeList 用户名。', 'Shinonomeiro_C' ),
			'my_anime_list_sort' => esc_html__( '设置 MyAnimeList 番剧排序方式。', 'Shinonomeiro_C' ),
			'bilibili_id' => esc_html__( '填写 Bilibili 用户 UID。', 'Shinonomeiro_C' ),
			'bilibili_cookie' => esc_html__( '填写 Bilibili Cookie（用于读取需登录的数据）。', 'Shinonomeiro_C' ),
			'bangumi_id' => esc_html__( '填写 Bangumi 用户 ID。', 'Shinonomeiro_C' ),
			'bangumi_cache' => esc_html__( '设置番剧数据缓存时长（秒）。', 'Shinonomeiro_C' ),
			'friend_link_align' => esc_html__( '设置友链卡片对齐方式。', 'Shinonomeiro_C' ),
			'friend_link_form' => esc_html__( '控制是否显示友链申请表单。', 'Shinonomeiro_C' ),
			'friend_link_sorting_mode' => esc_html__( '设置友链排序字段。', 'Shinonomeiro_C' ),
			'friend_link_order' => esc_html__( '设置友链排序方向（升序/降序）。', 'Shinonomeiro_C' ),
			'steam_id' => esc_html__( '填写 Steam 用户 ID，用于读取游戏库数据。', 'Shinonomeiro_C' ),
			'steam_key' => esc_html__( '填写 Steam Web API Key。', 'Shinonomeiro_C' ),
			'steam_covercdn' => esc_html__( '设置 Steam 封面图 CDN 地址。', 'Shinonomeiro_C' ),
			'steam_store' => esc_html__( '设置 Steam 商店地区（影响商店链接与价格区）。', 'Shinonomeiro_C' ),
			'steam_cache' => esc_html__( '设置 Steam 数据缓存时长（秒）。', 'Shinonomeiro_C' ),
			'smilies_name' => esc_html__( '设置自定义表情包显示名称。', 'Shinonomeiro_C' ),
			'smilies_dir' => esc_html__( '设置表情包资源目录或前缀路径。', 'Shinonomeiro_C' ),
			'smilies_proxy' => esc_html__( '设置表情资源代理地址。', 'Shinonomeiro_C' ),
			'comment_useragent' => esc_html__( '控制评论区是否显示访客浏览器信息。', 'Shinonomeiro_C' ),
			'comment_location' => esc_html__( '控制评论区是否显示访客 IP 归属地。', 'Shinonomeiro_C' ),
			'show_location_in_manage' => esc_html__( '控制评论 IP 归属地的记录与后台展示。', 'Shinonomeiro_C' ),
			'save_location' => esc_html__( '控制评论 IP 归属地的记录与后台展示。', 'Shinonomeiro_C' ),
			'comment_private_message' => esc_html__( '启用评论悄悄话功能。', 'Shinonomeiro_C' ),
			'comment_captcha_select' => esc_html__( '选择评论区验证码方案（关闭 / 主题验证码 / Turnstile）。', 'Shinonomeiro_C' ),
			'qq_avatar_link' => esc_html__( '填写 QQ 头像接口地址模板。', 'Shinonomeiro_C' ),
			'img_upload_api' => esc_html__( '选择评论图片上传通道（Imgur/SM.MS/Chevereto/Lsky 等）。', 'Shinonomeiro_C' ),
			'img_upload_max_size' => esc_html__( '设置评论图片上传大小上限（KB）。', 'Shinonomeiro_C' ),
			'imgur_client_id' => esc_html__( '填写 Imgur Client ID。', 'Shinonomeiro_C' ),
			'imgur_upload_image_proxy' => esc_html__( '设置 Imgur 上传代理地址。', 'Shinonomeiro_C' ),
			'smms_client_id' => esc_html__( '填写 SM.MS Token/Client ID。', 'Shinonomeiro_C' ),
			'chevereto_api_key' => esc_html__( '填写 Chevereto API Key。', 'Shinonomeiro_C' ),
			'cheverto_url' => esc_html__( '填写 Chevereto 上传接口地址。', 'Shinonomeiro_C' ),
			'lsky_api_key' => esc_html__( '填写 Lsky API Key。', 'Shinonomeiro_C' ),
			'lsky_url' => esc_html__( '填写 Lsky 上传接口地址。', 'Shinonomeiro_C' ),
			'comment_image_proxy' => esc_html__( '设置评论区图片加载代理地址。', 'Shinonomeiro_C' ),
			'mail_notify' => esc_html__( '设置评论或系统事件的邮件通知开关。', 'Shinonomeiro_C' ),
			'admin_notify' => esc_html__( '设置评论或系统事件的邮件通知开关。', 'Shinonomeiro_C' ),
			'custom_login_switch' => esc_html__( '启用自定义登录页样式。', 'Shinonomeiro_C' ),
			'login_logo_img' => esc_html__( '设置登录页 Logo 图片地址。', 'Shinonomeiro_C' ),
			'captcha_select' => esc_html__( '选择登录页验证码方案（关闭 / 主题验证码 / Vaptcha / Turnstile）。', 'Shinonomeiro_C' ),
			'vaptcha_vid' => esc_html__( '填写 Vaptcha VID。', 'Shinonomeiro_C' ),
			'vaptcha_key' => esc_html__( '填写 Vaptcha Key。', 'Shinonomeiro_C' ),
			'vaptcha_scene' => esc_html__( '设置 Vaptcha 场景值。', 'Shinonomeiro_C' ),
			'turnstile_site_key' => esc_html__( '填写 Cloudflare Turnstile Site Key。', 'Shinonomeiro_C' ),
			'turnstile_secret_key' => esc_html__( '填写 Cloudflare Turnstile Secret Key。', 'Shinonomeiro_C' ),
			'turnstile_theme' => esc_html__( '设置 Turnstile 组件主题（light/dark/auto）。', 'Shinonomeiro_C' ),
			'login_urlskip' => esc_html__( '启用后登录后跳过默认跳转流程。', 'Shinonomeiro_C' ),
			'login_language_opt' => esc_html__( '设置登录页语言切换选项。', 'Shinonomeiro_C' ),
			'admin_background' => esc_html__( '设置后台页面背景样式。', 'Shinonomeiro_C' ),
			'admin_left_style' => esc_html__( '设置后台左侧菜单样式。', 'Shinonomeiro_C' ),
			'admin_first_class_color' => esc_html__( '设置后台一级菜单颜色。', 'Shinonomeiro_C' ),
			'admin_second_class_color' => esc_html__( '设置后台二级菜单颜色。', 'Shinonomeiro_C' ),
			'admin_emphasize_color' => esc_html__( '设置后台强调色。', 'Shinonomeiro_C' ),
			'admin_text_color' => esc_html__( '设置后台文本颜色。', 'Shinonomeiro_C' ),
			'chatgpt_endpoint' => esc_html__( '填写 ChatGPT/OpenAI 兼容接口地址。', 'Shinonomeiro_C' ),
			'chatgpt_access_token' => esc_html__( '填写 ChatGPT 接口访问令牌。', 'Shinonomeiro_C' ),
			'chatgpt_max_tokens' => esc_html__( '设置单次生成最大 Token。', 'Shinonomeiro_C' ),
			'chatgpt_model' => esc_html__( '设置默认调用模型名称。', 'Shinonomeiro_C' ),
			'chatgpt_api_request_timeout' => esc_html__( '设置 ChatGPT 接口请求超时（秒）。', 'Shinonomeiro_C' ),
			'chatgpt_auto_article_summarize' => esc_html__( '启用后自动生成文章摘要。', 'Shinonomeiro_C' ),
			'chatgpt_exclude_ids' => esc_html__( '填写不参与自动摘要的文章 ID 列表。', 'Shinonomeiro_C' ),
			'chatgpt_init_prompt' => esc_html__( '设置 ChatGPT 初始化系统提示词。', 'Shinonomeiro_C' ),
			'chatgpt_annotations_prompt' => esc_html__( '设置注释/批注生成提示词模板。', 'Shinonomeiro_C' ),
			'statistics_api' => esc_html__( '设置站点统计功能的statistics api。', 'Shinonomeiro_C' ),
			'statistics_format' => esc_html__( '设置站点统计功能的statistics format。', 'Shinonomeiro_C' ),
			'google_analytics_id' => esc_html__( '设置站点统计功能的google analytics id。', 'Shinonomeiro_C' ),
			'iro_captcha_level' => esc_html__( '设置主题验证码触发等级（越高限制越严格）。', 'Shinonomeiro_C' ),
			'site_custom_style' => esc_html__( '填写站点自定义 CSS 代码。', 'Shinonomeiro_C' ),
			'site_header_insert' => esc_html__( '在页面 <head> 中插入自定义代码（如站长验证/统计脚本）。', 'Shinonomeiro_C' ),
			'time_zone_fix' => esc_html__( '启用时区修正，统一前后台时间显示偏差。', 'Shinonomeiro_C' ),
			'gravatar_proxy' => esc_html__( '为 Gravatar 头像请求启用代理通道。', 'Shinonomeiro_C' ),
			'custom_proxy_address_of_gravatar' => esc_html__( '自定义 Gravatar 代理地址（覆盖默认代理源）。', 'Shinonomeiro_C' ),
			'ghcard_proxy' => esc_html__( '为 GitHub 卡片数据请求设置代理地址。', 'Shinonomeiro_C' ),
			'lightbox' => esc_html__( '启用图片灯箱查看效果。', 'Shinonomeiro_C' ),
			'lightgallery_option' => esc_html__( '设置 LightGallery 参数（缩略图、动画等）。', 'Shinonomeiro_C' ),
			'code_highlight_method' => esc_html__( '选择代码高亮引擎（如 Prism 或关闭高亮）。', 'Shinonomeiro_C' ),
			'code_highlight_prism_line_number_all' => esc_html__( '为所有代码块启用 Prism 行号。', 'Shinonomeiro_C' ),
			'code_highlight_prism_autoload_path' => esc_html__( '设置 Prism 插件自动加载资源路径。', 'Shinonomeiro_C' ),
			'code_highlight_prism_theme_light' => esc_html__( '设置浅色模式下 Prism 代码高亮主题。', 'Shinonomeiro_C' ),
			'code_highlight_prism_theme_dark' => esc_html__( '设置深色模式下 Prism 代码高亮主题。', 'Shinonomeiro_C' ),
			'enable_theme_mathjax' => esc_html__( '启用 MathJax 数学公式渲染。', 'Shinonomeiro_C' ),
			'image_cdn' => esc_html__( '设置图片资源 CDN 前缀，加速图片加载。', 'Shinonomeiro_C' ),
			'classify_display' => esc_html__( '设置分类页与标签页的展示样式。', 'Shinonomeiro_C' ),
			'image_category' => esc_html__( '设置图片分类页的资源来源与展示规则。', 'Shinonomeiro_C' ),
			'cookie_version' => esc_html__( '更新 Cookie 版本号以强制前端缓存失效。', 'Shinonomeiro_C' ),
			'hide_login_portal' => esc_html__( '隐藏前台登录入口按钮（不影响直接访问登录页）。', 'Shinonomeiro_C' ),
			'fontawesome_source' => esc_html__( '选择 Font Awesome 资源加载来源。', 'Shinonomeiro_C' ),
			'dev_mode' => esc_html__( '启用开发模式（减少缓存，便于调试样式与脚本）。', 'Shinonomeiro_C' ),
			'php_notice_filter' => esc_html__( '设置 PHP 报错过滤级别，控制前台提示噪声。', 'Shinonomeiro_C' ),
			'iro_update_source' => esc_html__( '选择主题更新源（官方、GitHub 或镜像）。', 'Shinonomeiro_C' ),
			'channel_validate_value' => esc_html__( '测试通道风险确认文本（需完整填写后才可切换测试通道）。', 'Shinonomeiro_C' ),
			'iro_update_channel' => esc_html__( '主题更新通道（稳定版 / Beta / 预览版）。', 'Shinonomeiro_C' ),
			'core_library_basepath' => esc_html__( '核心前端资源本地托管基础路径。', 'Shinonomeiro_C' ),
			'shared_library_basepath' => esc_html__( '共享前端资源本地托管基础路径。', 'Shinonomeiro_C' ),
			'lib_cdn_path' => esc_html__( '第三方库 CDN 基础路径。', 'Shinonomeiro_C' ),
			'external_vendor_lib' => esc_html__( '启用外部供应商库地址，替换内置库来源。', 'Shinonomeiro_C' ),
			'vision_resource_basepath' => esc_html__( '视觉资源基础路径（主题预览图与装饰资源）。', 'Shinonomeiro_C' ),
			'send_theme_version' => esc_html__( '请求资源时附带主题版本号，用于缓存刷新与问题排查。', 'Shinonomeiro_C' ),
		];

		return isset( $description_map[ $legacy_key ] ) ? $description_map[ $legacy_key ] : '';
	}
}

add_action( 'customize_register', function( $wp_customize ) {
	$nav_menus_panel = $wp_customize->get_panel( 'nav_menus' );
	if ( $nav_menus_panel ) {
		$nav_menus_panel->title = esc_html__( 'WP 核心设置', 'Shinonomeiro_C' );
		$nav_menus_panel->description = esc_html__( '集中管理 WordPress 核心的菜单、主页显示与额外 CSS。', 'Shinonomeiro_C' );
		$nav_menus_panel->priority = 5;
	}

	$core_section_ids = [ 'static_front_page', 'custom_css' ];
	foreach ( $core_section_ids as $section_id ) {
		$core_section = $wp_customize->get_section( $section_id );
		if ( $core_section ) {
			$core_section->panel = 'nav_menus';
		}
	}
}, 1000 );

// 分组和设置项部分
// 分组：每个分组至少包含 id、title、description、所属面板 panel
// 设置项（Field）数组：每个设置项至少包含 type、settings、label、所属区块 section
$sections = [
	// ====================导航栏====================
	[
        'id'          => 'iro_nav',
        'title'       => esc_html__( '导航栏', 'Shinonomeiro_C' ),
        'description' => esc_html__( '导航栏样式、布局与交互。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'custom',
				'settings' => 'nav_menu_notice',
				'default'  => __('<p>You can edit your nav menu options <a href="/wp-admin/nav-menus.php">here</a></p>','Shinonomeiro_C'),
			],
			[
				'type'     => 'radio_image',
				'settings' => 'choice_of_nav_style',
				'iro_key'  => 'choice_of_nav_style',
				'label'    => esc_html__( 'Nav Menu Style', 'Shinonomeiro_C' ),
				'choices'     => [
					'iro' => $vision_resource_basepath . 'options/nav_menu_style_Island.webp',
					'sakura' => $vision_resource_basepath . 'options/nav_menu_style_bar.webp',
				],
			],
			[
				'type'     => 'select',
				'settings' => 'nav_menu_style',
				'iro_key'  => 'nav_menu_style',
				'label'    => esc_html__( 'Spirit Island Nav Style', 'Shinonomeiro_C' ),
				'choices'     => [
					'center' => __('Always centered','Shinonomeiro_C'),
					'space-between' => __('Dispersed','Shinonomeiro_C'),
				],
				'active_callback' => [
					[
						'setting'  => 'choice_of_nav_style',
						'operator' => '==',
						'value'    => 'iro',
					]
				],
			],
			[
				'type'     => 'slider',
				'settings' => 'nav_menu_cover_radius',
				'label'    => esc_html__( 'Nav Menu Radius', 'Shinonomeiro_C' ),
				'iro_key'  => 'nav_menu_cover_radius',
				'transport'   => 'auto',
				'choices'     => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'output' => array(
					array(
						'element'  => array('.site-branding',
										'.nav-search-wrapper',
										'.user-menu-wrapper',
										'.nav-search-wrapper nav ul li a',
										'.searchbox.js-toggle-search i',
										'.bg-switch i',
										'.site-header'),
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				)
			],
			[
				'type'     => 'select',
				'settings' => 'sakura_nav_style_style',
				'label'    => esc_html__( 'Classic Nav Style', 'Shinonomeiro_C' ),
				'iro_key'  => 'sakura_nav_style',
				'iro_subkey'  => 'style',
				'choices'     => [
					'sakura' => __('Loose','Shinonomeiro_C'),
					'sakurairo' => __('Standered','Shinonomeiro_C'),
				],
				'active_callback' => [
					[
						'setting'  => 'choice_of_nav_style',
						'operator' => '!=',
						'value'    => 'iro',
					]
				],
			],
			[
				'type'     => 'select',
				'settings' => 'sakura_nav_style_distribution', //分布
				'label'    => esc_html__( 'Nav Menu Options Display Method', 'Shinonomeiro_C' ),
				'iro_key'  => 'sakura_nav_style',
				'iro_subkey'  => 'distribution',
				'choices'     => [
					'left' => __('Keep to the left','Shinonomeiro_C'),
					'right' => __('Keep to the right','Shinonomeiro_C'),
					'center' => __('Always centered','Shinonomeiro_C'),
				],
				'active_callback' => [
					[
						'setting'  => 'choice_of_nav_style',
						'operator' => '!=',
						'value'    => 'iro',
					]
				],
				'transport'   => 'postMessage',
				'output' => array(
					array(
						'element'  => '.menu-wrapper .sakura_nav .menu',
						'property' => 'justify-content',
						'value_pattern' => '$ !important',
					),
				)
			],
			[
				'type'     => 'slider',
				'settings' => 'sakura_nav_style_option_spacing',
				'label'    => esc_html__( 'Menu option left and right spacing', 'Shinonomeiro_C' ),
				'iro_key'  => 'sakura_nav_style',
				'iro_subkey'  => 'option_spacing',
				'active_callback' => [
					[
						'setting'  => 'choice_of_nav_style',
						'operator' => '!=',
						'value'    => 'iro',
					]
				],
				'transport'   => 'auto',
				'choices'     => [
					'min'  => 1,
					'max'  => 150,
					'step' => 1,
				],
				'output' => array(
					array(
						'element'  => 'nav ul li',
						'property' => 'margin', 
						'value_pattern' => '0 $px !important',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'nav_menu_font',
				'label'    => esc_html__( 'Nav Menu Font', 'Shinonomeiro_C' ),
				'iro_key'  => 'nav_menu_font',
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => array( '.site-header a','.header-user-name','.header-user-menu a' ),
						'property' => 'font-family',
					),
				)
			],
			[
				'type'     => 'image',
				'settings' => 'iro_logo',
				'label'    => esc_html__( 'Navigation Menu Logo', 'Shinonomeiro_C' ),
				'iro_key'  => 'iro_logo',
				'js_vars'   => [
					[
						'element'  => '.site-title-logo',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'nav_text_logo_text',
				'label'    => esc_html__( 'Nav Menu Text Logo Text', 'Shinonomeiro_C' ),
				'iro_key'  => 'nav_text_logo',
				'iro_subkey'  => 'text',
				'transport'   => 'postMessage',
				'js_vars'   => [
					[
						'element'  => '.site-title',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'nav_text_logo_font',
				'label'    => esc_html__( 'Nav Menu Text Logo Font', 'Shinonomeiro_C' ),
				'iro_key'  => 'nav_text_logo',
				'iro_subkey'  => 'font_name',
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.site-title',
						'property' => 'font-family',
					),
				)
			],
			[
				'type'     => 'checkbox',
				'settings' => 'cover_random_graphs_switch',
				'iro_key'  => 'cover_random_graphs_switch',
				'label'    => esc_html__( 'Switch Button of Random Images', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'nav_user_menu',
				'label'    => esc_html__( 'Nav User Menu', 'Shinonomeiro_C' ),
				'description' => esc_html__( '默认开启。启用后会在导航栏显示用户头像与用户菜单。', 'Shinonomeiro_C' ),
				'section'  => 'iro_nav',
				'iro_key'  => 'nav_user_menu',
			],
			[
				'type'     => 'checkbox',
				'settings' => 'nav_quick_theme_toggle',
				'label'    => esc_html__( 'Show Theme Quick Toggle', 'Shinonomeiro_C' ),
				'description' => esc_html__( '控制导航快捷区中“深色模式切换”按钮的显示。', 'Shinonomeiro_C' ),
				'section'  => 'iro_nav',
				'iro_key'  => 'nav_quick_theme_toggle',
			],
			[
				'type'     => 'checkbox',
				'settings' => 'nav_quick_lang_toggle',
				'label'    => esc_html__( 'Show Language Quick Toggle', 'Shinonomeiro_C' ),
				'description' => esc_html__( '控制导航快捷区中“语言切换”按钮的显示。', 'Shinonomeiro_C' ),
				'section'  => 'iro_nav',
				'iro_key'  => 'nav_quick_lang_toggle',
			],
			[
				'type'     => 'checkbox',
				'settings' => 'nav_menu_search',
				'iro_key'  => 'nav_menu_search',
				'label'    => esc_html__( 'Nav Menu Search', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'search_area_background',
				'iro_key'  => 'search_area_background',
				'label'    => esc_html__( 'Search Area Background Image', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => '.search-form.is-visible',
						'property' => 'background-image',
					),
				),
			],
		],
    ],
	// ====================主题色部分====================
	[
        'id'          => 'iro_color',
        'title'       => esc_html__( '主题配色', 'Shinonomeiro_C' ),
        'description' => esc_html__( '主题配色、深色模式与阅读视觉。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			// ====================主题色====================
			[
				'type'     => 'checkbox',
				'settings' => 'extract_theme_skin_from_cover',
				'iro_key'  => 'extract_theme_skin_from_cover',
				'label'    => esc_html__( 'Extract Theme Color from Cover Image', 'Shinonomeiro_C' ),
				'description' => esc_html__('启用后，主题主色将从首页封面图自动提取。', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'extract_article_highlight_from_feature',
				'iro_key'  => 'extract_article_highlight_from_feature',
				'label'    => esc_html__( 'Extract Article Highlight from Featured Image', 'Shinonomeiro_C' ),
				'description' => esc_html__('启用后，文章页配色将从文章特色图自动提取。', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'color',
				'settings' => 'theme_skin',
				'label'    => esc_html__( 'Theme Color', 'Shinonomeiro_C' ),
				'iro_key'  => 'theme_skin',
				'choices'     => [
					'alpha' => true,
				],
			],
			[
				'type'     => 'color',
				'settings' => 'theme_skin_matching',
				'label'    => esc_html__( 'Matching Color', 'Shinonomeiro_C' ),
				'iro_key'  => 'theme_skin_matching',
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => ':root',
						'property' => '--theme-skin-matching',
					),
				),
			],
			// ====================深色模式====================
			[
				'type'     => 'color',
				'settings' => 'theme_skin_dark',
				'label'    => esc_html__( 'Dark Mode Theme Color', 'Shinonomeiro_C' ),
				'iro_key'  => 'theme_skin_dark',
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => ':root',
						'property' => '--theme-skin-dark',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'theme_darkmode_img_bright',
				'label'    => esc_html__( 'Dark Mode Image Brightness', 'Shinonomeiro_C' ),
				'iro_key'  => 'theme_darkmode_img_bright',
				'choices'     => [
					'min'  => 0.4,
					'max'  => 1,
					'step' => 0.01,
				],
			],
			[
				'type'     => 'slider',
				'settings' => 'theme_darkmode_widget_transparency',
				'label'    => esc_html__( 'Dark Mode Component Transparency', 'Shinonomeiro_C' ),
				'iro_key'  => 'theme_darkmode_widget_transparency',
				'choices'     => [
					'min'  => 0.2,
					'max'  => 1,
					'step' => 0.01,
				],
			],
		],
    ],
	// ====================封面LOGO====================
    [
        'id'          => 'iro_cover_logo',
        'title'       => esc_html__( '头像与身份展示', 'Shinonomeiro_C' ),
        'description' => esc_html__( '个人头像、特效文字与首页身份展示。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_user_profile',

		'fields'      =>[
			[
				'type'     => 'image',
				'settings' => 'personal_avatar',
				'label'    => esc_html__( 'Cover Personal Avatar', 'Shinonomeiro_C' ),
				'iro_key'  => 'personal_avatar',
				'transport'   => 'postMessage',
				'js_vars'   => [
					[
						'element'  => '.header-tou a',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'text_logo_options',
				'label'    => esc_html__( 'Enable Mashiro Special Effects Text', 'Shinonomeiro_C' ),
				'description' => __('启用后，将使用该设置替换首页展示头像。','Shinonomeiro_C'),
				'iro_key'  => 'text_logo_options',
			],
			[
				'type'     => 'text',
				'settings' => 'text_logo_text',
				'label'    => esc_html__( 'Mashiro Special Effects Text', 'Shinonomeiro_C' ),
				'iro_key'  => 'text_logo',
				'iro_subkey'  => 'text',
				'active_callback' => [
					[
						'setting'  => 'text_logo_options',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'postMessage',
				'js_vars'     => [
					[
						'element'  => '.center-text',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'color',
				'settings' => 'text_logo_color',
				'label'    => esc_html__( 'Mashiro Special Effects Text Color', 'Shinonomeiro_C' ),
				'iro_key'  => 'text_logo',
				'iro_subkey'  => 'color',
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => [
					[
						'setting'  => 'text_logo_options',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => '.center-text',
						'property' => 'color',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'text_logo_font',
				'label'    => esc_html__( 'Mashiro Special Effects Font', 'Shinonomeiro_C' ),
				'iro_key'  => 'text_logo',
				'iro_subkey'  => 'font',
				'active_callback' => [
					[
						'setting'  => 'text_logo_options',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => '.center-text',
						'property' => 'font-family',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'text_logo_size',
				'label'    => esc_html__( 'Mashiro Special Effects Size', 'Shinonomeiro_C' ),
				'iro_key'  => 'text_logo',
				'iro_subkey'  => 'size',
				'active_callback' => [
					[
						'setting'  => 'text_logo_options',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'choices'     => [
					'min'  => 40 ,
					'max'  => 140,
					'step' => 1,
				],
				'output' => array(
					array(
						'element'  => '.center-text',
						'property' => 'font-size',
						'value_pattern' => '$px !important',
					),
				),
			],
		],
    ],
	// ====================封面外观====================
	[
        'id'          => 'iro_cover_display',
        'title'       => esc_html__( '首屏外观', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首屏外观、动画与背景效果。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'checkbox',
				'settings' => 'cover_switch',
				'label'    => esc_html__( 'Enable Cover', 'Shinonomeiro_C' ),
				'iro_key'  => 'cover_switch',
			],
			[
				'type'     => 'checkbox',
				'settings' => 'cover_full_screen',
				'label'    => esc_html__( 'Cover Full Screen', 'Shinonomeiro_C' ),
				'iro_key'  => 'cover_full_screen',
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'select',
				'settings' => 'random_graphs_filter',
				'iro_key'  => 'random_graphs_filter',
				'label'    => esc_html__( 'Cover Random Images Filter', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					]
				],
				'choices'     => [
					'filter-nothing' => __('No filter','Shinonomeiro_C'),
					'filter-undertint' => __('Light filter','Shinonomeiro_C'),
					'filter-dim' => __('Dimmed filter','Shinonomeiro_C'),
					'filter-grid' => __('Grid filter','Shinonomeiro_C'),
					'filter-dot' => __('Dot filter','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'cover_half_screen_curve',
				'label'    => esc_html__( 'Cover Arc Occlusion (Below)', 'Shinonomeiro_C' ),
				'iro_key'  => 'cover_half_screen_curve',
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_full_screen',
						'operator' => '==',
						'value'    => false,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'cover_animation',
				'label'    => esc_html__( 'Cover Animation', 'Shinonomeiro_C' ),
				'iro_key'  => 'cover_animation',
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'slider',
				'settings' => 'cover_animation_time',
				'label'    => esc_html__( 'Cover Animation Time', 'Shinonomeiro_C' ),
				'iro_key'  => 'cover_animation_time',
				'choices'     => [
					'min'  => 0,
					'max'  => 5,
					'step' => 0.01,
				],
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_animation',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'hide_splash_wallpaper_switch',
				'iro_key'  => 'hide_splash_wallpaper_switch',
				'label'    => esc_html__( '隐藏开屏壁纸', 'Shinonomeiro_C' ),
				'description' => esc_html__( '启用后，仅隐藏首页开屏壁纸与相关效果，不移除容器结构。', 'Shinonomeiro_C' ),
				'default'  => false,
				'active_callback' => [
					[
						'setting'  => 'cover_switch',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
		],
    ],
	// ====================封面信息栏====================
	[
        'id'          => 'iro_cover_info',
        'title'       => esc_html__( '首屏信息栏', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首屏信息栏与一句话（Yiyan）展示。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'checkbox',
				'settings' => 'infor_bar',
				'iro_key'  => 'infor_bar',
				'label'    => esc_html__( 'Cover Info Bar', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'radio_image',
				'settings' => 'infor_bar_style',
				'iro_key'  => 'infor_bar_style',
				'label'    => esc_html__( 'Cover Info Bar Style', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'choices'     => [
					'v1' => $vision_resource_basepath . 'options/nav_menu_style_Island.webp',
					'v2' => $vision_resource_basepath . 'options/infor_bar_style_v2.webp',
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'slider',
				'settings' => 'homepage_widget_transparency',
				'iro_key'  => 'homepage_widget_transparency',
				'label'    => esc_html__( 'Cover Widget Transparency', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0.2,
					'max'  => 1,
					'step' => 0.01,
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => ':root',
						'property' => '--homepage_widget_transparency',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'avatar_radius',
				'iro_key'  => 'avatar_radius',
				'label'    => esc_html__( 'Cover Info Bar Avatar Radius', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.focusinfo .header-tou img',
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'signature_radius',
				'iro_key'  => 'signature_radius',
				'label'    => esc_html__( 'Cover Info Bar Rounded', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.header-info',
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'signature_text',
				'iro_key'  => 'signature_text',
				'label'    => esc_html__( 'Cover Signature Field Text', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'postMessage',
				'js_vars'     => [
					[
						'element'  => '.header-info p',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'cover_daily_poetry',
				'iro_key'  => 'cover_daily_poetry',
				'label'    => esc_html__( '启用首屏今日诗词', 'Shinonomeiro_C' ),
				'description' => esc_html__( '开启后，首屏签名文本将替换为今日诗词展示。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'slider',
				'settings' => 'cover_daily_poetry_timeout',
				'iro_key'  => 'cover_daily_poetry_timeout',
				'label'    => esc_html__( '诗词接口超时（毫秒）', 'Shinonomeiro_C' ),
				'description' => esc_html__( '建议保持 5000 毫秒，网络较慢时可适当调大。', 'Shinonomeiro_C' ),
				'choices'  => [
					'min'  => 1000,
					'max'  => 10000,
					'step' => 500,
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'color',
				'settings' => 'cover_poetry_light_color',
				'iro_key'  => 'cover_poetry_light_color',
				'label'    => esc_html__( '诗词日间颜色', 'Shinonomeiro_C' ),
				'default'  => '#333333',
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'color',
				'settings' => 'cover_poetry_dark_color',
				'iro_key'  => 'cover_poetry_dark_color',
				'label'    => esc_html__( '诗词夜间颜色', 'Shinonomeiro_C' ),
				'default'  => '#e2e8f0',
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'cover_poetry_author_offset',
				'iro_key'  => 'cover_poetry_author_offset',
				'label'    => esc_html__( '作者行偏移', 'Shinonomeiro_C' ),
				'default'  => '3em',
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'cover_poetry_font_size',
				'iro_key'  => 'cover_poetry_font_size',
				'label'    => esc_html__( '诗词字号', 'Shinonomeiro_C' ),
				'default'  => '1.35em',
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'cover_poetry_author_font_size',
				'iro_key'  => 'cover_poetry_author_font_size',
				'label'    => esc_html__( '作者行字号', 'Shinonomeiro_C' ),
				'default'  => '0.95em',
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'cover_poetry_font_family',
				'iro_key'  => 'cover_poetry_font_family',
				'label'    => esc_html__( '诗词字体族', 'Shinonomeiro_C' ),
				'default'  => "'STKaiti', 'KaiTi', '楷体', serif",
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'cover_daily_poetry',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'signature_font',
				'iro_key'  => 'signature_font',
				'label'    => esc_html__( 'Cover Signature Field Text Font', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.header-info p',
						'property' => 'font-family',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'signature_font_size',
				'iro_key'  => 'signature_font_size',
				'label'    => esc_html__( 'Cover Signature Field Text Font Size', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
				'choices'     => [
					'min'  => 5,
					'max'  => 20,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.header-info p',
						'property' => 'font-size',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'signature_typing',
				'iro_key'  => 'signature_typing',
				'label'    => esc_html__( 'Cover Signature Bar Typing Effects', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'signature_typing_marks',
				'iro_key'  => 'signature_typing_marks',
				'label'    => esc_html__( 'Cover Signature Field Typing Effects Double Quotes', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'signature_typing',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'code',
				'settings' => 'signature_typing_json',
				'iro_key'  => 'signature_typing_json',
				'label'    => esc_html__( 'Typed.js initial option', 'Shinonomeiro_C' ),
				'choices'     => [
					'language' => 'json',
				],
				'active_callback' => [
					[
						'setting'  => 'infor_bar',
						'operator' => '==',
						'value'    => true,
					],
					[
						'setting'  => 'signature_typing',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
		],
    ],
	// ====================杂项====================
	[
        'id'          => 'iro_cover_other',
        'title'       => esc_html__( '首屏其他', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首屏附加功能与可选开关。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'checkbox',
				'settings' => 'site_bg_as_cover',
				'iro_key'  => 'site_bg_as_cover',
				'label'    => esc_html__( 'Cover and Frontend Background Integration', 'Shinonomeiro_C' ),
				'description' => esc_html__( '启用后，封面背景设为透明，前台背景将调用封面随机图 API。', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'post_cover_as_bg',
				'iro_key'  => 'post_cover_as_bg',
				'label'    => esc_html__( 'Post Cover As Background', 'Shinonomeiro_C' ),
				'description' => esc_html__( '启用后，文章页将使用文章特色图作为背景。', 'Shinonomeiro_C' ),
			    'active_callback' => [
					[
						'setting'  => 'site_bg_as_cover',
						'operator' => '==',
						'value'    => true,
					],
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'wave_effects',
				'iro_key'  => 'wave_effects',
				'label'    => esc_html__( 'Cover Wave Effects', 'Shinonomeiro_C' ),
				'description' => __('深色模式下会被强制关闭。','Shinonomeiro_C'),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'drop_down_arrow',
				'iro_key'  => 'drop_down_arrow',
				'label'    => esc_html__( 'Cover Dropdown Arrow', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'drop_down_arrow_mobile',
				'iro_key'  => 'drop_down_arrow_mobile',
				'label'    => esc_html__( 'Cover Dropdown Arrow Display on Mobile Devices', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'drop_down_arrow',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'color',
				'settings' => 'drop_down_arrow_color',
				'iro_key'  => 'drop_down_arrow_color',
				'label'    => esc_html__( 'Cover Dropdown Arrow Color', 'Shinonomeiro_C' ),
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => [
					[
						'setting'  => 'drop_down_arrow',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => '.headertop-down svg path',
						'property' => 'fill',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'color',
				'settings' => 'drop_down_arrow_dark_color',
				'iro_key'  => 'drop_down_arrow_dark_color',
				'label'    => esc_html__( 'Cover Dropdown Arrow Color (Dark Mode)', 'Shinonomeiro_C' ),
				'choices'     => [
					'alpha' => true,
				],
				'active_callback' => [
					[
						'setting'  => 'drop_down_arrow',
						'operator' => '==',
						'value'    => true,
					],
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => 'body.dark .headertop-down svg path ',
						'property' => 'color',
						'value_pattern' => '$ !important',
					),
				),
			],
		],
	],
	// ====================主页整体布局====================
	[
        'id'          => 'iro_homepages_sections',
        'title'       => esc_html__( '首页模块布局', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首页模块排序与整体布局。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'sortable',
				'settings' => 'homepage_components',
				'iro_key'  => 'homepage_components',
				'label'    => esc_html__( 'Homepage Components', 'Shinonomeiro_C' ),
				'choices'     => [
          			'exhibition' => __('Display Area','Shinonomeiro_C'),
					'primary' => __('Article Area','Shinonomeiro_C'),
					'static_page' => __('Static Page','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'dropdown_pages',
				'settings' => 'static_page_id',
				'iro_key'  => 'static_page_id',
				'label'    => esc_html__( 'Select a page', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'homepage_components',
						'operator' => 'contains',
						'value'    => 'static_page',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'exhibition_area_icon',
				'iro_key'  => 'exhibition_area_icon',
				'label'    => esc_html__( 'Display Area Icon', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'homepage_components',
						'operator' => 'contains',
						'value'    => 'exhibition',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'exhibition_area_title',
				'iro_key'  => 'exhibition_area_title',
				'label'    => esc_html__( 'Display Area Title', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'homepage_components',
						'operator' => 'contains',
						'value'    => 'exhibition',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'post_area_icon',
				'iro_key'  => 'post_area_icon',
				'label'    => esc_html__( 'Post Area Icon', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'homepage_components',
						'operator' => 'contains',
						'value'    => 'primary',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'post_area_title',
				'iro_key'  => 'post_area_title',
				'label'    => esc_html__( 'Post Area Title', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'homepage_components',
						'operator' => 'contains',
						'value'    => 'primary',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'area_title_font',
				'iro_key'  => 'area_title_font',
				'label'    => esc_html__( 'Area Title Font', 'Shinonomeiro_C' ),
				'transport'   => 'postMessage',
				'output' => array(
					array(
						'element'  => array('h1.fes-title','h1.main-title'),
						'property' => 'font-family',
						'value_pattern' => '$ !important',
					),
				)
			],
			[
				'type'     => 'radio_image',
				'settings' => 'area_title_text_align',
				'iro_key'  => 'area_title_text_align',
				'label'    => esc_html__( 'Area Title Alignment', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'choices'     => [
					'left' => $vision_resource_basepath . 'options/area_title_text_left.webp',
					'right' => $vision_resource_basepath . 'options/area_title_text_right.webp',
					'center' => $vision_resource_basepath . 'options/area_title_text_center.webp',
				],
				'output' => array(
					array(
						'element'  => array('h1.fes-title','h1.main-title'),
						'property' => 'justify-content',
						'value_pattern' => '$ !important',
					),
				)
			],
		],
    ],
	// ====================展示区====================
	[
        'id'          => 'iro_display_aera',
        'title'       => esc_html__( '展示区', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首页展示区（说说/公告/图标）设置。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',
		'fields'      =>[
			[
				'type'     => 'sortable',
				'settings' => 'capsule_components',
				'iro_key'  => 'capsule_components',
				'label'    => esc_html__( 'Capsule Components', 'Shinonomeiro_C' ),
				'choices'     => [
          			'post_count'     => __('Posts Capsule','Shinonomeiro_C'),
					'comment_count'  => __('Comments Capsule','Shinonomeiro_C'),
					'view_count'  => __('Visitors Capsule','Shinonomeiro_C'),
					'link_count'     => __('Links Capsule','Shinonomeiro_C'),
					'author_count'     => __('Authors Capsule','Shinonomeiro_C'),
					'total_words'     => __('Total Words Capsule','Shinonomeiro_C'),
					'blog_days'     => __('Blog Running Capsule','Shinonomeiro_C'),
					'admin_online'     => __('Last Online Capsule','Shinonomeiro_C'),
					'random_link'     => __('Random Link Capsule','Shinonomeiro_C'),
					'announcement'     => __('Announcement Capsule','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'show_medal_capsules',
				'iro_key'  => 'show_medal_capsules',
				'label'    => esc_html__( 'Show Medal Badges Style Capsule', 'Shinonomeiro_C' ),
				'default'  => true,
				'description' => esc_html__( '启用后会显示博客里程碑铜/银/金徽章；需先解锁对应成就，才会替换对应展示卡片。', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'textarea',
				'settings' => 'stat_announcement_text',
				'iro_key'  => 'stat_announcement_text',
				'label'    => esc_html__( 'Announcement Text', 'Shinonomeiro_C' ),
				'description' => esc_html__( '设置公告胶囊文案。前台会自动拆分为两行，也可手动换行。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'capsule_components',
						'operator' => 'contains',
						'value'    => 'announcement',
					],
				],
			],
		],
    ],
	// ====================文章区====================
	[
        'id'          => 'iro_article_aera',
        'title'       => esc_html__( '文章卡片区', 'Shinonomeiro_C' ),
        'description' => esc_html__( '首页文章区样式与卡片展示。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'select',
				'settings' => 'article_meta_displays',
				'iro_key'  => 'article_meta_displays',
				'label'    => esc_html__( 'Article Area Meta Displays', 'Shinonomeiro_C' ),
				'description' => esc_html__( '控制首页文章卡片显示的元信息。注意：为保证移动端版面清晰，手机上不会显示“标签”。', 'Shinonomeiro_C' ),
				'multiple'    => 0, // 想选多少选多少
				'choices'     => [
					"post_tags" => __("标签","Shinonomeiro_C"),
					"author" => __("Author","Shinonomeiro_C"),
					"category" => __("Category","Shinonomeiro_C"),
					"comment_count" => __("Number of Comments","Shinonomeiro_C"),
					"post_views" => __("Number of Views","Shinonomeiro_C"),
					"post_words_count" => __("Number of Words","Shinonomeiro_C"),
					"reading_time" => __("Estimate Reading Time","Shinonomeiro_C"),
				],
			],
			[
				'type'     => 'radio_image',
				'settings' => 'post_list_design',
				'iro_key'  => 'post_list_design',
				'label'    => esc_html__( 'Article Area Card Design', 'Shinonomeiro_C' ),
				'choices'     => [
					'letter' => $vision_resource_basepath . 'options/post_list_design_letter.webp',
          			'ticket' => $vision_resource_basepath . 'options/post_list_design_ticket.webp',
				],
			],
			[
				'type'     => 'radio_image',
				'settings' => 'post_list_ticket_type',
				'iro_key'  => 'post_list_ticket_type',
				'label'    => esc_html__( 'Article Area Ticket Layout', 'Shinonomeiro_C' ),
				'choices'     => [
					'card' => $vision_resource_basepath . 'options/post_list_design_ticket.webp',
          			'non-card' => $vision_resource_basepath . 'options/post_list_design_ticket_2.webp',
				],
				'active_callback' => [
					[
						'setting'  => 'post_list_design',
						'operator' => '==',
						'value'    => 'ticket',
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_meta_background_compatible',
				'iro_key'  => 'article_meta_background_compatible',
				'label'    => esc_html__( 'Article Area Card Information Meta Background Compatible', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'show_shuoshuo_on_home_page',
				'iro_key'  => 'show_shuoshuo_on_home_page',
				'label'    => esc_html__( 'Show shuoshuo on home page', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'slider',
				'settings' => 'post_meta_radius', //信息
				'iro_key'  => 'post_meta_radius',
				'label'    => esc_html__( 'Article Area Card Information Meta Rounded Corners', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => array('.post-date', '.post-meta'),
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'post_list_title_radius', //标题
				'iro_key'  => 'post_list_title_radius',
				'label'    => esc_html__( 'Article Area Card Title Meta Rounded Corners', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.post-title',
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'post_list_card_radius', //卡片
				'iro_key'  => 'post_list_card_radius',
				'label'    => esc_html__( 'Article Area Card Rounded Corners', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 5,
					'max'  => 20,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => array('.shuoshuo-item','.post-list-thumb'),
						'property' => 'border-radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'post_title_font_size', //字体
				'iro_key'  => 'post_title_font_size',
				'label'    => esc_html__( 'Article Area Title Font Size', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 1,
					'max'  => 30,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.post-list-thumb .post-title h3',
						'property' => 'font-size',
						'value_pattern' => '$px !important',
					),
				),
			],
		],
    ],
	// ====================前台背景、字体====================
	[
        'id'          => 'iro_front',
        'title'       => esc_html__( '前台背景', 'Shinonomeiro_C' ),
        'description' => esc_html__( '前台背景图、透明度与特效。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'image',
				'settings' => 'reception_background_img1',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'img1',
				'label'    => esc_html__( 'Default Frontend Background', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'slider',
				'settings' => 'reception_background_transparency',
				'iro_key'  => 'reception_background_transparency',
				'label'    => esc_html__( 'Background Transparency in the Frontend', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0.2,
					'max'  => 1,
					'step' => 0.01,
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'reception_background_blur',
				'iro_key'  => 'reception_background_blur',
				'label'    => esc_html__( 'Background Transparency Blur', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'select',
				'settings' => 'reception_background_size',
				'iro_key'  => 'reception_background_size',
				'label'    => esc_html__( 'Frontend Background Scaling Method', 'Shinonomeiro_C' ),
				'choices'     => [
					'cover' => __('Cover','Shinonomeiro_C'),
					'contain' => __('Contain','Shinonomeiro_C'),
					'auto' => __('Auto','Shinonomeiro_C'),
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => 'body',
						'background-size' => 'font-size',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'global_default_font',
				'iro_key'  => 'global_default_font',
				'label'    => esc_html__( 'Global Default Font', 'Shinonomeiro_C' ),
				'description' => esc_html__( '填写字体名称后，可在“Shinonomeiro Options -> 全局设置 -> 字体设置”中添加自定义字体。', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.serif',
						'property' => 'font-family',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'global_font_weight',
				'iro_key'  => 'global_font_weight',
				'label'    => esc_html__( 'Non-Emphasis Text Weight', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 100,
					'max'  => 700,
					'step' => 10,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => ':root',
						'property' => '--global-font-weight',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'global_font_size',
				'iro_key'  => 'global_font_size',
				'label'    => esc_html__( 'Global Font Size', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 10,
					'max'  => 20,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => array('.serif','body'),
						'property' => 'font-size',
						'value_pattern' => '$px !important',
					),
				),
			],
		],
    ],
	// ====================小组件====================
	[
        'id'          => 'iro_widgets',
        'title'       => esc_html__( '小组件面板', 'Shinonomeiro_C' ),
        'description' => esc_html__( '小组件外观、字体与昼夜切换。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_home_display',

		'fields'      =>[
			[
				'type'     => 'slider',
				'settings' => 'style_menu_radius',
				'iro_key'  => 'style_menu_radius',
				'label'    => esc_html__( 'Widgets Panel Button Radius', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 50,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => ':root',
						'property' => '--style_menu_radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'slider',
				'settings' => 'style_menu_selection_radius',
				'iro_key'  => 'style_menu_selection_radius',
				'label'    => esc_html__( 'Widgets Panel Widget Radius', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => ':root',
						'property' => '--style_menu_selection_radius',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'style_menu_font',
				'iro_key'  => 'style_menu_font',
				'label'    => esc_html__( 'Widgets Panel Font', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'sakura_widget',
				'iro_key'  => 'sakura_widget',
				'label'    => esc_html__( 'Widgets Panel WP Widget Area', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'iro_widget_daynight',
				'iro_key'  => 'widget_daynight',
				'label'    => esc_html__( 'Widgets Panel Day&Night Switching', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'iro_widget_font',
				'iro_key'  => 'widget_font',
				'label'    => esc_html__( 'Widgets Panel Font Switching', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'text',
				'settings' => 'global_default_font_widgets',
				'iro_key'  => 'global_default_font',
				'label'    => esc_html__( 'Global Default Font&Widgets Panel Font Switching A', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.serif',
						'property' => 'font-family',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'text',
				'settings' => 'global_font_2',
				'iro_key'  => 'global_font_2',
				'label'    => esc_html__( 'Widgets Panel Font Switching B', 'Shinonomeiro_C' ),
			],
			//四个背景按钮
			[
				'type'     => 'checkbox',
				'settings' => 'reception_background_heart_shaped',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'heart_shaped',
				'label'    => esc_html__( '♡Option Switcher', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'reception_background_img2',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'img2',
				'label'    => esc_html__( '♡Corresponding Background', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'reception_background_star_shaped',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'star_shaped',
				'label'    => esc_html__( '☆Option Switcher', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'reception_background_img3',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'img3',
				'label'    => esc_html__( '☆Corresponding Background', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'reception_background_square_shaped',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'square_shaped',
				'label'    => esc_html__( '□Option Switcher', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'reception_background_img4',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'img4',
				'label'    => esc_html__( '□Corresponding Background', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'reception_background_lemon_shaped',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'lemon_shaped',
				'label'    => esc_html__( '🍋Option Switcher', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'reception_background_img5',
				'iro_key'  => 'reception_background',
				'iro_subkey'  => 'img5',
				'label'    => esc_html__( '🍋Corresponding Background', 'Shinonomeiro_C' ),
			],
		],
    ],
	// ====================粒子特效====================
	[
        'id'          => 'iro_particles',
        'title'       => esc_html__( '粒子与动效', 'Shinonomeiro_C' ),
        'description' => esc_html__( '粒子与动态前景效果。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_performance',

		'fields'      =>[
			[
				'type'     => 'select',
				'settings' => 'sakura_falling_effects',
				'iro_key'  => 'sakura_falling_effects',
				'label'    => esc_html__( 'Sakura Falling Effects', 'Shinonomeiro_C' ),
				'choices'     => [
					'off' => __('Off','Shinonomeiro_C'),
					'native' => __('Native Quantity','Shinonomeiro_C'),
					'quarter' => __('Quarter Quantity','Shinonomeiro_C'),
					'half' => __('Half Quantity','Shinonomeiro_C'),
					'less' => __('Less Quantity','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'particles_effects',
				'iro_key'  => 'particles_effects',
				'label'    => esc_html__( 'Particles Effects', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'code',
				'settings' => 'particles_json',
				'iro_key'  => 'particles_json',
				'label'    => esc_html__( 'Particles JSON', 'Shinonomeiro_C' ),
				'description' => esc_html__( '粒子参数可参考 https://vincentgarreau.com/particles.js/ 文档。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'particles_effects',
						'operator' => '==',
						'value'    => true,
					]
				],
				'choices'     => [
					'language' => 'json',
				],
			],
		],
    ],
	// ====================页脚====================
	[
        'id'          => 'iro_footer',
        'title'       => esc_html__( '页脚信息', 'Shinonomeiro_C' ),
        'description' => esc_html__( '页脚布局、文案与附加展示。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_dev_maintenance',

		'fields'      =>[
			[
				'type'     => 'select',
				'settings' => 'footer_direction',
				'iro_key'  => 'footer_direction',
				'label'    => esc_html__( 'Footer Content Distribution', 'Shinonomeiro_C' ),
				'choices'     => [
					'center' => __('Center','Shinonomeiro_C'),
					'columns' => __('Two Coloumns','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'footer_sakura',
				'iro_key'  => 'footer_sakura',
				'label'    => esc_html__( 'Footer Sakura Icon', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'code',
				'settings' => 'footer_info',
				'iro_key'  => 'footer_info',
				'label'    => esc_html__( 'Footer Info', 'Shinonomeiro_C' ),
				'choices'     => [
					'language' => 'html',
				],
				'transport'   => 'postMessage',
				'js_vars' => [
					[
						'element'  => '.footer_info',
						'function' => 'html',
					],
				],
			],
			[
				'type'     => 'text',
				'settings' => 'footer_text_font',
				'iro_key'  => 'footer_text_font',
				'label'    => esc_html__( 'Footer Text Font', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => array('.site-info','.site-info a'),
						'property' => 'font-family',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'footer_load_occupancy',
				'iro_key'  => 'footer_load_occupancy',
				'label'    => esc_html__( 'Footer Load Occupancy Query', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'footer_upyun',
				'iro_key'  => 'footer_upyun',
				'label'    => esc_html__( 'Footer Upyun League Logo', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'footer_yiyan',
				'iro_key'  => 'footer_yiyan',
				'label'    => esc_html__( 'Footer Hitokoto', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'code',
				'settings' => 'yiyan_api',
				'iro_key'  => 'yiyan_api',
				'label'    => esc_html__( 'Hitokoto API address', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'footer_yiyan',
						'operator' => '==',
						'value'    => true,
					]
				],
				'choices'     => [
					'language' => 'json',
				],
			],

			[
				'type'     => 'checkbox',
				'settings' => 'hide_theme_info_switch',
				'iro_key'  => 'hide_theme_info_switch',
				'label'    => esc_html__( '隐藏主题信息', 'Shinonomeiro_C' ),
				'description' => esc_html__( '隐藏页脚中的主题署名信息块（Theme Shinonomeiro By LHabc）。', 'Shinonomeiro_C' ),
				'default'  => false,
			],
		],
    ],
	// ====================全局杂项====================
	[
        'id'          => 'iro_global_others',
        'title'       => esc_html__( '加载与翻页', 'Shinonomeiro_C' ),
        'description' => esc_html__( '全局加载行为、滚动与翻页策略。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_performance',

		'fields'      =>[
			[
				'type'     => 'checkbox',
				'settings' => 'nprogress_on',
				'iro_key'  => 'nprogress_on',
				'label'    => esc_html__( 'NProgress Loading Progress Bar', 'Shinonomeiro_C' ),
				'description' => esc_html__('默认开启。页面加载时会显示顶部进度条。','Shinonomeiro_C'),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'smoothscroll_option',
				'iro_key'  => 'smoothscroll_option',
				'label'    => esc_html__( 'Global Smooth Scroll', 'Shinonomeiro_C' ),
				'description' => esc_html__('默认开启。启用后页面滚动更平滑。','Shinonomeiro_C'),
			],
			[
				'type'     => 'select',
				'settings' => 'pagenav_style',
				'iro_key'  => 'pagenav_style',
				'label'    => esc_html__( 'Pagination Mode', 'Shinonomeiro_C' ),
				'choices'     => [
					'ajax' => __('Ajax Load','Shinonomeiro_C'),
					'np' => __('Page Up/Down','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'select',
				'settings' => 'page_auto_load',
				'iro_key'  => 'page_auto_load',
				'label'    => esc_html__( 'Next Page Auto Load', 'Shinonomeiro_C' ),
				'choices'     => [
					'0' => __('0 Sec','Shinonomeiro_C'),
					'1' => __('1 Sec','Shinonomeiro_C'),
					'2' => __('2 Sec','Shinonomeiro_C'),
					'3' => __('3 Sec','Shinonomeiro_C'),
					'4' => __('4 Sec','Shinonomeiro_C'),
					'5' => __('5 Sec','Shinonomeiro_C'),
					'6' => __('6 Sec','Shinonomeiro_C'),
					'7' => __('7 Sec','Shinonomeiro_C'),
					'8' => __('8 Sec','Shinonomeiro_C'),
					'9' => __('9 Sec','Shinonomeiro_C'),
					'10' => __('10 Sec','Shinonomeiro_C'),
					'233' => __('Do not autoload','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'image',
				'settings' => 'load_nextpage_svg',
				'iro_key'  => 'load_nextpage_svg',
				'label'    => esc_html__( 'Placeholder SVG when loading the next page', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => ':root',
						'property' => '--load_nextpage_svg',
					),
				),
			],
		],
    ],
	// ====================页面通用设置====================
	[
        'id'          => 'iro_pages_common',
        'title'       => esc_html__( '页面通用', 'Shinonomeiro_C' ),
        'description' => esc_html__( '页面通用行为与阅读体验。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_article_reading',

		'fields'      =>[
			[
				'type'     => 'radio',
				'settings' => 'entry_content_style',
				'iro_key'  => 'entry_content_style',
				'label'    => esc_html__( 'Page Layout Style', 'Shinonomeiro_C' ),
				'choices'     => [
					'sakurairo' => __('Default Style','Shinonomeiro_C'),
          			'github' => __('Github Style','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'patternimg',
				'iro_key'  => 'patternimg',
				'label'    => esc_html__( 'Page Decoration Image', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'page_title_animation',
				'iro_key'  => 'page_title_animation',
				'label'    => esc_html__( 'Page Title Animation', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'slider',
				'settings' => 'page_title_animation_time',
				'iro_key'  => 'page_title_animation_time',
				'label'    => esc_html__( 'Page Title Animation Time', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 0,
					'max'  => 5,
					'step' => 0.01,
				],
				'active_callback' => [
					[
						'setting'  => 'page_title_animation',
						'operator' => '==',
						'value'    => true,
					]
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.entry-title,.single-center .entry-census,.entry-census,.p-time',
						'property' => 'animation',
						'value_pattern' => 'homepage-load-animation $s !important',
					),
					array(
						'element'  => '.single-center .single-header h1.entry-title::after',
						'property' => 'animation',
						'value_pattern' => 'lineWidth 2s $s forwards !important',
					),
				),
			],
			[
				'type'     => 'image',
				'settings' => 'load_in_svg',
				'iro_key'  => 'load_in_svg',
				'label'    => esc_html__( 'Page Image Placeholder SVG', 'Shinonomeiro_C' ),
			],
		],
    ],
	// ====================文章页设置====================
	[
        'id'          => 'iro_pages_post',
        'title'       => esc_html__( '文章页', 'Shinonomeiro_C' ),
        'description' => esc_html__( '文章页信息展示与元数据。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_article_reading',

		'fields'      =>[
			[
				'type'     => 'slider',
				'settings' => 'article_title_font_size',
				'iro_key'  => 'article_title_font_size',
				'description' => esc_html__( '该选项仅对设置了封面的文章生效。', 'Shinonomeiro_C' ),
				'label'    => esc_html__( 'Article Page Title Font Size', 'Shinonomeiro_C' ),
				'choices'     => [
					'min'  => 16,
					'max'  => 48,
					'step' => 1,
				],
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.single-center .single-header h1.entry-title',
						'property' => 'font-size',
						'value_pattern' => '$px !important',
					),
				),
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_title_line',
				'iro_key'  => 'article_title_line',
				'label'    => esc_html__( 'Article Page Title Underline Animation', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'select',
				'settings' => 'article_meta_show_in_head',
				'iro_key'  => 'article_meta_show_in_head',
				'label'    => esc_html__( 'Article Page Meta Displays', 'Shinonomeiro_C' ),
				'description' => esc_html__( '控制文章页头部显示的元信息。注意：为保证移动端阅读体验，手机上不会显示“标签”。', 'Shinonomeiro_C' ),
				'multiple'    => 0,
				'choices'     => [
					"post_tags" => __("标签","Shinonomeiro_C"),
					"author" => __("Author","Shinonomeiro_C"),
					"category" => __("Category","Shinonomeiro_C"),
					"comment_count" => __("Number of Comments","Shinonomeiro_C"),
					"post_views" => __("Number of Views","Shinonomeiro_C"),
					"post_words_count" => __("Number of Words","Shinonomeiro_C"),
					"reading_time" => __("Estimate Reading Time","Shinonomeiro_C"),
					"publish_time_relative" => __("Publish Time (Relatively)","Shinonomeiro_C"),
  					"last_edit_time_relative" => __("Last Edit Time (Relatively)","Shinonomeiro_C"),
  					"EDIT" => __("Action Edit (only displays while user has sufficient permissions)","Shinonomeiro_C"),
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_auto_toc',
				'iro_key'  => 'article_auto_toc',
				'label'    => esc_html__( 'Article Page Auto Show Menu', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'color',
				'settings' => 'inline_code_background_color',
				'iro_key'  => 'inline_code_background_color',
				'label'    => esc_html__( 'Inline Code Background Color', 'Shinonomeiro_C' ),
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => ':root',
						'property' => '--inline_code_background_color',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'color',
				'settings' => 'inline_code_background_color_in_dark_mode',
				'iro_key'  => 'inline_code_background_color_in_dark_mode',
				'label'    => esc_html__( 'Inline Code Background Color In Dark Mode', 'Shinonomeiro_C' ),
				'choices'     => [
					'alpha' => true,
				],
				'transport'   => 'auto',
				'output'      => array(
					array(
						'element'  => ':root',
						'property' => '--inline_code_background_color_in_dark_mode',
						'value_pattern' => '$ !important',
					),
				),
			],
		],
    ],
	// ====================文章扩展====================
	[
        'id'          => 'iro_pages_extra',
        'title'       => esc_html__( '文章扩展', 'Shinonomeiro_C' ),
        'description' => esc_html__( '页面扩展功能（打赏、作者信息等）。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_article_reading',

		'fields'      =>[
			[
				'type'     => 'checkbox',
				'settings' => 'article_function',
				'iro_key'  => 'article_function',
				'label'    => esc_html__( 'Article Page Function Bar', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'select',
				'settings' => 'article_lincenses',
				'iro_key'  => 'article_lincenses',
				'label'    => esc_html__( 'Article License', 'Shinonomeiro_C' ),
				'choices'     => [
					false => __("Not Display","Shinonomeiro_C"),
					"cc0" => "CC0 1.0",
					"cc-by" => "CC BY 4.0",
					"cc-by-nc" => "CC BY-NC 4.0",
					"cc-by-nc-nd" => "CC BY-NC-ND 4.0",
					true => "CC BY-NC-SA 4.0",
					"cc-by-nd" => "CC BY-ND 4.0",
					"cc-by-sa" => "CC BY-SA 4.0",
				],
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'reward_area_link',
				'iro_key'  => 'reward_area',
				'iro_subkey' => 'link',
				'label'    => esc_html__( 'Reward Button Link', 'Shinonomeiro_C' ),
				'description' => esc_html__( '点击“赞赏”按钮后将跳转到此链接。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'image',
				'settings' => 'reward_area_image1',
				'iro_key'  => 'reward_area',
				'iro_subkey' => 'image1',
				'label'    => esc_html__( 'Reward Image A', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'reward_area_link1',
				'iro_key'  => 'reward_area',
				'iro_subkey' => 'link1',
				'label'    => esc_html__( 'Reward Image Link A', 'Shinonomeiro_C' ),
				'description' => esc_html__( '点击图片后将跳转到此链接。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'image',
				'settings' => 'reward_area_image2',
				'iro_key'  => 'reward_area',
				'iro_subkey' => 'image2',
				'label'    => esc_html__( 'Reward Image B', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'text',
				'settings' => 'reward_area_link2',
				'iro_key'  => 'reward_area',
				'iro_subkey' => 'link2',
				'label'    => esc_html__( 'Reward Image Link B', 'Shinonomeiro_C' ),
				'description' => esc_html__( '点击图片后将跳转到此链接。', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'author_profile_avatar',
				'iro_key'  => 'author_profile_avatar',
				'label'    => esc_html__( 'Article Page Author Avatar', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'author_profile_name',
				'iro_key'  => 'author_profile_name',
				'label'    => esc_html__( 'Article Page Author Name', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'author_profile_quote',
				'iro_key'  => 'author_profile_quote',
				'label'    => esc_html__( 'Article Page Author Signature', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_modified_time',
				'iro_key'  => 'article_modified_time',
				'label'    => esc_html__( 'Article Last Update Time', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_tag',
				'iro_key'  => 'article_tag',
				'label'    => esc_html__( 'Article Tag', 'Shinonomeiro_C' ),
				'active_callback' => [
					[
						'setting'  => 'article_function',
						'operator' => '==',
						'value'    => true,
					]
				],
			],
			[
				'type'     => 'checkbox',
				'settings' => 'article_nextpre',
				'iro_key'  => 'article_nextpre',
				'label'    => esc_html__( 'Article Page Prev/Next Article Switcher', 'Shinonomeiro_C' ),
			],
		],
	],
	// ====================评论区====================
	[
        'id'          => 'iro_pages_comment',
        'title'       => esc_html__( '评论区', 'Shinonomeiro_C' ),
        'description' => esc_html__( '评论区样式、占位文案与互动设置。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_comment_interaction',

		'fields'      =>[
			[
				'type'     => 'radio',
				'settings' => 'comment_area',
				'iro_key'  => 'comment_area',
				'label'    => esc_html__( 'Page Comment Area Display', 'Shinonomeiro_C' ),
				'choices'     => [
					'unfold' => __('Expand','Shinonomeiro_C'),
          			'fold' => __('Fold','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'text',
				'settings' => 'comment_placeholder_text',
				'iro_key'  => 'comment_placeholder_text',
				'label'    => esc_html__( 'Custom CommentBox Placeholder', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'text',
				'settings' => 'comment_submit_button_text',
				'iro_key'  => 'comment_submit_button_text',
				'label'    => esc_html__( 'Custom Submit Button Content', 'Shinonomeiro_C' ),
			],
			[
				'type'     => 'image',
				'settings' => 'comment_area_image',
				'iro_key'  => 'comment_area_image',
				'label'    => esc_html__( 'Page Comment Area Bottom Right Background Image', 'Shinonomeiro_C' ),
				'transport'   => 'auto',
				'output' => array(
					array(
						'element'  => '.comment-respond textarea',
						'property' => 'background-image',
						'value_pattern' => '$ !important',
					),
				),
			],
			[
				'type'     => 'select',
				'settings' => 'smilies_list',
				'iro_key'  => 'smilies_list',
				'label'    => esc_html__( 'Comment Area Emoticon', 'Shinonomeiro_C' ),
				'description' => esc_html__( '请前往后台配置自定义表情包。', 'Shinonomeiro_C' ),
				'multiple'    => 0,
				'choices'     => [
					'bilibili'   => __('BiliBili Emoticon Pack','Shinonomeiro_C'),
					'tieba'   => __('Baidu Tieba Emoticon Pack','Shinonomeiro_C'),
					'yanwenzi' => __('Emoji','Shinonomeiro_C'),
					'custom' => __('Customized Emoticon Pack','Shinonomeiro_C'),
				],
			],
			[
				'type'     => 'custom',
				'settings' => 'comment_area_notice',
				'default'  => __('For more detailed configuration of the comment area, please go to the backend configuration','Shinonomeiro_C'),
			],
		],
	],
	[
        'id'          => 'iro_pages_comment_media',
        'title'       => esc_html__( '评论媒体上传', 'Shinonomeiro_C' ),
        'description' => esc_html__( '图床、邮件通知与评论媒体相关能力。', 'Shinonomeiro_C' ),
        'panel'       => 'iro_comment_interaction',
		'fields'      => [],
	],
];

$legacy_migrated_keys_file = __DIR__ . '/customizer-migrated-fields.php';
$enable_legacy_migrated_section = (bool) apply_filters(
	'shinonomeiro_enable_migrated_legacy_section',
	defined( 'SHINONOMEIRO_ENABLE_LEGACY_BRIDGE' ) ? SHINONOMEIRO_ENABLE_LEGACY_BRIDGE : true
);
if ( $enable_legacy_migrated_section && file_exists( $legacy_migrated_keys_file ) ) {
	$legacy_migrated_keys = require $legacy_migrated_keys_file;
	if ( is_array( $legacy_migrated_keys ) && ! empty( $legacy_migrated_keys ) ) {
		$legacy_group_rules = [
            'iro_legacy_group_user_profile' => [
                'title' => esc_html__( '社交与资料', 'Shinonomeiro_C' ),
                'description' => esc_html__( '统一管理个人资料、社交账号与对外展示信息。', 'Shinonomeiro_C' ),
                'panel' => 'iro_user_profile',
                'prefixes' => [ 'social_', 'wechat_', 'qq_', 'email_', 'wangyiyun', 'sina', 'github', 'telegram', 'youtube', 'instagram', 'douyin', 'xiaohongshu', 'discord', 'zhihu', 'linkedin', 'twitter', 'facebook', 'diysocialicons', 'unlisted_' ],
            ],
            'iro_legacy_group_custom_code' => [
                'title' => esc_html__( '代码注入', 'Shinonomeiro_C' ),
                'description' => esc_html__( '集中放置 CSS、页头与页脚注入代码，方便统一维护。', 'Shinonomeiro_C' ),
                'panel' => 'iro_custom_code',
                'prefixes' => [ 'site_custom_style', 'site_header_insert', 'footer_addition' ],
            ],
            'iro_legacy_group_performance' => [
                'title' => esc_html__( '搜索与加载策略', 'Shinonomeiro_C' ),
                'description' => esc_html__( '这里集中管理搜索范围、预加载、PJAX 与懒加载；主要影响页面打开速度和浏览连贯性。', 'Shinonomeiro_C' ),
                'panel' => 'iro_performance',
                'merge_to_section' => 'iro_global_others',
                'prefixes' => [ 'search_', 'only_admin_can_search_', 'sticky_', 'custom_exclude_', 'live_search', 'preload_', 'poi_', 'pjax_', 'missing_', 'clipboard_', 'page_lazyload' ],
            ],
            'iro_legacy_group_home_display' => [
                'title' => esc_html__( '首页扩展展示', 'Shinonomeiro_C' ),
                'description' => esc_html__( '用于控制首页封面细节、随机图来源和扩展展示；修改后会直接改变首屏观感。', 'Shinonomeiro_C' ),
                'panel' => 'iro_home_display',
                'merge_to_section' => 'iro_cover_other',
                'prefixes' => [ 'cover_', 'random_graphs_', 'cache_cover', 'exhibition', 'post_cover_' ],
            ],
            'iro_legacy_group_third_party_media' => [
                'title' => esc_html__( '媒体与内容服务', 'Shinonomeiro_C' ),
                'description' => esc_html__( '在这里接入音乐、番剧、Steam、Bilibili 等外部内容；启用后首页或侧栏会出现对应展示模块。', 'Shinonomeiro_C' ),
                'panel' => 'iro_third_party',
                'prefixes' => [ 'aplayer_', 'custom_music_', 'bangumi_', 'my_anime_', 'bilibili_', 'bili', 'steam_', 'steam', 'friend_link_', 'chatgpt_' ],
            ],
            'iro_legacy_group_third_party_analytics' => [
                'title' => esc_html__( '统计与追踪服务', 'Shinonomeiro_C' ),
                'description' => esc_html__( '放置统计代码与分析服务配置（如访问统计、行为追踪）；开启后可在第三方平台查看站点数据。', 'Shinonomeiro_C' ),
                'panel' => 'iro_third_party',
                'prefixes' => [ 'statistics_', 'google_analytics_' ],
            ],
            'iro_legacy_group_comment_media' => [
                'title' => esc_html__( '评论与媒体', 'Shinonomeiro_C' ),
                'description' => esc_html__( '管理评论区增强功能、表情包和图片上传通道；会影响评论可用能力与互动体验。', 'Shinonomeiro_C' ),
                'panel' => 'iro_comment_interaction',
                'merge_to_section' => 'iro_pages_comment_media',
                'prefixes' => [ 'smilies_', 'comment_', 'qq_avatar_', 'img_', 'imgur_', 'smms_', 'chever', 'lsky_', 'mail_' ],
            ],
            'iro_legacy_group_auth_login' => [
                'title' => esc_html__( '登录与后台', 'Shinonomeiro_C' ),
                'description' => esc_html__( '控制登录页样式、后台提示和管理入口行为；常用于统一品牌外观或收紧后台入口。', 'Shinonomeiro_C' ),
                'panel' => 'iro_account_security',
                'prefixes' => [ 'custom_login_', 'login_', 'admin_', 'admin_notify' ],
            ],
            'iro_legacy_group_auth_captcha' => [
                'title' => esc_html__( '验证码与防护', 'Shinonomeiro_C' ),
                'description' => esc_html__( '配置验证码与人机校验策略，主要用于拦截机器登录和恶意提交。', 'Shinonomeiro_C' ),
                'panel' => 'iro_account_security',
                'prefixes' => [ 'captcha_', 'vaptcha_', 'turnstile_' ],
            ],
            'iro_legacy_group_dev_maintenance' => [
                'title' => esc_html__( '站点基础与维护', 'Shinonomeiro_C' ),
                'description' => esc_html__( '管理更新通道、运行库来源、CDN 与维护开关；改动会影响资源加载稳定性和后续升级方式。', 'Shinonomeiro_C' ),
                'panel' => 'iro_dev_maintenance',
                'prefixes' => [ 'favicon_', 'iro_seo', 'iro_meta_', 'theme_', 'load_out_svg', 'time_zone_', 'reference_', 'exter_', 'gfonts_', 'iro_captcha_', 'site_', 'gravatar_', 'custom_proxy_', 'ghcard_', 'lightbox', 'lightgallery_', 'code_highlight_', 'enable_theme_', 'image_', 'classify_', 'cookie_', 'hide_login_', 'fontawesome_', 'dev_', 'php_notice_', 'iro_update_', 'channel_validate_', 'core_library_', 'shared_library_', 'lib_cdn_', 'external_vendor_', 'vision_resource_', 'send_theme_' ],
            ],
        ];

		$legacy_group_fields = [];
		foreach ( array_keys( $legacy_group_rules ) as $group_id ) {
			$legacy_group_fields[ $group_id ] = [];
		}

		$legacy_code_key_markers = [
			'custom_style',
			'header_insert',
			'footer_addition',
			'google_analytics',
			'script',
			'css',
			'js',
			'html',
			'template',
		];
		$legacy_textarea_key_markers = [
			'statistics_format',
			'statistics_api',
			'channel_validate_value',
			'meta_description',
			'prompt',
			'description',
			'content',
			'message',
			'notice',
		];

		$legacy_force_checkbox_keys = [
			'theme_darkmode_auto', 'theme_commemorate_mode', 'cover_video_loop', 'cover_video_live',
			'reference_exter_font', 'search_filter', 'random_graphs_mts', 'cache_cover', 'cover_video',
			'social_area', 'bangumi_cache', 'friend_link_form', 'steam_cache', 'comment_useragent',
			'comment_location', 'admin_notify', 'login_language_opt', 'chatgpt_auto_article_summarize',
			'ghcard_proxy', 'core_library_basepath', 'shared_library_basepath', 'external_vendor_lib',
			'send_theme_version',
			'wechat_qrcode_switch', 'wechat_copy_switch', 'qq_qrcode_switch', 'qq_copy_switch',
			'search_for_shuoshuo', 'search_for_pages', 'only_admin_can_search_pages', 'sticky_pinned_content',
			'live_search', 'live_search_comment', 'preload_animation', 'poi_pjax', 'pjax_keep_loading',
			'clipboard_ref', 'page_lazyload', 'show_location_in_manage', 'save_location', 'comment_private_message',
			'mail_notify', 'custom_login_switch', 'login_urlskip', 'time_zone_fix', 'lightbox',
			'code_highlight_prism_line_number_all', 'enable_theme_mathjax', 'hide_login_portal',
			'dev_mode',
		];
		$legacy_force_select_choices = [
			'captcha_select' => [
				'off' => esc_html__( '关闭', 'Shinonomeiro_C' ),
				'iro_captcha' => esc_html__( '主题验证码', 'Shinonomeiro_C' ),
				'vaptcha' => esc_html__( 'Vaptcha', 'Shinonomeiro_C' ),
				'turnstile' => esc_html__( 'Cloudflare Turnstile', 'Shinonomeiro_C' ),
			],
			'comment_captcha_select' => [
				'off' => esc_html__( '关闭', 'Shinonomeiro_C' ),
				'iro_captcha' => esc_html__( '主题验证码', 'Shinonomeiro_C' ),
				'turnstile' => esc_html__( 'Cloudflare Turnstile', 'Shinonomeiro_C' ),
			],
			'code_highlight_method' => [
				'prismjs' => esc_html__( 'Prism', 'Shinonomeiro_C' ),
				'' => esc_html__( '关闭', 'Shinonomeiro_C' ),
			],
			'php_notice_filter' => [
				'normal' => esc_html__( '仅严重错误（推荐）', 'Shinonomeiro_C' ),
				'all' => esc_html__( '屏蔽大部分错误', 'Shinonomeiro_C' ),
				'inner' => esc_html__( '全部关闭', 'Shinonomeiro_C' ),
			],
			'theme_darkmode_strategy' => [
				'time' => esc_html__( '按时间自动切换', 'Shinonomeiro_C' ),
				'client' => esc_html__( '跟随系统外观', 'Shinonomeiro_C' ),
				'eien' => esc_html__( '始终深色模式', 'Shinonomeiro_C' ),
			],
			'iro_update_channel' => [
				'stable' => esc_html__( '稳定通道', 'Shinonomeiro_C' ),
				'beta' => esc_html__( 'Beta 测试通道', 'Shinonomeiro_C' ),
				'preview' => esc_html__( '预览通道', 'Shinonomeiro_C' ),
			],
			'post_cover_options' => [
				'type_1' => esc_html__( '默认封面策略', 'Shinonomeiro_C' ),
				'type_2' => esc_html__( '优先特色图', 'Shinonomeiro_C' ),
			],
			'my_anime_list_sort' => [
				'favorites' => esc_html__( '按评分排序', 'Shinonomeiro_C' ),
				'title' => esc_html__( '按标题排序', 'Shinonomeiro_C' ),
				'start_date' => esc_html__( '按首播时间排序', 'Shinonomeiro_C' ),
			],
			'statistics_api' => [
				'netdata' => esc_html__( 'NetData API', 'Shinonomeiro_C' ),
				'busuanzi' => esc_html__( '不蒜子统计', 'Shinonomeiro_C' ),
			],
			'social_display_icon' => [
				'triangle' => esc_html__( '三角图标样式', 'Shinonomeiro_C' ),
				'square' => esc_html__( '方形图标样式', 'Shinonomeiro_C' ),
			],
			'bangumi_source' => [
				'mal' => esc_html__( 'MyAnimeList', 'Shinonomeiro_C' ),
				'bilibili' => esc_html__( 'Bilibili', 'Shinonomeiro_C' ),
				'bgm' => esc_html__( 'Bangumi', 'Shinonomeiro_C' ),
			],
			'friend_link_align' => [
				'left_and_right' => esc_html__( '两端对齐', 'Shinonomeiro_C' ),
				'left' => esc_html__( '左对齐', 'Shinonomeiro_C' ),
			],
			'admin_left_style' => [
				'business_card' => esc_html__( '名片风格', 'Shinonomeiro_C' ),
				'frosted' => esc_html__( '磨砂风格', 'Shinonomeiro_C' ),
			],
			'iro_update_source' => [
				'github' => esc_html__( 'GitHub 源', 'Shinonomeiro_C' ),
				'upyun' => esc_html__( '又拍云源', 'Shinonomeiro_C' ),
				'official_building' => esc_html__( '官方源', 'Shinonomeiro_C' ),
			],
			'lib_cdn_path' => [
				'jsd' => esc_html__( 'jsDelivr', 'Shinonomeiro_C' ),
				'custom' => esc_html__( '自定义地址', 'Shinonomeiro_C' ),
			],
		];
		$legacy_force_slider_choices = [
			'iro_captcha_level' => [
				'min' => 0,
				'max' => 100,
				'step' => 1,
			],
			'theme_darkmode_background_transparency' => ['min'=>0.0,'max'=>1.0,'step'=>0.01],
			'aplayer_volume' => ['min'=>0,'max'=>1,'step'=>0.01],
			'social_area_radius' => ['min'=>0,'max'=>50,'step'=>1],
			'page_temp_title_font_size' => ['min'=>20,'max'=>120,'step'=>1],
			'img_upload_max_size' => ['min'=>0,'max'=>10,'step'=>0.1],
			'chatgpt_max_tokens' => ['min'=>1,'max'=>4000,'step'=>1],
			'chatgpt_api_request_timeout' => ['min'=>1,'max'=>120,'step'=>1],
			'time_zone_fix' => ['min'=>-24,'max'=>24,'step'=>1],
		];

		foreach ( $legacy_migrated_keys as $legacy_key ) {
			$current_value = $GLOBALS['iro_options'][ $legacy_key ] ?? '';
			$field_type = 'text';
			$legacy_key_lower = strtolower( (string) $legacy_key );
			$checkbox_by_pattern = (bool) preg_match( '/(_switch$|_loop$|_live$|_auto$|_notify$|_lazyload$|_keep_loading$|_private_message$|^hide_|^enable_)/', $legacy_key_lower );
			if ( isset( $legacy_force_slider_choices[ $legacy_key ] ) ) {
				$field_type = 'slider';
			} elseif ( isset( $legacy_force_select_choices[ $legacy_key ] ) ) {
				$field_type = 'select';
			} elseif ( is_bool( $current_value ) || in_array( $legacy_key, $legacy_force_checkbox_keys, true ) || $checkbox_by_pattern ) {
				$field_type = 'checkbox';
			} elseif ( is_int( $current_value ) || is_float( $current_value ) ) {
				$field_type = 'number';
			} elseif ( is_array( $current_value ) ) {
				$field_type = 'textarea';
			} else {
				$normalized_value = is_string( $current_value ) ? strtolower( trim( $current_value ) ) : '';
				if ( in_array( $normalized_value, [ '0', '1', 'true', 'false', 'on', 'off' ], true ) ) {
					$field_type = 'checkbox';
				}
				if ( 'text' === $field_type ) {
					foreach ( $legacy_code_key_markers as $marker ) {
						if ( false !== strpos( $legacy_key_lower, $marker ) ) {
							$field_type = 'code';
							break;
						}
					}
				}
				if ( 'text' === $field_type ) {
					foreach ( $legacy_textarea_key_markers as $marker ) {
						if ( false !== strpos( $legacy_key_lower, $marker ) ) {
							$field_type = 'textarea';
							break;
						}
					}
				}
			}

			$default_value = is_array( $current_value )
				? wp_json_encode( $current_value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
				: $current_value;

			$field = [
				'type'        => $field_type,
				'settings'    => 'legacy_' . $legacy_key,
				'iro_key'     => $legacy_key,
				'label'       => iro_customizer_friendly_label_from_key( $legacy_key ),
				'default'     => $default_value,
			];
			if ( 'select' === $field_type && isset( $legacy_force_select_choices[ $legacy_key ] ) ) {
				$field['choices'] = $legacy_force_select_choices[ $legacy_key ];
			}
			if ( 'slider' === $field_type && isset( $legacy_force_slider_choices[ $legacy_key ] ) ) {
				$field['choices'] = $legacy_force_slider_choices[ $legacy_key ];
			}
			$legacy_description = iro_customizer_legacy_description_from_key( $legacy_key );
			if ( '' !== $legacy_description ) {
				$field['description'] = $legacy_description;
			}

			if ( in_array( $field_type, [ 'textarea', 'code' ], true ) ) {
				$field['sanitize_callback'] = 'iro_customizer_sanitize_json_or_text';
			}

			$target_group = 'iro_legacy_group_dev_maintenance';
			foreach ( $legacy_group_rules as $group_id => $group_rule ) {
				foreach ( $group_rule['prefixes'] as $prefix ) {
					if ( 0 === strpos( $legacy_key, $prefix ) || $legacy_key === $prefix ) {
						$target_group = $group_id;
						break 2;
					}
				}
			}

			$legacy_group_fields[ $target_group ][] = $field;
		}

		foreach ( $legacy_group_rules as $group_id => $group_rule ) {
			if ( empty( $legacy_group_fields[ $group_id ] ) ) {
				continue;
			}
			if ( ! empty( $group_rule['merge_to_section'] ) ) {
				$merged = false;
				foreach ( $sections as &$section_item ) {
					if ( $section_item['id'] === $group_rule['merge_to_section'] ) {
						$section_item['fields'] = array_merge( $section_item['fields'], $legacy_group_fields[ $group_id ] );
						$merged = true;
						break;
					}
				}
				unset( $section_item );
				if ( $merged ) {
					continue;
				}
			}
			$sections[] = [
				'id'          => $group_id,
				'title'       => $group_rule['title'],
				'description' => $group_rule['description'],
				'panel'       => isset( $group_rule['panel'] ) ? $group_rule['panel'] : 'iro_global',
				'fields'      => $legacy_group_fields[ $group_id ],
			];
		}
	}
}

// ====================Panel注册====================
$panelAutoPriority = 10;
foreach ( $panels as &$panel ) {
    if ( empty( $panel['priority'] ) ) {
        $panel['priority'] = $panelAutoPriority;
        $panelAutoPriority += 10;
    }
}
unset( $panel );

$sectionAutoPriority = 10;
foreach ( $sections as &$section ) {
    if ( empty( $section['priority'] ) ) {
        $section['priority'] = $sectionAutoPriority;
        $sectionAutoPriority += 10;
    }
}
unset( $section );

foreach ( $panels as $panel ) {
    // 必须字段：id、title
    // 可选字段：description、priority
    switch ( 'panel' ) {
        case 'panel':
			new \Kirki\Panel(
                $panel['id'],
                [
                    'title'       => $panel['title'],
                    'description' => isset( $panel['description'] ) ? $panel['description'] : '',
                    'priority'    => $panel['priority'],
                ]
            );
            break;
    }
}

// ====================分组和设置项注册====================
// 定义各字段类型的默认值映射（必填项 default 若未设置时使用）  
$type_defaults = [
	'background'       => [],
	'checkbox'         => false,
	'code'             => '',
	'color'            => '#000000',
	'color_palette'    => '#000000',
	'dashicons'        => '',
	'date'             => '',
	'dimension'        => '',
	'dimensions'       => [],
	'dropdown_pages'   => '',
	'editor'           => '',
	'generic'          => '',
	'image'            => '',
	'url'              => '',
	'multicheck'       => [],
	'multicolor'       => [],
	'number'           => 0,
	'palette'          => '',
	'radio'            => '',
	'radio_buttonset'  => '',
	'radio_image'      => '',
	'repeater'         => [],
	'select'           => '',
	'slider'           => 0,
	'sortable'         => [],
	'switch'           => false,
	'text'             => '',
	'textarea'         => '',
	'toggle'           => false,
	'typography'       => [],
	'upload'           => '',
	'input_slider'     => 0,
];
foreach ( $sections as $section ) {
    // 必须字段：id、title、panel
    // 可选字段：description、priority
    $section_id = $section['id'];
	new \Kirki\Section(
		$section['id'],
		[
			'title'       => $section['title'],
			'description' => isset( $section['description'] ) ? $section['description'] : '',
			'panel'       => $section['panel'],
			'priority'    => $section['priority'],
		]
	);

	// 自动设置字段排序
	$priority = 10;
	if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
		foreach ( $section['fields'] as &$field ) {
			if ( empty( $field['priority'] ) ) {
				$field['priority'] = $priority;
				$priority += 10;
			}
		}
		unset( $field );
	}

	// 含有fields设置项
	if ( isset( $section['fields'] ) && is_array( $section['fields'] ) ) {
		foreach ( $section['fields'] as $field ) {
			// 自动将当前 section 的 id 分配给字段
			$field['section'] = $section_id;

			// 构造设置项，仅提取允许的参数
			$args = [];
			foreach ( $allowed_params as $param ) {
				if ( isset( $field[ $param ] ) ) {
					$args[ $param ] = $field[ $param ];
				}
			}

			// 对必填项做检查与默认处理
			// 必须字段：label、settings、section、priority，
			if ( ! isset( $args['label'] ) ) {
				$args['label'] = '';
			}
			if ( ! isset( $args['settings'] ) ) {
				// 如果没有设置 settings，则跳过此字段（或记录错误）
				error_log( 'Customize filed setting name missed.' );
				continue;
			}
			if ( ! isset( $args['capability'] ) ) { // Kirki 4.0
				$args['capability'] = 'edit_theme_options';
			}

			$locale = function_exists( 'get_locale' ) ? get_locale() : '';
			if ( strpos( (string) $locale, 'zh_' ) === 0 ) {
				if ( isset( $args['label'] ) ) {
					$args['label'] = iro_customizer_localize_ui_text( (string) $args['label'] );
				}
				if ( isset( $args['description'] ) ) {
					$args['description'] = iro_customizer_localize_ui_text( (string) $args['description'] );
				}
			}
			if ( ! isset( $args['option_type'] ) ) {
				$args['option_type'] = 'theme_mod';
			}
			// if ( ! isset( $args['option_name'] ) ) { // 仅限option类型，theme_mod无效
			// 	$args['option_name'] = 'iro_options';
			// }

			// 自动根据类型补充默认值
			if ( ! isset( $args['default'] ) ) {
				// 将 type 转为小写并用下划线替换空格
				$type_key = strtolower($field['type'] ?? '');
				if ( isset( $type_defaults[ $type_key ] ) ) {
					$args['default'] = $type_defaults[ $type_key ];
				} else {
					$args['default'] = '';
				}
			}

			// switch 控件文案兜底，避免在部分环境下退化为不可用的 On/Off 文本
			if ( strtolower($field['type'] ?? '') === 'switch' && ! isset( $args['choices'] ) ) {
				$locale = function_exists( 'get_locale' ) ? get_locale() : '';
				if ( strpos( $locale, 'zh_' ) === 0 ) {
					$args['choices'] = [
						'on'  => '开',
						'off' => '关',
					];
				} else {
					$args['choices'] = [
						'on'  => 'On',
						'off' => 'Off',
					];
				}
			}

			if ( isset( $args['iro_key'] ) ) {
				$setting_id    = $args['settings'];
				$iro_key       = $args['iro_key'];
				$type_default  = $args['default'];
				$iro_default   = $GLOBALS['iro_options'][$iro_key] ?? null;
				$iro_subkey    = isset( $args['iro_subkey'] ) ? $args['iro_subkey'] : '';

				if ( ! isset( $args['transport'] ) ) { // 没设置预览方式的默认请求php渲染
					$args['transport'] = 'refresh';
				}

				$iro_options_map = get_theme_mod('iro_options_map', []);
				// 构建映射结构
				$iro_options_map[$setting_id] = [
					'iro_key'    => $iro_key,
					'iro_subkey' => $iro_subkey,
					'default'    => $iro_default,
				];

				// 存储映射表
				set_theme_mod('iro_options_map', $iro_options_map);

				// 自动default
				$args['default'] = isset($args['iro_subkey']) 
								? (is_array($iro_default) && isset($iro_default[$args['iro_subkey']]) ? $iro_default[$args['iro_subkey']] : $type_default) 
								: ($iro_default !== null ? $iro_default : $type_default); //从iro_opt中获取默认值，或使用种类默认值

				if ( is_array( $args['default'] ) && in_array( str_replace( ' ', '_', strtolower( $field['type'] ?? '' ) ), [ 'text', 'textarea', 'code', 'url' ], true ) ) {
					$args['default'] = wp_json_encode( $args['default'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
				}
				
				// set_theme_mod($setting_id, $args['default']);
			}

			// 根据字段类型选择对应的 Kirki 类注册组件
			// 将类型字符串统一转换为小写，下划线格式
			// 分类按需实例化
			$field_type_key = str_replace( ' ', '_', strtolower( $field['type'] ) );
			switch ( $field_type_key ) {
				case 'checkbox':
					new \Kirki\Field\Checkbox( $args );
					break;
				case 'code':
					new \Kirki\Field\Code( $args );
					break;
				case 'color':
					new \Kirki\Field\Color( $args );
					break;
				case 'custom':
					new \Kirki\Field\Custom( $args );
					break;
				case 'dashicons':
					new \Kirki\Field\Dashicons( $args );
					break;
				case 'dropdown_pages':
					new \Kirki\Field\Dropdown_Pages( $args );
					break;
				case 'generic':
					new \Kirki\Field\Generic( $args );
					break;
				case 'image':
					new \Kirki\Field\Image( $args );
					break;
				case 'url':
					new \Kirki\Field\URL( $args );
					break;
				case 'multicheck':
					new \Kirki\Field\Multicheck( $args );
					break;
				case 'number':
					new \Kirki\Field\Number( $args );
					break;
				case 'radio':
					new \Kirki\Field\Radio( $args );
					break;
				case 'radio_buttonset':
					new \Kirki\Field\Radio_Buttonset( $args );
					break;
				case 'radio_image':
					new \Kirki\Field\Radio_Image( $args );
					break;
				case 'repeater':
					new \Kirki\Field\Repeater( $args );
					break;
				case 'select':
					new \Kirki\Field\Select( $args );
					break;
				case 'slider':
					new \Kirki\Field\Slider( $args );
					break;
				case 'sortable':
					new \Kirki\Field\Sortable( $args );
					break;
				case 'switch':
					new \Kirki\Field\Checkbox_Switch( $args );
					break;
				case 'text':
					new \Kirki\Field\Text( $args );
					break;
				case 'textarea':
					new \Kirki\Field\Textarea( $args );
					break;
					case 'toggle':
					new \Kirki\Field\Checkbox_Toggle( $args );
					break;
				case 'upload':
					new \Kirki\Field\Upload( $args );
					break;
				case 'input_slider':
					new \Kirki\Pro\Field\InputSlider( $args );
					break;
				case 'divider':
					new \Kirki\Pro\Field\Divider( $args );
					break;
				default:
					error_log( 'Unknown Kirki field type: ' . $field['type'] );
					break;
			}
		}
	}
}