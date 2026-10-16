<?php
include("../includes/common.php");
$title='防墙引导页设置';
$mod='wall_guide';  // 用于菜单选中
include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 1);
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$mod=isset($_GET['mod'])?$_GET['mod']:null;

?>
<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10">

    <!-- 功能说明模块 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-info-circle text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">功能说明</h3>
        </div>
        <div class="p-6">
            <div class="p-5 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm shadow-sm space-y-4">
                <div>
                    <p class="font-bold text-sky-800 mb-2">应用场景与特性：</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-1 marker:text-sky-400">
                        <li>开启后，用户访问前台网站时会优先展示一个独立拦截的引导页面。</li>
                        <li>访客点击“继续访问”按钮后，在设定的时间内（如 24 小时）将不再重复显示。</li>
                        <li>有效适用于：网站免责声明下发、防封锁（防墙）缓冲、防范劣质恶意爬虫自动化抓取等。</li>
                        <li>前端深度优化，支持自动适配操作系统的暗黑模式，加载速度极快。</li>
                    </ul>
                </div>
                <div class="border-t border-sky-200/60 pt-3">
                    <p class="font-bold text-sky-800 mb-2">配置项参考：</p>
                    <ul class="list-disc list-inside space-y-1.5 ml-1 marker:text-sky-400">
                        <li><span class="font-semibold">标题与内容</span>：高度可定制化的文案展示（内容域支持回车换行排版）。</li>
                        <li><span class="font-semibold">显示间隔</span>：动态控制引导页展现频率，降低对真实用户的打扰。</li>
                        <li><span class="font-semibold">主题颜色</span>：灵活调节引导页的主体色调以完美契合您的站点品牌色。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 核心配置表单 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-shield text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">防墙引导页设置</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form onsubmit="return saveSetting(this)" method="post" role="form" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">防墙引导页全局开关</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="wall_guide_open" default="<?php echo $conf['wall_guide_open'] ?? '';?>">
                            <option value="0">暂不启用 (关闭)</option>
                            <option value="1">强制拦截 (开启)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">显示间隔时长 <span class="text-slate-400 font-normal text-xs ml-1">(小时)</span></label>
                        <div class="relative">
                            <input type="number" name="wall_guide_interval" value="<?php echo !empty($conf['wall_guide_interval']) ? $conf['wall_guide_interval'] : 24; ?>" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入多久显示一次" min="1" max="720">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center font-medium text-slate-400 text-sm pointer-events-none">H</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">设置同一访客多久会再次看到该页面，默认推荐 24 小时</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">引导页主标题</label>
                    <input type="text" name="wall_guide_title" value="<?php echo $conf['wall_guide_title'] ?? ''; ?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入引导页顶部的醒目标题">
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">引导页主要提示内容</label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[120px]" name="wall_guide_content" placeholder="请输入引导页显示的详细免责声明或提示内容（支持回车键换行）"><?php echo $conf['wall_guide_content'] ?? ''; ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">放行按钮文字</label>
                        <input type="text" name="wall_guide_btn" value="<?php echo $conf['wall_guide_btn'] ?? ''; ?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="如：我已知晓，继续访问">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">引导页主题色调</label>
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl overflow-hidden shadow-sm border border-slate-200 shrink-0">
                                <input type="color" name="wall_guide_theme_color" value="<?php echo !empty($conf['wall_guide_theme_color']) ? $conf['wall_guide_theme_color'] : '#2193b0'; ?>" class="w-[150%] h-[150%] -m-[25%] cursor-pointer border-0 outline-none p-0">
                            </div>
                            <p class="text-xs text-slate-400 leading-tight">影响进入按钮、背景微光及点缀图标的品牌颜色</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <button type="submit" name="submit" class="w-full md:w-auto px-10 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存配置修改
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
// 初始化下拉框的默认值
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
    $(items[i]).val($(items[i]).attr("default")||0);
}

// 异步提交表单设置
function saveSetting(obj){
    var ii = layer.load(2, {shade:[0.1,'#fff']});
    $.ajax({
        type : 'POST',
        url : 'ajax_wall_guide.php?act=set',
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
            layer.close(ii);
            layer.msg('服务器网络通信错误，请稍后重试');
            return false;
        }
    });
    return false;
}
</script>
