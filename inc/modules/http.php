<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('sakurairo_http_default_allowlist')) {
    function sakurairo_http_default_allowlist() {
        $defaults = array(
            'api.fuukei.org',
            'api.github.com',
            'api.qjqq.cn',
            'q2.qlogo.cn',
            'ptlogin2.qq.com',
            'ip-api.com',
            'api.bilibili.com',
            'api.bgm.tv',
            'myanimelist.net',
            '*.myanimelist.net',
            'api.steampowered.com',
            'challenges.cloudflare.com',
            'sm.ms',
        );

        return apply_filters('sakurairo_http_allowlist', $defaults);
    }
}

if (!function_exists('sakurairo_http_normalize_allowlist')) {
    function sakurairo_http_normalize_allowlist($allowlist) {
        if (empty($allowlist)) {
            return array();
        }

        if (!is_array($allowlist)) {
            $allowlist = array($allowlist);
        }

        $normalized = array();
        foreach ($allowlist as $item) {
            if (!is_string($item)) {
                continue;
            }

            $item = strtolower(trim($item));
            if ($item !== '') {
                $normalized[] = $item;
            }
        }

        return array_values(array_unique($normalized));
    }
}

if (!function_exists('sakurairo_http_is_host_allowed')) {
    function sakurairo_http_is_host_allowed($host, $allowlist) {
        $host = strtolower((string) $host);
        if ($host === '') {
            return false;
        }

        foreach ($allowlist as $rule) {
            if ($rule === $host) {
                return true;
            }

            if (strpos($rule, '*.') === 0) {
                $base = substr($rule, 2);
                if ($host === $base) {
                    return true;
                }

                $suffix = '.' . $base;
                if (strlen($host) > strlen($suffix) && substr($host, -strlen($suffix)) === $suffix) {
                    return true;
                }
            }
        }

        return false;
    }
}

if (!function_exists('sakurairo_http_error_log')) {
    function sakurairo_http_error_log($context, $url, $message) {
        $parts = wp_parse_url($url);
        $target = '';
        if (is_array($parts)) {
            $target = (isset($parts['host']) ? $parts['host'] : '') . (isset($parts['path']) ? $parts['path'] : '');
        }

        if ($target === '') {
            $target = (string) $url;
        }

        error_log(sprintf('Shinonomeiro HTTP [%s] %s - %s', $context, $target, $message));
    }
}

if (!function_exists('sakurairo_http_request')) {
    function sakurairo_http_request($method, $url, $args = array(), $meta = array()) {
        $meta = wp_parse_args($meta, array(
            'context' => 'general',
            'allowlist' => array(),
            'enforce_allowlist' => true,
            'timeout' => 8,
        ));

        $method = strtoupper((string) $method);
        $parts = wp_parse_url($url);
        $scheme = isset($parts['scheme']) ? strtolower($parts['scheme']) : '';
        $host = isset($parts['host']) ? strtolower($parts['host']) : '';

        if (!in_array($scheme, array('http', 'https'), true) || $host === '') {
            $error = new WP_Error(
                'sakurairo_http_invalid_url',
                __('Invalid remote URL.', 'sakurairo'),
                array('url' => $url, 'context' => $meta['context'])
            );
            sakurairo_http_error_log($meta['context'], $url, 'invalid url');
            return $error;
        }

        if (!empty($meta['enforce_allowlist'])) {
            $allowlist = array_merge(
                sakurairo_http_normalize_allowlist(sakurairo_http_default_allowlist()),
                sakurairo_http_normalize_allowlist($meta['allowlist'])
            );
            $allowlist = array_values(array_unique($allowlist));

            if (!sakurairo_http_is_host_allowed($host, $allowlist)) {
                $error = new WP_Error(
                    'sakurairo_http_disallowed_host',
                    __('Remote host is not in allowlist.', 'sakurairo'),
                    array('host' => $host, 'url' => $url, 'context' => $meta['context'])
                );
                sakurairo_http_error_log($meta['context'], $url, 'blocked by allowlist');
                return $error;
            }
        }

        $args = wp_parse_args($args, array(
            'timeout' => (int) $meta['timeout'],
            'redirection' => 3,
            'blocking' => true,
            'user-agent' => 'Shinonomeiro/' . (defined('IRO_VERSION') ? IRO_VERSION : 'unknown'),
        ));
        $args['method'] = $method;

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            sakurairo_http_error_log($meta['context'], $url, $response->get_error_message());
            return $response;
        }

        $status = (int) wp_remote_retrieve_response_code($response);
        if ($status < 200 || $status >= 300) {
            $error = new WP_Error(
                'sakurairo_http_bad_status',
                sprintf(__('Unexpected HTTP status: %d', 'sakurairo'), $status),
                array(
                    'status' => $status,
                    'url' => $url,
                    'context' => $meta['context'],
                )
            );
            sakurairo_http_error_log($meta['context'], $url, 'unexpected status ' . $status);
            return $error;
        }

        return $response;
    }
}

if (!function_exists('sakurairo_http_get')) {
    function sakurairo_http_get($url, $args = array(), $meta = array()) {
        return sakurairo_http_request('GET', $url, $args, $meta);
    }
}

if (!function_exists('sakurairo_http_post')) {
    function sakurairo_http_post($url, $args = array(), $meta = array()) {
        return sakurairo_http_request('POST', $url, $args, $meta);
    }
}
