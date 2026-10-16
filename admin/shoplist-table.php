<?php
include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$shequlist = $DB->getAll("SELECT id,url FROM pre_shequ order by id asc");
$shequurls = array();
foreach ($shequlist as $res) {
	$shequurls[$res["id"]] = $res["url"] . ($res["remark"] ? " (" . $res["remark"] . ")" : null);
}
$classlist = $DB->getAll("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">未分类</option>";
$shua_class[0] = "未分类";
foreach ($classlist as $res) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
if (isset($_SESSION["price_class"])) {
	$price_class = $_SESSION["price_class"];
} else {
	$pricelist = $DB->getAll("SELECT * FROM pre_price order by id asc");
	$price_class[0] = "不加价";
	foreach ($pricelist as $res) {
		$price_class[$res["id"]] = $res["name"];
	}
}
$pagesize = isset($_GET["num"]) ? intval($_GET["num"]) : 30;
$orderby = "A.tid desc";
if (isset($_GET["kw"])) {
	$kw = trim(daddslashes($_GET["kw"]));
	// P2-5：转义 LIKE 通配符 % 和 _，避免用户搜索含这些字符时变成全表匹配
	$kw_esc = addcslashes($kw, '%_');
	$sql = " A.name LIKE '%" . $kw_esc . "%' ESCAPE '\\\\'";
	if (is_numeric($kw)) {
		$sql .= " OR A.tid='" . $kw . "'";
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "包含 <span class=\"font-bold text-brand-600\">" . $kw . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["cid"])) {
	$cid = intval($_GET["cid"]);
	$sql = " A.cid='" . $cid . "'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	// cid 可能传入不存在的分类（越界 / 手改 URL），取不到名称时用兜底文案，避免 Undefined offset
	$classname = isset($shua_class[$cid]) ? $shua_class[$cid] : '未知分类(ID:' . $cid . ')';
	$con = "分类 <a href=\"../?cid=" . $cid . "\" target=\"_blank\" class=\"font-semibold text-brand-600 hover:underline\">" . $classname . "</a> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&cid=" . $cid;
	$orderby = "A.sort asc";
	// P2-4：分类下商品较多时避免一次性查出全部分页导致性能风险，设上限 1000 条
	if ($pagesize < $numrows) {
		$pagesize = min($numrows, 1000);
	}
} elseif (isset($_GET["prid"])) {
	$prid = intval($_GET["prid"]);
	$sql = " prid='" . $prid . "'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	// 同上：prid 可能不存在于加价模板列表
	$pricename = isset($price_class[$prid]) ? $price_class[$prid] : '未知模板(ID:' . $prid . ')';
	$con = "加价模板 <span class=\"font-semibold text-slate-700\">" . $pricename . "</span> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&prid=" . $prid;
} elseif (isset($_GET["tid"])) {
	$tid = intval($_GET["tid"]);
	$sql = " tid='" . $tid . "'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "商品列表";
	$link = "&tid=" . $tid;
} else {
	$sql = " 1";
	$link = "";
	$type_text = "";
	$status_text = "";
	
	if (isset($_GET["type"])) {
		$type = trim(daddslashes($_GET["type"]));
		if ($type == "1") {
			$sql .= " AND A.is_curl IN (1,2)";
			$type_text = "对接";
		} elseif ($type == "4") {
			$sql .= " AND A.is_curl=4";
			$type_text = "发卡";
		} elseif ($type == "other") {
			$sql .= " AND A.is_curl NOT IN (1,2,4)";
			$type_text = "自营";
		}
		if ($type_text) {
			$link .= "&type=" . $type;
		}
	}
	
	if (isset($_GET["status"])) {
		$status = trim(daddslashes($_GET["status"]));
		if ($status == "1") {
			$sql .= " AND A.active=1";
			$status_text = "显示";
		} elseif ($status == "0") {
			$sql .= " AND A.active=0";
			$status_text = "隐藏";
		} elseif ($status == "up") {
			$sql .= " AND A.close=0";
			$status_text = "上架";
		} elseif ($status == "down") {
			$sql .= " AND A.close=1";
			$status_text = "下架";
		}
		if ($status_text) {
			$link .= "&status=" . $status;
		}
	}
	
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	if ($type_text && $status_text) {
		$con = "类型为 <b class=\"text-brand-600\">" . $type_text . "</b> 且状态为 <b class=\"text-brand-600\">" . $status_text . "</b> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	} elseif ($type_text) {
		$con = "类型为 <b class=\"text-brand-600\">" . $type_text . "</b> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	} elseif ($status_text) {
		$con = "状态为 <b class=\"text-brand-600\">" . $status_text . "</b> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	} else {
		$con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	}
}
?>
<form name="form1" id="form1">
    <!-- 顶部提示信息栏 -->
    <div id="blocktitle-container" class="hidden"><?php echo $con;?></div>

    <!-- 响应式表格容器 -->
    <div class="w-full overflow-x-auto rounded-t-xl border-x border-t border-slate-100 shadow-sm bg-white scrollbar-thin scrollbar-thumb-slate-200">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap" id="shoplist">
          <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-4 py-3.5 font-semibold text-left w-2/5">商品名称</th>
                <th class="px-4 py-3.5 font-semibold text-center"><a href="./shoprank.php" class="text-slate-500 hover:text-brand-600 transition-colors">销量</a></th>
                <th class="px-4 py-3.5 font-semibold text-center">商品价格设置</th>
                <th class="px-4 py-3.5 font-semibold text-center">商品类型</th>
                <th class="px-4 py-3.5 font-semibold text-center <?php echo isset($_GET["cid"]) ? "hidden" : "";?>">所属分类</th>
                <th class="px-4 py-3.5 font-semibold text-center <?php echo isset($_GET["cid"]) ? "" : "hidden";?>">排序操作</th>
                <th class="px-4 py-3.5 font-semibold text-center">库存</th>
                <th class="px-4 py-3.5 font-semibold text-center">状态</th>
                <th class="px-4 py-3.5 font-semibold text-center">操作</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT A.*,B.name classname, (SELECT COUNT(*) FROM pre_pay WHERE tid=A.tid AND status=1) as sales FROM pre_tools A LEFT JOIN pre_class B ON A.cid=B.cid WHERE" . $sql . " order by " . $orderby . " limit " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
	if ($res["is_curl"] == 4) {
		$stock = "发卡";
	} elseif ($res["stock"] === null) {
		$stock = "无限";
	} else {
		$stock = $res["stock"];
	}
	
	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">";
    
    // 商品名称
	echo "<td class=\"px-4 py-3 max-w-[320px] whitespace-normal break-words\">
        <div class=\"flex items-start gap-2\">
            <input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["tid"] . "\" onClick=\"unselectall1()\" class=\"w-4 h-4 mt-0.5 shrink-0 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer\">
            <a href=\"javascript:show(" . $res["tid"] . ")\" class=\"text-slate-700 font-medium hover:text-brand-600 transition-colors\">" . $res["name"] . "</a>
        </div>
    </td>";
    
    // 销量
	echo "<td class=\"px-4 py-3 text-center\"><span class=\"inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold text-xs\">" . $res["sales"] . "</span></td>";
    
    // 价格设置
	echo "<td class=\"px-4 py-3 text-center\">" . ($res["prid"] > 0 ? "<span onclick=\"getPrice(" . $res["tid"] . ")\" class=\"cursor-pointer hover:opacity-80 transition-opacity\"><span class=\"font-medium text-brand-600\">" . $price_class[$res["prid"]] . "</span> <span class=\"text-xs text-slate-400\">(成本:" . $res["price"] . ")</span></span>" : "<span onclick=\"getPrice(" . $res["tid"] . ")\" class=\"cursor-pointer hover:text-brand-600 transition-colors text-[13px]\">" . $res["price"] . " <span class=\"text-slate-300\">|</span> " . $res["cost"] . " <span class=\"text-slate-300\">|</span> " . $res["cost2"] . "</span>") . "</td>";
    
    // 商品类型
	echo "<td class=\"px-4 py-3 text-center\">" . display_shoptype($res["is_curl"], $res["shequ"], $res["tid"]) . "</td>";
    
    // 所属分类
	echo "<td class=\"px-4 py-3 text-center " . (isset($_GET["cid"]) ? "hidden" : "") . "\"><a href=\"./shoplist.php?cid=" . $res["cid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition-colors text-xs font-medium max-w-[120px] truncate\">" . ($res["classname"] ?: "未分类") . "</a></td>";
    
    // 排序操作
	echo "<td class=\"px-4 py-3 text-center " . (isset($_GET["cid"]) ? "" : "hidden") . "\">
        <div class=\"inline-flex items-center gap-1\">
            <a class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer\" title=\"移到顶部\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",0)\"><i class=\"fa fa-long-arrow-up\"></i></a>
            <a class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer\" title=\"移到上一行\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",1)\"><i class=\"fa fa-chevron-circle-up\"></i></a>
            <a class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer\" title=\"移到下一行\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",2)\"><i class=\"fa fa-chevron-circle-down\"></i></a>
            <a class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-500 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer\" title=\"移到底部\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",3)\"><i class=\"fa fa-long-arrow-down\"></i></a>
        </div>
    </td>";
    
    // 库存
	echo "<td class=\"px-4 py-3 text-center\"><a href=\"javascript:setStock(" . $res["tid"] . ",'" . $stock . "')\" class=\"font-medium text-slate-700 hover:text-brand-600 transition-colors\">" . $stock . "</a></td>";
    
    // 状态
	echo "<td class=\"px-4 py-3 text-center whitespace-nowrap\">" . ($res["close"] == 1 ? "<span class=\"inline-flex items-center px-2 py-1 rounded bg-slate-100 text-slate-500 hover:bg-slate-200 cursor-pointer font-medium text-xs transition-colors mb-1 mr-1\" onclick=\"setClose(" . $res["tid"] . ",0)\">已下架</span>" : "<span class=\"inline-flex items-center px-2 py-1 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-100 cursor-pointer font-medium text-xs border border-emerald-100 transition-colors mb-1 mr-1\" onclick=\"setClose(" . $res["tid"] . ",1)\">上架中</span>") . ($res["active"] == 1 ? "<span class=\"inline-flex items-center px-2 py-1 rounded bg-sky-50 text-sky-600 hover:bg-sky-100 cursor-pointer font-medium text-xs border border-sky-100 transition-colors\" onclick=\"setActive(" . $res["tid"] . ",0)\">显示</span>" : "<span class=\"inline-flex items-center px-2 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-100 cursor-pointer font-medium text-xs border border-amber-100 transition-colors\" onclick=\"setActive(" . $res["tid"] . ",1)\">隐藏</span>") . "</td>";
    
    // 操作
	echo "<td class=\"px-4 py-3 text-center whitespace-nowrap\">
        <div class=\"inline-flex items-center gap-1.5\">
            <a href=\"./shopedit.php?my=edit&tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
            <a href=\"./list.php?tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">订单</a>
            <span class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"delTool(" . $res["tid"] . ")\">删除</span>
        </div>
    </td></tr>\r\n";
}
?>
          </tbody>
        </table>
    </div>

    <!-- 底部批量操作区 -->
    <div class="flex flex-col md:flex-row flex-wrap items-start sm:items-center justify-between gap-4 p-4 sm:p-5 bg-slate-50/80 border border-t-0 border-slate-100 rounded-b-xl shadow-sm">
        
        <input type="hidden" name="prid"/>
        <input type="hidden" name="stock"/>
        
        <!-- 批量基础操作 -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto">
            <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                <input name="chkAll1" type="checkbox" id="chkAll1" onClick="checkAll()" value="checkbox" class="w-4.5 h-4.5 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                <span class="ml-2.5 text-sm font-semibold text-slate-700">全选</span>
            </label>
            
            <div class="h-5 w-px bg-slate-200 mx-1 hidden sm:block"></div>
            
            <select name="aid" class="flex-1 sm:flex-none h-9 sm:h-10 px-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer outline-none">
                <option selected>批量操作</option>
                <option value="10">&gt;改加价模板</option>
                <option value="11">&gt;改商品库存</option>
                <option value="1">&gt;改为显示</option>
                <option value="2">&gt;改为隐藏</option>
                <option value="3">&gt;改为上架中</option>
                <option value="4">&gt;改为已下架</option>
                <option value="5">&gt;删除选中</option>
                <option value="6">&gt;复制选中</option>
            </select>
            <button type="button" onclick="change()" class="inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-5 rounded-lg sm:rounded-xl bg-brand-600 text-white font-medium text-[13px] sm:text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                执行
            </button>
        </div>
        
        <!-- 批量移动操作 -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full md:w-auto pt-3 md:pt-0 border-t md:border-t-0 border-slate-200">
            <select name="cid" class="flex-1 sm:flex-none h-9 sm:h-10 px-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer outline-none">
                <option selected>将选定商品移动到分类</option>
                <?php echo $select;?>
            </select>
            <button type="button" onclick="move()" class="inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-5 rounded-lg sm:rounded-xl bg-indigo-500 text-white font-medium text-[13px] sm:text-sm hover:bg-indigo-600 shadow-sm transition-all duration-200">
                确定移动
            </button>
        </div>
    </div>
</form>

<!-- 分页器组件 -->
<ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-6 mb-2 list-none p-0">
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

<script>
// 提取隐藏容器中的标题内容并注入到外层
if($("#blocktitle-container").length > 0) {
    $("#blocktitle").html($("#blocktitle-container").html());
}
</script>

<?php 
function display_shoptype($type, $shequ = 0, $tid = 0)
{
	global $shequurls;
	if ($type == 1 || $type == 2) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 cursor-help\" title=\"" . $shequurls[$shequ] . "\" data-toggle=\"tooltip\">对接</span>";
	} elseif ($type == 4) {
		return "<a href=\"fakakms.php?my=add&tid=" . $tid . "\" class=\"inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-500 hover:text-white transition-colors\">发卡</a>";
	} else {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-sky-50 text-sky-600 border border-sky-100\">自营</span>";
	}
}
?>
