<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("site", 1);
$zid = intval($_GET["zid"] ?? 0);
$price_obj = new \lib\Price($zid);

if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	// P2-5：转义 LIKE 通配符 % 和 _，与 shoplist-table.php 保持一致
	$kw_esc = addcslashes($kw, '%_');
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where name LIKE '%" . $kw_esc . "%' ESCAPE '\\\\'");
	$sql = " name LIKE '%" . $kw_esc . "%' ESCAPE '\\\\'";
	$con = "包含 <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["cid"])) {
	$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
	$select = "<option value=\"0\">未分类</option>";
	$shua_class[0] = "未分类";
	while ($res = $rs->fetch()) {
		$shua_class[$res["cid"]] = $res["name"];
		$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
	}
	$cid = intval($_GET["cid"]);
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where cid='" . $cid . "'");
	$sql = " cid='" . $cid . "'";
	$con = "分类 <a href=\"../?cid=" . $cid . "\" target=\"_blank\" class=\"text-brand-600 hover:underline font-bold mx-1\">" . htmlspecialchars($shua_class[$cid]) . "</a> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&cid=" . $cid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools");
	$sql = " 1";
	$con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
}
?>

<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold min-w-[200px]">商品名称</th>
                <th class="px-6 py-3.5 font-semibold text-center w-40">当前分站成本价 (底价)</th>
                <th class="px-6 py-3.5 font-semibold text-center w-40 text-brand-600">自定义独立密价</th>
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
$rs = $DB->query("SELECT * FROM pre_tools WHERE" . $sql . " order by sort asc limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	$price_obj->setToolInfo($res["tid"], $res);
	if ($price_obj->getPower() == 2) {
		$price = $price_obj->getToolCost2($res["tid"]);
	} else {
		$price = $price_obj->getToolCost($res["tid"]);
	}
	$iprice = $price_obj->getTooliPrice($res["tid"]);
    
    // 密价渲染表现
    $ipriceHtml = $iprice > 0 
        ? "<span class=\"text-sky-600 font-bold font-mono text-base\">" . $iprice . "</span> <span class=\"text-xs text-slate-400\">元</span>" 
        : "<span class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors border border-emerald-100\">点击设置</span>";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 font-bold text-slate-800 max-w-[350px] truncate\" title=\"" . htmlspecialchars($res["name"]) . "\">" . $res["name"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-slate-600 text-base\">
                <span class=\"text-xs font-normal text-slate-400 mr-0.5\">¥</span>" . $price . "
            </td>
            <td class=\"px-6 py-4 text-center\">
                <a title=\"为该分站单独设置密价\" href=\"javascript:setPrice(" . $res["tid"] . ",'" . $iprice . "')\">" . $ipriceHtml . "</a>
            </td>
          </tr>\r\n";
}

if($isEmpty) {
    echo '<tr><td colspan="3" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前条件下未检索到任何商品数据</p>
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

<!-- 动态通知父级窗口更新标题 -->
<script>
if($("#blocktitle").length > 0) {
    $("#blocktitle").html('<?php echo $con;?>');
}
</script>
