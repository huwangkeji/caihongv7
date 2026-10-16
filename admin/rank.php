<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('site', 1);
$title = "分站排行";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// 接收路由参数防报错处理
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$do = isset($_POST["do"]) ? $_POST["do"] : null;
$last = isset($_GET["last"]) ? $_GET["last"] : null;

?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
<?php 
if ($my == "setdo" && $do == "submit") {
	$rank_reward = $_POST["rank_reward"];
	$rank_percentage = $_POST["rank_percentage"];
	if ($rank_reward > 0) {
		if ($rank_percentage == null) {
			showmsg("奖励销量的比例不能为空", 3);
		} elseif ($rank_percentage < 0 || $rank_percentage > 50) {
			showmsg("请设置合理的奖励比例", 3);
		}
	}
	saveSetting("rank_reward", $rank_reward);
	saveSetting("rank_percentage", $rank_percentage);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} elseif ($my == "set") {
	$cron_lasttime = $DB->getColumn("SELECT `v` FROM `pre_config` WHERE `k` = 'cron_rank_time' LIMIT 1");
	$cron_money = $DB->getColumn("SELECT `v` FROM `pre_config` WHERE `k` = 'cron_rank_money' LIMIT 1");
	?>
    <!-- 奖励设置表单 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto mt-4">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-cogs text-brand-500 mr-2 text-lg"></i>分站排行奖励设置
            </h3>
            <a href="rank.php" class="inline-flex items-center justify-center px-4 h-8 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm text-sm">
                <i class="fa fa-reply mr-1.5"></i>返回排行
            </a>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./rank.php?my=setdo" method="post" role="form" class="space-y-6">
                <input type="hidden" name="do" value="submit"/>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">每日奖励前几名</label>
                    <input type="text" name="rank_reward" value="<?php echo $conf["rank_reward"] ?? '';?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="（填入数字，0或留空代表关闭奖励机制）"/>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">奖励销量的比例 <span class="font-normal text-slate-400 text-xs ml-1">(百分数)</span></label>
                    <div class="relative">
                        <input type="text" name="rank_percentage" value="<?php echo $conf["rank_percentage"] ?? '';?>" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="（例如填写 3 将额外奖励当日总销量的 3%）"/>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none font-bold">%</span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-slate-50">
                    <button type="submit" name="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存奖励参数
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- 统计状态卡片 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl mx-auto mt-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center">
            <div class="w-12 h-12 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa fa-calendar-times-o"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 mb-1">上次发放奖励时间</div>
                <div class="text-sm font-bold text-slate-800 font-mono"><?php echo $cron_lasttime ?: '暂无运行记录';?></div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mr-4 shrink-0">
                <i class="fa fa-money"></i>
            </div>
            <div>
                <div class="text-xs font-semibold text-slate-500 mb-1">上次发放奖励金额</div>
                <div class="text-sm font-bold text-emerald-600 font-mono">¥ <?php echo $cron_money ?: '0.00';?></div>
            </div>
        </div>
    </div>
    
    <!-- 外网 CRON 监控提示 -->
    <div class="max-w-3xl mx-auto mt-6 p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-700 text-sm flex items-center shadow-sm">
        <i class="fa fa-info-circle mr-2 text-brand-500 text-lg"></i>
        <div>
            <span class="font-bold">监控地址配置：</span>请配合宝塔等外部定时任务工具触发监控，<a href="./set.php?mod=cron" class="text-brand-600 hover:text-brand-800 underline font-medium ml-1">点此查看提取接口 URL</a>。
        </div>
    </div>

<?php 
} else {
	$thtime = date("Y-m-d") . " 00:00:00";
	$lastday = date("Y-m-d", strtotime("-1 day")) . " 00:00:00";
	$limit = ($conf["rank_reward"] ?? 0) > 10 ? ($conf["rank_reward"] ?? 0) : 10;
    
	if ($last == 1) {
		$sql = "select a.zid,(select b.sitename from pre_site as b where a.zid=b.zid) as sitename,count(id) as count,sum(money) as money from pre_orders as a where addtime>'" . $lastday . "' and addtime<'" . $thtime . "' and zid>1 group by zid order by money desc limit " . $limit;
		$addstr = "已发放奖励";
	} else {
		$sql = "select a.zid,(select b.sitename from pre_site as b where a.zid=b.zid) as sitename,count(id) as count,sum(money) as money from pre_orders as a where addtime>'" . $thtime . "' and zid>1 group by zid order by money desc limit " . $limit;
		$addstr = "预计发放奖励";
	}
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        <!-- 头部标题 -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-bar-chart text-brand-500 mr-2 text-lg"></i>分站销售排行
            </h3>
        </div>
        
        <!-- 平滑分段式导航栏 -->
        <div class="p-6 border-b border-slate-50">
            <div class="flex p-1 space-x-1 bg-slate-100/80 rounded-xl max-w-2xl mx-auto shadow-inner">
                <a href="rank.php" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 text-center <?php echo $last != 1 ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                    今日销售排行
                </a>
                <a href="rank.php?last=1" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 text-center <?php echo $last == 1 ? 'bg-white text-brand-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                    昨日销售排行
                </a>
                <a href="rank.php?my=set" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all duration-200 text-center text-slate-500 hover:text-slate-700 hover:bg-slate-200/50">
                    <i class="fa fa-cog mr-1"></i>奖励设置
                </a>
            </div>
        </div>

        <!-- 排名数据表格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">综合排名</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32">分站 ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center min-w-[200px]">分站名称</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32 text-sky-600">总订单数</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40 text-emerald-600">销售总额</th>
                        <?php echo ($conf["rank_reward"] ?? 0) > 0 ? "<th class=\"px-6 py-3.5 font-semibold text-center w-40 text-rose-500\">" . $addstr . "</th>" : null;?>
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
                    <a href=\"sitelist.php?zid=" . $res["zid"] . "\" target=\"_blank\" class=\"text-brand-600 hover:underline\">#" . $res["zid"] . "</a>
                </td>
                <td class=\"px-6 py-4 text-center font-bold text-slate-800\">" . mb_substr($res["sitename"], 0, 10, "utf-8") . "</td>
                <td class=\"px-6 py-4 text-center font-bold text-sky-500 font-mono text-base\">" . $res["count"] . "</td>
                <td class=\"px-6 py-4 text-center font-bold text-emerald-500 font-mono text-base\">
                    <span class=\"text-xs font-normal text-slate-400 mr-0.5\">¥</span>" . $res["money"] . "
                </td>";
                
		if (($conf["rank_reward"] ?? 0) > 0) {
			if ($i <= ($conf["rank_reward"] ?? 0)) {
				$reward = round($res["money"] * ($conf["rank_percentage"] ?? 0) / 100, 2);
			} else {
				$reward = 0;
			}
			echo "<td class=\"px-6 py-4 text-center font-bold text-rose-500 font-mono text-base\"><span class=\"text-xs font-normal text-slate-400 mr-0.5\">¥</span>" . $reward . "</td>";
		}
		echo "</tr>";
		$i++;
	}
    
    // 无数据时的缺省回显
    if($isEmpty) {
        $colspan = ($conf["rank_reward"] ?? 0) > 0 ? 6 : 5;
        echo "<tr><td colspan=\"{$colspan}\" class=\"px-6 py-20 text-center text-slate-400\">
                <i class=\"fa fa-bar-chart text-5xl mb-4 text-slate-200 block\"></i>
                <p class=\"text-sm font-medium text-slate-500\">该时间段内暂无任何分站的销售数据</p>
              </td></tr>";
    }
?>
                </tbody>
            </table>
        </div>
    </div>
<?php 
}
?> 
</div>
</body>
</html>
