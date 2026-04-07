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

        $encrypted = is_string($encrypted) ? trim($encrypted) : '';
        if ($encrypted === '' || !is_string($sakura_privkey) || strlen($sakura_privkey) < 8) {
            return null;
        }

        $payload = base64_decode(urldecode($encrypted), true);
        if ($payload === false) {
            return null;
        }

        $iv_length = openssl_cipher_iv_length('aes-128-cbc');
        if (!is_int($iv_length) || $iv_length <= 0 || strlen($payload) <= $iv_length) {
            return null;
        }

        $iv = substr($payload, 0, $iv_length);
        $ciphertext = substr($payload, $iv_length);
        if ($ciphertext === '') {
            return null;
        }

        $qq_number = openssl_decrypt($ciphertext, 'aes-128-cbc', $sakura_privkey, 0, $iv);
        if (!is_string($qq_number) || preg_match('/^\d{3,}$/', $qq_number) !== 1) {
            return null;
        }

        return 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq_number . '&spec=100';
    }
}
