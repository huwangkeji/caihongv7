<?php
/**
 * 分销推广与引流裂变素材库
**/
$is_defend=true;
require '../includes/common.php';

// 鉴权：防丢包跳转
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$title = '推广赚钱';
include 'head.php';
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
// 权限阻断逻辑
if($userrow['power']==0){
	showmsg('权限阻断：您当前为基础用户，暂无权限使用分销裂变功能！',3);
}
if(!$userrow['domain']) showmsg('阻断：您的专属分站尚未完成独立域名的挂载解析！',3);

// 提取当前引擎路径并组装专属 URL
$scriptpath = str_replace('\\','/',$_SERVER['SCRIPT_NAME']);
$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$scriptpath = substr($scriptpath, 0, strrpos($scriptpath, '/'));
$url = 'http://'.$userrow['domain'].$scriptpath.'/';

// 防红短链转换
if($conf['fanghong_api']>0){
	$turl = fanghongdwz($url);
	if($turl == $url){
		showmsg('防红引擎离线：短链接口生成失败，请呼叫总台站长修复路由！',3);
	}elseif(strpos($turl,'/')===false){
		showmsg('防红解析崩溃:'.$turl,3);
	}
}else{
	$turl = $url;
}
?>

    <!-- 顶部引流指南看板 -->
    <div class="bg-gradient-to-r from-indigo-500 to-sky-500 rounded-2xl shadow-lg p-6 sm:p-8 text-white relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute left-1/2 -bottom-10 w-32 h-32 bg-sky-300/20 rounded-full blur-xl"></div>
        
        <div class="relative z-10 w-full md:w-2/3">
            <h2 class="text-2xl font-bold tracking-tight m-0 drop-shadow flex items-center mb-3">
                <i class="fa fa-share-alt-square mr-3 text-3xl opacity-90"></i>裂变引流素材图谱
            </h2>
            <div class="space-y-2 text-indigo-50 text-sm font-medium">
                <p class="m-0 flex items-start"><i class="fa fa-check-circle text-emerald-400 mt-1 mr-2 shrink-0"></i>将下方素材分发至微信、QQ群、空间、贴吧或论坛等流量池。</p>
                <p class="m-0 flex items-start"><i class="fa fa-check-circle text-emerald-400 mt-1 mr-2 shrink-0"></i>当终端客源通过扫码或点击您的专属链接入场下单，系统将自动清算并为您派发流转差价提成！</p>
            </div>
        </div>
        

    </div>

    <!-- 素材操作核心台 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden min-h-[500px]">
        
        <!-- 自定义胶囊导航栏 (摒弃 Bootstrap nav-tabs) -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
            <div class="flex p-1 bg-slate-200/60 rounded-xl w-fit">
                <button id="tab-pic-btn" onclick="switchTab('pic')" class="px-6 py-2 rounded-lg text-sm font-bold transition-all shadow-sm bg-white text-indigo-600">
                    <i class="fa fa-picture-o mr-1.5"></i>海报视觉挂图
                </button>
                <button id="tab-text-btn" onclick="switchTab('text')" class="px-6 py-2 rounded-lg text-sm font-medium transition-all text-slate-500 hover:text-slate-700">
                    <i class="fa fa-file-text-o mr-1.5"></i>直客文字话术
                </button>
            </div>
        </div>

        <div class="p-6">
            
            <!-- ===================== 图片海报区 ===================== -->
            <div id="content-pic" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeIn">
                <?php for($i=1; $i<=6; $i++){ ?>
                <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-200 bg-white flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-slate-700"><i class="fa fa-image text-sky-500 mr-1.5"></i>高定宣发海报 <?php echo $i; ?></span>
                        <button onclick="CunTips()" class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-download mr-1"></i>提取</button>
                    </div>
                    <div class="p-4 flex-1 flex items-center justify-center bg-slate-100/50">
                        <img src="./timg/timg.php?id=<?php echo $i; ?>&url=<?php echo $turl?>" class="w-full h-auto max-h-[400px] object-contain rounded-lg shadow-sm border border-slate-200 group-hover:scale-[1.02] transition-transform duration-300" alt="专属宣发图档">
                    </div>
                </div>
                <?php } ?>
            </div>

            <!-- ===================== 文字话术区 ===================== -->
            <div id="content-text" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fadeIn">
                
                <!-- 话术模板 1 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>精简引流版 ①</span>
                        <button data-clipboard-target="#wen-a" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-sm text-slate-600 leading-relaxed font-medium">
                        <div id="wen-a">刷名片赞、空间人气、说说赞、QQ大会员、超级会员、全民K歌鲜花、QQ钻等。每天还可免费抽奖，欢迎收藏！<br><br>自助下单防迷路：<?php echo $turl?></div>
                    </div>
                </div>

                <!-- 话术模板 2 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>情怀洗脑版 ②</span>
                        <button data-clipboard-target="#wen-b" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-xs text-slate-600 leading-relaxed font-medium">
                        <div id="wen-b">1998年马化腾创立腾讯QQ，让你注册，你不注册。 现在一个5位数的QQ几万。2003年马云说开 淘宝店不要钱，让你开店，你不开。10年后淘宝造就了无数个亿万富翁。2009年曹国 伟创立微博，让你开通，你不开。如今一个微博搞笑排行榜年净赚1500万，2018年我 给你一个刷业务网站，再不好好珍惜的话，好好想想你还会错过什么？<br><br>自助下单防迷路：<?php echo $turl?></div>
                    </div>
                </div>

                <!-- 话术模板 3 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>情感暴击版 ③</span>
                        <button data-clipboard-target="#wen-c" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-xs text-slate-600 leading-relaxed font-medium h-48 overflow-y-auto scrollbar-thin">
                        <div id="wen-c">有时候你想约个炮，却不小心谈了场恋爱。有时候你想好好谈个恋爱，却发现只是约了个炮，世界那么大，床却那么小， 床上的两个人曾经那么好，却不能到老。我喜欢牵了手就能成婚的故事，却活在了一个上了床也没有结果的时代！ 有些人总埋怨泡不上妞、回头看看你的QQ，不是QQ会员，也不是QQ黄钻！谁会跟你走？在这个扣扣上遍地是会员， 空间满屏是黄钻的年代，不如加入我们吧！代唰欢迎您的加盟，会员钻全网最低价，还能免费抽年费，运气好还能成永久成绝版，价格就像我们宣传语那么直白！<br><br>自助入口：<?php echo $turl?></div>
                    </div>
                </div>

                <!-- 话术模板 4 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>直接粗暴版 ④</span>
                        <button data-clipboard-target="#wen-d" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-sm text-slate-600 leading-relaxed font-medium">
                        <div id="wen-d">还在互赞QQ名片？空间说说？空间留言？多费时间呀？给你们个网站无需注册，直接下单！<br><br>自助直达电梯：<?php echo $turl?></div>
                    </div>
                </div>

                <!-- 话术模板 5 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>诱导福利版 ⑤</span>
                        <button data-clipboard-target="#wen-e" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-sm text-slate-600 leading-relaxed font-medium">
                        <div id="wen-e">[QQ红包]恭喜发财<br>一个能刷QQ名片赞的网站价格低到冰点！！！现在老板疯了，疯狂送福利中，你还不赶快来下一单？还能搭建分站赚零花钱！这么便宜实惠还有优质售后的平台确定不来？<br><br>入口地址：<?php echo $turl?></div>
                    </div>
                </div>

                <!-- 话术模板 6 -->
                <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm hover:border-indigo-300 transition-colors flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 bg-indigo-50/30 flex justify-between items-center shrink-0">
                        <span class="text-sm font-bold text-indigo-700"><i class="fa fa-bullhorn mr-1.5"></i>阵列排版版 ⑥</span>
                        <button data-clipboard-target="#wen-f" class="copy-btn px-3 py-1 bg-indigo-50 text-indigo-600 border border-indigo-200 rounded-lg text-xs font-bold hover:bg-indigo-500 hover:text-white transition-colors active:scale-95"><i class="fa fa-copy mr-1"></i>拷贝</button>
                    </div>
                    <div class="p-4 flex-1 text-sm text-slate-600 leading-relaxed font-medium text-center bg-slate-50/50">
                        <div id="wen-f">
                            QQ代唰商城<br><?php echo $turl?><br>【自动处理】【下单秒唰】<br>【钻类业务】【搭建分站】<br>【唰名片赞】【空间业务】<br>【会员业务】【王者代练】<br>每天免费抽奖，赢好礼<br>开通分站每天签到送现金！<br>建议收藏网站方便下次访问
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script>
// 极简选项卡切换路由
function switchTab(tabId) {
    if (tabId === 'pic') {
        $('#content-text').addClass('hidden');
        $('#content-pic').removeClass('hidden');
        $('#tab-pic-btn').removeClass('text-slate-500 font-medium bg-transparent hover:text-slate-700').addClass('bg-white text-indigo-600 font-bold shadow-sm');
        $('#tab-text-btn').removeClass('bg-white text-indigo-600 font-bold shadow-sm').addClass('text-slate-500 font-medium bg-transparent hover:text-slate-700');
    } else {
        $('#content-pic').addClass('hidden');
        $('#content-text').removeClass('hidden');
        $('#tab-text-btn').removeClass('text-slate-500 font-medium bg-transparent hover:text-slate-700').addClass('bg-white text-indigo-600 font-bold shadow-sm');
        $('#tab-pic-btn').removeClass('bg-white text-indigo-600 font-bold shadow-sm').addClass('text-slate-500 font-medium bg-transparent hover:text-slate-700');
    }
}

// 图片提取策略弹窗 (改写老旧外观)
function CunTips() {
    var content = '<div class="p-5 text-sm text-slate-700 leading-relaxed font-medium">' +
                  '<div class="mb-3 flex items-start"><i class="fa fa-mobile text-2xl text-indigo-500 w-6 shrink-0 mt-0.5"></i><div><b class="text-slate-800">移动端：</b>长按目标素材呼出菜单，选择「保存图像」即可落盘（请使用原生浏览器以防拦截）。</div></div>' +
                  '<div class="flex items-start"><i class="fa fa-desktop text-xl text-sky-500 w-6 shrink-0 mt-0.5"></i><div><b class="text-slate-800">桌面端：</b>将光标悬停于海报之上，右击唤出选项，选择「图片另存为」。</div></div>' +
                  '</div>';
	layer.open({
	    type: 1,
		title: '<div class="font-bold text-slate-800"><i class="fa fa-download text-emerald-500 mr-2"></i>终端素材保存指引</div>',
		area: [$(window).width() > 400 ? '400px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        btn: ['我已悉知'],
        btnAlign: 'c',
        content: content
	});
}

// 投稿弹窗 (改写老旧外观)
function TgTips() {
    var content = '<div class="p-5 text-sm text-slate-700 leading-relaxed font-medium text-center">' +
                  '<i class="fa fa-envelope-open-o text-4xl text-amber-400 mb-3 block"></i>' +
                  '若您有更具杀伤力的引流图文或破防话术，请随时呼叫总台客服进行提交归档。<br>期待您的战术方案！' +
                  '</div>';
	layer.open({
	    type: 1,
		title: '<div class="font-bold text-slate-800"><i class="fa fa-lightbulb-o text-amber-500 mr-2"></i>发散维度的投稿通道</div>',
		area: [$(window).width() > 350 ? '350px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: content
	});
}

// 剪贴板监听
$(document).ready(function(){
	var clipboard = new Clipboard('.copy-btn');
    clipboard.on('success', function(e) {
        layer.msg('话术切片已拷贝至剪贴板，去发射吧！',{time: 1500, icon: 1});
    });
    clipboard.on('error', function(e) {
        layer.msg('浏览器拦截了复制指令，请长按手动圈选拷贝',{time: 2500, icon: 2});
    });
});
</script>
</body>
</html>
