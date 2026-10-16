<?php

/**
 * 引导层辅助类
 *
 * 承载安装检查、错误提示页、防 CC Cookie 校验等引导期行为，
 * 对应原混淆实现中的安装检查模块与 parapet 模块。
 *
 * @package lib
 */

namespace lib;

class Bootstrap
{
    /**
     * 安装状态检查
     *
     * 依次校验：
     *   1. install/install.lock 是否存在（不存在说明未安装）
     *   2. 核心数据表是否可用
     * 任一不满足则渲染安装引导页并终止请求。
     *
     * @param \lib\PdoHelper $DB
     * @param array          $conf
     */
    public static function assertInstalled($DB = null, array $conf = [])
    {
        $lockFile = ROOT . 'install/install.lock';

        if (!is_file($lockFile)) {
            self::renderInstallPage(
                '系统尚未安装',
                '未检测到 install/install.lock 文件，系统判定为未安装状态。',
                '首次使用请访问 <code>/install/</code> 进入安装向导；'
                . '若已安装过，请在服务器 <code>/install</code> 目录下新建名为 <code>install.lock</code> 的空白文件。'
                . '<br><span style="color:#dc2626;font-weight:700;">'
                . '在完成此操作前，任何访客都可访问安装页并重装站点，将导致数据被清空。</span>'
            );
        }

        // 数据表可用性
        if ($DB) {
            $exists = $DB->getColumn("SHOW TABLES LIKE 'pre_config'");
            if (!$exists) {
                self::renderInstallPage(
                    '数据表缺失',
                    '未找到核心数据表 pre_config，数据库可能未正确安装。',
                    '请检查数据库是否已正确安装；如为覆盖升级，请确认 config.php 中的库名与表前缀无误，'
                    . '<b>不要重新安装</b>（重装会清空现有数据）。'
                );
            }
        }
    }

    /**
     * 落地域名跳转
     *
     * 依据 pre_domain_landing 表配置的 old_domain → new_domain 映射，
     * 在引导阶段把访问旧域名的请求 301 跳转到新域名。
     * 该功能原由混淆层实现，此处以干净代码重新实现，行为保持一致：
     *   - 精确匹配：old_domain 与当前域名完全一致
     *   - 通配匹配：old_domain 以 "*." 开头时匹配其所有子域
     *   - 跳转时保留协议与原始 URI，使用 301 永久重定向
     *   - 表不存在或查询失败时静默跳过，绝不因辅助功能阻断站点
     *
     * @param \lib\PdoHelper $DB
     * @param string         $currentHost 当前访问域名（不含端口）
     *
     * @return bool 是否发生了跳转（跳转后脚本已终止）
     */
    public static function applyDomainLanding($DB, $currentHost)
    {
        if (!$DB || $currentHost === '') {
            return false;
        }

        $host = strtolower(preg_replace('/:\d+$/', '', trim($currentHost)));
        if ($host === '' || $host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
            return false;
        }

        try {
            $rules = $DB->getAll("SELECT * FROM pre_domain_landing");
        } catch (\Exception $e) {
            // 表不存在或查询异常：静默跳过
            return false;
        }

        if (empty($rules) || !is_array($rules)) {
            return false;
        }

        $target = null;
        foreach ($rules as $rule) {
            $old = isset($rule['old_domain']) ? strtolower(trim($rule['old_domain'])) : '';
            $new = isset($rule['new_domain']) ? strtolower(trim($rule['new_domain'])) : '';
            if ($old === '' || $new === '') {
                continue;
            }
            $old = preg_replace('/^https?:\/\//', '', $old);
            $new = preg_replace('/^https?:\/\//', '', $new);
            if ($old === '' || $new === '' || $old === $new) {
                continue;
            }

            if ($old === $host) {
                $target = $new;
                break;
            }

            // 通配：*.example.com
            if (strpos($old, '*.') === 0) {
                $base = substr($old, 2);
                if ($base !== '' && preg_match('/^([a-z0-9-]+\.)*' . preg_quote($base, '/') . '$/', $host)) {
                    $target = $new;
                    break;
                }
            }
        }

        if ($target === null) {
            return false;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');

        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

        header('Location: ' . ($isHttps ? 'https://' : 'http://') . $target . $uri, true, 301);
        exit;
    }

    /**
     * 渲染安装/错误引导页并终止请求
     *
     * @param string $title
     * @param string $reason
     * @param string $solution
     */
    public static function renderInstallPage($title, $reason, $solution)
    {
        header('Content-Type: text/html; charset=UTF-8');
        http_response_code(503);
        echo '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title></head>'
            . '<body style="margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;'
            . 'background:#f8fafc;font-family:system-ui,-apple-system,\'Microsoft YaHei\',sans-serif;padding:24px;">'
            . '<div style="max-width:560px;padding:32px;background:#fff;border-radius:16px;'
            . 'box-shadow:0 4px 24px rgba(15,23,42,.08);">'
            . '<div style="font-size:20px;font-weight:700;color:#0f172a;margin-bottom:16px;">'
            . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div style="font-size:14px;color:#b91c1c;line-height:1.7;margin-bottom:16px;">'
            . htmlspecialchars($reason, ENT_QUOTES, 'UTF-8') . '</div>'
            . '<div style="font-size:14px;color:#475569;line-height:1.7;">' . $solution . '</div>'
            . '<div style="margin-top:20px;"><a href="/install/" style="display:inline-block;padding:10px 20px;'
            . 'background:#3b82f6;color:#fff;border-radius:10px;text-decoration:none;font-weight:700;">'
            . '前往安装向导</a></div>'
            . '</div></body></html>';
        exit;
    }

    /**
     * 防 CC：Cookie 校验页
     *
     * 首次访问时下发一个 JS 页面，由浏览器设置 Cookie 后自动刷新；
     * 若刷新后仍无 Cookie，则提示浏览器不支持或被拦截。
     * 用于拦截不具备 Cookie 能力的低成本扫描器/CC 工具。
     */
    public static function renderCookieCheckPage()
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-Type: text/html; charset=UTF-8');

        // 已携带任意 Cookie 即视为通过
        if (!empty($_COOKIE)) {
            $_SESSION['parapet_ok'] = 1;
            return;
        }

        echo '<!DOCTYPE html><html lang="zh-CN"><head>'
            . '<meta http-equiv="pragma" content="no-cache">'
            . '<meta http-equiv="cache-control" content="no-cache">'
            . '<meta http-equiv="content-type" content="text/html;charset=utf-8">'
            . '<title>正在加载中</title>'
            . '<script>'
            . 'function setCookie(n,v){var e=new Date();e.setTime(e.getTime()+60*60*1000);'
            . 'document.cookie=n+"="+escape(v).replace(/\+/g,"%2B")+";expires="+e.toGMTString()+";path=/";}'
            . 'function getCookie(n){var a,r=new RegExp("(^| )"+n+"=([^;]*)(;|$)");'
            . 'if(a=document.cookie.match(r))return unescape(a[2]);return null;}'
            . 'var t=getCookie("sec_defend_time")||0;t++;'
            . 'setCookie("sec_defend",t);setCookie("sec_defend_time",t);'
            . 'if(t>1){window.location.href="./index.php";}else{window.location.reload();}'
            . '</script></head><body>'
            . '<div style="font-family:system-ui,\'Microsoft YaHei\',sans-serif;text-align:center;'
            . 'padding:60px 20px;color:#475569;">正在加载中，请稍候…</div>'
            . '</body></html>';
        exit;
    }
}
