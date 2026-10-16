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
adminpermission("workorder", 1);

if (isset($_GET["status"])) {
	$status = intval($_GET["status"]);
	$sql = " status=:status";
	$params = array(':status' => $status);
	$link = "&status=" . $status;
} else {
	$sql = " ts=1";
	$params = array();
	$link = "";
}
$numrows = $DB->getColumn("SELECT count(*) from pre_workorder WHERE" . $sql, $params);
$pagesize = 30;
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);

function getid($id)
{
	global $DB;
	$_var_0 = $DB->getRow("select * from pre_orders where id=:id limit 1", array(':id' => $id));
	// 添加错误处理，防止找不到订单时出错
	if ($_var_0) {
		return $_var_0["tid"];
	} else {
		return "-";
	}
}

function display_status($status)
{
	if ($status == 1) {
		return "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm\"><i class=\"fa fa-circle-o-notch fa-spin mr-1\"></i>处理中</span>";
	} elseif ($status == 2) {
		return "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm\"><i class=\"fa fa-check-circle mr-1\"></i>已完成</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm animate-pulse\"><i class=\"fa fa-clock-o mr-1\"></i>待处理</span>";
	}
}
?>

<form name="form1" id="form1">
    <!-- 响应式表格容器 -->
    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5 font-semibold text-left w-24">
                        <div class="flex items-center gap-3">
                            ID
                        </div>
                    </th>
                    <th class="px-6 py-3.5 font-semibold text-center w-24">发起分站 ZID</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-28">关联商品 ID</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-36">纠纷订单单号</th>
                    <th class="px-6 py-3.5 font-semibold min-w-[250px]">首条投诉描述摘要</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-28">当前裁决状态</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-40">初次立案时间</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-36">查阅与控制</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
<?php 
// 直接将limit和offset拼接到SQL语句中（MySQL PDO不支持将limit和offset作为参数）	
$rs = $DB->query("SELECT * FROM pre_workorder WHERE" . $sql . " order by id desc limit " . $offset . "," . $pagesize, $params);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	$content = explode("*", $res["content"]);
	$content = mb_substr($content[0], 0, 16, "utf-8");

	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4\">
                <div class=\"flex items-center gap-3\">
                    <input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["id"] . "\" onClick=\"unselectall1()\" class=\"w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors\">
                    <b class=\"font-mono text-slate-700\">" . $res["id"] . "</b>
                </div>
            </td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <a href=\"./sitelist.php?zid=" . $res["zid"] . "\" target=\"_blank\" class=\"text-sky-600 hover:text-sky-800 hover:underline font-bold\">#" . $res["zid"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <span class=\"inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-xs font-semibold border border-slate-200\">TID: " . getid($res["orderid"]) . "</span>
            </td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class=\"text-brand-600 hover:text-brand-800 hover:underline font-bold\">#" . $res["orderid"] . "</a>
            </td>
            <td class=\"px-6 py-4\">
                <a href=\"javascript:orderItem(" . $res["id"] . ")\" class=\"text-slate-700 hover:text-brand-600 font-medium max-w-[280px] truncate block\" title=\"" . htmlspecialchars($content) . "\">" . htmlspecialchars($content) . "</a>
            </td>
            <td class=\"px-6 py-4 text-center\">
                " . display_status($res["status"]) . "
            </td>
            <td class=\"px-6 py-4 text-center font-mono text-xs text-slate-500\">
                " . $res["addtime"] . "
            </td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center justify-center gap-1.5\">
                    <a href=\"javascript:orderItem(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">跟进查阅</a>
                    <a href=\"javascript:delworkorder(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\">阻断删除</a>
                </div>
            </td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="8" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前筛选条件下暂无任何投诉卷宗数据</p>
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
            <input type="hidden" name="content"/>
            
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700 select-none shrink-0">
                <input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors">
                <span>全选当页</span>
            </label>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all w-full sm:w-auto">
                <select name="aid" class="w-full sm:w-48 h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>批量高阶动作执行</option>
                    <option value="1">&gt; 批量回拨为：待处理</option>
                    <option value="2">&gt; 批量核销为：已完成</option>
                    <option value="3">&gt; 自动化批量回复跟进</option>
                    <option value="4">&gt; 彻底删除选中投诉条目</option>
                </select>
                <button type="button" onclick="change()" class="px-5 h-10 bg-brand-600 hover:bg-brand-700 text-white font-medium text-sm transition-colors border-0">
                    确认强执
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</form>

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
