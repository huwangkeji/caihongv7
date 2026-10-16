<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);
$orderby = "sid desc";
if (isset($_GET["sid"])) {
	$sql = " sid=" . $_GET["sid"];
} elseif (isset($_GET["kw"])) {
	$sql = " (user='" . $_GET["kw"] . "' or qq='" . $_GET["kw"] . "' or zid='" . $_GET["kw"] . "')";
	$link = "&kw=" . $_GET["kw"];
} elseif (isset($_GET["sort"])) {
	$sql = " 1";
	if ($_GET["sort"] == "0") {
		$orderby = "rmb asc";
	} elseif ($_GET["sort"] == "1") {
		$orderby = "rmb desc";
	}
	$link = "&sort=" . $_GET["sort"];
} else {
	$sql = " 1";
}
$numrows = $DB->getColumn("SELECT count(*) from pre_supplier where" . $sql);
?>
<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold text-center w-16">UID</th>
                <th class="px-6 py-3.5 font-semibold min-w-[120px]">登录用户名</th>
                <th class="px-6 py-3.5 font-semibold text-center w-32">联系 QQ</th>
                <th class="px-6 py-3.5 font-semibold text-center w-28 text-emerald-600">资金余额 (元)</th>
                <th class="px-6 py-3.5 font-semibold text-center w-44">注册 / 合约到期时间</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">营业状态</th>
                <th class="px-6 py-3.5 font-semibold text-center w-64">综合管理操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = 30;
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_supplier WHERE" . $sql . " order by " . $orderby . " limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 渲染：状态控制
    $statusBadge = $res["status"] == 1 
        ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer shadow-sm\" onclick=\"setActive(" . $res["sid"] . ",0)\">开启正常</span>" 
        : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer shadow-sm\" onclick=\"setActive(" . $res["sid"] . ",1)\">关闭停业</span>";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700 font-mono\">" . $res["sid"] . "</td>
            <td class=\"px-6 py-4 font-bold text-slate-800\">" . htmlspecialchars($res["user"]) . "</td>
            <td class=\"px-6 py-4 text-center font-mono text-slate-500\"><i class=\"fa fa-qq mr-1\"></i>" . htmlspecialchars($res["qq"]) . "</td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-emerald-600 text-base\">
                <a href=\"javascript:showRecharge(" . $res["sid"] . ")\" title=\"点击快速充值或扣款\" class=\"hover:underline\">¥ " . $res["rmb"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center\">
                <div class=\"flex flex-col gap-1\">
                    <span class=\"text-xs text-slate-500 font-mono\" title=\"创建时间\">起: " . $res["addtime"] . "</span>
                    <a href=\"javascript:setEndtime(" . $res["sid"] . ")\" title=\"点击续期或修改时间\" class=\"text-xs font-mono font-medium text-rose-500 hover:underline\">止: " . $res["endtime"] . "</a>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center\">" . $statusBadge . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center gap-1.5 flex-wrap justify-center max-w-[250px]\">
                    <a href=\"./suplist.php?my=edit&sid=" . $res["sid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
                    <a href=\"./list.php?sid=" . $res["sid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">订单</a>
                    <a href=\"./suprecord.php?sid=" . $res["sid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">明细</a>
                    <a href=\"./sso.php?sid=" . $res["sid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">免密登录</a>
                    <a href=\"javascript:delUser(" . $res["sid"] . ")\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">删除</a>
                </div>
            </td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-truck text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前条件下未检索到任何供货商数据</p>
          </td></tr>';
}
?>
        </tbody>
    </table>
</div>

<!-- 底部分页控制区 -->
<?php if(!$isEmpty): ?>
<div class="py-5 flex justify-center border-t border-slate-100 bg-slate-50/50 rounded-b-xl">
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
