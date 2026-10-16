<?php

include "../includes/common.php";
$title = "商品管理";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
if ($suprow["bond"] < $conf["sup_bond"]) {
	exit("<script>layer.msg('您当前未缴纳保证金，正在为您跳转...', {icon:0, time:2000}, function(){window.location.href='./bond.php';});</script>");
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php 
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
$select = "<option value=\"0\">请选择分类</option>";
$shua_class[0] = "未分类";
while ($res = $rs->fetch()) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
?>

    <!-- 隐藏舱：商品分类过滤弹窗 -->
    <div id="searchModalContent" style="display: none;" class="p-6">
        <form action="shoplist.php" method="GET" class="space-y-4">
            <select name="cid" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                <?php echo $select;?>
            </select>
            <button type="submit" class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-sm active:scale-95 transition-all">查看对应分类</button>
        </form>
    </div>

<?php 
if (isset($_GET["cid"])) {
	$cid = intval($_GET["cid"]);
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE cid='" . $cid . "' AND active=1 and goods_sid='" . $suprow["sid"] . "'");
	$sql = " cid=" . $cid . " AND goods_sid=" . $suprow["sid"] . "";
	
	$header_title = "<i class='fa fa-folder-open text-indigo-500 mr-2 text-xl'></i>" . htmlspecialchars($shua_class[$cid]) . " - 分类商品";
	$header_desc = "分类 [" . htmlspecialchars($shua_class[$cid]) . "] 下共有 <b class='text-indigo-600 text-sm mx-1 font-mono'>{$numrows}</b> 个商品";
	$link = "&cid=" . $cid;
} else {
	$numrows = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE active=1 and goods_sid='" . $suprow["sid"] . "'");
	$sql = " goods_sid=" . $suprow["sid"] . "";
	
	$header_title = "<i class='fa fa-cubes text-emerald-500 mr-2 text-xl'></i>商品列表";
	$header_desc = "系统累计挂载 <b class='text-emerald-600 text-sm mx-1 font-mono'>{$numrows}</b> 个商品档案 - 请合理设置价格区间以保障销量。";
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部看板与操作区 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <?php echo $header_title; ?>
                    <?php if (isset($_GET["cid"])) { ?>
                        <a href="shoplist.php" class="ml-4 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-200 text-slate-600 hover:bg-slate-300 transition-colors shadow-sm">查看全部</a>
                    <?php } ?>
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-1.5"><?php echo $header_desc; ?></p>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="showCategoryFilter()" class="h-10 px-4 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm border border-slate-200 hover:bg-slate-200 transition-colors shadow-sm flex items-center">
                    <i class="fa fa-navicon mr-1.5"></i>分类查看
                </button>
                <a href="./shopedit.php?my=add<?php echo isset($cid) ? '&cid='.$cid : ''; ?>" class="h-10 px-4 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-colors flex items-center">
                    <i class="fa fa-plus-circle mr-1.5"></i>上架商品
                </a>
            </div>
        </div>
        
        <!-- 列表区 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-24">操作</th>
                        <th class="px-5 py-4 font-semibold min-w-[200px]">商品名称</th>
                        <th class="px-5 py-4 font-semibold text-center">销售价格</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">审核状态</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">商品状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_tools WHERE" . $sql . " ORDER BY sort ASC LIMIT " . $offset . "," . $pagesize);
$hasData = false;

while ($res = $rs->fetch()) {
    $hasData = true;
	echo "<tr class='hover:bg-slate-50/50 transition-colors group'>";
	
    // 操作
	echo "<td class='px-5 py-3.5 text-center'>
            <a href=\"./shopedit.php?my=edit&tid={$res['tid']}\" class=\"px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors text-xs font-bold shadow-sm\">编辑</a>
          </td>";
          
    // 商品名称
	echo "<td class='px-5 py-3.5'>
            <a href=\"./shop.php?cid={$res['cid']}&tid={$res['tid']}\" title=\"点此体验下单流程\" class=\"font-bold text-slate-700 hover:text-indigo-600 transition-colors max-w-[250px] truncate block\">" . htmlspecialchars($res['name']) . "</a>
          </td>";
          
    // 销售价格
	echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-rose-500'>
            {$res['sup_price']}<span class=\"text-xs font-normal ml-0.5\">元</span>
          </td>";
          
    // 审核状态
	echo "<td class='px-5 py-3.5 text-center'>
            " . ($res['audit_status'] == 0 ? "<span class='px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold border border-rose-100 text-[11px] shadow-sm'>未通过</span>" : "<span class='px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold border border-emerald-100 text-[11px] shadow-sm'>已通过</span>") . "
          </td>";
          
    // 商品状态
	echo "<td class='px-5 py-3.5 text-center'>
            " . ($res['close'] == 1 ? "<span class='px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold border border-slate-200 text-[11px] shadow-sm'>已下架</span>" : "<span class='px-2 py-0.5 rounded bg-sky-50 text-sky-600 font-bold border border-sky-100 text-[11px] shadow-sm'>上架中</span>") . "
          </td>";
          
	echo "</tr>";
}

if(!$hasData){
    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500 m-0">暂无商品数据</p>
          </td></tr>';
}
?>		          
                </tbody>
            </table>
        </div>

        <!-- 分页栏 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
<?php 
$first = 1;
$prev = $page - 1;
$next = $page + 1;
$last = $pages;
$link = isset($link) ? $link : '';

if ($page > 1) {
	echo "<a href=\"shoplist.php?page=" . $first . $link . "\" class='min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold' title='首页'>首页</a>";
	echo "<a href=\"shoplist.php?page=" . $prev . $link . "\" class='w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm' title='上一页'><i class='fa fa-angle-left'></i></a>";
} else {
	echo "<span class='min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold'>首页</span>";
	echo "<span class='w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'><i class='fa fa-angle-left'></i></span>";
}

$start = $page - 4 > 1 ? $page - 4 : 1;
$end = $page + 4 < $pages ? $page + 4 : $pages;

if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';

for ($i = $start; $i < $page; $i++) {
	echo "<a href=\"shoplist.php?page=" . $i . $link . "\" class='min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
}

echo "<span class='min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30'>" . $page . "</span>";

for ($i = $page + 1; $i <= $end; $i++) {
	echo "<a href=\"shoplist.php?page=" . $i . $link . "\" class='min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
}

if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';

if ($page < $pages) {
	echo "<a href=\"shoplist.php?page=" . $next . $link . "\" class='w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm' title='下一页'><i class='fa fa-angle-right'></i></a>";
	echo "<a href=\"shoplist.php?page=" . $last . $link . "\" class='min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold' title='尾页'>尾页</a>";
} else {
	echo "<span class='w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'><i class='fa fa-angle-right'></i></span>";
	echo "<span class='min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold'>尾页</span>";
}
?>
            </nav>
        </div>
        <?php } ?>

    </div>

</div>

<?php 
include "./foot.php";
?>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
// 弹窗：商品分类筛选
function showCategoryFilter() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-navicon text-indigo-500 mr-2"></i>商品分类浏览</div>',
        area: [$(window).width() > 400 ? '400px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#searchModalContent')
    });
}
</script>
</body>
</html>
