<?php
/**
 * @Author: fuukei
 * @Date:   2022-03-13 18:16:15
 * @Last Modified by: nicocatxzc
 * @Last Modified time: 2025-01-15 11:25:30
 */


/**
 * Classes
 */
include_once('classes/Aplayer.php');
include_once('classes/Bilibili.php');
include_once('classes/Cache.php');
include_once('classes/Images.php');
include_once('classes/gallery.php');
include_once('classes/QQ.php');
include_once('classes/Captcha.php');
include_once('classes/MyAnimeList.php');
include_once('classes/BilibiliFavList.php');
include_once('classes/BilibiliFavListCron.php');
include_once('classes/bangumi.php');
include_once('classes/Steam.php');
use Sakura\API\QQ;
use Sakura\API\Cache;
use Sakura\API\Captcha;
use Sakura\API\BilibiliFavListCron;

function sakurairo_rest_json_response(array $payload, $status)
{
    $response = new WP_REST_Response($payload, (int) $status);
    $response->set_headers(array('Content-Type' => 'application/json'));
    return $response;
}

function sakurairo_rest_upload_permission(WP_REST_Request $request)
{
    if ('POST' !== $request->get_method()) {
        return false;
    }

    if (iro_opt('img_upload_api', 'off') === 'off') {
        return false;
    }

    return check_ajax_referer('wp_rest', '_wpnonce', false) !== false;
}

function sakurairo_rest_has_qq_permission(WP_REST_Request $request)
{
    return $request->get_method() === 'GET';
}

/**
 * Router
 */
add_action('rest_api_init', function () {
    register_rest_route('sakura/v1', '/image/upload', array(
        'methods' => 'POST',
        'callback' => 'upload_image',
        'permission_callback' => 'sakurairo_rest_upload_permission'
    )
    );
    register_rest_route('sakura/v1', '/cache_search/json', array(
        'methods' => 'GET',
        'callback' => 'cache_search_json',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/gallery', array(
        'methods' => 'GET',
        'callback' => [new \Sakura\API\gallery(), 'get_image'],
        'permission_callback' => '__return_true'
    )
    );
    // register_rest_route('sakura/v1', '/database/update', array(
    //     'methods' => 'GET',
    //     'callback' => 'update_database',
    //     'permission_callback'=>'__return_true'
    // ));
    register_rest_route('sakura/v1', '/qqinfo/json', array(
        'methods' => 'GET',
        'callback' => 'get_qq_info',
        'permission_callback' => 'sakurairo_rest_has_qq_permission'
    )
    );
    register_rest_route('sakura/v1', '/qqinfo/avatar', array(
        'methods' => 'GET',
        'callback' => 'get_qq_avatar',
        'permission_callback' => 'sakurairo_rest_has_qq_permission'
    )
    );
    register_rest_route('sakura/v1', '/bangumi/bilibili', array(
        'methods' => 'POST',
        'callback' => 'bgm_bilibili',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/bangumi', array(
        'methods' => 'POST',
        'callback' => 'bgm_bangumi',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/steam', array(
        'methods' => 'POST',
        'callback' => 'steam_library',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/movies/bilibili', array(
        'methods' => 'POST',
        'callback' => 'bfv_bilibili',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/favlist/bilibili', array(
        'methods' => 'GET',
        'callback' => 'favlist_bilibili',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/favlist/bilibili/folders', array(
        'methods' => 'GET',
        'callback' => 'favlist_bilibili_folders',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/meting/aplayer', array(
        'methods' => 'GET',
        'callback' => 'meting_aplayer',
        'permission_callback' => '__return_true'
    )
    );
    register_rest_route('sakura/v1', '/captcha/create', array(
        'methods' => 'GET',
        'callback' => 'create_CAPTCHA',
        'permission_callback' => '__return_true'
    )
    ); 
    // ChatGPT test route
    register_rest_route('sakura/v1', '/chatgpt', array(
        'methods' => 'GET',
        'callback' => 'chatgpt_summarize',
        'permission_callback' =>function ()
        {
         return current_user_can( 'administrator' ) ;
        }
     ));
    
    // 添加复杂名词注释API
    register_rest_route('sakura/v1', '/chatgpt/annotate', array(
        'methods' => 'GET',
        'callback' => 'chatgpt_annotate_terms',
        'permission_callback' =>function ()
        {
         return current_user_can( 'administrator' ) ;
        }
    ));
    // 归档页信息
    register_rest_route('sakura/v1', '/archive_info', array(
        'methods' => 'GET',
        'callback' => function (){
            $time_archive = get_transient('time_archive');
            if (!$time_archive) {
                $time_archive = get_archive_info();
                set_transient('time_archive',$time_archive,30);
            }
            return $time_archive;
        },
        'permission_callback' => '__return_true'
    )
    ); 
});

require_once ('chatgpt/hooks.php');
require_once ('chatgpt/chatgpt.php');

function chatgpt_summarize(WP_REST_Request $request)
{
    $post_id = $request->get_param('post_id');
    $post = get_post($post_id);
    if(!$post) {
        return new WP_REST_Response("Invalid post ID", 400);
    }
    $excerpt = IROChatGPT\summon_article_excerpt($post);
    return new WP_REST_Response($excerpt, 200);
}

function chatgpt_annotate_terms(WP_REST_Request $request)
{
    $post_id = $request->get_param('post_id');
    $post = get_post($post_id);
    if(!$post) {
        return new WP_REST_Response("Invalid post ID", 400);
    }
    
    // 调用管理页面中的注释生成函数
    $result = IROChatGPT\generate_annotations_for_post($post_id);
    
    // 查询结果以确认是否保存成功
    $saved_data = get_post_meta($post_id, 'iro_chatgpt_annotations', true);
    $success_message = "注释生成" . ($result ? "成功" : "失败") . 
                      "。已保存数据: " . (is_array($saved_data) ? count($saved_data) . " 个注释" : "无");
    
    if ($result) {
        return new WP_REST_Response($success_message, 200);
    } else {
        return new WP_REST_Response($success_message, 500);
    }
}

/**
 * Image uploader response
 */
function upload_image(WP_REST_Request $request)
{
    $files = $request->get_file_params();
    $images = new \Sakura\API\Images();
    $validation = $images->validate_uploaded_image($files, 'cmt_img_file');
    if (!$validation['success']) {
        return sakurairo_rest_json_response($validation, $validation['status']);
    }

    $image_file = $validation['file'];
    $api_request = null;
    switch (iro_opt("img_upload_api")) {
        case 'imgur':
            $api_request = $images->Imgur_API($image_file);
            break;
        case 'smms':
            $api_request = $images->SMMS_API($image_file);
            break;
        case 'chevereto':
            $api_request = $images->Chevereto_API($image_file);
            break;
        case 'lsky':
            $api_request = $images->LSKY_API($image_file);
            break;
        default:
            return sakurairo_rest_json_response(array(
                'status' => 503,
                'success' => false,
                'message' => 'Image upload service is unavailable.'
            ), 503);
    }

    return sakurairo_rest_json_response($api_request, $api_request['status']);
}

/*
 * update database rest api
 * @rest api接口路径：https://sakura.2heng.xin/wp-json/sakura/v1/database/update
 */
// function update_database() {
//     if (iro_opt('random_graphs_options') == "webp_optimization") {
//         $output = Cache::update_database();
//         $result = new WP_REST_Response($output, 200);
//         return $result;
//     } else {
//         return new WP_REST_Response("Invalid access", 200);
//     }
// }

/*
 * 定制实时搜索 rest api
 * @rest api接口路径：https://sakura.2heng.xin/wp-json/sakura/v1/cache_search/json
 * @可在cache_search_json()函数末尾通过设置 HTTP header 控制 json 缓存时间
 */
function cache_search_json()
{
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'status' => 403,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        $result = new WP_REST_Response($output, 403);
    } else {
        $output = Cache::search_json();
        $result = new WP_REST_Response($output, 200);
    }
    $result->set_headers(
        array(
            'Content-Type' => 'application/json',
            'Cache-Control' => 'max-age=3600', // json 缓存控制
        )
    );
    return $result;
}

/**
 * QQ info
 * https://sakura.2heng.xin/wp-json/sakura/v1/qqinfo/json
 */
function get_qq_info(WP_REST_Request $request)
{
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'status' => 403,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
    } else {
        $qq = trim((string) $request->get_param('qq'));
        if ($qq === '') {
            $output = array(
                'status' => 400,
                'success' => false,
                'message' => 'Bad Request'
            );
        } else {
            $output = QQ::get_qq_info($qq);
        }
    }

    return sakurairo_rest_json_response($output, $output['status']);
}

/**
 * QQ头像链接解密
 * https://sakura.2heng.xin/wp-json/sakura/v1/qqinfo/avatar
 */
function get_qq_avatar(WP_REST_Request $request)
{
    $encrypted = trim((string) $request->get_param('qq'));
    if ($encrypted === '') {
        return sakurairo_rest_json_response(array(
            'status' => 400,
            'success' => false,
            'message' => 'Missing qq parameter.'
        ), 400);
    }

    $imgurl = QQ::get_qq_avatar($encrypted);
    if (!$imgurl) {
        return sakurairo_rest_json_response(array(
            'status' => 400,
            'success' => false,
            'message' => 'Invalid qq payload.'
        ), 400);
    }

    if (iro_opt('qq_avatar_link') == 'type_2') {
        $remote_response = sakurairo_http_get(
            $imgurl,
            array(
                'timeout' => 5,
                'redirection' => 2,
            ),
            array(
                'context' => 'qq_avatar_proxy',
            )
        );

        if (is_wp_error($remote_response) || 200 !== (int) wp_remote_retrieve_response_code($remote_response)) {
            $message = is_wp_error($remote_response) ? $remote_response->get_error_message() : 'non-200 response';
            error_log('Shinonomeiro QQ avatar proxy failed: ' . $message);
            return new WP_REST_Response(
                array(
                    'status' => 502,
                    'success' => false,
                    'message' => 'QQ avatar fetch failed.'
                ),
                502
            );
        }

        $imgdata = wp_remote_retrieve_body($remote_response);
        $response = new WP_REST_Response($imgdata, 200);
        $response->set_headers(
            array(
                'Content-Type' => 'image/jpeg',
                'Cache-Control' => 'max-age=86400'
            )
        );
    } else {
        $response = new WP_REST_Response();
        $response->set_status(301);
        $response->header('Location', $imgurl);
    }
    return $response;
}

function bgm_bangumi($request)
{
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $response = array(
            'status' => 418,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        return new WP_REST_Response($response, 418);
    } else {
        $userID = $request->get_param('userID');
        $page = $request->get_param('page') ?: 1;
        $bgmList = new \Sakura\API\BangumiList();
    }
    return $bgmList->get_bgm_items($userID, (int)$page);
}

function bgm_bilibili()
{
    $response = null;
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'status' => 403,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        $response = new WP_REST_Response($output, 403);
    } else {
        $page = $_GET["page"] ?: 2;
        $bgm = new \Sakura\API\Bilibili();
        $html = preg_replace("/\s+|\n+|\r/", ' ', $bgm->get_bgm_items($page));
        $response = new WP_REST_Response($html, 200);
    }
    return $response;
}

function bfv_bilibili()
{
    $response = null;
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'status' => 403,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        $response = new WP_REST_Response($output, 403);
    } else {
        $page = $_GET["page"] ?: 2;
        $bgm = new \Sakura\API\Bilibili();
        $html = preg_replace("/\s+|\n+|\r/", ' ', $bgm->get_bfv_items($page));
        $response = new WP_REST_Response($html, 200);
    }
    return $response;
}

function steam_library ($request)
{
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $response = array(
            'status' => 418,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        return new WP_REST_Response($response, 418);
    } else {
        $page = $request->get_param('page') ?: 1;
        $SteamList = new \Sakura\API\Steam();
    }
    return $SteamList->get_steam_items((int)$page);
}

function favlist_bilibili(WP_REST_Request $request)
{
    // 使用 WP_REST_Request 参数而不是直接访问 $_GET
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'code' => 401,
            'message' => 'Unauthorized client.'
        );
        return new WP_REST_Response($output, 401);
    }
    
    // 获取请求参数
    $page = $request->get_param('page') ? intval($request->get_param('page')) : 1;
    $folder_id = $request->get_param('folder_id') ? intval($request->get_param('folder_id')) : 0;
    
    if (!$folder_id) {
        $output = array(
            'code' => 400,
            'message' => 'Missing folder_id parameter'
        );
        return new WP_REST_Response($output, 400);
    }
    
    try {
        // 缓存键名
        $cache_key = 'bilibili_favlist_' . $folder_id . '_' . $page;
        
        // 从缓存获取数据
        $folder_data = BilibiliFavListCron::get_cache($cache_key);
        $from_cache = ($folder_data !== false);
        
        // 如果缓存不存在，则从API获取
        if ($folder_data === false) {
            $bgm = new \Sakura\API\BilibiliFavList();
            $folder_resp = $bgm->fetch_folder_item_api($folder_id, $page);
            
            if (!$folder_resp) {
                throw new Exception('Failed to fetch folder items');
            }
            
            $folder_data = $folder_resp['data'];
            
            // 存入缓存
            set_transient($cache_key, $folder_data, BilibiliFavListCron::CACHE_EXPIRY);
            set_transient($cache_key . '_expire', time() + BilibiliFavListCron::CACHE_EXPIRY, BilibiliFavListCron::CACHE_EXPIRY);
        }
        
        $output = array(
            'code' => 0,
            'message' => 'success',
            'data' => $folder_data,
            'cache_info' => array(
                'from_cache' => $from_cache,
                'expires_in' => BilibiliFavListCron::get_cache_expiry($cache_key)
            )
        );
        return new WP_REST_Response($output, 200);
        
    } catch (Exception $e) {
        error_log('BilibiliFavList API error: ' . $e->getMessage());
        $output = array(
            'code' => 500,
            'message' => 'Failed to fetch folder items: ' . $e->getMessage()
        );
        return new WP_REST_Response($output, 500);
    }
}

function favlist_bilibili_folders(WP_REST_Request $request)
{
    if (!check_ajax_referer('wp_rest', '_wpnonce', false)) {
        $output = array(
            'code' => 401,
            'message' => 'Unauthorized client.'
        );
        return new WP_REST_Response($output, 401);
    }
    
    try {
        // 先尝试从缓存获取数据
        $folders_data = BilibiliFavListCron::get_cache('bilibili_favlist_folders');
        $from_cache = ($folders_data !== false);
        
        // 如果没有缓存或缓存过期，则从API获取并更新缓存
        if ($folders_data === false) {
            $bgm = new \Sakura\API\BilibiliFavList();
            $folders_resp = $bgm->fetch_folder_api();
            
            if (!$folders_resp) {
                throw new Exception('Failed to fetch folders');
            }
            
            if (!isset($folders_resp['data']) || $folders_resp['data'] === null) {
                throw new Exception('No folder data returned from API');
            }
            
            $folders_data = $folders_resp['data'];
            
            // 存入缓存
            set_transient('bilibili_favlist_folders', $folders_data, BilibiliFavListCron::CACHE_EXPIRY);
            set_transient('bilibili_favlist_folders_expire', time() + BilibiliFavListCron::CACHE_EXPIRY, BilibiliFavListCron::CACHE_EXPIRY);
        }
        
        $output = array(
            'code' => 0,
            'message' => 'success',
            'data' => $folders_data,
            'cache_info' => array(
                'from_cache' => $from_cache,
                'expires_in' => BilibiliFavListCron::get_cache_expiry('bilibili_favlist_folders')
            )
        );
        return new WP_REST_Response($output, 200);
        
    } catch (Exception $e) {
        error_log('BilibiliFavList Folders API error: ' . $e->getMessage());
        $output = array(
            'code' => 500,
            'message' => 'Failed to fetch folders: ' . $e->getMessage()
        );
        return new WP_REST_Response($output, 500);
    }
}

function meting_aplayer()
{
    $type = $_GET['type'];
    $id = $_GET['id'];
    if (in_array('_wpnonce', $_GET))
        $wpnonce = $_GET['_wpnonce'];
    if (in_array('meting_nonce', $_GET))
        $meting_nonce = $_GET['meting_nonce'];
    if ((isset($wpnonce) && !check_ajax_referer('wp_rest', $wpnonce, false)) || (isset($meting_nonce) && !wp_verify_nonce($meting_nonce, $type . '#:' . $id))) {
        $output = array(
            'status' => 403,
            'success' => false,
            'message' => 'Unauthorized client.'
        );
        $response = new WP_REST_Response($output, 403);
    } else {
        $Meting_API = new \Sakura\API\Aplayer();
        $data = $Meting_API->get_data($type, $id);
        if ($type === 'playlist') {
            $response = new WP_REST_Response($data, 200);
            $response->set_headers(array('cache-control' => 'max-age=3600'));
        } elseif ($type === 'lyric') {
            $response = new WP_REST_Response();
            $response->set_headers(array('cache-control' => 'max-age=3600'));
            $response->set_headers(array('Content-Type' => 'text/plain; charset=utf-8'));
            $response->set_data($data);
        } else {
            $response = new WP_REST_Response();
            $response->set_status(301);
            $response->header('Location', $data);
        }
    }
    return $response;
}

function create_CAPTCHA()
{
    $CAPTCHA = new Captcha();
    $response = new WP_REST_Response($CAPTCHA->create_captcha_img());
    $response->set_status(200);
    $response->set_headers(array('Content-Type' => 'application/json'));
    return $response;
}
