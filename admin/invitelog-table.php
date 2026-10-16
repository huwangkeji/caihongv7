<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	$sql = " A.`qq`='" . $kw . "'";
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_invite A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE" . $sql);
	$con = "查单QQ <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 条推广记录";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["nid"])) {
	$nid = intval($_GET["nid"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_invite where nid='" . $nid . "'");
	$sql = " nid='" . $nid . "'";
	$con = "该商品共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 条推广记录";
	$link = "&nid=" . $nid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_invite");
	$sql = " 1";
	$con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 条推广记录";
}
?>
<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold text-center w-20">ID</th>
                <th class="px-6 py-3.5 font-semibold min-w-[150px]">商品名称</th>
                <th class="px-6 py-3.5 font-semibold text-center">查单QQ</th>
                <th class="px-6 py-3.5 font-semibold text-center">下单账号</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">奖励次数</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">进度</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">状态</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">操作</th>
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
$rs = $DB->query("SELECT A.*,B.`name` FROM `pre_invite` A LEFT JOIN `pre_tools` B ON A.`tid`=B.`tid` WHERE" . $sql . " ORDER BY A.`id` DESC LIMIT " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	if ($res["plan"] > 0) {
		$progress = "<span class=\"font-mono font-medium text-slate-600\">" . $res["click"] . "<span class=\"text-slate-400 mx-0.5\">/</span>" . $res["plan"] . "</span>";
	} else {
		$progress = "<span class=\"text-slate-400\">--</span>";
	}
	if ($res["status"] == 1) {
		$status = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">已完成</span>";
	} else {
		$status = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100\">进行中</span>";
	}
	$input_arr = explode("|", $res["input"]);
	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 font-bold text-brand-600 max-w-[200px] truncate\" title=\"" . $res["name"] . "\"><a href=\"./shoplist.php?tid=" . $res["tid"] . "\" class=\"hover:underline\">" . $res["name"] . "</a></td>
            <td class=\"px-6 py-4 text-center text-slate-700 font-medium\">" . $res["qq"] . "</td>
            <td class=\"px-6 py-4 text-center text-slate-600\">" . $input_arr[0] . "</td>
            <td class=\"px-6 py-4 text-center font-bold text-sky-500\">" . $res["count"] . "</td>
            <td class=\"px-6 py-4 text-center\">" . $progress . "</td>
            <td class=\"px-6 py-4 text-center\">" . $status . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <span class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"delInvite(" . $res["id"] . ")\">删除</span>
            </td>
        </tr>\r\n";
}

if($isEmpty) {
    echo '<tr><td colspan="8" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有符合条件的推广记录</p>
          </td></tr>';
}
?>
        </tbody>
    </table>
</div>

<!-- 底部通用分页器 -->
<?php if(!$isEmpty): ?>
<div class="py-5">
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

<script>
// 动态更新外层主页面的统计信息标题
if($("#blocktitle").length > 0) {
    $("#blocktitle").html('<?php echo $con;?>');
}
</script>
