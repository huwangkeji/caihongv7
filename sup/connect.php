<?php
/**
 * 快捷登录
**/
$is_defend=true;
include("../includes/common.php");
if(!$conf['login_qq'] && !$conf['login_wx'])sysmsg('当前站点未开启QQ或微信快捷登录');
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	@header('Content-Type: application/json; charset=UTF-8');
	if(isset($_SESSION['findpwd_qq']) && $qq=$_SESSION['findpwd_qq']){
		$row=$DB->getRow("SELECT sid,user,pwd,status FROM pre_supplier WHERE qq=:qq LIMIT 1", [':qq'=>$qq]);
		unset($_SESSION['findpwd_qq']);
		if($row['user']){
			if($row['status']==0){
				exit('{"code":-1,"msg":"当前账号已被封禁！"}');
			}
			$session=md5($row['user'].$row['pwd'].$password_hash);
			$token=authcode("{$row['sid']}\t{$session}", 'ENCODE', SYS_KEY);
			setcookie("sup_token", $token, time() + 604800, '/');
			log_result('分站登录', 'User:'.$row['user'].' IP:'.$clientip, null, 1);
			$DB->exec("UPDATE pre_supplier SET lasttime='$date' WHERE sid='{$row['sid']}'");
			exit('{"code":1,"msg":"登陆用户中心成功！","url":"./"}');
		}else{
			exit('{"code":-1,"msg":"当前QQ不存在，请确认你已注册过本站账号"}');
		}
	}else{
		exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
	}
}elseif(($conf['login_qq']==1 || $conf['login_wx']==1) && $_GET['code'] && $_GET['type']){
	if($_GET['state'] != $_SESSION['Oauth_state']){
		sysmsg("<h2>The state does not match. You may be a victim of CSRF.</h2>");
	}
	$type = $_GET['type'];
	$Oauth = new \lib\Oauth($conf['login_apiurl'], $conf['login_appid'], $conf['login_appkey']);
	$arr = $Oauth->callback();
	if(isset($arr['code']) && $arr['code']==0){
		$openid=$arr['social_uid'];
		$access_token=$arr['access_token'];
		$nickname=$arr['nickname'];
		$faceimg=$arr['faceimg'];
	}elseif(isset($arr['code'])){
		sysmsg('<h3>error:</h3>'.$arr['errcode'].'<h3>msg  :</h3>'.$arr['msg']);
	}else{
		sysmsg('获取登录数据失败');
	}
	if($type=='wx'){
		$typename = '微信';
		$typecolumn = 'wx_openid';
	}else{
		$typename = 'QQ';
		$typecolumn = 'qq_openid';
	}

	$row=$DB->getRow("SELECT * FROM pre_supplier WHERE {$typecolumn}='{$openid}' limit 1");
	if($row){
		$user=$row['user'];
		$pass=$row['pwd'];
		if($islogin3==1){
			@header('Content-Type: text/html; charset=UTF-8');
			exit("<script language='javascript'>layer.msg('当前{$typename}已绑定用户:{$user}，请勿重复绑定！', {icon:2, time:2000}, function(){window.location.href='./uset.php?mod=user';});</script>");
		}
		$session=md5($user.$pass.$password_hash);
		$token=authcode("{$row['sid']}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("sup_token", $token, time() + 604800, '/');
		log_result('分站登录', 'User:'.$user.' IP:'.$clientip, null, 1);
		$DB->exec("UPDATE pre_supplier SET lasttime='$date' WHERE sid='{$row['sid']}'");
		if(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='index')$redirect = '../';
		elseif(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='recharge')$redirect = './recharge.php';
		elseif(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='workorder')$redirect = './workorder.php';
		else $redirect = './';
		exit("<script language='javascript'>window.location.href='{$redirect}';</script>");
	}elseif($islogin3==1){
		$sds=$DB->exec("update `pre_supplier` set `{$typecolumn}`='$openid' where `sid`='{$suprow['sid']}'");
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>layer.msg('已成功绑定{$typename}！', {icon:1, time:2000}, function(){window.location.href='./uset.php?mod=user';});</script>");
	}else{
		$_SESSION['Oauth_qq_type']=$type;
		$_SESSION['Oauth_qq_openid']=$openid;
		$_SESSION['Oauth_qq_token']=$access_token;
		$_SESSION['Oauth_qq_nickname']=$nickname;
		$_SESSION['Oauth_qq_faceimg']=$faceimg;
		@header('Content-Type: text/html; charset=UTF-8');
		if($_SESSION['Oauth_back'])$addstr = '&back='.$_SESSION['Oauth_back'];
		exit("<script language='javascript'>window.location.href='./connect.php?type={$type}{$addstr}';</script>");
	}
}elseif($islogin3==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>layer.msg('您已登陆！', {icon:1, time:2000}, function(){window.location.href='./';});</script>");
}elseif($conf['login_qq']!=2 && (!$_SESSION['Oauth_qq_openid'] || !$_SESSION['Oauth_qq_token'])){
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$type = $_GET['type'];

if($type=='wx'){
	$typename = '微信';
}elseif($type=='qq'){
	$typename = 'QQ';
}else{
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$title=$typename.'快捷登录';
include './head2.php';

if($_SESSION['Oauth_qq_faceimg']){
	$faceimg = $_SESSION['Oauth_qq_faceimg'];
}else{
	$faceimg = '/assets/img/user.png';
}

if($_GET['act'] == 'new'){
	$subtitle = '请完善以下信息';
}elseif($_GET['act'] == 'bind'){
	$subtitle = '绑定已有账号';
}else{
	$subtitle = $typename.'快捷登录';
}

if($_SESSION['Oauth_back'])$addstr = '&back='.$_SESSION['Oauth_back'];
?>

<?php if($background_image){?>
<img src="<?php echo $background_image;?>" alt="Full Background" class="fixed inset-0 w-full h-full object-cover z-[-1] animate-pulse">
<?php }else{ ?>
<div class="fixed inset-0 w-full h-full bg-slate-50 z-[-1]"></div>
<?php } ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-[400px]">
        
        <!-- 卡片主体 -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden relative">
            
            <!-- 顶部装饰背景 -->
            <div class="h-32 bg-cover bg-center relative" style="background-image: url('<?php echo $cdnserver?>assets/simple/img/head3.jpg');">
                <div class="absolute inset-0 bg-black/20"></div>
                <div class="absolute -bottom-10 inset-x-0 flex justify-center">
                    <img class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover bg-white" src="<?php echo $faceimg?>" alt="Avatar" />
                </div>
            </div>
            
            <div class="px-6 pt-14 pb-8">
                
                <!-- 标题与返回 -->
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                        <i class="fa fa-user text-indigo-500 mr-2 text-xl"></i><?php echo $subtitle?>
                    </h2>
                    <a href="../" class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 font-bold text-xs hover:bg-slate-100 transition-colors shadow-sm">
                        返回首页
                    </a>
                </div>

                <!-- 扫码登录区 -->
                <?php if($type=='qq' && $conf['login_qq']==2){?>
                    <div id="loginframe" class="space-y-6">
                        <div class="flex flex-col items-center">
                            <div class="bg-sky-50 border border-sky-100 px-4 py-2.5 rounded-xl text-sky-800 font-bold text-sm mb-6 flex items-center shadow-sm" id="login">
                                <span id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" class="text-rose-500 ml-2 animate-pulse">.</span>
                            </div>
                            
                            <div id="qrimg" class="p-3 bg-white border border-slate-200 shadow-sm rounded-2xl mb-4">
                                <!-- 二维码注入区 -->
                            </div>
                            
                            <div id="mobile" class="w-full space-y-3" style="display:none;">
                                <button type="button" id="mlogin" onclick="mloginurl()" class="w-full h-11 rounded-xl bg-amber-500 text-white font-bold text-sm shadow-md shadow-amber-500/20 hover:bg-amber-600 transition-all active:scale-95">跳转QQ快捷登录</button>
                                <button type="button" onclick="qrlogin()" class="w-full h-11 rounded-xl bg-emerald-500 text-white font-bold text-sm shadow-md shadow-emerald-500/20 hover:bg-emerald-600 transition-all active:scale-95">我已完成登录</button>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                            <a href="login.php" class="flex items-center justify-center h-10 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm hover:bg-indigo-100 transition-colors">
                                <i class="fa fa-user mr-1.5"></i>返回登录
                            </a>
                            <a href="reg.php" class="flex items-center justify-center h-10 rounded-xl bg-rose-50 text-rose-500 font-bold text-sm hover:bg-rose-100 transition-colors">
                                <i class="fa fa-user-plus mr-1.5"></i>注册用户
                            </a>
                        </div>
                    </div>
                
                <!-- 账号密码绑定区 -->
                <?php }elseif($_GET['act'] == 'bind'){?>
                    <form id="loginframe" class="space-y-5">
                        <div class="space-y-1.5">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="glyphicon glyphicon-user"></i></div>
                                <input type="text" name="user" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="用户名"/>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="glyphicon glyphicon-lock"></i></div>
                                <input type="password" name="pass" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="密码"/>
                            </div>
                        </div>
                        
                        <?php if($conf['captcha_open_login']==1 && $conf['captcha_open']>=1){?>
                        <input type="hidden" name="captcha_type" value="<?php echo $conf['captcha_open']?>"/>
                        <?php if($conf['captcha_open']>=2){?><input type="hidden" name="appid" value="<?php echo $conf['captcha_id']?>"/><?php }?>
                        <div id="captcha" class="flex flex-col items-center justify-center bg-slate-50 border border-slate-200 rounded-xl p-4 min-h-[80px]">
                            <div id="captcha_text" class="text-sm font-bold text-slate-500">正在加载验证码</div>
                            <div id="captcha_wait" class="mt-2">
                                <i class="fa fa-spinner fa-spin text-xl text-indigo-500"></i>
                            </div>
                        </div>
                        <div id="captchaform"></div>
                        <?php }?>
                        
                        <div class="pt-2">
                            <input type="button" value="立即绑定账号" id="submit_login" class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all cursor-pointer"/>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                            <a href="javascript:history.back(-1)" class="flex items-center justify-center h-10 rounded-xl bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors">
                                <i class="fa fa-reply mr-1.5"></i>返回
                            </a>
                            <a href="findpwd.php" class="flex items-center justify-center h-10 rounded-xl bg-sky-50 text-sky-600 font-bold text-sm hover:bg-sky-100 transition-colors">
                                <i class="fa fa-unlock mr-1.5"></i>找回密码
                            </a>
                        </div>
                    </form>
                
                <!-- 选择授权类型区 -->
                <?php }else{?>
                    <div id="loginframe" class="space-y-5">
                        <a href="javascript:quickreg('<?php echo $type?>')" class="flex items-center justify-center w-full h-12 rounded-xl bg-emerald-50 text-emerald-600 font-bold text-[15px] border border-emerald-100 hover:bg-emerald-500 hover:text-white shadow-sm transition-all active:scale-[0.98]">
                            <i class="fa fa-user-plus mr-2"></i>我是新用户，直接登录
                        </a>
                        
                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                            <div class="relative flex justify-center"><span class="bg-white px-4 text-xs font-medium text-slate-400">或者</span></div>
                        </div>
                        
                        <a href="connect.php?act=bind&type=<?php echo $type.$addstr?>" class="flex items-center justify-center w-full h-12 rounded-xl bg-slate-50 text-slate-600 font-bold text-[15px] border border-slate-200 hover:bg-slate-100 shadow-sm transition-all active:scale-[0.98]">
                            <i class="fa fa-user-circle-o mr-2"></i>我是老用户，绑定已有账号
                        </a>
                    </div>
                <?php }?>
                
            </div>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>

<?php if($type=='qq' && $conf['login_qq']==2){?>
<script>
var islogin=1;
</script>
<script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"></script>
<?php }else{?>
<script src="../assets/js/login.js?ver=<?php echo VERSION ?>"></script>
<?php }?>
</body>
</html>
