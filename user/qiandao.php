<?php
/**
 * 每日签到与任务奖励系统
**/
$is_defend=true;
require '../includes/common.php';

// 登录态拦截校验
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

if(!$conf['qiandao_reward']){
	showmsg('权限受限：当前站点未开启每日签到福利功能',3);
}
$_SESSION['isqiandao']=$userrow['zid'];

$day = date("Y-m-d");
$lastday = date("Y-m-d",strtotime("-1 day"));

// 判别今日与连续签到逻辑
if ($row = $DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$day' ORDER BY id DESC LIMIT 1")) {
	$isqiandao = true;
	$continue = $row['continue'];
}else{
	if ($row = $DB->getRow("SELECT * FROM pre_qiandao WHERE zid='{$userrow['zid']}' AND date='$lastday' ORDER BY id DESC LIMIT 1")) {
		$continue = $row['continue'];
	}else{
		$continue = 0;
	}
	$isqiandao = false;
}

// 抽取全域历史签到前 10 条流水
$rs=$DB->query("SELECT * FROM pre_qiandao ORDER BY id DESC LIMIT 10");
$qqrow=array();
$qdrow=array();
while($res = $rs->fetch()){
	if(count($qqrow)<5){
		$qqrow[]=$res['qq'];
	}
	$qdrow[]=$res;
}

// 虚拟签到引流数据算法
if($conf['qiandao_virtual']==1){
	$virtual_data = array();
	$now = time();
	
	// 生成5条虚拟签到流水
	for($i=0; $i<5; $i++){
		$virtual_zid = rand(1000, 9999);
		$virtual_time = date("Y-m-d H:i:s", $now - rand(10, 3600)); 
		$virtual_reward = number_format(rand(1, 50) / 100, 2); 
		$virtual_continue = rand(1, 30); 
		
		$virtual_data[] = array(
			'zid' => $virtual_zid,
			'time' => $virtual_time,
			'reward' => $virtual_reward,
			'continue' => $virtual_continue
		);
	}
	
	// 并入真实流并按时间重排
	$all_data = array_merge($qdrow, $virtual_data);
	usort($all_data, function($a, $b) {
		return strtotime($b['time']) - strtotime($a['time']);
	});
	
	$qdrow = array_slice($all_data, 0, 10);
	
	// 更新头部缩略图
	$qqrow = array();
	foreach($qdrow as $row){
		if(count($qqrow)<5){
			$qqrow[] = rand(10000, 999999999); 
		}
	}
}

$title = '每日签到打卡';
include 'head.php';

// 构建防红短链接推广
$url = 'http://'.$userrow['domain'].'/';
if($conf['fanghong_api']>0){
	$turl = fanghongdwz($url);
	if(strpos($turl,'/')===false){
		$turl = $url;
	}
}else{
	$turl = $url;
}
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

    <!-- 签到主横幅面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="px-6 py-4 bg-gradient-to-r from-sky-500 to-indigo-600 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg m-0 tracking-tight flex items-center">
                <i class="fa fa-calendar-check-o text-white/80 mr-2"></i>福利大厅
            </h3>
            <span class="text-white/80 text-xs font-medium">每日打卡赢取系统补贴</span>
        </div>

        <div class="relative overflow-hidden group h-48 sm:h-56 bg-slate-900">
            <!-- 占位插图，您若更换此图可直接替换路径 -->
            <img src="../assets/img/qiandao.jpg" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity duration-500 transform group-hover:scale-105">
            
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-transparent"></div>
            
            <!-- 天气微件插槽 -->
            <div class="absolute top-4 left-4 rounded-xl overflow-hidden backdrop-blur-sm bg-black/20 border border-white/10 shadow-sm">
                <iframe width="300" scrolling="no" height="60" frameborder="0" allowtransparency="true" src="//i.tianqi.com/index.php?c=code&id=12&icon=1&num=2&site=12"></iframe>
            </div>

            <!-- 数据概览 -->
            <div class="absolute bottom-4 left-4 right-4 flex items-end justify-between">
                <div>
                    <span class="text-white/70 text-xs font-bold block mb-1">历史累计补贴总额</span>
                    <div class="text-white font-mono text-2xl sm:text-3xl font-bold tracking-tight drop-shadow-md">
                        ¥ <span id="rewardcount">0.00</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-emerald-400 text-xs font-bold block mb-1">坚持就是胜利</span>
                    <div class="text-white font-mono text-xl sm:text-2xl font-bold tracking-tight drop-shadow-md flex items-center">
                        <i class="fa fa-fire text-amber-500 mr-1.5 animate-pulse"></i>已连签 <?php echo $continue?> 天
                    </div>
                </div>
            </div>
        </div>

        <!-- 交互按键栏 -->
        <div class="p-5 bg-white flex gap-3">
            <button type="button" id="qiandao" class="flex-1 h-12 rounded-xl text-white font-bold text-[15px] shadow-sm transition-all duration-200 active:scale-[0.98] <?php echo $isqiandao ? 'bg-slate-300 cursor-not-allowed shadow-none' : 'bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 shadow-emerald-500/30' ?>" <?php echo $isqiandao ? 'disabled' : '' ?>>
                <i class="fa <?php echo $isqiandao ? 'fa-check' : 'fa-hand-pointer-o' ?> mr-2"></i><?php echo $isqiandao ? '今天您已拔得头筹' : '点击激活今日打卡' ?>
            </button>
            <button type="button" onclick="showShareModal()" class="w-1/3 sm:w-1/4 h-12 rounded-xl bg-rose-50 text-rose-600 font-bold hover:bg-rose-500 hover:text-white transition-colors shadow-sm text-[15px]">
                <i class="fa fa-share-alt mr-1"></i>推链
            </button>
        </div>
    </div>

    <!-- 动态排行榜大盘 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-trophy text-amber-500 mr-2 text-lg"></i>全域福报动态公示板
            </h3>
            
            <!-- 头像墙 -->
            <div class="flex -space-x-3">
                <?php foreach($qqrow as $row){ ?>
                    <img class="w-8 h-8 rounded-full border-2 border-white shadow-sm object-cover bg-slate-100" src="/assets/img/user.png">
                <?php } ?>
            </div>
        </div>

        <!-- 聚合数据表盘 -->
        <div class="grid grid-cols-3 divide-x divide-slate-100 border-b border-slate-100">
            <div class="py-4 text-center">
                <span class="text-slate-400 text-[11px] font-bold block mb-1 uppercase tracking-wider"><i class="fa fa-line-chart mr-1"></i>今日进账</span>
                <span class="text-slate-700 font-mono font-bold">999+ <small class="text-slate-400">人</small></span>
            </div>
            <div class="py-4 text-center">
                <span class="text-slate-400 text-[11px] font-bold block mb-1 uppercase tracking-wider"><i class="fa fa-history mr-1"></i>昨日封档</span>
                <span class="text-slate-700 font-mono font-bold">999+ <small class="text-slate-400">人</small></span>
            </div>
            <div class="py-4 text-center">
                <span class="text-slate-400 text-[11px] font-bold block mb-1 uppercase tracking-wider"><i class="fa fa-database mr-1"></i>历史累计</span>
                <span class="text-slate-700 font-mono font-bold">999+ <small class="text-slate-400">次</small></span>
            </div>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($qdrow as $row){ ?>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div>
                                        <span class="font-mono text-slate-700 font-bold">UID: <?php echo $row['zid'] ?></span>
                                        <span class="text-xs text-slate-400 ml-2">于 <b class="text-slate-500"><?php echo date("H:i",strtotime($row['time'])) ?></b> 完成打卡</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="font-mono font-bold text-rose-500">
                                        +¥ <?php echo $row['reward'] ?>
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm">
                                        坚持 <?php echo $row['continue'] ?> 天
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 分享内容隐藏容器 (供 Layer 调用) -->
<div id="shareContent" class="hidden">
    <div class="p-5 space-y-4">
        <div class="bg-amber-50 text-amber-700 p-3 rounded-xl border border-amber-200 text-xs font-bold shadow-sm">
            <i class="fa fa-gift mr-1"></i>将站点分享至群聊，有机会引流获取平台赞助及下级消费分润！
        </div>
        <textarea id="fxggc" class="w-full h-40 p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono text-slate-600 focus:outline-none focus:border-brand-500 transition-colors resize-none shadow-inner" readonly unselectable="on">
网站：<?php echo $conf['sitename'] ?>

👉 泛娱乐/应用周边全覆盖
👉 提供全品类代练与业务挂载
👉 开放站长加盟——低槛建站裂变
👉 源头接口——底层成本批发货架
🔥 点此链接极速开通：
【 <?php echo $turl?> 】

✅ 多样化提现结算 —— 极速响应
✅ 问题卡密包退换 —— 全职客服驻守
</textarea>
        <button data-clipboard-target="#fxggc" class="fenx w-full h-11 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold hover:from-brand-700 hover:to-indigo-700 shadow-md shadow-brand-500/20 transition-all duration-200 active:scale-95">
            <i class="fa fa-copy mr-1.5"></i>一键复制上述广告词
        </button>
    </div>
</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>clipboard.js/1.7.1/clipboard.min.js"></script>
<script>
// 剪贴板强化
var clipboard = new Clipboard('.fenx');
clipboard.on('success', function(e) {
  	layer.msg("广告文案复制成功！快去群内散播吧", {icon: 1, time: 2000});
});
clipboard.on('error', function(e) {
     layer.msg("复制失败，请点击文本框长按手动全选复制", {icon: 2});
});	

// 呼出分享弹窗 (抛弃老旧 Bootstrap Modal)
function showShareModal() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-share-alt text-brand-500 mr-2"></i>裂变拉新推广面板</div>',
        area: [$(window).width() > 480 ? '400px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#shareContent').html()
    });
}

// 签到动作与数据轮询
$(document).ready(function(){
	$("#qiandao").click(function(){
        var ii = layer.load(2, {shade: [0.1, '#fff']});
		$.ajax({
            type: "get",
            url: "ajax_user.php?act=qiandao",
            dataType: "json",
            success: function(data){
                layer.close(ii);
                if(data.code == 0){
                    layer.alert(data.msg,{icon:1, title:'系统下发通知'},function(){
                        window.location.reload();
                    });
                }else{
                    layer.alert(data.msg,{icon:2, title:'签到受阻'});
                }
            },
            error: function(){
                layer.close(ii);
                layer.alert('请求链路超时，请检查网络后重试！',{icon:2}); 
            }
	   });
	});
    
    // 异步加载签到数据总量
	$.ajax({
		type : "GET",
		url : "ajax_user.php?act=qdcount",
		dataType : 'json',
		async: true,
		success : function(data) {
			$('#count1').html(data.count1);
			$('#count2').html(data.count2);
			$('#count3').html(data.count3);
			$('#rewardcount').html(data.rewardcount);
		}
	});
});
</script>
</body>
</html>
