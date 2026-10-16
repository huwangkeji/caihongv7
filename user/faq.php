<?php
require '../includes/common.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$title = '常见问题';
include 'head.php';	
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题栏 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0 shadow-sm">
                <i class="fa fa-question-circle text-lg"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">常见问题 (FAQ)</h3>
                <p class="text-xs text-slate-500 m-0 mt-1">为您解答系统推广、提现及订单售后的常见疑惑</p>
            </div>
        </div>

        <!-- 现代化手风琴折叠列表 -->
        <div class="p-6">
            <div class="space-y-3" id="modern-accordion">
                
                <!-- Q1 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">怎么获取收益提成的？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            你只需要把你的网址发给你的用户让他下单，一旦下单付款成功，你的账户就会增加你所赚差价的金额，自己是可以设置销售价格的哦！<br>
                            <span class="inline-block mt-2 px-2 py-1 bg-white border border-slate-200 rounded text-indigo-600 font-mono font-bold text-xs">差价提成算法：销售价格 - 拿货价格 = 提成</span>
                        </div>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">怎么去推广自己的站点？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            你也可以通过（<a href="shoplist.php" class="text-indigo-600 font-bold hover:underline">商品管理</a>）把商品售价提高，那样利润就会更高，但是也要有分寸。<br><br>
                            最简单的方法就是QQ群，多加点互赞、名片赞群，发名片赞广告。空间互踩群，发空间人气、说说赞等相关广告。<br>
                            当然还有百度贴吧、网上各大论坛，你都可以去推广，付出越多，收益越大！不要局限在自己那几十个或者几百个QQ好友里面，互联网的陌生人才是你赚钱的方向！
                        </div>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">账户提现需要注意什么？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            只要你的账户达到 <b class="text-rose-500 font-mono"><?php echo $conf['tixian_min']; ?></b> 元，即可提现，提现会扣取 <b class="text-rose-500 font-mono"><?php echo (100-$conf['tixian_rate'])?>%</b> 的手续费，提现一般会在24小时内审核成功后并打款！<br><br>
                            提现方式支持：支付宝、QQ、微信，<span class="text-amber-600 font-bold">需要上传相关的收款二维码（没有上传的提现不会到账）！</span>
                        </div>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">下单前需要充值余额吗？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            完全不需要的哈，用户在你网站下单后，差价一样会到达你的账户里面，而且你赚取的钱还可以在后台以代理价格开单或者提现！<br>
                            除非你自己在后台下单，后台下单是按拿货价格下单的！
                        </div>
                    </div>
                </div>

                <!-- Q5 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">下单需要注意什么问题？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            下单前不管是你还是你的用户，必须要提醒他，看清楚商品说明，避免刷单不到账的情况！<br>
                            订单有什么问题直接发给客服QQ，让客服处理！
                        </div>
                    </div>
                </div>

                <!-- Q6 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">网址给QQ或微信爆毒拦截？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            在用户中心会看到域名 <b class="text-emerald-600">防红链接</b>。<br>
                            复制即可发给QQ或微信好友，通过短网址在QQ或微信访问是不会报毒的。
                        </div>
                    </div>
                </div>

                <!-- Q7 -->
                <div class="border border-slate-200 rounded-xl overflow-hidden transition-all duration-200 hover:border-indigo-300">
                    <button class="faq-toggle w-full px-5 py-4 text-left flex justify-between items-center bg-white hover:bg-slate-50 focus:outline-none transition-colors group">
                        <span class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors">售后和处理订单需要自己来嘛？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="faq-content bg-slate-50/80 shadow-inner overflow-hidden transition-all duration-300 max-h-0">
                        <div class="p-5 text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                            把你网站分享出去，别人下单后你就会有钱赚！或者你在外面接单，自己到后台下单！<br>
                            你只需要宣传或者接单就行了，订单处理和售后等有我们的客服来解决！
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include './foot.php';?>

<script>
// 原生 JavaScript 实现的现代化丝滑手风琴逻辑
document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.faq-toggle');
    
    toggles.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.fa-chevron-down');
            const text = this.querySelector('span');
            
            // 排他性：关闭其他所有打开的面板
            document.querySelectorAll('.faq-content').forEach(c => {
                if (c !== content && c.style.maxHeight) {
                    c.style.maxHeight = null;
                    const prevBtn = c.previousElementSibling;
                    prevBtn.querySelector('.fa-chevron-down').classList.remove('rotate-180');
                    prevBtn.querySelector('span').classList.remove('text-indigo-600');
                    prevBtn.classList.remove('bg-slate-50');
                }
            });

            // 切换当前面板状态
            if (content.style.maxHeight) {
                content.style.maxHeight = null;
                icon.classList.remove('rotate-180');
                text.classList.remove('text-indigo-600');
                this.classList.remove('bg-slate-50');
            } else {
                content.style.maxHeight = content.scrollHeight + "px";
                icon.classList.add('rotate-180');
                text.classList.add('text-indigo-600');
                this.classList.add('bg-slate-50');
            }
        });
    });

    // 初始化：默认展开第一个问题面板
    if(toggles.length > 0) {
        toggles[0].click();
    }
});
</script>

</body>
</html>
