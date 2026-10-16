<?php
include('../includes/common.php');
$title = '优惠券管理';
include_once('head.php');
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);

if($islogin==1) {
    // 确保 zid 始终有值，管理员默认为 0
    $zid = 0; // 直接设置为0，避免任何可能的未定义问题
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // 自动创建优惠券表并插入测试数据
    if($action == 'list') {
        try {
            // 创建优惠券表（逐条执行SQL语句）
            $create_tables = array(
                "CREATE TABLE IF NOT EXISTS `shua_coupons` (
                    `cid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `name` varchar(255) NOT NULL COMMENT '优惠券名称',
                    `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '优惠券类型：0满减券，1折扣券，2固定金额券',
                    `value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券价值',
                    `min_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用门槛',
                    `max_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最大优惠金额（仅折扣券）',
                    `total` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发放总量',
                    `used` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '已使用数量',
                    `start_time` datetime DEFAULT NULL COMMENT '开始时间',
                    `end_time` datetime DEFAULT NULL COMMENT '结束时间',
                    `valid_days` int(11) NOT NULL DEFAULT '0' COMMENT '有效期天数（0表示固定时间）',
                    `description` text COMMENT '优惠券描述',
                    `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
                    `add_time` datetime DEFAULT NULL COMMENT '添加时间',
                    PRIMARY KEY (`cid`),
                    KEY `zid` (`zid`),
                    KEY `active` (`active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_user_coupons` (
                    `ucid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `userid` varchar(32) NOT NULL COMMENT '用户ID',
                    `orderid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用订单ID',
                    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：0未使用，1已使用，2已过期',
                    `get_time` datetime DEFAULT NULL COMMENT '获取时间',
                    `use_time` datetime DEFAULT NULL COMMENT '使用时间',
                    `expire_time` datetime DEFAULT NULL COMMENT '过期时间',
                    PRIMARY KEY (`ucid`),
                    KEY `zid` (`zid`),
                    KEY `cid` (`cid`),
                    KEY `userid` (`userid`),
                    KEY `status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户优惠券关联表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_coupon_rules` (
                    `rid` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `scene` tinyint(1) NOT NULL DEFAULT '0' COMMENT '场景：0每日签到，1推广链接，2抽奖商品',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `params` text COMMENT '场景参数（JSON格式）',
                    `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否启用',
                    `add_time` datetime DEFAULT NULL COMMENT '添加时间',
                    PRIMARY KEY (`rid`),
                    KEY `zid` (`zid`),
                    KEY `scene` (`scene`),
                    KEY `cid` (`cid`),
                    KEY `active` (`active`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券发放规则表';",
                
                "CREATE TABLE IF NOT EXISTS `shua_coupon_logs` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `zid` int(11) unsigned NOT NULL DEFAULT '1',
                    `cid` int(11) unsigned NOT NULL COMMENT '优惠券ID',
                    `userid` varchar(32) NOT NULL COMMENT '用户ID',
                    `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型：0发放，1使用，2过期',
                    `reason` varchar(255) DEFAULT NULL COMMENT '原因',
                    `add_time` datetime DEFAULT NULL COMMENT '操作时间',
                    PRIMARY KEY (`id`),
                    KEY `zid` (`zid`),
                    KEY `cid` (`cid`),
                    KEY `userid` (`userid`),
                    KEY `type` (`type`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠券发放记录表';"
            );
            
            // 逐条执行SQL语句
            foreach($create_tables as $sql) {
                $DB->exec($sql);
            }
            
            // 检查是否有优惠券数据，如果没有，插入一条测试数据
            // 仅在系统首次安装时执行，通过文件标记判断
            $init_file = __DIR__ . '/coupon_init_flag.txt';
            if(!file_exists($init_file)) {
                $coupon_count = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupons`");
                if($coupon_count == 0) {
                    $DB->exec("INSERT INTO `shua_coupons` (zid, name, type, value, max_discount, min_amount, total, start_time, end_time, valid_days, description, active, add_time) VALUES (0, '测试优惠券', 0, 10, 0, 100, 100, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0, '测试优惠券描述', 1, NOW())");
                }
                // 创建初始化标记文件
                file_put_contents($init_file, '1');
            }
        } catch (Exception $e) {
            // 忽略错误，继续执行
        }
    }
    
    echo '<div class="w-full max-w-[1400px] mx-auto space-y-6">';

    // 优惠券列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 20;
        $start = ($page - 1) * $pageSize;
        
        // 直接使用简单的查询方式，不使用预处理
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupons`");
        $coupons = $DB->getAll("SELECT * FROM `shua_coupons` ORDER BY cid DESC LIMIT {$start}, {$pageSize}");
        
        // 先输出状态切换脚本，确保函数在使用前定义
        echo '<script>
        function toggleStatus(cid, status) {
            $.ajax({
                url: "?action=toggle",
                type: "POST",
                data: {
                    cid: cid,
                    status: status
                },
                success: function(data) {
                    location.reload();
                },
                error: function() {
                    alert("状态切换失败，请刷新页面重试");
                }
            });
        }
        </script>';
        
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                    <i class="fa fa-ticket text-brand-500 mr-2 text-lg"></i>优惠券列表
                </h3>
                <a href="?action=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-colors text-sm">
                    <i class="fa fa-plus mr-1.5"></i>添加优惠券
                </a>
            </div>
            
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold text-center w-16">ID</th>
                            <th class="px-6 py-3.5 font-semibold min-w-[150px]">名称</th>
                            <th class="px-6 py-3.5 font-semibold text-center">类型</th>
                            <th class="px-6 py-3.5 font-semibold text-center">价值</th>
                            <th class="px-6 py-3.5 font-semibold text-center">使用门槛</th>
                            <th class="px-6 py-3.5 font-semibold text-center">发放总量</th>
                            <th class="px-6 py-3.5 font-semibold text-center text-emerald-600">已使用</th>
                            <th class="px-6 py-3.5 font-semibold text-center">有效期</th>
                            <th class="px-6 py-3.5 font-semibold text-center">状态</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-28">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    <?php
                    if(count($coupons) > 0) {
                        foreach($coupons as $coupon) {
                            $type_badge = '';
                            if($coupon['type'] == 0) $type_badge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-100">满减券</span>';
                            elseif($coupon['type'] == 1) $type_badge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100">折扣券</span>';
                            elseif($coupon['type'] == 2) $type_badge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">固定金额券</span>';
                            
                            $status_text = $coupon['active'] ? '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer" onclick="toggleStatus(' . $coupon['cid'] . ', 0)">启用中</span>' : '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer" onclick="toggleStatus(' . $coupon['cid'] . ', 1)">已禁用</span>';
                            
                            // 处理有效期显示，确保日期格式正确
                            if($coupon['valid_days'] > 0) {
                                $valid_text = '<span class="text-slate-500 font-medium">领取后 <b class="text-brand-600">' . $coupon['valid_days'] . '</b> 天有效</span>';
                            } else {
                                // 检查日期是否有效
                                $start_date = date('Y-m-d', strtotime($coupon['start_time']));
                                $end_date = date('Y-m-d', strtotime($coupon['end_time']));
                                
                                // 如果日期无效，显示默认值
                                if($start_date == '1970-01-01') $start_date = '';
                                if($end_date == '1970-01-01') $end_date = '';
                                
                                $valid_text = '<div class="text-[11px] text-slate-500 leading-tight">起：' . $start_date . '<br>止：' . $end_date . '</div>';
                            }
                            
                            echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">
                                <td class="px-6 py-4 text-center font-bold text-slate-700">' . $coupon['cid'] . '</td>
                                <td class="px-6 py-4 font-bold text-brand-600 max-w-[200px] truncate" title="' . htmlspecialchars($coupon['name']) . '">' . $coupon['name'] . '</td>
                                <td class="px-6 py-4 text-center">' . $type_badge . '</td>
                                <td class="px-6 py-4 text-center font-bold text-orange-500 text-base">' . ($coupon['type'] == 1 ? floatval($coupon['value']).' <span class="text-sm font-medium">折</span>' : '<span class="text-sm font-medium">¥</span> '.floatval($coupon['value'])) . '</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600">' . ($coupon['min_amount'] > 0 ? '满 <span class="text-slate-800 font-bold">¥'.floatval($coupon['min_amount']).'</span> 可用' : '<span class="text-emerald-500 font-bold">无门槛</span>') . '</td>
                                <td class="px-6 py-4 text-center font-medium text-slate-600">' . ($coupon['total'] == 0 ? '<span class="text-emerald-500">无限</span>' : $coupon['total']) . '</td>
                                <td class="px-6 py-4 text-center font-bold text-emerald-600">' . $coupon['used'] . '</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">' . $valid_text . '</td>
                                <td class="px-6 py-4 text-center">' . $status_text . '</td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="?action=edit&cid=' . $coupon['cid'] . '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm" title="编辑"><i class="fa fa-pencil"></i></a>
                                        <a href="?action=delete&cid=' . $coupon['cid'] . '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors shadow-sm" title="删除" onclick="return confirm(\'确定要删除此优惠券吗？相关发放记录也将一并移除！\');"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </td>
                            </tr>';
                        }
                    } else {
                        echo '<tr><td colspan="10" class="px-6 py-16 text-center text-slate-400">
                                <i class="fa fa-ticket text-5xl mb-3 text-slate-200 block"></i>
                                <p class="text-sm font-medium text-slate-500">暂无任何优惠券</p>
                              </td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(count($coupons) > 0): ?>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
                <ul class="flex items-center gap-1 list-none p-0 m-0">
                    <?php echo str_replace(['<ul class="pagination">', '</ul>', '<li>', '</li>', '<a', '<span', 'class="active"'], ['', '', '<li class="inline-flex">', '</li>', '<a class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium"', '<span class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium"', 'class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default"'], page($page, $pageSize, $total, '?page=')); ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php
        
        // 删除测试数据脚本
        if(file_exists('test_coupons_db.php')) {
            unlink('test_coupons_db.php');
        }
    }
    
    // 添加优惠券
    elseif($action == 'add') {
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-plus-square text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">创建新优惠券</h3>
            </div>
            
            <div class="p-6 sm:p-8">
                <form action="?action=save" method="post" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-sm font-semibold text-slate-700" for="name">优惠券名称</label>
                            <input type="text" id="name" name="name" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="如：国庆特惠满50减10专属券" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="type">优惠券类型</label>
                            <select id="type" name="type" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="0">满减券</option>
                                <option value="1">折扣券</option>
                                <option value="2">固定金额券</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="value">优惠券价值</label>
                            <input type="number" id="value" name="value" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入抵扣金额或折扣比例" step="0.01" min="0" required>
                            <p class="text-xs text-brand-600 mt-1"><i class="fa fa-info-circle mr-1"></i>折扣券请输入小数（如8.5折输入8.5），满减及固定金额券输入金额</p>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="min_amount">使用门槛金额</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                                <input type="number" id="min_amount" name="min_amount" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无门槛" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="max_discount">最大抵扣金额限制 <span class="text-slate-400 font-normal">(仅针对折扣券)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                                <input type="number" id="max_discount" name="max_discount" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无上限" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="total">发行总量</label>
                            <div class="relative">
                                <input type="number" id="total" name="total" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无限制" min="0" required>
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">张</span>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="active">优惠券状态</label>
                            <select id="active" name="active" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="1">立即启用</option>
                                <option value="0">暂不启用 (禁用)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-slate-50 border border-slate-100 rounded-xl space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="valid_type">有效期类型</label>
                            <select id="valid_type" name="valid_type" class="w-full max-w-sm h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="0">设定固定起止时间</option>
                                <option value="1">动态计算 (领取后N天有效)</option>
                            </select>
                        </div>
                        
                        <div id="fixed_time" class="grid grid-cols-1 sm:grid-cols-2 gap-5 animate-[fadeIn_0.3s_ease-out]">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">生效开始时间</label>
                                <input type="date" name="start_time" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">失效结束时间</label>
                                <input type="date" name="end_time" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" required>
                            </div>
                        </div>
                        
                        <div id="valid_days" style="display: none;" class="space-y-1.5 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700" for="days">动态有效期天数</label>
                            <div class="relative max-w-sm">
                                <input type="number" id="days" name="days" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="用户领取后几日内可用" min="1">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">日有效</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="description">详细使用说明 <span class="text-slate-400 font-normal">(描述及限制条件)</span></label>
                        <textarea id="description" name="description" class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" rows="3" placeholder="例如：仅限购买指定商品，不与其他活动同享..."></textarea>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-check mr-1.5"></i>确认创建
                        </button>
                        <button type="button" class="w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0" onclick="window.location.href='coupons.php'">
                            返回列表
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        $(function() {
            // 有效期类型切换
            $("#valid_type").change(function() {
                if($(this).val() == "0") {
                    $("#fixed_time").show();
                    $("#valid_days").hide();
                    $("#fixed_time input").prop("required", true);
                    $("#valid_days input").prop("required", false);
                } else {
                    $("#fixed_time").hide();
                    $("#valid_days").show();
                    $("#fixed_time input").prop("required", false);
                    $("#valid_days input").prop("required", true);
                }
            });
            
            // 初始化检查
            var initialValidType = $("#valid_type").val();
            if(initialValidType == "0") {
                $("#fixed_time input").prop("required", true);
                $("#valid_days input").prop("required", false);
            } else {
                $("#fixed_time input").prop("required", false);
                $("#valid_days input").prop("required", true);
            }
        });
        </script>
        <?php
    }
    
    // 编辑优惠券
    elseif($action == 'edit') {
        $cid = intval($_GET['cid']);
        $coupon = $DB->getRow("SELECT * FROM `shua_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        
        if(!$coupon) {
            showmsg('优惠券不存在', 'coupons.php');
        }
        
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">编辑优惠券信息 <span class="ml-2 font-mono text-xs font-normal text-slate-400">#<?php echo $coupon['cid']; ?></span></h3>
            </div>
            
            <div class="p-6 sm:p-8">
                <form action="?action=save" method="post" class="space-y-6">
                    <input type="hidden" name="cid" value="<?php echo $coupon['cid']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-sm font-semibold text-slate-700" for="name">优惠券名称</label>
                            <input type="text" id="name" name="name" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入优惠券名称" value="<?php echo htmlspecialchars($coupon['name']); ?>" required>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="type">优惠券类型</label>
                            <select id="type" name="type" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="0" <?php echo ($coupon['type'] == 0 ? 'selected' : ''); ?>>满减券</option>
                                <option value="1" <?php echo ($coupon['type'] == 1 ? 'selected' : ''); ?>>折扣券</option>
                                <option value="2" <?php echo ($coupon['type'] == 2 ? 'selected' : ''); ?>>固定金额券</option>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="value">优惠券价值</label>
                            <input type="number" id="value" name="value" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入优惠券价值" step="0.01" min="0" value="<?php echo $coupon['value']; ?>" required>
                            <p class="text-xs text-brand-600 mt-1"><i class="fa fa-info-circle mr-1"></i>折扣券请输入小数（如8.5折输入8.5），满减及固定金额券输入金额</p>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="min_amount">使用门槛金额</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                                <input type="number" id="min_amount" name="min_amount" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无门槛" step="0.01" min="0" value="<?php echo $coupon['min_amount']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="max_discount">最大抵扣金额限制 <span class="text-slate-400 font-normal">(仅针对折扣券)</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                                <input type="number" id="max_discount" name="max_discount" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无上限" step="0.01" min="0" value="<?php echo $coupon['max_discount']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="total">发行总量 <span class="text-emerald-500 font-normal text-xs ml-2">(已发放 <?php echo $coupon['used']; ?> 张)</span></label>
                            <div class="relative">
                                <input type="number" id="total" name="total" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="0 表示无限制" min="0" value="<?php echo $coupon['total']; ?>" required>
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">张</span>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="active">优惠券状态</label>
                            <select id="active" name="active" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="1" <?php echo ($coupon['active'] == 1 ? 'selected' : ''); ?>>启用</option>
                                <option value="0" <?php echo ($coupon['active'] == 0 ? 'selected' : ''); ?>>禁用</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="p-5 bg-slate-50 border border-slate-100 rounded-xl space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700" for="valid_type">有效期类型</label>
                            <select id="valid_type" name="valid_type" class="w-full max-w-sm h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                                <option value="0" <?php echo ($coupon['valid_days'] == 0 ? 'selected' : ''); ?>>设定固定起止时间</option>
                                <option value="1" <?php echo ($coupon['valid_days'] > 0 ? 'selected' : ''); ?>>动态计算 (领取后N天有效)</option>
                            </select>
                        </div>
                        
                        <div id="fixed_time" <?php echo ($coupon['valid_days'] > 0 ? 'style="display: none;"' : ''); ?> class="grid grid-cols-1 sm:grid-cols-2 gap-5 animate-[fadeIn_0.3s_ease-out]">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">生效开始时间</label>
                                <input type="date" name="start_time" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" value="<?php echo is_numeric($coupon['start_time']) ? date('Y-m-d', $coupon['start_time']) : date('Y-m-d', strtotime($coupon['start_time'])); ?>" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">失效结束时间</label>
                                <input type="date" name="end_time" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" value="<?php echo is_numeric($coupon['end_time']) ? date('Y-m-d', $coupon['end_time']) : date('Y-m-d', strtotime($coupon['end_time'])); ?>" required>
                            </div>
                        </div>
                        
                        <div id="valid_days" <?php echo ($coupon['valid_days'] == 0 ? 'style="display: none;"' : ''); ?> class="space-y-1.5 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700" for="days">动态有效期天数</label>
                            <div class="relative max-w-sm">
                                <input type="number" id="days" name="days" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="用户领取后几日内可用" min="1" value="<?php echo $coupon['valid_days']; ?>">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">日有效</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="description">详细使用说明 <span class="text-slate-400 font-normal">(描述及限制条件)</span></label>
                        <textarea id="description" name="description" class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" rows="3" placeholder="例如：仅限购买指定商品，不与其他活动同享..."><?php echo htmlspecialchars($coupon['description']); ?></textarea>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-floppy-o mr-1.5"></i>保存修改
                        </button>
                        <button type="button" class="w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0" onclick="window.location.href='coupons.php'">
                            放弃返回
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        $(function() {
            // 有效期类型切换
            $("#valid_type").change(function() {
                if($(this).val() == "0") {
                    $("#fixed_time").show();
                    $("#valid_days").hide();
                    $("#fixed_time input").prop("required", true);
                    $("#valid_days input").prop("required", false);
                } else {
                    $("#fixed_time").hide();
                    $("#valid_days").show();
                    $("#fixed_time input").prop("required", false);
                    $("#valid_days input").prop("required", true);
                }
            });
            
            // 初始化检查
            var initialValidType = $("#valid_type").val();
            if(initialValidType == "0") {
                $("#fixed_time input").prop("required", true);
                $("#valid_days input").prop("required", false);
            } else {
                $("#fixed_time input").prop("required", false);
                $("#valid_days input").prop("required", true);
            }
        });
        </script>
        <?php
    }
    
    // 保存优惠券
    elseif($action == 'save') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = trim($_POST['name']);
            $type = intval($_POST['type']);
            $value = floatval($_POST['value']);
            $max_discount = floatval($_POST['max_discount']);
            $min_amount = floatval($_POST['min_amount']);
            $total = intval($_POST['total']);
            $valid_type = intval($_POST['valid_type']);
            $description = trim($_POST['description']);
            $active = intval($_POST['active']);
            
            // 处理有效期
            if($valid_type == 0) {
                // 固定时间
                $start_time = date('Y-m-d H:i:s', strtotime($_POST['start_time']));
                $end_time = date('Y-m-d H:i:s', strtotime($_POST['end_time']));
                $valid_days = 0;
            } else {
                // 领取后N天有效
                $start_time = date('Y-m-d H:i:s');
                $end_time = date('Y-m-d H:i:s', strtotime('+365 days'));
                $valid_days = intval($_POST['days']);
            }
            
            if(isset($_POST['cid'])) {
                // 编辑
                $cid = intval($_POST['cid']);
                $DB->exec("UPDATE `shua_coupons` SET name = ?, type = ?, value = ?, max_discount = ?, min_amount = ?, total = ?, start_time = ?, end_time = ?, valid_days = ?, description = ?, active = ?, add_time = ? WHERE cid = ? AND zid = ?", [$name, $type, $value, $max_discount, $min_amount, $total, $start_time, $end_time, $valid_days, $description, $active, date('Y-m-d H:i:s'), $cid, $zid]);
            } else {
                // 添加
                $DB->exec("INSERT INTO `shua_coupons` (zid, name, type, value, max_discount, min_amount, total, start_time, end_time, valid_days, description, active, used, add_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)", [$zid, $name, $type, $value, $max_discount, $min_amount, $total, $start_time, $end_time, $valid_days, $description, $active, date('Y-m-d H:i:s')]);
            }
            
            showmsg('优惠券信息已成功保存', 'coupons.php');
        } else {
            showmsg('非法请求，请按正常流程操作', 'coupons.php');
        }
    }
    
    // 切换优惠券状态
    elseif($action == 'toggle') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $cid = intval($_POST['cid']);
            $status = intval($_POST['status']);
            
            $DB->exec("UPDATE `shua_coupons` SET active = ? WHERE cid = ? AND zid = ?", [$status, $cid, $zid]);
            exit('success');
        } else {
            exit('error');
        }
    }
    
    // 删除优惠券
    elseif($action == 'delete') {
        $cid = intval($_GET['cid']);
        $DB->exec("DELETE FROM `shua_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        $DB->exec("DELETE FROM `shua_user_coupons` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        $DB->exec("DELETE FROM `shua_coupon_rules` WHERE cid = ? AND zid = ?", [$cid, $zid]);
        showmsg('优惠券及相关规则已被彻底删除', 'coupons.php');
    }
    
    echo '</div>'; // End wrapper max-w-[1400px]
} else {
    showmsg('登录已过期或验证失败，请重新登录系统！', 'login.php');
}

?>
</body>
</html>
