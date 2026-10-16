<?php
/**
 * 上架日志管理
**/
include("../includes/common.php");
$title='上架日志';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 mt-4">
<?php
adminpermission('shop', 1);
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题栏及分页控制 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-history text-brand-500 mr-2 text-lg"></i>上架记录
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页载入数量</span>
                <select id="pagesize" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" title="控制每页渲染行数">
                    <option value="30">30 行 / 页</option>
                    <option value="50">50 行 / 页</option>
                    <option value="60">60 行 / 页</option>
                    <option value="80">80 行 / 页</option>
                    <option value="100">100 行 / 页</option>
                </select>
            </div>
        </div>

        <!-- 核心工作区 -->
        <div class="p-6">
            
            <!-- 操作工作台 -->
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
                <a href="./toollogsedit.php?my=add" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0 active:scale-[0.98]">
                    <i class="fa fa-plus mr-1.5"></i>新增上架日志
                </a>

                <div class="flex gap-2 shrink-0">
                    <a href="javascript:clearItem()" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="无痕重刷日志列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>

            <!-- 异步表格加载挂载点 -->
            <div id="listTable" class="w-full"></div>
            
        </div>

        <!-- 底部系统提示信息栏 -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-start sm:items-center text-sm text-slate-500">
            <i class="fa fa-info-circle text-slate-400 mr-2 mt-0.5 sm:mt-0 text-lg shrink-0"></i>
            <div class="flex flex-wrap items-center gap-1.5 leading-relaxed">
                <span>前端自行调用上架日志页面路由：</span>
                <code class="bg-white border border-slate-200 px-2 py-0.5 rounded-md text-brand-600 font-mono text-xs font-bold shadow-sm">/toollogs.php</code> 
                <span class="mx-1 text-slate-300 hidden sm:inline-block">|</span>
                <a href="/toollogs.php" target="_blank" class="inline-flex items-center text-brand-600 hover:text-brand-700 hover:underline font-medium transition-colors">
                    前往前台查看上架日志 <i class="fa fa-external-link ml-1 text-xs opacity-70"></i>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/toollogs.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
