<?php
include("../includes/common.php");
$title='自助更换域名';
include './head.php';
if($islogin2==1){}else exit("<script>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if($userrow['power']==0){
	showmsg('权限阻断：您当前没有权限使用此高阶功能！',3);
}
if($conf['fenzhan_editd']==0)showmsg('配置锁定：总台未开启自助更换域名功能',3);

$price = $conf['fenzhan_editd'];
$opt = isset($_GET['opt'])?intval($_GET['opt']):0;

$domains=explode(',',$conf['fenzhan_domain']);
$select='';
foreach($domains as $domain){
	$select.='<option value="'.$domain.'">'.$domain.'</option>';
}
if(empty($select))showmsg('系统未就绪：请联系总台管理员到后台分站设置中，填写可选的分站域名池',3);

if($_GET['act']=='submit'){
	if(!checkRefererHost())exit();
	$qz = trim(strtolower(daddslashes($_POST['qz'])));
	$domain = trim(strtolower(htmlspecialchars(strip_tags(daddslashes($_POST['domain'])))));
	$domain = $qz . '.' . $domain;
	if (strlen($qz) < 2 || strlen($qz) > 10 || !preg_match('/^[a-z0-9\-]+$/',$qz)) {
		showmsg('阻断：域名前缀格式不合格，请检查长度或非法字符！',3);
	} elseif (!preg_match('/^[a-zA-Z0-9\_\-\.]+$/',$domain)) {
		showmsg('阻断：总域名格式验证不通过！',3);
	} elseif ($domain == $userrow['domain']) {
		showmsg('无效操作：新域名不能和当前正在使用的域名一模一样！',3);
	} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE domain=:domain OR domain2=:domain LIMIT 1", [':domain'=>$domain]) || $qz=='www' || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode(',',$conf['fenzhan_remain']))) {
		showmsg('冲突：此前缀太火爆，已被其他用户占用或被总台保留！',3);
	}
	if($price>$userrow['rmb'])exit("<script language='javascript'>layer.msg('您的可用余额不足，请先前往充值！', {icon:2, time:2000}, function(){window.location.href='./recharge.php';});</script>");
	
	if($opt==1){
		$column = 'domain2';
	}else{
		$column = 'domain';
	}
	$DB->exec("UPDATE `pre_site` SET `{$column}`=:domain,`rmb`=`rmb`-:rmb WHERE `zid`=:zid", [':domain'=>$domain, ':rmb'=>$price, ':zid'=>$userrow['zid']]);
	addPointRecord($userrow['zid'], $price, '消费', '自助更换域名');
	exit("<script language='javascript'>layer.msg('成功将您的域更换为 {$domain}，本次操作共扣除 {$price} 元！', {icon:1, time:2000}, function(){window.location.href='uset.php?mod=site';});</script>");
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0">
                <i class="fa fa-exchange text-lg"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">自助更换域名</h3>
                <p class="text-xs text-slate-500 m-0 mt-1">您可以自定义配置您的专属后缀域名，同时系统支持双轨绑定 2 个域名</p>
            </div>
        </div>

        <div class="p-6">
            
            <!-- 胶囊导航栏：代替原本粗糙的 h4 a 标签 -->
            <div class="flex p-1 bg-slate-100 rounded-xl mb-6 shadow-inner max-w-sm mx-auto">
                <a href="cdomain.php" class="flex-1 py-2 text-center rounded-lg text-sm transition-all duration-200 <?php echo $opt==0 ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-700'; ?>">
                    主域名更换 (链路①)
                </a>
                <a href="cdomain.php?opt=1" class="flex-1 py-2 text-center rounded-lg text-sm transition-all duration-200 <?php echo $opt==1 ? 'bg-white text-indigo-600 font-bold shadow-sm' : 'text-slate-500 font-medium hover:text-slate-700'; ?>">
                    备用域更换 (链路②)
                </a>
            </div>

            <form action="./cdomain.php?act=submit&opt=<?php echo $opt?>" method="post" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">当前正在服役的域名</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-globe"></i></div>
                        <input name="domain" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed font-bold" value="<?php echo $opt==1?$userrow['domain2']:$userrow['domain']?>" disabled/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">申请挂载的新域名 <span class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                        <input type="text" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" name="qz" class="w-1/2 h-11 px-4 text-sm bg-white border-0 focus:ring-0 font-mono text-indigo-600 font-bold placeholder:text-slate-300 placeholder:font-normal text-right" required data-parsley-length="[2,8]" placeholder="请输入前缀">
                        <div class="h-11 flex items-center justify-center text-slate-400 bg-white font-mono font-bold">.</div>
                        <select name="domain" class="w-1/2 h-11 px-3 text-sm bg-slate-50 border-0 border-l border-slate-200 focus:ring-0 cursor-pointer font-mono font-bold text-slate-700">
                            <?php echo $select?>
                        </select>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1"><i class="fa fa-info-circle mr-1"></i>前缀长度需控制在 2-10 字符之间，仅限小写字母与数字组合。</p>
                </div>

                <div class="space-y-1.5 p-5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-rose-700 mb-0.5">本次更名手续费</div>
                        <div class="text-[11px] text-rose-500">将从您的账户可用余额中直扣</div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-sm font-bold text-rose-500 pb-1">¥</span>
                        <span class="text-3xl font-bold font-mono text-rose-600 tracking-tight leading-none"><?php echo $price?></span>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="submit" class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check-circle-o mr-1.5"></i>确认支付并执行换绑
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>

<?php include './foot.php';?>
