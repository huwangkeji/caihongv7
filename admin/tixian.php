<?php
/**
 * 余额提现处理
**/
include("../includes/common.php");
$title='余额提现处理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1400px] mx-auto space-y-6 pb-10 mt-4">
<?php adminpermission('tixian', 1);?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题栏及全局操作按钮 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-money text-brand-500 mr-2 text-lg"></i>用户余额提现申请总览
            </h2>
            
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="javascript:config()" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-sm transition-colors shadow-sm border border-amber-100">
                    <i class="fa fa-cogs mr-1.5"></i>自动转账接口配置
                </a>
                <a href="javascript:pl_config()" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm active:scale-[0.98]">
                    <i class="fa fa-random mr-1.5"></i>执行批量转账(针对选中项)
                </a>
            </div>
        </div>

        <!-- 搜索与筛查控制台 -->
        <div class="p-6 border-b border-slate-50 bg-white">
            <form onsubmit="return searchOrder()" method="GET" class="flex flex-col lg:flex-row flex-wrap gap-3">
                <input type="hidden" name="zid" value="<?php echo htmlspecialchars(@$_GET['zid'], ENT_QUOTES, 'UTF-8')?>">
                
                <!-- 关键词搜索框 -->
                <div class="relative flex-1 min-w-[280px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" name="kw" class="w-full h-11 pl-10 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors bg-slate-50" placeholder="请输入要搜索的提现收款账号或者姓名">
                </div>
                
                <!-- 筛查条件组 -->
                <div class="flex flex-wrap gap-2 shrink-0">
                    <select name="status" class="h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors w-[130px] font-medium text-slate-600">
                        <option value="-1">全部处理状态</option>
                        <option value="0">未完成 (处理中)</option>
                        <option value="1">已完成 (已打款)</option>
                        <option value="2">处理失败 (驳回)</option>
                    </select>
                    
                    <select name="type" class="h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors w-[130px] font-medium text-slate-600">
                        <option value="-1">全部收款通道</option>
                        <option value="2">QQ钱包体系</option>
                        <option value="1">微信支付体系</option>
                        <option value="0">支付宝体系</option>
                    </select>

                    <button type="submit" id="search_submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors active:scale-[0.98]">
                        执行检索
                    </button>
                    
                    <a href="javascript:clearOrder()" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="清空条件并无痕重载列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- 异步表格加载挂载点 -->
        <div id="listTable" class="w-full"></div>
        
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/tixian.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
