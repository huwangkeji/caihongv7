<?php
/**
 * 标的物批量并发下单通道
**/
include("../includes/common.php");
$title='批量下单';
include './head.php';

// 登录态阻断与鉴权
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$usershop = true;

// 防跨站伪造请求 (CSRF) 与接口混淆盐签发
$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
$x = new \lib\hieroglyphy();
$addsalt_js = $x->hieroglyphyString($addsalt);

// 拉取分类阵列
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select='<option value="0">请选择资源分类大区</option>';
$select_count=0;
while($res = $rs->fetch()){
	if($is_fenzhan && in_array($res['cid'], $classhide))continue;
	$select_count++;
	$select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}
if($select_count==0)$hideclass = true;
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

    <!-- 顶部状态大盘 -->
    <div class="bg-gradient-to-r from-indigo-500 to-sky-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute left-1/2 -bottom-10 w-32 h-32 bg-sky-300/20 rounded-full blur-xl"></div>
        
        <div class="flex items-center gap-4 relative z-10 w-full sm:w-auto">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shadow-inner shrink-0 border border-white/20">
                <i class="fa fa-cubes text-3xl drop-shadow-md"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight m-0 drop-shadow">批量并发部署通道</h2>
                <p class="text-indigo-50 text-xs sm:text-sm font-medium m-0 mt-1">支持多行数据并发提交，单次执行海量任务</p>
            </div>
        </div>
        
        <div class="bg-white/10 px-5 py-3 rounded-xl backdrop-blur-sm border border-white/20 flex flex-col items-center sm:items-end w-full sm:w-auto relative z-10 shadow-sm <?php echo !$islogin2?'hidden':''?>">
            <div class="text-indigo-100 text-[11px] font-bold uppercase tracking-wider mb-0.5"><i class="fa fa-diamond mr-1.5"></i>可用金额</div>
            <div class="font-mono font-bold text-2xl leading-none tracking-tight drop-shadow">¥ <?php echo $userrow['rmb']?></div>
        </div>
    </div>

    <!-- 交互操作台主容器 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 sm:p-8" id="goodTypeContents">
            
            <!-- 系统前置通告 -->
            <?php if(!empty($conf['alert'])){ ?>
            <div class="mb-6 prose prose-sm max-w-none text-slate-600 prose-img:rounded-xl">
                <?php echo $conf['alert']?>
            </div>
            <?php } ?>

            <div class="space-y-5">
                
                <!-- 商品精准检索 -->
                <?php if($conf['search_open']==1){?>
                <div id="display_searchBar" class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">定位特征商品</label>
                    <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50">
                        <div class="pl-4 flex items-center pointer-events-none text-slate-400 bg-slate-50">
                            <i class="fa fa-search"></i>
                        </div>
                        <input type="text" id="searchkw" class="flex-1 h-12 px-3 text-sm font-bold border-0 bg-slate-50 focus:ring-0 placeholder:text-slate-400 placeholder:font-normal" placeholder="键入商品关键词组并回车检索..." onkeydown="if(event.keyCode==13){$('#doSearch').click()}">
                        <button type="button" id="doSearch" class="px-6 bg-white border-l border-slate-200 text-indigo-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-700 transition-colors cursor-pointer active:scale-95"><i class="fa fa-crosshairs mr-1.5"></i>搜索</button>
                    </div>
                </div>
                <?php }?>

                <!-- 资源分类边界 -->
                <div id="display_selectclass" class="space-y-1.5" <?php if(isset($hideclass) && $hideclass){?> style="display:none;"<?php }?>>
                    <label class="text-sm font-semibold text-slate-700 ml-1">选择分类</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa fa-folder-open-o"></i>
                        </div>
                        <select name="tid" id="cid" class="w-full h-12 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 text-slate-700 cursor-pointer transition-colors">
                            <?php echo $select?>
                        </select>
                    </div>
                </div>

                <!-- 锁定具体标的物 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">选择商品</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-indigo-500">
                            <i class="fa fa-shopping-bag"></i>
                        </div>
                        <select name="tid" id="tid" class="w-full h-12 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-indigo-50/30 text-indigo-700 cursor-pointer transition-colors" onchange="getPoint();">
                            <option value="0">等待主站调度商品列表...</option>
                        </select>
                    </div>
                </div>

                <!-- 数据回显区：价格与库存双重并列 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div id="display_price" class="space-y-1.5" style="display:none;">
                        <label class="text-sm font-semibold text-slate-700 ml-1">测算结算单价</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500 font-bold">
                                <i class="fa fa-cny"></i>
                            </div>
                            <input type="text" name="need" id="need" class="w-full h-12 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-rose-200 bg-rose-50/50 text-rose-600 shadow-inner cursor-not-allowed focus:ring-0" disabled/>
                        </div>
                    </div>

                    <div id="display_left" class="space-y-1.5" style="display:none;">
                        <label class="text-sm font-semibold text-slate-700 ml-1">当前余量</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500">
                                <i class="fa fa-database"></i>
                            </div>
                            <input type="text" name="leftcount" id="leftcount" class="w-full h-12 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-emerald-200 bg-emerald-50/50 text-emerald-600 shadow-inner cursor-not-allowed focus:ring-0" disabled/>
                        </div>
                    </div>
                </div>

                <!-- 动态商品释义弹窗 (受 JS 操控) -->
                <div id="alert_frame" style="display:none;" class="p-5 bg-sky-50 border border-sky-100 rounded-xl text-sm text-sky-800 leading-relaxed font-medium prose prose-sm max-w-none prose-img:rounded-lg shadow-inner"></div>
                
                <!-- 核心：载荷录入区 -->
                <div class="space-y-3 pt-4 border-t border-slate-100 mt-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold text-slate-800">批量任务录入区 <span class="text-rose-500">*</span></label>
                    </div>
                    
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs sm:text-sm text-amber-800 font-medium shadow-sm leading-relaxed">
                        <i class="fa fa-exclamation-triangle text-amber-500 mr-1.5 text-base"></i>当前选择商品所需入参：【<span id="inputsname" class="text-rose-600 font-bold mx-0.5 text-sm">下单账号</span>】。<br>
                        <div class="mt-2 text-amber-700/80 bg-white/60 p-2 rounded-lg border border-amber-200/50">
                            <strong>并发格式约定：</strong>每行只能存在一条独立任务，多个任务必须通过 <kbd class="bg-white px-1.5 py-0.5 rounded shadow-sm text-amber-900 border border-amber-200 mx-0.5 font-mono">回车换行</kbd> 剥离。<br>若单个任务要求输入多个关联参数，请严格使用 <kbd class="bg-white px-1.5 py-0.5 rounded shadow-sm text-amber-900 border border-amber-200 mx-0.5 font-mono">|</kbd> 符号切割。
                        </div>
                    </div>
                    
                    <textarea type="text" name="inputvalues" id="inputvalues" required placeholder="请严格遵循上方格式录入执行数据...&#10;示例：&#10;111111111|附加参数一&#10;222222222|附加参数二" rows="10" class="w-full p-5 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 transition-colors leading-relaxed placeholder:font-normal placeholder:text-slate-300"></textarea>
                </div>

                <!-- 发射按钮 -->
                <div class="pt-4">
                    <input type="submit" id="submit_buy" class="w-full h-14 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-600 text-white font-bold text-lg shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-700 active:scale-[0.98] transition-all cursor-pointer" value="执行">
                </div>

            </div>
        </div>
    </div>
</div>

<?php include './foot.php';?>

<!-- 底层组件库与通信脚本挂载 -->
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script type="text/javascript">
    // 维持底层逻辑变量稳定
    var isModal=false;
    var homepage=false;
    var islogin=<?php echo $islogin2?'1':'0'?>;
    var hashsalt=<?php echo $addsalt_js?>;
    
    // 初始化懒加载生态
    $(function() {
    	$("img.lazy").lazyload({effect: "fadeIn"});
    });
</script>

<!-- 注入负责核心请求中转的专属业务逻辑 -->
<script src="../assets/js/usershops.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
