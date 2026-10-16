<?php
/**
 * 站点架构自助升级控制台
**/
$is_defend=true;
include("../includes/common.php");
$title='自助升级站点';
include './head.php';

// 登录态防泄漏鉴权
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
// 系统核心级与权限级阻断
if(!$conf['fenzhan_upgrade']) showmsg('通道管控：总台暂未开放自助越级升档功能',3);
if($userrow['power']==0){
	showmsg('权限阻断：您当前无权使用此高阶功能！',3);
}

$price = $conf['fenzhan_upgrade'];

// 动态提取上级代理的自定义升档售价
if($userrow['upzid']>1){
	$upsite = $DB->getRow("SELECT zid,power,ktfz_price2 FROM pre_site WHERE zid='{$userrow['upzid']}' LIMIT 1");
	if($upsite && $upsite['power']==2){
		if($upsite['ktfz_price2'] && $upsite['ktfz_price2']>0){
			$price = $upsite['ktfz_price2'];
		}
		// 核算分佣剪刀差
		$tc_point=round($price-$conf['fenzhan_cost2'], 2);
	}
}

// 核心资产扣减与升级指令
if($_GET['act']=='submit'){
	if(!checkRefererHost())exit();
	
	if($price>$userrow['rmb']){
	    exit("<script language='javascript'>layer.msg('资产拦截：您金库的可用余额不足以支付升级费用，请注资！', {icon:2, time:2500}, function(){window.location.href='./recharge.php';});</script>");
	}
	
	// 执行阶跃
	$DB->exec("UPDATE `pre_site` SET `power`=2,`rmb`=`rmb`-'{$price}' WHERE `zid`='{$userrow['zid']}'");
	addPointRecord($userrow['zid'], $price, '消费', '升级到专业版分站');
	
	// 给上线分发提成红利
	if(isset($tc_point) && $tc_point>0){
		changeUserMoney($upsite['zid'], $tc_point, true, '提成', '你网站的用户升级分站获得'.$tc_point.'元提成');
	}
	
	exit("<script language='javascript'>layer.msg('恭喜您，站点已成功完成阶跃，正式升级至专业版架构！', {icon:1, time:2500}, function(){window.location.href='index.php';});</script>");
}
?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 左侧：特权展板区 -->
        <div class="bg-gradient-to-br from-indigo-500 to-sky-600 rounded-2xl shadow-lg p-6 sm:p-8 text-white relative overflow-hidden flex flex-col justify-center">
            <!-- 科技感光晕背景 -->
            <div class="absolute -right-10 -top-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-sky-300/20 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm shadow-inner border border-white/20 mb-6">
                    <i class="fa fa-diamond text-3xl text-amber-300 drop-shadow-md"></i>
                </div>
                
                <h3 class="text-2xl font-bold tracking-tight mb-2 drop-shadow">全功能专业版</h3>
                <p class="text-indigo-100 text-sm font-medium mb-8">提升基础权限，成为高阶合伙人</p>
                
                <ul class="space-y-5 text-sm font-medium text-white/90">
                    <li class="flex items-start bg-white/5 p-3 rounded-xl border border-white/10">
                        <i class="fa fa-check-circle text-emerald-400 mt-0.5 mr-3 text-lg shrink-0"></i>
                        <span class="leading-relaxed">解锁旗下分站裂变与管理权限，无上限提取下级分站的流转红利与提成</span>
                    </li>
                    <li class="flex items-start bg-white/5 p-3 rounded-xl border border-white/10">
                        <i class="fa fa-check-circle text-emerald-400 mt-0.5 mr-3 text-lg shrink-0"></i>
                        <span class="leading-relaxed">掌握核心定价主轴，支持自定义下辖站点的开通资费与子商品结算底价</span>
                    </li>
                    <li class="flex items-start bg-white/5 p-3 rounded-xl border border-white/10">
                        <i class="fa fa-check-circle text-emerald-400 mt-0.5 mr-3 text-lg shrink-0"></i>
                        <span class="leading-relaxed">额外受领由系统独家派发的专属 APP 客户端控制权限</span>
                    </li>
                    <li class="flex items-start bg-white/5 p-3 rounded-xl border border-white/10">
                        <i class="fa fa-spinner fa-spin text-sky-300 mt-0.5 mr-3 text-lg shrink-0"></i>
                        <span class="leading-relaxed">更多全网首发的特权红利引擎正在持续装配迭代中...</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 右侧：升级操作台 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col relative h-full">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <i class="fa fa-arrow-circle-up text-indigo-500 mr-2 text-xl"></i>升级操作台
                </h3>
                <div class="text-[11px] font-mono font-bold text-slate-500 bg-white px-2 py-1 rounded border border-slate-200 shadow-sm flex items-center">
                    <i class="fa fa-google-wallet mr-1"></i>可用余额: <span class="text-emerald-600 ml-1">¥ <?php echo $userrow['rmb']?></span>
                </div>
            </div>
            
            <div class="p-6 space-y-6 flex-1 flex flex-col justify-center">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">选择版本</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-500"><i class="fa fa-diamond"></i></div>
                        <select class="w-full h-12 pl-10 pr-4 text-sm font-bold rounded-xl border-amber-200 bg-amber-50/50 text-amber-700 shadow-inner cursor-not-allowed focus:ring-0" disabled>
                            <option value="2">全功能专业版 (Pro)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5 p-5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between mt-2">
                    <div>
                        <div class="text-sm font-semibold text-rose-700 mb-0.5">所需费用</div>
                        <div class="text-[11px] text-rose-500">将从您的账户可用余额中直扣</div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-sm font-bold text-rose-500 pb-1">¥</span>
                        <span class="text-3xl font-bold font-mono text-rose-600 tracking-tight leading-none"><?php echo $price?></span>
                    </div>
                </div>

                <!-- 利用 margin-auto 把按钮推到最下面对齐 -->
                <div class="pt-4 mt-auto">
                    <button type="button" onclick="confirmUpgrade()" class="w-full h-14 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-600 text-white font-bold text-lg shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-700 active:scale-95 transition-all flex items-center justify-center">
                        <i class="fa fa-rocket mr-2"></i>授权扣费并升级
                    </button>
                </div>
                
                <div class="text-center mt-2">
                    <p class="text-[11px] font-medium text-slate-400 m-0">
                        <i class="fa fa-shield mr-1"></i>此步骤不可逆，点击确认即代表您接受最终的结算价。
                    </p>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<?php include './foot.php';?>

<script>
// 防误触：升级扣费二次确认弹窗
function confirmUpgrade() {
    layer.confirm('<div class="text-center mt-2">系统将核销您金库内的 <b class="text-rose-500 text-lg font-mono">¥ <?php echo $price?></b> 元以扩建底层权限。<br>该操作不可逆，请确认是否立刻启动升档程序？</div>', {
        title: '<div class="font-bold text-slate-800"><i class="fa fa-arrow-circle-up text-indigo-500 mr-2"></i>跃迁支付确认协议</div>',
        icon: 3,
        btn: ['确认支付并升级', '暂缓变更'],
        skin: 'layui-layer-rim rounded-2xl'
    }, function(){
        // 挂载遮罩防重复点击
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        window.location.href = '?act=submit';
    });
}
</script>
</body>
</html>
