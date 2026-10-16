<?php

include("../includes/common.php");
$title = '祥云数据迁移';
include './head.php';
if ($islogin == 1) {
} else exit("<script language='javascript'>window.location.href='./login.php';</script>");
// 数据迁移会直接 DELETE 清空订单 / 商品 / 分站等业务表，必须限定为拥有"系统设置"
// 权限的账号（超级管理员不受此限制）
adminpermission('set', 1);
?>
<div class="w-full max-w-[1000px] mx-auto space-y-6">
    <?php
    $my = isset($_GET['my']) ? $_GET['my'] : null;
    if($my=='add'){
    ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-cloud-upload text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">祥云数据迁移选项</h3>
            </div>
            
            <div class="p-6">
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm flex items-start shadow-sm mb-6">
                    <i class="fa fa-exclamation-triangle mt-0.5 mr-2 shrink-0 text-lg"></i>
                    <span class="leading-relaxed font-bold">高危操作提示：勾选以下模块进行迁移后，系统将自动彻底清空当前对应模块的所有现有数据，请在执行前务必确认已做好备份！</span>
                </div>
                
                <form action="./datamove.php?my=add_submit" method="post" role="form" class="space-y-6">
                    <div class="space-y-3">
                        <label class="text-sm font-semibold text-slate-700">请选择需要从祥云系统强行合并过来的数据模块：</label>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 bg-slate-50/70 p-5 rounded-xl border border-slate-100">
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="order" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">订单列表</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="tools" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">商品列表</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="shequ" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">社区对接列表</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="price" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">加价模板</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="faka" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">发卡库存</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="site" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">分站/用户</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="workorder" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">工单管理</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="message" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">站内通知</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="article" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">文章管理</span>
                            </label>
                            
                            <label class="relative flex items-center p-3 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-colors group m-0">
                                <input type="checkbox" name="permission[]" value="record" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                <span class="ml-2.5 text-sm font-semibold text-slate-700 group-hover:text-brand-700">收支明细</span>
                            </label>

                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" name="submit" class="w-full h-12 rounded-xl bg-rose-500 text-white font-bold text-base hover:bg-rose-600 shadow-sm transition-all duration-200 active:scale-[0.98]" onclick="return confirm('二次确认：此操作极度危险，系统将根据您的勾选删除对应的现有数据，是否继续？');">
                            确定并执行迁移覆盖
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php
}elseif ($my == 'add_submit') {
        $permission = implode(',', $_POST['permission']);
        if (strpos($permission, 'order')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_order");
            $DB->exec("ALTER TABLE `shua_order` RENAME TO `{$dbconfig['dbqz']}_order`");
        }
        if (strpos($permission, 'tools')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_tools");
            $DB->exec("ALTER TABLE `shua_tools` RENAME TO `{$dbconfig['dbqz']}_tools`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `valiserv` VARCHAR(15) NULL AFTER `uptime`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `sales` INT(11) NULL AFTER `validate`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `stock` INT(11) NULL AFTER `sales`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `addtime` datetime NULL AFTER `stock`");
        }
        if (strpos($permission, 'shequ')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_shequ");
            $DB->exec("ALTER TABLE `shua_shequ` RENAME TO `{$dbconfig['dbqz']}_shequ`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_shequ` ADD `result` tinyint(1) NULL AFTER `type`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `remark` VARCHAR(255) NULL AFTER `status`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `protocol` tinyint(1) NULL AFTER `remark`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_tools` ADD `monitor` tinyint(1) NULL AFTER `protocol`");
        }
        if (strpos($permission, 'price')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_price");
            $DB->exec("ALTER TABLE `shua_price` RENAME TO `{$dbconfig['dbqz']}_price`");
        }
        if (strpos($permission, 'faka')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_faka");
            $DB->exec("ALTER TABLE `shua_faka` RENAME TO `{$dbconfig['dbqz']}_faka`");
        }
        if (strpos($permission, 'site')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_site");
            $DB->exec("ALTER TABLE `shua_site` RENAME TO `{$dbconfig['dbqz']}_site`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `email` tinyint(1) NULL AFTER `pwd`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `phone` VARCHAR(255) NULL AFTER `email`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `qq_openid` varchar(64) NULL AFTER `phone`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `wx_openid` varchar(64) NULL AFTER `qq_openid`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `nickname` varchar(64) NULL AFTER `wx_openid`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `faceimg` varchar(150) NULL AFTER `nickname`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `rmbtc` decimal(10,2) NULL AFTER `rmb`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `kfqq` varchar(12) NULL AFTER `description`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `kfwx` varchar(20) NULL AFTER `kfqq`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_site` ADD `appurl` varchar(150) NULL AFTER `iprice`");
        }
        if (strpos($permission, 'workorder')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_workorder");
            $DB->exec("ALTER TABLE `shua_workorder` RENAME TO `{$dbconfig['dbqz']}_workorder`");
        }
        if (strpos($permission, 'message')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_message");
            $DB->exec("ALTER TABLE `shua_message` RENAME TO `{$dbconfig['dbqz']}_message`");
        }
        if (strpos($permission, 'article')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_article");
            $DB->exec("ALTER TABLE `shua_article_list` RENAME TO `{$dbconfig['dbqz']}_article`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` ADD `zid` INT(11) UNSIGNED NULL AFTER `id`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` RENAME COLUMN status TO active");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` RENAME COLUMN seoKeywords TO keywords");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` RENAME COLUMN seoDescription TO description");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` ADD `color` varchar(20) NULL AFTER `description`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` RENAME COLUMN createTime TO addtime");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` ADD `count` INT(11) NULL AFTER `addtime`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_article` ADD `top` tinyint(1) NULL AFTER `count`");
        }
        if (strpos($permission, 'record')) {
            $DB->exec("DELETE FROM {$dbconfig['dbqz']}_points");
            $DB->exec("ALTER TABLE `shua_points` RENAME TO `{$dbconfig['dbqz']}_points`");
            $DB->exec("ALTER TABLE `{$dbconfig['dbqz']}_points` ADD `status` tinyint(1) NULL AFTER `orderid`");
        }
        showmsg('恭喜，祥云数据模块结构合并与迁移成功！', 1);
    } else {
        // 默认页面内容
    ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto mt-8">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-center bg-slate-50/50">
                <i class="fa fa-cloud text-brand-500 mr-2 text-xl"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">祥云数据迁移辅助工具</h3>
            </div>
            <div class="p-8 text-center space-y-5">
                <i class="fa fa-database text-6xl text-slate-200 mb-4 block"></i>
                <p class="text-slate-600 text-sm font-medium leading-relaxed">此工具专项用于将“祥云”系统的数据结构平滑地迁移整合到当前版本系统中。</p>
                <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm shadow-sm inline-block text-left">
                    <p class="font-bold flex items-center mb-1"><i class="fa fa-info-circle mr-1.5"></i>迁移工作流说明：</p>
                    <p class="text-slate-600 ml-5">系统会首先清空并删除当前系统对应库表的数据，然后将导入的祥云系统数据进行字段重命名并增补必要的系统参数，使其兼容当前系统底层框架。</p>
                </div>
                <div class="pt-4">
                    <a href="./datamove.php?my=add" class="inline-flex items-center justify-center w-full sm:w-auto px-8 h-12 rounded-xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.98]">
                        进入勾选迁移面板
                    </a>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>
