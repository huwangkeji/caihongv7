<?php

/**
 * 后台首页（可读实现）
 *
 * 替代原 eval / base64 / rot13 / hex / goto 多层混淆版本
 * （原始混淆文件已隔离备份至
 *  .workbuddy/quarantine/encrypted_originals_20261015/admin_index_core.php.obfuscated）。
 *
 * 本文件职责：
 *   1. 引入公共引导（DB / 配置 / 鉴权）；
 *   2. 未登录跳转到登录页；
 *   3. 引入后台公共头部（侧边栏 + 全局保存 / CSRF / layer 皮肤注入）；
 *   4. 输出仪表盘主体（admin/index_body.php）。
 *
 * 仪表盘数据（统计卡片、图表、访问详情）全部通过 AJAX 填充：
 *   - 统计数字：ajax.php?act=getcount
 *   - 访问详情：对应 ajax 动作
 * 本文件不再包含任何云控横幅、fetchGlobalStats 或授权客户端逻辑，
 * 也不向任何外部地址发起请求。
 */

require __DIR__ . '/../includes/common.php';

if ($islogin != 1) {
    header('Location: ./login.php');
    exit;
}

// 页面标题：由 head.php（混淆层 head_core.php）在 <title> 中回显。
// 原混淆版通过等价赋值硬编码该文案，此处显式声明以保持一致。
$title = '虎王云自助下单系统管理中心';

include __DIR__ . '/head.php';
include __DIR__ . '/index_body.php';
