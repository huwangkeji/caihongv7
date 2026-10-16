<?php
/**
 * 我的工单
**/
include("../includes/common.php");
$title='我的工单';
include './head.php';
if($islogin3==1){}else exit("<script language='javascript'>window.location.href='./login.php?back=workorder';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
if($conf['workorder_open']==0){
	showmsg('当前站点未开启工单功能',3);
}

function display_type($type){
	global $conf;
	$types = explode('|', $conf['workorder_type']);
	if($type==0 || !array_key_exists($type-1,$types))
		return '其它问题';
	else
		return $types[$type-1];
}

function display_status($status){
	if($status==1)
		return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm"><i class="fa fa-commenting-o mr-1"></i>待补充</span>';
	elseif($status==2)
		return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm"><i class="fa fa-check-circle mr-1"></i>已结单</span>';
	else
		return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm"><i class="fa fa-clock-o mr-1"></i>待处理</span>';
}

$count1=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE sid='{$suprow['sid']}' AND status=1");
$count2=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE sid='{$suprow['sid']}' AND status=0");
$count3=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE sid='{$suprow['sid']}'");

$my=isset($_GET['my'])?$_GET['my']:null;

// ----------------------------------------------------
// 视图：提交工单
// ----------------------------------------------------
if($my=='add')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-plus-circle text-indigo-500 mr-2 text-xl"></i>提交工单
            </h3>
            <a href="./workorder.php" class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 transition-colors shadow-sm border border-slate-200"><i class="fa fa-times"></i></a>
        </div>
        
        <div class="p-6">
            <form action="./workorder.php?my=add_submit" method="POST" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">关联订单编号</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-hashtag"></i></div>
                        <?php
                        if(isset($_GET['orderid']) && $_GET['orderid'] && md5($_GET['orderid'].SYS_KEY.$_GET['orderid'])===$_GET['skey']){
                        	$orderid = intval($_GET['orderid']);
                        	$res=$DB->getRow("SELECT id,tid,input FROM pre_orders WHERE id='{$orderid}' LIMIT 1");
                        	$toolname=$DB->getColumn("SELECT name FROM pre_tools WHERE tid='{$res['tid']}' LIMIT 1");
                        	echo '<input type="text" name="orderid" value="'.$orderid.'_'.htmlspecialchars($toolname).'_'.htmlspecialchars($res['input']).'" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed shadow-inner" disabled/>
                                  <input type="hidden" name="orderid" value="'.$orderid.'"/>
                                  <input type="hidden" name="skey" value="'.$_GET['skey'].'"/>';
                        }else{
                        	echo '<select name="orderid" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer"><option value="0">无（非订单问题无需选择）</option>';
                        	$rs=$DB->query("SELECT id,tid,input FROM pre_orders WHERE userid='{$suprow['sid']}' ORDER BY id DESC LIMIT 20");
                        	while($res = $rs->fetch()){
                        		$toolname=$DB->getColumn("SELECT name FROM pre_tools WHERE tid='{$res['tid']}' LIMIT 1");
                        		echo '<option value="'.$res['id'].'">'.$res['id'].'_'.htmlspecialchars($toolname).'_'.htmlspecialchars($res['input']).'</option>';
                        	}
                        	echo '</select>';
                        }
                        ?>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">工单类型分类</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-tags"></i></div>
                        <select name="type" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-white cursor-pointer">
                        	<?php foreach(explode('|', $conf['workorder_type']) as $key=>$value){
                        	    echo '<option value="'.($key+1).'">'.htmlspecialchars($value).'</option>';
                            }?>
                        	<option value="0">其它问题</option>
                        </select>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">详细描述</label>
                    <textarea class="w-full p-4 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400" name="content" rows="5" placeholder="请详细描述您遇到的问题或诉求..." required></textarea>
                </div>
                
                <?php if($conf['workorder_pic']==1){?>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">问题图片 (选填)</label>
                    <input type="file" id="file" onchange="fileUpload()" class="hidden"/>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <div class="pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-image"></i></div>
                        <input type="text" id="picurl" name="picurl" class="flex-1 h-12 pl-3 pr-4 text-sm font-mono border-0 focus:ring-0 placeholder:text-slate-400 cursor-pointer" readonly onclick="fileView()" placeholder="点击右侧按钮上传相关截图">
                        <button type="button" onclick="fileSelect()" class="px-4 border-l border-slate-200 bg-emerald-50 text-emerald-600 font-bold text-sm hover:bg-emerald-100 transition-colors shrink-0 flex items-center"><i class="fa fa-cloud-upload mr-1.5"></i>上传</button>
                    </div>
                </div>
                <?php }?>
                
                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                    <label class="text-sm font-semibold text-slate-700 ml-1">安全验证</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <img id="codeimg" src="./code.php?r=<?php echo time();?>" class="h-12 w-28 object-cover cursor-pointer border-r border-slate-200 shrink-0" onclick="this.src='./code.php?r='+Math.random();" title="点击更换验证码">
                        <input type="text" name="code" class="flex-1 h-12 pl-4 pr-4 text-sm font-bold border-0 focus:ring-0 placeholder:font-normal placeholder:text-slate-400" required="required" placeholder="请输入左侧验证码"/>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full h-14 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all"><i class="fa fa-paper-plane mr-2"></i>提交工单</button>
                </div>
            </form>
        </div>
        
        <div class="px-6 py-4 bg-amber-50 border-t border-amber-100 flex items-start">
            <i class="fa fa-info-circle text-amber-500 mr-2 mt-0.5 text-lg shrink-0"></i>
            <p class="text-xs text-amber-700 font-medium leading-relaxed m-0">找不到要提交的订单？<a href="../?chadan=1" class="font-bold hover:underline">点击进入查询订单</a>，在订单详情页面点击【投诉订单】即可自动携带订单号直接提交工单。</p>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="./workorder.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors"><i class="fa fa-angle-left mr-1"></i>返回工单列表</a>
    </div>

<?php
// ----------------------------------------------------
// 视图：工单详情对话界面
// ----------------------------------------------------
}
elseif($my=='view')
{
$id=intval($_GET['id']);
$rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND sid='{$suprow['sid']}' LIMIT 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$contents = explode('*',$rows['content']);
$myimg = '/assets/img/user.png';
$kfimg = 'https://imgcache.qq.com/open_proj/proj_qcloud_v2/mc_2014/work-order/css/img/custom-service-avatar.svg';
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto flex flex-col h-[80vh] min-h-[600px]">
        <!-- 头部信息 -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <i class="fa fa-sticky-note-o text-indigo-500 mr-2 text-lg"></i>工单详情 #<?php echo $id; ?>
                </h3>
                <p class="text-xs text-slate-500 mt-1 mb-0 font-medium">包含 <?php echo count($contents); ?> 条沟通记录</p>
            </div>
            <div class="flex items-center gap-3">
                <?php echo display_status($rows['status']); ?>
                <a href="./workorder.php" class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm border border-slate-200"><i class="fa fa-times"></i></a>
            </div>
        </div>

        <!-- 沟通记录滚动区 (Chat UI) -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 bg-slate-50/30 scrollbar-thin scrollbar-thumb-slate-200">
            
            <!-- 第一条：用户发起的问题描述 -->
            <div class="flex gap-4">
                <img src="<?php echo $myimg; ?>" class="w-10 h-10 rounded-full border border-slate-200 shadow-sm shrink-0 bg-white">
                <div class="flex-1 max-w-[85%]">
                    <div class="flex items-end gap-2 mb-1">
                        <span class="text-xs font-bold text-slate-700"><?php echo htmlspecialchars($suprow['user']); ?></span>
                        <span class="text-[10px] text-slate-400 font-mono"><?php echo $rows['addtime']; ?></span>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-2xl rounded-tl-none px-4 py-3 text-sm shadow-sm space-y-2">
                        <p class="m-0 leading-relaxed font-medium"><?php echo str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars(explode('^',$contents[0])[0])); ?></p>
                        <div class="mt-3 pt-3 border-t border-indigo-200/50 space-y-1 text-xs">
                            <p class="m-0"><span class="opacity-70">关联订单：</span><span class="font-mono font-bold"><?php echo $rows['orderid'] ? $rows['orderid'] : '无'; ?></span></p>
                            <p class="m-0"><span class="opacity-70">问题类型：</span><span class="font-bold"><?php echo display_type($rows['type']); ?></span></p>
                        </div>
                        <?php if($rows['picurl']){ ?>
                            <div class="mt-2 pt-2 border-t border-indigo-200/50">
                                <a href="../<?php echo $rows['picurl']; ?>" target="_blank" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-xs font-bold transition-colors">
                                    <i class="fa fa-picture-o mr-1.5"></i>查看附带的问题图片
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php
            // 后续对话遍历
            for($i=1; $i<count($contents); $i++){
            	$content = explode('^', $contents[$i]);
            	if(count($content)==3){
                    $isService = ($content[0] == 1);
                    if($isService){
                        // 客服回复 (居左)
                        echo '<div class="flex gap-4">
                                <img src="'.$kfimg.'" class="w-10 h-10 rounded-full border border-emerald-200 shadow-sm shrink-0 bg-white">
                                <div class="flex-1 max-w-[85%]">
                                    <div class="flex items-end gap-2 mb-1">
                                        <span class="text-xs font-bold text-emerald-600">官方客服</span>
                                        <span class="text-[10px] text-slate-400 font-mono">'.$content[1].'</span>
                                    </div>
                                    <div class="bg-white border border-slate-200 text-slate-700 rounded-2xl rounded-tl-none px-4 py-3 text-sm shadow-sm">
                                        <p class="m-0 leading-relaxed font-medium">'.str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($content[2])).'</p>
                                    </div>
                                </div>
                              </div>';
                    } else {
                        // 用户回复 (居右)
                        echo '<div class="flex gap-4 flex-row-reverse">
                                <img src="'.$myimg.'" class="w-10 h-10 rounded-full border border-indigo-200 shadow-sm shrink-0 bg-white">
                                <div class="flex-1 max-w-[85%] flex flex-col items-end">
                                    <div class="flex items-end gap-2 mb-1 flex-row-reverse">
                                        <span class="text-xs font-bold text-slate-700">'.htmlspecialchars($suprow['user']).'</span>
                                        <span class="text-[10px] text-slate-400 font-mono">'.$content[1].'</span>
                                    </div>
                                    <div class="bg-indigo-50 border border-indigo-100 text-indigo-900 rounded-2xl rounded-tr-none px-4 py-3 text-sm shadow-sm text-right">
                                        <p class="m-0 leading-relaxed font-medium">'.str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($content[2])).'</p>
                                    </div>
                                </div>
                              </div>';
                    }
            	}
            }
            ?>
        </div>

        <!-- 底部回复框控制 -->
        <div class="px-6 py-4 bg-white border-t border-slate-100 shrink-0">
            <?php if($rows['status']==0){ ?>
                <div class="flex items-center justify-center p-3 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 font-bold text-sm">
                    <i class="fa fa-coffee mr-2"></i>工单已提交，请耐心等待客服处理
                </div>
            <?php }elseif($rows['status']==2){ ?>
                <div class="flex items-center justify-center p-3 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 font-bold text-sm">
                    <i class="fa fa-lock mr-2"></i>此工单已经结单归档，无法继续回复
                </div>
            <?php }elseif($rows['status']==1){ ?>
                <form action="./workorder.php?my=reply&id=<?php echo $id?>" method="POST" class="space-y-3">
                	<textarea name="content" class="w-full h-20 p-3 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-inner bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400 resize-none" placeholder="输入需要补充的内容，官方客服将会收到您的消息..." required></textarea>
                	<div class="flex gap-3">
                    	<button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-indigo-600 text-white font-bold text-sm shadow-sm hover:bg-indigo-700 transition-colors"><i class="fa fa-paper-plane mr-1.5"></i>发送补充内容</button>
                    	<button type="button" onclick="window.location.href='./workorder.php?my=complete&id=<?php echo $id?>'" class="w-32 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 font-bold text-sm shadow-sm hover:bg-emerald-500 hover:text-white transition-colors">完结工单</button>
                	</div>
                </form>
            <?php } ?>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="./workorder.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors"><i class="fa fa-angle-left mr-1"></i>返回工单列表</a>
    </div>

<?php
// ----------------------------------------------------
// 逻辑处理集合
// ----------------------------------------------------
}
elseif($my=='add_submit')
{
    $orderid=intval($_POST['orderid']);
    $type=intval($_POST['type']);
    $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
    $picurl=strip_tags(daddslashes($_POST['picurl']));
    $code = isset($_POST['code'])?$_POST['code']:null;
    if (!$code || strtolower($code) != $_SESSION['vc_code']) {
    	unset($_SESSION['vc_code']);
    	showmsg('验证码错误！');
    }
    if (empty($content)) {
    	showmsg('描述信息不能为空！');
    } elseif ($orderid>0 && !$DB->getRow("SELECT id FROM pre_orders WHERE id='$orderid' AND userid='{$suprow['sid']}' LIMIT 1") && md5($orderid.SYS_KEY.$orderid)!==$_POST['skey']) {
    	showmsg('你只能选择自己的订单');
    } elseif ($orderid>0 && $DB->getRow("SELECT id FROM pre_workorder WHERE orderid='$orderid' AND status<2 ORDER BY id DESC LIMIT 1")) {
    	showmsg('请勿重复提交工单！');
    } else {
        $sql="INSERT INTO `pre_workorder` (`sid`,`type`,`orderid`,`content`,`picurl`,`addtime`,`status`) VALUES (:sid, :type, :orderid, :content, :picurl, NOW(), 0)";
        $data = [':sid'=>$suprow['sid'], ':type'=>$type, ':orderid'=>$orderid, ':content'=>$content, ':picurl'=>$picurl];
        if($DB->exec($sql, $data)){
        	$id = $DB->lastInsertId();
        	if($conf['message_workorder']==1){
        		\lib\MessageSend::workorder_new($id, $suprow['user'], $suprow['sid'], display_type($type), $content);
        	}
        	unset($_SESSION['vc_code']);
        	showmsg('提交工单成功！请等待管理员处理。<br/><br/><a href="./workorder.php" class="text-indigo-600 hover:underline">>> 返回工单列表</a>',1);
        }else{
        	showmsg('提交工单失败！'.$DB->error(),4);
        }
    }
}
elseif($my=='reply')
{
    $id=intval($_GET['id']);
    $rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND sid='{$suprow['sid']}' LIMIT 1");
    if(!$rows)
    	showmsg('当前记录不存在！',3);
    elseif($rows['status']==2)
    	showmsg('此工单已经结单',3);
    elseif($rows['status']==0)
    	showmsg('请耐心等待客服处理',3);
    $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
    if (empty($content)) {
    	showmsg('补充信息不能为空！');
    } else {
        $contents = addslashes($rows['content']).'*0^'.$date.'^'.$content;
        if($DB->exec("update pre_workorder set content=:content,status=0 where id=:id", [':content'=>$contents, ':id'=>$id])!==false){
        	if($conf['message_workorder']==1){
        		\lib\MessageSend::workorder_reply($id, $suprow['user'], $suprow['sid'], display_type($rows['type']), $content);
        	}
        	showmsg('回复工单成功！请等待管理员处理。<br/><br/><a href="./workorder.php" class="text-indigo-600 hover:underline">>> 返回工单列表</a>',1);
        }else{
        	showmsg('回复工单失败！'.$DB->error(),4);
        }
    }
}
elseif($my=='complete')
{
    $id=intval($_GET['id']);
    $rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND sid='{$suprow['sid']}' LIMIT 1");
    if(!$rows)
    	showmsg('当前记录不存在！',3);
    elseif($rows['status']==2)
    	showmsg('此工单已经结单',3);
    if($DB->exec("UPDATE pre_workorder SET status=2 WHERE id='{$id}'")!==false)
    	exit("<script language='javascript'>layer.msg('完结工单成功！', {icon:1, time:2000}, function(){history.go(-1);});</script>");
    else
    	showmsg('完结工单失败！'.$DB->error(),4);
}
elseif($my=='delete')
{
    $id=intval($_GET['id']);
    $sql="DELETE FROM pre_workorder WHERE id='$id' AND sid='{$suprow['sid']}'";
    if($DB->exec($sql)!==false)
    	exit("<script language='javascript'>layer.msg('删除成功！', {icon:1, time:2000}, function(){history.go(-1);});</script>");
    else
    	showmsg('删除失败！'.$DB->error(),4);
}
else
{
// ----------------------------------------------------
// 视图：工单列表大盘
// ----------------------------------------------------
?>
    <!-- 顶部工单状态概览 -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-3"><i class="fa fa-exclamation-circle text-xl"></i></div>
            <p class="text-[11px] font-bold text-slate-400 mt-1 uppercase">待补充</p>
            <h3 class="text-2xl font-mono font-bold text-rose-600 tracking-tight m-0 mt-1"><?php echo $count1?></h3>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <div class="w-12 h-12 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center mx-auto mb-3"><i class="fa fa-clock-o text-xl"></i></div>
            <p class="text-[11px] font-bold text-slate-400 mt-1 uppercase">处理中</p>
            <h3 class="text-2xl font-mono font-bold text-sky-600 tracking-tight m-0 mt-1"><?php echo $count2?></h3>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm text-center">
            <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-3"><i class="fa fa-check-circle text-xl"></i></div>
            <p class="text-[11px] font-bold text-slate-400 mt-1 uppercase">全部工单</p>
            <h3 class="text-2xl font-mono font-bold text-slate-800 tracking-tight m-0 mt-1"><?php echo $count3?></h3>
        </div>
    </div>

    <!-- 工单列表卡片 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-reorder text-indigo-500 mr-2 text-xl"></i>我的工单记录
            </h3>
            <a href="./workorder.php?my=add" class="px-4 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-sm transition-colors flex items-center"><i class="fa fa-plus mr-1.5"></i>发起工单</a>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center">ID</th>
                        <th class="px-5 py-4 font-semibold text-center">类型</th>
                        <th class="px-5 py-4 font-semibold">关联订单号</th>
                        <th class="px-5 py-4 font-semibold min-w-[200px]">问题描述</th>
                        <th class="px-5 py-4 font-semibold text-center">状态</th>
                        <th class="px-5 py-4 font-semibold text-center">提交时间</th>
                        <th class="px-5 py-4 font-semibold text-center">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
$numrows=$DB->getColumn("SELECT count(*) from pre_workorder WHERE sid='{$suprow['sid']}'");

$pagesize=30;
$pages=intval($numrows/$pagesize);
if ($numrows%$pagesize) { $pages++; }
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset=$pagesize*($page - 1);
$link = '';

$rs=$DB->query("SELECT * FROM pre_workorder WHERE sid='{$suprow['sid']}' ORDER BY id DESC LIMIT $offset,$pagesize");
$hasData = false;

while($res = $rs->fetch())
{
    $hasData = true;
    $content=explode('*',$res['content']);
    $content_brief=mb_substr(explode('^', $content[0])[0] ?? $content[0], 0, 16, 'utf-8');
    
    echo "<tr class='hover:bg-slate-50/50 transition-colors group'>";
    echo "<td class='px-5 py-3.5 text-center'><span class='font-mono font-bold text-slate-500'>{$res['id']}</span></td>";
    echo "<td class='px-5 py-3.5 text-center'><span class='px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs font-bold'>".display_type($res['type'])."</span></td>";
    
    if($res['orderid'] > 0){
        echo "<td class='px-5 py-3.5 font-mono'><a href=\"javascript:showOrder(".$res['orderid'].",'".md5($res['orderid'].SYS_KEY.$res['orderid'])."')\" title=\"查询订单详情\" class=\"text-indigo-600 hover:underline font-bold\">{$res['orderid']}</a></td>";
    } else {
        echo "<td class='px-5 py-3.5 font-mono text-slate-400'>-</td>";
    }
    
    echo "<td class='px-5 py-3.5'><a href=\"./workorder.php?my=view&id={$res['id']}\" class=\"font-bold text-slate-700 hover:text-indigo-600 transition-colors\">".htmlspecialchars($content_brief)."...</a></td>";
    echo "<td class='px-5 py-3.5 text-center'>".display_status($res['status'])."</td>";
    echo "<td class='px-5 py-3.5 text-center font-mono text-xs text-slate-400'>{$res['addtime']}</td>";
    echo "<td class='px-5 py-3.5 text-center flex items-center justify-center gap-1.5'>
            <a href=\"./workorder.php?my=view&id={$res['id']}\" class=\"px-3 py-1 rounded bg-indigo-50 text-indigo-600 font-bold text-xs hover:bg-indigo-500 hover:text-white transition-colors border border-indigo-100\">处理</a>
            <a href=\"./workorder.php?my=delete&id={$res['id']}\" class=\"px-3 py-1 rounded bg-rose-50 text-rose-600 font-bold text-xs hover:bg-rose-500 hover:text-white transition-colors border border-rose-100\" onclick=\"return confirm('确定要删除此工单吗？删除后无法恢复。');\">删除</a>
          </td>";
    echo "</tr>";
}

if(!$hasData){
    echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium m-0">暂无工单记录</p>
          </td></tr>';
}
?>
                </tbody>
            </table>
        </div>

        <!-- 分页栏 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
<?php
$first=1;
$prev=$page-1;
$next=$page+1;
$last=$pages;
if ($page>1)
{
echo '<a href="workorder.php?page='.$first.$link.'" class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold">首页</a>';
echo '<a href="workorder.php?page='.$prev.$link.'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-left"></i></a>';
} else {
echo '<span class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold">首页</span>';
echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
}

$s = ($pages >= 10) ? 10 : $pages; // 简单分页逻辑保留原样
for ($i=1; $i<$page; $i++)
    echo '<a href="workorder.php?page='.$i.$link.'" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">'.$i .'</a>';

echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">'.$page.'</span>';

for ($i=$page+1; $i<=$s; $i++)
    echo '<a href="workorder.php?page='.$i.$link.'" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">'.$i .'</a>';

if ($page<$pages)
{
echo '<a href="workorder.php?page='.$next.$link.'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-right"></i></a>';
echo '<a href="workorder.php?page='.$last.$link.'" class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-xs font-bold">尾页</a>';
} else {
echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
echo '<span class="min-w-[3rem] px-3 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-xs font-bold">尾页</span>';
}
?>
            </nav>
        </div>
        <?php } ?>
    </div>

<?php
}
?>
</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
function fileSelect(){
	$("#file").trigger("click");
}
function fileView(){
	var picurl = $("#picurl").val();
	if(picurl=='') {
		return;
	}
	if(picurl.indexOf('http') == -1)picurl = '../'+picurl;
	layer.open({
		type: 1,
		title: false,
		closeBtn: 0,
		skin: 'layui-layer-nobg',
		shadeClose: true,
		content: '<img src="'+picurl+'" class="max-w-[90vw] max-h-[90vh] rounded-2xl shadow-2xl border-4 border-white object-contain">'
	});
}
function fileUpload(){
	var fileObj = $("#file")[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
		return;
	}
	var formData = new FormData();
	formData.append("do","upload");
	formData.append("type","user");
	formData.append("file",fileObj);
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		url: "ajax_user.php?act=uploadimg",
		data: formData,
		type: "POST",
		dataType: "json",
		cache: false,
		processData: false,
		contentType: false,
		success: function (data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('图片上传并绑定成功！', {icon: 1});
				$("#picurl").val(data.url);
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('服务器网络异常', {icon: 2});
			return false;
		}
	})
}
function showOrder(id,skey){
	if(id==0)return false;
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	var statusHtml = [
	    '<span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 font-bold border border-sky-100 text-[11px]">待处理</span>',
	    '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold border border-emerald-100 text-[11px]">已完成</span>',
	    '<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 font-bold border border-amber-100 text-[11px]">处理中</span>',
	    '<span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold border border-rose-100 text-[11px]">异常</span>',
	    '<span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold border border-slate-200 text-[11px]">已退款</span>'
	];
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=order",
		data : {id:id,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var item = '<div class="p-6 bg-slate-50 rounded-2xl"><div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">';
				item += '<table class="w-full text-sm text-left text-slate-600">';
				item += '<tbody>';
				item += '<tr class="border-b border-slate-50"><th colspan="2" class="px-4 py-2 font-bold text-slate-700 text-center text-sm bg-slate-50">订单基本信息</th></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单编号</th><td class="px-4 py-3 font-mono font-bold text-indigo-600">'+id+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">商品名称</th><td class="px-4 py-3 font-bold text-slate-700">'+data.name+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单金额</th><td class="px-4 py-3 font-mono font-bold text-slate-700">'+data.money+'元</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">购买时间</th><td class="px-4 py-3 font-mono text-xs text-slate-400">'+data.date+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">下单信息</th><td class="px-4 py-3 font-mono text-xs text-slate-500 break-all">'+data.inputs+'</td></tr>';
				item += '<tr><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">订单状态</th><td class="px-4 py-3">'+statusHtml[data.status]+'</td></tr>';
				
				if(data.list && data.list.order_state){
				    item += '<tr class="border-t border-slate-200 bg-amber-50/30"><th colspan="2" class="px-4 py-2 font-bold text-amber-700 text-center text-xs tracking-widest"><i class="fa fa-dashboard mr-1"></i>订单实时状态</th></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">下单数量</th><td class="px-4 py-2 font-mono font-bold text-slate-700">'+data.list.num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">初始数量</th><td class="px-4 py-2 font-mono text-slate-500">'+data.list.start_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">当前数量</th><td class="px-4 py-2 font-mono text-emerald-600 font-bold">'+data.list.now_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">订单状态</th><td class="px-4 py-2 font-bold text-indigo-600">'+data.list.order_state+'</td></tr>';
				}else if(data.kminfo){
				    item += '<tr class="border-t border-slate-200 bg-sky-50/30"><th colspan="2" class="px-4 py-2 font-bold text-sky-700 text-center text-xs tracking-widest">以下是你的卡密信息</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 font-mono text-xs text-slate-600 leading-relaxed break-all bg-sky-50/10">'+data.kminfo+'</td></tr>';
				}else if(data.result){
				    item += '<tr class="border-t border-slate-200 bg-slate-50/30"><th colspan="2" class="px-4 py-2 font-bold text-slate-600 text-center text-xs tracking-widest">处理结果</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-xs text-slate-500 leading-relaxed">'+data.result+'</td></tr>';
				}
				if(data.alert){
				    item += '<tr class="border-t border-slate-200 bg-slate-50/30"><th colspan="2" class="px-4 py-2 font-bold text-slate-600 text-center text-xs tracking-widest">商品简介</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-xs text-slate-500 leading-relaxed">'+data.desc+'</td></tr>';
				}
				item += '</tbody></table></div></div>';
				
				layer.open({
				  type: 1,
				  title: '<div class="font-bold text-slate-800"><i class="fa fa-list-alt text-indigo-500 mr-2"></i>订单详细信息</div>',
				  area: [$(window).width() > 500 ? '500px' : '95%', 'auto'],
				  skin: 'layui-layer-rim rounded-2xl',
				  content: item
				});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		},
		error: function(){
		    layer.close(ii);
		    layer.msg('查询接口无响应');
		}
	});
}
</script>
</body>
</html>
