<?php
/**
 * 社区/卡盟对接日志
**/
include("../includes/common.php");
$title='批量工具';
include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$option = '<option value="-1">--请选择对接社区--</option>';
$option2 = '';
$re=$DB->query("SELECT * FROM `pre_shequ`");
while($res = $re->fetch()){
    $option .= '<option value="'.$res['id'].'" type="'.$res['type'].'">'.($res['remark']==''?'':$res['remark'].'_').$res['url'].'</option>';
}

if($_GET['act'] == 'getGoodsLiti'){
    @header('Content-Type: application/json; charset=UTF-8');
    $shequ = daddslashes($_POST['shequ']);
    $prid = daddslashes($_POST['prid']);
    $row=$DB->getRow("SELECT * FROM `pre_shequ` WHERE type='daishua' AND id='{$shequ}' LIMIT 1");

    //exit('{"code":-1,"msg":"'.json_encode($row).'"}');

    if($row){
        $url = ($row['protocol']==1?'https://':'http://') . $row['url'].'/api_tool/api/api.php?act=getGoodsLiti';
        $post = 'user='.$row['username'].'&pass='.$row['password'];

        $list = get_curl($url, $post);

        exit($list);

    }else{
        exit('{"code":-1,"msg":"获取社区失败"}');
    }
}
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6">

    <!-- 模块 1：图片下载到本地 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-cloud-download text-brand-500 mr-2 text-lg"></i>
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0">图片下载到本地</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="p-3 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start">
                <i class="fa fa-info-circle mt-0.5 mr-2 shrink-0"></i>
                <span>网络的商品图片下载到本地，仅下载如http(s)://开头的网络图片，图片过多可能会超时，多次下载即可，不会重复下载。</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">请选择社区</label>
                <div class="md:col-span-2">
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" id="shequ">
                        <?php echo $option?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">类型</label>
                <div class="md:col-span-2">
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" id="type">
                        <option value="0">商品图片</option>
                        <option value="1">商品介绍图片</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">下载数量</label>
                <div class="md:col-span-2">
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" id="sum">
                        <option value="50">50个</option>
                        <option value="100">100个</option>
                        <option value="200">200个</option>
                        <option value="9999999">全部</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <div class="md:col-start-2 md:col-span-2">
                    <button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]" onclick="downloadAll()">开始下载</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 模块 2：商品标题批量修改 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-brand-500 mr-2 text-lg"></i>
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0">商品标题批量修改</h2>
        </div>
        <div class="p-6 space-y-5">
            <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm flex items-start">
                <i class="fa fa-exclamation-triangle mt-0.5 mr-2 shrink-0"></i>
                <span>批量对接时，是无法判断商品下单信息是否要提取ID、链接。当商品介绍里面有某个关键字如【作品链接、主页链接、作品ID....】可以用本工具来修改本系统的下单标题。</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">请选择社区</label>
                <div class="md:col-span-2">
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" id="shequ2">
                        <?php echo $option?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">商品介绍里的关键字</label>
                <div class="md:col-span-2">
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" id="keyword" placeholder="例如：作品链接">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">第一个输入框标题</label>
                <div class="md:col-span-2 flex gap-2">
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors flex-1" id="title" placeholder="例如：作品ID[shareid]">
                    <a href="#inputabout" data-toggle="modal" class="inline-flex items-center justify-center px-4 h-11 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium shadow-sm transition-colors shrink-0" title="查看说明"><i class="fa fa-info-circle mr-1.5"></i>说明</a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                <div class="md:col-start-2 md:col-span-2">
                    <button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]" onclick="upInputTitle()">批量替换</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 模块 3：初始化商品 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-refresh text-brand-500 mr-2 text-lg"></i>
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0">初始化商品</h2>
        </div>
        <div class="p-6 space-y-5">
            <p class="text-sm text-slate-600 font-medium">包括：商品图片、商品介绍、下单参数</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                <label class="text-sm font-semibold text-slate-700 md:text-right">初始化目标社区</label>
                <div class="md:col-span-2 flex gap-2">
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors flex-1" id="shequ3">
                        <?php echo $option?>
                    </select>
                    <button type="button" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors shrink-0" onclick="getGoodsParam()">开始初始化</button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- 模态框：输入框标题说明 -->
<div class="modal fade fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden" align="left" id="inputabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog w-full max-w-lg m-4">
    <div class="modal-content bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
      <div class="modal-header px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="modal-title text-base font-bold text-slate-800 m-0" id="myModalLabel">输入框标题说明</h4>
        <button type="button" class="close text-slate-400 hover:text-slate-600 text-xl font-normal outline-none" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body p-6 text-sm text-slate-600 space-y-3">
          <p>使用以下输入框标题可实现特殊的转换功能</p>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1.5 text-xs">
              <p class="font-semibold text-slate-700">自动从链接和文字取出链接：</p>
              <p><a href="javascript:changeinput('作品链接')" class="text-brand-600 hover:underline font-medium">作品链接</a>、<a href="javascript:changeinput('视频链接')" class="text-brand-600 hover:underline font-medium">视频链接</a>、<a href="javascript:changeinput('分享链接')" class="text-brand-600 hover:underline font-medium">分享链接</a>、<a href="javascript:changeinput('自定义[shareurl]')" class="text-brand-600 hover:underline font-medium">自定义[shareurl]</a></p>
          </div>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1.5 text-xs">
              <p class="font-semibold text-slate-700">自动获取音乐/视频ID：</p>
              <p><a href="javascript:changeinput('作品ID')" class="text-brand-600 hover:underline font-medium">作品ID</a>、<a href="javascript:changeinput('帖子ID')" class="text-brand-600 hover:underline font-medium">帖子ID</a>、<a href="javascript:changeinput('用户ID')" class="text-brand-600 hover:underline font-medium">用户ID</a>、<a href="javascript:changeinput('自定义[shareid]')" class="text-brand-600 hover:underline font-medium">自定义[shareid]</a></p>
          </div>
          <p class="text-xs text-slate-500 pt-1 border-t border-slate-100">注：在输入框名称后面加[shareid]、[shareurl]可以分别有获取ID、获取URL功能</p>
      </div>
      <div class="modal-footer px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex justify-end">
        <button type="button" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-medium transition-colors shadow-sm" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
    // 原生模态框关闭支持
    $(document).ready(function() {
        $('[data-dismiss="modal"]').on('click', function() {
            $(this).closest('.modal').addClass('hidden');
        });
        $('[data-toggle="modal"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $(target).removeClass('hidden');
        });
    });

    function changeinput(str){
        $("#title").val(str);
        $("#inputabout").addClass('hidden'); // 选择后自动关闭弹窗
    }
    
    function upInputTitle(){
        var shequ = $('#shequ2').val();
        var keyword = $('#keyword').val();
        var title = $('#title').val();
        if(keyword == '' || title == ''){
            layer.msg('内容不能为空', {icon: 2});return false;
        }
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax_batch_docking.php?act=upInputTitle",
            dataType : 'json',
            data : {shequ:shequ,keyword:keyword,title:title},
            success : function(data) {
                layer.close(ii);
                layer.alert(data.msg);
            },
            error:function(data){
                layer.close(ii);
                layer.msg('加载失败，请刷新重试', {icon: 2});
                return false;
            }
        });
    }
    
     function downloadAll(){
        var shequ = $('#shequ').val();
        var type = $('#type').val();
        var sum = $('#sum').val();
        if(shequ == -1){
            layer.msg('请先选择社区', {icon: 0});
            return false;
        }
        if(type == 1){
            type= 'downloaddescimg';
        }else{
            type= 'downloadimg2';
        }
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            type : "POST",
            url : "ajax_batch_docking.php?act="+type,
            dataType : 'json',
            data : {shequ:shequ,sum:sum},
            success : function(data) {
                layer.close(ii);
                if(data.code == 1){
                    layer.alert(data.msg, {icon: 1});
                }else{
                    layer.alert(data.msg, {icon:2});
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('加载失败，请刷新重试', {icon: 2});
                return false;
            }
        });
     }
    
    // 初始化商品功能 (将 layer 原有内联 HTML 进行了样式美化)
    function getGoodsParam(){
        var shequ = $('#shequ3').val();
        if(shequ == -1){
            layer.alert('请先选择需要初始化的社区', {icon: 0});return false;
        }
        
        var customHtml = '<div style="padding: 10px; line-height: 24px;">' +
                         '<p style="color:#64748b; margin-bottom:15px; font-size:13px;">由于每个社区访问速度不一样，如果数量过多会导致超时报错。</p>' +
                         '<div style="display:flex; align-items:center; gap:10px;">' +
                         '<span style="font-weight:600; color:#334155; font-size:14px; white-space:nowrap;">执行数量</span>' +
                         '<select id="count" style="flex:1; height:36px; padding:0 10px; border-radius:8px; border:1px solid #e2e8f0; font-size:14px; outline:none;">' +
                         '<option value="1">1条</option><option value="10">10条</option><option value="20">20条</option><option value="30">30条</option><option value="50">50条</option><option value="100">100条</option><option value="200">200条</option><option value="1000">全部</option>' +
                         '</select></div></div>';
                         
        layer.open({
            title: '配置初始化参数',
            content: customHtml,
            btn:['确定执行','取消操作'],
            yes:function(index,layero){
                var count = $('#count').val();
                var ii = layer.load(2, {shade:[0.1,'#fff']});
                $.ajax({
                    type : "POST",
                    url : "ajax_batch_docking.php?act=getGoodsParam",
                    dataType : 'json',
                    data : {shequ:shequ,count:count},
                    success : function(data) {
                        layer.close(ii);
                        if(data.code == 0){
                            layer.alert(data.msg, {icon: 1});
                        }else{
                            layer.alert(data.msg, {icon:2});
                        }
                    },
                    error:function(data){
                        layer.close(ii);
                        layer.msg('加载失败，请刷新重试', {icon: 2});
                        return false;
                    }
                });            
            }
        });
    }
</script>
