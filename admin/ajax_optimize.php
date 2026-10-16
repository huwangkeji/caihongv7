<?php

include("../includes/common.php");
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 2);
header('Content-Type: application/json; charset=utf-8');

// 关闭错误显示，防止PHP错误干扰JSON输出
ini_set('display_errors', 0);

// 登录状态由 common.php -> bootstrap.php -> member.php 统一设置
if ($islogin != 1) {
    exit(json_encode(['success' => false, 'message' => '未登录']));
}

$do = isset($_POST['do']) ? $_POST['do'] : null;

// 获取当前公告配置
$notice_settings = [
    'anounce' => '首页公告',
    'modal' => '首页弹出公告',
    'bottom' => '站点工具/友情链接',
    'alert' => '在线下单提示',
    'gg_search' => '订单查询页面公告',
    'gg_panel' => '分站后台公告',
    'gg_announce' => '所有分站显示首页公告',
    'footer' => '首页底部排版',
    'paymsg' => '支付方式选择页面提示',
    'appalert' => 'APP启动弹出内容'
];

// 应用优化内容
if ($do == 'apply') {
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $content = isset($_POST['content']) ? $_POST['content'] : '';

    if (empty($type) || empty($content)) {
        exit(json_encode(['success' => false, 'message' => '参数错误']));
    }

    // 记录日志
    error_log('[' . date('Y-m-d H:i:s') . '] AJAX应用优化内容: 类型=' . $type . ', 内容长度=' . strlen($content));

    try {
        // 保存到数据库
        $DB->exec("UPDATE `pre_config` SET `v`=:value WHERE `k`=:key", [':value' => $content, ':key' => $type]);

        // 更新配置缓存
        saveSetting($type, $content);

        // 清除缓存
        $CACHE->clear();

        // 记录成功
        error_log('[' . date('Y-m-d H:i:s') . '] AJAX应用成功: 类型=' . $type);

        exit(json_encode([
            'success' => true,
            'message' => '应用成功！内容已更新到' . (isset($notice_settings[$type]) ? $notice_settings[$type] : $type)
        ]));
    } catch (Exception $e) {
        // 记录异常
        error_log('[' . date('Y-m-d H:i:s') . '] AJAX应用异常: ' . $e->getMessage());
        exit(json_encode([
            'success' => false,
            'message' => '应用过程中发生异常：' . $e->getMessage()
        ]));
    }
} else {
    exit(json_encode(['success' => false, 'message' => '未知操作']));
}
