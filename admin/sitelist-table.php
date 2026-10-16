<?php
// 显示所有错误
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);
$orderby = "zid desc";
$params = array();

// 验证排序字段，防止SQL注入
$allowed_orderby = array('zid desc', 'zid asc', 'rmb asc', 'rmb desc');

if (isset($_GET["zid"])) {
	$zid = intval($_GET["zid"]);
	$power = $DB->getRow("SELECT power FROM pre_site WHERE zid=:zid limit 1", array(':zid' => $zid));
	if ($power && $power["power"] == 0) {
		exit("<script language='javascript'>top.location.href='./userlist.php?zid=" . $zid . "';</script>");
	}
	$sql = " zid=:zid and power>0";
	$params[':zid'] = $zid;
	$link = "&zid=" . $zid;
} elseif (isset($_GET["power"])) {
	$power = intval($_GET["power"]);
	$sql = " power=:power";
	$params[':power'] = $power;
	$link = "&power=" . $power;
} elseif (isset($_GET["mod"])) {
	$params = array(); // 初始化params数组
	if ($_GET["mod"] == "mj") {
		$sql = " iprice!=''";
		$link = "&mod=mj";
	} elseif ($_GET["mod"] == "1") {
		// 处理 mod=1 的情况，根据实际需求调整条件
		$sql = " power>0";
		$link = "&mod=1";
	} else {
		// 默认情况，防止$sql变量未定义
		$sql = " power>0";
		$link = "&mod=" . $_GET["mod"];
	}
} elseif (isset($_GET["sort"])) {
	$sql = " power>0";
	if ($_GET["sort"] == "0") {
		$orderby = "rmb asc";
	} elseif ($_GET["sort"] == "1") {
		$orderby = "rmb desc";
	}
	$link = "&sort=" . $_GET["sort"];
} elseif (isset($_GET["kw"])) {
	$kw = $_GET["kw"];
	$sql = " (user=:kw or zid=:kw_zid or domain=:kw or domain2=:kw or qq=:kw) and power>0";
	$params[':kw'] = $kw;
	$params[':kw_zid'] = intval($kw); // 尝试将关键词转换为整数用于zid查询
	$link = "&kw=" . urlencode($kw);
} else {
	$sql = " power>0";
	$params = array(); // 初始化params数组
	$link = "";
}

// 确保orderby是允许的值，防止SQL注入
if (!in_array($orderby, $allowed_orderby)) {
	$orderby = "zid desc";
}

$numrows = $DB->getColumn("SELECT count(*) from pre_site where" . $sql, $params);
?>
<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-y border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold text-center w-16">ZID</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">站点类型</th>
                <th class="px-6 py-3.5 font-semibold min-w-[120px]">管理员账号</th>
                <th class="px-6 py-3.5 font-semibold min-w-[150px]">站点名称 / 站长QQ</th>
                <th class="px-6 py-3.5 font-semibold text-center w-28 text-emerald-600">总余额 (元)</th>
                <th class="px-6 py-3.5 font-semibold text-center w-36">开通 / 到期时间</th>
                <th class="px-6 py-3.5 font-semibold min-w-[180px]">绑定域名网络</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">营业状态</th>
                <th class="px-6 py-3.5 font-semibold text-center w-64">综合操作</th>
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

// 直接将limit和offset拼接到SQL语句中（MySQL PDO不支持将limit和offset作为参数）	
$rs = $DB->query("SELECT * FROM pre_site WHERE" . $sql . " order by " . $orderby . " limit " . $offset . "," . $pagesize, $params);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    
    // 渲染：类型标签
    $powerBadge = $res["power"] == 2 
        ? "<span onclick=\"setSuper(" . $res["zid"] . ")\" title=\"点击转换站点层级类型\" class=\"inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-500 hover:text-white transition-colors cursor-pointer shadow-sm\">专业版</span>" 
        : "<span onclick=\"setSuper(" . $res["zid"] . ")\" title=\"点击转换站点层级类型\" class=\"inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100 hover:bg-sky-500 hover:text-white transition-colors cursor-pointer shadow-sm\">普及版</span>";

    // 渲染：状态控制
    $statusBadge = $res["status"] == 1 
        ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["zid"] . ",0)\">开启正常</span>" 
        : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["zid"] . ",1)\">关闭停业</span>";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700 font-mono\">" . $res["zid"] . "</td>
            <td class=\"px-6 py-4 text-center\">" . $powerBadge . "</td>
            <td class=\"px-6 py-4 font-bold text-slate-800\">" . $res["user"] . "</td>
            <td class=\"px-6 py-4\">
                <div class=\"flex flex-col gap-1\">
                    <span class=\"font-bold text-slate-700 truncate max-w-[150px]\" title=\"" . htmlspecialchars($res["sitename"]) . "\">" . $res["sitename"] . "</span>
                    <span class=\"text-xs text-slate-400 font-mono\"><i class=\"fa fa-qq mr-1\"></i>" . ($res["qq"] ?: "未设置") . "</span>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-emerald-600 text-base\">
                <a href=\"javascript:showRecharge(" . $res["zid"] . ")\" title=\"点击立即发起资金充值或扣款\" class=\"hover:underline\">¥ " . $res["rmb"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center\">
                <div class=\"flex flex-col gap-1\">
                    <span class=\"text-xs text-slate-500 font-mono\" title=\"建站开通时间\">起: " . date("Y-m-d", strtotime($res["addtime"])) . "</span>
                    <a href=\"javascript:setEndtime(" . $res["zid"] . ")\" title=\"点击延期/修改到期时间\" class=\"text-xs font-mono font-medium text-rose-500 hover:underline\">止: " . date("Y-m-d", strtotime($res["endtime"])) . "</a>
                </div>
            </td>
            <td class=\"px-6 py-4\">
                <div class=\"flex flex-col gap-1 text-xs font-mono font-medium\">
                    <a href=\"http://" . $res["domain"] . "\" target=\"_blank\" rel=\"noreferrer\" class=\"text-sky-600 hover:text-sky-800 hover:underline\"><i class=\"fa fa-globe mr-1\"></i>" . $res["domain"] . "</a>
                    " . ($res["domain2"] ? "<a href=\"http://" . $res["domain2"] . "\" target=\"_blank\" rel=\"noreferrer\" class=\"text-slate-500 hover:text-slate-700 hover:underline\"><i class=\"fa fa-link mr-1\"></i>" . $res["domain2"] . "</a>" : "") . "
                </div>
            </td>
            <td class=\"px-6 py-4 text-center\">" . $statusBadge . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center gap-1.5 flex-wrap justify-center max-w-[200px]\">
                    <a href=\"./sitelist.php?my=edit&zid=" . $res["zid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
                    <a href=\"./list.php?zid=" . $res["zid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">订单</a>
                    <a href=\"./record.php?zid=" . $res["zid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">明细</a>
                    <a href=\"./sso.php?zid=" . $res["zid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">一键登录</a>
                    <a href=\"javascript:delSite(" . $res["zid"] . ")\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">删除</a>
                </div>
            </td>
          </tr>";
}

// 缺省状态处理
if($isEmpty) {
    echo '<tr><td colspan="9" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-sitemap text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前条件下未检索到任何分站数据</p>
          </td></tr>';
}
?>
        </tbody>
    </table>
</div>

<!-- 底部分页控制区 -->
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
