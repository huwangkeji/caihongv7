<?php

include "../includes/common.php";
$title = "订单管理";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

function display_zt($zt)
{
	if ($zt == 1) {
		return "<span class='inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm'>已完成</span>";
	} elseif ($zt == 2) {
		return "<span class='inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-100 shadow-sm'>正在处理</span>";
	} elseif ($zt == 3) {
		return "<span class='inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm'>异常</span>";
	} elseif ($zt == 4) {
		return "<span class='inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm'>已退款</span>";
	} else {
		return "<span class='inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm'>待处理</span>";
	}
}
?>
<link href="../assets/appui/css/datepicker.css" rel="stylesheet">

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php 
$sqls = $suprow["power"] > 0 ? "A.sid='" . $suprow["sid"] . "'" : "A.userid='" . $suprow["sid"] . "'";
$links = "";

if (!empty($_GET["starttime"]) || !empty($_GET["endtime"])) {
	if (!empty($_GET["starttime"])) {
		$sqls .= " AND A.addtime>='" . $_GET["starttime"] . " 00:00:00'";
		$links .= "&starttime=" . $_GET["starttime"];
	}
	if (!empty($_GET["endtime"])) {
		$sqls .= " AND A.addtime<='" . $_GET["endtime"] . " 23:59:59'";
		$links .= "&endtime=" . $_GET["endtime"];
	}
}

if (isset($_GET["kw"]) && !empty($_GET["kw"])) {
	$kw = daddslashes($_GET["kw"]);
	$sql = " (A.`input`='" . $kw . "' OR A.`id`='" . $kw . "' OR A.`tradeno`='" . $kw . "') AND " . $sqls;
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE" . $sql);
	$header_title = "<i class='fa fa-search text-indigo-500 mr-2 text-xl'></i>搜索结果: <span class='text-indigo-600 font-mono ml-2'>{$_GET['kw']}</span>";
	$header_desc = "包含 " . htmlspecialchars($_GET['kw']) . " 的共有 <b class='text-indigo-600 text-sm mx-1 font-mono'>{$numrows}</b> 个订单";
	$link = "&kw=" . $_GET["kw"] . $links;
} else {
	$sql = " " . $sqls;
	if (isset($_GET["type"]) && $_GET["type"] >= 0) {
		$sql .= " AND `status`='" . $_GET["type"] . "'";
		$links .= "&type=" . $_GET["type"];
		$numrows = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE" . $sql);
		$header_title = "<i class='fa fa-filter text-sky-500 mr-2 text-xl'></i>订单查询";
		$header_desc = display_zt($_GET["type"]) . " 状态的共有 <b class='text-sky-600 text-sm mx-1 font-mono'>{$numrows}</b> 个订单";
	} else {
		$numrows = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE" . $sql);
		$ondate = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE status=1 AND" . $sql);
		$ondate2 = $DB->getColumn("SELECT count(*) FROM pre_orders A WHERE status=2 AND" . $sql);
		$header_title = "<i class='fa fa-list-alt text-emerald-500 mr-2 text-xl'></i>订单查询";
		$header_desc = "共有 <b class='text-emerald-600 text-sm mx-1 font-mono'>{$numrows}</b> 个订单，其中已完成的有 <b class='text-emerald-600 text-sm mx-1 font-mono'>{$ondate}</b> 个，正在处理的有 <b class='text-emerald-600 text-sm mx-1 font-mono'>{$ondate2}</b> 个。";
	}
	$link = $links;
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部看板与搜索区域 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col xl:flex-row xl:items-center justify-between gap-6">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <?php echo $header_title; ?>
                    <?php if(isset($_GET['kw']) || isset($_GET['type']) || isset($_GET['starttime'])){ ?>
                        <a href="list.php" class="ml-4 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors shadow-sm">查看全部</a>
                    <?php } ?>
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-1.5"><?php echo $header_desc; ?></p>
            </div>
            
            <!-- 搜索表单布局修复：使用 flex-wrap 且各项设置合理 min-width 避免挤压 -->
            <div class="bg-white p-2 rounded-xl border border-slate-200 shadow-sm w-full xl:w-auto">
                <form action="./list.php" method="GET" class="flex flex-wrap items-center gap-2">
                    
                    <!-- 关键词搜索 -->
                    <div class="relative flex-grow min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class="fa fa-search"></i></div>
                        <input type="text" class="w-full h-10 pl-9 pr-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 placeholder:font-normal" name="kw" placeholder="请输入下单账号或订单号" value="<?php echo htmlspecialchars(@$_GET['kw']); ?>">
                    </div>

                    <!-- 日期区间选择 (恢复 datepicker) -->
                    <div class="flex items-center gap-1 flex-grow sm:flex-grow-0 input-daterange" id="datepicker">
                        <input type="text" id="starttime" name="starttime" class="w-[110px] sm:w-[130px] h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" placeholder="开始日期" autocomplete="off" title="留空则不限时间范围" value="<?php echo htmlspecialchars(@$_GET["starttime"], ENT_QUOTES, 'UTF-8');?>">
                        <span class="text-slate-400 text-xs px-1 shrink-0"><i class="fa fa-chevron-right"></i></span>
                        <input type="text" id="endtime" name="endtime" class="w-[110px] sm:w-[130px] h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" placeholder="结束日期" autocomplete="off" title="留空则不限时间范围" value="<?php echo htmlspecialchars(@$_GET["endtime"], ENT_QUOTES, 'UTF-8');?>">
                        <button type="button" onclick="$('#starttime').val('');$('#endtime').val('');" class="w-10 h-10 ml-1 flex items-center justify-center rounded-lg bg-rose-50 text-rose-500 border border-rose-100 hover:bg-rose-500 hover:text-white transition-colors shrink-0" title="清除时间"><i class="fa fa-trash"></i></button>
                    </div>

                    <!-- 状态筛选与操作按钮 -->
                    <div class="flex items-center gap-2 flex-grow sm:flex-grow-0">
                        <select name="type" class="h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white text-slate-700 cursor-pointer min-w-[120px]">
                            <option value="-1" <?php if(@$_GET['type']=='-1') echo 'selected'; ?>>全部状态</option>
                            <option value="0" <?php if(@$_GET['type']=='0') echo 'selected'; ?>>待处理</option>
                            <option value="2" <?php if(@$_GET['type']=='2') echo 'selected'; ?>>正在处理</option>
                            <option value="1" <?php if(@$_GET['type']=='1') echo 'selected'; ?>>已完成</option>
                            <option value="3" <?php if(@$_GET['type']=='3') echo 'selected'; ?>>异常</option>
                            <option value="4" <?php if(@$_GET['type']=='4') echo 'selected'; ?>>已退款</option>
                        </select>
                        <button type="submit" class="h-10 px-4 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-md shadow-indigo-500/20 active:scale-95 transition-all shrink-0"><i class="fa fa-search mr-1.5"></i>搜索</button>
                        <button type="button" onclick="showStatusRule()" class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200 transition-colors shrink-0" title="订单状态说明"><i class="fa fa-question-circle"></i></button>
                    </div>
                    
                </form>
            </div>
        </div>

        <!-- 订单列表网格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-24">操作</th>
                        <th class="px-5 py-4 font-semibold text-center">订单ID</th>
                        <th class="px-5 py-4 font-semibold">商品名称</th>
                        <th class="px-5 py-4 font-semibold min-w-[200px]">下单信息</th>
                        <th class="px-5 py-4 font-semibold text-center">份数</th>
                        <th class="px-5 py-4 font-semibold text-center">下单时间</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
						
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT A.*,B.name FROM pre_orders A left join pre_tools B on A.tid=B.tid WHERE" . $sql . " ORDER BY id DESC LIMIT " . $offset . "," . $pagesize);
$hasData = false;

while ($res = $rs->fetch()) {
    $hasData = true;
	if ($res["djzt"] == 3 && $res["userid"] != $suprow["sid"]) {
		$input = "****" . substr($res["input"], 4);
	} else {
		$input = $res["input"];
	}
	
	echo "<tr class='hover:bg-slate-50/50 transition-colors group'>";
	
    // 操作
    echo "<td class='px-5 py-3.5 text-center'>";
    if($res["userid"] == $suprow["sid"]){
        echo "<a href=\"javascript:showOrder(" . $res["id"] . ",'" . md5($res["id"] . SYS_KEY . $res["id"]) . "')\" title=\"查看订单详细\" class=\"px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors text-xs font-bold shadow-sm\">详细</a>";
    }else{
        echo "<span class=\"px-3 py-1.5 rounded-lg bg-slate-50 text-slate-400 border border-slate-100 cursor-not-allowed text-xs font-bold\" title=\"不是你支付的订单\">详细</span>";
    }
    echo "</td>";

	// 订单ID
	echo "<td class='px-5 py-3.5 text-center'>
            <span class='px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-mono font-bold text-[11px] border border-slate-200'>{$res['id']}</span>
          </td>";
          
    // 商品名称
    echo "<td class='px-5 py-3.5'>
            <span class='font-bold text-slate-700 max-w-[200px] truncate block' title='".htmlspecialchars($res['name'])."'>{$res['name']}</span>
          </td>";
          
    // 下单信息
    echo "<td class='px-5 py-3.5 font-mono text-slate-500'>
            <div class='max-w-[300px] truncate' title='".htmlspecialchars($input)."'>{$input}</div>
          </td>";
          
    // 份数
    echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-indigo-600'>
            {$res['value']}
          </td>";
          
    // 下单时间
    echo "<td class='px-5 py-3.5 text-center'>
            <span class='text-[11px] font-mono text-slate-400'>{$res['addtime']}</span>
          </td>";
          
    // 状态
    echo "<td class='px-5 py-3.5 text-center'>
            ".display_zt($res['status'])."
          </td>";
          
    echo "</tr>";
}

if(!$hasData){
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">暂无订单数据</p>
          </td></tr>';
}
?>
                </tbody>
            </table>
        </div>

        <!-- 分页栏 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
                <?php
                $first = 1;
                $prev  = $page - 1;
                $next  = $page + 1;
                $last  = $pages;
                
                if ($page > 1) {
                    echo '<a href="list.php?page=' . $first . $link . '" class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold" title="首页">首页</a>';
                    echo '<a href="list.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="上一页"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold">首页</span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                $start = $page - 4 > 1 ? $page - 4 : 1;
                $end   = $page + 4 < $pages ? $page + 4 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="list.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="list.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                if ($page < $pages) {
                    echo '<a href="list.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下一页"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="list.php?page=' . $last . $link . '" class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold" title="尾页">尾页</a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold">尾页</span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>

    </div>
    
    <!-- 隐藏舱：订单状态说明 -->
    <div id="statusRuleContent" style="display: none;" class="p-6">
        <div class="text-sm text-slate-600 leading-relaxed font-medium bg-slate-50 p-4 rounded-xl border border-slate-100">
            <?php echo $conf["gg_search"];?>
        </div>
    </div>

</div>

<?php 
include "./foot.php";
?>

<script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.zh-CN.min.js"></script>

<script>
// 弹窗：订单状态说明
function showStatusRule() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-info-circle text-sky-500 mr-2"></i>订单状态说明</div>',
        area: [$(window).width() > 450 ? '450px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#statusRuleContent')
    });
}

// 订单详细信息
function showOrder(id,skey){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	var statusHtml = [
	    '<span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 font-bold border border-sky-100 text-[11px]">待处理</span>',
	    '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold border border-emerald-100 text-[11px]">已完成</span>',
	    '<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 font-bold border border-amber-100 text-[11px]">正在处理</span>',
	    '<span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold border border-rose-100 text-[11px]">异常</span>',
	    '<span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold border border-slate-200 text-[11px]">已退款</span>'
	];
	
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=order",
		data : {id:id,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var item = '<div class="p-6 bg-slate-50 rounded-2xl"><div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">';
				item += '<table class="w-full text-sm text-left text-slate-600">';
				item += '<tbody>';
				
				item += '<tr class="border-b border-slate-50"><th colspan="2" class="px-4 py-2 font-bold text-slate-700 text-center text-sm bg-slate-50">订单基本信息</th></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单编号</th><td class="px-4 py-3 font-mono font-bold text-indigo-600">'+id+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">商品名称</th><td class="px-4 py-3 font-bold text-slate-700">'+data.name+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单金额</th><td class="px-4 py-3 font-mono font-bold text-slate-700">'+data.money+'元</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">购买时间</th><td class="px-4 py-3 font-mono text-xs text-slate-400">'+data.date+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">下单信息</th><td class="px-4 py-3 font-mono text-xs text-slate-500 break-all">'+data.inputs+'</td></tr>';
				item += '<tr><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单状态</th><td class="px-4 py-3">'+statusHtml[data.status]+'</td></tr>';
				
				if(data.complain){
				    item += '<tr class="border-t border-slate-200 bg-rose-50/30"><th colspan="2" class="px-4 py-2 font-bold text-rose-700 text-center text-xs tracking-widest"><i class="fa fa-legal mr-1"></i>订单操作</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-center"><div class="flex justify-center gap-2">';
				    item += '<a href="./workorder.php?my=add&orderid='+id+'&skey='+skey+'" target="_blank" onclick="return checklogin('+data.islogin+')" class="px-4 py-1.5 rounded-lg bg-rose-500 text-white text-xs font-bold hover:bg-rose-600 transition-colors shadow-sm">投诉订单</a>';
					
					if(data.selfrefund == 1 && data.islogin == 1 && (data.status == 0 || data.status == 3)){
						item += '<button onclick="return apply_refund('+id+',\''+skey+'\')" class="px-4 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold hover:bg-slate-900 transition-colors shadow-sm">申请退款</button>';
					}
					item += '</div></td></tr>';
				}
				
				if(data.list && data.list.order_state){
				    item += '<tr class="border-t border-slate-200 bg-amber-50/30"><th colspan="2" class="px-4 py-2 font-bold text-amber-700 text-center text-xs tracking-widest"><i class="fa fa-dashboard mr-1"></i>订单实时状态</th></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">下单数量</th><td class="px-4 py-2 font-mono font-bold text-slate-700">'+data.list.num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">初始数量</th><td class="px-4 py-2 font-mono text-slate-500">'+data.list.start_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">当前数量</th><td class="px-4 py-2 font-mono text-emerald-600 font-bold">'+data.list.now_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">订单状态</th><td class="px-4 py-2 font-bold text-indigo-600">'+data.list.order_state+'</td></tr>';
				}else if(data.kminfo){
				    item += '<tr class="border-t border-slate-200 bg-sky-50/30"><th colspan="2" class="px-4 py-2 font-bold text-sky-700 text-center text-xs tracking-widest">以下是你的卡密信息</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 font-mono text-xs text-slate-600 leading-relaxed break-all bg-sky-50/10">'+data.kminfo+'</td></tr>';
				}else if(data.result){
				    item += '<tr class="border-t border-slate-200 bg-slate-50/30"><th colspan="2" class="px-4 py-2 font-bold text-slate-600 text-center text-xs tracking-widest">处理结果</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-xs text-slate-500 leading-relaxed">'+data.result+'</td></tr>';
				}
				if(data.desc){
				    item += '<tr class="border-t border-slate-200 bg-slate-50/30"><th colspan="2" class="px-4 py-2 font-bold text-slate-600 text-center text-xs tracking-widest">商品简介</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-xs text-slate-500 leading-relaxed">'+data.desc+'</td></tr>';
				}
				
				item += '</tbody></table></div></div>';
				
				layer.open({
				  type: 1,
				  title: '<div class="font-bold text-slate-800"><i class="fa fa-list-alt text-indigo-500 mr-2"></i>订单详细信息</div>',
				  area: [$(window).width() > 500 ? '500px' : '95%', 'auto'],
				  skin: 'layui-layer-rim rounded-2xl',
				  shadeClose: true,
				  content: item
				});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		}
	});
}

function checklogin(islogin){
	if(islogin==1){
		return true;
	}else{
		var confirmobj = layer.confirm('<div class="text-center mt-2">为方便反馈处理结果，投诉订单前请先登录网站！</div>', {
		  title: '提示',
		  icon: 3,
		  btn: ['登录','注册','取消'],
		  skin: 'layui-layer-rim rounded-2xl'
		}, function(){
			window.location.href='./login.php';
		}, function(){
			window.location.href='./reg.php';
		}, function(){
			layer.close(confirmobj);
		});
		return false;
	}
}

function apply_refund(id,skey){
	var confirmobj = layer.confirm('待处理或异常状态订单可以申请退款，退款之后资金会退到用户余额，是否确认退款？', {
	  title: '确认退款',
	  icon: 0,
	  btn: ['确认退款','取消'],
	  skin: 'layui-layer-rim rounded-2xl'
	}, function(){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=apply_refund",
			data : {id:id,skey:skey},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					layer.alert('成功退款'+data.money+'元到余额！', {icon:1}, function(){ window.location.reload(); });
				}else{
					layer.alert(data.msg, {icon:2});
				}
			}
		});
	}, function(){
		layer.close(confirmobj);
	});
}

$(document).ready(function(){
	$('.input-datepicker, .input-daterange').datepicker({
        format: 'yyyy-mm-dd',
		autoclose: true,
        clearBtn: true,
        language: 'zh-CN'
    });
});
</script>
</body>
</html>
