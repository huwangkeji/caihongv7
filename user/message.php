<?php
/**
 * 站内信件与消息通知列表
**/
$is_defend=true;
require '../includes/common.php';
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 根据用户身份阶层拉取不同级别的通告
if($userrow['power']==2){
	$type = '0,2,4';
}elseif($userrow['power']==1){
	$type = '0,2,3';
}else{
	$type = '0,1';
}

$msgcount=$DB->getColumn("SELECT count(*) FROM pre_message WHERE type IN ($type) AND active=1");
$msgread = explode(',',$userrow['msgread']);
$limit=isset($_GET['limit'])?intval($_GET['limit']):10;
$rs=$DB->query("SELECT * FROM pre_message WHERE type IN ($type) AND active=1 ORDER BY id DESC LIMIT 0,$limit");
$msgrow=array();

while($res = $rs->fetch()){
	if(in_array($res['id'],$msgread))$res['read']=true;
	else $res['read']=false;
	$msgrow[]=$res;
}

$title = '消息列表';
include 'head.php';
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部看板 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0 shadow-sm">
                    <i class="fa fa-bell text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">系统消息与站内信</h3>
                    <p class="text-xs text-slate-500 m-0 mt-1">您共收到了 <b class="text-indigo-600 text-sm mx-1 font-mono"><?php echo $msgcount?></b> 条系统级信件通知</p>
                </div>
            </div>
            
            <button onclick="msg_read_all()" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors shadow-sm active:scale-95 shrink-0 border border-slate-200">
                <i class="fa fa-check-square-o mr-1.5"></i>一键悉知 (全部已读)
            </button>
        </div>

        <!-- 消息流水列表 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-center w-20">档案提取</th>
                        <th class="px-6 py-4 font-semibold w-1/2">通告发文标题</th>
                        <th class="px-6 py-4 font-semibold text-center hidden sm:table-cell">接收时间戳</th>
                        <th class="px-6 py-4 font-semibold text-center">查阅状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
foreach($msgrow as $row){
    $statusBadge = $row['read'] 
        ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm"><i class="fa fa-envelope-open-o mr-1"></i>已读</span>' 
        : '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-200 shadow-sm"><i class="fa fa-envelope mr-1"></i>未读信件</span>';
    
    // 未读信件给予明显的底色交互高亮
    $bgClass = $row['read'] ? 'hover:bg-slate-50/50' : 'bg-amber-50/20 hover:bg-amber-50/50';
    $titleClass = $row['read'] ? 'text-slate-600' : 'text-amber-700';
    
    echo '<tr class="transition-colors group ' . $bgClass . '">
            <td class="px-6 py-3 text-center">
                <button onclick="show('.$row['id'].')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm" title="启封信件"><i class="fa fa-eye"></i></button>
            </td>
            <td class="px-6 py-3 font-bold ' . $titleClass . ' max-w-[200px] sm:max-w-[350px] truncate" title="'.htmlspecialchars($row['title']).'">
                '.$row['title'].'
            </td>
            <td class="px-6 py-3 text-center font-mono text-xs text-slate-400 hidden sm:table-cell">
                '.$row['addtime'].'
            </td>
            <td class="px-6 py-3 text-center">
                '.$statusBadge.'
            </td>
        </tr>';
}

if($msgcount==0){
	echo '<tr><td colspan="4" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-bell-slash-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">信箱空空如也，系统暂未对您下发任何通告</p>
          </td></tr>';
}
?>	
                </tbody>
            </table>
        </div>
        
        <!-- 加载更多分页钩子 -->
		<?php if($msgcount>$limit){?>
		<div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center">
            <a href="?limit=<?php echo $limit+10;?>" id="btnload" class="inline-flex items-center justify-center px-6 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold text-sm hover:bg-slate-100 transition-colors shadow-sm active:scale-95">
                <i class="fa fa-angle-double-down mr-1.5"></i>加载更早的信件记录
            </a>
        </div>
		<?php }?>
        
    </div>
</div>

<?php include './foot.php';?>

<script>
// 一键已读 (补充了 Loading 防止连点卡顿)
function msg_read_all() {
    var ii = layer.load(2, {shade: [0.1, '#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax_user.php?act=msg_read_all',
		dataType : 'json',
		success : function(data) {
            layer.close(ii);
			if(data.code==0){
                layer.msg('全部信件已标记悉知', {icon: 1, time: 1000}, function(){
				    window.location.reload();
                });
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('网络通信断裂，请求驳回', {icon: 2});
		}
	});        	
}

// 查阅信件弹窗 
function show(id) {
    var ii = layer.load(2, {shade: [0.1, '#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax_user.php?act=msginfo&id='+id,
		dataType : 'json',
		success : function(data) {
            layer.close(ii);
			if(data.code==0){
                
                var contentHtml = '<div class="p-5 sm:p-6 bg-white overflow-y-auto max-h-[80vh] scrollbar-thin">';
                
                contentHtml += '<div class="text-center border-b border-slate-100 pb-4 mb-4">';
                contentHtml += '<h4 class="text-lg sm:text-xl font-bold text-slate-800 m-0 mb-2">' + data.title + '</h4>';
                contentHtml += '<div class="text-xs text-slate-400 font-mono"><i class="fa fa-user-circle mr-1"></i>系统总台发布于 ' + data.date + '</div>';
                contentHtml += '</div>';
                
                contentHtml += '<div class="text-sm text-slate-600 leading-relaxed prose prose-sm max-w-none prose-img:rounded-lg prose-a:text-indigo-600">' + data.content + '</div>';
                
                contentHtml += '</div>';

				layer.open({
				    type: 1,
				    skin: 'layui-layer-rim rounded-2xl overflow-hidden',
				    anim: 2,
				    area: [$(window).width() > 480 ? '450px' : '90%', 'auto'],
				    btn: ['关阅并销毁浮窗'],
				    btnAlign: 'c',
				    shadeClose: true,
				    title: false,
				    content: contentHtml,
				    end: function(){
					    window.location.reload();
				    }
				});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('信件提取失败，请重试', {icon: 2});
		}
	});
}
</script>
</body>
</html>
