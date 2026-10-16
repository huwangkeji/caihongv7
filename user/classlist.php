<?php
/**
 * 分类展示管理
**/
include("../includes/common.php");
$title='分类管理';
include './head.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if($userrow['power']==0){
	showmsg('权限拦截：您没有权限使用分站商品分类管理功能！',3);
}
$classhide = explode(',',$userrow['class']);
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0">
                    <i class="fa fa-th-large text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">商品分类展示管理</h3>
                    <p class="text-xs text-slate-500 m-0 mt-1">控制您的分站前台是否展示特定的商品大类</p>
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold w-2/3">分类系统名称</th>
                        <th class="px-6 py-4 font-semibold w-1/3 text-center">前台展示状态 (点击切换)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <form id="classlist">
<?php
$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 ORDER BY sort ASC");
$isEmpty = true;

while($res = $rs->fetch())
{
    $isEmpty = false;
    $isHidden = in_array($res['cid'], $classhide);
    
    // 构建基于 Tailwind 的状态按钮
    $btnHtml = $isHidden 
        ? '<span class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 hover:bg-amber-500 hover:text-white font-bold text-xs transition-colors cursor-pointer shadow-sm active:scale-95" onclick="setActive('.$res['cid'].',1)"><i class="fa fa-eye-slash mr-1.5"></i>已隐藏</span>'
        : '<span class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-500 hover:text-white font-bold text-xs transition-colors cursor-pointer shadow-sm active:scale-95" onclick="setActive('.$res['cid'].',0)"><i class="fa fa-eye mr-1.5"></i>展示中</span>';
        
    echo '<tr class="hover:bg-slate-50/50 transition-colors">
            <td class="px-6 py-4">
                <input type="text" class="w-full h-10 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-600 font-bold shadow-inner cursor-not-allowed focus:ring-0" name="name'.$res['cid'].'" value="'.htmlspecialchars($res['name']).'" placeholder="分类名称" disabled>
            </td>
            <td class="px-6 py-4 text-center">
                '.$btnHtml.'
            </td>
          </tr>';
}

if($isEmpty) {
    echo '<tr><td colspan="2" class="px-6 py-12 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-4xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">主站暂未配置任何有效的商品分类</p>
          </td></tr>';
}
?>
                    </form>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include './foot.php';?>

<script>
// 控制分类显示与隐藏的钩子，补全了 layer.load 过渡体验
function setActive(cid, active) {
    var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax_user.php?act=setClass&cid='+cid+'&active='+active,
		dataType : 'json',
		success : function(data) {
            layer.close(ii);
            layer.msg('分类展示状态切换成功！', {icon: 1, time: 1000}, function(){
			    window.location.reload();
            });
		},
		error:function(data){
            layer.close(ii);
			layer.msg('网络或服务器异常，状态同步失败', {icon: 2});
			return false;
		}
	});
}
</script>
</body>
</html>
