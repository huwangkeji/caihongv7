<?php
/**
 * 商品管理
**/
include("../includes/common.php");
$title='商品管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

?>
<div class="w-full max-w-[1600px] mx-auto">
<?php
adminpermission('shop', 1);

$rs=$DB->query("SELECT * FROM pre_price order by id asc");
$priceselect='<option value="0">不使用加价模板</option>';
$price_class[0]='不加价';
while($res = $rs->fetch()){
	$kind = $res['kind']==1?'元':'倍';
	$priceselect.='<option value="'.$res['id'].'" kind="'.$res['kind'].'" p_2="'.$res['p_2'].'" p_1="'.$res['p_1'].'" p_0="'.$res['p_0'].'" >'.$res['name'].'('.$res['p_2'].$kind.'|'.$res['p_1'].$kind.'|'.$res['p_0'].$kind.')</option>';
	$price_class[$res['id']]=$res['name'];
}
$_SESSION['priceselect']=$priceselect;
$_SESSION['price_class']=$price_class;

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='qk2')
{
$sql="TRUNCATE TABLE `pre_tools`";
if($DB->exec($sql)!==false)
	exit("<script language='javascript'>alert('清空成功！');window.location.href='shoplist.php';</script>");
else
	exit("<script language='javascript'>alert('清空失败！".$DB->error()."');history.go(-1);</script>");
}
else
{

$cid = isset($_GET['cid'])?intval($_GET['cid']):0;
?>

    <!-- 搜索与操作面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 sm:p-5 lg:p-6 mb-6">
        
        <!-- 头部标题与分页设置 -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-5 pb-4 border-b border-slate-50 gap-3 sm:gap-4">
            <div class="flex items-center gap-3">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 flex items-center m-0" id="blocktitle">
                    <!-- JS 会自动写入此处的统计文本 -->
                </h3>
                <?php if(isset($_GET['cid']))echo '<span class="hidden sm:inline-flex px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-500 border border-rose-100">提示：如果出现排序操作移动不了，请点击"重置商品排序"</span>';?>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                <?php if(isset($_GET['cid']))echo '<span class="sm:hidden inline-flex px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-500 border border-rose-100">提示：排序失效请重置排序</span>';?>
                <div class="flex items-center bg-slate-50 px-3 py-1.5 rounded-lg sm:rounded-xl border border-slate-100 shrink-0 w-full sm:w-auto">
                    <span class="text-xs font-medium text-slate-500 mr-2 whitespace-nowrap">每页显示</span>
                    <select id="pagesize" class="bg-transparent border-none text-[13px] sm:text-sm font-semibold text-brand-600 focus:ring-0 p-0 pr-6 cursor-pointer w-full sm:w-auto">
                        <option value="30">30</option>
                        <option value="50">50</option>
                        <option value="60">60</option>
                        <option value="80">80</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>

        <form onsubmit="return searchItem()" method="GET" class="space-y-3 sm:space-y-4">
            <!-- 第一排：搜索与筛选区 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- 关键字搜索 -->
                <div class="relative w-full lg:col-span-2">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400 text-xs sm:text-sm"></i>
                    </div>
                    <input type="text" class="w-full h-9 sm:h-10 pl-8 sm:pl-9 pr-3 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入商品名称">
                </div>
                
                <!-- 商品类型筛选 -->
                <select name="type" class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white">
                    <option value="0">商品类型</option>
                    <option value="1">对接</option>
                    <option value="4">发卡</option>
                    <option value="other">自营</option>
                </select>

                <!-- 商品状态筛选 -->
                <select name="status" class="w-full h-9 sm:h-10 px-3 sm:px-4 text-[13px] sm:text-sm rounded-lg sm:rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors cursor-pointer bg-white">
                    <option value="0">商品状态</option>
                    <option value="1">显示</option>
                    <option value="0">隐藏</option>
                    <option value="up">上架</option>
                    <option value="down">下架</option>
                </select>
            </div>
            
            <!-- 第二排：按钮操作区 -->
            <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-3 pt-1 sm:pt-2">
                <!-- 添加商品 (核心突出) -->
                <a href="./shopedit.php?my=add&cid=<?php echo htmlspecialchars($_GET['cid'] ?? '')?>" class="col-span-2 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-6 rounded-lg sm:rounded-xl bg-brand-600 text-white font-medium text-[13px] sm:text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                    <i class="fa fa-plus mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>添加商品
                </a>
                
                <!-- 搜索 -->
                <button type="submit" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-5 rounded-lg sm:rounded-xl bg-emerald-500 text-white font-medium text-[13px] sm:text-sm hover:bg-emerald-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-search mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>搜 索
                </button>
                
                <!-- 商品销量排行 -->
                <a href="./shoprank.php" class="col-span-1 sm:col-span-1 w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-5 rounded-lg sm:rounded-xl bg-sky-500 text-white font-medium text-[13px] sm:text-sm hover:bg-sky-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-bar-chart mr-1.5 sm:mr-2 text-xs sm:text-sm"></i>销量排行
                </a>
                
                <!-- 更多操作 (下拉菜单，整合低频操作，保持手机端清爽) -->
                <div class="col-span-2 sm:col-span-1 w-full sm:w-auto relative dropdown">
                    <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center h-9 sm:h-10 px-4 sm:px-5 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-700 font-medium text-[13px] sm:text-sm hover:bg-slate-50 shadow-sm transition-all duration-200 custom-dropdown-toggle">
                        <i class="fa fa-cogs mr-1.5 sm:mr-2 text-brand-500 text-xs sm:text-sm"></i>更多操作 <i class="fa fa-angle-down ml-2"></i>
                    </button>
                    <ul class="dropdown-menu absolute left-0 mt-2 w-full sm:w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-1.5 z-50 hidden">
                        <li><a href="javascript:reset_sort(<?php echo $cid?>)" class="block px-4 py-2 hover:bg-slate-50 text-slate-700 text-sm"><i class="fa fa-sort w-5 text-center mr-1 text-slate-400"></i> 重置商品排序</a></li>
                        <li><a href="javascript:change_shopname()" class="block px-4 py-2 hover:bg-slate-50 text-slate-700 text-sm"><i class="fa fa-edit w-5 text-center mr-1 text-slate-400"></i> 批量替换名称</a></li>
                        <li><a href="javascript:change_inputs()" class="block px-4 py-2 hover:bg-slate-50 text-slate-700 text-sm"><i class="fa fa-keyboard-o w-5 text-center mr-1 text-slate-400"></i> 批量替换标题</a></li>
                    </ul>
                </div>
                
                <!-- 刷新列表 -->
                <a href="javascript:listTable('start')" class="col-span-2 sm:col-span-1 w-full sm:w-10 inline-flex items-center justify-center h-9 sm:h-10 px-2 sm:px-0 rounded-lg sm:rounded-xl bg-white border border-slate-200 text-slate-500 text-[13px] sm:text-sm hover:text-brand-600 hover:bg-brand-50 hover:border-brand-200 shadow-sm transition-all duration-200" title="刷新商品列表">
                    <i class="fa fa-refresh text-xs sm:text-sm"></i> <span class="sm:hidden ml-1">刷新列表</span>
                </a>
            </div>
        </form>
    </div>

    <!-- 表格展示区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden relative">
        <div id="listTable" class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200"></div>
    </div>
<?php }?>
    
    <!-- 底部友好提示 -->
    <?php if(!isset($_GET['cid']))echo '<div class="mt-4 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 text-sm flex items-center shadow-sm"><i class="fa fa-lightbulb-o text-amber-500 text-lg mr-2 shrink-0"></i> 提示：查看单个分类的商品列表可进行商品排序操作。</div>';?>
</div>

<!-- 补充下拉菜单点击关闭的原生 JS 支持 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.custom-dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;
            
            // 关闭其他所有下拉菜单
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            
            menu.classList.toggle('hidden');
        });
    });

    // 点击空白处关闭
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                m.classList.add('hidden');
            });
        }
    });
});
</script>
      
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/shoplist.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
