<?php
/**
 * 开通分站/建站成功反馈页
**/
$is_defend=true;
include("../includes/common.php");
$title='开通分站成功';

include './head.php';
?>

<div class="w-full max-w-[650px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-8">

<?php
// 核心数据解析逻辑 100% 保留
if(isset($_GET['orderid'])){
	$orderid = daddslashes($_GET['orderid']);
	$row=$DB->getRow("SELECT * FROM pre_pay WHERE trade_no='{$orderid}' LIMIT 1");
	if(!$row || $row['status']==0 || $row['tid']!=-2)showmsg('核验阻断：订单不存在或尚未完成支付流程！',3);
	if(!$cookiesid || $row['userid']!=$cookiesid)showmsg('越权阻断：仅限查看自己名下开通的分站资产信息',3);
	
	$input=explode('|',$row['input']);
	$type = $input[0];
	if($type == 'update'){
		$zid = intval($input[1]);
		$row=$DB->getRow("SELECT * FROM pre_site WHERE zid='{$zid}' LIMIT 1");
		$kind = intval($row['power']);
		$domain = $row['domain'];
		$user = $row['user'];
		$pwd = $row['pwd'];
		$name = $row['sitename'];
		$qq = $row['qq'];
		$endtime = $row['endtime'];
	}else{
		$kind = intval($input[1]);
		$domain = daddslashes($input[2]);
		$user = daddslashes($input[3]);
		$pwd = daddslashes($input[4]);
		$name = daddslashes($input[5]);
		$qq = daddslashes($input[6]);
		$endtime = daddslashes($input[7]);
	}
	$sitepath = str_replace('/user','',$sitepath);
	$url = 'http://'.$domain.$sitepath.'/';
	
}elseif(isset($_GET['zid'])){
	$zid = intval($_GET['zid']);
	$row=$DB->getRow("SELECT * FROM pre_site WHERE zid='{$zid}' LIMIT 1");
	if(!$row || !$_SESSION['newzid'] || $_SESSION['newzid']!=$zid)showmsg('校验失败：您所查询的分站档案不存在或已失效！',3);
	
	$kind = intval($row['power']);
	$domain = $row['domain'];
	$user = $row['user'];
	$pwd = $row['pwd'];
	$name = $row['sitename'];
	$qq = $row['qq'];
	$endtime = $row['endtime'];
	$sitepath = str_replace('/user','',$sitepath);
	$url = 'http://'.$domain.$sitepath.'/';
	
}else{
	showmsg('缺少追踪参数，无法定位资产信息',4);
}
?>

    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden relative">
        
        <!-- 成功状态头部 -->
        <div class="bg-gradient-to-br from-emerald-400 to-teal-600 p-8 text-center relative overflow-hidden">
            <!-- 背景光晕点缀 -->
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-white/20 rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-xl"></div>
            
            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/50 mb-5 relative z-10 transform transition-transform hover:scale-110 duration-300">
                <i class="fa fa-check text-4xl text-emerald-500"></i>
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-bold text-white relative z-10 tracking-tight m-0 mb-2">分站部署成功</h2>
            <p class="text-emerald-50 text-sm font-medium relative z-10 m-0">恭喜您，您的专属站点已成功激活，请务必妥善保管以下核心资产凭证</p>
        </div>

        <!-- 凭证展板内容区 -->
        <div class="p-6 sm:p-8 space-y-6">
            
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 shadow-inner space-y-4">
                
                <!-- 站点入口 -->
                <div class="space-y-1.5 border-b border-slate-200 border-dashed pb-4">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><i class="fa fa-globe mr-1.5 text-indigo-400"></i>您的分站首页地址</span>
                    <a href="<?php echo $url?>" target="_blank" class="block text-indigo-600 font-mono font-bold text-[15px] hover:text-indigo-800 transition-colors break-all">
                        <?php echo $url?>
                    </a>
                </div>

                <!-- 后台入口 -->
                <div class="space-y-1.5 border-b border-slate-200 border-dashed pb-4">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><i class="fa fa-cogs mr-1.5 text-amber-500"></i>分站独立管理后台</span>
                    <a href="<?php echo $url?>user/" target="_blank" class="block text-amber-600 font-mono font-bold text-[15px] hover:text-amber-800 transition-colors break-all">
                        <?php echo $url?>user/
                    </a>
                </div>

                <!-- 管理员账号 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><i class="fa fa-user mr-1.5 text-slate-400"></i>管理账号</span>
                        <div class="bg-white border border-slate-200 px-3 py-2 rounded-lg text-slate-700 font-mono font-bold">
                            <?php echo htmlspecialchars($user)?>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><i class="fa fa-lock mr-1.5 text-slate-400"></i>管理密码</span>
                        <div class="bg-white border border-slate-200 px-3 py-2 rounded-lg text-slate-700 font-mono font-bold">
                            <?php echo htmlspecialchars($pwd)?>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 操作分流栏 -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <a href="/user" target="_blank" class="flex-1 h-12 flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-bold text-[15px] hover:from-emerald-600 hover:to-teal-700 shadow-md shadow-emerald-500/20 transition-all active:scale-95">
                    <i class="fa fa-sign-in mr-1.5"></i>前往后台配置站点
                </a>
                <a href="../" class="w-full sm:w-1/3 h-12 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                    返回总台主页
                </a>
            </div>

            <!-- 安全警示 -->
            <div class="text-center mt-6">
                <p class="text-xs text-rose-500 font-medium">
                    <i class="fa fa-shield mr-1"></i>温馨提示：请截图保存此页！
                </p>
            </div>

        </div>
    </div>
</div>

<?php include './foot.php';?>
</body>
</html>
