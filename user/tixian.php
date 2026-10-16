<?php
/**
 * 资产金库结转与余额提现
**/
include("../includes/common.php");
$title='余额提现';
include './head.php';

// 登录态防泄漏鉴权
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
// 系统核心级与权限级阻断
if($conf['fenzhan_tixian']==0) showmsg('通道管控：当前站点暂未开放资产提现与结转通道！');
if($userrow['power']==0){
	showmsg('权限阻断：您尚未开通合伙人权限，无法使用财务提取功能！',3);
}

function display_zt($zt,$id){
	if($zt==2)
		return '<a onclick="getResult('.$id.')" class="cursor-pointer inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm hover:bg-rose-500 hover:text-white transition-colors"><i class="fa fa-exclamation-circle mr-1"></i>驳回(点此看因)</a>';
	elseif($zt==1)
		return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm"><i class="fa fa-check mr-1"></i>下发成功</span>';
	else
		return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-600 border border-amber-100 shadow-sm"><i class="fa fa-hourglass-half mr-1"></i>队列审核中</span>';
}

function display_type($type){
	if($type==1) return '微信钱包';
	elseif($type==2) return 'QQ钱包';
	else return '支付宝账段';
}


if(isset($_POST['money']))
{
    if(!checkRefererHost()) exit();
    $money=daddslashes(strip_tags($_POST['money']));
    
    if(!is_numeric($money) || !preg_match('/^[0-9.]+$/', $money)) showmsg('入参异常：提现金额需为合法的数字流',3);
    $realmoney = round($money*$conf['tixian_rate']/100,2);
    
    if($conf['fenzhan_skimg']==1 && !file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png')){
    	exit("<script language='javascript'>layer.msg('拦截：您还未上传收款二维码！', {icon:2, time:2000}, function(){window.location.href='uset.php?mod=skimg';});</script>");
    }elseif(empty($userrow['pay_account']) || empty($userrow['pay_name'])){
    	exit("<script language='javascript'>layer.msg('拦截：您尚未配配收款账号与实名档案！', {icon:2, time:2000}, function(){history.go(-1);});</script>");
    }
    
    $DB->beginTransaction();
    $userrow = $DB->getRow("SELECT * FROM pre_site WHERE zid='{$userrow['zid']}' LIMIT 1 FOR UPDATE");
    
    if($conf['tixian_limit']==1 && $money>$userrow['rmbtc'] || $money>$userrow['rmb'] || $money<=0){
    	exit("<script language='javascript'>layer.msg('资金阻断：您申请提现的额度已超过您可动用的金库余量！', {icon:2}); history.go(-1);</script>");
    }
    if($money<$conf['tixian_min']){
    	exit("<script language='javascript'>layer.msg('额度受限：单笔结算下限不能低于 {$conf['tixian_min']} 元！', {icon:2}); history.go(-1);</script>");
    }
    
    $sql = "INSERT INTO `pre_tixian` (`zid`, `money`, `realmoney`, `pay_type`, `pay_account`, `pay_name`, `status`, `addtime`) VALUES (:zid, :money, :realmoney, :pay_type, :pay_account, :pay_name, 0, NOW())";
    $data = [':zid'=>$userrow['zid'], ':money'=>$money, ':realmoney'=>$realmoney, ':pay_type'=>$userrow['pay_type'], ':pay_account'=>$userrow['pay_account'], ':pay_name'=>$userrow['pay_name']];
    
    if($DB->exec($sql, $data)){
    	if($DB->exec("UPDATE pre_site SET rmb=rmb-:money,rmbtc=rmbtc-:money WHERE zid=:zid", [':money'=>$money, ':zid'=>$userrow['zid']])){
    		addPointRecord($userrow['zid'], $money, '提现', '站点余额提现'.$money.'元');
    		$DB->commit();
    		exit("<script language='javascript'>layer.msg('资产已成功锁定并进入结转队列，实到额度:{$realmoney}元，请耐心等待总台人工打款！', {icon:1, time:2500}, function(){window.location.href='tixian.php';});</script>");
    	}else{
    		$DB->rollBack();
    		exit("<script>layer.alert('账本挂载失败，数据已回滚防泄漏！', {icon:2}); location.href='tixian.php';</script>");
    	}
    }else{
    	exit("<script language='javascript'>layer.msg('引擎异常，资产结转受阻！', {icon:2}); history.go(-1);</script>");
    }
}

$numrows=$DB->getColumn("SELECT count(*) FROM pre_tixian WHERE zid='{$userrow['zid']}'");
?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- 左侧：提现控制台 -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0 shadow-sm">
                        <i class="fa fa-money text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">资产金库结转</h3>
                        <p class="text-xs text-slate-500 m-0 mt-1">将您合法合规赚取的佣金结转至您的现实账户</p>
                    </div>
                </div>

                <div class="p-6">
                    <!-- 收款信息与收款图展板 -->
                    <div class="mb-5 p-4 rounded-xl border <?php echo (!empty($userrow['pay_account']) && !empty($userrow['pay_name']) || ($conf['fenzhan_skimg']==1 && file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png'))) ? 'bg-sky-50 border-sky-200' : 'bg-rose-50 border-rose-200' ?>">
                    	<?php if($conf['fenzhan_skimg']==1 && file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png')){?>
                    	    <div class="text-sm font-bold text-sky-800 mb-2 border-b border-sky-200 pb-2"><i class="fa fa-id-card-o mr-1.5"></i>已挂载结算通道 (带收款图)</div>
                    		<div class="flex items-start gap-4">
                    		    <img onclick="img('<?php echo $userrow['zid'] ?>')" src="<?php echo '../assets/img/skimg/sk_'.$userrow['zid'].'.png' ?>" class="w-20 h-20 object-cover rounded-lg border-2 border-white shadow-sm cursor-pointer hover:scale-105 transition-transform" title="点击放大查看">
                    		    <div class="flex flex-col gap-2 w-full">
                    		        <span class="text-xs font-bold text-sky-700 bg-sky-100 px-2 py-1 rounded inline-block w-fit">方式：<?php echo display_type($userrow['pay_type']); ?></span>
                    		        <div class="flex gap-2">
                    		            <a href="uset.php?mod=skimg" class="flex-1 text-center py-1.5 rounded bg-sky-500 text-white font-bold text-xs hover:bg-sky-600 transition-colors shadow-sm">更换收款图</a>
                    		            <a href="uset.php?mod=user" class="flex-1 text-center py-1.5 rounded bg-slate-100 text-slate-600 font-bold text-xs hover:bg-slate-200 transition-colors border border-slate-200 shadow-sm">切换通道</a>
                    		        </div>
                    		    </div>
                    		</div>
                    	<?php }elseif($conf['fenzhan_skimg']==1){?>
                    	    <div class="flex flex-col items-center justify-center py-2 text-rose-600">
                    	        <i class="fa fa-qrcode text-3xl mb-2 text-rose-400"></i>
                    		    <span class="text-sm font-bold mb-3">您的金库结算账号受阻，因未上传收款二维码！</span>
                    		    <a href="uset.php?mod=skimg" class="px-5 py-2 rounded-lg bg-rose-500 text-white font-bold text-sm shadow-md shadow-rose-500/30 hover:bg-rose-600 active:scale-95 transition-all">前往上传</a>
                    		</div>
                    	<?php }elseif(!empty($userrow['pay_account']) && !empty($userrow['pay_name'])){?>
                    	    <div class="text-sm font-bold text-sky-800 mb-2 border-b border-sky-200 pb-2 flex justify-between items-center">
                    	        <span><i class="fa fa-id-card-o mr-1.5"></i>已配置结算通道</span>
                    	        <a href="uset.php?mod=user" class="text-[11px] bg-sky-100 text-sky-600 px-2 py-1 rounded hover:bg-sky-200 transition-colors">修改</a>
                    	    </div>
                    		<div class="grid grid-cols-2 gap-2 text-sm text-sky-700">
                    		    <div class="flex flex-col"><span class="text-[10px] text-sky-500 uppercase tracking-wider">通道类别</span><span class="font-bold"><?php echo display_type($userrow['pay_type']); ?></span></div>
                    		    <div class="flex flex-col"><span class="text-[10px] text-sky-500 uppercase tracking-wider">打款实名</span><span class="font-bold"><?php echo htmlspecialchars($userrow['pay_name']); ?></span></div>
                    		    <div class="flex flex-col col-span-2"><span class="text-[10px] text-sky-500 uppercase tracking-wider">收款账号</span><span class="font-bold font-mono tracking-tight bg-white/50 px-2 py-1 rounded mt-0.5"><?php echo htmlspecialchars($userrow['pay_account']); ?></span></div>
                    		</div>
                    	<?php }else{?>
                    		<div class="flex flex-col items-center justify-center py-2 text-rose-600">
                    	        <i class="fa fa-id-card-o text-3xl mb-2 text-rose-400"></i>
                    		    <span class="text-sm font-bold mb-3">资产锁定：您尚未向系统报备收款账号！</span>
                    		    <a href="uset.php?mod=user" class="px-5 py-2 rounded-lg bg-rose-500 text-white font-bold text-sm shadow-md shadow-rose-500/30 hover:bg-rose-600 active:scale-95 transition-all">开启实名认证与绑定</a>
                    		</div>
                    	<?php }?>
                    </div>
                    
                    <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-3 rounded-r-xl shadow-sm flex items-start">
                        <i class="fa fa-exclamation-triangle text-amber-500 mt-0.5 mr-2"></i>
                        <p class="text-xs text-amber-800 font-medium m-0 leading-relaxed">
                            阈值控制：单笔下发流水额不得低于 <b class="text-rose-500"><?php echo $conf['tixian_min']; ?></b> 元。<br>
                            损耗说明：总台将扣除 <b class="text-rose-500"><?php echo (100-$conf['tixian_rate'])?>%</b> 作为结算服务费。
                        </p>
                    </div>

                    <!-- 提取表单 -->
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="action" value="tixian">
                        
                        <?php if($conf['tixian_limit']==1){
            			    $notmoney = $userrow['rmb']-$userrow['rmbtc'];
            			    if($notmoney<0)$notmoney=0;
            			    
            			    $enmoney = $userrow['rmbtc'];
            			    if($enmoney>$userrow['rmb'])$enmoney = $userrow['rmb'];
            			?>
            			<div class="grid grid-cols-2 gap-4">
            			    <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700 ml-1">消费余额 (禁止提现)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-bold"><i class="fa fa-lock"></i></div>
                                    <input type="text" class="w-full h-10 pl-8 pr-2 text-sm font-mono font-bold rounded-lg border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed" value="<?php echo number_format($notmoney, 2)?>" disabled>
                                </div>
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-slate-700 ml-1">可结转纯利润 (允许提现)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-500 font-bold"><i class="fa fa-check-circle"></i></div>
                                    <input type="text" name="tmoney" class="w-full h-10 pl-8 pr-2 text-sm font-mono font-bold rounded-lg border-emerald-200 bg-emerald-50/50 text-emerald-600 shadow-inner cursor-not-allowed" value="<?php echo number_format($enmoney, 2)?>" disabled>
                                </div>
                            </div>
            			</div>
            			<div class="text-[10px] text-slate-400 bg-slate-50 p-2 rounded border border-slate-100 leading-relaxed font-medium">
            			    <i class="fa fa-info-circle mr-1"></i>机制释义：您主动充值或系统签到补贴的额度被锁定在系统内，仅限用作铺货打单消费。只有网外真实客源买单产生的流水差价，方被认可为纯利润进行结转。
            			    <?php echo $conf['tixian_days']>0?'<br><i class="fa fa-clock-o mr-1"></i>账期安全期：订单下发 '.$conf['tixian_days'].' 天后，所获提成才会跨过冷冻期划入您的「可结转纯利润」槽位。':null;?>
            			</div>
            			
            			<?php }else{?>
            			
            			<div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">余额可结转额度</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="tmoney" class="w-full h-12 pl-10 pr-4 text-lg font-mono font-bold rounded-xl border-emerald-200 bg-emerald-50/50 text-emerald-600 shadow-inner cursor-not-allowed" value="<?php echo number_format($userrow['rmb'], 2)?>" disabled>
                            </div>
                        </div>
            			<?php }?>
            			
            			<div class="space-y-1.5 border-t border-slate-100 pt-4">
                            <label class="text-sm font-semibold text-slate-700 ml-1">结转金额 <span class="text-rose-500">*</span></label>
                            <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                                <div class="pl-4 flex items-center pointer-events-none text-indigo-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="money" class="flex-1 h-12 pl-2 pr-4 text-base font-mono font-bold border-0 focus:ring-0 text-indigo-600 placeholder:text-slate-300 placeholder:font-normal" placeholder="输入提取总额..." required>
                                <button type="button" onclick="inputMoney()" class="px-4 bg-slate-50 border-l border-slate-200 text-indigo-600 font-bold text-sm hover:bg-slate-100 transition-colors cursor-pointer active:scale-95 border-0">最大</button>
                            </div>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-700 active:scale-[0.98] transition-all">
                                <i class="fa fa-paper-plane mr-1.5"></i>申请下发
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- 右侧：流水与快照区 -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight flex items-center"><i class="fa fa-list-alt text-slate-400 mr-2 text-lg"></i>结算记录</h3>
                    <div class="text-xs font-mono text-slate-400"><i class="fa fa-database mr-1"></i>展示近 10 条流水</div>
                </div>
                
                <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 flex-1">
                    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap min-w-[700px]">
                        <thead class="text-[11px] text-slate-500 uppercase bg-slate-50/50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-center w-16">单号</th>
                                <th class="px-4 py-3 font-semibold text-center">结算额度</th>
                                <th class="px-4 py-3 font-semibold text-center">核销实到</th>
                                <th class="px-4 py-3 font-semibold">通道</th>
                                <th class="px-4 py-3 font-semibold text-center">时间</th>
                                <th class="px-4 py-3 font-semibold text-center w-24">状态</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                        <?php
                        $rs=$DB->query("SELECT * FROM pre_tixian WHERE zid='{$userrow['zid']}' ORDER BY id DESC LIMIT 10");
                        $hasData = false;
                        while($res = $rs->fetch())
                        {
                            $hasData = true;
                            
                            // 金额格式化
                            $applyMoney = number_format($res['money'], 2);
                            $realMoney = number_format($res['realmoney'], 2);
                            
                            echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-mono text-slate-400 font-bold text-[11px]">#'.$res['id'].'</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-slate-700">
                                        ¥ '.$applyMoney.'
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono font-bold text-emerald-600">
                                        ¥ '.$realMoney.'
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-col gap-0.5">
                                            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded w-fit">'.display_type($res['pay_type']).'</span>
                                            <span class="font-mono font-bold text-indigo-600 tracking-tight">'.htmlspecialchars($res['pay_account']).'</span>
                                            <span class="text-[10px] text-slate-400"><i class="fa fa-user-circle mr-1"></i>'.htmlspecialchars($res['pay_name']).'</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-[10px] font-mono text-slate-500" title="发起申请">↑ '.$res['addtime'].'</span>
                                            '.($res['status']==1?'<span class="text-[10px] font-mono text-emerald-500 mt-1" title="核销完成">↓ '.$res['endtime'].'</span>':null).'
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        '.display_zt($res['status'],$res['id']).'
                                    </td>
                                  </tr>';
                        }
                        
                        if(!$hasData){
                            echo '<tr><td colspan="6" class="px-4 py-16 text-center text-slate-400">
                                    <i class="fa fa-inbox text-4xl mb-3 text-slate-200 block"></i>
                                    <p class="text-sm font-medium text-slate-500">检测仪反馈：您的档案区尚无任何资金结转的历史痕迹</p>
                                  </td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include './foot.php';?>

<script>
// 一键填充 MAX 额度
function inputMoney(){
	var tmoneyStr = $("input[name='tmoney']").val();
	// 剔除字符串中的千位符、多余空格和 "元" 字符，精准提取浮点数
	var pureMoney = tmoneyStr.replace(/,/g, '').split(' ')[0];
	$("input[name='money']").val(pureMoney);
}

// 捕获并解析失败原因
function getResult(id) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_user.php?act=tixian_note',
		data : {id:id},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.result!=null) {
			    var msgHtml = '<div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-sm text-rose-700 font-medium leading-relaxed shadow-inner"><i class="fa fa-exclamation-triangle text-rose-500 mr-2 text-lg"></i>' + data.result + '</div>';
				layer.open({
				    type: 1,
				    title: '<div class="font-bold text-slate-800">总台审批驳回口径</div>',
				    area: [$(window).width() > 400 ? '400px' : '90%', 'auto'],
				    skin: 'layui-layer-rim rounded-2xl',
				    shadeClose: true,
				    btn: ['了解退回原委'],
				    btnAlign: 'c',
				    content: '<div class="p-5">' + msgHtml + '</div>'
				});
			} else {
				layer.alert('数据断片：由于远古记录归档，无法确切捕获退回根因，如有疑虑请直接电传总台站长。', {icon: 0, title:'记录缺失'});
			}
		},
		error:function(data){
		    layer.close(ii);
			layer.msg('与总台通信链路断裂', {icon: 2});
		}
	});
}

// 控制收款图阅览器 (如果您的 JS 生态需要定义该函数，这里做兼容)
function img(zid){
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        area: ['auto', 'auto'],
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: '<img src="../assets/img/skimg/sk_'+zid+'.png" class="max-w-[90vw] max-h-[90vh] rounded-xl shadow-2xl">'
    });
}
</script>
</body>
</html>
