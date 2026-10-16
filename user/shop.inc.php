<?php
if(!defined('IN_CRONLITE'))exit();
$classhide = explode(',',$siterow['class']);

// 定义空数据提示函数
function showEmptyMessage($title, $message) {
    echo '<div class="py-16 px-4 text-center bg-slate-50 border border-slate-100 rounded-2xl my-6">
        <i class="fa fa-folder-open-o text-5xl text-slate-300 mb-4 block"></i>
        <h3 class="text-lg font-bold text-slate-700 mb-2 m-0">'.$title.'</h3>
        <p class="text-sm text-slate-500 m-0">'.$message.'</p>
    </div>';
}
?>

<!-- 强制重置外部 JS (usershop.js) 动态注入的表单，使其适配等宽风格，解决长短不一问题 -->
<style>
#inputsname .form-group { width: 100%; margin-bottom: 1rem; }
#inputsname .input-group { display: flex; width: 100%; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); border-radius: 0.75rem; overflow: hidden; border: 1px solid #e2e8f0; }
#inputsname .input-group-addon { display: flex; align-items: center; padding: 0 1rem; background-color: #f8fafc; color: #64748b; font-size: 0.875rem; font-weight: 600; border-right: 1px solid #e2e8f0; white-space: nowrap; }
#inputsname .form-control { flex: 1; height: 2.75rem; padding: 0 1rem; border: none; font-size: 0.875rem; width: 100%; outline: none; background: #ffffff; }
#inputsname .form-control:focus { box-shadow: inset 0 0 0 1px #3b82f6; }
</style>

<?php if($conf['ui_shop']>0){
// 获取分类数据逻辑
$primary_classes = array();
$secondary_classes = array();
$rs=$DB->query("select * from pre_class where active=1 order by sort asc");
while($row = $rs->fetch()){
    if($is_fenzhan && in_array($row['cid'], $classhide))continue;
    if($row['pid'] == 0){
        $primary_classes[] = $row;
    }else{
        if(!isset($secondary_classes[$row['pid']])){
            $secondary_classes[$row['pid']] = array();
        }
        $secondary_classes[$row['pid']][] = $row;
    }
}
?>

<!-- 警告信息警报栏 -->
<div id="orderWarning" class="mb-4 bg-rose-50 border border-rose-200 rounded-xl p-4 text-center">
    <div class="text-rose-700 font-bold mb-2">
        <i class="fa fa-exclamation-triangle mr-1"></i>【防呆拦截：先确认能正常使用再提交下单】违者后果自负
    </div>
    <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-bold mt-2">
        <span class="px-2 py-1 bg-white rounded-md text-rose-500 shadow-sm">① 选择分类</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-emerald-600 shadow-sm">② 锁定商品</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-purple-600 shadow-sm">③ 填入参数</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-amber-500 shadow-sm">④ 成功履约</span>
    </div>
</div>

<!-- 购前必看指引横幅 -->
<a href="/template/XHY-01/content.html" target="_blank" class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3 mb-6 hover:bg-slate-50 hover:border-brand-300 transition-colors shadow-sm group">
    <div class="flex items-center gap-3">
        <img src="/assets/img/gif_lb.jpg" class="w-8 h-8 rounded-lg object-cover">
        <div class="flex flex-col">
            <span class="font-bold text-slate-800 text-sm">购前避坑排雷必看</span>
            <span class="text-[11px] text-slate-500 font-medium mt-0.5">点击查阅通用业务执行教程与常见疑难解法</span>
        </div>
    </div>
    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-brand-100 group-hover:text-brand-600 transition-colors shrink-0">
        <i class="fa fa-angle-right"></i>
    </div>
</a>

<!-- 大类选择挂载容器 -->
<div id="goodType" <?php if(isset($_GET['cid'])){?>style="display: none"<?php }?>>
    
    <!-- 模态1：标准九宫格 -->
    <?php if($conf['ui_shop']==1){?>
	<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
    <?php foreach($primary_classes as $primary){
        $productimg = !empty($primary["shopimg"]) ? $primary["shopimg"] : '/assets/img/Product/default.png';
        if($usershop) $productimg = '../'.$productimg;
        
        $count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$primary['cid']} and active=1");
        $total_count = $count;
        if(isset($secondary_classes[$primary['cid']])){
            foreach($secondary_classes[$primary['cid']] as $secondary){
                $total_count += $DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
            }
        }
    ?>
		<div class="bg-white border border-slate-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col relative group">
			<div class="aspect-square bg-slate-50 p-4 flex items-center justify-center border-b border-slate-50">
                <img class="lazy w-full h-full object-contain group-hover:scale-105 transition-transform duration-300" data-original="<?php echo $productimg?>">
            </div>
			<div class="p-4 flex flex-col flex-1 text-center">
				<strong class="text-sm text-slate-800 truncate block mb-1"><?php echo $primary["name"]?></strong>
				<span class="text-xs text-slate-400 mb-3 block font-mono">共包裹 <?php echo $total_count?> 种业务</span>
				<button type="button" data-id="<?php echo $primary["cid"]?>" class="mt-auto w-full py-2 rounded-xl bg-sky-50 text-sky-600 font-bold text-xs hover:bg-sky-500 hover:text-white transition-colors primaryClassChange">
                    直达专区
                </button>
                
                <?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
				<div class="mt-3 pt-3 border-t border-slate-100 border-dashed space-y-1.5">
					<span class="text-[10px] font-bold text-slate-400 block mb-1">子级裂变航道：</span>
					<?php foreach($secondary_classes[$primary['cid']] as $secondary){
						$sec_count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
					?>
					<button type="button" data-id="<?php echo $secondary["cid"]?>" class="w-full py-1.5 px-2 rounded-lg bg-emerald-50 text-emerald-600 text-[11px] font-bold hover:bg-emerald-500 hover:text-white transition-colors truncate goodTypeChange"> 
                        <?php echo $secondary["name"]?> <span class="opacity-70">(<?php echo $sec_count?>)</span>
                    </button>
					<?php }?>
				</div>
				<?php }?>
			</div>
		</div>
    <?php }?>
	</div>
	<?php if(empty($primary_classes)){ showEmptyMessage('航道空置', '系统货架空空如也，请联系管理层发货补仓'); }?>

    <!-- 模态2：列表形态 -->
    <?php }elseif($conf['ui_shop']==2){?>
	<div class="space-y-3">
    <?php foreach($primary_classes as $primary){
        $productimg = !empty($primary["shopimg"]) ? $primary["shopimg"] : 'assets/img/Product/default.png';
        if($usershop) $productimg = '../'.$productimg;
        
        $count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$primary['cid']} and active=1");
        $total_count = $count;
        if(isset($secondary_classes[$primary['cid']])){
            foreach($secondary_classes[$primary['cid']] as $secondary){
                $total_count += $DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
            }
        }
    ?>
		<div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:border-brand-200 transition-all duration-300 flex flex-col sm:flex-row gap-4 items-start sm:items-center">
            
            <div class="flex items-center gap-4 flex-1 w-full">
                <div class="w-14 h-14 bg-slate-50 rounded-xl flex items-center justify-center p-2 shrink-0 border border-slate-100">
                    <img data-original="<?php echo $productimg?>" class="lazy w-full h-full object-contain">
                </div>
                <div class="flex flex-col min-w-0">
                    <h3 class="text-base font-bold text-slate-800 m-0 mb-1 truncate"><?php echo $primary["name"]?></h3>
                    <span class="text-xs text-slate-400 font-mono">该类下设 <?php echo $total_count?> 种特项商品</span>
                </div>
            </div>

            <div class="flex flex-col sm:items-end gap-3 w-full sm:w-auto mt-2 sm:mt-0">
                <button type="button" data-id="<?php echo $primary["cid"]?>" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-sky-500 text-white font-bold text-sm hover:bg-sky-600 transition-all active:scale-95 shrink-0 primaryClassChange shadow-sm">
                    开启下单
                </button>

                <?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
                <div class="flex flex-wrap gap-2 w-full sm:justify-end">
                    <?php foreach($secondary_classes[$primary['cid']] as $secondary){
                        $sec_count=$DB->getColumn("SELECT count(*) from pre_tools where cid={$secondary['cid']} and active=1");
                    ?>
                    <button type="button" data-id="<?php echo $secondary["cid"]?>" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-bold hover:bg-emerald-500 hover:text-white transition-colors goodTypeChange"> 
                        <?php echo $secondary["name"]?> <span class="opacity-70 font-mono">(<?php echo $sec_count?>)</span>
                    </button>
                    <?php }?>
                </div>
                <?php }?>
            </div>
        </div>
    <?php }?>
    </div>
	<?php if(empty($primary_classes)){ showEmptyMessage('航道空置', '系统货架空空如也，请联系管理层发货补仓'); }?>

    <!-- 模态3：极简紧凑缩略图 -->
    <?php }elseif($conf['ui_shop']==3){?>
	<div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
    <?php foreach($primary_classes as $primary){
        $productimg = !empty($primary["shopimg"]) ? $primary["shopimg"] : 'assets/img/Product/default.png';
        if($usershop) $productimg = '../'.$productimg;
    ?>
		<div class="bg-white border border-slate-100 rounded-xl p-3 shadow-sm hover:border-brand-300 hover:shadow-md transition-all flex flex-col items-center group relative">
			<div class="w-16 h-16 mb-2">
                <img class="lazy w-full h-full object-contain group-hover:scale-110 transition-transform" data-original="<?php echo $productimg?>">
            </div>
            <strong class="text-xs text-slate-700 w-full text-center truncate mb-2 block"><?php echo $primary["name"]?></strong>
            <button type="button" data-id="<?php echo $primary["cid"]?>" class="w-full py-1.5 rounded-lg bg-brand-50 text-brand-600 font-bold text-[11px] hover:bg-brand-500 hover:text-white transition-colors primaryClassChange">
                点我钻入
            </button>
            
            <?php if(isset($secondary_classes[$primary['cid']]) && count($secondary_classes[$primary['cid']]) > 0){?>
            <div class="w-full mt-2 space-y-1.5 pt-2 border-t border-slate-50">
                <?php foreach($secondary_classes[$primary['cid']] as $secondary){ ?>
                <button type="button" data-id="<?php echo $secondary["cid"]?>" class="w-full py-1 rounded bg-slate-50 text-slate-500 text-[10px] font-bold hover:bg-slate-200 transition-colors truncate goodTypeChange"> 
                    <?php echo $secondary["name"]?>
                </button>
                <?php }?>
            </div>
            <?php }?>
		</div>
    <?php }?>
	</div>
	<?php if(empty($primary_classes)){ showEmptyMessage('航道空置', '系统货架空空如也，请联系管理层发货补仓'); }?>
    <?php }?>
</div>

<script>
// 保留原生一级分类点击逻辑钩子
$(document).on('click', '.primaryClassChange', function() {
    var cid = $(this).data('id');
    $(this).addClass('goodTypeChange').removeClass('primaryClassChange');
    $(this).trigger('click');
    $(this).removeClass('goodTypeChange').addClass('primaryClassChange');
});
</script>

<!-- 下沉商品入参展开界面容器 (挂载上方好图文) -->
<div id="goodTypeContent" <?php if(!isset($_GET['cid'])){?>style="display: none"<?php }?> class="animate-[fadeIn_0.3s_ease-out]">
    
    <div class="text-center bg-slate-50 rounded-2xl p-6 border border-slate-100 mb-6 relative overflow-hidden group">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <h3 class="text-lg font-bold text-slate-800 m-0 mb-4 tracking-tight"><span id="className"></span></h3>
        <img src="" id="classImg" class="max-w-[160px] mx-auto rounded-xl shadow-md border-[3px] border-white transition-transform group-hover:scale-105" onerror="this.style.display='none'">
    </div>
    
    <input type="hidden" name="cid" id="cid" value="0"/>
    
    <?php if(isset($_GET['cid'])){
        $current_cid = intval($_GET['cid']);
        $current_class = $DB->getRow("SELECT * FROM pre_class WHERE cid='$current_cid'");
        if($current_class && $current_class['pid'] > 0){
            $parent_class = $DB->getRow("SELECT * FROM pre_class WHERE cid='{$current_class['pid']}'");
        }
    }?>
    
    <!-- 模态1/2/3通用下的 最终锁定商品框 -->
    <div class="space-y-4">
        <div class="space-y-1.5">
            <label class="text-sm font-semibold text-slate-700">锁定具体发货款</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-rose-400"><i class="fa fa-shopping-bag"></i></div>
                <select name="tid" id="tid" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-rose-200 focus:border-rose-500 shadow-sm bg-white font-bold text-rose-600" onchange="getPoint();">
                    <option value="0">载入货架数据中...</option>
                </select>
            </div>
        </div>

        <div class="bg-rose-50/50 border-l-4 border-rose-500 rounded-r-xl p-3 text-sm text-rose-700 font-bold shadow-sm flex items-start gap-2">
            <i class="fa fa-shield mt-0.5 text-rose-500"></i>
            <span>风控墙：系统已挂载安全阻断。请认真填写表单变量，切勿写入过弱密码导致卡密资产被撞库偷盗！</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5" id="display_price" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">实时计算抽水单价</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sky-500 font-bold">¥</div>
                    <input type="text" name="need" id="need" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 bg-sky-50 font-mono text-sky-600 font-bold text-lg shadow-inner cursor-not-allowed" disabled/>
                </div>
            </div>
            <div class="space-y-1.5" id="display_left" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">当前冷备库存储备</label>
                <input type="text" name="leftcount" id="leftcount" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 font-mono text-slate-500 font-bold shadow-inner cursor-not-allowed" disabled/>
            </div>
        </div>

        <div class="space-y-1.5" id="display_num" style="display:none;">
            <label class="text-sm font-semibold text-slate-700">敲定发货批次总量</label>
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 transition-all w-full">
                <input id="num_min" type="button" class="w-12 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold border-r border-slate-200 transition-colors cursor-pointer" value="一">
                <input id="num" name="num" class="flex-1 h-11 px-2 text-center text-lg font-mono font-bold border-0 focus:ring-0 text-brand-600" type="number" min="1" value="1"/>
                <input id="num_add" type="button" class="w-12 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold border-l border-slate-200 transition-colors cursor-pointer" value="十">
            </div>
        </div>

        <div id="inputsname" class="space-y-4 pt-2"></div>

        <div id="alert_frame" class="hidden p-4 rounded-xl shadow-md bg-gradient-to-r from-emerald-400 to-teal-400 text-white font-bold animate-[rubberBand_1s]"></div>

        <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <?php if($conf['shoppingcart']==1){?>
            <a id="submit_buy" class="flex-1 flex items-center justify-center h-12 rounded-xl bg-gradient-to-r from-rose-500 to-red-700 text-white font-bold text-lg hover:from-rose-600 hover:to-red-800 shadow-md shadow-rose-500/30 transition-all active:scale-[0.98] cursor-pointer">
                <i class="fa fa-flash mr-2"></i>熔断入池并买入
            </a>
            <?php }else{?>
            <input type="submit" id="submit_buy" class="flex-1 h-12 rounded-xl bg-gradient-to-r from-rose-500 to-red-700 text-white font-bold text-lg hover:from-rose-600 hover:to-red-800 shadow-md shadow-rose-500/30 transition-all active:scale-[0.98] cursor-pointer" value="熔断入池并买入">
            <?php }?>
            
            <button type="button" class="w-full sm:w-48 h-12 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 transition-colors shrink-0 backType shadow-sm">
                撤销重筛类目
            </button>
        </div>
    </div>
</div>

<?php
}else{
// ==== 模态0：经典下拉选择表单模式 ====
?>
<!-- 警告信息 -->
<div id="orderWarning" class="mb-4 bg-rose-50 border border-rose-200 rounded-xl p-4 text-center">
    <div class="text-rose-700 font-bold mb-2">
        <i class="fa fa-exclamation-triangle mr-1"></i>请确认好商品信息后再下单
    </div>
    <div class="flex flex-wrap items-center justify-center gap-2 text-xs font-bold mt-2">
        <span class="px-2 py-1 bg-white rounded-md text-rose-500 shadow-sm">① 选择分类</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-emerald-600 shadow-sm">② 锁定商品</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-purple-600 shadow-sm">③ 输入信息</span>
        <i class="fa fa-angle-right text-slate-400"></i>
        <span class="px-2 py-1 bg-white rounded-md text-amber-500 shadow-sm">④ 成功下单</span>
    </div>
</div>

<a href="/template/XHY-01/content.html" target="_blank" class="flex items-center justify-between bg-white border border-slate-200 rounded-xl p-3 mb-6 hover:bg-slate-50 hover:border-brand-300 transition-colors shadow-sm group">
    <div class="flex items-center gap-3">
        <img src="/assets/img/gif_lb.jpg" class="w-8 h-8 rounded-lg object-cover">
        <div class="flex flex-col">
            <span class="font-bold text-slate-800 text-sm">购前避坑排雷必看</span>
            <span class="text-[11px] text-slate-500 font-medium mt-0.5">点击查阅通用业务教程与常见疑难解法</span>
        </div>
    </div>
    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-brand-100 group-hover:text-brand-600 transition-colors shrink-0">
        <i class="fa fa-angle-right"></i>
    </div>
</a>

<?php
// 构建原下拉框树级结构
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 AND pid=0 order by sort asc");
$primary_select='<option value="0">请选择主分类</option>';
$primary_count=0;
while($res = $rs->fetch()){
	if($is_fenzhan && in_array($res['cid'], $classhide))continue;
	$primary_count++;
	$primary_select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}
if($primary_count==0) $hideclass = true;
?>
<div id="goodTypeContents" class="space-y-4">
    <!-- 公告占位槽 -->
    <?php echo $conf['alert']?>

    <!-- 顶层大搜索框 -->
    <?php if($conf['search_open']==1){?>
    <div class="space-y-1.5" id="display_searchBar">
        <label class="text-sm font-semibold text-slate-700">商品检索</label>
        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 transition-all">
            <div class="w-10 bg-slate-50 border-r border-slate-200 flex items-center justify-center text-slate-400"><i class="fa fa-search"></i></div>
            <input type="text" id="searchkw" class="flex-1 h-11 px-3 text-sm bg-white border-0 focus:ring-0 placeholder:text-slate-300 font-bold" placeholder="输入商品词缀快速搜索..." onkeydown="if(event.keyCode==13){$('#doSearch').click()}"/>
            <button type="button" id="doSearch" class="px-5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm transition-colors border-0">搜索</button>
        </div>
    </div>
    <?php }?>

    <!-- 级联分类容器 -->
    <?php if($hideclass){?>
        <?php showEmptyMessage('暂无分类', '系统分类空空如也，请联系管理员添加');?>
    <?php }else{?>
    
    <div class="space-y-4">
        <!-- 一级 -->
        <div class="space-y-1.5" id="display_selectclass">
            <label class="text-sm font-semibold text-slate-700">主分类</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><i class="fa fa-folder-open"></i></div>
                <select name="tid" id="cid" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 shadow-sm bg-white font-medium" onchange="changePrimaryCategory(this.value)">
                    <?php echo $primary_select?>
                </select>
            </div>
        </div>

        <!-- 二级 -->
        <div class="space-y-1.5" id="display_selectsubclass">
            <label class="text-sm font-semibold text-slate-700">二级分类</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-400"><i class="fa fa-folder"></i></div>
                <select name="subcid" id="subcid" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 shadow-sm bg-white font-medium" onchange="changeSecondaryCategory(this.value)">
                    <option value="0">等待主分类...</option>
                </select>
            </div>
        </div>
    </div>

    <!-- 终极商品锚定下拉 -->
    <div class="space-y-1.5 pt-2">
        <label class="text-sm font-semibold text-slate-700">二级分类</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-rose-400"><i class="fa fa-shopping-bag"></i></div>
            <select name="tid" id="tid" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-rose-200 focus:border-rose-500 shadow-sm bg-rose-50/30 font-bold text-rose-600" onchange="getPoint();">
                <option value="0">请先在上方选择主分类...</option>
            </select>
        </div>
    </div>
    <?php }?>

    <div class="bg-rose-50/50 border-l-4 border-rose-500 rounded-r-xl p-3 text-sm text-rose-700 font-bold shadow-sm flex items-start gap-2 my-2">
        <i class="fa fa-shield mt-0.5 text-rose-500"></i>
        <span>风控墙：系统已配置安全系统。请认真填写下单密码，切勿写入过弱密码导致订单被撞库偷盗！</span>
    </div>

    <!-- 联动加载浮出隐藏框 -->
    <div class="space-y-4">
        <div class="space-y-1.5" id="display_price" style="display:none;">
            <label class="text-sm font-semibold text-slate-700">实时单价</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-sky-500 font-bold">¥</div>
                <input type="text" name="need" id="need" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 bg-sky-50 font-mono text-sky-600 font-bold text-lg shadow-inner cursor-not-allowed" disabled/>
            </div>
        </div>

        <div class="space-y-1.5" id="display_left" style="display:none;">
            <label class="text-sm font-semibold text-slate-700">系统剩余库存</label>
            <input type="text" name="leftcount" id="leftcount" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 font-mono text-slate-500 font-bold shadow-inner cursor-not-allowed" disabled/>
        </div>
    </div>

    <!-- 加数器 -->
    <div class="space-y-1.5" id="display_num" style="display:none;">
        <label class="text-sm font-semibold text-slate-700">下单总份数</label>
        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 transition-all w-full">
            <input id="num_min" type="button" class="w-12 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold border-r border-slate-200 transition-colors cursor-pointer" value="一">
            <input id="num" name="num" class="flex-1 h-11 px-2 text-center text-lg font-mono font-bold border-0 focus:ring-0 text-brand-600" type="number" min="1" value="1"/>
            <input id="num_add" type="button" class="w-12 bg-slate-50 hover:bg-slate-100 text-slate-600 font-bold border-l border-slate-200 transition-colors cursor-pointer" value="十">
        </div>
    </div>

    <!-- 动态入参框插槽 -->
    <div id="inputsname" class="space-y-4 pt-2"></div>

    <div id="alert_frame" class="hidden p-4 rounded-xl shadow-md bg-gradient-to-r from-emerald-400 to-teal-400 text-white font-bold animate-[rubberBand_1s]"></div>

    <!-- 提交按键池 -->
    <div class="pt-4 flex flex-col sm:flex-row gap-3">
        <?php if($conf['shoppingcart']==1){?>
        <a id="submit_buy" class="flex-1 flex items-center justify-center h-12 rounded-xl bg-gradient-to-r from-rose-500 to-red-700 text-white font-bold text-lg hover:from-rose-600 hover:to-red-800 shadow-md shadow-rose-500/30 transition-all active:scale-[0.98] cursor-pointer">
            <i class="fa fa-flash mr-2"></i>立即购买
        </a>
        <?php }else{?>
        <input type="submit" id="submit_buy" class="flex-1 h-12 rounded-xl bg-gradient-to-r from-rose-500 to-red-700 text-white font-bold text-lg hover:from-rose-600 hover:to-red-800 shadow-md shadow-rose-500/30 transition-all active:scale-[0.98] cursor-pointer" value="立即购买">
        <?php }?>
    </div>

    <!-- 底部漂浮购物车引流 -->
    <div id="alert_cart" style="display:none;" class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
        <div class="text-slate-600 font-medium">
            <i class="fa fa-shopping-cart text-brand-500 mr-1.5"></i>当前购物车储存了 <b id="cart_count" class="text-rose-500 font-mono text-lg mx-1">0</b> 个商品
        </div>
        <a href="javascript:openCart()" class="px-4 py-1.5 rounded-lg bg-rose-50 text-rose-600 font-bold hover:bg-rose-500 hover:text-white transition-colors text-sm shadow-sm">清点购物车并下单</a>
    </div>

</div>
<?php } ?>

<script>
// ============ 核心数据装载器与监听系统 ============

// 监控下拉框 1 号源：顶级类目大动脉
function changePrimaryCategory(cid) {
    cid = parseInt(cid);
    if (cid == 0) {
        $('#display_selectsubclass').hide();
        $('#subcid').html('<option value="0">请先激活上层域...</option>');
        $('#tid').html('<option value="0">节点停摆，等待分类输入...</option>');
        return;
    }
    
    // AJAX 请求：装配裂变子域
    $.ajax({
        url: './ajax.php?act=getsubclass',
        type: 'GET',
        data: { cid: cid },
        dataType: 'json',
        success: function(data) {
            if (data.code == 0) {
                $('#subcid').html(data.html);
                var optionCount = $('#subcid option').length;
                if (optionCount > 1) {
                    $('#display_selectsubclass').fadeIn(200);
                } else {
                    $('#display_selectsubclass').hide();
                }
            } else {
                $('#display_selectsubclass').hide();
                $('#subcid').html('<option value="0">该域内无降级分类</option>');
            }
            // 顺带拉取顶级层面的货架数据
            loadGoodsByCategory(cid);
        },
        error: function(xhr, status, error) {
            console.error('致命错误：子域下沉失败', error);
            $('#display_selectsubclass').hide();
            $('#subcid').html('<option value="0">网络受阻：该域内无降级分类</option>');
            loadGoodsByCategory(cid);
        }
    });
    
    // 强制清洗并截断商品锁
    $('#tid').html('<option value="0">清仓完毕，等待重新锁价...</option>');
}

// 监控下拉框 2 号源：裂变次级域动作
function changeSecondaryCategory(subcid) {
    var cid = parseInt($('#cid').val());
    subcid = parseInt(subcid);
    
    // 权限判决：子层覆盖上层
    var categoryId = subcid > 0 ? subcid : cid;
    
    // 重发货架装配信号
    loadGoodsByCategory(categoryId);
}

// 货架装配器：渲染 `<option>` 链
function loadGoodsByCategory(categoryId) {
    categoryId = parseInt(categoryId);
    if (categoryId == 0) {
        $('#tid').html('<option value="0">节点异常，拒载数据...</option>');
        return;
    }
    
    $('#tid').html('<option value="0">API下发数据中...</option>');
    
    $.ajax({
        url: './ajax.php?act=gettool',
        type: 'GET',
        data: { cid: categoryId },
        dataType: 'json',
        success: function(data) {
            if (data.code == 0) {
                var html = '<option value="0">-- 点击下拉挑拣您想要的商品 --</option>';
                if (data.data.length > 0) {
                    for (var i = 0; i < data.data.length; i++) {
                        var res = data.data[i];
                        // 承载了外部 userjs.js 极其沉重的数据钩子（不可删改）
                        html += '<option value="' + res.tid + '" cid="' + res.cid + '" price="' + res.price + '" desc="' + escape(res.desc) + '" alert="' + escape(res.alert) + '" inputname="' + res.input + '" inputsname="' + res.inputs + '" multi="' + res.multi + '" isfaka="' + res.isfaka + '" count="' + res.value + '" close="' + res.close + '" prices="' + res.prices + '" max="' + res.max + '" min="' + res.min + '" stock="' + res.stock + '">' + res.name + '</option>';
                    }
                } else {
                    html = '<option value="0">当前类目仓储告罄，请催促管理员上新</option>';
                }
                $('#tid').html(html);
            } else {
                $('#tid').html('<option value="0">致命：库表连线断裂</option>');
            }
        },
        error: function(xhr, status, error) {
            console.error('致命：库表连线断裂', error);
            $('#tid').html('<option value="0">API接口超时或数据坍塌</option>');
        }
    });
}
</script>
