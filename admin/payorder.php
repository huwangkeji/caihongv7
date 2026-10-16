<?php
include "../includes/common.php";
$title = "支付记录";
include "./head.php";
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<style type="text/tailwindcss">
@layer utilities {
    /* 表格基础重构 */
    .orderList { @apply w-full text-sm text-left text-slate-600 whitespace-nowrap; }
    .orderList thead { @apply text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100; }
    .orderList thead th { @apply px-4 py-3.5 font-semibold text-center tracking-wider text-[13px] sm:text-[14px]; }
    .orderList tbody tr { @apply border-b border-slate-50 hover:bg-brand-50/40 transition-colors duration-200; }
    .orderList tbody td { @apply px-4 py-3 align-middle; }
    
    /* 根据原有逻辑映射列宽与对齐 */
    .orderList tbody tr>td:nth-child(3), .orderList tbody tr>td:nth-child(4) { @apply max-w-[320px] whitespace-normal break-all text-[13px] leading-snug; }
    .orderList tbody tr>td:nth-child(5), .orderList tbody tr>td:nth-child(6), .orderList tbody tr>td:nth-child(8) { @apply min-w-[65px] text-center; }
    .orderList tbody tr>td:nth-child(7) { @apply min-w-[150px] text-center; }
}
</style>

<div class="w-full max-w-[1600px] mx-auto">
    <?php adminpermission("shop", 1); ?>
    
    <!-- 搜索与操作面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-5 lg:p-6 mb-6">
        
        <!-- 头部标题与分页设置 -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-5 pb-4 border-b border-slate-50 gap-3 sm:gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center m-0" id="blocktitle">
                <!-- JS 会自动写入此处的统计文本 -->
            </h3>
            
            <div class="flex items-center bg-slate-50 px-3 py-1.5 rounded-lg sm:rounded-xl border border-slate-100 shrink-0">
                <span class="text-xs font-medium text-slate-500 mr-2 whitespace-nowrap">每页显示</span>
                <select id="pagesize" class="bg-transparent border-none text-[13px] sm:text-sm font-semibold text-brand-600 focus:ring-0 p-0 pr-6 cursor-pointer">
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="80">80</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- 搜索表单 -->
        <form onsubmit="return searchItem()" method="GET" class="space-y-3 sm:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                <!-- 搜索字段选择 -->
                <select class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white" name="column">
                    <option value="trade_no">支付订单号</option>
                    <option value="api_trade_no">支付接口订单号</option>
                    <option value="zid">站点ID</option>
                    <option value="userid">用户ID</option>
                    <option value="tid">商品ID</option>
                    <option value="name">订单名称</option>
                    <option value="money">支付金额</option>
                    <option value="input">下单内容</option>
                    <option value="ip">用户IP</option>
                </select>

                <!-- 关键字输入框 -->
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400 text-xs sm:text-sm"></i>
                    </div>
                    <input type="text" class="w-full h-9 sm:h-10 pl-8 sm:pl-9 pr-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入搜索内容">
                </div>
                
                <!-- 支付方式筛选 -->
                <select name="type" class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white" default="0">
                    <option value="all">所有支付方式</option>
                    <option value="alipay">支付宝</option>
                    <option value="wxpay">微信支付</option>
                    <option value="qqpay">QQ钱包</option>
                </select>

                <!-- 支付状态筛选 -->
                <select id="dstatus" class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white">
                    <option value="0">显示全部状态</option>
                    <option value="2">只显示已支付</option>
                    <option value="1">只显示未支付</option>
                </select>
            </div>
            
            <!-- 按钮区域 -->
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-3 pt-1 sm:pt-2">
                <button type="submit" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-6 rounded-lg sm:rounded-xl bg-brand-600 text-white font-medium text-[13px] sm:text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                    <i class="fa fa-search mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>搜 索
                </button>
                <a href="javascript:clearItem()" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-5 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-600 font-medium text-[13px] sm:text-sm hover:text-brand-600 hover:bg-brand-50 hover:border-brand-200 shadow-sm transition-all duration-200" title="刷新支付记录列表">
                    <i class="fa fa-refresh mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>刷新列表
                </a>
            </div>
        </form>
    </div>

    <!-- 表格展示区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden relative">
        <div id="listTable" class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200"></div>
    </div>
</div>
      
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/payorder.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>
