<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('site', 1);
$title = "收支明细";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
<?php 
if (isset($_GET["zid"])) {
	$zid = intval($_GET["zid"]);
	$sql = " zid=" . $zid;
	$link = "&zid=" . $zid;
} else {
	$zid = 0;
	$sql = " 1";
    $link = "";
}
$thtime = date("Y-m-d") . " 00:00:00";
$lastday = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
$income_today = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='提成' AND" . $sql . " AND addtime>'" . $thtime . "'");
$outcome_today = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='消费' AND" . $sql . " AND addtime>'" . $thtime . "'");
$income_lastday = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='提成' AND" . $sql . " AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
$outcome_lastday = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='消费' AND" . $sql . " AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
$income_all = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='提成' AND" . $sql);
$outcome_all = $DB->getColumn("SELECT sum(point) FROM pre_points WHERE action='消费' AND" . $sql);
$numrows = $DB->getColumn("SELECT count(*) from pre_points WHERE" . $sql);
?>
    <!-- 页面标题 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-6 py-5 flex items-center justify-between">
        <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
            <i class="fa fa-pie-chart text-brand-500 mr-2 text-xl"></i>
            <?php echo $zid > 0 ? "分站 ZID: <span class=\"text-brand-600 font-mono mx-1.5\">" . $zid . "</span> " : "全部分站";?> 收支明细
        </h3>
        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows;?> 条明细</span>
    </div>

    <!-- 顶层数据统计卡片矩阵 -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-2"><i class="fa fa-line-chart"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">今日收益</div>
            <div class="text-rose-500 text-lg font-bold font-mono">¥<?php echo round($income_today, 2);?></div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-2"><i class="fa fa-shopping-cart"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">今日消费</div>
            <div class="text-emerald-500 text-lg font-bold font-mono">¥<?php echo round($outcome_today, 2);?></div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-2"><i class="fa fa-bar-chart"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">昨日收益</div>
            <div class="text-rose-500 text-lg font-bold font-mono">¥<?php echo round($income_lastday, 2);?></div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-2"><i class="fa fa-credit-card"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">昨日消费</div>
            <div class="text-emerald-500 text-lg font-bold font-mono">¥<?php echo round($outcome_lastday, 2);?></div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-2"><i class="fa fa-area-chart"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">总计收益</div>
            <div class="text-rose-500 text-lg font-bold font-mono">¥<?php echo round($income_all, 2);?></div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex flex-col items-center justify-center hover:-translate-y-1 transition-transform duration-300">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center mb-2"><i class="fa fa-database"></i></div>
            <div class="text-slate-500 text-xs font-semibold mb-1">总计消费</div>
            <div class="text-emerald-500 text-lg font-bold font-mono">¥<?php echo round($outcome_all, 2);?></div>
        </div>
    </div>

    <!-- 数据明细表格 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-20">ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">站点ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28">类型</th>
                        <th class="px-6 py-3.5 font-semibold text-right w-32">金额变动</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[200px]">详情备注</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">变动时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-36">关联订单号</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_points WHERE" . $sql . " order by id desc limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 金额颜色与符号格式化
    $isPositive = in_array($res["action"], array("提成", "奖励", "赠送", "退款", "退回", "充值", "加款"));
    $colorClass = $isPositive ? "text-rose-500" : "text-emerald-500";
    $sign = $isPositive ? "+" : "-";
    
	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 text-center font-bold text-brand-600\">
                <a href=\"sitelist.php?zid=" . $res["zid"] . "\" class=\"hover:underline\">" . $res["zid"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center font-medium text-slate-700\">
                <span class=\"px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-xs\">" . $res["action"] . "</span>
            </td>
            <td class=\"px-6 py-4 text-right font-mono font-bold {$colorClass} text-base\">
                " . $sign . " " . $res["point"] . "
            </td>
            <td class=\"px-6 py-4 text-slate-600 truncate max-w-[250px]\" title=\"" . htmlspecialchars($res["bz"]) . "\">" . $res["bz"] . "</td>
            <td class=\"px-6 py-4 text-center text-xs text-slate-500 font-mono\">" . $res["addtime"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono text-sm\">" . 
                ($res["orderid"] ? "<a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class=\"text-sky-500 hover:text-sky-700 hover:underline font-bold\">#" . $res["orderid"] . "</a>" : "<span class=\"text-slate-300\">无</span>") . 
            "</td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-file-text-o text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有符合条件的收支明细记录</p>
          </td></tr>';
}
?>                
                </tbody>
            </table>
        </div>
        
        <!-- 分页组件 -->
        <?php if(!$isEmpty): ?>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
            <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 m-0 list-none p-0">
            <?php 
            $first = 1;
            $prev = $page - 1;
            $next = $page + 1;
            $last = $pages;

            $btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
            $btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
            $btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

            if ($page > 1) {
                echo "<li><a href=\"record.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
                echo "<li><a href=\"record.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
            }
            $start = $page - 10 > 1 ? $page - 10 : 1;
            $end = $page + 10 < $pages ? $page + 10 : $pages;
            for ($i = $start; $i < $page; $i++) {
                echo "<li><a href=\"record.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
            for ($i = $page + 1; $i <= $end; $i++) {
                echo "<li><a href=\"record.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            if ($page < $pages) {
                echo "<li><a href=\"record.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
                echo "<li><a href=\"record.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
                echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
            }
            ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
