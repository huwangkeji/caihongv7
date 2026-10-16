<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);

if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$sql = " B.name LIKE '%" . $kw . "%'";
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_sitetask A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql);
	$con = "包含 <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["id"])) {
	$id = intval($_GET["id"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_sitetask where id='" . $id . "'");
	$sql = " id='" . $id . "'";
	$con = "分站任务列表";
	$link = "&id=" . $id;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_sitetask");
	$sql = " 1";
	$con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个分站任务";
}
?>
<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold text-center w-16">ID</th>
                <th class="px-6 py-3.5 font-semibold min-w-[150px]">对客任务名称</th>
                <th class="px-6 py-3.5 font-semibold text-center w-36">核心任务类型</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24" title="数字越小越靠前">排序权重 <i class="fa fa-question-circle-o text-slate-400 font-normal"></i></th>
                <th class="px-6 py-3.5 font-semibold text-center w-48 text-emerald-600">目标阈值 / 奖励金额</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">当前状态</th>
                <th class="px-6 py-3.5 font-semibold text-center w-36">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_sitetask WHERE" . $sql . " ORDER BY sort ASC LIMIT " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 修正了原版源码 $row 和 $res 变量不匹配的 BUG
	$type = "今日";
	if ($res["task"] == 1) {
		$type = "总计";
	}
	$tasktype = sitetask_type($res["task"]);
	if ($res["task"] == 5) {
		$tasktype = $tasktype . "任务";
	} else {
		$tasktype = $type . $tasktype . "任务";
	}
    
    // 渲染：条件与奖励金额展示重构
    $condition = "<div class=\"flex flex-col items-center gap-1 font-mono\">
                    <span class=\"text-xs text-slate-500\">目标: <strong class=\"text-slate-700\">" . $res["value"] . "</strong></span>
                    <span class=\"text-xs text-slate-500\">奖励: <strong class=\"text-emerald-500\">¥ " . $res["money"] . "</strong></span>
                  </div>";

    // 渲染：状态控制
    $statusBadge = $res["active"] == 1 
        ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["id"] . ",0)\">显示正常</span>" 
        : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏停用</span>";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700 font-mono\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 font-bold text-slate-800 max-w-[200px] truncate\" title=\"" . htmlspecialchars($res["name"]) . "\">" . $res["name"] . "</td>
            <td class=\"px-6 py-4 text-center\">
                <span class=\"inline-flex items-center px-2.5 py-1 rounded bg-sky-50 text-sky-600 border border-sky-100 text-xs font-semibold\">" . $tasktype . "</span>
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-600 font-mono\">" . $res["sort"] . "</td>
            <td class=\"px-6 py-4 text-center\">" . $condition . "</td>
            <td class=\"px-6 py-4 text-center\">" . $statusBadge . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center gap-1.5 justify-center\">
                    <a href=\"./sitetask.php?my=edit&id=" . $res["id"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑规则</a>
                    <span class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"delTask(" . $res["id"] . ")\">彻底删除</span>
                </div>
            </td>
          </tr>\n";
}

if($isEmpty) {
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">未检索到符合条件的任务规则数据</p>
          </td></tr>';
}
?>
        </tbody>
    </table>
</div>

<!-- 底部分页控制区 -->
<?php if(!$isEmpty): ?>
<div class="py-5 flex justify-center">
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
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $first . $link . "')\" class=\"".$btn_normal."\">首页</a></li>";
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $prev . $link . "')\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
    } else {
        echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
        echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
    }
    
    $start = $page - 10 > 1 ? $page - 10 : 1;
    $end = $page + 10 < $pages ? $page + 10 : $pages;
    
    for ($i = $start; $i < $page; $i++) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
    }
    
    echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
    
    for ($i = $page + 1; $i <= $end; $i++) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
    }
    
    if ($page < $pages) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $next . $link . "')\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $last . $link . "')\" class=\"".$btn_normal."\">尾页</a></li>";
    } else {
        echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
        echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
    }
    ?>
    </ul>
</div>
<?php endif; ?>

<!-- 动态通知父级窗口更新总控标题 -->
<script>
if($("#blocktitle").length > 0) {
    $("#blocktitle").html('<?php echo $con;?>');
}
</script>
