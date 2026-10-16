<?php

include "../includes/common.php";

if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$shequlist = $DB->getAll("SELECT id,url,remark FROM pre_shequ order by id asc");
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
	$sql = " A.name LIKE '%" . $kw . "%' and goods_sid != 0";
	if (is_numeric($kw)) {
		$sql .= " OR A.tid='" . $kw . "'";
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "包含 <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&kw=" . urlencode($kw);
} elseif (isset($_GET["cid"])) {
	$cid = intval($_GET["cid"]);
	$sql = " A.cid='" . $cid . "' and goods_sid != 0";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "分类 <a href=\"../?cid=" . $cid . "\" target=\"_blank\" class=\"text-brand-600 hover:underline font-bold mx-1\">" . htmlspecialchars($shua_class[$cid]) . "</a> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&cid=" . $cid;
	$orderby = "A.sort asc";
	if ($pagesize < $numrows) {
		$pagesize = $numrows;
	}
} elseif (isset($_GET["prid"])) {
	$prid = intval($_GET["prid"]);
	$sql = " prid='" . $prid . "' and goods_sid != 0";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "加价模板 <span class=\"text-brand-600 font-bold mx-1\">" . htmlspecialchars($price_class[$prid]) . "</span> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&prid=" . $prid;
} elseif (isset($_GET["tid"])) {
	$tid = intval($_GET["tid"]);
	$sql = " tid='" . $tid . "' and goods_sid != 0";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "商品列表";
	$link = "&tid=" . $tid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where goods_sid != 0");
	$sql = " goods_sid != 0";
	$con = "系统共有 <b class=\"text-lg text-brand-600 mx-1\">" . $numrows . "</b> 个供货商商品";
}

function display_shoptype($type, $shequ = 0)
{
	global $shequurls;
	if ($type == 1 || $type == 2) {
		$title = isset($shequurls[$shequ]) ? $shequurls[$shequ] : '未知对接站';
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-600 text-xs font-semibold border border-amber-100\" title=\"" . htmlspecialchars($title) . "\"><i class=\"fa fa-link mr-1\"></i>对接</span>";
	} elseif ($type == 4) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs font-semibold border border-emerald-100\"><i class=\"fa fa-credit-card mr-1\"></i>发卡</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-sky-50 text-sky-600 text-xs font-semibold border border-sky-100\"><i class=\"fa fa-server mr-1\"></i>自营</span>";
	}
}
?>

<form name="form1" id="form1">
    <!-- 响应式表格容器 -->
    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap" id="supshoplist2">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5 font-semibold text-left min-w-[250px]">
                        <div class="flex items-center gap-3">
                            <label class="flex items-center cursor-pointer mb-0">
                                <input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors">
                            </label>
                            <span>商品名称</span>
                        </div>
                    </th>
                    <th class="px-6 py-3.5 font-semibold text-center min-w-[140px]">对外商品定价设置</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-36 text-rose-500">供货商成本底价</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-28">商品流向</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-32 <?php echo isset($_GET["cid"]) ? "hidden" : "";?>">归属分类</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-36 <?php echo isset($_GET["cid"]) ? "" : "hidden";?>">排序调控</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-24">当前库存</th>
                    <th class="px-6 py-3.5 font-semibold text-center min-w-[140px]">经营状态</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-48">全量操作</th>
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
$rs = $DB->query("SELECT A.*,B.name classname FROM pre_tools A LEFT JOIN pre_class B ON A.cid=B.cid WHERE" . $sql . " order by " . $orderby . " limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	
    if ($res["is_curl"] == 4) {
		$stock = "发卡模式";
	} elseif ($res["stock"] === null) {
		$stock = "无限量";
	} else {
		$stock = $res["stock"];
	}
    
    // 渲染：商品定价设置表现
    $priceDisplay = $res["prid"] > 0 
        ? "<span onclick=\"getPrice(" . $res["tid"] . ")\" class=\"cursor-pointer hover:underline flex flex-col items-center gap-1\"><span class=\"text-sky-600 font-bold bg-sky-50 px-2 py-0.5 rounded text-xs border border-sky-100\">" . htmlspecialchars($price_class[$res["prid"]]) . "</span><span class=\"text-[11px] text-slate-400 font-mono\">独立成本: ¥" . $res["price"] . "</span></span>" 
        : "<span onclick=\"getPrice(" . $res["tid"] . ")\" class=\"cursor-pointer hover:underline flex items-center justify-center font-mono text-slate-500 text-xs\">" . $res["price"] . " <span class=\"text-slate-300 mx-1\">|</span> " . $res["cost"] . " <span class=\"text-slate-300 mx-1\">|</span> " . $res["cost2"] . "</span>";

    // 渲染：上下架状态
    $shelfStatus = $res["close"] == 1 
        ? "<span class=\"inline-flex items-center px-2 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer\" onclick=\"setClose(" . $res["tid"] . ",0)\">已下架隐盘</span>" 
        : "<span class=\"inline-flex items-center px-2 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer\" onclick=\"setClose(" . $res["tid"] . ",1)\">上架营业中</span>";

    // 渲染：显示隐藏状态
    $displayStatus = $res["active"] == 1 
        ? "<span class=\"inline-flex items-center px-2 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100 hover:bg-sky-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["tid"] . ",0)\">全站展示</span>" 
        : "<span class=\"inline-flex items-center px-2 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["tid"] . ",1)\">列表隐藏</span>";

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4\">
                <div class=\"flex items-center gap-3\">
                    <input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["tid"] . "\" onClick=\"unselectall1()\" class=\"w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer shrink-0 mt-0.5\">
                    <div class=\"flex flex-col gap-0.5\">
                        <a href=\"javascript:show(" . $res["tid"] . ")\" class=\"font-bold text-slate-800 hover:text-brand-600 hover:underline max-w-[280px] whitespace-normal break-words leading-snug\" title=\"" . htmlspecialchars($res["name"]) . "\">" . $res["name"] . "</a>
                        <span class=\"text-[11px] text-slate-400 font-mono\">TID: #" . $res["tid"] . "</span>
                    </div>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center\">
                " . $priceDisplay . "
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-rose-500 text-base\">
                <span class=\"text-xs font-normal text-rose-300 mr-0.5\">¥</span>" . $res["sup_price"] . "
            </td>
            <td class=\"px-6 py-4 text-center\">
                " . display_shoptype($res["is_curl"], $res["shequ"]) . "
            </td>
            <td class=\"px-6 py-4 text-center " . (isset($_GET["cid"]) ? "hidden" : "") . "\">
                <a href=\"./supshoplist2.php?cid=" . $res["cid"] . "\" class=\"inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-medium border border-slate-200 transition-colors\">" . ($res["classname"] ?: "未分类") . "</a>
            </td>
            <td class=\"px-6 py-4 text-center " . (isset($_GET["cid"]) ? "" : "hidden") . "\">
                <div class=\"inline-flex items-center justify-center gap-1 bg-slate-50 p-1 rounded-lg border border-slate-100\">
                    <a href=\"javascript:void(0)\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",0)\" class=\"w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:bg-white hover:text-brand-600 hover:shadow-sm transition-all\" title=\"强置登顶\"><i class=\"fa fa-long-arrow-up\"></i></a>
                    <a href=\"javascript:void(0)\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",1)\" class=\"w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:bg-white hover:text-sky-500 hover:shadow-sm transition-all\" title=\"微调上移一行\"><i class=\"fa fa-angle-up\"></i></a>
                    <a href=\"javascript:void(0)\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",2)\" class=\"w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:bg-white hover:text-amber-500 hover:shadow-sm transition-all\" title=\"微调下移一行\"><i class=\"fa fa-angle-down\"></i></a>
                    <a href=\"javascript:void(0)\" onclick=\"sort(" . $res["cid"] . "," . $res["tid"] . ",3)\" class=\"w-7 h-7 flex items-center justify-center rounded text-slate-400 hover:bg-white hover:text-rose-500 hover:shadow-sm transition-all\" title=\"强置沉底\"><i class=\"fa fa-long-arrow-down\"></i></a>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-slate-600\">
                <a href=\"javascript:setStock(" . $res["tid"] . ",'" . $stock . "')\" class=\"hover:text-brand-600 hover:underline\">" . $stock . "</a>
            </td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"flex flex-col items-center gap-1.5\">
                    " . $shelfStatus . "
                    " . $displayStatus . "
                </div>
            </td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center justify-center gap-1.5 flex-wrap max-w-[200px]\">
                    <a href=\"./shopedit.php?my=edit&tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">精细编辑</a>
                    <a href=\"./list.php?tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">追踪订单</a>
                    <span class=\"inline-flex items-center justify-center px-2 py-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"delTool(" . $res["tid"] . ")\">废弃删除</span>
                </div>
            </td>
          </tr>\r\n";
}

if($isEmpty) {
    echo '<tr><td colspan="9" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有检索到任何已通过审核的供货商商品数据</p>
          </td></tr>';
}
?>
            </tbody>
        </table>
    </div>
    
    <!-- 底部批量操作工作台 -->
    <?php if(!$isEmpty): ?>
    <div class="mt-4 p-5 bg-slate-50 border border-slate-100 rounded-xl shadow-inner flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <input type="hidden" name="prid"/>
            <input type="hidden" name="stock"/>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all w-full sm:w-auto">
                <select name="aid" class="w-full sm:w-48 h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>批量高阶动作执行</option>
                    <option value="10">&gt; 批量换绑加价模板</option>
                    <option value="11">&gt; 批量覆盖商品库存</option>
                    <option value="1">&gt; 批量设为 正常显示</option>
                    <option value="2">&gt; 批量设为 隐藏防刷</option>
                    <option value="3">&gt; 批量设为 上架营业</option>
                    <option value="4">&gt; 批量设为 强制下架</option>
                    <option value="5">&gt; 彻底删除选中项</option>
                    <option value="6">&gt; 一键无痕克隆选中项</option>
                </select>
                <button type="button" onclick="change()" class="px-5 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm transition-colors border-0">
                    确认执行
                </button>
            </div>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all w-full sm:w-auto">
                <select name="cid" class="w-full sm:w-48 h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>将选中项重新挂载至</option>
                    <?php echo $select;?>
                </select>
                <button type="button" onclick="move()" class="px-5 h-10 bg-brand-50 hover:bg-brand-100 text-brand-600 font-medium text-sm transition-colors border-0">
                    确认过户
                </button>
            </div>
        </div>
        
    </div>
    <?php endif; ?>
</form>

<!-- 分页控制区 -->
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
