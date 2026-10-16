<?php
/**
 * APP在线生成
**/
include("../includes/common.php");
$title='APP在线生成';
include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 1);
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1000px] mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col min-h-[500px]">
        <!-- 现代化选项卡头部 -->
        <div class="px-2 pt-2 border-b border-slate-100 bg-slate-50/50 flex flex-nowrap overflow-x-auto scrollbar-none custom-tabs">
            <button type="button" class="tab-btn active px-5 py-3 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors relative whitespace-nowrap outline-none" data-target="set">
                <i class="fa fa-cogs mr-1.5 opacity-70"></i>APP生成配置
                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-brand-600 rounded-t-full transform scale-x-0 transition-transform origin-center tab-indicator"></span>
            </button>
            <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors relative whitespace-nowrap outline-none" data-target="create">
                <i class="fa fa-magic mr-1.5 opacity-70"></i>生成APP
                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-brand-600 rounded-t-full transform scale-x-0 transition-transform origin-center tab-indicator"></span>
            </button>
            <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors relative whitespace-nowrap outline-none" data-target="query" onclick="querytask()">
                <i class="fa fa-search mr-1.5 opacity-70"></i>我的生成
                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-brand-600 rounded-t-full transform scale-x-0 transition-transform origin-center tab-indicator"></span>
            </button>
            <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors relative whitespace-nowrap outline-none" data-target="other">
                <i class="fa fa-wrench mr-1.5 opacity-70"></i>其他设置
                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-brand-600 rounded-t-full transform scale-x-0 transition-transform origin-center tab-indicator"></span>
            </button>
        </div>

        <div class="p-6 sm:p-8 flex-1">
            <!-- APP生成配置 -->
            <div id="set" class="tab-content-pane block animate-[fadeIn_0.3s_ease-out]">
                <form onsubmit="return saveSetting(this)" method="post" role="form" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">分站自助生成APP功能</label>
                        <div class="md:col-span-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="appcreate_open" default="<?php echo $conf['appcreate_open']?>">
                                <option value="0">关闭</option>
                                <option value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">APP生成平台密钥</label>
                        <div class="md:col-span-2 flex gap-2">
                            <input type="text" name="appcreate_key" value="<?php echo $conf['appcreate_key']; ?>" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1"/>
                            <a href="https://app.51zhihuigu.com/" class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 shadow-sm transition-colors shrink-0 text-sm" target="_blank" rel="noreferrer">获取密钥</a>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">允许自定义图标和启动图</label>
                        <div class="md:col-span-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="appcreate_diy" default="<?php echo $conf['appcreate_diy']?>">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">APP生成价格(普及版)</label>
                        <div class="md:col-span-2">
                            <input type="text" name="appcreate_price" value="<?php echo $conf['appcreate_price']; ?>" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="填写0或留空则免费生成APP"/>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">APP生成价格(专业版)</label>
                        <div class="md:col-span-2">
                            <input type="text" name="appcreate_price2" value="<?php echo $conf['appcreate_price2']; ?>" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="填写0或留空则免费生成APP"/>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">APP默认主题颜色</label>
                        <div class="md:col-span-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="appcreate_theme" default="<?php echo $conf['appcreate_theme']?>">
                                <option value="#00A7AA">绿色</option>
                                <option value="#FFB6C1">浅粉丝</option>
                                <option value="#00BFFF">深蓝色</option>
                                <option value="#FF4500">橙红色</option>
                                <option value="#4169E1">皇家蓝</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">APP是否隐藏下方导航栏</label>
                        <div class="md:col-span-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="appcreate_nonav" default="<?php echo $conf['appcreate_nonav']?>">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                        <label class="text-sm font-semibold text-slate-700 md:text-right">分站未配置默认使用主站</label>
                        <div class="md:col-span-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="appcreate_default" default="<?php echo $conf['appcreate_default']?>">
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                        <div class="md:col-start-2 md:col-span-2">
                            <input type="submit" name="submit" value="保存修改" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"/>
                            <div class="mt-4 p-3 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 flex items-center">
                                <i class="fa fa-info-circle text-brand-500 mr-2 text-sm"></i>APP自动化打包平台：<a href="https://app.51zhihuigu.com/" class="text-brand-600 hover:underline font-medium ml-1" target="_blank" rel="noreferrer">点此进入</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 生成APP -->
            <div id="create" class="tab-content-pane hidden animate-[fadeIn_0.3s_ease-out]">
                <form method="post" role="form" class="space-y-6 max-w-2xl mx-auto">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">应用名称</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa fa-font text-slate-400"></i>
                            </div>
                            <input type="text" name="name" class="w-full h-11 pl-10 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo $conf['sitename']?>" maxlength="12"/>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">应用网址</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fa fa-link text-slate-400"></i>
                            </div>
                            <input type="text" name="url" class="w-full h-11 pl-10 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo $siteurl?>"/>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">应用图标</label>
                        <input type="file" id="file_icon" onchange="fileUpload(this, 'icon')" style="display:none;"/>
                        <div class="flex gap-2">
                            <input type="text" name="icon" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner flex-1" value="" placeholder="不上传则使用默认应用图标" data-fileid="" disabled/>
                            <a href="javascript:fileSelect('file_icon')" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium shadow-sm transition-colors shrink-0" title="上传图标"><i class="fa fa-upload mr-2"></i>上传</a>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">应用启动图</label>
                        <input type="file" id="file_background" onchange="fileUpload(this, 'background')" style="display:none;"/>
                        <div class="flex gap-2">
                            <input type="text" name="background" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner flex-1" value="" placeholder="不上传则使用默认应用启动图" data-fileid="" disabled/>
                            <a href="javascript:fileSelect('file_background')" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium shadow-sm transition-colors shrink-0" title="上传启动图"><i class="fa fa-upload mr-2"></i>上传</a>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-50">
                        <input type="button" id="submit" value="立即打包生成" class="w-full h-12 rounded-xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98]"/>
                    </div>
                </form>
            </div>

            <!-- 我的生成 -->
            <div id="query" class="tab-content-pane hidden animate-[fadeIn_0.3s_ease-out]">
                <div class="max-w-2xl mx-auto space-y-6">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa fa-globe text-slate-400"></i>
                            </div>
                            <input type="text" name="queryurl" value="<?php echo $qq?>" class="w-full h-11 pl-10 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="请输入要查询的应用网址（留空则为上一次生成）" onkeydown="if(event.keyCode==13){submit_query.click()}"/>
                        </div>
                        <button type="button" id="submit_query" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0">
                            <i class="fa fa-search mr-2"></i>查询状态
                        </button>
                    </div>
                    
                    <div id="result" class="hidden">
                        <!-- 查询结果将通过 JS 渲染在这里 -->
                    </div>
                </div>
            </div>

            <!-- 其他设置 -->
            <div id="other" class="tab-content-pane hidden animate-[fadeIn_0.3s_ease-out]">
                <div class="max-w-2xl mx-auto py-8">
                    <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6 text-center space-y-4 shadow-sm">
                        <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center text-3xl mx-auto mb-2">
                            <i class="fa fa-trash-o"></i>
                        </div>
                        <h4 class="text-rose-600 font-bold text-lg m-0">清空缓存与连接记录</h4>
                        <p class="text-sm text-rose-500 leading-relaxed max-w-md mx-auto">此功能是当APP下载链接集体失效的时候，点击“清空全部已生成的APP链接”后，系统会强制重新获取每个APP的下载链接。</p>
                        <div class="pt-2">
                            <input type="button" id="cleanApp" value="清空全部已生成的APP链接" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-rose-500 text-white font-bold hover:bg-rose-600 shadow-sm transition-all duration-200 cursor-pointer"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
// 选项卡切换逻辑 (取代原有的 Bootstrap 强依赖)
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const panes = document.querySelectorAll('.tab-content-pane');

    function switchTab(targetId) {
        tabs.forEach(tab => {
            if (tab.getAttribute('data-target') === targetId) {
                tab.classList.add('text-brand-600');
                tab.classList.remove('text-slate-500');
                tab.querySelector('.tab-indicator').classList.add('scale-x-100');
                tab.querySelector('.tab-indicator').classList.remove('scale-x-0');
            } else {
                tab.classList.remove('text-brand-600');
                tab.classList.add('text-slate-500');
                tab.querySelector('.tab-indicator').classList.remove('scale-x-100');
                tab.querySelector('.tab-indicator').classList.add('scale-x-0');
            }
        });

        panes.forEach(pane => {
            if (pane.id === targetId) {
                pane.classList.remove('hidden');
                pane.classList.add('block');
            } else {
                pane.classList.add('hidden');
                pane.classList.remove('block');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.getAttribute('data-target'));
        });
    });
    
    // 初始化激活第一个Tab
    switchTab('set');
});

// 后端业务逻辑补充
function checkURL(name)
{
	var url = $("input[name='"+name+"']").val();
	if(url == "")return;

	if (url.indexOf(" ")>=0){
		url = url.replace(/ /g,"");
	}
	if (url.toLowerCase().indexOf("http://")<0 && url.toLowerCase().indexOf("https://")<0){
		url = "http://"+url;
	}
	if (url.slice(url.length-1)=="/"){
		url = url.substring(0,url.length-1);
	}
	$("input[name='"+name+"']").val(url);
}
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
function saveSetting(obj){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax.php?act=set',
		data : $(obj).serialize(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert('设置保存成功！', {
					icon: 1,
					closeBtn: false
				}, function(){
				  window.location.reload()
				});
			}else{
				layer.alert(data.msg, {icon: 2})
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
	return false;
}
function fileSelect(obj){
	$("#"+obj).trigger("click");
}
function fileUpload(obj, des){
	var fileObj = $(obj)[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
		return;
	}
	var formData = new FormData();
	formData.append("file",fileObj);
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		url: "ajax_app.php?act=app_upload",
		data: formData,
		type: "POST",
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			layer.close(ii);
			if(data.code == 0){
				$("input[name='"+des+"']").val('图片上传成功('+data.fileid+')');
                $("input[name='"+des+"']").attr('data-fileid', data.fileid);
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	})
}
function querytask(){
	checkURL('queryurl');
	var url = $("input[name='queryurl']").val();
    var ii = layer.load(0);
    $("#result").addClass('hidden');
    $.ajax({
        type : 'POST',
        url : 'ajax_app.php?act=app_query',
		data : {url:url},
        dataType : 'json',
        success : function(data) {
            layer.close(ii);
            if(data.code == 0){
                // 采用 HuWangV7 UI 替代传统 table 渲染结果
                var statusBadge = data.data.status==1 ? '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">成功</span>' : data.data.status==-1 ? '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-500 border border-rose-100">打包失败</span>' : '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100"><i class="fa fa-spinner fa-spin mr-1"></i>正在打包中</span>';
                
                var item = '<div class="bg-slate-50 border border-slate-100 rounded-2xl overflow-hidden">';
                item += '<div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center bg-white"><h4 class="text-sm font-bold text-slate-800 m-0">任务查询结果</h4><button onclick="querytask()" class="text-xs font-medium text-brand-600 hover:underline outline-none"><i class="fa fa-refresh mr-1"></i>刷新状态</button></div>';
                
                item += '<div class="divide-y divide-slate-100 text-sm">';
                item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">应用名称</span><span class="text-slate-800 font-bold mt-1 sm:mt-0">'+data.data.name+'</span></div>';
                item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">应用网址</span><span class="text-slate-800 break-all mt-1 sm:mt-0">'+data.url+'</span></div>';
                item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">创建时间</span><span class="text-slate-600 mt-1 sm:mt-0">'+data.data.created_at+'</span></div>';
                item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">任务状态</span><span class="mt-1 sm:mt-0">'+statusBadge+'</span></div>';
                
                if(data.data.status==1){
                    item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">双端下载页</span><a href="'+data.download_url+'" target="_blank" class="text-brand-600 hover:underline break-all mt-1 sm:mt-0">'+data.download_url_show+'</a></div>';
					item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">安卓APP</span><a href="'+data.android_url+'" target="_blank" class="text-emerald-600 hover:underline break-all mt-1 sm:mt-0">'+data.android_url+'</a></div>';
					item += '<div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center"><span class="w-24 text-slate-500 font-medium shrink-0">iOS APP</span><span class="text-slate-600 mt-1 sm:mt-0"><a href="'+data.download_url+'" target="_blank" class="text-sky-600 hover:underline break-all">'+data.download_url_show+'</a> <span class="text-xs text-slate-400 ml-1">(须用 Safari 访问)</span></span></div>';
                    
                    if(navigator.userAgent.indexOf('Windows')>-1){
                        item += '<div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center bg-white"><span class="w-24 text-slate-500 font-medium shrink-0">扫码下载</span><div class="mt-2 sm:mt-0 p-2 bg-white border border-slate-100 rounded-xl shadow-sm inline-block"><img class="w-32 h-32 rounded-lg" src="//api.qrserver.com/v1/create-qr-code/?size=150x150&margin=10&data='+encodeURIComponent(data.download_url_show)+'"></div></div>';
                    }
                }
                item += '</div></div>';
                $("#result").html(item).removeClass('hidden').addClass('block animate-[fadeIn_0.3s_ease-out]');
            }else{
                item = '<div class="px-4 py-3 bg-rose-50 border border-rose-100 text-rose-600 rounded-xl text-sm flex items-center shadow-sm animate-[fadeIn_0.3s_ease-out]"><i class="fa fa-exclamation-circle text-lg mr-2"></i>'+data.msg+'</div>';
                $("#result").html(item).removeClass('hidden').addClass('block');
            }
        }
    });
}
$(document).ready(function(){
    $("#submit").click(function(){
		checkURL('url');
		var name = $("input[name='name']").val();
		var url = $("input[name='url']").val();
        var icon = $("input[name='icon']").attr('data-fileid');
        var background = $("input[name='background']").attr('data-fileid');
		if(name==''){layer.msg('应用名称不能为空！', {icon: 2});return false;}
        
        var confirmHtml = '<div style="padding: 10px; line-height: 24px;">请确认你的APP信息：<br/>应用名称：<strong style="color:#4f46e5;">'+name+'</strong><br/>应用网址：<strong style="color:#4f46e5;">'+url+'</strong></div>';
        
        var confirmobj = layer.confirm(confirmHtml, {
            title: '操作确认',
            btn: ['确定生成','返回修改']
        }, function(){
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({
                type : 'POST',
                url : 'ajax_app.php?act=app_submit',
                data : {name: name, url: url, icon: icon, background: background},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 0){
                        layer.alert(data.msg, {icon:1});
                    }else{
                        layer.alert(data.msg, {icon:2});
                    }
                }
            });
        }, function(){
            layer.close(confirmobj);
        });
	});
	$("#submit_query").click(function(){
		querytask();
	});
	$("#cleanApp").click(function(){
        var confirmobj = layer.confirm('是否确定强制清空全部已生成的APP链接记录？<br><span style="font-size:12px;color:red;">此操作主要用于系统下载链接集体失效时</span>', {
            title: '风险警告',
            btn: ['确定清空','取消操作'],
            icon: 0
        }, function(){
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $.ajax({
                type : 'POST',
                url : 'ajax_app.php?act=app_clean',
                data : {action: 'yes'},
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 0){
                        layer.alert(data.msg, {icon:1});
                    }else{
                        layer.alert(data.msg, {icon:2});
                    }
                }
            });
        }, function(){
            layer.close(confirmobj);
        });
	});
})
</script>
</body>
</html>
