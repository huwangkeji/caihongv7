<?php
/**
 * 资金注入与余额充值台
**/
include("../includes/common.php");
$title='充值余额';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php?back=recharge';</script>");
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

    <!-- 顶部核心视图：资产与充值 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- 左侧/上侧：注资控制台 -->
        <div class="lg:col-span-7 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mr-3 shrink-0 shadow-sm">
                            <i class="fa fa-google-wallet text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">安全充值中心</h3>
                            <p class="text-xs text-slate-500 m-0 mt-1">目前暂且仅支持作为消费使用</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    
                    <!-- 资产展板 -->
                    <div class="mb-6 p-5 bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl text-white shadow-lg shadow-slate-900/10 flex items-center justify-between relative overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                        <div>
                            <div class="text-slate-400 text-xs font-bold mb-1">当前可用资金</div>
                            <div class="text-3xl font-mono font-bold tracking-tight">
                                <span class="text-emerald-400 mr-1">¥</span><?php echo $userrow['rmb']?>
                            </div>
                        </div>
                        <i class="fa fa-database text-4xl text-white/10"></i>
                    </div>

                    <?php 
                    $rebate_enabled = isset($conf['recharge_rebate_enabled']) ? $conf['recharge_rebate_enabled'] : 1;
                    $rebate_rate = isset($conf['recharge_rebate_rate']) ? $conf['recharge_rebate_rate'] : 3;
                    if ($rebate_enabled == 1) {
                        $example_rebate = round(100 * ($rebate_rate / 100), 2);
                        $example_total = 100 + $example_rebate;
                    ?>
                    <!-- 返利微拟态横幅 -->
                    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3 shadow-sm">
                        <i class="fa fa-gift text-amber-500 mt-0.5 text-lg"></i>
                        <div>
                            <h4 class="text-sm font-bold text-amber-800 mb-1 leading-none">限时加息红利派发中！</h4>
                            <p class="text-xs text-amber-700 font-medium leading-relaxed">
                                今日入金即可额外获赠本金 <b class="text-rose-500 text-sm mx-1"><?php echo $rebate_rate; ?>%</b> 的返利。<br>
                                实时测算：充值 100 元，实际将有 <b class="text-emerald-600 font-mono"><?php echo $example_total; ?></b> 元进入您的余额。
                            </p>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- 输入金额核心 -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">充值金额 <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-bold">
                                    <i class="fa fa-cny"></i>
                                </div>
                                <input type="number" name="value" id="rechargeAmount" class="w-full h-14 pl-10 pr-4 text-xl font-mono font-bold rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors bg-white text-emerald-600 placeholder:text-slate-300 placeholder:font-normal" autocomplete="off" placeholder="输入要充值的数额" oninput="calculateRebate()">
                            </div>
                            
                            <?php if($conf['tixian_limit']==1 || $conf['recharge_min']>0){ ?>
                            <div class="flex items-center gap-2 mt-1 text-[11px] font-bold text-rose-400 pl-1">
                                <i class="fa fa-info-circle"></i>
                                <span>
                                    <?php if($conf['tixian_limit']==1) echo '注资仅能用于系统内消费，无法提现。'; ?>
                                    <?php if($conf['recharge_min']>0) echo '通道设限最低起充额：¥ '.$conf['recharge_min'].'。'; ?>
                                </span>
                            </div>
                            <?php } ?>
                        </div>

                        <!-- 动态返利显影器 -->
                        <div id="rebateInfo" style="display:none;" class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[11px] text-emerald-600 font-bold uppercase tracking-wider mb-1">系统测算返佣</span>
                                <span class="text-lg font-mono font-bold text-amber-500">+¥ <span id="rebateAmount">0.00</span></span>
                            </div>
                            <div class="w-px h-8 bg-emerald-200"></div>
                            <div class="flex flex-col text-right">
                                <span class="text-[11px] text-emerald-600 font-bold uppercase tracking-wider mb-1">最终下发至余额</span>
                                <span class="text-xl font-mono font-bold text-emerald-600">¥ <span id="totalAmount">0.00</span></span>
                            </div>
                        </div>

                        <!-- 支付收银台区 -->
                        <div class="pt-4 border-t border-slate-100">
                            <label class="text-sm font-semibold text-slate-700 ml-1 block mb-3">选择结算通道</label>
                            <div class="grid grid-cols-2 gap-3">
                                <?php if(!empty($conf['codepay_key'])){ ?>
                                <button type="button" id="buy_usdt" class="h-12 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-teal-400 hover:bg-teal-50 text-slate-700 font-bold transition-all shadow-sm active:scale-95 group">
                                    <img src="../other/usdt-trc20/static/img/tether.svg" class="w-5 h-5 group-hover:scale-110 transition-transform"> <span class="text-sm tracking-tight">USDT-TRC20</span>
                                </button>
                                <?php } ?>
                                
                                <?php if($conf['alipay_api']){ ?>
                                <button type="button" id="buy_alipay" class="h-12 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-blue-400 hover:bg-blue-50 text-slate-700 font-bold transition-all shadow-sm active:scale-95 group">
                                    <img src="../assets/img/alipay.png" class="w-5 h-5 group-hover:scale-110 transition-transform"> <span class="text-sm tracking-tight">支付宝渠道</span>
                                </button>
                                <?php } ?>
                                
                                <?php if($conf['qqpay_api']){ ?>
                                <button type="button" id="buy_qqpay" class="h-12 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-sky-400 hover:bg-sky-50 text-slate-700 font-bold transition-all shadow-sm active:scale-95 group">
                                    <img src="../assets/img/qqpay.png" class="w-5 h-5 group-hover:scale-110 transition-transform"> <span class="text-sm tracking-tight">QQ钱包收银</span>
                                </button>
                                <?php } ?>
                                
                                <?php if($conf['wxpay_api']){ ?>
                                <button type="button" id="buy_wxpay" class="h-12 flex items-center justify-center gap-2 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 text-slate-700 font-bold transition-all shadow-sm active:scale-95 group">
                                    <img src="../assets/img/wxpay.png" class="w-5 h-5 group-hover:scale-110 transition-transform"> <span class="text-sm tracking-tight">微信安全付</span>
                                </button>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- 卡密独立加款区 -->
            <?php if($conf['fenzhan_jiakuanka']){?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-purple-50/50 flex items-center">
                    <i class="fa fa-ticket text-purple-500 mr-2 text-lg"></i>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">卡密核销引擎 (零费率加款)</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa fa-barcode"></i>
                        </div>
                        <input type="text" name="km" class="w-full h-12 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-colors bg-slate-50 placeholder:text-slate-400" autocomplete="off" placeholder="请录入系统加款卡密特征码">
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button" id="usekm" class="flex-1 h-11 rounded-xl bg-purple-600 text-white font-bold text-sm hover:bg-purple-700 shadow-md shadow-purple-500/20 transition-all duration-200 active:scale-95">
                            提取校验并核销
                        </button>
                        
                    </div>
                </div>
            </div>
            <?php }?>
            
        </div>

        <!-- 右侧/下侧：入金流水历史 -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
                    <i class="fa fa-history text-slate-400 mr-2 text-lg"></i>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">近期资金注入记录</h3>
                </div>
                
                <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                        <thead class="text-[11px] text-slate-400 uppercase bg-white border-b border-slate-50">
                            <tr>
                                <th class="px-5 py-3 font-semibold">充值金额</th>
                                <th class="px-5 py-3 font-semibold text-center">时间戳</th>
                                <th class="px-5 py-3 font-semibold text-right">状态</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                        <?php
                        $flag=false;
                        $rs=$DB->query("SELECT * FROM pre_points WHERE zid='{$userrow['zid']}' AND action='充值' ORDER BY id DESC LIMIT 10");
                        while($res = $rs->fetch())
                        {
                            $flag=true;
                            echo '<tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-5 py-3.5"><span class="font-mono font-bold text-emerald-600">+¥ '.$res['point'].'</span></td>
                                    <td class="px-5 py-3.5 text-center text-[11px] font-mono text-slate-400">'.$res['addtime'].'</td>
                                    <td class="px-5 py-3.5 text-right"><span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100"><i class="fa fa-check mr-1"></i>核销成功</span></td>
                                  </tr>';
                        }
                        if(!$flag) echo '<tr><td colspan="3" class="px-5 py-12 text-center text-slate-400"><i class="fa fa-file-text-o text-3xl mb-2 text-slate-200 block"></i><span class="text-xs">该区域暂无资金流转记录</span></td></tr>';
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include './foot.php';?>

<script>
// 动态红利推演器
function calculateRebate() {
	var value = parseFloat($("#rechargeAmount").val());
	var rebateInfo = document.getElementById('rebateInfo');
	var rebateAmount = document.getElementById('rebateAmount');
	var totalAmount = document.getElementById('totalAmount');
	
	var rebateEnabled = <?php echo isset($conf['recharge_rebate_enabled']) ? $conf['recharge_rebate_enabled'] : 1;?>;
	var rebateRate = <?php echo isset($conf['recharge_rebate_rate']) ? $conf['recharge_rebate_rate'] : 3;?>;
	
	if (rebateEnabled == 1 && !isNaN(value) && value > 0) {
		var rebate = value * (rebateRate / 100);
		var total = value + rebate;
		rebateAmount.textContent = rebate.toFixed(2);
		totalAmount.textContent = total.toFixed(2);
		
		// 加上 Tailwind 平滑折叠展开
		if(rebateInfo.style.display === 'none') {
		    $(rebateInfo).slideDown(200);
		}
	} else {
		$(rebateInfo).slideUp(200);
	}
}

// 信道握手发令枪
function dopay(type){
	var value = $("#rechargeAmount").val();
	if(value=='' || value==0){
	    layer.msg('阻断：充值额度异常，请重新输入', {icon: 2});
	    return false;
	}
	
	var ii = layer.load(2, {shade: [0.1, '#fff']});
	$.get("ajax_user.php?act=recharge&type="+type+"&value="+value, function(data) {
	    layer.close(ii);
		if(data.code == 0){
		    layer.msg('收银台链路构建成功，正在跳转...', {icon: 16, shade: 0.2, time: 2000});
			window.location.href='../other/submit.php?type='+type+'&orderid='+data.trade_no;
		}else{
			layer.alert(data.msg, {icon: 2, title:'收银台调度阻断'});
		}
	}, 'json');
}

$(document).ready(function(){
    // 法币与数字货币网关挂载
    $("#buy_usdt").click(function(){ dopay('usdt_pay') });
    $("#buy_alipay").click(function(){ dopay('alipay') });
    $("#buy_qqpay").click(function(){ dopay('qqpay') });
    $("#buy_wxpay").click(function(){ dopay('wxpay') });
    
    // 独立卡密核销引擎
    $("#usekm").click(function(){
    	var km = $("input[name='km']").val();
    	if(!km){
    	    layer.msg('无法提取空数据码段', {icon:2});
    	    return false;
    	}
    	
    	var ii = layer.load(2, {shade: [0.1, '#fff']});
    	$.ajax({
    		type : "POST",
    		url : "ajax_user.php?act=usekm",
    		data : {km:km},
    		dataType : 'json',
    		async: true,
    		success : function(data) {
    		    layer.close(ii);
    			if(data.code == 0){
    				layer.alert('底层特征码校准无误：' + data.msg, {icon:1, title:'核销成功'}, function(){ window.location.reload() });
    			}else{
    				layer.alert(data.msg, {icon:2, title:'防伪拦截'});
    			}
    		}
    	});
    });
});
</script>
</body>
</html>
