<?php
/**
 * 订单管理
**/
include("../includes/common.php");
$title = '订单管理';
include './head.php';
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<!-- 针对 JS 动态生成的表格类名映射 Tailwind 样式 -->
<style type="text/tailwindcss">
@layer utilities {
    /* 表格基础重构 */
    .orderList { @apply w-full text-sm text-left text-slate-600 whitespace-nowrap; }
    .orderList thead { @apply text-xs text-slate-500 uppercase bg-slate-50/80 border-b border-slate-100; }
    .orderList thead th { @apply px-4 py-3 font-semibold text-center tracking-wider; }
    .orderList tbody tr { @apply border-b border-slate-50 hover:bg-brand-50/40 transition-colors duration-200; }
    .orderList tbody td { @apply px-4 py-3.5 align-middle; }
    
    /* 根据原有逻辑映射列宽与对齐 */
    .orderList tbody tr>td:nth-child(1) { @apply min-w-[60px] text-center font-medium text-slate-700; }
    .orderList tbody tr>td:nth-child(2), .orderList tbody tr>td:nth-child(3) { @apply max-w-[360px] min-w-[160px] whitespace-normal break-all; }
    .orderList tbody tr>td:nth-child(4), .orderList tbody tr>td:nth-child(5) { @apply max-w-[100px] min-w-[50px] text-center; }
    .orderList tbody tr>td:nth-child(6) { @apply max-w-[100px] min-w-[80px] text-center; }
    .orderList tbody tr>td:nth-child(7) { @apply max-w-[100px] min-w-[50px] text-center; }
    .orderList tbody tr>td:nth-child(8) { @apply min-w-[150px] text-center; }
    .orderList tbody tr>td:nth-child(9), .orderList tbody tr>td:nth-child(10) { @apply min-w-[70px] text-center; }
    .orderList tbody tr>td:nth-child(11) { @apply min-w-[150px] text-center; }
    td.wbreak { @apply max-w-[480px] whitespace-normal break-all; }
    
    /* 修复 JS 表格中的动态 Select 样式 */
    table.orderList td select.form-control {
        @apply w-full min-w-[140px] h-auto min-h-[36px] py-1.5 px-3 text-xs leading-relaxed border-slate-200 rounded-lg focus:border-brand-500 focus:ring-brand-500 block whitespace-normal shadow-sm bg-white;
    }
}
</style>

<link href="../assets/appui/css/datepicker.css" rel="stylesheet">
<script src="<?php echo $cdnpublic?>bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $cdnpublic?>bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.zh-CN.min.js"></script>

<div class="w-full max-w-[1600px] mx-auto">
    <?php adminpermission('order', 1);?>
    
    <!-- 搜索与操作面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 lg:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-5 pb-4 border-b border-slate-50 gap-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center m-0">
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mr-3">
                    <i class="fa fa-list-ol"></i>
                </div>
                订单搜索与操作
            </h3>
            
            <div class="flex items-center bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                <span class="text-xs font-medium text-slate-500 mr-2 whitespace-nowrap">每页显示</span>
                <select id="pagesize" class="bg-transparent border-none text-sm font-semibold text-brand-600 focus:ring-0 p-0 pr-6 cursor-pointer" title="每页显示">
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="80">80</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <form onsubmit="return searchOrder()" method="GET" class="space-y-3 sm:space-y-4">
            <input type="hidden" name="tid" value="<?php echo htmlspecialchars(@$_GET['tid'], ENT_QUOTES, 'UTF-8')?>">
            <input type="hidden" name="zid" value="<?php echo htmlspecialchars(@$_GET['zid'], ENT_QUOTES, 'UTF-8')?>">
            <input type="hidden" name="uid" value="<?php echo htmlspecialchars(@$_GET['uid'], ENT_QUOTES, 'UTF-8')?>">
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- 关键字搜索 (手机端缩小高度和圆角) -->
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400 text-xs sm:text-sm"></i>
                    </div>
                    <input type="text" class="w-full h-9 sm:h-10 pl-8 sm:pl-9 pr-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入下单账号或订单号" value="">
                </div>
                
                <!-- 时间区间选择 -->
                <div class="flex items-center gap-2 lg:col-span-2 w-full">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-calendar text-slate-400 text-xs sm:text-sm"></i>
                        </div>
                        <input type="text" id="starttime" name="starttime" class="w-full h-9 sm:h-10 pl-7 sm:pl-9 pr-2 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors dates" placeholder="开始日期" autocomplete="off">
                    </div>
                    
                    <button type="button" class="w-7 sm:w-8 h-9 sm:h-10 rounded-md sm:rounded-lg flex items-center justify-center text-slate-400 hover:text-brand-500 bg-slate-50 hover:bg-brand-50 transition-colors shrink-0" onclick="$('#starttime').val('');$('#endtime').val('');" title="清除时间">
                        <i class="fa fa-exchange text-xs sm:text-sm"></i>
                    </button>
                    
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-2.5 sm:pl-3 flex items-center pointer-events-none">
                            <i class="fa fa-calendar text-slate-400 text-xs sm:text-sm"></i>
                        </div>
                        <input type="text" id="endtime" name="endtime" class="w-full h-9 sm:h-10 pl-7 sm:pl-9 pr-2 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors dates" placeholder="结束日期" autocomplete="off">
                    </div>
                </div>
                
                <!-- 状态筛选 -->
                <select name="type" class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white">
                    <option value="-1">全部状态</option>
                    <option value="0">待处理</option>
                    <option value="2">正在处理</option>
                    <option value="1">已完成</option>
                    <option value="3">异常</option>
                    <option value="4">已退单</option>
                </select>
            </div>
            
            <!-- 按钮区域 (手机端等比例缩小，不再傻大黑粗) -->
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-3 pt-1 sm:pt-2">
                <!-- 搜索 -->
                <button type="submit" class="col-span-2 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-6 rounded-lg sm:rounded-xl bg-brand-600 text-white font-medium text-[13px] sm:text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                    <i class="fa fa-search mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>搜 索
                </button>
                <!-- 导出订单 -->
                <a href="./export.php" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-5 rounded-lg sm:rounded-xl bg-emerald-500 text-white font-medium text-[13px] sm:text-sm hover:bg-emerald-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-cloud-download mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>导出订单
                </a>
                <!-- 对接日志 -->
                <a href="./log.php" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-5 rounded-lg sm:rounded-xl bg-amber-500 text-white font-medium text-[13px] sm:text-sm hover:bg-amber-600 shadow-sm transition-all duration-200" target="_blank">
                    <i class="fa fa-file-text-o mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>对接日志
                </a>
                <!-- 一键补单 -->
                <a href="javascript:onekeyDj()" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-5 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-[13px] sm:text-sm hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 shadow-sm transition-all duration-200">
                    <i class="fa fa-wrench mr-1.5 sm:mr-2 text-brand-500 text-xs sm:text-sm"></i>一键补单
                </a>
                <!-- 刷新列表 -->
                <a href="javascript:clearOrder()" class="col-span-1 sm:col-span-1 w-full sm:w-10 inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-0 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-500 text-[13px] sm:text-sm hover:text-brand-600 hover:bg-brand-50 hover:border-brand-200 shadow-sm transition-all duration-200" title="刷新订单列表">
                    <i class="fa fa-refresh text-xs sm:text-sm"></i> <span class="sm:hidden ml-1">刷新列表</span>
                </a>
            </div>
        </form>


    <!-- 表格展示区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden relative">
        <div id="listTable" class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200"></div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/list.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
