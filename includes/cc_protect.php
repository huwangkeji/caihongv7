<?php

/**
 * CC 攻击防护核心函数库
 *
 * 修复来源：后台体检报告 — includes/cc_protect.php include 缺失
 * admin/cc_protect.php 第 8 行 include_once SYSTEM_ROOT . 'cc_protect.php'，
 * 原文件缺失导致 "failed to open stream" 报错。
 * 现按 admin/cc_protect.php 的调用签名补齐以下函数：
 *   - setup_cc_protect()            初始化 pre_cc_log / pre_cc_ban 表
 *   - ban_ip_manually()             手动封禁 IP
 *   - unban_ip()                    解除 IP 封禁
 *   - clean_cc_ban_list()           清理过期封禁
 *   - get_cc_attack_stats()         获取统计信息（current_bans / today_blocks）
 */

if (!function_exists('setup_cc_protect')) {
    /**
     * 初始化防CC相关数据表（首次访问时调用）
     */
    function setup_cc_protect()
    {
        global $DB;

        if (!$DB || !is_object($DB)) {
            return false;
        }

        try {
            // 访问日志表
            $DB->exec("CREATE TABLE IF NOT EXISTS `pre_cc_log` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ip` varchar(64) NOT NULL DEFAULT '',
                `uri` varchar(255) NOT NULL DEFAULT '',
                `user_agent` varchar(255) NOT NULL DEFAULT '',
                `log_time` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                KEY `idx_ip` (`ip`),
                KEY `idx_log_time` (`log_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // IP 封禁表
            $DB->exec("CREATE TABLE IF NOT EXISTS `pre_cc_ban` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `ip` varchar(64) NOT NULL DEFAULT '',
                `reason` varchar(255) NOT NULL DEFAULT '',
                `block_time` int(11) NOT NULL DEFAULT '0',
                `expire_time` int(11) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`),
                UNIQUE KEY `idx_ip` (`ip`),
                KEY `idx_expire_time` (`expire_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            return true;
        } catch (Exception $e) {
            error_log('setup_cc_protect 失败: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('ban_ip_manually')) {
    /**
     * 手动封禁 IP
     *
     * @param string $ip        IP 地址
     * @param string $reason    封禁原因
     * @param int    $ban_hours 封禁时长（小时）
     */
    function ban_ip_manually($ip, $reason = '手动封禁', $ban_hours = 24)
    {
        global $DB;

        if (!$DB || !is_object($DB) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }

        $block_time  = time();
        $expire_time = $block_time + intval($ban_hours) * 86400;

        try {
            $exists = $DB->getRow("SELECT id FROM pre_cc_ban WHERE ip=:ip LIMIT 1", [':ip' => $ip]);
            if ($exists) {
                $DB->exec("UPDATE pre_cc_ban SET reason=:reason, block_time=:block_time, expire_time=:expire_time WHERE ip=:ip", [
                    ':reason'      => $reason,
                    ':block_time'  => $block_time,
                    ':expire_time' => $expire_time,
                    ':ip'          => $ip,
                ]);
            } else {
                $DB->exec("INSERT INTO pre_cc_ban (ip, reason, block_time, expire_time) VALUES (:ip, :reason, :block_time, :expire_time)", [
                    ':ip'          => $ip,
                    ':reason'      => $reason,
                    ':block_time'  => $block_time,
                    ':expire_time' => $expire_time,
                ]);
            }
            return true;
        } catch (Exception $e) {
            error_log('ban_ip_manually 失败: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('unban_ip')) {
    /**
     * 解除 IP 封禁
     *
     * @param string $ip IP 地址
     */
    function unban_ip($ip)
    {
        global $DB;

        if (!$DB || !is_object($DB)) {
            return false;
        }

        try {
            $DB->exec("DELETE FROM pre_cc_ban WHERE ip=:ip", [':ip' => $ip]);
            return true;
        } catch (Exception $e) {
            error_log('unban_ip 失败: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('clean_cc_ban_list')) {
    /**
     * 清理已过期的封禁记录
     */
    function clean_cc_ban_list()
    {
        global $DB;

        if (!$DB || !is_object($DB)) {
            return false;
        }

        try {
            $DB->exec("DELETE FROM pre_cc_ban WHERE expire_time > 0 AND expire_time < :time", [':time' => time()]);
            return true;
        } catch (Exception $e) {
            error_log('clean_cc_ban_list 失败: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('get_cc_attack_stats')) {
    /**
     * 获取防CC统计信息
     *
     * @return array current_bans 当前封禁数 / today_blocks 今日拦截次数
     */
    function get_cc_attack_stats()
    {
        global $DB;

        $stats = [
            'current_bans' => 0,
            'today_blocks' => 0,
        ];

        if (!$DB || !is_object($DB)) {
            return $stats;
        }

        try {
            $stats['current_bans'] = intval($DB->getColumn(
                "SELECT COUNT(*) FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0)",
                [':time' => time()]
            ));

            $today_start = strtotime(date('Y-m-d 00:00:00'));
            $stats['today_blocks'] = intval($DB->getColumn(
                "SELECT COUNT(*) FROM pre_cc_log WHERE log_time > :start",
                [':start' => $today_start]
            ));
        } catch (Exception $e) {
            error_log('get_cc_attack_stats 失败: ' . $e->getMessage());
        }

        return $stats;
    }
}
