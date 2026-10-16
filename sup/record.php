<?php

include "../includes/common.php";
$title = "收支明细";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$thtime = date("Y-m-d") . " 00:00:00";
$lastday = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
$income_today = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' AND action='提成' AND addtime>'" . $thtime . "'");
$outcome_today = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' AND action='消费' AND addtime>'" . $thtime . "'");
$income_lastday = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' AND action='提成' AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
$outcome_lastday = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' AND action='消费' AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

    <!-- 顶部数据概览卡片 -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 今日收益 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <i class="fa fa-tint text-3xl text-emerald-500 mb-3 opacity-90 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-2xl font-mono font-bold text-slate-800 tracking-tight m-0"><?php echo round($income_today, 2);?><span class="text-sm font-normal ml-1">元</span></h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">今日收益</p>
            </div>
        </div>
        
        <!-- 今日消费 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <i class="fa fa-check-square-o text-3xl text-sky-500 mb-3 opacity-90 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-2xl font-mono font-bold text-slate-800 tracking-tight m-0"><?php echo round($outcome_today, 2);?><span class="text-sm font-normal ml-1">元</span></h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">今日消费</p>
            </div>
        </div>
        
        <!-- 昨日收益 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <i class="fa fa-tint text-3xl text-emerald-400 mb-3 opacity-90 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-2xl font-mono font-bold text-slate-800 tracking-tight m-0"><?php echo round($income_lastday, 2);?><span class="text-sm font-normal ml-1">元</span></h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">昨日收益</p>
            </div>
        </div>
        
        <!-- 昨日消费 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <i class="fa fa-check-square-o text-3xl text-sky-400 mb-3 opacity-90 group-hover:scale-110 transition-transform"></i>
                <h3 class="text-2xl font-mono font-bold text-slate-800 tracking-tight m-0"><?php echo round($outcome_lastday, 2);?><span class="text-sm font-normal ml-1">元</span></h3>
                <p class="text-[11px] font-bold text-slate-400 mt-1">昨日消费</p>
            </div>
        </div>
    </div>

    <!-- 收支明细列表区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-list-alt text-indigo-500 mr-2 text-xl"></i>收支明细
            </h3>
        </div>
        
        <!-- 数据表格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-20">ID</th>
                        <th class="px-5 py-4 font-semibold text-center">类型</th>
                        <th class="px-5 py-4 font-semibold text-center">金额</th>
                        <th class="px-5 py-4 font-semibold min-w-[200px]">详情</th>
                        <th class="px-5 py-4 font-semibold text-center">时间</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$numrows = $DB->getColumn("SELECT count(*) from pre_suppoints WHERE sid='" . $suprow["sid"] . "'");
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' order by id desc limit " . $offset . "," . $pagesize);
$hasData = false;

while ($res = $rs->fetch()) {
    $hasData = true;
    // 判断颜色：收益类标红(rose)，消费类标绿(emerald)[span_0](start_span)[span_0](end_span)
    $isIncome = in_array($res["action"], array("提成", "奖励", "赠送", "退款", "退回", "充值", "加款"));
    $colorClass = $isIncome ? "text-rose-500" : "text-emerald-500";
    
	echo "<tr class='hover:bg-slate-50/50 transition-colors group'>";
    echo "<td class='px-5 py-3.5 text-center'><span class='font-mono font-bold text-slate-500'>{$res['id']}</span></td>";
    echo "<td class='px-5 py-3.5 text-center'><span class='px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold text-xs'>{$res['action']}</span></td>";
    echo "<td class='px-5 py-3.5 text-center font-mono font-bold {$colorClass}'>{$res['point']}</td>";
    echo "<td class='px-5 py-3.5 text-slate-600 max-w-[300px] truncate' title='".htmlspecialchars($res['bz'])."'>{$res['bz']}</td>";
    echo "<td class='px-5 py-3.5 text-center font-mono text-xs text-slate-400'>{$res['addtime']}</td>";
    echo "</tr>";
}

if(!$hasData){
    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500 m-0">暂无收支明细记录</p>
          </td></tr>';
}
?>
                </tbody>
            </table>
        </div>

        <!-- 分页包裹器 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
<?php 
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
$link = ''; // 若有附加链接参数，原版为空，保留占位

if ($page > 1) {
	echo "<a href=\"record.php?page=" . $first . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>首页</a>";
	echo "<a href=\"record.php?page=" . $prev . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&laquo;</a>";
} else {
	echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>首页</span>";
	echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&laquo;</span>";
}

$start = $page - 5 > 1 ? $page - 5 : 1;
$end = $page + 5 < $pages ? $page + 5 : $pages;

if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';

for ($i = $start; $i < $page; $i++) {
	echo "<a href=\"record.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
}

echo "<span class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30'>" . $page . "</span>";

for ($i = $page + 1; $i <= $end; $i++) {
	echo "<a href=\"record.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
}

if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';

if ($page < $pages) {
	echo "<a href=\"record.php?page=" . $next . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&raquo;</a>";
	echo "<a href=\"record.php?page=" . $last . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>尾页</a>";
} else {
	echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&raquo;</span>";
	echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>尾页</span>";
}
?>
            </nav>
        </div>
        <?php } ?>
   </div>
</div>

<?php 
include "./foot.php";
?>
</body>
</html>
