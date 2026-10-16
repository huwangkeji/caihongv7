<?php
/**
 * 资金账户收支明细与流水
**/
include("../includes/common.php");
$title='收支明细';
include './head.php';

// 鉴权防漏
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$thtime=date("Y-m-d").' 00:00:00';
$lastday=date("Y-m-d",strtotime("-1 day")).' 00:00:00';

// 四大聚合维度数据拉取
$income_today=$DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='提成' AND addtime>'$thtime'");
$outcome_today=$DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='消费' AND addtime>'$thtime'");
$income_lastday=$DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='提成' AND addtime<'$thtime' AND addtime>'$lastday'");
$outcome_lastday=$DB->getColumn("SELECT sum(point) FROM pre_points WHERE zid='{$userrow['zid']}' AND action='消费' AND addtime<'$thtime' AND addtime>'$lastday'");
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

    <!-- 顶部核心看板：四大财务聚合卡片 -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        
        <!-- 今日收益 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-line-chart text-emerald-500 mr-1.5"></i>今日总收益</span>
                <span class="text-2xl font-mono font-bold text-slate-800 tracking-tight">
                    <span class="text-emerald-500 text-lg mr-0.5">¥</span><?php echo round($income_today,2)?>
                </span>
            </div>
        </div>

        <!-- 今日消费 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-rose-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-shopping-cart text-rose-500 mr-1.5"></i>今日总消费</span>
                <span class="text-2xl font-mono font-bold text-slate-800 tracking-tight">
                    <span class="text-rose-500 text-lg mr-0.5">¥</span><?php echo round($outcome_today,2)?>
                </span>
            </div>
        </div>

        <!-- 昨日收益 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-bar-chart text-sky-500 mr-1.5"></i>昨日总收益</span>
                <span class="text-xl font-mono font-bold text-slate-700 tracking-tight mt-1">
                    <span class="text-sky-500 text-base mr-0.5">¥</span><?php echo round($income_lastday,2)?>
                </span>
            </div>
        </div>

        <!-- 昨日消费 -->
        <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-credit-card text-amber-500 mr-1.5"></i>昨日总消费</span>
                <span class="text-xl font-mono font-bold text-slate-700 tracking-tight mt-1">
                    <span class="text-amber-500 text-base mr-0.5">¥</span><?php echo round($outcome_lastday,2)?>
                </span>
            </div>
        </div>

    </div>

    <!-- 资金流水清单明细 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <i class="fa fa-list-alt text-indigo-500 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">账户收支流水线</h3>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-center w-20">账单 ID</th>
                        <th class="px-6 py-4 font-semibold text-center w-28">交易类型</th>
                        <th class="px-6 py-4 font-semibold text-right w-32">变动金额</th>
                        <th class="px-6 py-4 font-semibold w-1/3">业务详述</th>
                        <th class="px-6 py-4 font-semibold text-center">结算时间戳</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
$numrows=$DB->getColumn("SELECT count(*) from pre_points WHERE zid='{$userrow['zid']}'");

$pagesize=30;
$pages=ceil($numrows/$pagesize);
$page=isset($_GET['page'])?intval($_GET['page']):1;
$offset=$pagesize*($page - 1);

$rs=$DB->query("SELECT * FROM pre_points WHERE zid='{$userrow['zid']}' order by id desc limit $offset,$pagesize");
$hasData = false;

while($res = $rs->fetch())
{
    $hasData = true;
    
    // 资金流向识别机制
    $isIncome = in_array($res['action'], array('提成','奖励','赠送','退款','退回','充值','加款'));
    $amountClass = $isIncome ? 'text-rose-500' : 'text-emerald-500';
    $amountPrefix = $isIncome ? '+ ' : '- ';
    
    // 类型徽章渲染
    $typeBadge = $isIncome 
        ? '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm">'.$res['action'].'</span>'
        : '<span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm">'.$res['action'].'</span>';

    echo '<tr class="hover:bg-slate-50/50 transition-colors group">
            <td class="px-6 py-3.5 text-center">
                <span class="font-mono text-slate-400 font-medium">#'.$res['id'].'</span>
            </td>
            <td class="px-6 py-3.5 text-center">
                '.$typeBadge.'
            </td>
            <td class="px-6 py-3.5 text-right font-mono font-bold '.$amountClass.'">
                '.$amountPrefix.number_format($res['point'], 2).'
            </td>
            <td class="px-6 py-3.5 font-medium text-slate-700 max-w-[250px] truncate" title="'.htmlspecialchars($res['bz']).'">
                '.htmlspecialchars($res['bz']).'
            </td>
            <td class="px-6 py-3.5 text-center font-mono text-[11px] text-slate-400">
                '.$res['addtime'].'
            </td>
          </tr>';
}

if(!$hasData) {
    echo '<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-4xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前您的账户尚未产生任何资金流转记录</p>
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
                
                // 确保分页链接保持原逻辑中的 record.php
                $baseUrl = 'record.php';
                
                // 上一页
                if ($page > 1) {
                    echo '<a href="'.$baseUrl.'?page=' . $first . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="首页"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="'.$baseUrl.'?page=' . $prev . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="上一页"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                // 数字页码
                $start = $page - 4 > 1 ? $page - 4 : 1;
                $end   = $page + 4 < $pages ? $page + 4 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="'.$baseUrl.'?page=' . $i . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="'.$baseUrl.'?page=' . $i . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                // 下一页
                if ($page < $pages) {
                    echo '<a href="'.$baseUrl.'?page=' . $next . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下一页"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="'.$baseUrl.'?page=' . $last . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="尾页"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>

    </div>
</div>

<?php include './foot.php';?>
</body>
</html>
