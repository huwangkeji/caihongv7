<?php
include("../includes/common.php");
$title='自助增加域名';
include './head.php';
if($islogin2==1){}else exit("<script>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 px-4 sm:px-0 mt-4">

<?php
if($userrow['power']==0){
	showmsg('权限阻断：您当前没有权限使用此高阶功能！',3);
}

if(!empty($userrow['domain2']))showmsg('限制阻断：您的网站已经绑定过额外的备用域名，无法重复叠加！',3);

$price = $conf['fenzhan_editd'];

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
		showmsg('无效操作：新增的备用域名不能和当前主域名一模一样！',3);
	} elseif ($DB->getRow("SELECT zid FROM pre_site WHERE domain=:domain OR domain2=:domain LIMIT 1", [':domain'=>$domain]) || $qz=='www' || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode(',',$conf['fenzhan_remain']))) {
		showmsg('冲突：此前缀太火爆，已被其他用户占用或被总台保留！',3);
	}
	if($price>$userrow['rmb'])exit("<script language='javascript'>layer.msg('您的可用余额不足，请先前往充值！', {icon:2, time:2000}, function(){window.location.href='./recharge.php';});</script>");
	
	$DB->exec("UPDATE `pre_site` SET `domain2`=:domain,`rmb`=`rmb`-:rmb WHERE `zid`=:zid", [':domain'=>$domain, ':rmb'=>$price, ':zid'=>$userrow['zid']]);
	addPointRecord($userrow['zid'], $price, '消费', '自助增加域名');
	exit("<script language='javascript'>layer.msg('成功绑定备用域名 {$domain}，本次操作共扣除 {$price} 元！', {icon:1, time:2000}, function(){window.location.href='uset.php?mod=site';});</script>");
}
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mr-3 shrink-0">
                <i class="fa fa-plus-circle text-lg"></i>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">自助增加域名</h3>
                <p class="text-xs text-slate-500 m-0 mt-1">为主站增加一个备用的访问域名，实现双域名运行</p>
            </div>
        </div>

        <div class="p-6">
            <!-- 指向 ndomain.php 的提交路由 -->
            <form action="./ndomain.php?act=submit" method="post" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">申请创建的全新前缀 <span class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all">
                        <input type="text" onkeyup="value=value.replace(/[^\w\.\/]/ig,'')" name="qz" class="w-1/2 h-11 px-4 text-sm bg-white border-0 focus:ring-0 font-mono text-indigo-600 font-bold placeholder:text-slate-300 placeholder:font-normal text-right" required data-parsley-length="[2,8]" placeholder="请输入前缀">
                        <div class="h-11 flex items-center justify-center text-slate-400 bg-white font-mono font-bold">.</div>
                        <select name="domain" class="w-1/2 h-11 px-3 text-sm bg-slate-50 border-0 border-l border-slate-200 focus:ring-0 cursor-pointer font-mono font-bold text-slate-700">
                            <?php echo $select?>
                        </select>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1"><i class="fa fa-info-circle mr-1"></i>前缀长度需控制在 2-10 字符之间，仅限小写字母与数字组合。</p>
                </div>

                <!-- 费用结算区 -->
                <div class="space-y-1.5 p-5 bg-rose-50/50 border border-rose-100 rounded-xl flex items-center justify-between">
                    <div>
                        <div class="text-sm font-semibold text-rose-700 mb-0.5">本次耗费</div>
                        <div class="text-[11px] text-rose-500">将从您的账户可用余额中直扣</div>
                    </div>
                    <div class="flex items-end gap-1">
                        <span class="text-sm font-bold text-rose-500 pb-1">¥</span>
                        <span class="text-3xl font-bold font-mono text-rose-600 tracking-tight leading-none"><?php echo $price?></span>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="submit" class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-plug mr-1.5"></i>确认支付并执行增加
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include './foot.php';?>
