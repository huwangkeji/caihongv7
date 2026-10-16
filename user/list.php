<?php
/**
 * 订单管理
 **/
include("../includes/common.php");
$title = '订单管理';
include './head.php';
if ($islogin2 == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">
    
    <div id="statusHelpContent" class="hidden">
        <div class="p-6 text-sm text-slate-600 leading-relaxed">
            <?php echo $conf['gg_search'] ?>
        </div>
    </div>

    <?php
    function display_zt($zt)
    {
        if ($zt == 1) {
            return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-sm"><i class="fa fa-check-circle mr-1"></i>已完成</span>';
        } elseif ($zt == 2) {
            return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200 shadow-sm"><i class="fa fa-clock-o mr-1"></i>正在处理</span>';
        } elseif ($zt == 3) {
            return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200 shadow-sm"><i class="fa fa-exclamation-triangle mr-1"></i>异常报错</span>';
        } elseif ($zt == 4) {
            return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm"><i class="fa fa-reply mr-1"></i>已退款</span>';
        } else {
            return '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200 shadow-sm"><i class="fa fa-hourglass-start mr-1"></i>排队待处理</span>';
        }
    }

    $sqls = $userrow['power'] > 0 ? "A.zid='{$userrow['zid']}'" : "A.userid='{$userrow['zid']}'";

    // 构建查询信息面板
    if (isset($_GET['kw']) && !empty($_GET['kw'])) {
        $kw      = daddslashes($_GET['kw']);
        $sql     = " (A.`input`='{$kw}' OR A.`id`='{$kw}' OR A.`tradeno`='{$kw}') AND {$sqls}";
        $numrows = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE{$sql}");
        $link    = '&kw=' . $_GET['kw'];
        
        $con = '<div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-indigo-800 font-bold mb-1"><i class="fa fa-search mr-1.5"></i>检索锁定：' . htmlspecialchars($_GET['kw']) . '</span>
                        <span class="text-xs text-indigo-600/70 font-medium">精准匹配到 <b class="text-indigo-600 text-lg mx-1">' . $numrows . '</b> 笔历史订单记录</span>
                    </div>
                    <a href="list.php" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white text-indigo-600 font-bold text-xs hover:bg-indigo-600 hover:text-white transition-colors shadow-sm shrink-0 border border-indigo-200"><i class="fa fa-list mr-1"></i>撤销检索返回全量库</a>
                </div>';
    } else {
        $sql     = " {$sqls}";
        $numrows = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE{$sql}");
        $ondate  = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE status=1 AND{$sql}");
        $ondate2 = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE status=2 AND{$sql}");
        
        $con = '<div class="bg-white border border-slate-100 rounded-xl p-5 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-xl flex items-center justify-center shrink-0">
                            <i class="fa fa-database text-xl"></i>
                        </div>
                        <div class="flex flex-col">
                            <h3 class="text-base font-bold text-slate-800 m-0">全局订单总览库</h3>
                            <span class="text-xs text-slate-500 font-medium mt-1">记录着您账户下的每一笔流转日志</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-6 text-sm divide-x divide-slate-100">
                        <div class="flex flex-col items-center">
                            <span class="text-xs text-slate-400 font-bold mb-1">总流转单量</span>
                            <span class="font-mono font-bold text-slate-700 text-lg leading-none">' . $numrows . '</span>
                        </div>
                        <div class="flex flex-col items-center pl-6">
                            <span class="text-xs text-emerald-500/70 font-bold mb-1">已成功处理</span>
                            <span class="font-mono font-bold text-emerald-600 text-lg leading-none">' . $ondate . '</span>
                        </div>
                        <div class="flex flex-col items-center pl-6">
                            <span class="text-xs text-amber-500/70 font-bold mb-1">队列执行中</span>
                            <span class="font-mono font-bold text-amber-500 text-lg leading-none">' . $ondate2 . '</span>
                        </div>
                    </div>
                </div>';
    }
    
    // 输出顶部卡片
    echo $con;
    ?>
    
    <!-- 核心检索引擎 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6 p-4 sm:p-6">
        <form action="./list.php" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-search"></i>
                </div>
                <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-slate-50 placeholder:text-slate-400 placeholder:font-normal" name="kw" placeholder="输入下单主账号或流水单号搜索..." value="<?php echo isset($_GET['kw']) ? htmlspecialchars($_GET['kw']) : ''; ?>">
            </div>
            <!-- 修复了按钮颜色显示问题，并适配移动端全宽 -->
            <div class="flex gap-3 w-full md:w-auto shrink-0">
                <button type="submit" class="flex-1 md:flex-none inline-flex items-center justify-center px-6 h-11 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-sm transition-all duration-200 active:scale-95">
                    <i class="fa fa-search mr-1.5"></i>搜索订单
                </button>
                <button type="button" onclick="showStatusHelp()" class="flex-none inline-flex items-center justify-center px-4 h-11 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 shadow-sm transition-all duration-200 active:scale-95" title="点击查看状态释义">
                    <i class="fa fa-question-circle mr-1"></i>说明
                </button>
            </div>
        </form>
    </div>

    <!-- 订单流水清单 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 font-semibold text-center w-20">档案提取</th>
                    <th class="px-6 py-4 font-semibold text-center">订单ID</th>
                    <th class="px-6 py-4 font-semibold w-1/3">商品名</th>
                    <th class="px-6 py-4 font-semibold">下单信息</th>
                    <th class="px-6 py-4 font-semibold text-center">发货批次</th>
                    <th class="px-6 py-4 font-semibold text-center hidden sm:table-cell">创建时间</th>
                    <th class="px-6 py-4 font-semibold text-center">反馈状态</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">

                <?php
                $pagesize = 30;
                $pages    = ceil($numrows / $pagesize);
                $page     = isset($_GET['page']) ? intval($_GET['page']) : 1;
                $offset   = $pagesize * ($page - 1);

                $rs = $DB->query("SELECT A.*,B.name FROM pre_orders A left join pre_tools B on A.tid=B.tid WHERE{$sql} ORDER BY id DESC LIMIT $offset,$pagesize");
                $hasData = false;
                
                while ($res = $rs->fetch()) {
                    $hasData = true;
                    // 脱敏逻辑
                    if ($res['djzt'] == 3 && $res['userid'] != $userrow['zid']) {
                        $input = '****' . substr($res['input'], 4);
                    } else {
                        $input = htmlspecialchars($res['input']);
                    }
                    
                    // 操作按钮
                    $actionBtn = ($res['userid'] == $userrow['zid']) 
                        ? '<a href="javascript:showOrder(' . $res['id'] . ',\'' . md5($res['id'] . SYS_KEY . $res['id']) . '\')" title="提档审核" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm"><i class="fa fa-eye"></i></a>' 
                        : '<span title="越权阻断：非本人结算订单" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed border border-slate-100"><i class="fa fa-eye-slash"></i></span>';

                    echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-3 text-center">
                                ' . $actionBtn . '
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="font-mono text-slate-400 font-medium">#' . $res['id'] . '</span>
                            </td>
                            <td class="px-6 py-3 font-bold text-slate-700 max-w-[200px] sm:max-w-[280px] truncate" title="'.htmlspecialchars($res['name']).'">
                                ' . htmlspecialchars($res['name']) . '
                            </td>
                            <td class="px-6 py-3">
                                <div class="max-w-[150px] sm:max-w-[200px] truncate font-mono text-slate-500 bg-slate-50 px-2 py-1 rounded border border-slate-100" title="' . $input . '">
                                    ' . $input . '
                                </div>
                            </td>
                            <td class="px-6 py-3 text-center font-bold text-slate-600">
                                ' . $res['value'] . '
                            </td>
                            <td class="px-6 py-3 text-center font-mono text-xs text-slate-400 hidden sm:table-cell">
                                ' . $res['addtime'] . '
                            </td>
                            <td class="px-6 py-3 text-center">
                                ' . display_zt($res['status']) . '
                            </td>
                        </tr>';
                }
                
                if(!$hasData){
                    echo '<tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <i class="fa fa-inbox text-4xl mb-3 text-slate-200 block"></i>
                            <p class="text-sm font-medium text-slate-500">此空间暂无任何交易流水痕迹</p>
                          </td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页系统包裹器 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
                <?php
                $first = 1;
                $prev  = $page - 1;
                $next  = $page + 1;
                $last  = $pages;
                
                // 上一页
                if ($page > 1) {
                    echo '<a href="list.php?page=' . $first . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="退回源头"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="list.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="回退一步"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                // 数字页码
                $start = $page - 3 > 1 ? $page - 3 : 1;
                $end   = $page + 3 < $pages ? $page + 3 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="list.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="list.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                // 下一页
                if ($page < $pages) {
                    echo '<a href="list.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下潜一步"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="list.php?page=' . $last . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="直达尾端"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>
    </div>
</div>

<?php include './foot.php'; ?>

<!-- 底层支撑脚本 -->
<script src="<?php echo $cdnpublic ?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>var homepage = false;</script>
<script src="/assets/js/main.js?ver=<?php echo VERSION ?>"></script>

<script>
    function showStatusHelp() {
        layer.open({
            type: 1,
            title: '<div class="font-bold text-slate-800"><i class="fa fa-info-circle text-sky-500 mr-2"></i>订单状态说明</div>',
            area: [$(window).width() > 480 ? '400px' : '90%', 'auto'],
            skin: 'layui-layer-rim rounded-2xl',
            shadeClose: true,
            content: $('#statusHelpContent').html()
        });
    }

    // 跨模态挂载详情拉取
    function showOrder(id, skey) {
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        
        var status = [
            '<span class="px-2 py-0.5 rounded text-xs font-bold bg-sky-50 text-sky-600 border border-sky-200">排队待处理</span>', 
            '<span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">已成功执行</span>', 
            '<span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-50 text-amber-600 border border-amber-200">等待处理中</span>', 
            '<span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">失效异常</span>', 
            '<span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">无效已退款</span>'
        ];
        
        $.ajax({
            type: "POST",
            url: "../ajax.php?act=order",
            data: {id: id, skey: skey},
            dataType: 'json',
            success: function (data) {
                layer.close(ii);
                if (data.code == 0) {
                    var item = '<div class="p-4 sm:p-6 bg-white overflow-y-auto max-h-[80vh] scrollbar-thin">';
                    item += '<div class="space-y-4">';
                    
                    item += '<div class="bg-slate-50 border border-slate-100 rounded-xl p-4">';
                    item += '<h4 class="text-sm font-bold text-slate-800 border-b border-slate-200 pb-2 mb-3"><i class="fa fa-cube text-indigo-500 mr-2"></i>基础数据</h4>';
                    item += '<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">';
                    item += '<div class="flex flex-col"><span class="text-slate-400 text-xs font-bold">编号</span><span class="font-mono text-slate-700 font-bold break-all">#' + id + '</span></div>';
                    item += '<div class="flex flex-col"><span class="text-slate-400 text-xs font-bold">标的物名</span><span class="text-slate-700 font-bold break-all">' + data.name + '</span></div>';
                    item += '<div class="flex flex-col"><span class="text-slate-400 text-xs font-bold">交易金额</span><span class="text-emerald-600 font-bold font-mono">¥ ' + data.money + '</span></div>';
                    item += '<div class="flex flex-col"><span class="text-slate-400 text-xs font-bold">下单时间</span><span class="text-slate-600 font-mono">' + data.date + '</span></div>';
                    item += '<div class="flex flex-col sm:col-span-2"><span class="text-slate-400 text-xs font-bold mb-1">入参信息</span><div class="bg-white p-2 border border-slate-200 rounded-lg text-slate-600 break-all font-mono text-xs">' + data.inputs + '</div></div>';
                    item += '<div class="flex flex-col"><span class="text-slate-400 text-xs font-bold mb-1">周期</span><div>' + status[data.status] + '</div></div>';
                    item += '</div></div>';
                    
                    if (data.complain) {
                        item += '<div class="flex gap-3 pt-2">';
                        item += '<a href="./workorder.php?my=add&orderid=' + id + '&skey=' + skey + '" target="_blank" onclick="return checklogin(' + data.islogin + ')" class="flex-1 py-2 text-center rounded-lg bg-amber-50 text-amber-600 font-bold hover:bg-amber-500 hover:text-white transition-colors border border-amber-200 shadow-sm text-sm"><i class="fa fa-warning mr-1"></i>发起仲裁投诉</a>';
                        item += '<a href="#" onclick="return orderRefund(' + data.islogin + ',' + id + ')" class="flex-1 py-2 text-center rounded-lg bg-rose-50 text-rose-600 font-bold hover:bg-rose-500 hover:text-white transition-colors border border-rose-200 shadow-sm text-sm"><i class="fa fa-reply mr-1"></i>强制撤销退款</a>';
                        item += '</div>';
                    }
                    
                    if (data.list && data.list.order_state) {
                        item += '<div class="bg-sky-50 border border-sky-100 rounded-xl p-4">';
                        item += '<h4 class="text-sm font-bold text-sky-800 border-b border-sky-200 pb-2 mb-3"><i class="fa fa-heartbeat text-sky-500 mr-2"></i>底层引擎实时探针</h4>';
                        item += '<div class="grid grid-cols-2 gap-3 text-sm">';
                        item += '<div class="flex flex-col"><span class="text-sky-600/70 text-xs font-bold">订单总额</span><span class="font-mono text-sky-800 font-bold">' + data.list.num + '</span></div>';
                        item += '<div class="flex flex-col"><span class="text-sky-600/70 text-xs font-bold">订单时间</span><span class="font-mono text-sky-800 text-xs">' + data.list.add_time + '</span></div>';
                        item += '<div class="flex flex-col"><span class="text-sky-600/70 text-xs font-bold">原数据</span><span class="font-mono text-sky-800 font-bold">' + data.list.start_num + '</span></div>';
                        item += '<div class="flex flex-col"><span class="text-sky-600/70 text-xs font-bold">当前进度</span><span class="font-mono text-sky-800 font-bold">' + data.list.now_num + '</span></div>';
                        item += '<div class="flex flex-col col-span-2"><span class="text-sky-600/70 text-xs font-bold mb-1">外链引擎状态反馈</span><div class="px-2 py-1 bg-white rounded border border-sky-200 text-sky-700 font-bold text-xs">' + data.list.order_state + '</div></div>';
                        item += '</div></div>';
                    } else if (data.kminfo) {
                        item += '<div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4">';
                        item += '<h4 class="text-sm font-bold text-emerald-800 border-b border-emerald-200 pb-2 mb-3"><i class="fa fa-key text-emerald-500 mr-2"></i>数字卡密提取</h4>';
                        item += '<div class="bg-white p-3 rounded-lg border border-emerald-200 font-mono text-sm text-emerald-700 break-all">' + data.kminfo + '</div>';
                        item += '</div>';
                    } else if (data.result) {
                        item += '<div class="bg-slate-50 border border-slate-200 rounded-xl p-4">';
                        item += '<h4 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-3"><i class="fa fa-terminal text-slate-400 mr-2"></i>执行端回传</h4>';
                        item += '<div class="bg-white p-2 rounded border border-slate-200 font-mono text-xs text-slate-600 break-all">' + data.result + '</div>';
                        item += '</div>';
                    }
                    
                    if (data.desc) {
                        item += '<div class="bg-slate-50 border border-slate-200 rounded-xl p-4">';
                        item += '<h4 class="text-sm font-bold text-slate-700 border-b border-slate-200 pb-2 mb-3"><i class="fa fa-book text-slate-400 mr-2"></i>商品快照备注</h4>';
                        item += '<div class="text-sm text-slate-600 prose prose-sm max-w-none prose-img:rounded-lg">' + data.desc + '</div>';
                        item += '</div>';
                    }
                    
                    item += '</div></div>';
                    
                    var area = [$(window).width() > 480 ? '480px' : '95%', ';max-height:90vh'];
                    
                    layer.open({
                        type: 1,
                        area: area,
                        title: false,
                        closeBtn: 1,
                        skin: 'layui-layer-rim rounded-2xl overflow-hidden',
                        content: item
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            }
        });
    }
</script>
</body>
</html>
