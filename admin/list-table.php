<?php
include "../includes/common.php";
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("order", 1);
$sqls = "";
$links = "";
if (!empty($_GET["starttime"]) || !empty($_GET["endtime"])) {
    if (!empty($_GET["starttime"])) {
        $sqls .= " AND A.addtime>='" . $_GET["starttime"] . " 00:00:00'";
        $links .= "&starttime=" . $_GET["starttime"];
    }
    if (!empty($_GET["endtime"])) {
        $sqls .= " AND A.addtime<='" . $_GET["endtime"] . " 23:59:59'";
        $links .= "&endtime=" . $_GET["endtime"];
    }
}
if (isset($_GET["kw"]) && !empty($_GET["kw"])) {
    $kw = daddslashes($_GET["kw"]);
    $sql = " A.`input`='" . $kw . "' or A.`id`='" . $kw . "' or `tradeno`='" . $kw . "'";
    if ($sqls) {
        $sql = "(" . $sql . ")" . $sqls;
    }
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "包含 <span class=\"font-bold text-brand-600\">" . $_GET["kw"] . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    $link = "&kw=" . $_GET["kw"] . $links;
} elseif (isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $sql = " A.`id`='" . $id . "'" . $sqls;
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "";
    $link = "&id=" . $_GET["id"] . $links;
} elseif (isset($_GET["tid"])) {
    $tid = intval($_GET["tid"]);
    $sql = " A.`tid`='" . $tid . "'" . $sqls;
    if (isset($_GET["type"]) && $_GET["type"] >= 0) {
        $sql .= " AND `status`='" . $_GET["type"] . "'";
        $addstr = display_zt($_GET["type"]) . " 状态的";
    }
    $tool_name = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $tid . "' limit 1");
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "<span class=\"font-medium\">".$tool_name."</span> " . (@$addstr) . "共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    $link = "&tid=" . $_GET["tid"] . $links;
} elseif (isset($_GET["cid"])) {
    $cid = intval($_GET["cid"]);
    $tidlist = $DB->getAll("SELECT tid FROM pre_tools WHERE cid='" . $cid . "'");
    $tids = array();
    foreach ($tidlist as $tidrow) {
        $tids[] = $tidrow["tid"];
    }
    $sql = " A.`tid` IN (" . implode(",", $tids) . ")" . $sqls;
    if (isset($_GET["type"]) && $_GET["type"] >= 0) {
        $sql .= " AND `status`='" . $_GET["type"] . "'";
        $addstr = display_zt($_GET["type"]) . " 状态的";
    }
    $class_name = $DB->getColumn("SELECT name FROM pre_class WHERE cid='" . $cid . "' limit 1");
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "<span class=\"font-medium\">".$class_name."</span> " . (@$addstr) . "共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    $link = "&cid=" . $_GET["cid"] . $links;
} elseif (isset($_GET["zid"])) {
    $zid = intval($_GET["zid"]);
    $sql = " A.`zid`='" . $zid . "'" . $sqls;
    if (isset($_GET["type"]) && $_GET["type"] >= 0) {
        $sql .= " AND `status`='" . $_GET["type"] . "'";
        $addstr = display_zt($_GET["type"]) . " 状态的";
    }
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "站点ID: <span class=\"font-bold text-brand-600\">" . $_GET["zid"] . "</span> " . (@$addstr) . "共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    $link = "&zid=" . $_GET["zid"] . $links;
} elseif (isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    $sql = " A.`userid`='" . $uid . "'" . $sqls;
    if (isset($_GET["type"]) && $_GET["type"] >= 0) {
        $sql .= " AND A.`status`='" . $_GET["type"] . "'";
        $addstr = display_zt($_GET["type"]) . " 状态的";
    }
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "用户ID: <span class=\"font-bold text-brand-600\">" . $_GET["uid"] . "</span> " . (@$addstr) . "共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    $link = "&uid=" . $_GET["uid"] . $links;
} elseif (isset($_GET["type"]) && $_GET["type"] >= 0) {
    $sql = " A.`status`='" . $_GET["type"] . "'" . $sqls;
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $con = "" . display_zt($_GET["type"]) . " 状态的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单";
    if ($_GET["type"] == 3) {
        $con .= "<a href=\"list.php?my=fillall\" onclick=\"return confirm('你确定要将所有异常订单改为待处理状态吗？');\" class=\"ml-3 inline-flex items-center px-3 py-1.5 rounded-lg bg-white border border-brand-200 text-brand-600 hover:bg-brand-50 transition-colors text-xs font-semibold shadow-sm\"><i class=\"fa fa-magic mr-1\"></i> 批量转待处理</a>";
    }
    $link = "&type=" . $_GET["type"] . $links;
} else {
    $sql = " 1" . $sqls;
    $link = $links;
    $numrows = $DB->getColumn("SELECT count(*) from pre_orders A WHERE" . $sql);
    $ondate = $DB->getColumn("select count(*) from pre_orders A where status=1" . $sqls);
    $ondate2 = $DB->getColumn("select count(*) from pre_orders A where status=2" . $sqls);
    $con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个订单，其中已完成的有 <b class=\"text-emerald-600 mx-1\">" . $ondate . "</b> 个，正在处理的有 <b class=\"text-amber-500 mx-1\">" . $ondate2 . "</b> 个。";
}
?>
    <form name="form1" id="form1">
        <?php if(!empty($con)): ?>
        <div class="mb-4 px-4 py-3 bg-brand-50/50 border border-brand-100 rounded-xl text-slate-600 text-sm flex items-center flex-wrap gap-2 shadow-sm">
            <i class="fa fa-info-circle text-brand-500 text-lg shrink-0"></i>
            <div class="flex-1 flex flex-wrap items-center gap-1"><?php echo $con;?></div>
        </div>
        <?php endif; ?>

        <!-- 响应式表格容器 -->
        <div class="w-full overflow-x-auto rounded-t-xl border-x border-t border-slate-100 shadow-sm bg-white scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap orderList">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">订单ID</th>
                        <th class="px-4 py-3.5 font-semibold text-left whitespace-nowrap">商品名称</th>
                        <th class="px-4 py-3.5 font-semibold text-left whitespace-nowrap">下单数据</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">份数</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">订单金额</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">站点ID</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">用户ID</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">添加时间</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">对接状态</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">订单状态</th>
                        <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">操作</th>
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
$rs = $DB->query("SELECT A.*,B.name FROM pre_orders A left join pre_tools B on A.tid=B.tid WHERE" . $sql . " order by id desc limit " . $offset . "," . $pagesize);
while ($res = $rs->fetch()) {
    echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">";
    
    // 订单ID 与 Checkbox
    echo "<td class=\"px-4 py-3 text-center\"><label class=\"inline-flex items-center cursor-pointer m-0\"><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["id"] . "\" onClick=\"unselectall1()\" class=\"w-4 h-4 text-brand-600 bg-slate-100 border-slate-300 rounded focus:ring-brand-500 cursor-pointer\"><span class=\"ml-2.5 font-bold text-slate-700\">" . $res["id"] . "</span></label></td>";
    
    // 商品名称
    echo "<td class=\"px-4 py-3 min-w-[200px] max-w-[280px] whitespace-normal break-words\"><span class=\"cursor-pointer hover:text-brand-600 transition-colors font-medium\" onclick=\"showOrder(" . $res["id"] . ")\" title=\"点击查看详情\">" . $res["name"] . "</span></td>";
    
    // 下单数据
    echo "<td class=\"px-4 py-3 min-w-[150px] max-w-[280px] whitespace-normal break-all text-[13px] leading-snug\"><span class=\"cursor-pointer hover:text-brand-600 transition-colors\" onclick=\"inputOrder(" . $res["id"] . ")\" title=\"点击修改数据\">" . $res["input"] . ($res["input2"] ? "<br/><span class=\"text-slate-400\">" . $res["input2"] ."</span>" : null) . ($res["input3"] ? "<br/><span class=\"text-slate-400\">" . $res["input3"] ."</span>" : null) . ($res["input4"] ? "<br/><span class=\"text-slate-400\">" . $res["input4"] ."</span>" : null) . ($res["input5"] ? "<br/><span class=\"text-slate-400\">" . $res["input5"] ."</span>" : null) . "</span></td>";
    
    // 份数
    echo "<td class=\"px-4 py-3 text-center\"><span class=\"cursor-pointer inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 hover:bg-brand-100 hover:text-brand-700 text-slate-700 font-semibold transition-colors text-xs\" onclick=\"inputNum(" . $res["id"] . ")\" title=\"点击修改份数\">" . $res["value"] . "</span></td>";
    
    // 订单金额
    echo "<td class=\"px-4 py-3 text-center font-bold text-orange-500\">￥" . $res["money"] . "</td>";
    
    // 站点ID
    echo "<td class=\"px-4 py-3 text-center\"><a href =\"sitelist.php?zid=" . $res["zid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-sky-50 text-sky-600 font-semibold hover:bg-sky-500 hover:text-white transition-colors text-xs\">" . $res["zid"] . "</a></td>";
    
    // 用户ID
    echo "<td class=\"px-4 py-3 text-center\">" . (strlen($res["userid"]) != 32 ? "<a href =\"userlist.php?zid=" . $res["userid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center min-w-[1.75rem] h-7 px-1.5 rounded bg-indigo-50 text-indigo-600 font-semibold hover:bg-indigo-500 hover:text-white transition-colors text-xs\">" . $res["userid"] . "</a>" : "<span class=\"text-slate-400\">0</span>") . "</td>";
    
    // 添加时间
    echo "<td class=\"px-4 py-3 text-center text-xs text-slate-500 whitespace-nowrap\">" . $res["addtime"] . "</td>";
    
    // 对接状态
    echo "<td class=\"px-4 py-3 text-center\">" . display_djzt($res["djzt"], $res["id"]) . "</td>";
    
    // 订单状态
    echo "<td class=\"px-4 py-3 text-center\">" . display_zt($res["status"], $res["id"]) . "</td>";
    
    // 操作
    echo "<td class=\"px-4 py-3 text-center\"><select onChange=\"javascript:setStatus('" . $res["id"] . "',this.value)\" class=\"w-full min-w-[96px] h-8 px-2 py-1 text-[13px] border-slate-200 rounded-lg focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer outline-none transition-colors hover:border-slate-300\"><option selected>操作订单</option><option value=\"0\">待处理</option><option value=\"2\">正在处理</option><option value=\"1\">已完成</option><option value=\"4\">已退单</option><option value=\"3\">异常</option>" . ($res["zid"] > 1 || is_numeric($res["userid"]) ? "<option value=\"6\">退款</option>" : null) . "<option value=\"5\">删除订单</option></select></td>";
    
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </div>
        
        <!-- 底部批量操作区 -->
        <div class="flex flex-wrap items-center gap-3 p-4 bg-slate-50/50 border border-t-0 border-slate-100 rounded-b-xl shadow-sm">
            <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                <input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4.5 h-4.5 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                <span class="ml-2.5 text-sm font-semibold text-slate-700">全选本页</span>
            </label>
            
            <div class="h-5 w-px bg-slate-200 mx-1 hidden sm:block"></div>
            
            <select name="status" class="h-9 sm:h-10 px-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer outline-none transition-colors">
                <option selected>请选择批量操作</option>
                <option value="0">改为待处理</option>
                <option value="2">改为正在处理</option>
                <option value="1">改为已完成</option>
                <option value="3">改为异常</option>
                <option value="5">重新下单</option>
                <option value="6">订单退款</option>
                <option value="4">删除订单</option>
            </select>
            
            <button type="button" onclick="operation()" class="inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-6 rounded-lg sm:rounded-xl bg-brand-600 text-white font-medium text-[13px] sm:text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-95">
                <i class="fa fa-check-circle-o mr-1.5"></i> 确定执行
            </button>
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
echo "</ul>";

function display_zt($zt, $id = 0)
{
    if ($zt == 1) {
        return "<a onclick=\"setResult(" . $id . ",'订单结果')\" title=\"点此填写结果\" class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 cursor-pointer hover:bg-emerald-100 transition-colors whitespace-nowrap\">已完成</a>";
    } elseif ($zt == 2) {
        return "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-500 border border-amber-100 whitespace-nowrap\">正在处理</span>";
    } elseif ($zt == 3) {
        return "<a onclick=\"setResult(" . $id . ")\" title=\"点此填写异常原因\" class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-500 border border-rose-100 cursor-pointer hover:bg-rose-100 transition-colors whitespace-nowrap\">异常</a>";
    } elseif ($zt == 4) {
        return "<a onclick=\"setResult(" . $id . ")\" title=\"点此填写异常原因\" class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200 cursor-pointer hover:bg-slate-200 transition-colors whitespace-nowrap\">已退单</a>";
    } else {
        return "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-sky-50 text-sky-500 border border-sky-100 whitespace-nowrap\">待处理</span>";
    }
}
function display_djzt($zt, $id = 0)
{
    if ($zt == 1) {
        return "<span onclick=\"showStatus(" . $id . ")\" title=\"查看订单进度\" class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500 text-white shadow-sm cursor-pointer hover:bg-emerald-600 transition-colors whitespace-nowrap\">成功</span>";
    } elseif ($zt == 2) {
        return "<span onclick=\"djOrder(" . $id . ")\" title=\"点击重试\" class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-500 text-white shadow-sm cursor-pointer hover:bg-rose-600 transition-colors whitespace-nowrap resubmit\" data-id=\"" . $id . "\">失败</span>";
    } elseif ($zt == 3) {
        return "<a onclick=\"window.open('fakakms.php?orderid=" . $id . "')\" title=\"查看卡密信息\" class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200 cursor-pointer hover:bg-emerald-200 transition-colors whitespace-nowrap\">已发卡</a>";
    } elseif ($zt == 4) {
        return "<span onclick=\"djOrder(" . $id . ")\" title=\"点击重试\" class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-500 text-white shadow-sm cursor-pointer hover:bg-amber-600 transition-colors whitespace-nowrap\">未发卡</span>";
    } else {
        return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-400 whitespace-nowrap\">未对接</span>";
    }
}
?>
