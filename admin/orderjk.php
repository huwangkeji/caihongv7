<?php

include "../includes/common.php";
$title = "订单状态监控";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="showresult" role="dialog" aria-hidden="true">
  <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col max-h-[85vh]">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-terminal text-brand-500 mr-2"></i>手动同步订单状态</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none" onclick="closeModal()">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="flex-1 overflow-y-auto bg-slate-50 relative p-0" id="result_content">
        <!-- iframe 将由 JS 动态注入此处 -->
      </div>
      <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end shrink-0">
        <button type="button" class="px-6 py-2.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-medium hover:bg-slate-200 shadow-sm transition-colors" onclick="closeModal()">关闭窗口</button>
      </div>
  </div>
</div>

<div class="w-full max-w-[1000px] mx-auto space-y-6">
<?php 
adminpermission("shequ", 1);
if (($_POST["do"] ?? '') == "submit") {
	$updatestatus = $_POST["updatestatus"];
	$updatestatus_interval = $_POST["updatestatus_interval"];
	saveSetting("updatestatus", $updatestatus);
	saveSetting("updatestatus_interval", $updatestatus_interval);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-heartbeat text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">订单状态监控设置</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            
            <div class="space-y-4 mb-8">
                <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-info-circle mt-0.5 mr-2 text-lg shrink-0"></i>
                    <div class="leading-relaxed">
                        订单状态监控功能可以定时将“正在处理”状态的订单批量检测并更新状态，支持全部类型的对接站点，对接站点需要设置“下单成功后订单状态”是“正在处理”才能生效。<br/>
                        <span class="font-medium text-sky-800 mt-1 inline-block">注：订单状态监控不是必须的，用户前台查询订单的时候也会自动更新订单状态，无需监控。</span>
                    </div>
                </div>
                
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-shield mt-0.5 mr-2 text-lg shrink-0"></i>
                    <div class="leading-relaxed">
                        <b class="font-bold">外网 CRON 监控地址：</b>
                        <a class="text-brand-600 hover:text-brand-800 underline font-medium ml-1 bg-white px-2 py-0.5 rounded border border-emerald-200" href="./set.php?mod=cron">点此查阅提取</a><br/>
                        <div class="mt-2 text-emerald-800"><b class="font-bold">系统上次运行时间：</b><span class="font-mono ml-1"><?php echo $conf["updatestatus_lasttime"] ?? '';?></span></div>
                    </div>
                </div>
            </div>

            <form action="./orderjk.php" method="post" role="form" class="space-y-6">
                <input type="hidden" name="do" value="submit"/>
                
                <div class="space-y-1.5 max-w-md">
                    <label class="text-sm font-semibold text-slate-700">是否开启订单状态监控自动扫描</label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="updatestatus" default="<?php echo $conf["updatestatus"];?>">
                        <option value="0">暂不启用 (关闭)</option>
                        <option value="1">开启自动监控扫描</option>
                    </select>
                </div>
                
                <div class="space-y-1.5 max-w-md">
                    <label class="text-sm font-semibold text-slate-700">订单状态复测时间间隔阀值</label>
                    <div class="relative">
                        <input type="text" name="updatestatus_interval" value="<?php echo $conf["updatestatus_interval"];?>" class="w-full h-11 pl-4 pr-16 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="填入数字"/>
                        <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 font-medium text-sm pointer-events-none border-l border-slate-100 pl-3 my-2">小时</span>
                    </div>
                    <p class="text-xs text-emerald-600 mt-2 leading-relaxed"><i class="fa fa-check-circle-o mr-1"></i>设置的时间间隔是下单之后间隔 X 小时去检测，检测订单状态如果还是未完成的话，将再间隔 X 小时进行下一轮复测。</p>
                </div>
                
                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存监控设置
                    </button>
                    <button type="button" onclick="showresult()" class="inline-flex items-center justify-center w-full sm:w-auto px-8 h-11 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm shrink-0">
                        <i class="fa fa-play-circle text-lg mr-1.5"></i>手动执行一次状态同步
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<script>
// 下拉选单默认值回显
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}

// 打开弹窗并加载 Iframe
function showresult(){
	var url = '../cron.php?do=updatestatus&key=<?php echo $conf["cronkey"];?>&test=1';
	var content = '<div id="loadiframe" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10"><i class="fa fa-spinner fa-spin text-4xl mb-4 text-brand-500"></i><p class="text-sm font-medium text-slate-500">正在与远端对接站点通信，检测同步中...</p></div>' + 
                  '<iframe src="'+url+'" frameborder="0" scrolling="auto" seamless="seamless" class="w-full h-[60vh] relative z-0" onload="$(\'#loadiframe\').fadeOut(300);"></iframe>';
    
    // 注入内容
	$("#result_content").html(content);
    
    // 调出模态框并执行渐显动画
    var modal = document.getElementById('showresult');
    modal.classList.remove('hidden');
    setTimeout(function() {
        modal.classList.remove('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

// 关闭弹窗的交互逻辑
function closeModal() {
    var modal = document.getElementById('showresult');
    modal.classList.add('opacity-0');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(function() {
        modal.classList.add('hidden');
        $("#result_content").html(''); // 关闭时清空 Iframe 防止仍在后台加载
    }, 300);
}

// 绑定背景层点击关闭
document.getElementById('showresult').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
