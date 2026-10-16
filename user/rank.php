<?php
/**
 * 分站大盘营收排行榜
**/
include("../includes/common.php");
$title='今日分站排行';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if(!$conf['fenzhan_rank'])showmsg('权限阻断：当前站点未开启分站营收排行功能',3);
if($userrow['power']==0){
	showmsg('权限阻断：您当前为普通用户，没有权限查看合伙人分站榜单！',3);
}

$thtime=date("Y-m-d").' 00:00:00';
$lastday=date("Y-m-d",strtotime("-1 day")).' 00:00:00';
$limit = $conf['rank_reward']>10 ? $conf['rank_reward'] : 10;
$isYesterday = (isset($_GET['last']) && $_GET['last']==1);

if($isYesterday){
	$sql = "select a.zid,(select b.sitename from pre_site as b where a.zid=b.zid) as sitename,count(id) as count,sum(money) as money from pre_orders as a where addtime>'$lastday' and addtime<'$thtime' and zid>1 group by zid order by money desc limit {$limit}";
	$addstr = '已发放奖励';
}else{
	$sql = "select a.zid,(select b.sitename from pre_site as b where a.zid=b.zid) as sitename,count(id) as count,sum(money) as money from pre_orders as a where addtime>'$thtime' and zid>1 group by zid order by money desc limit {$limit}";
	$addstr = '预计发放奖励';
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部看板 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0 shadow-sm">
                    <i class="fa fa-line-chart text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">分站销售风云榜</h3>
                    <p class="text-xs text-slate-500 m-0 mt-1">展示全站顶尖分站合伙人的实时营收数据</p>
                </div>
            </div>
            <div class="text-xs font-mono text-slate-400 bg-white border border-slate-100 px-3 py-1.5 rounded-lg shadow-sm">
                <i class="fa fa-clock-o mr-1"></i>排行榜基准：Top <?php echo $limit; ?>
            </div>
        </div>

        <div class="p-6">
            <!-- 胶囊导航栏：代替原本旧版 nav-tabs -->
            <div class="flex p-1 bg-slate-100 rounded-xl mb-6 shadow-inner max-w-sm mx-auto">
                <a href="rank.php" class="flex-1 py-2 text-center rounded-lg text-sm transition-all duration-200 <?php echo !$isYesterday ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-700'; ?>">
                    <i class="fa fa-sun-o mr-1.5"></i>今日销售排行
                </a>
                <a href="rank.php?last=1" class="flex-1 py-2 text-center rounded-lg text-sm transition-all duration-200 <?php echo $isYesterday ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-700'; ?>">
                    <i class="fa fa-moon-o mr-1.5"></i>昨日销售排行
                </a>
            </div>

            <!-- 数据列盘 -->
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-4 font-semibold text-center w-16">榜单座次</th>
                            <th class="px-5 py-4 font-semibold text-center">站点标识 (UID)</th>
                            <th class="px-5 py-4 font-semibold">分站名称</th>
                            <th class="px-5 py-4 font-semibold text-center">成交订单数</th>
                            <th class="px-5 py-4 font-semibold text-center">总销售额</th>
                            <?php echo $conf['rank_reward']>0 ? '<th class="px-5 py-4 font-semibold text-center">'.$addstr.'</th>' : null; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
<?php
$rs=$DB->query($sql);
$i=1;
$hasData = false;

while($res = $rs->fetch())
{
    $hasData = true;
    
    // 名次专属荣誉样式
    if ($i == 1) {
        $rankBadge = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-amber-300 to-yellow-500 text-white font-bold shadow-md shadow-yellow-500/30 text-xs">1</span>';
    } elseif ($i == 2) {
        $rankBadge = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-slate-300 to-slate-400 text-white font-bold shadow-md shadow-slate-400/30 text-xs">2</span>';
    } elseif ($i == 3) {
        $rankBadge = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-orange-300 to-rose-400 text-white font-bold shadow-md shadow-orange-400/30 text-xs">3</span>';
    } else {
        $rankBadge = '<span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-500 font-bold border border-slate-200 text-xs">'.$i.'</span>';
    }

    echo '<tr class="hover:bg-indigo-50/30 transition-colors group">
            <td class="px-5 py-3.5 text-center">
                '.$rankBadge.'
            </td>
            <td class="px-5 py-3.5 text-center">
                <span class="font-mono text-slate-500 font-bold">#'.$res['zid'].'</span>
            </td>
            <td class="px-5 py-3.5 font-bold text-slate-700 max-w-[200px] truncate" title="'.htmlspecialchars($res['sitename']).'">
                '.mb_substr($res['sitename'], 0, 10, 'utf-8').'
            </td>
            <td class="px-5 py-3.5 text-center">
                <span class="font-bold text-slate-600">'.$res['count'].' <small class="text-slate-400 font-normal">单</small></span>
            </td>
            <td class="px-5 py-3.5 text-center">
                <span class="font-mono font-bold text-emerald-600">¥ '.$res['money'].'</span>
            </td>';
            
    if($conf['rank_reward']>0){
        if($i<=$conf['rank_reward']) $reward = round($res['money'] * $conf['rank_percentage'] / 100, 2);
        else $reward = 0;
        
        $rewardClass = $reward > 0 ? 'text-rose-500 font-bold font-mono' : 'text-slate-400 font-mono';
        echo '<td class="px-5 py-3.5 text-center">
                <span class="'.$rewardClass.'">'.($reward > 0 ? '+¥ '.$reward : '¥ 0.00').'</span>
              </td>';
    }
    echo '</tr>';
    $i++;
}

if(!$hasData){
    $cols = $conf['rank_reward']>0 ? 6 : 5;
    echo '<tr><td colspan="'.$cols.'" class="px-6 py-12 text-center text-slate-400">
            <i class="fa fa-bar-chart text-4xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前时段暂无任何合伙人的销售流水记录</p>
          </td></tr>';
}
?>
                    </tbody>
                </table>
            </div>

            <!-- 底部奖励机制释义说明 -->
            <?php if($conf['rank_reward'] > 0){ ?>
            <div class="mt-6 p-4 bg-sky-50 border border-sky-100 rounded-xl flex items-start gap-3">
                <i class="fa fa-gift text-sky-500 mt-0.5 text-lg"></i>
                <div class="text-sm text-sky-800 leading-relaxed font-medium">
                    <strong class="block mb-1 text-sky-900">站长冲榜提成奖励规则：</strong>
                    系统将会在每日凌晨 <code class="bg-white px-1.5 py-0.5 rounded text-sky-600 font-bold mx-0.5 shadow-sm">00:00</code> 后，自动为昨日销量排行榜的前 <b class="text-rose-500 text-base mx-1"><?php echo $conf['rank_reward']?></b> 名合伙人发放冲榜奖励金。当前的额外提成奖励比例为总流水的 <b class="text-rose-500 text-base mx-1"><?php echo $conf['rank_percentage']?>%</b>！
                </div>
            </div>
            <?php } ?>

        </div>
    </div>
</div>

<?php include './foot.php';?>
</body>
</html>
