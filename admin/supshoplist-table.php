<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$classlist = $DB->getAll("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">未分类</option>";
$shua_class[0] = "未分类";
foreach ($classlist as $res) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
if ($_SESSION["price_class"]) {
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
	$sql = " A.name LIKE '%" . $kw . "%' AND goods_sid != 0 and audit_status='0'";
	if (is_numeric($kw)) {
		$sql .= " OR A.tid='" . $kw . "'";
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "包含 <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&kw=" . $kw;
} elseif (isset($_GET["cid"])) {
	$cid = intval($_GET["cid"]);
	$sql = " A.cid='" . $cid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools A where" . $sql);
	$con = "分类 <a href=\"../?cid=" . $cid . "\" target=\"_blank\" class=\"text-brand-600 hover:underline font-bold mx-1\">" . htmlspecialchars($shua_class[$cid]) . "</a> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&cid=" . $cid;
	$orderby = "A.sort asc";
	if ($pagesize < $numrows) {
		$pagesize = $numrows;
	}
} elseif (isset($_GET["prid"])) {
	$prid = intval($_GET["prid"]);
	$sql = " prid='" . $prid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "加价模板 <span class=\"text-brand-600 font-bold mx-1\">" . htmlspecialchars($price_class[$prid]) . "</span> 共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个商品";
	$link = "&prid=" . $prid;
} elseif (isset($_GET["tid"])) {
	$tid = intval($_GET["tid"]);
	$sql = " tid='" . $tid . "' AND goods_sid != 0 and audit_status='0'";
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where" . $sql);
	$con = "商品列表";
	$link = "&tid=" . $tid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_tools where goods_sid != 0 and audit_status=0");
	$sql = "  goods_sid != 0 and audit_status=0";
	$con = "系统共有 <b class=\"text-lg text-rose-500 mx-1\">" . $numrows . "</b> 个待审核商品";
}
?>
<form name="form1" id="form1">
    <!-- 响应式表格容器 -->
    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap" id="shoplist">
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
                    <th class="px-6 py-3.5 font-semibold text-center w-36 text-emerald-600">提报结算底价</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-32">提报供货商 ID</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-40 <?php echo isset($_GET["cid"]) ? "hidden" : "";?>">预申请分类</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-40">审批操作</th>
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
	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4\">
                <div class=\"flex items-center gap-3\">
                    <input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["tid"] . "\" onClick=\"unselectall1()\" class=\"w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer shrink-0 mt-0.5\">
                    <div class=\"flex flex-col gap-0.5\">
                        <a href=\"javascript:show(" . $res["tid"] . ")\" class=\"font-bold text-slate-800 hover:text-brand-600 hover:underline max-w-[300px] truncate\" title=\"" . htmlspecialchars($res["name"]) . "\">" . $res["name"] . "</a>
                        <span class=\"text-[11px] text-slate-400 font-mono\">TID: #" . $res["tid"] . "</span>
                    </div>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-emerald-600 text-base\">
                <span class=\"text-xs font-normal text-slate-400 mr-0.5\">¥</span>" . $res["sup_price"] . "
            </td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <span class=\"inline-flex items-center px-2 py-0.5 rounded bg-sky-50 text-sky-600 text-xs font-semibold border border-sky-100\">SID: " . $res["goods_sid"] . "</span>
            </td>
            <td class=\"px-6 py-4 text-center " . (isset($_GET["cid"]) ? "hidden" : "") . "\">
                <a href=\"./shoplist.php?cid=" . $res["cid"] . "\" class=\"inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-medium border border-slate-200 transition-colors\">" . ($res["classname"] ?: "未分类") . "</a>
            </td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center justify-center gap-1.5\">
                    <a href=\"./supshoplist.php?my=edit&tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">进入审批</a>
                    <span class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"delTool(" . $res["tid"] . ")\">退回删除</span>
                </div>
            </td>
          </tr>\r\n";
}

if($isEmpty) {
    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有检索到任何待审批的供货商商品</p>
          </td></tr>';
}
?>            
            </tbody>
        </table>
    </div>

    <!-- 底部批量操作工作台 -->
    <?php if(!$isEmpty): ?>
    <div class="mt-4 p-5 bg-slate-50 border border-slate-100 rounded-xl shadow-inner flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
        
        <!-- 隐藏组件及批量操作按钮组 -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <input type="hidden" name="prid"/>
            <input type="hidden" name="stock"/>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all w-full sm:w-auto">
                <select name="aid" class="w-full sm:w-40 h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>批量操作</option>
                    <option value="10">&gt; 改加价模板</option>
                    <option value="5">&gt; 删除选中</option>
                    <option value="6">&gt; 复制选中</option>
                </select>
                <button type="button" onclick="change()" class="px-4 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm transition-colors border-0">
                    执行
                </button>
            </div>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all w-full sm:w-auto">
                <select name="cid" class="w-full sm:w-48 h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>将选定商品移动到分类</option>
                    <?php echo $select;?>
                </select>
                <button type="button" onclick="move()" class="px-4 h-10 bg-brand-50 hover:bg-brand-100 text-brand-600 font-medium text-sm transition-colors border-0">
                    确定移动
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
