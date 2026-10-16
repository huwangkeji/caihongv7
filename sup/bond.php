<?php

include "../includes/common.php";
$title = "保证金管理";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

if ($_GET["mod"] == "thaw") {
	$money = round($_POST["money"], 2);
	if ($money > $suprow["bond"]) {
		showmsg("解冻金额不能大于您已缴纳金额！", 3);
	}
	if ($money <= 0) {
		showmsg("解冻金额不能小于等于0！", 3);
	}
	if ($DB->exec("update pre_supplier set rmb = rmb + '" . $money . "', bond = bond - '" . $money . "' where sid='" . $suprow["sid"] . "'") !== false) {
		showmsg("解冻保证金成功！<br/><br/><a href=\"./bond.php?act=thaw\">>>返回保证金解冻</a>", 1);
	} else {
		showmsg("解冻保证金失败！" . $DB->error(), 4);
	}
} elseif ($_GET["mod"] == "pay") {
	$money = round($_POST["money"], 2);
	if ($money > $suprow["rmb"]) {
		showmsg("您的余额不足以缴纳，请充值后再来！", 3);
	}
	if ($money <= 0) {
		showmsg("缴纳金额不能小于等于0！", 3);
	}
	if ($DB->exec("update pre_supplier set rmb = rmb - '" . $money . "', bond = bond + '" . $money . "' where sid='" . $suprow["sid"] . "'") !== false) {
		showmsg("缴纳保证金成功！<br/><br/><a href=\"./bond.php\">>>返回保证金缴纳</a>", 1);
	} else {
		showmsg("缴纳保证金失败！" . $DB->error(), 4);
	}
}
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

    <!-- 顶部数据概览 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-flag text-slate-400 mr-1.5"></i>需缴纳</span>
                <span class="text-2xl font-mono font-bold text-slate-700 tracking-tight"><?php echo $conf["sup_bond"];?> <span class="text-sm font-normal">元</span></span>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-lock text-emerald-500 mr-1.5"></i>已缴纳</span>
                <span class="text-2xl font-mono font-bold text-emerald-600 tracking-tight"><?php echo $suprow["bond"];?> <span class="text-sm font-normal">元</span></span>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-google-wallet text-sky-500 mr-1.5"></i>您的余额</span>
                <span class="text-2xl font-mono font-bold text-sky-600 tracking-tight"><?php echo $suprow["rmb"];?> <span class="text-sm font-normal">元</span></span>
            </div>
        </div>
    </div>

    <!-- 左右双排布局 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- 左侧：操作表单 -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
                
                <?php if (isset($_GET["act"]) && $_GET["act"] == "thaw") { ?>
                <!-- 保证金解冻 -->
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                        <i class="fa fa-unlock text-emerald-500 mr-2 text-xl"></i>保证金解冻
                    </h3>
                    <a href="./bond.php" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors">切换至缴纳</a>
                </div>
                
                <div class="p-6 flex-1 flex flex-col">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-6 flex items-start text-emerald-700 font-medium text-sm leading-relaxed shadow-inner">
                        <i class="fa fa-info-circle text-emerald-500 mr-2 mt-0.5 text-lg"></i>
                        解冻金额前需将所有商品隐藏即可解冻
                    </div>
                    
                    <form action="?mod=thaw" method="post" class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">解冻金额</label>
                            <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 transition-all bg-white">
                                <div class="pl-4 flex items-center pointer-events-none text-emerald-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="money" class="flex-1 h-12 pl-2 pr-4 text-base font-mono font-bold border-0 focus:ring-0 text-emerald-600 placeholder:text-slate-300 placeholder:font-normal" placeholder="解冻金额" required>
                            </div>
                        </div>
                        
                        <div class="pt-4 mt-auto">
                            <button type="submit" name="submit" class="w-full h-14 rounded-xl bg-emerald-500 text-white font-bold text-[15px] shadow-md shadow-emerald-500/20 hover:bg-emerald-600 active:scale-95 transition-all">
                                解冻
                            </button>
                        </div>
                    </form>
                </div>

                <?php } else { ?>
                <!-- 保证金缴纳 -->
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                        <i class="fa fa-lock text-indigo-500 mr-2 text-xl"></i>保证金缴纳
                    </h3>
                    <a href="./bond.php?act=thaw" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition-colors">切换至解冻</a>
                </div>
                
                <div class="p-6 flex-1 flex flex-col">
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mb-6 flex items-start text-indigo-700 font-medium text-sm leading-relaxed shadow-inner">
                        <i class="fa fa-bullhorn text-indigo-500 mr-2 mt-0.5 text-lg"></i>
                        缴纳足够的保证金即可上架商品
                    </div>
                    
                    <form action="?mod=pay" method="post" class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">缴纳金额</label>
                            <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                                <div class="pl-4 flex items-center pointer-events-none text-indigo-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="money" class="flex-1 h-12 pl-2 pr-4 text-base font-mono font-bold border-0 focus:ring-0 text-indigo-600 placeholder:text-slate-300 placeholder:font-normal" placeholder="缴纳金额" required>
                            </div>
                        </div>
                        
                        <div class="pt-4 mt-auto">
                            <button type="submit" name="submit" class="w-full h-14 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all">
                                缴纳
                            </button>
                        </div>
                    </form>
                </div>
                <?php } ?>
                
            </div>
        </div>

        <!-- 右侧：保证金介绍 -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center shrink-0">
                    <i class="fa fa-book text-amber-500 mr-2 text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">保证金介绍</h3>
                </div>
                
                <div class="p-6 space-y-6 flex-1">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center font-bold text-sm shrink-0 mr-3 mt-0.5">1</div>
                        <div>
                            <p class="text-sm text-rose-600 leading-relaxed font-bold m-0">1.缴纳保证金上架商品免审（需符合平台要求，否则会受到处罚或封号）！</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center font-bold text-sm shrink-0 mr-3 mt-0.5">2</div>
                        <div>
                            <p class="text-sm text-amber-600 leading-relaxed font-bold m-0">2.保证金可随时解冻到余额进行提现，也可以联系客服将余额缴纳到保证金！</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php 
// 保持原有闭合逻辑
?>
