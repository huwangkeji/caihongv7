<?php
include("../includes/common.php");
$title = '工单处理中心';
include("./head.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 mt-4 px-4 sm:px-0">
<?php
adminpermission('workorder', 1);

// 提取全维度的工单状态总数用于上方计数看板
$count1=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0');
$count2=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=0');
$count3=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=1');
$count4=$DB->getColumn('SELECT count(*) FROM pre_workorder WHERE ts=0 and status=2');
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题栏及全局状态筛选 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-envelope-open-o text-brand-500 mr-2 text-lg"></i>平台客服工单处理总线
            </h3>
            
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <a href="javascript:listTable('start')" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium text-sm transition-colors shadow-sm active:scale-[0.98]">
                    <i class="fa fa-list-ul mr-1.5"></i>全部列单 (<?php echo $count1?>)
                </a>
                <a href="javascript:listTable('status=0')" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-sm transition-colors shadow-sm active:scale-[0.98]">
                    <i class="fa fa-clock-o mr-1.5"></i>排队待跟进 (<?php echo $count2?>)
                </a>
                <a href="javascript:listTable('status=1')" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-sm transition-colors shadow-sm active:scale-[0.98]">
                    <i class="fa fa-circle-o-notch fa-spin mr-1.5"></i>受理中 (<?php echo $count3?>)
                </a>
                <a href="javascript:listTable('status=2')" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-sm transition-colors shadow-sm active:scale-[0.98]">
                    <i class="fa fa-check-circle mr-1.5"></i>已完结 (<?php echo $count4?>)
                </a>
            </div>
        </div>

        <!-- 异步列表渲染挂载点 -->
        <div class="p-6">
            <div id="listTable" class="w-full"></div>
        </div>
        
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/workorder.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
