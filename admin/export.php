<?php

include "../includes/common.php";
$title = "导出订单列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择分类</option>";
$shua_class[0] = "默认分类";
while ($res = $rs->fetch()) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$select2 = "<option value=\"0\">请选择商品</option>";
?>
<link href="../assets/appui/css/datepicker.css" rel="stylesheet">
<script src="<?php echo $cdnpublic;?>bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo $cdnpublic;?>bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.zh-CN.min.js"></script>

<div class="w-full max-w-3xl mx-auto space-y-6">
<?php 
adminpermission("order", 1);
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-download text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">导出订单列表</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="download.php" method="get" role="form" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">选择分类</label>
                        <select name="cid" id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <?php echo $select;?>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">选择商品</label>
                        <select name="tid" id="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <?php echo $select2;?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">要导出的订单状态</label>
                        <select name="status" id="status" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <option value="0">待处理</option>
                            <option value="1">已完成</option>
                            <option value="2">正在处理</option>
                            <option value="3">异常</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">导出同时改变状态</label>
                        <select name="sign" id="sign" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <option value="0">不改变状态</option>
                            <option value="1">标记为已完成</option>
                            <option value="2">标记为正在处理</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">时间段范围筛选 <span class="font-normal text-slate-400 text-xs ml-1">(留空则不限时间)</span></label>
                    <div class="input-daterange flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-100">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa fa-calendar text-slate-400"></i>
                            </div>
                            <input type="text" id="starttime" name="starttime" class="w-full h-10 pl-9 pr-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors dates" placeholder="开始日期" autocomplete="off" title="留空则不限时间范围">
                        </div>
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 hover:bg-rose-50 hover:text-rose-500 hover:border-rose-200 transition-colors cursor-pointer shrink-0 shadow-sm" onclick="$('#starttime').val('');$('#endtime').val('');" title="清除时间段"><i class="fa fa-times"></i></span>
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa fa-calendar text-slate-400"></i>
                            </div>
                            <input type="text" id="endtime" name="endtime" class="w-full h-10 pl-9 pr-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors dates" placeholder="结束日期" autocomplete="off" title="留空则不限时间范围">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50">
                    <button type="submit" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-file-text-o mr-1.5"></i>生成 TXT 并导出
                    </button>
                </div>
            </form>
        </div>
        
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 space-y-2">
            <div class="flex items-start">
                <i class="fa fa-info-circle text-brand-500 mt-0.5 mr-2 shrink-0 text-sm"></i> 
                <div class="leading-relaxed">
                    <p class="mb-1">生成 txt 文件的默认格式为：<code class="bg-white border border-slate-200 px-1.5 py-0.5 rounded text-brand-600 font-mono shadow-sm">输入内容1----输入内容2----输入内容3----输入内容4----输入内容5----下单数量</code></p>
                    <p>注：一旦在导出时勾选并成功标记为“已完成”，该订单在下次以相同条件导出时将自动被过滤排除，以防重复。</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
$("#cid").change(function () {
	var cid = $(this).val();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$("#tid").empty();
	$("#tid").append('<option value="0">请选择商品</option>');
	$.ajax({
		type : "GET",
		url : "../ajax.php?act=gettool&cid="+cid,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var num = 0;
				$.each(data.data, function (i, res) {
					$("#tid").append('<option value="'+res.tid+'">'+res.name+'</option>');
					num++;
				});
				$("#tid").val(0);
				if(num==0 && cid!=0)layer.alert('该分类下没有商品', {icon: 0});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('服务器网络错误', {icon: 2});
			return false;
		}
	});
});
$(document).ready(function(){
	$('.input-datepicker, .input-daterange').datepicker({
        format: 'yyyy-mm-dd',
		autoclose: true,
        clearBtn: true,
        language: 'zh-CN'
    });
	$("#cid").change();
})
</script>
