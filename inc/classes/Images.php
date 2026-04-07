<?php

namespace Sakura\API;

use CURLFile;
use RuntimeException;

class Images
{
    private $chevereto_api_key;
    private $imgur_client_id;
    private $smms_client_id;
    private $lsky_api_key;

    public function __construct() {
        $this->chevereto_api_key = iro_opt('chevereto_api_key');
        $this->lsky_api_key = iro_opt('lsky_api_key');
        $this->imgur_client_id = iro_opt('imgur_client_id');
        $this->smms_client_id = iro_opt('smms_client_id');
    }

    /**
     * 返回默认的错误图片 SVG（支持国际化）
     */
    private function getDefaultErrorImage() {
        $error_text = __('Image Failed to Load', 'sakurairo');
        return 'data:image/svg+xml;utf8,' . urlencode('<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200"><rect x="5" y="5" width="290" height="190" rx="10" fill="#f8f9fa" stroke="#ddd"/><circle cx="150" cy="70" r="30" fill="#ff6b6b"/><text x="150" y="80" font-family="Arial" font-size="30" text-anchor="middle" fill="#fff">!</text><text x="150" y="130" font-family="Arial" font-size="16" text-anchor="middle" fill="#555">' . $error_text . '</text></svg>');
    }

    private function buildResult($status, $success, $message, $link = null) {
        if ($link === null || $link === '') {
            $link = $this->getDefaultErrorImage();
        }

        return array(
            'status' => (int) $status,
            'success' => (bool) $success,
            'message' => (string) $message,
            'link' => $link,
            'proxy' => iro_opt('comment_image_proxy') . $link,
        );
    }

    private function requireConfig($value, $message) {
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException($message);
        }

        return trim($value);
    }

    private function decodeResponse($body, $context) {
        $decoded = json_decode((string) $body, true);
        if (!is_array($decoded)) {
            throw new RuntimeException($context . ': invalid JSON response');
        }

        return $decoded;
    }

    private function createCurlFile(array $image) {
        if (!class_exists('CURLFile')) {
            throw new RuntimeException('CURLFile is unavailable.');
        }

        return new CURLFile(
            $image['tmp_name'],
            $image['type'],
            $image['name']
        );
    }

    private function curlRequest($url, array $options) {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('cURL extension is unavailable.');
        }

        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('Unable to initialize cURL.');
        }

        $defaults = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        );

        foreach ($defaults + $options as $key => $value) {
            curl_setopt($ch, $key, $value);
        }

        $body = curl_exec($ch);
        if ($body === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('cURL request failed: ' . $error);
        }

        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new RuntimeException('Unexpected HTTP status: ' . $status);
        }

        return $body;
    }

    public function validate_uploaded_image($files, $field_name = 'cmt_img_file') {
        if (!is_array($files) || !isset($files[$field_name]) || !is_array($files[$field_name])) {
            return $this->buildResult(400, false, __('Missing uploaded file.', 'sakurairo'));
        }

        $image = $files[$field_name];
        if (!isset($image['error']) || (int) $image['error'] !== UPLOAD_ERR_OK) {
            return $this->buildResult(400, false, __('Image upload failed.', 'sakurairo'));
        }

        if (empty($image['tmp_name']) || !is_uploaded_file($image['tmp_name'])) {
            return $this->buildResult(400, false, __('Invalid uploaded file.', 'sakurairo'));
        }

        $max_size_mb = (float) iro_opt('img_upload_max_size', 5);
        if ($max_size_mb <= 0) {
            $max_size_mb = 5;
        }

        $max_size_bytes = (int) ceil($max_size_mb * 1024 * 1024);
        if (!isset($image['size']) || (int) $image['size'] <= 0 || (int) $image['size'] > $max_size_bytes) {
            return $this->buildResult(413, false, sprintf(__('Image exceeds the maximum allowed size of %s MB.', 'sakurairo'), $max_size_mb));
        }

        $extension = strtolower((string) pathinfo((string) $image['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        if (!in_array($extension, $allowed_extensions, true)) {
            return $this->buildResult(400, false, __('Unsupported image file extension.', 'sakurairo'));
        }

        $finfo = wp_check_filetype_and_ext($image['tmp_name'], $image['name']);
        $allowed_mimes = array(
            'jpg|jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        );

        if (
            empty($finfo['ext']) ||
            empty($finfo['type']) ||
            !in_array($finfo['type'], $allowed_mimes, true)
        ) {
            return $this->buildResult(400, false, __('Unsupported image MIME type.', 'sakurairo'));
        }

        $image['type'] = $finfo['type'];

        return array(
            'status' => 200,
            'success' => true,
            'message' => 'success',
            'file' => $image,
        );
    }

    /**
     * LSky Pro upload interface
     */
    public function LSKY_API($image) {
        try {
            $upload_url = trailingslashit($this->requireConfig(iro_opt('lsky_url'), 'LSKY upload URL is missing.')) . 'api/v1/upload';
            $token = $this->requireConfig($this->lsky_api_key, 'LSKY API token is missing.');
            $body = $this->curlRequest($upload_url, array(
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token,
                    'Accept: application/json',
                ),
                CURLOPT_POSTFIELDS => array(
                    'file' => $this->createCurlFile($image),
                ),
            ));
            $reply = $this->decodeResponse($body, 'LSKY upload');
            if (!empty($reply['status']) && !empty($reply['data']['links']['url'])) {
                return $this->buildResult(200, true, 'success', $reply['data']['links']['url']);
            }

            $message = isset($reply['message']) ? $reply['message'] : 'LSKY upload failed.';
            return $this->buildResult(400, false, $message);
        } catch (\Throwable $e) {
            error_log('Images LSKY upload failed: ' . $e->getMessage());
            return $this->buildResult(502, false, $e->getMessage());
        }
    }

    /**
     * Chevereto upload interface
     */
    public function Chevereto_API($image) {
        try {
            $upload_url = trailingslashit($this->requireConfig(iro_opt('cheverto_url'), 'Chevereto upload URL is missing.')) . 'api/1/upload';
            $api_key = $this->requireConfig($this->chevereto_api_key, 'Chevereto API key is missing.');
            $body = $this->curlRequest($upload_url, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array(
                    'source' => $this->createCurlFile($image),
                    'key' => $api_key,
                ),
            ));
            $reply = $this->decodeResponse($body, 'Chevereto upload');
            if (
                isset($reply['status_txt'], $reply['status_code']) &&
                $reply['status_txt'] === 'OK' &&
                (int) $reply['status_code'] === 200 &&
                !empty($reply['image']['image']['url'])
            ) {
                return $this->buildResult(200, true, 'success', $reply['image']['image']['url']);
            }

            $message = $reply['error']['message'] ?? 'Chevereto upload failed.';
            $status = isset($reply['status_code']) ? (int) $reply['status_code'] : 400;
            return $this->buildResult($status, false, $message);
        } catch (\Throwable $e) {
            error_log('Images Chevereto upload failed: ' . $e->getMessage());
            return $this->buildResult(502, false, $e->getMessage());
        }
    }

    /**
     * Imgur upload interface
     */
    public function Imgur_API($image) {
        try {
            $upload_url = $this->requireConfig(iro_opt('imgur_upload_image_proxy'), 'Imgur upload URL is missing.');
            $client_id = $this->requireConfig($this->imgur_client_id, 'Imgur Client ID is missing.');
            $body = $this->curlRequest($upload_url, array(
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Client-ID ' . $client_id,
                ),
                CURLOPT_POSTFIELDS => array(
                    'image' => $this->createCurlFile($image),
                ),
            ));
            $reply = $this->decodeResponse($body, 'Imgur upload');
            if (!empty($reply['success']) && (int) ($reply['status'] ?? 0) === 200 && !empty($reply['data']['link'])) {
                return $this->buildResult(200, true, 'success', $reply['data']['link']);
            }

            $message = $reply['data']['error'] ?? 'Imgur upload failed.';
            $status = isset($reply['status']) ? (int) $reply['status'] : 400;
            return $this->buildResult($status, false, $message);
        } catch (\Throwable $e) {
            error_log('Images Imgur upload failed: ' . $e->getMessage());
            return $this->buildResult(502, false, $e->getMessage());
        }
    }

    /**
     * sm.ms upload interface
     */
    public function SMMS_API($image) {
        try {
            $client_id = $this->requireConfig($this->smms_client_id, 'SM.MS token is missing.');
            $body = $this->curlRequest('https://sm.ms/api/v2/upload', array(
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . $client_id,
                    'Accept: application/json',
                    'User-Agent: Shinonomeiro/' . (defined('IRO_VERSION') ? IRO_VERSION : 'unknown'),
                ),
                CURLOPT_POSTFIELDS => array(
                    'smfile' => $this->createCurlFile($image),
                ),
            ));
            $reply = $this->decodeResponse($body, 'SM.MS upload');
            if (!empty($reply['success']) && ($reply['code'] ?? '') === 'success' && !empty($reply['data']['url'])) {
                return $this->buildResult(200, true, (string) ($reply['message'] ?? 'success'), $reply['data']['url']);
            }

            $message = (string) ($reply['message'] ?? 'SM.MS upload failed.');
            if (preg_match("/Image upload repeated limit/i", $message)) {
                $link = str_replace('Image upload repeated limit, this image exists at: ', '', $message);
                return $this->buildResult(200, true, $message, $link);
            }

            return $this->buildResult(400, false, $message);
        } catch (\Throwable $e) {
            error_log('Images SM.MS upload failed: ' . $e->getMessage());
            return $this->buildResult(502, false, $e->getMessage());
        }
    }
}
