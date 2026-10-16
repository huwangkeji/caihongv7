<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);
$title = "商品销量排行";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$last = isset($_GET["last"]) ? $_GET["last"] : null;
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
<?php 
$thtime = date("Y-m-d") . " 00:00:00";
$lastday = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
if ($last == 1) {
	$sql = "SELECT B.tid,B.name,count(A.id) num FROM pre_orders A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE A.addtime>='" . $lastday . "' AND A.addtime<'" . $thtime . "' GROUP BY B.tid ORDER BY num DESC LIMIT 20";
} else {
	$sql = "SELECT B.tid,B.name,count(A.id) num FROM pre_orders A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE A.addtime>='" . $thtime . "' GROUP BY B.tid ORDER BY num DESC LIMIT 20";
}
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        <!-- 头部标题 -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-line-chart text-brand-500 mr-2 text-lg"></i>商品销量排行榜 (TOP 20)
            </h3>
        </div>
        
        <!-- 平滑分段式导航栏 -->
        <div class="p-6 border-b border-slate-50">
            <div class="flex p-1 space-x-1 bg-slate-100/80 rounded-xl max-w-lg mx-auto shadow-inner">
                <a href="shoprank.php" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 text-center <?php echo $last != 1 ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                    今日销量排行
                </a>
                <a href="shoprank.php?last=1" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 text-center <?php echo $last == 1 ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                    昨日销量排行
                </a>
            </div>
        </div>

        <!-- 排名数据表格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">综合排名</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32">商品 ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center min-w-[200px]">商品名称</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32 text-sky-600">总计订单数量</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$rs = $DB->query($sql);
$i = 1;
$isEmpty = true;

while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 前三名荣誉徽章样式分配
    $badgeClass = "bg-slate-100 text-slate-600 border border-slate-200";
    if($i == 1) $badgeClass = "bg-gradient-to-br from-amber-100 to-amber-200 text-amber-600 border border-amber-300 font-bold shadow-sm"; // 金牌
    elseif($i == 2) $badgeClass = "bg-gradient-to-br from-slate-100 to-slate-200 text-slate-600 border border-slate-300 font-bold shadow-sm"; // 银牌
    elseif($i == 3) $badgeClass = "bg-gradient-to-br from-orange-50 to-orange-100 text-orange-600 border border-orange-200 font-bold shadow-sm"; // 铜牌
    
    echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center\">
                <span class=\"inline-flex items-center justify-center w-7 h-7 rounded-full text-xs {$badgeClass}\">" . $i . "</span>
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-700\">
                <a href=\"./shopedit.php?my=edit&tid=" . $res["tid"] . "\" target=\"_blank\" class=\"text-brand-600 hover:underline\">#" . $res["tid"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-800 max-w-[400px] truncate\" title=\"" . htmlspecialchars($res["name"]) . "\">" . htmlspecialchars($res["name"]) . "</td>
            <td class=\"px-6 py-4 text-center font-bold text-sky-500 font-mono text-base\">" . $res["num"] . "</td>
          </tr>";
    $i++;
}

// 无数据时的缺省回显
if($isEmpty) {
    echo "<tr><td colspan=\"4\" class=\"px-6 py-20 text-center text-slate-400\">
            <i class=\"fa fa-bar-chart text-5xl mb-4 text-slate-200 block\"></i>
            <p class=\"text-sm font-medium text-slate-500\">该时间段内暂无任何商品的销售数据</p>
          </td></tr>";
}
?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
