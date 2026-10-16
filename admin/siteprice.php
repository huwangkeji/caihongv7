<?php

include "../includes/common.php";
$title = "自定义分站商品密价";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
<?php 
adminpermission("site", 1);
$zid = isset($_GET["zid"]) ? intval($_GET["zid"]) : showmsg("参数不完整");
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- 头部区域 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <!-- 动态标题挂载点 -->
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <!-- 由 siteprice.js 动态渲染填充 -->
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页显示条数</span>
                <select id="pagesize" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="80">80</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        
        <div class="p-6">
            <!-- 搜索与操作控制栏 -->
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 mb-6">
                <div class="relative flex-1 min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入要检索的商品名称">
                </div>
                
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        检索
                    </button>
                    <a href="javascript:changePrice()" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-emerald-500 text-white font-medium text-sm hover:bg-emerald-600 shadow-sm transition-colors">
                        <i class="fa fa-bolt mr-1.5"></i>一键批量改价
                    </a>
                    <a href="javascript:clearPrice()" class="inline-flex items-center justify-center px-5 h-11 rounded-xl bg-amber-500 text-white font-medium text-sm hover:bg-amber-600 shadow-sm transition-colors">
                        <i class="fa fa-undo mr-1.5"></i>恢复系统原价
                    </a>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="重载商品列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>

            <!-- 异步表格渲染挂载点 -->
            <div id="listTable" class="w-full"></div>
        </div>
    </div>
</div>

<script type="text/html" id="content">
    <div class="p-6 space-y-5 bg-white">
        <div class="space-y-1.5">
            <label class="text-sm font-semibold text-slate-700">批量改价算法类型</label>
            <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 cursor-pointer transition-colors" name="type">
                <option value="0">按比例幅度 (百分比 %)</option>
                <option value="1">按绝对金额 (固定数值)</option>
            </select>
        </div>
        <div class="space-y-1.5">
            <label class="text-sm font-semibold text-slate-700">调整参数值</label>
            <input type="number" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="value" placeholder="填写对应的增减幅度参数值">
        </div>
        <div class="pt-2">
            <input type="button" id="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer" value="确认执行全局修改">
        </div>
    </div>
</script>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/siteprice.js?ver=<?php echo VERSION;?>"></script>
<script>
    // 保留原始交互与 AJAX 通信逻辑
    function changePrice(){
        layer.open({
            type: 1,
            title: '批量密价一键调整引擎',
            shade: 0.4,
            area: ['380px', 'auto'],
            content: $("#content").html(),
            success: function(index, layero){
                $("#submit").click(function (){
                    var value = $("input[name='value']").val();
                    var type = $("select[name='type']").val();
                    if(value == ''){
                        layer.msg('改价参数值请勿留空！', {icon:0});
                        return false;
                    }
                    var ii = layer.load(2, {shade: [0.1, '#fff']});
                    $.ajax({
                        type: "post",
                        url: "ajax_site.php?act=change_price",
                        data: {type: type, value: value, zid: zid},
                        dataType: "json",
                        success: function(data){
                            layer.close(ii);
                            layer.closeAll();
                            if(data.code == 0){
                                layer.alert('站点密价全库覆盖修改成功！', {icon: 1}, function(){
                                    listTable();
                                    layer.closeAll();
                                });
                            }else{
                                layer.alert(data.msg, {icon: 2});
                            }
                        },
                        error: function() {
                            layer.close(ii);
                            layer.msg('服务器通信异常，请重试');
                        }
                    });
                });
            }
        });
    }
</script>
</body>
</html>
