<?php

/**
 * 遗留符号兼容层
 *
 * 原 includes/common.php（混淆单行，已隔离至 .workbuddy/quarantine/）
 * 在被替换前对外暴露了若干函数。全项目检索确认其中只有 x_real_ip()
 * 被业务代码调用（includes/lib/RegionPrice.php），其余为混淆层内部使用。
 *
 * 本文件以干净可读的方式重新提供这些符号，保证替换后不会因
 * "Call to undefined function" 而致命错误。所有实现都做了 function_exists
 * 保护，便于日后官方代码自行定义时自动让位。
 */

if (!function_exists('x_real_ip')) {
    /**
     * 获取客户端真实 IP
     *
     * 与 real_ip() 的区别：本函数用于业务留痕（如区域定价日志），
     * 只信任 REMOTE_ADDR 与经过严格校验的代理头，且非法值一律回落
     * 到 REMOTE_ADDR，避免日志被伪造 IP 污染。
     *
     * @return string
     */
    function x_real_ip()
    {
        $fallback = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';

        $candidates = [];
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            $candidates[] = $_SERVER['HTTP_X_REAL_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // XFF 可能是逗号分隔的链路，取最左侧
            foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $part) {
                $candidates[] = trim($part);
            }
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $candidates[] = $_SERVER['HTTP_CLIENT_IP'];
        }

        foreach ($candidates as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }

        return $fallback;
    }
}

if (!function_exists('check_spider')) {
    /**
     * 判断当前访客是否为搜索引擎爬虫
     *
     * @return bool
     */
    function check_spider()
    {
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        if ($ua === '') {
            return false;
        }
        $keywords = [
            'bot', 'crawl', 'spider', 'slurp', 'sohu-search', 'lycos', 'robozilla',
            'google', 'bing', 'baidu', 'yahoo', 'sogou', 'so.com', 'youdao', 'archiver',
        ];
        foreach ($keywords as $kw) {
            if (strpos($ua, $kw) !== false) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('cc_defender')) {
    /**
     * CC 防护：基于 Session 的滑动窗口计数
     *
     * 原混淆实现的具体阈值已不可考，此处按保守策略实现：
     * 同一会话 60 秒内超过 120 次请求才判定为异常，仅返回判定结果，
     * 不做阻断，交由调用方决定。
     *
     * @param int $window 统计窗口（秒）
     * @param int $limit  窗口内允许的最大请求数
     *
     * @return bool true 表示触发 CC 阈值
     */
    function cc_defender($window = 60, $limit = 120)
    {
        if (PHP_SAPI === 'cli') {
            return false;
        }
        if (session_status() === PHP_SESSION_NONE) {
            return false;
        }

        $now = time();
        if (empty($_SESSION['cc_defender'])) {
            $_SESSION['cc_defender'] = ['start' => $now, 'count' => 0];
        }
        if ($now - $_SESSION['cc_defender']['start'] > $window) {
            $_SESSION['cc_defender'] = ['start' => $now, 'count' => 0];
        }
        $_SESSION['cc_defender']['count']++;

        return $_SESSION['cc_defender']['count'] > $limit;
    }
}

if (!function_exists('copydirs')) {
    /**
     * 递归复制目录
     *
     * @param string $source 源目录
     * @param string $dest   目标目录
     *
     * @return bool
     */
    function copydirs($source, $dest)
    {
        $source = rtrim((string)$source, '/\\');
        $dest   = rtrim((string)$dest, '/\\');
        if (!is_dir($source)) {
            return false;
        }
        if (!is_dir($dest) && !@mkdir($dest, 0755, true)) {
            return false;
        }

        $handle = @opendir($source);
        if (!$handle) {
            return false;
        }
        while (($entry = readdir($handle)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $srcPath = $source . DIRECTORY_SEPARATOR . $entry;
            $dstPath = $dest . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($srcPath)) {
                copydirs($srcPath, $dstPath);
            } else {
                @copy($srcPath, $dstPath);
            }
        }
        closedir($handle);
        return true;
    }
}

if (!function_exists('rmdirs')) {
    /**
     * 递归删除目录
     *
     * @param string $dir    目录
     * @param bool   $rmself 是否连自身一起删除
     *
     * @return bool
     */
    function rmdirs($dir, $rmself = true)
    {
        $dir = rtrim((string)$dir, '/\\');
        if (!is_dir($dir)) {
            return false;
        }

        $handle = @opendir($dir);
        if (!$handle) {
            return false;
        }
        while (($entry = readdir($handle)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($path)) {
                rmdirs($path);
            } else {
                @unlink($path);
            }
        }
        closedir($handle);

        return $rmself ? @rmdir($dir) : true;
    }
}

if (!function_exists('checkMySQLService')) {
    /**
     * 检测 MySQL 服务可达性
     *
     * @param array $dbconfig 数据库配置
     *
     * @return bool
     */
    function checkMySQLService($dbconfig)
    {
        if (!is_array($dbconfig) || empty($dbconfig['host'])) {
            return false;
        }
        $host = $dbconfig['host'];
        $port = isset($dbconfig['port']) ? (int)$dbconfig['port'] : 3306;

        $errno  = 0;
        $errstr = '';
        $fp = @fsockopen($host, $port, $errno, $errstr, 2);
        if (!$fp) {
            return false;
        }
        fclose($fp);
        return true;
    }
}

if (!function_exists('checkDatabaseError')) {
    /**
     * 数据库连接异常时的统一处理：记录日志并渲染错误页
     *
     * @param array          $dbconfig  数据库配置
     * @param \Throwable|null $exception 原始异常
     */
    function checkDatabaseError($dbconfig, $exception = null)
    {
        $message = $exception && method_exists($exception, 'getMessage')
            ? $exception->getMessage()
            : '未知数据库错误';

        @file_put_contents(
            (defined('SYSTEM_ROOT') ? SYSTEM_ROOT : __DIR__ . '/') . 'log/db_error_' . date('Y-m-d') . '.log',
            '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
            FILE_APPEND
        );

        if (class_exists('\lib\Bootstrap')) {
            \lib\Bootstrap::renderInstallPage(
                '数据库异常',
                '无法连接到数据库：' . $message,
                '请检查 MySQL 服务是否启动，以及 config.php 中的主机、端口、账号、密码是否正确。'
            );
        }

        header('Content-Type: text/html; charset=UTF-8');
        http_response_code(503);
        echo '数据库异常，请稍后再试。';
        exit;
    }
}

if (!function_exists('showDatabaseError')) {
    /**
     * 渲染数据库错误提示并终止
     *
     * @param string $message
     */
    function showDatabaseError($message)
    {
        checkDatabaseError(isset($GLOBALS['dbconfig']) ? $GLOBALS['dbconfig'] : [], null);
    }
}
