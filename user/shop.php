<?php
/**
 * 自助下单总线枢纽
**/
require '../includes/common.php';
$title='自助下单';

// 登录与鉴权拦截
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

include './head.php';

$usershop = true;
if(!isset($siterow)) {
    $siterow = array('class' => '');
}
if(!isset($is_fenzhan)) {
    $is_fenzhan = false;
}

$addsalt=md5(mt_rand(0,999).time());
$_SESSION['addsalt']=$addsalt;
$x = new \lib\hieroglyphy();
$addsalt_js = $x->hieroglyphyString($addsalt);
?>
<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">
    
    <!-- 主发单控制面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 面板头部看板 -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-cart-plus text-brand-500 mr-2 text-lg"></i>直营下单台
            </h3>
            <div class="flex items-center">
                <span class="text-xs text-slate-500 font-medium mr-1.5">可用余额：</span>
                <span class="font-mono text-emerald-600 font-bold text-lg">¥ <?php echo $userrow['rmb']?></span>
            </div>
        </div>
        
        <!-- 原生重构的切换选项卡 -->
        <div class="flex border-b border-slate-100 bg-slate-50/50">
            <button type="button" class="tab-btn flex-1 py-3.5 text-sm font-bold border-b-2 transition-colors border-brand-500 text-brand-600 focus:outline-none" data-target="#onlinebuy">
                在线极速下单
            </button>
            <button type="button" class="tab-btn flex-1 py-3.5 text-sm font-bold border-b-2 transition-colors border-transparent text-slate-500 hover:text-slate-800 focus:outline-none" id="tab-query" data-target="#query">
                订单记录查看
            </button>
        </div>

        <div class="p-6">
            
            <div id="onlinebuy" class="tab-pane block animate-[fadeIn_0.3s_ease-out]">
                <?php include 'shop.inc.php'; ?>
            </div>
            
            <div id="query" class="tab-pane hidden animate-[fadeIn_0.3s_ease-out] space-y-6">
                
                <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm shadow-sm leading-relaxed">
                    <i class="fa fa-info-circle mr-1"></i><?php echo $conf['gg_search']?>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">业务查询（下单信息）</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-search"></i></div>
                        <input type="text" name="qq" id="qq3" value="" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" placeholder="输入下单账号（留空则默认依循当前设备缓存查询）" required>
                    </div>
                </div>
                
                <div class="pt-2">
               <button type="button" id="submit_query" class="w-full h-11 rounded-xl bg-[#16a34a] text-white font-medium text-[15px] hover:bg-[#15803d] shadow-sm transition-all duration-200 active:scale-[0.98]">搜索</button>
               </div>
                
                <!-- 异步加载的数据呈现层 -->
                <div id="result2" style="display:none;" class="mt-6 border-t border-slate-100 pt-6">
                    <div class="text-xs text-center text-slate-400 mb-3"><i class="fa fa-arrows-h mr-1"></i>小屏设备可左右滑动查看隐藏字段</div>
                    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-200 shadow-sm bg-white">
                        <table class="w-full min-w-max text-sm text-left text-slate-600 whitespace-nowrap">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-4 py-3 font-semibold min-w-[120px]">下单账号</th>
                                    <th class="px-4 py-3 font-semibold min-w-[150px]">商品名称</th>
                                    <th class="px-4 py-3 font-semibold min-w-[60px] text-center">份数</th>
                                    <th class="px-4 py-3 font-semibold min-w-[150px] text-center hidden sm:table-cell">创建时间</th>
                                    <th class="px-4 py-3 font-semibold min-w-[100px] text-center">执行状态</th>
                                    <th class="px-4 py-3 font-semibold min-w-[100px] text-center">相关操作</th>
                                </tr>
                            </thead>
                            <tbody id="list" class="divide-y divide-slate-100">
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

<script type="text/javascript">
var isModal = false;
var homepage = false;
var hashsalt = <?php echo $addsalt_js?>;

$(function() {
	// 图片延时加载配置
	$("img.lazy").lazyload({effect: "fadeIn"});
    
    // 多维购物车轮询拉取 (按需启用)
    <?php if($conf['shoppingcart']==1){?>
    $.ajax({
        type : "GET",
        url : "../ajax.php?act=cart_info",
        dataType : 'json',
        async: true,
        success : function(data) {
            if(data.count != null && data.count>0){
                $('#cart_count').html(data.count);
                $('#alert_cart').show();
            }
        }
    });
    <?php }?>
    
    // 原生轻量化选项卡控制逻辑
    $('.tab-btn').on('click', function() {
        // 重置按钮外观
        $('.tab-btn').removeClass('border-brand-500 text-brand-600').addClass('border-transparent text-slate-500 hover:text-slate-800');
        // 点亮激活按钮
        $(this).removeClass('border-transparent text-slate-500 hover:text-slate-800').addClass('border-brand-500 text-brand-600');
        
        // 隐藏所有面板
        $('.tab-pane').removeClass('block').addClass('hidden');
        // 展示匹配面板
        const targetId = $(this).attr('data-target');
        $(targetId).removeClass('hidden').addClass('block');
    });
});
</script>

<!-- 注入核心执行脚本 -->
<script src="../assets/js/usershop.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
