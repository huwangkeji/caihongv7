<?php

include "../includes/common.php";
$title = "充值余额";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php?back=recharge';</script>");
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 左侧：充值操作区 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
                <i class="fa fa-google-wallet text-indigo-500 mr-2 text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">充值余额</h3>
            </div>
            
            <div class="p-6 flex-1 flex flex-col items-center justify-center">
                <!-- 余额展示 -->
                <div class="text-center mb-8">
                    <p class="text-sm font-medium text-slate-500 mb-2">当前账户余额</p>
                    <div class="text-4xl font-mono font-bold text-slate-800 tracking-tight">
                        <span class="text-2xl text-slate-400 mr-1">¥</span><?php echo $suprow["rmb"];?>
                    </div>
                </div>
                
                <!-- 充值输入与按钮 -->
                <div class="w-full max-w-sm space-y-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">¥</div>
                        <input type="text" name="value" class="w-full h-12 pl-10 pr-4 text-lg font-mono font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 transition-colors placeholder:font-normal placeholder:text-slate-400" autocomplete="off" placeholder="输入要充值的金额">
                    </div>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <?php if ($conf["alipay_api"]) { ?>
                        <button type="button" id="buy_alipay" class="flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-blue-100 text-blue-600 font-bold text-sm hover:bg-blue-50 hover:border-blue-200 shadow-sm transition-all active:scale-95">
                            <img src="../assets/img/alipay.png" class="w-6 h-6 mb-1.5 opacity-90">
                            支付宝
                        </button>
                        <?php } ?>
                        
                        <?php if ($conf["qqpay_api"]) { ?>
                        <button type="button" id="buy_qqpay" class="flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-sky-100 text-sky-600 font-bold text-sm hover:bg-sky-50 hover:border-sky-200 shadow-sm transition-all active:scale-95">
                            <img src="../assets/img/qqpay.png" class="w-6 h-6 mb-1.5 opacity-90">
                            QQ钱包
                        </button>
                        <?php } ?>
                        
                        <?php if ($conf["wxpay_api"]) { ?>
                        <button type="button" id="buy_wxpay" class="flex flex-col items-center justify-center p-3 rounded-xl bg-white border border-emerald-100 text-emerald-600 font-bold text-sm hover:bg-emerald-50 hover:border-emerald-200 shadow-sm transition-all active:scale-95">
                            <img src="../assets/img/wxpay.png" class="w-6 h-6 mb-1.5 opacity-90">
                            微信支付
                        </button>
                        <?php } ?>
                    </div>
                    
                    <!-- 保留原代码中的隐藏按钮，防止外部依赖断裂 -->
                    <button type="button" class="hidden" data-toggle="modal" data-target="#myModa4" id="alink"></button>
                </div>
            </div>
        </div>
        
        <!-- 右侧：充值记录区 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
                <i class="fa fa-list-alt text-sky-500 mr-2 text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">充值记录</h3>
            </div>
            
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 flex-1">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 font-semibold">充值金额</th>
                            <th class="px-6 py-4 font-semibold text-center">状态</th>
                            <th class="px-6 py-4 font-semibold text-right">充值时间</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
<?php 
$flag = false;
$rs = $DB->query("SELECT * FROM pre_suppoints WHERE sid='" . $suprow["sid"] . "' AND action='1' ORDER BY id DESC LIMIT 10");
while ($res = $rs->fetch()) {
	$flag = true;
	echo "<tr class='hover:bg-slate-50/50 transition-colors'>";
    echo "<td class='px-6 py-4 font-mono font-bold text-emerald-600'><span class='text-emerald-400 mr-1'>¥</span>" . $res["point"] . "</td>";
    echo "<td class='px-6 py-4 text-center'><span class='px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-bold shadow-sm'>已完成</span></td>";
    echo "<td class='px-6 py-4 text-right font-mono text-xs text-slate-400'>" . $res["addtime"] . "</td>";
    echo "</tr>";
}
if (!$flag) {
	echo "<tr><td colspan='3' class='px-6 py-16 text-center text-slate-400'>
            <i class='fa fa-inbox text-5xl mb-4 text-slate-200 block'></i>
            <p class='text-sm font-medium m-0'>暂无充值记录</p>
          </td></tr>";
}
?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

</div>

<?php 
include "./foot.php";
?>

<script>
function dopay(type){
	var value=$("input[name='value']").val();
	if(value=='' || value==0){
        layer.msg('充值金额不能为空', {icon: 2});
        return false;
    }
    var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.get("ajax_user.php?act=recharge&type="+type+"&value="+value, function(data) {
        layer.close(ii);
		if(data.code == 0){
			window.location.href='../other/submit.php?type='+type+'&orderid='+data.trade_no;
		}else{
			layer.alert(data.msg, {icon: 2});
		}
	}, 'json');
}

$(document).ready(function(){
    $("#buy_alipay").click(function(){
    	dopay('alipay');
    });
    $("#buy_qqpay").click(function(){
    	dopay('qqpay');
    });
    $("#buy_wxpay").click(function(){
    	dopay('wxpay');
    });
});
</script>
