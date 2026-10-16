<?php

/**
 * 后台首页（壳层）
 *
 * 实际渲染逻辑位于 index_core.php。本壳层在渲染完成后对最终 HTML 做处理：
 *   1. 移除站点统计横幅及其配套 JS，页面不再展示站点统计模块；
 *   2. 将残留的 staticfile.net 外部 CDN 脚本引用替换为本地镜像；
 *   3. 校验 PHP 语法（__hwcount 兼容层位于 index_core.php 头部）。
 */

if (!function_exists('hw_index_filter')) {

    function hw_index_filter($html)
    {
        if (!is_string($html) || $html === '') {
            return $html;
        }
        @ini_set('pcre.backtrack_limit', '100000000');

        // ---------- 1. 移除站点统计横幅（含其专属 <style>） ----------
        // 横幅特征：class 含 from-slate-900 的渐变深色大块；从其专属 <style> 起点删到 div 平衡闭合
        $pos = strpos($html, 'marquee-horizontal');
        if ($pos !== false) {
            // 找包含该 style 块的 <style> 起点
            $styleStart = strrpos(substr($html, 0, $pos), '<style');
            if ($styleStart !== false) {
                $styleEnd = strpos($html, '</style>', $pos);
                // 在横幅 div 闭合后再截断：先找 div 起点，做 div 平衡扫描
                $divStart = strpos($html, '<div', $styleEnd === false ? $styleStart : $styleEnd);
                if ($divStart !== false && strpos(substr($html, $divStart, 120), 'from-slate-900') !== false) {
                    $depth = 0;
                    $i = $divStart;
                    $len = strlen($html);
                    $end = -1;
                    while ($i < $len) {
                        $open = strpos($html, '<div', $i);
                        $close = strpos($html, '</div>', $i);
                        if ($close === false) break;
                        if ($open !== false && $open < $close) {
                            $depth++;
                            $i = $open + 4;
                        } else {
                            $depth--;
                            $i = $close + 6;
                            if ($depth === 0) { $end = $i; break; }
                        }
                    }
                    if ($end > 0) {
                        $html = substr($html, 0, $styleStart) . substr($html, $end);
                    }
                }
            }
        }

        // ---------- 2. 移除配套 JS（fetchGlobalStats 定义与初始化调用） ----------
        $html = preg_replace('~window\.fetchGlobalStats\s*=\s*function\s*\(.*?\n\s*\};~s', '', $html);
        $html = preg_replace("~[^;\n]*fetchGlobalStats\([^;]*?;~s", '', $html);
        $html = preg_replace('~//\s*全网统计数据模块逻辑~', '', $html);

        // ---------- 3. 外部 CDN 本地化 ----------
        $html = str_replace(
            'https://cdn.staticfile.net/flot/0.8.3/',
            '/assets/vendor/cdn.staticfile.net/flot/0.8.3/',
            $html
        );

        return $html;
    }
}

// ---------- 变量兜底 ----------
// index_core.php（混淆层）foreach($sec_msg as $row) 但未初始化为数组，
// 产生 "foreach Invalid argument" 告警，此处兜底为空数组。
$sec_msg = is_array($sec_msg) ? $sec_msg : [];
// 混淆层 echo $mysqlversion 但从未定义（原始代码既有缺陷），产生 PHP Notice。
$mysqlversion = '';

ob_start('hw_index_filter');
require __DIR__ . '/index_core.php';
