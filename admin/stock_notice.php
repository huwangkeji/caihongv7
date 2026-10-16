<?php

include "../includes/common.php";
$title = "库存不足提醒";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

adminpermission("faka", 1);

// 先行计算库存报警数据，以便于前端判断是否展示缺省状态页
$stockrows = $DB->getAll("SELECT `tid`,`name`,`active` FROM `pre_tools` WHERE `active` = 1 AND `close` = 0 AND `is_curl` = 4");
$warning_items = [];
foreach ($stockrows as $stockrow) {
	$stockTotal = $DB->getColumn("SELECT count(`tid`) FROM `pre_faka` WHERE `orderid` = 0 AND `tid` = :tid", array(":tid" => $stockrow["tid"]));
	if ($stockTotal < 1) {
		$warning_items[] = $stockrow;
	}
}
$count = count($warning_items);
?>
<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        
        <!-- 头部预警状态栏 -->
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center">
                <i class="fa fa-bell text-rose-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">自动发卡缺货预警面板</h3>
            </div>
            <span class="px-3 py-1 rounded-full <?php echo $count > 0 ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-emerald-50 text-emerald-600 border-emerald-200'; ?> border text-xs font-bold shadow-sm">
                当前有 <?php echo $count; ?> 个商品库存告急
            </span>
        </div>

        <!-- 监控规则说明 -->
        <div class="p-6 border-b border-slate-50">
            <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 text-sm shadow-inner flex items-start">
                <i class="fa fa-info-circle mt-0.5 mr-2 text-lg text-slate-400 shrink-0"></i>
                <div class="leading-relaxed">
                    <strong class="font-bold text-slate-700">库存监控过滤规则说明：</strong><br/>
                    本系统仅主动监控状态正常且业务逻辑为“自动发卡”的在售商品。已被管理员隐藏、手动下架、或者属于第三方对接（非自营发卡）的商品将自动被风控豁免拦截，不会在此处进行无意义的缺货报警。
                </div>
            </div>
        </div>

        <!-- 预警清单展示区 -->
        <div class="w-full">
            <?php if($count > 0): ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach($warning_items as $item): ?>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-6 hover:bg-rose-50/30 transition-colors duration-200 gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center shrink-0 shadow-sm mt-0.5 sm:mt-0 text-xl border border-rose-200">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                                <div class="flex flex-col justify-center min-h-[48px]">
                                    <div class="text-slate-800 font-bold text-base mb-1.5 pr-4 leading-tight">
                                        <?php echo htmlspecialchars($item["name"]); ?>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-xs font-mono">
                                        <span class="text-slate-400 font-semibold bg-slate-100 px-2 py-0.5 rounded border border-slate-200">商品关联 TID: #<?php echo $item["tid"]; ?></span>
                                        <span class="text-slate-300">|</span>
                                        <span class="text-rose-600 font-semibold bg-rose-50 px-2 py-0.5 rounded border border-rose-100"><i class="fa fa-times-circle mr-1"></i>可用卡密库存已清空 (0)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="shrink-0 flex items-center">
                                <a href="./fakakms.php?my=add&tid=<?php echo $item["tid"]; ?>" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                                    <i class="fa fa-plus-circle mr-1.5"></i>前往补充卡密
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- 缺省状态：库存健康 -->
                <div class="px-6 py-24 text-center">
                    <div class="w-20 h-20 mx-auto bg-emerald-50 rounded-full flex items-center justify-center mb-4 shadow-sm border border-emerald-100">
                        <i class="fa fa-check text-4xl text-emerald-500 drop-shadow-sm"></i>
                    </div>
                    <p class="text-lg font-bold text-emerald-600 mb-2">库存状态健康，业务运转正常</p>
                    <p class="text-sm font-medium text-slate-500">当前所有在售的“自动发卡”类型商品库存均处于充盈状态，暂无需人工介入补充卡密。</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
