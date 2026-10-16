<?php
include "../includes/common.php";
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("shop", 1);
$typename = array("alipay" => "支付宝", "wxpay" => "微信支付", "qqpay" => "QQ钱包", "tenpay" => "财付通", "bank" => "银联支付", "jdpay" => "京东支付");
// 白名单校验 type 参数，防止 SQL 注入
if (!empty($_GET["type"]) && $_GET["type"] != "all" && !isset($typename[$_GET["type"]])) {
    $_GET["type"] = "all";
}
// 白名单校验 column 参数，防止列名注入
$valid_columns = array("trade_no", "api_trade_no", "name", "input", "userid", "zid", "tid", "ip", "type", "channel", "money", "status");
if (!empty($_GET["column"]) && !in_array($_GET["column"], $valid_columns)) {
    unset($_GET["column"], $_GET["kw"]);
}
$sqls = "";
$links = "";
if (!empty($_GET["type"]) && $_GET["type"] != "all") {
    $sqls .= " AND type='" . $_GET["type"] . "'";
    $links .= "&type=" . urlencode($_GET["type"]);
}
if (isset($_GET["dstatus"]) && $_GET["dstatus"] > 0) {
    $dstatus = intval($_GET["dstatus"]) - 1;
    $sqls .= " AND status=" . $dstatus;
    $links .= "&dstatus=" . $_GET["dstatus"];
}
if (isset($_GET["column"]) && isset($_GET["kw"]) && !empty($_GET["kw"])) {
    $column = trim(daddslashes($_GET["column"]));
    $kw = trim(daddslashes($_GET["kw"]));
    if ($column == "input") {
        $sql = " `" . $column . "` LIKE '%" . $kw . "%'";
    } else {
        $sql = " `" . $column . "`='" . $kw . "'";
    }
    $sql .=$sqls;
    $link = "&type=" . urlencode($_GET["type"] ?? '') . "&kw=" . urlencode($kw) .$links;
    $numrows =$DB->getColumn("SELECT count(*) FROM pre_pay WHERE channel IS NOT NULL AND" . $sql);$con = "包含 <span class=\"font-bold text-brand-600\">" . htmlspecialchars($_GET["kw"], ENT_QUOTES, 'UTF-8') . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 条支付记录";
} else {
    if (!empty($_GET["type"]) && $_GET["type"] != "all") {
        $sql = " type='" . $_GET["type"] . "'";
        $link = "&type=" . urlencode($_GET["type"]);
    } else {
        $sql = " 1";
    }
    $sql .=$sqls;
    $link =$links;
    $numrows =$DB->getColumn("SELECT count(*) FROM pre_pay WHERE channel IS NOT NULL AND" . $sql);
    $con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 条支付记录 <span class=\"text-xs text-slate-400 font-normal\">(未支付记录自动在24小时后删除)</span>";
}
?>

<!-- 顶部提示信息栏 -->
<?php if(!empty($con)): ?>
<div class="mb-4 px-4 py-3 bg-brand-50/50 border border-brand-100 rounded-xl text-slate-600 text-sm flex items-center flex-wrap gap-2 shadow-sm">
    <i class="fa fa-info-circle text-brand-500 text-lg shrink-0"></i>
    <div class="flex-1 flex flex-wrap items-center gap-1" id="blocktitle-container"><?php echo $con;?></div>
</div>
<?php endif; ?>

<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto rounded-t-xl border-x border-t border-slate-100 shadow-sm bg-white scrollbar-thin scrollbar-thumb-slate-200">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap orderList">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">支付订单号<br/><span class="text-[10px] text-slate-400 font-normal">支付接口订单号</span></th>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">支付接口</th>
                <th class="px-4 py-3.5 font-semibold text-left whitespace-nowrap">订单名称<br/><span class="text-[10px] text-orange-500 font-normal">订单金额</span></th>
                <th class="px-4 py-3.5 font-semibold text-left whitespace-nowrap">下单内容<br/><span class="text-[10px] text-slate-400 font-normal">用户IP</span></th>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">站点ID<br/><span class="text-[10px] text-slate-400 font-normal">用户ID</span></th>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">商品ID</th>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">创建时间<br/><span class="text-[10px] text-slate-400 font-normal">完成时间</span></th>
                <th class="px-4 py-3.5 font-semibold text-center whitespace-nowrap">状态</th>
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
$rs =$DB->query("SELECT * FROM `pre_pay` WHERE channel IS NOT NULL AND" . $sql . " ORDER BY trade_no DESC LIMIT " . $offset . "," . $pagesize);
while ($res =$rs->fetch()) {
    if (strpos($res["input"], "|")) {
        $res["input"] = explode("\vert{}", $res["input"])[0];
    }
    if (mb_strlen($res["input"], "utf-8") > 15) {
        $res["input"] = mb_substr($res["input"], 0, 15, "utf-8") . "...";
    }
    
    echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">";
    
    // 支付订单号 & 接口订单号
    echo "<td class=\"px-4 py-3 text-center\"><b class=\"text-slate-700\">" . $res["trade_no"] . "</b><br/><span class=\"text-xs text-slate-400\">" . $res["api_trade_no"] . "</span></td>";
    
    // 支付接口
    echo "<td class=\"px-4 py-3 text-center\"><span class=\"font-medium text-slate-700\">" . ($typename[$res["type"]] ?? $res["type"]) . "</span><br/><span class=\"text-xs text-slate-400\">(" . $res["channel"] . ")</span></td>";
    
    // 订单名称 & 金额
    echo "<td class=\"px-4 py-3 max-w-[280px] whitespace-normal break-words\"><span class=\"font-medium text-slate-700\">" . $res["name"] . "</span><br/><b class=\"text-orange-500\">￥" . $res["money"] . "</b></td>";
    
    // 下单内容 & IP
    echo "<td class=\"px-4 py-3 max-w-[280px] whitespace-normal break-all text-[13px]\">" . $res["input"] . "<br/><a href=\"https://m.ip138.com/iplookup.asp?ip=" . $res["ip"] . "\" target=\"_blank\" rel=\"noreferrer\" class=\"text-xs text-brand-600 hover:underline\"><i class=\"fa fa-map-marker mr-1\"></i>" . $res["ip"] . "</a></td>";
    
    // 站点ID & 用户ID
    echo "<td class=\"px-4 py-3 text-center\"><a href=\"sitelist.php?zid=" . $res["zid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center w-7 h-7 rounded bg-sky-50 text-sky-600 font-semibold hover:bg-sky-500 hover:text-white transition-colors text-xs mb-0.5\">" . $res["zid"] . "</a><br/>" . (strlen($res["userid"]) != 32 ? "<a href=\"userlist.php?zid=" . $res["userid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center px-1.5 h-6 rounded bg-indigo-50 text-indigo-600 font-semibold hover:bg-indigo-500 hover:text-white transition-colors text-[11px]\">" . $res["userid"] . "</a>" : "<span class=\"text-xs text-slate-400\">游客</span>") . "</td>";
    
    // 商品ID
    echo "<td class=\"px-4 py-3 text-center\">" . ($res["tid"] > 0 ? "<a href=\"shoplist.php?tid=" . $res["tid"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center px-2 py-0.5 rounded bg-brand-50 text-brand-600 font-semibold hover:bg-brand-500 hover:text-white transition-colors text-xs\">" . $res["tid"] . "</a>" : "<span class=\"text-slate-400 text-xs\">" . $res["tid"] . "</span>") . "</td>";
    
    // 创建时间 & 完成时间
    echo "<td class=\"px-4 py-3 text-center text-xs text-slate-500 whitespace-nowrap\">" . $res["addtime"] . "<br/><span class=\"text-slate-400\">" . $res["endtime"] . "</span></td>";
    
    // 状态
    echo "<td class=\"px-4 py-3 text-center\">" . ($res["status"] == 1 ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 whitespace-nowrap\">已支付</span>" : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-500 border border-rose-100 whitespace-nowrap\">未支付</span>") . "</td>";
    
    // 操作
    echo "<td class=\"px-4 py-3 text-center whitespace-nowrap\"><div class=\"inline-flex items-center gap-1.5\">" . ($res["status"] == 1 ? "<a class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" href=\"list.php?kw=" . $res["trade_no"] . "\" target=\"_blank\">订单</a>" : "<a class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" href=\"javascript:fillOrder('" . $res["trade_no"] . "')\">补单</a>") . " <a class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" href=\"javascript:delOrder('" . $res["trade_no"] . "')\">删除</a></div></td>";
    
    echo "</tr>\r\n";
}
?>
        </tbody>
    </table>
</div>

<!-- 分页器组件 -->
<ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-6 mb-2 list-none p-0">
<?php
$first = 1;
$prev =$page - 1;
$next =$page + 1;
$last =$pages;

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
$start = $page - 10 > 1 ?$page - 10 : 1;
$end = $page + 10 <$pages ? $page + 10 :$pages;
for ($i = $start; $i < $page; $i++) {
    echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i .$link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
}
echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
for ($i = $page + 1; $i <= $end; $i++) {
    echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i .$link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
}
if ($page <$pages) {
    echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $next . $link . "')\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
    echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $last . $link . "')\" class=\"".$btn_normal."\">尾页</a></li>";
} else {
    echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
    echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
}
?>
</ul>

<script>
$("#blocktitle").html($('#blocktitle-container').html() || '<?php echo addslashes($con);?>');
</script>
