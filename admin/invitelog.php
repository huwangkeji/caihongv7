<?php

include "../includes/common.php";
$title = "推广记录";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6">
<?php 
adminpermission("shop", 1);
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- 头部动态标题与分页条数控制 -->
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <!-- 这里的文字将由 JS 的 searchItem 回调动态填充 (对应 invitelog-table.php 里的 $con) -->
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">显示条数</span>
                <select id="pagesize" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="80">80</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        
        <div class="p-6">
            <!-- 检索表单 -->
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
                
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入你要检索的查单 QQ 号">
                </div>
                
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        检索记录
                    </button>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="重置并刷新推广记录列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>

            <!-- AJAX 异步列表挂载点 -->
            <div id="listTable" class="w-full"></div>
        </div>
    </div>
</div>
      
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/invitelog.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>
