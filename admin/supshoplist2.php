<?php
/**
 * 供货商品管理
**/
include("../includes/common.php");
$title='供货商品管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 mt-4">

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
        exit("<script language='javascript'>alert('数据表截断清空执行成功！');window.location.href='shoplist.php';</script>");
    else
        exit("<script language='javascript'>alert('高危：清空执行失败！".$DB->error()."');history.go(-1);</script>");
}
else
{

$cid = isset($_GET['cid'])?intval($_GET['cid']):0;
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题栏 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0">
                <!-- 预留由 supshoplist2.js 动态渲染的标题挂载点 -->
            </h2>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页显示条数</span>
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
            <!-- 搜索与控制工作台 -->
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col lg:flex-row flex-wrap gap-3 mb-6">
                
                <a href="./shopedit.php?my=add&cid=<?php echo htmlspecialchars($_GET['cid'] ?? 0, ENT_QUOTES, 'UTF-8'); ?>" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0 active:scale-[0.98]">
                    <i class="fa fa-plus mr-1.5"></i>新建商品
                </a>
                
                <div class="relative flex-1 min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="输入待检索的供货商品名称关键词">
                </div>
                
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors active:scale-[0.98]">
                        执行搜索
                    </button>
                    
                    <!-- Tailwind 下拉操作菜单 -->
                    <div class="relative group z-50">
                        <button type="button" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-amber-50 text-amber-600 font-medium hover:bg-amber-500 hover:text-white shadow-sm transition-colors text-sm border border-amber-100">
                            更多高阶操作 <i class="fa fa-angle-down ml-1.5 transition-transform group-hover:-rotate-180"></i>
                        </button>
                        
                        <div class="absolute right-0 top-[110%] w-48 bg-white border border-slate-100 shadow-xl rounded-xl overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right transform scale-95 group-hover:scale-100">
                            <div class="py-1">
                                <a href="javascript:reset_sort(<?php echo $cid; ?>)" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 font-medium">
                                    <i class="fa fa-sort-numeric-asc w-5 text-slate-400"></i> 重置商品排序
                                </a>
                                <a href="./shoprank.php" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 font-medium">
                                    <i class="fa fa-bar-chart w-5 text-slate-400"></i> 查阅商品销量排行
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="javascript:change_shopname()" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 font-medium">
                                    <i class="fa fa-pencil-square-o w-5 text-slate-400"></i> 批量替换商品名称
                                </a>
                                <a href="javascript:change_inputs()" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 font-medium">
                                    <i class="fa fa-i-cursor w-5 text-slate-400"></i> 批量替换输入框标题
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="无痕重载商品列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>

            <!-- 异步表格挂载点 -->
            <div id="listTable" class="w-full"></div>
            
            <?php if(!isset($_GET['cid'])) echo '<div class="mt-4 p-4 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 text-sm shadow-sm flex items-start"><i class="fa fa-info-circle mr-2 mt-0.5 text-slate-400"></i><span>温馨提示：如需进行商品排序及拖拽等高阶操作，请先通过左侧或分类选项进入具体<strong>单个分类</strong>的商品列表面板中。</span></div>'; ?>
        </div>
    </div>
<?php } ?>

</div>
      
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/supshoplist2.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
