<?php
/*
 * 自动补单设置页面
 * 功能：管理自动补单功能，设置补单参数、补单规则等
 */
include("../includes/common.php");

// 自动创建缺少的表
function createMissingTables() {
    global $DB;
    
    try {
        // 检查pre_goods表
        $goods_check =$DB->query("SELECT 1 FROM pre_goods LIMIT 1");
        if ($goods_check === false) {
            // 创建pre_goods表
            $create_goods =$DB->exec("CREATE TABLE IF NOT EXISTS `pre_goods` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `price` decimal(10,2) NOT NULL,
                `stock` int(11) NOT NULL DEFAULT '0',
                `sales` int(11) NOT NULL DEFAULT '0',
                `shequ_id` int(11) NOT NULL DEFAULT '0',
                `shequ_goods_id` varchar(255) NOT NULL DEFAULT '',
                `type` int(11) NOT NULL DEFAULT '0',
                `cid` int(11) NOT NULL DEFAULT '0',
                `active` int(11) NOT NULL DEFAULT '1',
                `addtime` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1");
        }
        
        // 检查pre_orders表是否有reorder_times和last_reorder_time字段
        $orders_check = $DB->query("SELECT reorder_times, last_reorder_time FROM pre_orders LIMIT 1");
        if ($orders_check === false) {
            // 添加reorder_times字段
            $DB->exec("ALTER TABLE pre_orders ADD COLUMN reorder_times int(11) NOT NULL DEFAULT '0'");
            // 添加last_reorder_time字段
            $DB->exec("ALTER TABLE pre_orders ADD COLUMN last_reorder_time datetime DEFAULT NULL");
        }
        
    } catch (Exception $e) {
        // 忽略错误，继续执行
    }
}

// 检查是否是 AJAX 请求
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$is_ajax = $is_ajax || isset($_POST['ajax']) && $_POST['ajax'] == 1;

// 如果是 AJAX 请求，不输出 HTML 头部
if (!$is_ajax) {
    $title='自动补单设置';
    include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);
}

if($islogin==1){}else {
    if ($is_ajax) {
        ob_clean();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 0, 'msg' => '请先登录']);
        exit;
    } else {
        exit("<script language='javascript'>window.location.href='./login.php';</script>");
    }
}

// 执行表创建
createMissingTables();

// 获取当前配置
$autoreorder_config = [];
$config_keys = [
    'autoreorder' => 0,        // 自动补单开关
    'autoreorder_interval' => 5, // 补单间隔（分钟）
    'autoreorder_limit' => 50,   // 每次补单数
    'autoreorder_max_retries' => 3, // 最大重试次数
    'autoreorder_status' => '0,1,2', // 需要补单的订单状态
    'autoreorder_after' => 10,   // 下单多久后开始补单（分钟）
    'autoreorder_timeout' => 30  // 补单超时时间（分钟）
];

foreach($config_keys as $key => $default) {
    $value = $DB->getColumn("SELECT v FROM pre_config WHERE k='{$key}'");
    $autoreorder_config[$key] = $value !== false ? $value : $default;
}

// 处理保存操作
if(isset($_POST['submit'])) {
    $data = [
        'autoreorder' => intval($_POST['autoreorder']),
        'autoreorder_interval' => intval($_POST['autoreorder_interval']),
        'autoreorder_limit' => intval($_POST['autoreorder_limit']),
        'autoreorder_max_retries' => intval($_POST['autoreorder_max_retries']),
        'autoreorder_after' => intval($_POST['autoreorder_after']),
        'autoreorder_timeout' => intval($_POST['autoreorder_timeout'])
    ];
    
    // 处理订单状态数组
    if(isset($_POST['autoreorder_status'])) {
        $data['autoreorder_status'] = implode(',', $_POST['autoreorder_status']);
    } else {
        $data['autoreorder_status'] = '0,1';
    }
    
    // 保存配置
    $success = 0;
    $error = '';
    
    foreach($data as $key => $value) {
        if($DB->exec("REPLACE INTO pre_config SET k='{$key}',v='{$value}'")) {
            $success++;
        } else {
            $error .= "保存{$key}失败<br/>";
        }
    }
    
    // 检查是否是 AJAX 请求（同时检查 POST 参数中的 ajax 标记）
    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $is_ajax = $is_ajax || isset($_POST['ajax']) && $_POST['ajax'] == 1;
    
    if ($is_ajax) {
        // 清空所有输出缓冲区
        ob_clean();
        // 返回 JSON 格式响应
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        if($error) {
            echo json_encode(['code' => 0, 'msg' => $error]);
        } else {
            echo json_encode(['code' => 1, 'msg' => "成功更新{$success}项配置"]);
        }
        exit;
    } else {
        // 返回 HTML 格式响应
        if($error) {
            showmsg($error, 3);
        } else {
            showmsg("成功更新{$success}项配置", 1);
        }
    }
}

// 获取最近补单记录
$last_reorder_time = $DB->getColumn("SELECT v FROM pre_config WHERE k='last_reorder_time'");
if(!$last_reorder_time) $last_reorder_time = '从未运行';

// 获取补单统计信息
$total_reordered = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE reorder_times > 0");
$today_reordered = $DB->getColumn("SELECT COUNT(*) FROM pre_orders WHERE reorder_times > 0 AND addtime >= CURDATE()");

if (!$is_ajax) {
?>        

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-refresh text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">自动补单设置</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form onsubmit="return saveConfig()" method="post" role="form" class="space-y-8">
                
                <!-- 统计模块 -->
                <div class="space-y-3">
                    <label class="text-sm font-bold text-slate-700 block">数据统计与运行状态</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-center">
                            <span class="text-xs font-semibold text-slate-500 mb-1">最近自动运行时间</span>
                            <span class="text-sm font-mono font-bold text-slate-800"><?php echo $last_reorder_time?></span>
                        </div>
                        <div class="p-4 rounded-xl bg-sky-50 border border-sky-100 flex flex-col justify-center">
                            <span class="text-xs font-semibold text-sky-600 mb-1">系统累计总补单数</span>
                            <span class="text-xl font-mono font-bold text-sky-600"><?php echo $total_reordered?> <span class="text-sm font-normal">笔</span></span>
                        </div>
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex flex-col justify-center">
                            <span class="text-xs font-semibold text-emerald-600 mb-1">今日执行补单数</span>
                            <span class="text-xl font-mono font-bold text-emerald-600"><?php echo $today_reordered?> <span class="text-sm font-normal">笔</span></span>
                        </div>
                        <div class="p-4 rounded-xl bg-brand-50 border border-brand-100 flex flex-col items-center justify-center border-dashed">
                            <a href="javascript:void(0)" onclick="runReorder()" class="inline-flex items-center justify-center px-5 h-9 rounded-lg bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                                <i class="fa fa-play-circle mr-1.5"></i>立刻手动补单
                            </a>
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100 border-dashed">

                <!-- 核心设置项 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">自动补单引擎状态</label>
                        <div class="flex items-center gap-4 h-11">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="autoreorder" value="1" class="w-4 h-4 text-brand-600 border-slate-300 focus:ring-brand-500" <?php if($autoreorder_config['autoreorder'] == 1) echo 'checked';?>>
                                <span class="ml-2 text-sm font-medium text-slate-700">开启执行</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="autoreorder" value="0" class="w-4 h-4 text-rose-500 border-slate-300 focus:ring-rose-500" <?php if($autoreorder_config['autoreorder'] == 0) echo 'checked';?>>
                                <span class="ml-2 text-sm font-medium text-slate-700">暂停关闭</span>
                            </label>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">自动补单循环间隔</label>
                        <div class="relative">
                            <input type="number" name="autoreorder_interval" value="<?php echo $autoreorder_config['autoreorder_interval']?>" class="w-full h-11 pl-4 pr-16 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" min="1" max="1440" required>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">分钟</span>
                        </div>
                        <p class="text-[11px] text-slate-400">设置两次补单之间的时间间隔，建议设置为 5-15 分钟</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单次列队处理限额</label>
                        <div class="relative">
                            <input type="number" name="autoreorder_limit" value="<?php echo $autoreorder_config['autoreorder_limit']?>" class="w-full h-11 pl-4 pr-16 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" min="1" max="1000" required>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">个 / 次</span>
                        </div>
                        <p class="text-[11px] text-slate-400">单次脚本最多处理订单数量，建议根据服务器性能设定</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单笔订单最大重试次数</label>
                        <div class="relative">
                            <input type="number" name="autoreorder_max_retries" value="<?php echo $autoreorder_config['autoreorder_max_retries']?>" class="w-full h-11 pl-4 pr-16 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" min="1" max="10" required>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">次</span>
                        </div>
                        <p class="text-[11px] text-slate-400">超过该重试次数的订单将被标记为异常并不再尝试</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">触发延迟阈值</label>
                        <div class="relative">
                            <input type="number" name="autoreorder_after" value="<?php echo $autoreorder_config['autoreorder_after']?>" class="w-full h-11 pl-4 pr-16 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" min="0" max="1440" required>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">分钟</span>
                        </div>
                        <p class="text-[11px] text-slate-400">订单下单后静置多久才允许进入补单池，建议 10 分钟以上</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">失效超时阈值</label>
                        <div class="relative">
                            <input type="number" name="autoreorder_timeout" value="<?php echo $autoreorder_config['autoreorder_timeout']?>" class="w-full h-11 pl-4 pr-16 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" min="5" max="1440" required>
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">分钟</span>
                        </div>
                        <p class="text-[11px] text-slate-400">订单产生距离当前时间超过该值将不再处理，建议 30-60 分钟</p>
                    </div>
                </div>
                
                <!-- 补单订单状态选择 -->
                <div class="space-y-2 pt-4">
                    <label class="text-sm font-semibold text-slate-700 block">监听的订单状态类别</label>
                    <div class="flex flex-wrap gap-2.5">
                        <?php 
                        $status_array = explode(',', $autoreorder_config['autoreorder_status']);
                        $status_options = [
                            '0' => '未处理',
                            '1' => '处理中',
                            '2' => '已完成',
                            '3' => '已取消',
                            '4' => '已退款',
                            '5' => '退款中',
                            'djzt2' => '对接失败'
                        ];
                        
                        foreach($status_options as $val => $label) {
                            $checked = in_array($val, $status_array) ? 'checked' : '';
                        ?>
                        <label class="relative flex items-center cursor-pointer group">
                            <input type="checkbox" name="autoreorder_status[]" value="<?php echo $val; ?>" class="peer sr-only" <?php echo $checked; ?>>
                            <div class="px-4 py-2 rounded-lg border border-slate-200 bg-white text-sm font-medium text-slate-600 transition-all duration-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:text-brand-600 group-hover:border-brand-300">
                                <?php echo $label; ?>
                            </div>
                        </label>
                        <?php } ?>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">强烈建议仅勾选【未处理】与【处理中】状态，避免对已完结订单造成破坏性变更。</p>
                </div>
                
                <!-- 计划任务 -->
                <div class="space-y-2 pt-4">
                    <label class="text-sm font-semibold text-slate-700 block">服务器 Cron 计划任务</label>
                    <div class="p-4 bg-slate-800 rounded-xl overflow-hidden shadow-inner">
                        <code class="text-xs sm:text-sm text-emerald-400 font-mono block mb-3 break-all">*/5 * * * * curl --silent <?php echo $siteurl?>admin/cron.php</code>
                        <div class="text-[11px] text-slate-400 border-t border-slate-700 pt-3">
                            API 触发端点：<a href="<?php echo $siteurl?>admin/cron.php?action=reorder" target="_blank" class="text-sky-400 hover:text-sky-300 hover:underline break-all"><?php echo $siteurl?>admin/cron.php?action=reorder</a>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <button type="submit" name="submit" class="w-full sm:w-auto px-10 h-12 rounded-xl bg-brand-600 text-white font-bold text-[15px] hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存引擎配置
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- 最佳实践说明模块 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-full">
            <div class="flex items-center text-sky-600 mb-3"><i class="fa fa-info-circle text-xl mr-2"></i><h4 class="font-bold m-0 text-base">系统逻辑简介</h4></div>
            <ul class="text-sm text-slate-600 space-y-2.5 flex-1 leading-relaxed list-disc list-inside marker:text-sky-400">
                <li>本模块在后台轮询静默执行，检查符合条件的僵死/卡单数据。</li>
                <li>发现目标订单后，向目标接口重新投递建单请求。</li>
                <li>有效改善漏单、断网以及第三方服务器抽风带来的流失率。</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-full">
            <div class="flex items-center text-amber-500 mb-3"><i class="fa fa-exclamation-triangle text-xl mr-2"></i><h4 class="font-bold m-0 text-base">高危操作提示</h4></div>
            <ul class="text-sm text-slate-600 space-y-2.5 flex-1 leading-relaxed list-disc list-inside marker:text-amber-400">
                <li>参数设置过激（如间隔过短、数量过多）将面临被上游封禁 IP 的风险。</li>
                <li>切勿将重试次数拉得过高，可能导致被上游连续双重扣费。</li>
                <li>必须依赖系统自带的 CRON 或者宝塔的任务面板才能自发运行。</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col h-full">
            <div class="flex items-center text-emerald-500 mb-3"><i class="fa fa-check-circle text-xl mr-2"></i><h4 class="font-bold m-0 text-base">最佳实践推荐</h4></div>
            <div class="text-sm text-slate-600 grid grid-cols-2 gap-x-2 gap-y-2.5 flex-1">
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>间隔: 5-15分</div>
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>限额: 30-100笔</div>
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>重试: ≤ 3次</div>
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>延迟: 10-30分</div>
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>超时: 30-60分</div>
                <div class="flex items-center"><i class="fa fa-angle-right text-emerald-400 mr-1.5"></i>状态: 0与1</div>
            </div>
        </div>
    </div>

</div>

<!-- 引入layer.js库 -->
<script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/layer/3.1.1/layer.js"></script>
<script>
function saveConfig() {
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : window.location.href,
        data : $("form").serialize() + '&submit=1&ajax=1',
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 1){
                layer.alert(data.msg, {icon: 1}, function(){
                    window.location.reload();
                });
            }else{
                layer.alert(data.msg, {icon: 2});
            }
        },
        error:function(xhr, status, error){            
            layer.close(ii);            
            layer.msg('服务器通信异常: ' + error, {icon: 2});            
            console.log('AJAX错误:', status, error);            
            console.log('响应内容:', xhr.responseText);            
            $("input[type='submit']").attr("disabled",false);        
        }
    });
    return false;
}

function runReorder() {
    layer.confirm('确定要立刻唤起一次异步队列补单操作吗？', {
        title: '手动执行确权',
        btn: ['确定执行', '放弃']
    }, function() {
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.get("./cron.php?action=reorder", function(data) {
            layer.close(ii);
            try {
                var res = JSON.parse(data);
                if(res.code == 1) {
                    layer.msg(res.msg, {icon: 1});
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    layer.msg(res.msg, {icon: 2});
                }
            } catch(e) {
                layer.msg('上游通信回包解析失败', {icon: 2});
            }
        }).fail(function() {
            layer.close(ii);
            layer.msg('网络层通信中断，请检查连通性', {icon: 2});
        });
    });
}
</script>

<?php
// 如果不是 AJAX 请求，才输出 foot.php
    include './foot.php';
}
?>
