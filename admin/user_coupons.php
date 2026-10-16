<?php
include('../includes/common.php');
$title = '用户优惠券管理';
include_once('head.php');
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);

// 格式化时间函数，与coupons.php保持一致
function formatTime($time) {
    if (is_null($time) || $time === '') {
        return '-';
    }
    
    $timestamp = strtotime($time);
    if ($timestamp === false || $timestamp <= 0) {
        return '-';
    }
    
    return date('Y-m-d H:i:s', $timestamp);
}

if($islogin==1) {
    // 设置 zid，管理员默认为 0
    $zid = isset($zid) ? $zid : 0;
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    // 检查用户优惠券表，如果没有数据，插入一些示例数据
    if($action == 'list') {
        try {
            // 修复现有无效时间数据
            $DB->exec("UPDATE shua_user_coupons SET get_time = NOW() WHERE get_time IN ('0000-00-00 00:00:00', '0000-00-00')");
            $DB->exec("UPDATE shua_user_coupons SET expire_time = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE expire_time IN ('0000-00-00 00:00:00', '0000-00-00')");
        } catch (Exception $e) {
            // 忽略错误，继续执行
        }
    }
    
    echo '<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 mt-4">';
    
    // 用户优惠券列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 100; // 增加到每页显示100条
        $start = ($page - 1) * $pageSize;
        
        // 使用正常的查询，不使用zid条件，确保所有用户优惠券都能显示
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_user_coupons` uc LEFT JOIN `shua_coupons` c ON uc.cid = c.cid");
        $user_coupons = $DB->getAll("SELECT uc.*, c.name, c.type, c.value, c.min_amount FROM `shua_user_coupons` uc LEFT JOIN `shua_coupons` c ON uc.cid = c.cid ORDER BY uc.ucid DESC LIMIT " . intval($start) . ", " . intval($pageSize));
        
        $type_text = [
            0 => '<span class="inline-flex items-center px-2 py-0.5 rounded bg-sky-50 text-sky-600 text-xs font-semibold border border-sky-100">满减券</span>', 
            1 => '<span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-600 text-xs font-semibold border border-amber-100">折扣券</span>', 
            2 => '<span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-50 text-purple-600 text-xs font-semibold border border-purple-100">固定金额券</span>'
        ];
        $status_text = [
            0 => '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fa fa-circle-o mr-1"></i>未使用</span>', 
            1 => '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200"><i class="fa fa-check-circle mr-1"></i>已核销</span>', 
            2 => '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-100"><i class="fa fa-times-circle mr-1"></i>已过期</span>'
        ];
        
        echo '
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/50 gap-4">
                <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                    <i class="fa fa-ticket text-brand-500 mr-2 text-lg"></i>用户持有优惠券明细
                </h2>
                <a href="?action=issue" class="inline-flex items-center justify-center px-5 h-10 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                    <i class="fa fa-paper-plane mr-1.5"></i>定向手动下发票券
                </a>
            </div>
            
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold text-center w-20">发卡ID</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-24">归属UID</th>
                            <th class="px-6 py-3.5 font-semibold min-w-[150px]">持有票券名称</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-28">票券类型</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-24 text-rose-500">面额价值</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-28">核销门槛</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-36">入账获取时间</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-36">有效终止时间</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-28">当前状态</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-24">操作管理</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">';
        
        foreach($user_coupons as $uc) {
            $value_text = $uc['type'] == 1 ? $uc['value'].' 折' : '¥ '.$uc['value'];
            echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">
                <td class="px-6 py-4 text-center font-bold text-slate-700 font-mono">' . $uc['ucid'] . '</td>
                <td class="px-6 py-4 text-center font-mono">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-bold border border-slate-200">UID: ' . $uc['userid'] . '</span>
                </td>
                <td class="px-6 py-4 font-bold text-slate-800 max-w-[200px] truncate" title="' . htmlspecialchars($uc['name']) . '">' . $uc['name'] . '</td>
                <td class="px-6 py-4 text-center">' . $type_text[$uc['type']] . '</td>
                <td class="px-6 py-4 text-center font-mono font-bold text-rose-600 text-base">' . $value_text . '</td>
                <td class="px-6 py-4 text-center font-mono font-semibold text-slate-500">满 ¥' . $uc['min_amount'] . '</td>
                <td class="px-6 py-4 text-center font-mono text-xs text-slate-400">' . formatTime($uc['get_time']) . '</td>
                <td class="px-6 py-4 text-center font-mono text-xs text-brand-600 font-medium">' . formatTime($uc['expire_time']) . '</td>
                <td class="px-6 py-4 text-center">' . $status_text[$uc['status']] . '</td>
                <td class="px-6 py-4 text-center">
                    <a href="?action=delete&ucid=' . $uc['ucid'] . '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium transition-colors shadow-sm" title="强制删除核销记录" onclick="return confirm(\'确定要抹除该用户的该张票券吗？\');">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>';
        }
        
        if(empty($user_coupons)) {
            echo '<tr><td colspan="10" class="px-6 py-16 text-center text-slate-400">
                <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">当前系统内暂无任何用户的优惠券持有记录</p>
            </td></tr>';
        }
        
        echo '</tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">';
        echo '<ul class="flex items-center gap-1 list-none p-0 m-0">';
        echo str_replace(['<ul class="pagination">', '</ul>', '<li>', '</li>', '<a', '<span', 'class="active"'], ['', '', '<li class="inline-flex">', '</li>', '<a class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium"', '<span class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium"', 'class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default"'], page($page, $pageSize, $total, '?page='));
        echo '</ul>';
        echo '</div>
    </div>';
    }
    
    // 手动发放优惠券
    elseif($action == 'issue') {
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE active = 1 ORDER BY cid DESC");
        echo '
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-paper-plane text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">定向手动下发优惠券</h3>
            </div>
            
            <div class="p-6 sm:p-8">
                <form action="?action=do_issue" method="post" class="space-y-6">
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="userid">接收目标 用户UID <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-user"></i></div>
                            <input type="text" id="userid" name="userid" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入精准的用户 ID 数字" required>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="cid">选择待发行的优惠券类型 <span class="text-rose-500">*</span></label>
                        <select id="cid" name="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                            <option value="">请从现存可用票券中选择...</option>';
                            foreach($coupons as $coupon) {
                                echo '<option value="' . $coupon['cid'] . '">' . htmlspecialchars($coupon['name']) . '</option>';
                            }
                            echo '
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="num">单次发放张数</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-sort-numeric-asc"></i></div>
                            <input type="number" id="num" name="num" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入整数数量" min="1" max="100" value="1" required>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">系统单次批处理极限为 100 张防抖限制</p>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-check mr-1.5"></i>确认发行并推送入账
                        </button>
                        <button type="button" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0" onclick="window.location.href=\'user_coupons.php\'">
                            放弃并返回
                        </button>
                    </div>
                </form>
            </div>
        </div>';
    }
    
    // 执行发放优惠券
    elseif($action == 'do_issue') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userid = intval($_POST['userid']);
            $cid = intval($_POST['cid']);
            $num = intval($_POST['num']);
            
            $coupon = $DB->getRow("SELECT * FROM `shua_coupons` WHERE cid = ? AND active = 1", [$cid]);
            if(!$coupon) {
                showmsg('校验阻断：您选定的优惠券已不存在或被强行禁用挂起！', 'user_coupons.php');
            }
            
            // 检查发放总量
            if($coupon['total'] > 0) {
                $issued = $DB->getColumn("SELECT COUNT(*) FROM `shua_user_coupons` WHERE cid = ?", [$cid]);
                if($issued + $num > $coupon['total']) {
                    showmsg('预警拦截：该面额的优惠券系统总发行配额度已被触及上限！', 'user_coupons.php');
                }
            }
            
            // 计算过期时间
            if($coupon['valid_days'] > 0) {
                $expire_time = strtotime('+' . $coupon['valid_days'] . ' days');
            } else {
                $expire_time = strtotime($coupon['end_time']);
            }
            
            // 发放优惠券
            $success = 0;
            $current_time = date('Y-m-d H:i:s');
            $expire_time_formatted = date('Y-m-d H:i:s', $expire_time);
            for($i = 0; $i < $num; $i++) {
                $result = $DB->exec("INSERT INTO `shua_user_coupons` (zid, userid, cid, get_time, expire_time, status) VALUES (?, ?, ?, ?, ?, ?)", [$zid, $userid, $cid, $current_time, $expire_time_formatted, 0]);
                if($result) {
                    $success++;
                }
            }
            
            if($success > 0) {
                showmsg('业务执行完毕，系统已成功向指定用户推送 ' . $success . ' 张票券', 'user_coupons.php');
            } else {
                showmsg('致命异常：优惠券账单记录写入失败！', 'user_coupons.php');
            }
        } else {
            showmsg('安全异常拦截：非法的数据包投递请求', 'user_coupons.php');
        }
    }
    
    // 删除用户优惠券
    elseif($action == 'delete') {
        $ucid = intval($_GET['ucid']);
        $DB->exec("DELETE FROM `shua_user_coupons` WHERE ucid = ?", [$ucid]);
        showmsg('核销明细记录强制抹除指令已成功下达执行', 'user_coupons.php');
    }
    
    echo '</div>'; // 关闭主容器
} else {
    showmsg('登录授权态已过期或校验失败，可能是密码篡改或越权请求！', 'login.php');
}

?></body>
</html>
