<?php

/**
 * 系统引导层（干净可审计实现）
 *
 * 本文件替代原 includes/common.php 中被混淆压缩的 431KB 单行代码，
 * 以可读、可审计的方式完成同样的引导职责。
 * 原混淆层中的授权校验与数据上报机制已全部移除，系统启动与运行
 * 不依赖任何授权验证，也不向任何外部地址发送数据。
 *
 * 引导顺序：
 *   1. 路径常量     SYSTEM_ROOT / ROOT / TEMPLATE_ROOT / PLUGIN_ROOT
 *   2. 运行时设置   时区、错误级别、超时
 *   3. 会话         Session 参数与启动
 *   4. 自动加载     Autoloader
 *   5. 数据库       读取 config.php，实例化 PdoHelper
 *   6. 配置缓存     $conf
 *   7. 版本常量     SYS_VERSION / DB_VERSION，密钥 SYS_KEY，表前缀 DBQZ
 *   8. 函数库       function.php / core.func.php
 *   9. 安装检查     install.lock、config.php、数据表可用性
 *  10. 安全防护     360safe WAF、txprotect
 *  11. 防 CC        Cookie 校验（$conf['parapet']）
 *  12. 身份识别     member.php（$islogin/$islogin2/$islogin3）
 *  13. 分站识别     $domain / $is_fenzhan / $siterow
 */

// ---------------------------------------------------------------- 1. 路径常量
define('IN_CRONLITE', true);
define('CACHE_FILE', 0);
define('SYSTEM_ROOT', __DIR__ . '/');
define('ROOT', dirname(SYSTEM_ROOT) . '/');
define('TEMPLATE_ROOT', ROOT . 'template/');
define('PLUGIN_ROOT', ROOT . 'includes/plugins/');

// ---------------------------------------------------------------- 2. 运行时设置
date_default_timezone_set('Asia/Shanghai');
$date = date('Y-m-d H:i:s');

if (function_exists('set_time_limit')) {
    @set_time_limit(0);
}
if (function_exists('ignore_user_abort')) {
    @ignore_user_abort(true);
}

// 注意（运维约束）：admin/head.php 受 admin/login.php（混淆层）的文件大小校验保护，
// 该文件字节数必须保持 22789，否则后台登录页会输出空内容、整个后台无法登录。
// 对 head.php 的任何改动都必须以「等长替换」方式进行。详见 head.php 头部注释。
// 另：实测在本环境中关闭 PCRE JIT（pcre.jit=0）反而会让 head.php 的菜单过滤正则
// 更早返回 NULL 造成白屏，故不要在此处关闭 JIT。

// ---------------------------------------------------------------- 3. 会话
@ini_set('session.use_cookies', '1');
@ini_set('session.use_only_cookies', '1');
@ini_set('session.cookie_httponly', '1');
@ini_set('session.gc_maxlifetime', '7200');
@ini_set('session.cookie_lifetime', '0');

// ---------- 安全 Cookie 属性（修复 #6） ----------
// SameSite=Lax 防御 CSRF；Secure 在 HTTPS 下自动启用
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    || (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https');
@ini_set('session.cookie_samesite', 'Lax');
if ($isHttps) {
    @ini_set('session.cookie_secure', '1');
}

// 部分接口（如 api.php）会先置 $nosession = true 表示不需要会话
if (empty($nosession) && session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// ---------- 安全响应头（修复 #6） ----------
// 仅对浏览器请求发送，CLI/接口不受影响
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // 轻运保护（1 年）
    header('Strict-Transport-Security: max-age=31536000');
    // CSP：允许内联脚本/样式（系统大量使用）+ 本源 + CDN 已本地化
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: blob: https:; font-src 'self' data:; connect-src 'self'; frame-ancestors 'self'");
}

// ---------------------------------------------------------------- 4. 自动加载
require_once SYSTEM_ROOT . 'autoloader.php';
if (class_exists('Autoloader')) {
    Autoloader::register();
}

// ---------------------------------------------------------------- 5. 数据库
if (!is_file(ROOT . 'config.php')) {
    \lib\Bootstrap::renderInstallPage(
        '系统未安装',
        '未找到 config.php 配置文件。',
        '请访问 <code>/install/</code> 完成安装，或在服务器根目录放置 config.php 后再访问。'
    );
}

require ROOT . 'config.php';

if (!isset($dbconfig) || !is_array($dbconfig) || empty($dbconfig['dbname'])) {
    \lib\Bootstrap::renderInstallPage(
        '配置无效',
        'config.php 中的数据库配置不完整。',
        '请直接修改根目录下的 <code>config.php</code>，填写正确的数据库主机、账号、密码、库名与表前缀。'
    );
}

define('DBQZ', $dbconfig['dbqz'] . '_');

try {
    $DB = new \lib\PdoHelper($dbconfig);
} catch (\Exception $e) {
    \lib\Bootstrap::renderInstallPage(
        '数据库连接失败',
        '无法连接到数据库：' . $e->getMessage(),
        '请检查 MySQL 服务是否正常运行，以及 config.php 中的账号密码是否正确。'
    );
}

// ---------------------------------------------------------------- 6. 配置缓存
$CACHE = new \lib\Cache();
$conf  = $CACHE->pre_fetch();
if (empty($conf) || !is_array($conf)) {
    $conf = $CACHE->update();
}
if (!is_array($conf)) {
    $conf = [];
}

// ---------------------------------------------------------------- 7. 版本与密钥
require_once SYSTEM_ROOT . 'version.php';       // SYS_VERSION / DB_VERSION
if (!defined('SYS_KEY')) {
    define('SYS_KEY', isset($conf['syskey']) ? (string)$conf['syskey'] : '');
}
if (!defined('VERSION')) {
    define('VERSION', defined('SYS_VERSION') ? SYS_VERSION : '');
}

// 会话有效期（依赖配置，故在此补充）
if (!empty($conf['session_lifetime']) && (int)$conf['session_lifetime'] > 0) {
    @ini_set('session.gc_maxlifetime', (string)((int)$conf['session_lifetime'] * 60));
}

// ---------------------------------------------------------------- 8. 函数库
require_once SYSTEM_ROOT . 'function.php';
if (is_file(SYSTEM_ROOT . 'core.func.php')) {
    require_once SYSTEM_ROOT . 'core.func.php';
}
if (is_file(SYSTEM_ROOT . 'ajax.func.php')) {
    require_once SYSTEM_ROOT . 'ajax.func.php';
}
if (is_file(SYSTEM_ROOT . 'compat.php')) {
    require_once SYSTEM_ROOT . 'compat.php';
}

// member.php 需要的默认密码盐
if (!isset($password_hash)) {
    $password_hash = '!@#%!s!0';
}

// ---------------------------------------------------------------- 9. 安装检查
\lib\Bootstrap::assertInstalled($DB, $conf);

// ---------------------------------------------------------------- 10. 安全防护
if (empty($nosecu)) {
    $nosecu = false;
}
if (!$nosecu) {
    if (is_file(SYSTEM_ROOT . '360safe/360webscan.php')) {
        require_once SYSTEM_ROOT . '360safe/360webscan.php';
    }
    if (is_file(SYSTEM_ROOT . '360safe/xss.php')) {
        require_once SYSTEM_ROOT . '360safe/xss.php';
    }
    if (is_file(SYSTEM_ROOT . 'txprotect.php')) {
        require SYSTEM_ROOT . 'txprotect.php';
    }
}

// ---------------------------------------------------------------- 10b. 登录失败锁定（修复 #5）
// 原 login.php（混淆）无失败次数限制，可被自动化暴力破解。
// 此处在引导层拦截：POST 到 login.php 时先检查 IP 是否已锁定，
// 并通过 register_shutdown_function 在请求结束后判定登录是否成功。
if (PHP_SAPI !== 'cli' && !empty($DB)) {
    // 自动建表
    try {
        $DB->exec("CREATE TABLE IF NOT EXISTS `" . DBQZ . "login_log` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `ip` VARCHAR(45) NOT NULL,
            `username` VARCHAR(100) DEFAULT NULL,
            `success` TINYINT(1) NOT NULL DEFAULT 0,
            `addtime` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_ip` (`ip`),
            INDEX `idx_addtime` (`addtime`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (\Exception $e) {
        // 建表失败不阻断主流程
    }

    $hwCurScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
    $hwIsLoginPost = ($hwCurScript === 'login.php'
        && $_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['user'], $_POST['pass']));

    if ($hwIsLoginPost) {
        // 获取客户端 IP（real_ip 在 section 12 才定义，这里内联兜底）
        $hwIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $hwParts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $hwIp = trim($hwParts[0]);
        }
        $hwIp = substr($hwIp, 0, 45);

        // 检查最近 15 分钟内的失败次数
        $hwLockWindow  = 15 * 60;    // 锁定窗口 15 分钟
        $hwMaxAttempts = 5;          // 最大失败次数
        $hwLockTime    = 30 * 60;    // 超限后锁定 30 分钟

        try {
            $hwRecent = $DB->getRow(
                "SELECT COUNT(*) AS cnt, MAX(addtime) AS last_time
                 FROM `" . DBQZ . "login_log`
                 WHERE ip = ? AND success = 0 AND addtime > ?",
                [$hwIp, date('Y-m-d H:i:s', time() - $hwLockTime)]
            );
            $hwFailCount = $hwRecent ? (int)$hwRecent['cnt'] : 0;

            if ($hwFailCount >= $hwMaxAttempts) {
                // 已锁定，计算剩余时间
                $hwLastTime  = $hwRecent ? strtotime($hwRecent['last_time']) : time();
                $hwRemain    = max(0, $hwLockTime - (time() - $hwLastTime));
                $hwRemainMin = ceil($hwRemain / 60);

                if (!headers_sent()) {
                    header('HTTP/1.1 429 Too Many Requests');
                    header('Content-Type: text/html; charset=UTF-8');
                }
                echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>登录锁定</title>'
                   . '<style>body{font-family:system-ui,sans-serif;display:flex;align-items:center;'
                   . 'justify-content:center;min-height:100vh;margin:0;background:#f1f5f9}'
                   . '.box{background:#fff;padding:40px 48px;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.08);'
                   . 'text-align:center;max-width:420px}'
                   . '.icon{font-size:48px;color:#ef4444;margin-bottom:16px}'
                   . 'h2{color:#1e293b;margin:0 0 12px}p{color:#64748b;line-height:1.6;margin:8px 0}'
                   . '.btn{display:inline-block;margin-top:20px;padding:10px 28px;border-radius:8px;'
                   . 'background:#6366f1;color:#fff;text-decoration:none;font-weight:500}</style></head><body>'
                   . '<div class="box"><div class="icon">&#128274;</div>'
                   . '<h2>登录已被临时锁定</h2>'
                   . '<p>由于连续登录失败次数过多，您的 IP 地址已被临时限制登录。</p>'
                   . '<p>剩余锁定时间：<strong style="color:#ef4444">' . $hwRemainMin . ' 分钟</strong></p>'
                   . '<p style="font-size:13px;color:#94a3b8">如非本人操作，请稍后再试或联系系统管理员。</p>'
                   . '<a class="btn" href="./login.php">返回登录页</a></div></body></html>';
                exit;
            }
        } catch (\Exception $e) {
            // 查询失败不阻断登录流程
        }

        // 注册关闭函数：请求结束后判定登录结果
        register_shutdown_function(function () use ($DB, $hwIp) {
            $hwSuccess = isset($_SESSION['admin_token']) && !empty($_SESSION['admin_token']);
            try {
                $DB->exec(
                    "INSERT INTO `" . DBQZ . "login_log` (`ip`,`username`,`success`,`addtime`) VALUES (?,?,?,NOW())",
                    [
                        $hwIp,
                        substr($_POST['user'] ?? '', 0, 100),
                        $hwSuccess ? 1 : 0,
                    ]
                );
                // 登录成功时清除该 IP 的失败记录
                if ($hwSuccess) {
                    $DB->exec("DELETE FROM `" . DBQZ . "login_log` WHERE ip = ?", [$hwIp]);
                }
            } catch (\Exception $e) {
                // 记录失败不影响主流程
            }
        });
    }
}

// ---------------------------------------------------------------- 10c. 登录页 HTML 过滤（修复 #9 #10）
// login.php（混淆）输出的找回密码弹窗暴露 phpMyAdmin 路径与 SQL 语句，
// 且扫码登录 Tab 默认未启用却显示在界面。通过输出缓冲在 HTML 层修正。
if (PHP_SAPI !== 'cli' && $hwCurScript === 'login.php') {
    if (!function_exists('hw_login_filter')) {
        function hw_login_filter($html) {
            if (!is_string($html) || $html === '') {
                return $html;
            }
            @ini_set('pcre.backtrack_limit', '100000000');

            // ---------- #9: 清理找回密码弹窗中的敏感运维指引 ----------
            // 替换包含 phpMyAdmin / pre_config / UPDATE pre_config 等敏感内容的文本块
            if (stripos($html, 'phpMyAdmin') !== false || stripos($html, 'pre_config') !== false) {
                // 匹配包含 phpMyAdmin 或 pre_config 的模态框/弹窗内容块
                // 策略：将包含敏感关键词的 <div> / <p> / <span> 文本替换为安全提示
                $html = preg_replace(
                    '~<div[^>]*>(?:(?!</div>).)*?(?:phpMyAdmin|pre_config|UPDATE\s+pre_config)(?:(?!</div>).)*?</div>~is',
                    '<div style="padding:16px;background:#f0f9ff;border-radius:8px;color:#0369a1;font-size:14px;line-height:1.6">'
                    . '<strong>忘记密码？</strong><br>'
                    . '请联系系统管理员通过安全渠道重置后台密码。<br>'
                    . '如您是系统管理员，请查阅系统部署文档中的密码重置说明。</div>',
                    $html
                );
                // 清理可能残留的 SQL 语句文本节点
                $html = preg_replace('~UPDATE\s+`?pre_config`?\s+SET[^<;\n]*~i', '', $html);
                $html = preg_replace('~UPDATE\s+`?pre_cache`?\s+SET[^<;\n]*~i', '', $html);
            }

            // ---------- #10: 隐藏未启用的扫码快捷登录 Tab ----------
            // 移除 qr_login Tab 及其对应的面板内容
            if (stripos($html, 'qr_login') !== false || stripos($html, '扫码') !== false) {
                // 移除包含"扫码"文字的 Tab 项（<li> 或 <a>）
                $html = preg_replace(
                    '~<li[^>]*>(?:(?!</li>).)*?扫码(?:快捷)?(?:登录)?(?:(?!</li>).)*?</li>~is',
                    '',
                    $html
                );
                // 移除 qr_login 相关的 id/class 容器
                $html = preg_replace(
                    '~<div[^>]*id=["\']qr_login["\'][^>]*>.*?</div>~is',
                    '',
                    $html
                );
                $html = preg_replace(
                    '~<div[^>]*class=["\'][^"\']*qr_login[^"\']*["\'][^>]*>.*?</div>~is',
                    '',
                    $html
                );
                // 移除包含"若无法直接拉起"提示的文本块
                $html = preg_replace(
                    '~<[^>]+>(?:(?!</).)*?若无法直接拉起(?:(?!</).)*?</[^>]+>~is',
                    '',
                    $html
                );
            }

            return $html;
        }
    }
    ob_start('hw_login_filter');
}

// ---------------------------------------------------------------- 10d. 破坏性操作审计日志（修复 #11）
// 记录所有删除/清空类操作，提供事后追溯能力。
// 前端已注入二次确认模态（head.php），此处补充服务端审计。
if (PHP_SAPI !== 'cli' && !empty($DB) && basename($_SERVER['SCRIPT_NAME'] ?? '') !== 'login.php') {
    try {
        $DB->exec("CREATE TABLE IF NOT EXISTS `" . DBQZ . "audit_log` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `actor` VARCHAR(100) DEFAULT NULL,
            `ip` VARCHAR(45) DEFAULT NULL,
            `script` VARCHAR(200) DEFAULT NULL,
            `action` VARCHAR(50) DEFAULT NULL,
            `target` VARCHAR(200) DEFAULT NULL,
            `method` VARCHAR(10) DEFAULT NULL,
            `addtime` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `idx_addtime` (`addtime`),
            INDEX `idx_actor` (`actor`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    } catch (\Exception $e) {}

    // 检测是否为破坏性操作
    $hwAuditAction = null;
    $hwAuditTarget = null;
    $hwDangerKeys  = ['action', 'act', 'do', 'op', 'type'];
    $hwDangerVals  = ['delete', 'del', 'remove', 'clear', 'truncate', 'drop', 'wipe', 'purge', '抹除', '清空', '删除'];

    foreach ($hwDangerKeys as $hk) {
        $hv = $_GET[$hk] ?? $_POST[$hk] ?? null;
        if ($hv !== null) {
            $hvLower = strtolower(trim($hv));
            foreach ($hwDangerVals as $dv) {
                if ($hvLower === $dv || stripos($hv, $dv) !== false) {
                    $hwAuditAction = $hv;
                    $hwAuditTarget = $_GET['id'] ?? $_POST['id'] ?? $_GET['cid'] ?? $_POST['cid'] ?? $_GET['zid'] ?? $_POST['zid'] ?? '';
                    break 2;
                }
            }
        }
    }

    if ($hwAuditAction !== null) {
        try {
            $hwActor = $_SESSION['admin_token'] ?? ($_SESSION['user'] ?? 'guest');
            $hwAuditIp = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $hwParts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $hwAuditIp = trim($hwParts[0]);
            }
            $DB->exec(
                "INSERT INTO `" . DBQZ . "audit_log` (`actor`,`ip`,`script`,`action`,`target`,`method`,`addtime`) VALUES (?,?,?,?,?,?,NOW())",
                [
                    substr((string)$hwActor, 0, 100),
                    substr($hwAuditIp, 0, 45),
                    substr(basename($_SERVER['SCRIPT_NAME'] ?? ''), 0, 200),
                    substr((string)$hwAuditAction, 0, 50),
                    substr((string)$hwAuditTarget, 0, 200),
                    $_SERVER['REQUEST_METHOD'] ?? 'GET',
                ]
            );
        } catch (\Exception $e) {}
    }
}

// ---------------------------------------------------------------- 11. URL / UA 规范化
$scriptpath = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
$sitepath   = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$siteurl    = (function_exists('is_https') && is_https() ? 'https://' : 'http://')
    . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $sitepath . '/';

if (!isset($_SERVER['HTTP_USER_AGENT']) || strpos($_SERVER['HTTP_USER_AGENT'], chr(46)) === false) {
    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Windows NT 10.0) Safari/537.36';
}

// 防 CC（Cookie 校验），对应原混淆实现中的 parapet 模块
if (!empty($conf['parapet']) && $conf['parapet'] == 1 && empty($_SESSION['parapet_ok'])) {
    \lib\Bootstrap::renderCookieCheckPage();
}

// ---------------------------------------------------------------- 12. 身份识别
$islogin  = 0;
$islogin2 = 0;
$islogin3 = 0;
$userrow  = null;
$siterow  = null;
$suprow   = null;
$clientip = real_ip(isset($conf['ip_type']) ? $conf['ip_type'] : 0);

if (is_file(SYSTEM_ROOT . 'member.php')) {
    require SYSTEM_ROOT . 'member.php';
}

// ---------------------------------------------------------------- 13. 分站识别
$domain     = preg_replace('/^www\./', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
$is_fenzhan = false;

// 落地域名跳转（配置于后台「域名落地页配置」）
\lib\Bootstrap::applyDomainLanding($DB, $domain);

if ($domain !== '' && isset($DB) && $DB) {
    $row = $DB->getRow(
        "SELECT * FROM pre_site WHERE domain=:domain OR domain2=:domain LIMIT 1",
        [':domain' => $domain]
    );
    if ($row) {
        $is_fenzhan = true;
        $siterow    = $row;
        if (empty($conf['zid'])) {
            $conf['zid'] = $row['zid'];
        }
    }
}

// 分站模板
if ($is_fenzhan && !empty($siterow['template'])) {
    $conf['template'] = $siterow['template'];
}

// 静态资源根路径（已完全本地化）
// 原实现按 $conf['cdnpublic'] 在 lib.baomitu.com / cdn.bootcdn.net / s1.pstatp.com
// 三个公共 CDN 之间切换，全部会向外部域名发请求。现改为项目内镜像目录
// assets/vendor/lib.baomitu.com/，所有由该变量拼接出的路径都指向本地。
// 若系统部署在子目录（如 http://host/shop/），只需把此常量改为 '/shop/assets/vendor/lib.baomitu.com/'。
if (empty($cdnpublic)) {
    $cdnpublic = '/assets/vendor/lib.baomitu.com/';
}

// ---------------------------------------------------------------- 14. 云端客户端（已彻底移除）
// 原官方上报 / 授权校验客户端（lib\License / lib\LicenseClient / lib\NullApiClient）
// 已全部删除，后台不再持有任何对外通信的客户端实例。

// ---------------------------------------------------------------- 15. CSRF 防护
// 为后台 POST 请求提供 CSRF Token 校验，防止跨站请求伪造。
// Token 存储在 SESSION 中，前端通过 JS 自动注入到表单和 AJAX 请求。
if (session_status() === PHP_SESSION_ACTIVE) {
    // 生成 Token（仅当 SESSION 中不存在时）
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrf_token = $_SESSION['csrf_token'];

    // 判定是否为后台 POST 请求
    $_script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    $_is_admin_post = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
        && stripos($_script_name, '/admin/') !== false;

    // 排除登录页（登录时尚无有效 session token 需要提交）
    $_csrf_skip = stripos(basename($_script_name), 'login') !== false;

    if ($_is_admin_post && !$_csrf_skip && !empty($islogin)) {
        $_submitted = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!hash_equals($csrf_token, (string)$_submitted)) {
            // AJAX 请求返回 JSON，普通表单返回 HTML 提示
            $_is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            if ($_is_ajax) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                exit(json_encode(['code' => -1, 'msg' => 'CSRF Token 校验失败，请刷新页面后重试']));
            }
            http_response_code(403);
            exit('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>CSRF 校验失败</title></head>'
                . '<body style="font-family:sans-serif;text-align:center;padding:60px">'
                . '<h2 style="color:#ef4444">CSRF Token 校验失败</h2>'
                . '<p>检测到可能的跨站请求伪造攻击，请<a href="javascript:history.back()">返回</a>后重试。</p>'
                . '</body></html>');
        }
    }
}
