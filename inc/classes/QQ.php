<?php

namespace Sakura\API;

class QQ
{
    public static function get_qq_info($qq) {
        $request_url = 'https://api.qjqq.cn/api/qqinfo?qq=' . rawurlencode($qq);
        $response = \sakurairo_http_get(
            $request_url,
            array(
                'timeout' => 5,
                'redirection' => 2,
            ),
            array(
                'context' => 'qq_info_lookup',
            )
        );

        if (is_wp_error($response)) {
            error_log('Shinonomeiro QQ info request failed: ' . $response->get_error_message());
            return array(
                'status' => 502,
                'success' => false,
                'message' => 'QQ info service unavailable.'
            );
        }

        if (200 !== (int) wp_remote_retrieve_response_code($response)) {
            error_log('Shinonomeiro QQ info request returned non-200 status.');
            return array(
                'status' => 502,
                'success' => false,
                'message' => 'QQ info service unavailable.'
            );
        }

        $name = json_decode(wp_remote_retrieve_body($response), true);
        if (is_array($name) && isset($name['code']) && (int) $name['code'] === 200 && !empty($name['name'])) {
            return array(
                'status' => 200,
                'success' => true,
                'message' => 'success',
                'avatar' => 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq . '&spec=100',
                'name' => $name['name'],
            );
        }

        return array(
            'status' => 404,
            'success' => false,
            'message' => 'QQ number not exist.'
        );
    }

    public static function get_qq_avatar($encrypted) {
        global $sakura_privkey;
        if (isset($encrypted)) {
            $iv = str_repeat($sakura_privkey, 2);
            $encrypted = base64_decode(urldecode($encrypted));
            $qq_number = openssl_decrypt($encrypted, 'aes-128-cbc', $sakura_privkey, 0, $iv);
            preg_match('/^\d{3,}$/', $qq_number, $matches);
            return 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $matches[0] . '&spec=100';
        }
    }
}
