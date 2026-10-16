<?php

include "../includes/common.php";
$title = "对接价格监控";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="showresult" role="dialog" aria-hidden="true">
  <div class="w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col max-h-[85vh]">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-terminal text-brand-500 mr-2"></i>手动同步价格执行结果</h4>
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
	$pricejk_cid = implode(",", $_POST["pricejk_cid"]);
	$pricejk_edit = $_POST["pricejk_edit"];
	$pricejk_time = $_POST["pricejk_time"];
	$pricejk_yile = $_POST["pricejk_yile"];
	saveSetting("pricejk_cid", $pricejk_cid);
	saveSetting("pricejk_edit", $pricejk_edit);
	saveSetting("pricejk_time", $pricejk_time);
	saveSetting("pricejk_yile", $pricejk_yile);
	$ad = $CACHE->clear();
	if ($ad) {
		showmsg("修改成功！", 1);
	} else {
		showmsg("修改失败！<br/>" . $DB->error(), 4);
	}
} else {
	$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
	$pricejk_cid = explode(",", $conf["pricejk_cid"] ?? '');
	$select = "<option value=\"0\" " . (in_array("0", $pricejk_cid) ? "selected" : "NULL") . ">未分类商品</option>";
	while ($res = $rs->fetch()) {
		$select .= "<option value=\"" . $res["cid"] . "\" " . (in_array($res["cid"], $pricejk_cid) ? "selected" : "NULL") . ">" . $res["name"] . "</option>";
	}
}
$pricejk_lasttime = $DB->getColumn("SELECT v FROM pre_config WHERE k='pricejk_lasttime' limit 1");
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-line-chart text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">对接价格监控设置</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            
            <div class="space-y-4 mb-8">
                <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-info-circle mt-0.5 mr-2 text-lg shrink-0"></i>
                    <div class="leading-relaxed">
                        价格监控可以实现自动修改商品成本价格和商品上下架，部分对接类型还支持库存同步。<br/>
                        <span class="font-bold text-rose-600">仅支持使用加价模板的商品！成本价是 0 的商品不会同步！</span>
                    </div>
                </div>
                
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-shield mt-0.5 mr-2 text-lg shrink-0"></i>
                    <div class="leading-relaxed">
                        <b class="font-bold">外网价格监控 CRON 地址：</b>
                        <a class="text-brand-600 hover:text-brand-800 underline font-medium ml-1 bg-white px-2 py-0.5 rounded border border-emerald-200" href="./set.php?mod=cron">点此查看提取</a>
                    </div>
                </div>
            </div>

            <form action="./pricejk.php" method="post" role="form" class="space-y-6">
                <input type="hidden" name="do" value="submit"/>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">选择要监控的商品类别</label>
                    <select name="pricejk_cid[]" multiple="multiple" class="w-full h-48 px-4 py-2 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors outline-none">
                        <?php echo $select;?>
                    </select>
                    <p class="text-xs text-emerald-600 mt-1.5"><i class="fa fa-info-circle mr-1"></i>按住 Ctrl 键（Mac上按 Cmd 键）可实现多选操作。</p>
                </div>
                
                <div class="space-y-1.5 max-w-xl">
                    <label class="text-sm font-semibold text-slate-700">在以下情况修改商品价格</label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="pricejk_edit" default="<?php echo $conf["pricejk_edit"] ?? '';?>">
                        <option value="0">成本价格与社区价格不符时</option>
                        <option value="1">成本价格低于社区价格时</option>
                    </select>
                </div>
                
                <div class="space-y-1.5 max-w-xl">
                    <label class="text-sm font-semibold text-slate-700">价格自动同步频率</label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="pricejk_time" default="<?php echo $conf["pricejk_time"] ?? '';?>">
                        <option value="10">10秒 (极速)</option>
                        <option value="600">10分钟</option>
                        <option value="1200">20分钟</option>
                        <option value="1800">30分钟</option>
                        <option value="3600">60分钟</option>
                        <option value="7200">120分钟</option>
                    </select>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">亿乐 / 卡商网 / 卡易信 / 直客等同步价格方式 <span class="text-xs text-slate-400 font-normal">(未对接此类平台的可以无视)</span></label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="pricejk_yile" default="<?php echo $conf["pricejk_yile"] ?? '';?>">
                        <option value="0">下单同时同步价格（无需监控）</option>
                        <option value="1">监控批量同步价格（可能会超时）</option>
                    </select>
                    <p class="text-xs text-emerald-600 mt-2 leading-relaxed"><i class="fa fa-lightbulb-o mr-1"></i>此类对接网站无法直接批量获取商品价格，只能一个一个商品循环获取。因此改用“下单同时同步价格”，可以有效避免监控批量同步导致 502 超时，但用户提交订单的时候如果遇到上游价格波动会出现实时提示框。</p>
                </div>
                
                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存价格监控设置
                    </button>
                    <button type="button" onclick="showresult()" class="inline-flex items-center justify-center w-full sm:w-auto px-8 h-11 rounded-xl bg-white border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm shrink-0">
                        <i class="fa fa-play-circle text-lg mr-1.5"></i>立即同步一次价格
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

// 打开弹窗并加载 Iframe 异步任务
function showresult(){
	var url = '../cron.php?do=pricejk&key=<?php echo $conf["cronkey"];?>&test=1';
	var content = '<div id="loadiframe" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 z-10"><i class="fa fa-spinner fa-spin text-4xl mb-4 text-brand-500"></i><p class="text-sm font-medium text-slate-500">正在与各个对接上游通信，比对并更新商品价格...</p></div>' + 
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
