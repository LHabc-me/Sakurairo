<?php
// 定时刷新 Bilibili 收藏夹缓存数据

namespace Sakura\API;

class BilibiliFavListCron {
    // 缓存有效期常量（12 小时 = 43200 秒）
    const CACHE_EXPIRY = 43200;

    /**
     * 初始化定时任务
     */
    public static function init() {
        add_action('bilibili_favlist_update_cron', array(__CLASS__, 'update_all_favlist_data'));
        self::ensure_scheduled_event();
    }

    /**
     * 确保定时任务已正确注册
     */
    public static function ensure_scheduled_event() {
        if (!wp_next_scheduled('bilibili_favlist_update_cron')) {
            wp_schedule_event(time(), 'twicedaily', 'bilibili_favlist_update_cron');
        }
    }

    /**
     * 重新注册定时任务
     */
    public static function schedule_updates() {
        self::clear_scheduled_updates();
        wp_schedule_event(time(), 'twicedaily', 'bilibili_favlist_update_cron');
    }

    /**
     * 清理定时任务
     */
    public static function clear_scheduled_updates() {
        $timestamp = wp_next_scheduled('bilibili_favlist_update_cron');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'bilibili_favlist_update_cron');
        }
    }

    /**
     * 刷新所有收藏夹数据并更新缓存
     */
    public static function update_all_favlist_data() {
        $uid = iro_opt('bilibili_id');
        if (empty($uid)) {
            error_log("BilibiliFavListCron: UID is empty, cannot update favlist data");
            return false;
        }

        try {
            $bilibili_api = new BilibiliFavList();
            $folders_data = $bilibili_api->fetch_folder_api();
            if (!$folders_data || !isset($folders_data['data']) || !isset($folders_data['data']['list'])) {
                error_log("BilibiliFavListCron: Failed to fetch folders list");
                return false;
            }

            $folders = $folders_data['data'];
            self::save_cache('bilibili_favlist_folders', $folders);

            foreach ($folders['list'] as $folder) {
                $folder_id = $folder['id'];
                $folder_data = $bilibili_api->fetch_folder_item_api($folder_id, 1);

                if ($folder_data && isset($folder_data['data'])) {
                    self::save_cache('bilibili_favlist_' . $folder_id . '_1', $folder_data['data']);
                    error_log("BilibiliFavListCron: Updated cache for folder " . $folder['title'] . " (ID: $folder_id)");
                }
            }

            return true;
        } catch (\Exception $e) {
            error_log("BilibiliFavListCron: Error updating favlist data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 保存数据到 WordPress 缓存
     */
    private static function save_cache($key, $data) {
        set_transient($key, $data, self::CACHE_EXPIRY);
        set_transient($key . '_expire', time() + self::CACHE_EXPIRY, self::CACHE_EXPIRY);
    }

    /**
     * 获取缓存
     */
    public static function get_cache($key) {
        return get_transient($key);
    }

    /**
     * 获取缓存剩余有效期
     */
    public static function get_cache_expiry($key) {
        $expire = get_transient($key . '_expire');
        if ($expire === false) {
            return 0;
        }

        return max(0, $expire - time());
    }
}
