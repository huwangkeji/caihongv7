<?php

/**
 * 系统公共引导入口
 *
 * 【变更说明】
 * 本文件原先是一段 431KB 的单行混淆代码（含 base64/rot13/strrev 多层嵌套、
 * 17 处 eval、西里尔字母变量别名表），承载引导 + 本地授权验证 + 防盗版逻辑。
 * 因其不可审计、且存在关闭 SSL 证书校验等隐患，现替换为干净实现：
 *
 *   includes/bootstrap.php          干净可读的引导流程（14 个阶段）
 *   includes/lib/Bootstrap.php      安装检查 / 错误页 / 域名落地跳转
 *   includes/compat.php             遗留函数兼容层
 *
 * 原混淆文件已完整备份至（MD5 已校验一致）：
 *   .workbuddy/quarantine/includes_common.php.orig
 *   .workbuddy/quarantine/includes_ApiClient.php.orig
 *   .workbuddy/quarantine/includes_ApiAction.php.orig
 *
 * 授权校验与数据上报机制已全部移除（2026-10-13）：
 *   - lib\License / lib\LicenseClient / lib\NullApiClient / admin/license.php 已彻底删除
 *   - pre_config 中的 auth_code 配置项已清除
 *   系统启动与运行不再依赖任何授权验证，也不向任何外部地址发送数据。
 *
 * 对外暴露的全局变量保持不变，引用本文件的页面无需改动：
 *   $DB $CACHE $conf $date $siteurl $scriptpath $sitepath
 *   $islogin $islogin2 $islogin3 $userrow $siterow $suprow
 *   $domain $is_fenzhan $clientip $password_hash $cdnpublic
 */

require_once __DIR__ . '/bootstrap.php';
