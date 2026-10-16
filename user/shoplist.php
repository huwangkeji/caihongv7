<?php
/**
 * 商品管理与定价控制中枢
**/
include("../includes/common.php");
$title='商品管理';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

unset($islogin2);
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if($userrow['power']==0){
	showmsg('权限阻断：您没有权限使用高级商品管理功能！',3);
}

$my=isset($_GET['my'])?$_GET['my']:null;


$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
$select='<option value="0">全部分类</option>';
$shua_class[0]='未分类';
while($res = $rs->fetch()){
	$shua_class[$res['cid']]=$res['name'];
	$select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}

$price_obj = new \lib\Price($userrow['zid'],$userrow);


if($my=='edit')
{
    $tid=intval($_GET['tid']);
    $row=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
    $price_obj->setToolInfo($tid,$row);
    
    // 获取之前的分类和关键词参数
    $prev_cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    $prev_kw = isset($_GET['kw']) ? urlencode($_GET['kw']) : '';

    // 构建返回链接
    $back_url = './shoplist.php';
    if($prev_cid > 0 || $prev_kw != '') {
        $back_url .= '?cid=' . $prev_cid;
        if($prev_kw != '') {
            $back_url .= '&kw=' . $prev_kw;
        }
    }
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-edit text-indigo-500 mr-2"></i>分站定价管理
            </h3>
            <a href="<?php echo $back_url; ?>" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-reply mr-1"></i>返回列表</a>
        </div>
        <div class="p-6">
            <form action="./shoplist.php?my=edit_submit&tid=<?php echo $tid; ?>&cid=<?php echo $prev_cid; ?>&kw=<?php echo $prev_kw; ?>" method="POST" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">商品名</label>
                    <input type="text" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed focus:ring-0" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" disabled>
                </div>
                
                <?php if($userrow['power']==2){ ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">后台底价</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500 font-bold"><i class="fa fa-cny"></i></div>
                            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 bg-rose-50/50 text-rose-600 shadow-inner cursor-not-allowed focus:ring-0" name="cost2" value="<?php echo $price_obj->getToolCost2($tid); ?>" disabled>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">代理密价</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-500 font-bold"><i class="fa fa-cny"></i></div>
                            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm transition-colors bg-white text-amber-600" name="cost" value="<?php echo $price_obj->getToolCost($tid); ?>">
                        </div>
                    </div>
                </div>
                <?php }else{ ?>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">拿货结算底价</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500 font-bold"><i class="fa fa-cny"></i></div>
                        <input type="text" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 bg-rose-50/50 text-rose-600 shadow-inner cursor-not-allowed focus:ring-0" name="cost" value="<?php echo $price_obj->getToolCost($tid); ?>" disabled>
                    </div>
                </div>
                <?php } ?>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">对客终端售价</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-bold"><i class="fa fa-cny"></i></div>
                            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors bg-white text-emerald-600" name="price" value="<?php echo $price_obj->getToolPrice($tid); ?>">
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">上下架状态干预</label>
                        <select class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-white" name="del" id="del_select" data-default="<?php echo $price_obj->getToolDel($tid); ?>">
                            <option value="0">前端上架展示</option>
                            <option value="1">强制下架隐藏</option>
                        </select>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold text-[15px] hover:from-indigo-600 hover:to-sky-600 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check-circle mr-1.5"></i>设定并生效
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var delSelect = document.getElementById('del_select');
            if(delSelect) delSelect.value = delSelect.getAttribute('data-default') || '0';
        });
    </script>
<?php
}

elseif($my=='edit_submit')
{
    $tid=intval($_GET['tid']);
    $rows=$DB->getRow("SELECT * FROM pre_tools WHERE tid='$tid' LIMIT 1");
    if(!$rows) showmsg('当前挂载节点不存在！',3);
    
    $price_obj->setToolInfo($tid,$rows);
    $price=round(daddslashes($_POST['price']),2);
    $mainprice = $price_obj->getMainPrice();
    $maincost = $price_obj->getMainCost();
    $del=intval($_POST['del']);
    
    if(!is_numeric($price) || !preg_match('/^[0-9.]+$/', $price))showmsg('对客售价入参存在不规范格式',3);
    
    if($userrow['power']==2){
        $cost=round(daddslashes($_POST['cost']),2);
        if(!is_numeric($cost) || !preg_match('/^[0-9.]+$/', $cost))showmsg('下发底价入参存在不规范格式',3);
        if($cost<$price_obj->getToolCost2($tid)){
            showmsg('熔断：下级代理结算底价不能低于总台拿货成本！',3);
        }
        if($price<$cost){
            showmsg('熔断：终端对客售价不能倒挂（低于）下发的代理底价！',3);
        }
        if($conf['fenzhan_pricelimit']==1 && $maincost>0 && ($maincost>1 && $cost>$maincost*2 || $maincost<=1 && $price>2))
            showmsg('风控拦截：下级代理价格涨幅越界，最高不能超出原价2倍（底价1元内的最高限价为2元）',3);
    }else{
        if($price<$price_obj->getToolCost($tid)){
            showmsg('熔断：终端对客售价不能倒挂您的拿货成本底价！',3);
        }
        $cost=0;
    }
    if($conf['fenzhan_pricelimit']==1 && ($mainprice>1 && $price>$mainprice*2 || $mainprice<=1 && $price>2))
        showmsg('风控拦截：终端售价涨幅越界，最高不能超出原价2倍（底价1元内的最高限价为2元）',3);
        
    $prev_cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
    $prev_kw = isset($_GET['kw']) ? urlencode($_GET['kw']) : '';

    $back_url = './shoplist.php';
    if($prev_cid > 0 || $prev_kw != '') {
        $back_url .= '?cid=' . $prev_cid;
        if($prev_kw != '') $back_url .= '&kw=' . $prev_kw;
    }

    if($price_obj->setPriceInfo($tid,$del,$price,$cost))
        showmsg('价格矩阵同步成功！<br/><br/><a href="'.$back_url.'" class="text-indigo-600 font-bold hover:underline">>> 点此返回商品库列表</a>',1);
    else
        showmsg('同步失败：'.$DB->error(),4);
}

elseif($my=='reset')
{
    if($DB->exec("UPDATE pre_site SET price=NULL WHERE zid='{$userrow['zid']}'"))
        showmsg('全域价格重置熔断成功！<br/><br/><a href="./shoplist.php" class="text-indigo-600 font-bold hover:underline">>> 点此返回商品库列表</a>',1);
    else
        showmsg('执行异常：'.$DB->error(),4);
}

else
{
    $cid = intval($_GET['cid']);
    $kw = isset($_GET['kw']) ? trim(daddslashes($_GET['kw'])) : '';

    // 构建过滤参数
    $where = " active=1";
    if($cid > 0) $where .= " AND cid='$cid'";
    if(!empty($kw)) $where .= " AND name LIKE '%$kw%'";

    $numrows=$DB->getColumn("SELECT count(*) FROM pre_tools WHERE{$where}");

    $link = '';
    if($cid > 0) $link .= '&cid='.$cid;
    if(!empty($kw)) $link .= '&kw='.urlencode($kw);
    
    $title_text = '';
    if($cid > 0 || !empty($kw)) {
        if($cid > 0) $title_text .= '<span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded text-xs ml-2 border border-indigo-100">' . htmlspecialchars($shua_class[$cid]) . '</span>';
        if(!empty($kw)) $title_text .= '<span class="px-2 py-0.5 bg-sky-50 text-sky-600 rounded text-xs ml-2 border border-sky-100">检索关键词: "'.htmlspecialchars($kw).'"</span>';
    }
?>
    
    <!-- 顶层看板与功能集群 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="p-5 flex flex-col lg:flex-row justify-between lg:items-center gap-4 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <i class="fa fa-shopping-bag text-indigo-500 mr-2 text-xl"></i>商品统配核心库
                    <?php echo $title_text; ?>
                </h3>
                <p class="text-xs text-slate-500 m-0 mt-2">
                    <?php if($cid > 0 || !empty($kw)){ ?>
                        共为您锁定 <b class="text-indigo-600 text-sm mx-1 font-mono"><?php echo $numrows; ?></b> 个目标商品
                        <a href="shoplist.php" class="ml-2 text-indigo-500 font-bold hover:underline">撤销检索</a>
                    <?php }else{ ?>
                        货架仓储总计 <b class="text-indigo-600 text-sm mx-1 font-mono"><?php echo $numrows; ?></b> 种商品，合理的提价方能实现利润最大化
                    <?php } ?>
                </p>
            </div>
            
            <div class="flex flex-wrap gap-2">
                <button onclick="showSearch()" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 font-bold text-xs rounded-lg hover:bg-slate-50 hover:text-indigo-600 transition-colors shadow-sm"><i class="fa fa-search mr-1.5"></i>商品查找</button>
                
                <?php if($cid > 0 || !empty($kw)){ ?>
                    <button onclick="up_price(<?php echo $cid; ?>)" class="px-3 py-1.5 bg-sky-50 border border-sky-100 text-sky-600 font-bold text-xs rounded-lg hover:bg-sky-500 hover:text-white transition-colors shadow-sm"><i class="fa fa-arrow-up mr-1.5"></i>局部溢价提升</button>
                <?php }else{ ?>
                    <button onclick="showBatchPrice()" class="px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-600 font-bold text-xs rounded-lg hover:bg-indigo-500 hover:text-white transition-colors shadow-sm"><i class="fa fa-calculator mr-1.5"></i>批量调价</button>
                    <button onclick="restoreLastPrice()" class="px-3 py-1.5 bg-amber-50 border border-amber-100 text-amber-600 font-bold text-xs rounded-lg hover:bg-amber-500 hover:text-white transition-colors shadow-sm"><i class="fa fa-undo mr-1.5"></i>撤销至上一步</button>
                    <button onclick="showResetPrice()" class="px-3 py-1.5 bg-rose-50 border border-rose-100 text-rose-600 font-bold text-xs rounded-lg hover:bg-rose-500 hover:text-white transition-colors shadow-sm"><i class="fa fa-refresh mr-1.5"></i>底价恢复</button>
                <?php } ?>
            </div>
        </div>
        
        <!-- 商品表单 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-center w-20">终端控制</th>
                        <th class="px-6 py-4 font-semibold max-w-[250px]">商品名称</th>
                        <th class="px-6 py-4 font-semibold text-center">结算底价</th>
                        <?php if($userrow['power']==2){ ?>
                        <th class="px-6 py-4 font-semibold text-center text-amber-600">下级代理价</th>
                        <?php } ?>
                        <th class="px-6 py-4 font-semibold text-center text-emerald-600">对客售价</th>
                        <th class="px-6 py-4 font-semibold text-center">上下架状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
    $pagesize=30;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);

    $sql = $where;
    $rs=$DB->query("SELECT * FROM pre_tools WHERE{$sql} ORDER BY sort ASC LIMIT $offset,$pagesize");
    $hasData = false;
    
    while($res = $rs->fetch())
    {
        $hasData = true;
        $price_obj->setToolInfo($res['tid'],$res);
        
        // 状态微章
        $isOff = ($price_obj->getToolDel($res['tid'])==1 || $res['close']==1);
        $statusBadge = $isOff 
            ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100"><i class="fa fa-eye-slash mr-1"></i>已下架</span>' 
            : '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fa fa-eye mr-1"></i>在售中</span>';
            
        // 价格渲染
        $cost1 = $userrow['power']==2 ? $price_obj->getToolCost2($res['tid']) : $price_obj->getToolCost($res['tid']);
        $cost2 = $userrow['power']==2 ? $price_obj->getToolCost($res['tid']) : null;
        $sellPrice = $price_obj->getToolPrice($res['tid']);

        echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-6 py-3.5 text-center">
                    <a href="./shoplist.php?my=edit&tid='.$res['tid'].'&cid='.$cid.'&kw='.urlencode($kw).'" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors shadow-sm" title="深度调价"><i class="fa fa-edit"></i></a>
                </td>
                <td class="px-6 py-3.5 font-bold text-slate-700 max-w-[200px] sm:max-w-[300px] truncate" title="'.htmlspecialchars($res['name']).'">
                    <a href="./shop.php?cid='.$res['cid'].'&tid='.$res['tid'].'" target="_blank" class="hover:text-indigo-600 transition-colors">'.$res['name'].'</a>
                </td>
                <td class="px-6 py-3.5 text-center font-mono font-bold text-rose-500">
                    ¥ '.$cost1.'
                </td>';
                
        if($userrow['power']==2){
            echo '<td class="px-6 py-3.5 text-center font-mono font-bold text-amber-500">
                    ¥ '.$cost2.'
                  </td>';
        }
        
        echo '  <td class="px-6 py-3.5 text-center font-mono font-bold text-emerald-500">
                    ¥ '.$sellPrice.'
                </td>
                <td class="px-6 py-3.5 text-center">
                    '.$statusBadge.'
                </td>
              </tr>';
    }
    
    if(!$hasData){
        $cols = $userrow['power']==2 ? 6 : 5;
        echo '<tr><td colspan="'.$cols.'" class="px-6 py-12 text-center text-slate-400">
                <i class="fa fa-search-minus text-4xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">未检索到任何符合条件的商品</p>
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
                
                // 上一页
                if ($page > 1) {
                    echo '<a href="shoplist.php?page=' . $first . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="首页"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="shoplist.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="上一页"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                // 数字页码
                $start = $page - 4 > 1 ? $page - 4 : 1;
                $end   = $page + 4 < $pages ? $page + 4 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="shoplist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="shoplist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                // 下一页
                if ($page < $pages) {
                    echo '<a href="shoplist.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下一页"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="shoplist.php?page=' . $last . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="尾页"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>
        
        <div class="px-6 py-4 bg-sky-50 border-t border-sky-100 text-xs text-sky-700 font-medium">
            <i class="fa fa-info-circle mr-1"></i>缓存须知：若调价后未立即在前台反馈展示，请退出当前管理员身份或使用无痕模式查看，登录态下默认会查看到底价体系。
        </div>
    </div>

<?php } ?>

</div>




<!-- 商品查找 -->
<div id="searchModal" style="display:none;" class="p-6">
    <form action="shoplist.php" method="GET" class="space-y-4">
        <div class="space-y-1.5">
            <label class="text-sm font-semibold text-slate-700 ml-1">选择分类</label>
            <select name="cid" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white">
                <?php echo $select?>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-sm font-semibold text-slate-700 ml-1">检索关键词</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-search"></i></div>
                <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white" name="kw" placeholder="输入商品字眼..." value="<?php echo isset($_GET['kw'])?htmlspecialchars($_GET['kw']):''?>">
            </div>
        </div>
        <div class="pt-2">
            <button type="submit" class="w-full h-11 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-sm transition-colors active:scale-95"><i class="fa fa-crosshairs mr-1.5"></i>精准查询</button>
        </div>
    </form>
</div>

<!-- 批量修改价格 -->
<div id="batchPriceModal" style="display:none;" class="p-5 sm:p-6">
    <div class="bg-sky-50 border border-sky-100 rounded-lg p-3 text-xs text-sky-700 font-medium mb-5">
        <i class="fa fa-exclamation-triangle text-sky-500 mr-1"></i>计算隔离：价格为0或带有"免费"字样的公益商品将会被自动排除。
    </div>
    
    <div class="space-y-4 max-h-[60vh] overflow-y-auto px-1 scrollbar-thin">
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">变动走向</label>
                <select id="operationType" class="w-full h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="add">涨幅加价 (+)</option>
                    <option value="subtract">折让降价 (-)</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">锚定字段</label>
                <select id="priceType" class="w-full h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="price">终端对客售价</option>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">测算模型</label>
                <select id="batchType" class="w-full h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="fixed">绝对值锚点</option>
                    <option value="percent">相对百分比</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">执行标量</label>
                <input type="text" id="modifyValue" class="w-full h-10 px-3 text-sm font-mono font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="输入整数或百分数">
            </div>
        </div>
        
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-700 ml-1">作用分类限定 (大类)</label>
            <select id="goodsCategory" multiple="multiple" size="4" class="w-full p-2 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm scrollbar-thin">
                <option value="0" selected>跨级全部分类</option>
                <?php echo $select; ?>
            </select>
            <p class="text-[10px] text-slate-400 mt-1 pl-1">PC端按住 Ctrl/Cmd 可进行多范围点选。</p>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">特征拦截 (可选)</label>
                <input type="text" id="goodsName" class="w-full h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm placeholder:font-normal" placeholder="留空则跨域拦截">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-semibold text-slate-700 ml-1">上下架干预</label>
                <select id="goodsStatus" class="w-full h-10 px-3 text-sm font-bold rounded-lg border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="0">无视状态全域覆盖</option>
                    <option value="1">仅锁定售卖中</option>
                    <option value="2">仅锁定已下架</option>
                </select>
            </div>
        </div>
        
        <div class="pt-4 pb-2">
            <button type="button" onclick="batchUpdatePrice()" class="w-full h-11 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold text-sm shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-600 active:scale-95 transition-all"><i class="fa fa-magic mr-1.5"></i>批量执行</button>
        </div>
    </div>
</div>

<!-- 恢复价格条件面板 -->
<div id="resetPriceModal" style="display:none;" class="p-5 sm:p-6">
    <div class="bg-rose-50 border border-rose-100 rounded-lg p-3 text-xs text-rose-700 font-medium mb-5">
        <i class="fa fa-warning text-rose-500 mr-1"></i>抹除警告：此动作将永久清除您在筛选范围内的所有定制化价格体系，强制恢复至底层拿货价。
    </div>
    
    <div class="space-y-4">
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-700 ml-1">选择分类</label>
            <select id="resetGoodsCategory" class="w-full h-11 px-3 text-sm font-bold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm">
                <option value="0">全部分类</option>
                <?php echo $select; ?>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-700 ml-1">特征词</label>
            <input type="text" id="resetGoodsName" class="w-full h-11 px-3 text-sm font-bold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm placeholder:font-normal" placeholder="非必填，留空波及全体">
        </div>
        <div class="space-y-1.5">
            <label class="text-xs font-semibold text-slate-700 ml-1">状态</label>
            <select id="resetGoodsStatus" class="w-full h-11 px-3 text-sm font-bold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm">
                <option value="0">全部商品</option>
                <option value="1">上架中</option>
                <option value="2">已下架</option>
            </select>
        </div>
        
        <div class="pt-4 pb-2">
            <button type="button" onclick="resetPrice()" class="w-full h-11 rounded-xl bg-rose-500 text-white font-bold text-sm shadow-md shadow-rose-500/20 hover:bg-rose-600 active:scale-95 transition-all"><i class="fa fa-bomb mr-1.5"></i>授权执行</button>
        </div>
    </div>
</div>

<!-- 价格历史记录容器 -->
<div id="priceHistoryModal" style="display:none;" class="p-0 sm:p-2 bg-slate-50 h-full flex flex-col">
    <div class="p-4 sm:p-5 flex-1 overflow-hidden flex flex-col">
        <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-xs text-amber-700 font-medium mb-4 shrink-0">
            <i class="fa fa-history text-amber-500 mr-1"></i>回溯：系统为您保存了定期的调价快照，点击恢复将覆写当前时空。
        </div>
        <div id="historyList" class="flex-1 overflow-y-auto scrollbar-thin bg-white rounded-xl border border-slate-200 relative">
            <!-- 列表动态注入区 -->
        </div>
    </div>
</div>

<?php include './foot.php';?>

<script>
function showSearch() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-search text-indigo-500 mr-2"></i>检索商品档案</div>',
        area: [$(window).width() > 400 ? '400px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#searchModal')
    });
}

function showBatchPrice() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-calculator text-indigo-500 mr-2"></i>批量调价</div>',
        area: [$(window).width() > 500 ? '500px' : '95%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#batchPriceModal')
    });
}

function showResetPrice() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-bomb text-rose-500 mr-2"></i>底价恢复系统</div>',
        area: [$(window).width() > 450 ? '450px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#resetPriceModal')
    });
}

function showPriceHistory() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-history text-amber-500 mr-2"></i>调价溯源快照</div>',
        area: [$(window).width() > 800 ? '800px' : '98%', $(window).height() > 600 ? '600px' : '90%'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#priceHistoryModal')
    });
    loadHistoryList(1);
}

// ==========================================
// 业务执行接口层
// ==========================================

function reset_price(cid){
	if(confirm('警告：是否要将该大类商品强制调整至底层拿货价？')){
		$.ajax({
			type:"post",
			url:"ajax_user.php?act=reset_price",
			data:{cid:cid},
			dataType:"json",
			success:function(data){
				if(data.code==0){
					layer.alert('指令下发成功！',{icon:1},function(){ window.location.reload(); });
				}else{
					layer.alert(data.msg, {icon:2});
				}
			}
		});
	}
}

function resetPrice() {
    var goodsCategory = $('#resetGoodsCategory').val();
    var goodsName = $('#resetGoodsName').val();
    var goodsStatus = $('#resetGoodsStatus').val();
    
    layer.confirm('抹除警告：确定焚毁圈定范围内商品的所有自主定价权吗？', {icon: 3, title: '终极确认'}, function(index) {
        layer.close(index);
        var loadingIndex = layer.load(2, {shade: [0.1,'#fff']});
        
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=reset_price",
            data: { cid: goodsCategory, name: goodsName, status: goodsStatus },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 1, time:1500}, function() { window.location.reload(); });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.msg('中枢通信断裂，执行失败', {icon: 2});
            }
        });
    });
}

function up_price(cid){
    var kw = '';
    var urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('kw')){ kw = urlParams.get('kw'); }
    
    layer.prompt({title: '请签发局部提价百分比额度 (建议≤10)', formType: 0}, function(text, index){
	    layer.close(index);
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type:"post",
            url:"ajax_user.php?act=up_price",
            data:{ up:text, cid:cid, kw:kw },
            dataType:"json",
            success:function(data){
                layer.close(ii);
                if(data.code==0){
                    layer.msg('提价成功生效，全局刷新后可见', {icon:1, time:1500}, function(){ window.location.reload(); });
                }else{
                    layer.alert(data.msg, {icon:2});
                }
            }
        });
	});
}

function batchUpdatePrice() {
    var operationType = $('#operationType').val();
    var priceType = $('#priceType').val();
    var batchType = $('#batchType').val();
    var modifyValue = $('#modifyValue').val();
    var goodsCategory = $('#goodsCategory').val();
    var goodsName = $('#goodsName').val();
    var goodsStatus = $('#goodsStatus').val();
    
    if (Array.isArray(goodsCategory)) goodsCategory = goodsCategory.join(',');
    
    if (!modifyValue) return layer.msg('拒批：必须录入执行标量', {icon: 2});
    if (batchType == 'percent' && (isNaN(modifyValue) || parseFloat(modifyValue) <= 0)) return layer.msg('拒批：相对百分比应当为正实数', {icon: 2});
    if (batchType == 'fixed' && isNaN(modifyValue)) return layer.msg('拒批：绝对值必须由数字构成', {icon: 2});
    
    layer.confirm('核销指令：确认向全域下发此价格偏转矩阵吗？', {icon: 3, title: '矩阵下发确认'}, function(index) {
        layer.close(index);
        var loadingIndex = layer.load(2, {shade: [0.1,'#fff']});
        
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=batch_update_price",
            data: { operation: operationType, price_type: priceType, batch_type: batchType, value: modifyValue, cid: goodsCategory, name: goodsName, status: goodsStatus },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 1, time:1500}, function() { window.location.reload(); });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.msg('通道崩塌，部署失败', {icon: 2});
            }
        });
    });
}

function restoreLastPrice() {
    layer.confirm('逆转未来：您确信要回滚到上一次价格偏转前的位面吗？', {
        icon: 3, title: '启动回滚', btn: ['确认激活引擎', '中止操作']
    }, function(index) {
        layer.close(index);
        var loadingIndex = layer.load(2, {shade: [0.1,'#fff']});
        
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=restore_last_price",
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                if (data.code == 0) {
                    layer.msg('时空回溯闭环，数据已更迭', {icon: 1, time:1500}, function() { window.location.reload(); });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.msg('时空跃迁失败', {icon: 2});
            }
        });
    });
}

function loadHistoryList(page) {
    page = page || 1;
    $('#historyList').html('<div class="flex items-center justify-center h-40 text-slate-400 font-bold"><i class="fa fa-circle-o-notch fa-spin text-2xl mr-2 text-indigo-500"></i>正在深度扒取快照切片...</div>');
    
    $.ajax({
        type: "post",
        url: "ajax_user.php?act=get_price_history",
        data: { page: page },
        dataType: "json",
        success: function(data) {
            if (data.code == 0) {
                renderHistoryList(data);
            } else {
                $('#historyList').html('<div class="p-6 text-center text-rose-500 font-bold">' + data.msg + '</div>');
            }
        },
        error: function() {
            $('#historyList').html('<div class="p-6 text-center text-rose-500 font-bold">源文件破损，加载终止</div>');
        }
    });
}

function renderHistoryList(data) {
    var html = '';
    
    if (data.data && data.data.length > 0) {
        html += '<div class="w-full overflow-x-auto"><table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">';
        html += '<thead class="text-xs text-slate-500 bg-slate-50 border-b border-slate-100 uppercase"><tr><th class="px-5 py-3 font-semibold">变更事务概览</th><th class="px-5 py-3 font-semibold text-center">系统打点时间戳</th><th class="px-5 py-3 font-semibold text-center">波及标的物规模</th><th class="px-5 py-3 font-semibold text-center">时光机操作</th></tr></thead>';
        html += '<tbody class="divide-y divide-slate-50">';
        
        $.each(data.data, function(index, item) {
            html += '<tr class="hover:bg-slate-50 transition-colors group">';
            html += '<td class="px-5 py-3 font-bold text-slate-700 max-w-[200px] truncate" title="'+item.description+'">' + item.description + '</td>';
            html += '<td class="px-5 py-3 text-center text-xs font-mono text-slate-400">' + item.create_time + '</td>';
            html += '<td class="px-5 py-3 text-center"><span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 font-bold">' + item.price_data_count + ' <small class="font-normal text-indigo-400">个</small></span></td>';
            html += '<td class="px-5 py-3 text-center"><button class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-100 hover:text-indigo-600 transition-colors shadow-sm active:scale-95" onclick="restoreFromHistory(' + item.id + ')"><i class="fa fa-history mr-1"></i>复盘并加载</button></td>';
            html += '</tr>';
        });
        
        html += '</tbody></table></div>';
        
        var totalPages = Math.ceil(data.total / data.pagesize);
        if (totalPages > 1) {
            html += '<div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-center"><div class="flex space-x-1">';
            
            if (data.page > 1) {
                html += '<button onclick="loadHistoryList(' + (data.page - 1) + ')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors"><i class="fa fa-angle-left"></i></button>';
            } else {
                html += '<button disabled class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></button>';
            }
            
            for (var i = 1; i <= totalPages; i++) {
                if (i == data.page) {
                    html += '<button class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-500 text-white font-bold shadow-md">' + i + '</button>';
                } else {
                    html += '<button onclick="loadHistoryList(' + i + ')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors">' + i + '</button>';
                }
            }
            
            if (data.page < totalPages) {
                html += '<button onclick="loadHistoryList(' + (data.page + 1) + ')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors"><i class="fa fa-angle-right"></i></button>';
            } else {
                html += '<button disabled class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 border border-slate-200 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></button>';
            }
            
            html += '</div></div>';
        }
    } else {
        html += '<div class="p-12 text-center text-slate-400 flex flex-col items-center"><i class="fa fa-hourglass-o text-4xl mb-3 text-slate-200 block"></i><span class="text-sm font-medium text-slate-500">此域界尚无任何时空折叠快照</span></div>';
    }
    
    $('#historyList').html(html);
}

function restoreFromHistory(id) {
    layer.confirm('强制覆写警告：确认将整个定价格局拨回至该坐标点吗？此操作不可逆！', {
        icon: 3, title: '覆写确认', btn: ['确认注入快照', '撤销']
    }, function(index) {
        layer.close(index);
        var loadingIndex = layer.load(2, {shade: [0.1,'#fff']});
        
        $.ajax({
            type: "post",
            url: "ajax_user.php?act=restore_from_history",
            data: { id: id },
            dataType: "json",
            success: function(data) {
                layer.close(loadingIndex);
                if (data.code == 0) {
                    layer.msg(data.msg, {icon: 1, time:1500}, function() {
                        layer.closeAll('page'); // 关闭所有自定义层
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error: function() {
                layer.close(loadingIndex);
                layer.msg('时空桥接断裂，请重试', {icon: 2});
            }
        });
    });
}
</script>
</body>
</html>
