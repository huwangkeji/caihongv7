<?php

/**
 * TX 防护层（蜘蛛/异常请求拦截）
 *
 * 修复来源：后台体检报告_只读审计.md — 低危#7
 * 原代码直接访问 $_SERVER / $_SESSION 超全局数组的 HTTP_* 键，
 * 当请求未携带对应 Header 时产生 PHP Notice 噪音。
 * 现在统一在顶部做判空初始化，消除所有 Undefined index / Undefined variable 告警。
 */

// ---------- 超全局键判空初始化 ----------
if (!isset($nosecu)) {
    $nosecu = false;
}

// HTTP Header 键可能不存在（CLI、精简请求等场景），统一兜底为空字符串
$_server_ua     = $_SERVER['HTTP_USER_AGENT']     ?? '';
$_server_ref    = $_SERVER['HTTP_REFERER']        ?? '';
$_server_accept = $_SERVER['HTTP_ACCEPT']         ?? '';
$_server_lang   = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
$_server_conn   = $_SERVER['HTTP_CONNECTION']     ?? '';

if (!isset($_SESSION['txprotectblock'])) {
    $_SESSION['txprotectblock'] = false;
}
$_session_block = $_SESSION['txprotectblock'];

// ---------- 早退：全局跳过 ----------
if ($nosecu === true) {
    return;
}

// ---------- 蜘蛛白名单 ----------
if (strpos($_server_ua, 'Baiduspider') !== false
    || strpos($_server_ua, '360Spider') !== false
    || strpos($_server_ua, 'YisouSpider') !== false
    || strpos($_server_ua, 'Sogou web spider') !== false
    || strpos($_server_ua, 'Sogou inst spider') !== false
    || strpos($_server_ua, 'Googlebot/') !== false
    || strpos($_server_ua, 'bingbot/') !== false
    || strpos($_server_ua, 'Bytespider') !== false
) {
    return;
}

// ---------- Referer 黑名单 → 标记拦截 ----------
if (strpos($_server_ref, '.tr.com') !== false
    || strpos($_server_ref, '.wsd.com') !== false
    || strpos($_server_ref, '.oa.com') !== false
    || strpos($_server_ref, '.cm.com') !== false
    || strpos($_server_ref, '/membercomprehensive/') !== false
    || strpos($_server_ref, 'www.internalrequests.org') !== false
) {
    $_SESSION['txprotectblock'] = true;
    $_session_block = true;
}

// ---------- HEADER 特征屏蔽 ----------
if (!isset($_SERVER['HTTP_ACCEPT'])
    || $_server_ua === ''
    || strpos(strtolower($_server_ua), 'manager') !== false
    || strpos($_server_ua, 'ozilla') !== false && strpos($_server_ua, 'Mozilla') === false
    || strpos($_server_ua, 'Windows NT 6.1') !== false && $_server_accept === '*/*'
    || strpos($_server_ua, 'Windows NT 5.1') !== false && $_server_accept === '*/*'
    || strpos($_server_accept, 'vnd.wap.wml') !== false && strpos($_server_ua, 'Windows NT 5.1') !== false
    || isset($_COOKIE['ASPSESSIONIDQASBQDRC'])
    || strpos($_server_ua, 'Alibaba.Security.Heimdall') !== false
    || strpos($_server_ua, 'wechatdevtools/') !== false
    || strpos($_server_ua, 'libcurl/') !== false
    || strpos($_server_ua, 'python') !== false
    || strpos($_server_ua, 'Go-http-client') !== false
    || strpos($_server_ua, 'HeadlessChrome') !== false
    || $_session_block === true
) {
    exit('Click to continue!' . ($date ?? ''));
}

// ---------- UA / Accept 组合特征屏蔽 ----------
if (strpos($_server_ua, 'Coolpad Y82-520') !== false && $_server_accept === '*/*'
    || strpos($_server_ua, 'Mac OS X 10_12_4') !== false && $_server_accept === '*/*'
    || strpos($_server_ua, 'iPhone OS') !== false && strpos($_server_ua, 'baiduboxapp/') === false && $_server_accept === '*/*'
    || strpos($_server_ua, 'Android') !== false && $_server_accept === '*/*'
    || strpos($_server_lang, 'en') !== false && strpos($_server_lang, 'zh') === false
    || strpos($_server_ua, 'iPhone') !== false && strpos($_server_ua, 'en-') !== false && strpos($_server_ua, 'zh') === false
    || strpos($_server_ua, 'iPhone OS 9_1') !== false && $_server_conn === 'close'
) {
    exit('您当前浏览器不支持或操作系统语言设置非中文，无法访问本站！');
}
