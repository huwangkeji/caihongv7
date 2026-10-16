<?php
/**
 * 直属下级用户管理总控
**/
include("../includes/common.php");
$title='用户管理';
include './head.php';

// 登录态防泄漏鉴权
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
// 权限拦截：普通分站站长及以上可用
if($userrow['power']<1){
	showmsg('权限阻断：您当前未开通站长权限，无法使用下级客源管理功能！',3);
}

$my=isset($_GET['my'])?$_GET['my']:null;

// SQL核心构建与数据探针
$numrows=$DB->getColumn("SELECT count(*) FROM pre_site WHERE upzid='{$userrow['zid']}' AND power=0");
$link = '';

if(isset($_GET['zid'])){
	$zid=intval($_GET['zid']);
	$sql = " zid={$zid} AND upzid='{$userrow['zid']}' AND power=0";
	$link .= '&zid='.$zid;
}elseif(isset($_GET['kw'])){
	$kw=daddslashes($_GET['kw']);
	$sql = " (user='{$kw}' OR qq='{$kw}' OR zid='{$kw}') AND upzid='{$userrow['zid']}' AND power=0";
	$link .= '&kw='.urlencode($kw);
}else{
	$sql = " upzid='{$userrow['zid']}' AND power=0";
}
?>

    <!-- 头部看板卡片 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                    <i class="fa fa-users text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">下级用户列表</h3>
                    <p class="text-xs text-slate-500 m-0 mt-1">您麾下现已注册了 <b class="text-indigo-600 text-sm mx-1 font-mono"><?php echo $numrows; ?></b> 个基础用户账号</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <button onclick="showSearch()" class="flex-1 md:flex-none inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-all shadow-md shadow-indigo-500/20 active:scale-95"><i class="fa fa-search mr-1.5"></i>搜索账号</button>
            </div>
        </div>

        <?php if(isset($_GET['kw']) || isset($_GET['zid'])){ ?>
        <div class="px-6 py-3 bg-sky-50 border-b border-sky-100 flex items-center justify-between">
            <span class="text-xs font-bold text-sky-700"><i class="fa fa-filter mr-1.5"></i>已开启精准搜索，展示过滤后的目标客源</span>
            <a href="./userlist.php" class="text-xs font-bold text-sky-600 hover:text-sky-800 hover:underline">返回</a>
        </div>
        <?php } ?>
        
        <!-- 表单列表区 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-20">标识(UID)</th>
                        <th class="px-5 py-4 font-semibold">登录账户身份</th>
                        <th class="px-5 py-4 font-semibold">预留信息(QQ)</th>
                        <th class="px-5 py-4 font-semibold text-right text-emerald-600">可用余额</th>
                        <th class="px-5 py-4 font-semibold text-center">创建时间</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">权限管理</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
    $pagesize=30;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);

    $rs=$DB->query("SELECT * FROM pre_site WHERE{$sql} ORDER BY zid DESC LIMIT $offset,$pagesize");
    $hasData = false;
    
    while($res = $rs->fetch())
    {
        $hasData = true;
        // 财务高亮样式
        $rmbClass = $res['rmb'] > 0 ? 'text-emerald-600 font-bold' : 'text-slate-400';

        echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-5 py-3.5 text-center">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-mono font-bold text-[11px] border border-slate-200">#'.$res['zid'].'</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="font-bold text-slate-700">'.htmlspecialchars($res['user']).'</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-[11px] font-mono text-slate-500 bg-slate-50 px-2 py-1 rounded border border-slate-100"><i class="fa fa-qq mr-1"></i>'.($res['qq'] ?: '未留痕').'</span>
                </td>
                <td class="px-5 py-3.5 text-right font-mono '.$rmbClass.'">
                    ¥ '.$res['rmb'].'
                </td>
                <td class="px-5 py-3.5 text-center">
                    <span class="text-[11px] font-mono text-slate-400">'.$res['addtime'].'</span>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <!-- 源程序中此操作为硬编码 disabled，在此我们忠实还原并予以优雅的封锁提示 -->
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 border border-slate-100 cursor-not-allowed" title="此功能接口暂未对您的权限级别开放"><i class="fa fa-edit"></i></span>
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-300 border border-slate-100 cursor-not-allowed" title="此功能接口暂未对您的权限级别开放"><i class="fa fa-trash"></i></span>
                    </div>
                </td>
              </tr>';
    }
    
    if(!$hasData){
        echo '<tr><td colspan="6" class="px-6 py-12 text-center text-slate-400">
                <i class="fa fa-users text-4xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">探测网内暂未发现符合条件的客源档案节点</p>
              </td></tr>';
    }
?>
                </tbody>
            </table>
        </div>

        <!-- 分页系统包裹器 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
                <?php
                $first = 1;
                $prev  = $page - 1;
                $next  = $page + 1;
                $last  = $pages;
                
                if ($page > 1) {
                    echo '<a href="userlist.php?page=' . $first . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="首页"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="userlist.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="上一页"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                $start = $page - 4 > 1 ? $page - 4 : 1;
                $end   = $page + 4 < $pages ? $page + 4 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="userlist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="userlist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                if ($page < $pages) {
                    echo '<a href="userlist.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下一页"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="userlist.php?page=' . $last . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="尾页"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>

    </div>

    <!-- 隐藏搜寻雷达装载舱 (交由 Layer 渲染) -->
    <div id="searchModal" style="display:none;" class="p-6">
        <form action="userlist.php" method="GET" class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 ml-1">输入关键词</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-search"></i></div>
                    <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 placeholder:font-normal" name="kw" placeholder="输入 UID / 登录账户 / QQ 号进行搜索...">
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full h-11 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-sm transition-colors active:scale-95"><i class="fa fa-crosshairs mr-1.5"></i>搜索</button>
            </div>
        </form>
    </div>

</div>

<?php include './foot.php';?>

<script>
// 接管原生旧的 Bootstrap Modal
function showSearch() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-search text-indigo-500 mr-2"></i>客源情报雷达系统</div>',
        area: [$(window).width() > 450 ? '450px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#searchModal')
    });
}
</script>
</body>
</html>
