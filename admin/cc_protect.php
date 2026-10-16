<?php
/**
 * CC攻击防护管理页面
 * 用于配置和管理CC攻击防护功能
 */
// 防CC攻击管理页面
require_once '../includes/common.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 1);
include_once SYSTEM_ROOT . 'cc_protect.php';

// 检查权限
// 原判断只用 $islogin2（分站 / 供货商登录标识），后台管理员登录后 $islogin2 为 0，
// 导致管理员永远被挡在门外。这里改为：管理员或分站 / 供货商任一登录即可访问。
if ($islogin != 1 && $islogin2 != 1) {
    sysmsg('您没有权限访问此页面');
    exit();
}

// 初始化表（首次访问时）
if(!$DB->query("SHOW TABLES LIKE 'pre_cc_log'")->fetch()) {
    setup_cc_protect();
}

// 处理POST操作
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch($action) {
        case 'save_config':
            // 保存配置
            $cc_protect_enabled = isset($_POST['cc_protect_enabled']) ? 1 : 0;
            $cc_max_requests = intval($_POST['cc_max_requests']);
            $cc_time_window = intval($_POST['cc_time_window']);
            $cc_ban_time = intval($_POST['cc_ban_time']);
            $cc_redirect_url = trim($_POST['cc_redirect_url']);
            
            // 验证输入
            if($cc_max_requests <= 0) $cc_max_requests = 30;
            if($cc_time_window <= 0) $cc_time_window = 60;
            if($cc_ban_time <= 0) $cc_ban_time = 3600;
            if(!$cc_redirect_url) $cc_redirect_url = 'https://www.baidu.com';
            
            // 保存到数据库
            $DB->query("UPDATE pre_config SET v=:v WHERE k='cc_protect_enabled'", [':v' => $cc_protect_enabled]);
            $DB->query("UPDATE pre_config SET v=:v WHERE k='cc_max_requests'", [':v' => $cc_max_requests]);
            $DB->query("UPDATE pre_config SET v=:v WHERE k='cc_time_window'", [':v' => $cc_time_window]);
            $DB->query("UPDATE pre_config SET v=:v WHERE k='cc_ban_time'", [':v' => $cc_ban_time]);
            $DB->query("UPDATE pre_config SET v=:v WHERE k='cc_redirect_url'", [':v' => $cc_redirect_url]);
            
            // 清除缓存
            $CACHE->update();
            
            sysmsg('配置保存成功', 1);
            break;
            
        case 'ban_ip':
            // 手动封禁IP
            $ip = trim($_POST['ip']);
            $reason = trim($_POST['reason']);
            $ban_hours = intval($_POST['ban_hours']);
            
            if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                sysmsg('IP格式不正确', 0);
            }
            
            if(!$reason) $reason = '手动封禁';
            if($ban_hours <= 0) $ban_hours = 24;
            
            ban_ip_manually($ip, $reason, $ban_hours);
            sysmsg('IP封禁成功', 1);
            break;
            
        case 'unban_ip':
            // 解除IP封禁
            $ip = trim($_POST['ip']);
            
            if(!$ip || !filter_var($ip, FILTER_VALIDATE_IP)) {
                sysmsg('IP格式不正确', 0);
            }
            
            unban_ip($ip);
            sysmsg('IP封禁已解除', 1);
            break;
            
        case 'clean_logs':
            // 清理访问日志
            $days = intval($_POST['days']);
            $time = time() - ($days * 86400);
            
            $DB->query("DELETE FROM pre_cc_log WHERE log_time < :time", [':time' => $time]);
            sysmsg('访问日志清理成功', 1);
            break;
            
        case 'clean_expired':
            // 清理过期封禁
            clean_cc_ban_list();
            sysmsg('过期封禁清理成功', 1);
            break;
    }
    exit();
}

// 获取配置
$conf = $CACHE->pre_fetch();
$cc_protect_enabled = isset($conf['cc_protect_enabled']) ? $conf['cc_protect_enabled'] : 0;
$cc_max_requests = isset($conf['cc_max_requests']) ? $conf['cc_max_requests'] : 30;
$cc_time_window = isset($conf['cc_time_window']) ? $conf['cc_time_window'] : 60;
$cc_ban_time = isset($conf['cc_ban_time']) ? $conf['cc_ban_time'] : 3600;
$cc_redirect_url = isset($conf['cc_redirect_url']) ? $conf['cc_redirect_url'] : 'https://www.baidu.com';

// 获取统计信息
$stats = get_cc_attack_stats();

// 获取当前封禁列表
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$pageSize = 20;
$offset = ($page - 1) * $pageSize;
$total = $DB->getColumn("SELECT COUNT(*) FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0)", [':time' => time()]);
$ban_list = $DB->getAll("SELECT * FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0) ORDER BY block_time DESC LIMIT :offset, :pageSize", [':time' => time(), ':offset' => $offset, ':pageSize' => $pageSize]);

$title = '防CC攻击管理';
// 必须先引入 head.php：page() 分页函数定义在其中，此前在引入前调用导致
// "Call to undefined function page()" 致命错误，整个页面 500。
include './head.php';

// 分页参数钳制（与其它 *-table.php 保持一致的防御：负数 / 超界页码会产生非法 LIMIT）
$pageSize = 20;
$totalPages = max(1, (int)ceil($total / $pageSize));
if ($page < 1) {
    $page = 1;
}
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $pageSize;
$ban_list = $DB->getAll("SELECT * FROM pre_cc_ban WHERE (expire_time > :time OR expire_time = 0) ORDER BY block_time DESC LIMIT :offset, :pageSize", [':time' => time(), ':offset' => $offset, ':pageSize' => $pageSize]);

// 生成分页
$pages = page($total, $pageSize, $page, 'cc_protect.php?page=');
?>

<div class="w-full max-w-[1400px] mx-auto space-y-6">
    
    <!-- 头部与全局统计面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-shield text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">防CC攻击概览</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 状态卡片 -->
                <div class="flex items-center p-4 rounded-xl border <?php echo $cc_protect_enabled ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 border-slate-200'; ?>">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center <?php echo $cc_protect_enabled ? 'bg-emerald-100 text-emerald-500' : 'bg-slate-200 text-slate-400'; ?> mr-4 shrink-0 text-xl">
                        <i class="fa <?php echo $cc_protect_enabled ? 'fa-check-circle' : 'fa-power-off'; ?>"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold <?php echo $cc_protect_enabled ? 'text-emerald-700' : 'text-slate-500'; ?>">防CC功能状态</div>
                        <div class="text-xl font-bold <?php echo $cc_protect_enabled ? 'text-emerald-600' : 'text-slate-600'; ?>"><?php echo $cc_protect_enabled ? '正在运行' : '已禁用'; ?></div>
                    </div>
                </div>
                
                <!-- 当前封禁 -->
                <div class="flex items-center p-4 rounded-xl border bg-rose-50 border-rose-100">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-rose-100 text-rose-500 mr-4 shrink-0 text-xl">
                        <i class="fa fa-ban"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-rose-700">当前封禁 IP 数</div>
                        <div class="text-xl font-bold text-rose-600"><?php echo $stats['current_bans']; ?> <span class="text-sm font-normal">个</span></div>
                    </div>
                </div>
                
                <!-- 今日拦截 -->
                <div class="flex items-center p-4 rounded-xl border bg-sky-50 border-sky-100">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center bg-sky-100 text-sky-500 mr-4 shrink-0 text-xl">
                        <i class="fa fa-hand-paper-o"></i>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-sky-700">今日拦截次数</div>
                        <div class="text-xl font-bold text-sky-600"><?php echo $stats['today_blocks']; ?> <span class="text-sm font-normal">次</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 防CC配置表单 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
                <i class="fa fa-cogs text-brand-500 mr-2 text-lg"></i>
                <h4 class="text-base font-bold text-slate-800 m-0">防护规则配置</h4>
            </div>
            <div class="p-6 flex-1">
                <form onsubmit="return saveConfig(this)" method="post" class="space-y-5">
                    <input type="hidden" name="action" value="save_config">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-xl gap-3">
                        <div>
                            <div class="font-semibold text-slate-800">启用防CC攻击拦截</div>
                            <div class="text-xs text-slate-500 mt-1">自动识别并拦截恶意的超高频 CC 并发请求</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="cc_protect_enabled" value="1" class="sr-only peer" <?php echo $cc_protect_enabled ? 'checked' : ''; ?>>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">最大请求次数</label>
                        <input type="number" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="cc_max_requests" value="<?php echo $cc_max_requests; ?>" placeholder="时间窗口内允许的最大请求次数" min="1">
                        <p class="text-xs text-slate-500">在指定时间窗口内，单个 IP 允许访问的最大请求总数</p>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">时间窗口 (秒)</label>
                            <div class="relative">
                                <input type="number" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="cc_time_window" value="<?php echo $cc_time_window; ?>" placeholder="检测时间" min="1">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">秒</span>
                            </div>
                            <p class="text-xs text-slate-500">统计请求次数的时长区间</p>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">触发后封禁时长</label>
                            <div class="relative">
                                <input type="number" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="cc_ban_time" value="<?php echo $cc_ban_time; ?>" placeholder="封禁时间" min="1">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">秒</span>
                            </div>
                            <p class="text-xs text-slate-500">检测到攻击后封禁对方的时长</p>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">拦截重定向 URL</label>
                        <input type="url" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="cc_redirect_url" value="<?php echo $cc_redirect_url; ?>" placeholder="检测到攻击后重定向的URL">
                        <p class="text-xs text-slate-500">当检测到恶意攻击时，将攻击者强行跳转至该地址</p>
                    </div>
                    
                    <div class="pt-3 border-t border-slate-50">
                        <button type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.98]"><i class="fa fa-floppy-o mr-2"></i>保存规则配置</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="space-y-6 flex flex-col">
            <!-- 手动封禁 IP -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
                    <i class="fa fa-lock text-rose-500 mr-2 text-lg"></i>
                    <h4 class="text-base font-bold text-slate-800 m-0">手动封禁 IP</h4>
                </div>
                <div class="p-6">
                    <form onsubmit="return banIP(this)" method="post" class="space-y-4">
                        <input type="hidden" name="action" value="ban_ip">
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">恶意 IP 地址</label>
                            <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm bg-white" name="ip" placeholder="请输入要强制封禁的 IP 地址" required>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">封禁原因</label>
                                <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm bg-white" name="reason" placeholder="如：恶意抓取数据" value="手动封禁">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-slate-700">封禁时长</label>
                                <div class="relative">
                                    <input type="number" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm bg-white" name="ban_hours" value="24" placeholder="时长" min="1">
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">小时</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="w-full h-11 rounded-xl bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-all duration-200 active:scale-[0.98]"><i class="fa fa-ban mr-2"></i>立即执行封禁</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 维护操作 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex-1">
                <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
                    <i class="fa fa-wrench text-amber-500 mr-2 text-lg"></i>
                    <h4 class="text-base font-bold text-slate-800 m-0">缓存与维护操作</h4>
                </div>
                <div class="p-6 space-y-5">
                    <form onsubmit="return cleanLogs(this)" method="post">
                        <input type="hidden" name="action" value="clean_logs">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">清理历史访问日志</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="number" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white" name="days" value="7" placeholder="保留天数" min="1">
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">天前</span>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-colors shrink-0">执行清理</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="pt-4 border-t border-slate-50">
                        <form onsubmit="return cleanExpired(this)" method="post">
                            <input type="hidden" name="action" value="clean_expired">
                            <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <div>
                                    <div class="text-sm font-bold text-slate-700">清理过期封禁记录</div>
                                    <div class="text-xs text-slate-500 mt-1">移除数据库中已经过了封禁时长的历史记录</div>
                                </div>
                                <button type="submit" class="inline-flex items-center justify-center px-5 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-brand-600 font-medium text-sm shadow-sm transition-colors shrink-0 whitespace-nowrap"><i class="fa fa-trash-o mr-1.5"></i>清理记录</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 封禁列表 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-list-alt text-brand-500 mr-2"></i>封禁黑名单列表</h4>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">IP地址</th>
                        <th class="px-6 py-3.5 font-semibold">封禁原因</th>
                        <th class="px-6 py-3.5 font-semibold">封禁发起时间</th>
                        <th class="px-6 py-3.5 font-semibold">解封过期时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center">恶意访问次数</th>
                        <th class="px-6 py-3.5 font-semibold text-center">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($ban_list as $ban): ?>
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-rose-500"><?php echo $ban['ip']; ?></td>
                        <td class="px-6 py-4 text-slate-700"><?php echo $ban['block_reason']; ?></td>
                        <td class="px-6 py-4 text-xs text-slate-500"><?php echo $ban['block_time']; ?></td>
                        <td class="px-6 py-4 text-xs text-slate-500"><?php echo $ban['expire_time'] == 0 ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-100 text-rose-700">永久封禁</span>' : date('Y-m-d H:i:s', $ban['expire_time']); ?></td>
                        <td class="px-6 py-4 text-center font-bold text-slate-700"><?php echo $ban['access_count']; ?></td>
                        <td class="px-6 py-4 text-center">
                            <button class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm" onclick="unbanIP('<?php echo $ban['ip']; ?>')">解除封禁</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($ban_list)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa fa-check-circle-o text-4xl mb-2 text-emerald-400 block"></i>
                            太棒了，当前没有任何被封禁的 IP 记录
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(!empty($ban_list)): ?>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
            <ul class="flex items-center gap-1 list-none p-0 m-0">
                <?php echo str_replace(['<ul class="pagination">', '</ul>', '<li>', '</li>', '<a', '<span', 'class="active"'], ['', '', '<li class="inline-flex">', '</li>', '<a class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium"', '<span class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium"', 'class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default"'], $pages); ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
// 修复并优化 JS 请求交互，通过传递 form 对象保证 serialize() 上下文准确
function saveConfig(form) {
    if(confirm('确定要保存并应用当前的防护规则配置吗？')) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if(data.code == 1) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(ii);
                layer.alert('网络请求异常，请检查并重试', {icon: 2});
            }
        });
    }
    return false;
}

function banIP(form) {
    if(confirm('强烈警告：确定要强制封禁此目标 IP 吗？')) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if(data.code == 1) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(ii);
                layer.alert('网络请求异常，请检查并重试', {icon: 2});
            }
        });
    }
    return false;
}

function unbanIP(ip) {
    if(confirm('确定要为 IP：' + ip + ' 解除封禁限制吗？')) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: {action: 'unban_ip', ip: ip},
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if(data.code == 1) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(ii);
                layer.alert('网络请求异常，请检查并重试', {icon: 2});
            }
        });
    }
}

function cleanLogs(form) {
    if(confirm('将清理指定天数之前的旧日志数据，确定继续？')) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if(data.code == 1) {
                    layer.msg(data.msg, {icon: 1});
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(ii);
                layer.alert('网络请求异常，请检查并重试', {icon: 2});
            }
        });
    }
    return false;
}

function cleanExpired(form) {
    if(confirm('系统将自动找出并清理数据库中所有已过期的封禁记录，是否执行？')) {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type: 'POST',
            url: 'cc_protect.php',
            data: $(form).serialize(),
            dataType: 'json',
            success: function(data) {
                layer.close(ii);
                if(data.code == 1) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(ii);
                layer.alert('网络请求异常，请检查并重试', {icon: 2});
            }
        });
    }
    return false;
}
</script>

<?php include './foot.php'; ?>
