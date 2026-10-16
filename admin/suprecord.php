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

<div class="w-full max-w-[1400px] mx-auto space-y-6 pb-10">

<?php 
if (isset($_GET["sid"])) {
	$sid = intval($_GET["sid"]);
	$sql = " sid=" . $sid;
	$link = "&sid=" . $sid;
} else {
	$sid = 0;
	$sql = " 1";
	$link = "";
}

$thtime = date("Y-m-d") . " 00:00:00";
$lastday = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
$income_today = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='提成' AND" . $sql . " AND addtime>'" . $thtime . "'");
$outcome_today = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='消费' AND" . $sql . " AND addtime>'" . $thtime . "'");
$income_lastday = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='提成' AND" . $sql . " AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
$outcome_lastday = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='消费' AND" . $sql . " AND addtime<'" . $thtime . "' AND addtime>'" . $lastday . "'");
if ($sid > 0) {
	$income_all = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='提成' AND" . $sql);
	$outcome_all = $DB->getColumn("SELECT sum(point) FROM pre_suppoints WHERE action='消费' AND" . $sql);
}
$numrows = $DB->getColumn("SELECT count(*) from pre_suppoints WHERE" . $sql);
?>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        <!-- 头部区域 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-list-alt text-brand-500 mr-2 text-lg"></i>
                <?php echo $sid > 0 ? "供货商 SID: <span class=\"text-brand-600 font-mono ml-2\">#" . $sid . "</span> " : "全局供货商";?> 资金流转明细
            </h2>
            <div class="flex items-center gap-3">
                <a href="suplist.php" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm text-sm shrink-0">
                    <i class="fa fa-reply mr-1.5"></i>返回供货商列表
                </a>
            </div>
        </div>

        <!-- 财务统计看板区 -->
        <div class="p-6 border-b border-slate-50">
            <div class="grid grid-cols-2 lg:grid-cols-4 <?php echo $sid > 0 ? 'xl:grid-cols-6' : ''; ?> gap-4">
                
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-emerald-500 rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-line-chart"></i></div>
                    <span class="text-xs font-semibold text-emerald-600 mb-1 z-10">今日提成收益 (元)</span>
                    <span class="text-xl font-mono font-bold text-emerald-600 z-10"><?php echo round((float)$income_today, 2);?></span>
                </div>

                <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-rose-500 -rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-shopping-cart"></i></div>
                    <span class="text-xs font-semibold text-rose-600 mb-1 z-10">今日总计消费 (元)</span>
                    <span class="text-xl font-mono font-bold text-rose-600 z-10"><?php echo round((float)$outcome_today, 2);?></span>
                </div>

                <div class="p-4 rounded-xl bg-teal-50 border border-teal-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-teal-500 rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-pie-chart"></i></div>
                    <span class="text-xs font-semibold text-teal-600 mb-1 z-10">昨日提成收益 (元)</span>
                    <span class="text-xl font-mono font-bold text-teal-600 z-10"><?php echo round((float)$income_lastday, 2);?></span>
                </div>

                <div class="p-4 rounded-xl bg-orange-50 border border-orange-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-orange-500 -rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-credit-card"></i></div>
                    <span class="text-xs font-semibold text-orange-600 mb-1 z-10">昨日总计消费 (元)</span>
                    <span class="text-xl font-mono font-bold text-orange-600 z-10"><?php echo round((float)$outcome_lastday, 2);?></span>
                </div>
                
                <?php if ($sid > 0) { ?>
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-blue-500 rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-database"></i></div>
                    <span class="text-xs font-semibold text-blue-600 mb-1 z-10">历史总计提成 (元)</span>
                    <span class="text-xl font-mono font-bold text-blue-600 z-10"><?php echo round((float)$income_all, 2);?></span>
                </div>

                <div class="p-4 rounded-xl bg-pink-50 border border-pink-100 flex flex-col relative overflow-hidden group hover:shadow-md transition-shadow">
                    <div class="absolute right-[-10px] bottom-[-10px] opacity-10 text-5xl text-pink-500 -rotate-12 group-hover:scale-110 transition-transform"><i class="fa fa-tags"></i></div>
                    <span class="text-xs font-semibold text-pink-600 mb-1 z-10">历史总计消费 (元)</span>
                    <span class="text-xl font-mono font-bold text-pink-600 z-10"><?php echo round((float)$outcome_all, 2);?></span>
                </div>
                <?php } ?>
            </div>
        </div>
        
        <!-- 表格数据区 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-20">单号 ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">供货商 SID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28">交易类型</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32">涉及金额 (元)</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[200px]">摘要详情</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32">关联订单</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_suppoints WHERE" . $sql . " order by id desc limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 收支状态高亮表现
    $isIncome = in_array($res["action"], array("提成", "奖励", "赠送", "退款", "退回", "充值", "加款"));
    $pointColor = $isIncome ? "text-emerald-500" : "text-rose-500";
    $pointPrefix = $isIncome ? "+" : "-";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700 font-mono\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <a href=\"suplist.php?sid=" . $res["sid"] . "\" class=\"text-sky-600 hover:text-sky-800 hover:underline font-bold\">#" . $res["sid"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center\">
                <span class=\"inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-semibold border border-slate-200\">" . $res["action"] . "</span>
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-base " . $pointColor . "\">
                " . $pointPrefix . $res["point"] . "
            </td>
            <td class=\"px-6 py-4 text-slate-600 max-w-[300px] truncate\" title=\"" . htmlspecialchars($res["bz"]) . "\">" . $res["bz"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono text-xs text-slate-500\">" . $res["addtime"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono\">
                " . ($res["orderid"] ? "<a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class=\"text-brand-600 hover:underline font-bold\">#" . $res["orderid"] . "</a>" : "<span class=\"text-slate-400\">无关联</span>") . "
            </td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-file-text-o text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前未检索到任何资金收支明细数据</p>
          </td></tr>';
}
?>
                </tbody>
            </table>
        </div>

        <!-- 分页控制区 -->
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
                echo "<li><a href=\"suprecord.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
                echo "<li><a href=\"suprecord.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
            }
            
            $start = $page - 10 > 1 ? $page - 10 : 1;
            $end = $page + 10 < $pages ? $page + 10 : $pages;
            
            for ($i = $start; $i < $page; $i++) {
                echo "<li><a href=\"suprecord.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            
            echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
            
            for ($i = $page + 1; $i <= $end; $i++) {
                echo "<li><a href=\"suprecord.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            
            if ($page < $pages) {
                echo "<li><a href=\"suprecord.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
                echo "<li><a href=\"suprecord.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
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
