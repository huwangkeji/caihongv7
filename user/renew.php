<?php
/**
 * 自助续期站点
**/
include("../includes/common.php");
$title='自助续期站点';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[650px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if($userrow['power']==0){
	showmsg('权限阻断：您没有权限使用此高阶功能！',3);
}

// 代理阶梯价格与上级价格继承逻辑
if($userrow['power']==2){
	$price = $conf['fenzhan_price2'];
	if($userrow['upzid']>1){
		$ktfz_price2 = $DB->getColumn("SELECT ktfz_price2 FROM pre_site WHERE zid='{$userrow['upzid']}' LIMIT 1");
		if($ktfz_price2 && $ktfz_price2>0){
			$price = $ktfz_price2;
		}
	}
}else{
	$price = $conf['fenzhan_price'];
	if($userrow['upzid']>1){
		$ktfz_price = $DB->getColumn("SELECT ktfz_price FROM pre_site WHERE zid='{$userrow['upzid']}' LIMIT 1");
		if($ktfz_price && $ktfz_price>0){
			$price = $ktfz_price;
		}
	}
}

// 续期时间运算
$fenzhan_expiry = $conf['fenzhan_expiry']>0 ? $conf['fenzhan_expiry'] : 12;
if($userrow['endtime']>date("Y-m-d")) {
    $endtime = date("Y-m-d", strtotime("+ {$fenzhan_expiry} months", strtotime($userrow['endtime'])));
} else {
    $endtime = date("Y-m-d", strtotime("+ {$fenzhan_expiry} months"));
}

// 续期扣费入库动作
if($_GET['act']=='submit'){
	if($price>$userrow['rmb']) {
        exit("<script language='javascript'>layer.msg('您的可用余额不足，请先前往充值！', {icon:2, time:2000}, function(){window.location.href='./recharge.php';});</script>");
    }
	$DB->exec("UPDATE `pre_site` SET `endtime`='$endtime',`rmb`=`rmb`-'{$price}' WHERE `zid`='{$userrow['zid']}'");
	addPointRecord($userrow['zid'], $price, '消费', '自助续期站点');
	exit("<script language='javascript'>layer.msg('恭喜您，站点已成功续期至 {$endtime}！', {icon:1, time:2000}, function(){window.location.href='index.php';});</script>");
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0">
                <i class="fa fa-calendar-plus-o text-lg"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">自助续期站点</h3>
                <p class="text-xs text-slate-500 m-0 mt-1">延长您的分站使用期限，确保业务流转持续稳定</p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            
            <!-- 当前到期时间 -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">当前到期时间</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed focus:ring-0 focus:border-slate-200" value="<?php echo $userrow['endtime']?>" disabled/>
                </div>
            </div>

            <!-- 续期后时间 (高亮显示) -->
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">续期后到期时间</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500">
                        <i class="fa fa-calendar-check-o"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-emerald-200 bg-emerald-50/50 text-emerald-600 shadow-inner cursor-not-allowed focus:ring-0 focus:border-emerald-200" value="<?php echo $endtime?>" disabled/>
                </div>
            </div>

            <!-- 费用结算区 -->
            <div class="space-y-1.5 p-5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between mt-2">
                <div>
                    <div class="text-sm font-semibold text-rose-700 mb-0.5">续期所需费用</div>
                    <div class="text-[11px] text-rose-500">将从您的账户可用余额中直扣</div>
                </div>
                <div class="flex items-end gap-1">
                    <span class="text-sm font-bold text-rose-500 pb-1">¥</span>
                    <span class="text-3xl font-bold font-mono text-rose-600 tracking-tight leading-none"><?php echo $price?></span>
                </div>
            </div>

            <!-- 提交动作 -->
            <div class="pt-2">
                <a href="javascript:void(0)" onclick="confirmRenew()" class="flex items-center justify-center w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                    <i class="fa fa-check-circle-o mr-1.5"></i>确认支付并续期
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php include './foot.php';?>

<script>
// 增加二次确认防误触机制
function confirmRenew() {
    layer.confirm('系统将扣除您 <b style="color:red;"><?php echo $price?></b> 元可用余额进行站点续期，是否确认？', {
        title: '续期资产核算确认',
        icon: 3,
        btn: ['确认支付续期', '暂不操作']
    }, function(){
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        window.location.href = '?act=submit';
    });
}
</script>
</body>
</html>
