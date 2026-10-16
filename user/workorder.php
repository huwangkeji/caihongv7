<?php
/**
 * 售后工单与通信枢纽
**/
include("../includes/common.php");
$title='我的工单';
include './head.php';

// 登录防泄漏隔离
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php?back=workorder';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
if($conf['workorder_open']==0){
	showmsg('通道关闭：总台暂未开放售后工单流转引擎！',3);
}

// ==========================================
// 核心状态释义驱动器
// ==========================================
function display_type($type){
	global $conf;
	$types = explode('|', $conf['workorder_type']);
	if($type==0 || !array_key_exists($type-1,$types)) return '其它问题';
	else return $types[$type-1];
}

function display_status($status){
	if($status==1) return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-100 shadow-sm"><i class="fa fa-reply mr-1"></i>总台已复</span>';
	elseif($status==2) return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-500 border border-slate-200 shadow-sm"><i class="fa fa-check-circle mr-1"></i>档案封存</span>';
	else return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm"><i class="fa fa-clock-o mr-1"></i>列队待审</span>';
}

$count1=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE zid='{$userrow['zid']}' AND status=1");
$count2=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE zid='{$userrow['zid']}' AND status=0");
$count3=$DB->getColumn("SELECT count(*) FROM pre_workorder WHERE zid='{$userrow['zid']}'");

$my=isset($_GET['my'])?$_GET['my']:null;

// ==========================================
// 视图分支：新建工单提交流水线
// ==========================================
if($my=='add')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-paper-plane text-indigo-500 mr-2 text-xl"></i>起草新售后工单
            </h3>
            <a href="./workorder.php" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-reply mr-1"></i>返回队列</a>
        </div>
        
        <div class="p-6">
            <form action="./workorder.php?my=add_submit" method="POST" class="space-y-5">
                
                <div class="space-y-1.5 border-b border-slate-100 pb-5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">受控目标：关联订单编号 <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-hashtag"></i></div>
                        <?php
                        if(isset($_GET['orderid']) && $_GET['orderid'] && md5($_GET['orderid'].SYS_KEY.$_GET['orderid'])===$_GET['skey']){
                        	$orderid = intval($_GET['orderid']);
                        	$res=$DB->getRow("SELECT id,tid,input FROM pre_orders WHERE id='{$orderid}' LIMIT 1");
                        	$toolname=$DB->getColumn("SELECT name FROM pre_tools WHERE tid='{$res['tid']}' LIMIT 1");
                        	echo '<input type="text" name="orderid_display" value="'.htmlspecialchars($orderid.'_'.$toolname.'_'.$res['input'], ENT_QUOTES, 'UTF-8').'" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-indigo-200 focus:ring-0 shadow-inner bg-indigo-50 text-indigo-700 cursor-not-allowed" disabled/><input type="hidden" name="orderid" value="'.htmlspecialchars($orderid, ENT_QUOTES, 'UTF-8').'"/><input type="hidden" name="skey" value="'.htmlspecialchars($_GET['skey'], ENT_QUOTES, 'UTF-8').'"/>';
                        }else{
                        	echo '<select name="orderid" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white text-slate-700 cursor-pointer"><option value="0">请选择异标订单（非订单纠纷请无视）</option>';
                        	$rs=$DB->query("SELECT id,tid,input FROM pre_orders WHERE userid='{$userrow['zid']}' ORDER BY id DESC LIMIT 20");
                        	while($res = $rs->fetch()){
                        		$toolname=$DB->getColumn("SELECT name FROM pre_tools WHERE tid='{$res['tid']}' LIMIT 1");
                        		echo '<option value="'.$res['id'].'">'.$res['id'].'_'.$toolname.'_'.$res['input'].'</option>';
                        	}
                        	echo '</select>';
                        }
                        ?>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">事发归类维度 <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-sitemap"></i></div>
                        <select name="type" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white text-slate-700 cursor-pointer">
                        	<?php foreach(explode('|', $conf['workorder_type']) as $key=>$value){
                            	echo '<option value="'.($key+1).'">'.$value.'</option>';
                            }?>
                        	<option value="0">其它异常盲区</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">案情复盘 (详细描述) <span class="text-rose-500">*</span></label>
                    <textarea name="content" rows="6" class="w-full p-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 placeholder:font-normal placeholder:text-slate-300 leading-relaxed" placeholder="请详尽还原问题现场，提供有效线索以便总台快速排查..." required></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">案发现场截图 (非必传)</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-sky-500 focus-within:ring-1 focus-within:ring-sky-500 transition-all bg-white">
                        <div class="pl-4 flex items-center pointer-events-none text-slate-400 bg-white"><i class="fa fa-picture-o"></i></div>
                        <input type="file" id="file" onchange="fileUpload()" class="hidden"/>
                        <input type="text" id="picurl" name="picurl" class="flex-1 h-11 px-3 text-sm font-mono font-bold border-0 focus:ring-0 placeholder:text-slate-300 placeholder:font-normal bg-white cursor-pointer" readonly onclick="fileView()" placeholder="未挂载图像流...">
                        <button type="button" onclick="fileSelect()" class="px-4 bg-sky-50 border-l border-slate-200 text-sky-600 font-bold text-xs hover:bg-sky-100 transition-colors cursor-pointer active:scale-95"><i class="fa fa-cloud-upload mr-1.5"></i>呼唤图床上传</button>
                    </div>
                </div>

                <div class="space-y-1.5 pt-2">
                    <label class="text-sm font-semibold text-slate-700 ml-1">人工干预防护 <span class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <img id="codeimg" src="./code.php?r=<?php echo time();?>" class="h-11 cursor-pointer border-r border-slate-200" onclick="this.src='./code.php?r='+Math.random();" title="干扰过大？点击重绘图灵测试">
                        <input type="text" name="code" class="flex-1 h-11 px-4 text-base font-mono font-bold tracking-widest border-0 focus:ring-0 placeholder:text-slate-300 placeholder:font-normal placeholder:tracking-normal bg-white uppercase" required placeholder="录入视觉验证字符..."/>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-600 text-white font-bold text-lg shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-700 active:scale-[0.98] transition-all">
                        <i class="fa fa-send mr-2"></i>封装数据并投递至总台信箱
                    </button>
                </div>
            </form>
            
            <div class="mt-5 p-3 bg-sky-50 rounded-xl border border-sky-100 flex items-start text-xs text-sky-800 font-medium leading-relaxed shadow-inner">
                <i class="fa fa-lightbulb-o text-sky-500 mr-2 text-lg mt-0.5 shrink-0"></i>
                <div>
                    无法在列表中定位订单？请前往 <a href="../?chadan=1" target="_blank" class="text-indigo-600 font-bold hover:underline mx-0.5">访客查单大厅</a>。在订单详情追踪面板内，可直接点击【投诉此订单】按钮，系统将自动携带标的物特征链回转至本页面。
                </div>
            </div>
        </div>
    </div>
<?php
}
// ==========================================
// 视图分支：工单详情与双边沟通会话
// ==========================================
elseif($my=='view')
{
    $id=intval($_GET['id']);
    $rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND zid='{$userrow['zid']}' LIMIT 1");
    if(!$rows) showmsg('查无此案：总台档案库未收录该案卷！',3);
    
    // 解析古老的对话切割语法
    $contents = explode('*',$rows['content']);
    $myimg = '/assets/img/user.png';
    $kfimg = 'https://imgcache.qq.com/open_proj/proj_qcloud_v2/mc_2014/work-order/css/img/custom-service-avatar.svg';
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto flex flex-col h-[85vh]">
        <!-- 通信舱头部 -->
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <a href="./workorder.php" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:text-indigo-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-angle-left text-xl"></i></a>
                <div>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">案宗卷宗 #<?php echo $id; ?></h3>
                    <p class="text-[11px] text-slate-500 m-0 mt-0.5">双边会话记录: <?php echo count($contents); ?> 条信道交互</p>
                </div>
            </div>
            <div>
                <?php echo display_status($rows['status'])?>
            </div>
        </div>

        <!-- 通信舱滚动画布 (类似微信聊天区) -->
        <div class="p-6 flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-200 bg-slate-100 space-y-6">
            
            <!-- 首发案件申诉气泡 -->
            <div class="flex w-full mt-2 space-x-3 max-w-3xl ml-auto justify-end">
                <div class="flex flex-col items-end gap-1 w-full">
                    <span class="text-[10px] text-slate-400 font-bold uppercase"><i class="fa fa-clock-o mr-1"></i><?php echo $rows['addtime']?></span>
                    <div class="bg-indigo-500 text-white p-4 rounded-2xl rounded-tr-sm shadow-md text-sm leading-relaxed max-w-[90%] break-words">
                        <div class="font-bold border-b border-indigo-400/50 pb-2 mb-2 flex flex-col gap-1 text-indigo-50 text-xs">
                            <span><i class="fa fa-hashtag w-4"></i>订单：<span class="font-mono"><?php echo $rows['orderid']?$rows['orderid']:'未指派标的物';?></span></span>
                            <span><i class="fa fa-sitemap w-4"></i>分类：<?php echo display_type($rows['type'])?></span>
                        </div>
                        <div class="whitespace-pre-wrap"><?php echo str_replace(array("\r\n", "\r", "\n"), "<br/>",htmlspecialchars($contents[0]))?></div>
                        <?php if($rows['picurl']){ ?>
                            <div class="mt-3 pt-3 border-t border-indigo-400/50">
                                <a href="../<?php echo $rows['picurl']; ?>" target="_blank" class="inline-flex items-center text-xs font-bold text-indigo-100 hover:text-white transition-colors"><i class="fa fa-image mr-1"></i>查阅随案光栅图档</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <img src="<?php echo $myimg?>" class="w-10 h-10 rounded-full border-2 border-white shadow-sm shrink-0">
            </div>

            <!-- 后续交互气泡流 -->
            <?php
            for($i=1;$i<count($contents);$i++){
            	$content = explode('^',$contents[$i]);
            	if(count($content)==3){
            	    $isKf = ($content[0]==1);
            	    if($isKf){
            	        // 客服气泡 (左侧)
            	        echo '<div class="flex w-full mt-2 space-x-3 max-w-3xl">
                                <img src="'.$kfimg.'" class="w-10 h-10 rounded-full border-2 border-white shadow-sm shrink-0 bg-white">
                                <div class="flex flex-col gap-1 w-full">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-700">总台干员</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">'.$content[1].'</span>
                                    </div>
                                    <div class="bg-white text-slate-700 p-4 rounded-2xl rounded-tl-sm shadow-sm border border-slate-100 text-sm leading-relaxed max-w-[90%] break-words whitespace-pre-wrap">'.str_replace(array("\r\n", "\r", "\n"), "<br/>",htmlspecialchars($content[2])).'</div>
                                </div>
                              </div>';
            	    }else{
            	        // 站长气泡 (右侧)
            	        echo '<div class="flex w-full mt-2 space-x-3 max-w-3xl ml-auto justify-end">
                                <div class="flex flex-col items-end gap-1 w-full">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-400 font-bold uppercase">'.$content[1].'</span>
                                        <span class="text-xs font-bold text-slate-700">我方提交</span>
                                    </div>
                                    <div class="bg-indigo-500 text-white p-4 rounded-2xl rounded-tr-sm shadow-md text-sm leading-relaxed max-w-[90%] break-words whitespace-pre-wrap">'.str_replace(array("\r\n", "\r", "\n"), "<br/>",htmlspecialchars($content[2])).'</div>
                                </div>
                                <img src="'.$myimg.'" class="w-10 h-10 rounded-full border-2 border-white shadow-sm shrink-0 bg-white">
                              </div>';
            	    }
            	}
            }
            ?>
        </div>

        <!-- 底部交互控制台 -->
        <div class="px-4 py-4 bg-white border-t border-slate-100 shrink-0">
            <?php if($rows['status']==0){ ?>
                <div class="text-center p-3 bg-amber-50 text-amber-600 rounded-xl text-sm font-bold border border-amber-100"><i class="fa fa-coffee mr-2"></i>案宗已挂起，静候总台指派干员响应...</div>
            <?php }elseif($rows['status']==2){ ?>
                <div class="text-center p-3 bg-slate-50 text-slate-500 rounded-xl text-sm font-bold border border-slate-200"><i class="fa fa-lock mr-2"></i>卷宗已封存打入冷宫，通信信道已断开。</div>
            <?php }elseif($rows['status']==1){ ?>
                <form action="./workorder.php?my=reply&id=<?php echo $id?>" method="POST" class="flex items-end gap-3">
                    <div class="flex-1 relative">
                        <textarea name="content" rows="3" class="w-full p-3 pl-4 pr-12 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 transition-colors leading-relaxed placeholder:font-normal placeholder:text-slate-400 resize-none scrollbar-thin" placeholder="输入补充证言，再次唤醒通信链路..." required></textarea>
                        <i class="fa fa-pencil absolute right-4 top-4 text-slate-300 pointer-events-none"></i>
                    </div>
                    <div class="flex flex-col gap-2 shrink-0">
                        <button type="submit" name="submit" class="h-10 px-5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all active:scale-95"><i class="fa fa-reply mr-1.5"></i>提交回击</button>
                        <button type="button" onclick="window.location.href='./workorder.php?my=complete&id=<?php echo $id?>'" class="h-10 px-5 rounded-lg bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 shadow-md shadow-emerald-500/20 transition-all active:scale-95"><i class="fa fa-check-circle mr-1.5"></i>申请结案</button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
<?php
}
// ==========================================
// 逻辑处理分支：入库落盘
// ==========================================
elseif($my=='add_submit')
{
    $orderid=intval($_POST['orderid']);
    $type=intval($_POST['type']);
    $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
    $picurl=strip_tags(daddslashes($_POST['picurl']));
    $code = isset($_POST['code'])?$_POST['code']:null;
    
    // 图灵测试核验
    if (!$code || strtolower($code) != $_SESSION['vc_code']) {
    	unset($_SESSION['vc_code']);
    	showmsg('拦截：视觉图形密钥破译失败！');
    }
    
    if (empty($content)) {
    	showmsg('阻断：案情复盘不可为空！');
    } elseif ($orderid>0 && !$DB->getRow("SELECT id FROM pre_orders WHERE id='$orderid' AND userid='{$userrow['zid']}' LIMIT 1") && md5($orderid.SYS_KEY.$orderid)!==$_POST['skey']) {
    	showmsg('越权：您只能向自己辖区内的订单发起调查令！');
    } elseif ($orderid>0 && $DB->getRow("SELECT id FROM pre_workorder WHERE orderid='$orderid' AND status<2 ORDER BY id DESC LIMIT 1")) {
    	showmsg('冗余阻断：该标的物已有挂起的调查令，切勿重复轰炸引擎！');
    } else {
        $sql="INSERT INTO `pre_workorder` (`zid`,`type`,`orderid`,`content`,`picurl`,`addtime`,`status`) VALUES (:zid, :type, :orderid, :content, :picurl, NOW(), 0)";
        $data = [':zid'=>$userrow['zid'], ':type'=>$type, ':orderid'=>$orderid, ':content'=>$content, ':picurl'=>$picurl];
        
        if($DB->exec($sql, $data)){
        	$id = $DB->lastInsertId();
        	if($conf['message_workorder']==1){
        		\lib\MessageSend::workorder_new($id, $userrow['user'], $userrow['zid'], display_type($type), $content);
        	}
        	unset($_SESSION['vc_code']);
        	showmsg('数据已发往总台。工单序列生成成功！请耐心静候反馈。<br/><br/><a href="./workorder.php" class="text-indigo-600 font-bold hover:underline">>> 点此返回队列面板</a>',1);
        }else{
        	showmsg('引擎崩溃：指令入库失败！'.$DB->error(),4);
        }
    }
}
elseif($my=='reply')
{
    $id=intval($_GET['id']);
    $rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND zid='{$userrow['zid']}' LIMIT 1");
    if(!$rows) showmsg('查无此案卷！',3);
    elseif($rows['status']==2) showmsg('此案已在宗卷室封底沉睡，无法再次启封！',3);
    elseif($rows['status']==0) showmsg('总台尚未下发一审意见，请勿强行干扰信道！',3);
    
    $content=str_replace(array('*','^','|'),'',trim(strip_tags(daddslashes($_POST['content']))));
    if (empty($content)) {
    	showmsg('阻断：补充口径不得为空！');
    } else {
        $contents = addslashes($rows['content']).'*0^'.$date.'^'.$content;
        if($DB->exec("update pre_workorder set content=:content,status=0 where id=:id", [':content'=>$contents, ':id'=>$id])!==false){
        	if($conf['message_workorder']==1){
        		\lib\MessageSend::workorder_reply($id, $userrow['user'], $userrow['zid'], display_type($rows['type']), $content);
        	}
        	showmsg('反击信道投递成功！已重新挂起该案宗。<br/><br/><a href="./workorder.php" class="text-indigo-600 font-bold hover:underline">>> 返回主控台</a>',1);
        }else{
        	showmsg('信道破损：数据回滚！'.$DB->error(),4);
        }
    }
}
elseif($my=='complete')
{
    $id=intval($_GET['id']);
    $rows=$DB->getRow("SELECT * FROM pre_workorder WHERE id='$id' AND zid='{$userrow['zid']}' LIMIT 1");
    if(!$rows) showmsg('查无此案卷！',3);
    elseif($rows['status']==2) showmsg('此案已在宗卷室封底沉睡！',3);
    
    if($DB->exec("UPDATE pre_workorder SET status=2 WHERE id='{$id}'")!==false)
    	exit("<script language='javascript'>layer.msg('撤卷成功！此通信链路已被切断', {icon:1, time:1500}, function(){history.go(-1);});</script>");
    else
    	showmsg('封卷失败：'.$DB->error(),4);
}
elseif($my=='delete')
{
    $id=intval($_GET['id']);
    $sql="DELETE FROM pre_workorder WHERE id='$id' AND zid='{$userrow['zid']}'";
    if($DB->exec($sql)!==false)
    	exit("<script language='javascript'>layer.msg('该案宗已被您物理焚毁！', {icon:1, time:1500}, function(){history.go(-1);});</script>");
    else
    	showmsg('销毁中断：'.$DB->error(),4);
}
// ==========================================
// 视图分支：主工单列表与仪表盘
// ==========================================
else
{
?>
    <!-- 头部看板卡片 -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-100 rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-reply text-amber-500 mr-1.5"></i>总台已反馈 (待补充)</span>
                <span class="text-2xl font-mono font-bold text-amber-600 tracking-tight"><?php echo $count1?></span>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-sky-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-clock-o text-sky-500 mr-1.5"></i>队列待审中</span>
                <span class="text-2xl font-mono font-bold text-sky-600 tracking-tight"><?php echo $count2?></span>
            </div>
        </div>
        <div class="bg-white border border-slate-100 rounded-2xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-slate-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10 flex flex-col">
                <span class="text-slate-500 text-xs font-bold mb-1 flex items-center"><i class="fa fa-database text-slate-500 mr-1.5"></i>全域总案卷</span>
                <span class="text-2xl font-mono font-bold text-slate-700 tracking-tight"><?php echo $count3?></span>
            </div>
        </div>
    </div>

    <!-- 表单列表区 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa fa-reorder text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">我的通信阵列</h3>
                </div>
            </div>
            <a href="./workorder.php?my=add" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-500/20 active:scale-95"><i class="fa fa-plus-circle mr-1.5"></i>起草并发起申诉</a>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-20">档案库(ID)</th>
                        <th class="px-5 py-4 font-semibold">维度归类</th>
                        <th class="px-5 py-4 font-semibold">标的物锚点(订单号)</th>
                        <th class="px-5 py-4 font-semibold">案情切片预览</th>
                        <th class="px-5 py-4 font-semibold text-center">系统判定态</th>
                        <th class="px-5 py-4 font-semibold text-center">初始立案时</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">终端接驳</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
    $numrows=$DB->getColumn("SELECT count(*) from pre_workorder WHERE zid='{$userrow['zid']}'");
    $pagesize=30;
    $pages=intval($numrows/$pagesize);
    if ($numrows%$pagesize) $pages++;
    $page=isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset=$pagesize*($page - 1);

    $rs=$DB->query("SELECT * FROM pre_workorder WHERE zid='{$userrow['zid']}' ORDER BY id DESC LIMIT $offset,$pagesize");
    $hasData = false;
    
    while($res = $rs->fetch())
    {
        $hasData = true;
        $content=explode('*',$res['content']);
        $content=mb_substr($content[0], 0, 16, 'utf-8') . (mb_strlen($content[0], 'utf-8') > 16 ? '...' : '');

        echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-5 py-3.5 text-center">
                    <span class="font-mono text-slate-400 font-bold text-[11px]">#'.$res['id'].'</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2 py-1 rounded">'.display_type($res['type']).'</span>
                </td>
                <td class="px-5 py-3.5 font-mono font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
                    '.($res['orderid'] ? '<a href="javascript:showOrder('.$res['orderid'].',\''.md5($res['orderid'].SYS_KEY.$res['orderid']).'\')" title="展开侧写面板"><i class="fa fa-link mr-1"></i>'.$res['orderid'].'</a>' : '<span class="text-slate-400">无挂载</span>').'
                </td>
                <td class="px-5 py-3.5 font-medium text-slate-600 max-w-[200px] truncate" title="点击切入信道">
                    <a href="./workorder.php?my=view&id='.$res['id'].'" class="hover:text-indigo-600 transition-colors">'.htmlspecialchars($content).'</a>
                </td>
                <td class="px-5 py-3.5 text-center">
                    '.display_status($res['status']).'
                </td>
                <td class="px-5 py-3.5 text-center font-mono text-[11px] text-slate-400">
                    '.$res['addtime'].'
                </td>
                <td class="px-5 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="./workorder.php?my=view&id='.$res['id'].'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white transition-colors shadow-sm" title="连通信道"><i class="fa fa-comments-o"></i></a>
                        <a href="javascript:void(0)" onclick="delWorkorder('.$res['id'].')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors shadow-sm" title="物理抹除"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
              </tr>';
    }
    
    if(!$hasData){
        echo '<tr><td colspan="7" class="px-6 py-16 text-center text-slate-400">
                <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">深空静谧：您当前尚无任何流转中的案宗轨迹</p>
              </td></tr>';
    }
?>
                </tbody>
            </table>
        </div>

        <!-- 分页系统包裹器 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
                <?php
                $first = 1;
                $prev  = $page - 1;
                $next  = $page + 1;
                $last  = $pages;
                $link = ''; // 如果需要额外的 URL 参数拼接，保留占位
                
                if ($page > 1) {
                    echo '<a href="workorder.php?page=' . $first . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="workorder.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                for ($i = 1; $i < $page; $i++) {
                    echo '<a href="workorder.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                $s = ($pages >= 10) ? 10 : $pages;
                for ($i = $page + 1; $i <= $s; $i++) {
                    echo '<a href="workorder.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if ($page < $pages) {
                    echo '<a href="workorder.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="workorder.php?page=' . $last . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>

    </div>
<?php } ?>

</div>

<?php include './foot.php';?>

<script>
// 图床唤醒
function fileSelect(){
	$("#file").trigger("click");
}

// 案情光栅图预览
function fileView(){
	var picurl = $("#picurl").val();
	if(picurl=='') return;
	if(picurl.indexOf('http') == -1) picurl = '../'+picurl;
	layer.open({
		type: 1,
		title: false,
		closeBtn: 0,
		skin: 'layui-layer-nobg',
		shadeClose: true,
		content: '<img src="'+picurl+'" class="max-w-[90vw] max-h-[90vh] rounded-2xl shadow-2xl">'
	});
}

// 图片异步挂载执行流
function fileUpload(){
	var fileObj = $("#file")[0].files[0];
	if (typeof (fileObj) == "undefined" || fileObj.size <= 0) return;
	
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
				layer.msg('光栅图挂载至云端', {icon:1});
				$("#picurl").val(data.url);
			}else{
				layer.alert(data.msg, {icon:2});
			}
		},
		error:function(data){
		    layer.close(ii);
			layer.msg('存储介质异常，链路崩溃', {icon:2});
		}
	})
}

// 侧写提取订单微核
function showOrder(id,skey){
	if(id==0)return false;
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	var statusHtml = [
	    '<span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 font-bold border border-sky-100 text-[11px]">列队待排产</span>',
	    '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold border border-emerald-100 text-[11px]">总成封包完毕</span>',
	    '<span class="px-2 py-0.5 rounded bg-amber-50 text-amber-600 font-bold border border-amber-100 text-[11px]">底层作业执行中</span>',
	    '<span class="px-2 py-0.5 rounded bg-rose-50 text-rose-600 font-bold border border-rose-100 text-[11px]">接口反馈异样</span>',
	    '<span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-bold border border-slate-200 text-[11px]">结算熔断与退回</span>'
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
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">标的物流水</th><td class="px-4 py-3 font-mono font-bold text-indigo-600">#'+id+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">标的名称</th><td class="px-4 py-3 font-bold text-slate-700">'+data.name+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">沉没结算额</th><td class="px-4 py-3 font-mono font-bold text-rose-500">¥ '+data.money+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">刻录时空</th><td class="px-4 py-3 font-mono text-xs text-slate-400">'+data.date+'</td></tr>';
				item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">核心载荷集</th><td class="px-4 py-3 font-mono text-xs text-slate-500 break-all">'+data.inputs+'</td></tr>';
				item += '<tr><th class="w-24 px-4 py-3 bg-slate-50 font-bold text-slate-500 text-right">全域执行态</th><td class="px-4 py-3">'+statusHtml[data.status]+'</td></tr>';
				
				if(data.list && data.list.order_state){
				    item += '<tr class="border-t border-slate-200 bg-amber-50/30"><th colspan="2" class="px-4 py-2 font-bold text-amber-700 text-center text-xs tracking-widest"><i class="fa fa-dashboard mr-1"></i>接口回传作业切片</th></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">目标配额</th><td class="px-4 py-2 font-mono font-bold text-slate-700">'+data.list.num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">初值锚点</th><td class="px-4 py-2 font-mono text-slate-500">'+data.list.start_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">现值游标</th><td class="px-4 py-2 font-mono text-emerald-600 font-bold">'+data.list.now_num+'</td></tr>';
				    item += '<tr class="border-b border-slate-50"><th class="w-24 px-4 py-2 bg-slate-50 font-bold text-slate-500 text-right">引擎答复</th><td class="px-4 py-2 font-bold text-indigo-600">'+data.list.order_state+'</td></tr>';
				}else if(data.kminfo){
				    item += '<tr class="border-t border-slate-200 bg-sky-50/30"><th colspan="2" class="px-4 py-2 font-bold text-sky-700 text-center text-xs tracking-widest"><i class="fa fa-ticket mr-1"></i>机密卡密分发提取域</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 font-mono text-xs text-slate-600 leading-relaxed break-all bg-sky-50/10">'+data.kminfo+'</td></tr>';
				}else if(data.result){
				    item += '<tr class="border-t border-slate-200 bg-slate-50/30"><th colspan="2" class="px-4 py-2 font-bold text-slate-600 text-center text-xs tracking-widest"><i class="fa fa-info-circle mr-1"></i>接口附加馈回释义</th></tr>';
				    item += '<tr><td colspan="2" class="px-4 py-3 text-xs text-slate-500 leading-relaxed">'+data.result+'</td></tr>';
				}
				
				item += '</tbody></table></div></div>';
				
				layer.open({
				  type: 1,
				  title: '<div class="font-bold text-slate-800"><i class="fa fa-archive text-indigo-500 mr-2"></i>标的物全域侧写追踪</div>',
				  area: [$(window).width() > 500 ? '500px' : '95%', 'auto'],
				  skin: 'layui-layer-rim rounded-2xl',
				  shadeClose: true,
				  content: item
				});
			}else{
				layer.alert(data.msg, {icon: 2});
			}
		}
	});
}

// 物理抹除二次确认
function delWorkorder(id) {
    layer.confirm('抹除警报：此指令将连根拔除该案宗的所有挂载卷宗通信，确定要清洗此轨迹吗？', {
        icon: 3, title: '轨迹焚毁序列', btn: ['确认清洗指令', '脱离']
    }, function(index){
        layer.close(index);
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        window.location.href = './workorder.php?my=delete&id=' + id;
    });
}
</script>
</body>
</html>
