<?php
/**
 * 快捷登录/Oauth 授权
**/
$is_defend=true;
include("../includes/common.php");
if(!$conf['login_qq'] && !$conf['login_wx'])sysmsg('安全拦截：当前站点未开启QQ或微信快捷登录通道');

// 路由分支一：QR 扫码验签回调
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	@header('Content-Type: application/json; charset=UTF-8');
	if(isset($_SESSION['findpwd_qq']) && $qq=$_SESSION['findpwd_qq']){
		$row=$DB->getRow("SELECT zid,user,pwd,status FROM pre_site WHERE qq=:qq LIMIT 1", [':qq'=>$qq]);
		unset($_SESSION['findpwd_qq']);
		if($row['user']){
			if($row['status']==0){
				exit('{"code":-1,"msg":"授权驳回：当前绑定的实体账号已被系统封禁！"}');
			}
			$session=md5($row['user'].$row['pwd'].$password_hash);
			$token=authcode("{$row['zid']}\t{$session}", 'ENCODE', SYS_KEY);
			setcookie("user_token", $token, time() + 604800, '/');
			log_result('分站登录', 'User:'.$row['user'].' IP:'.$clientip, null, 1);
			$DB->exec("UPDATE pre_site SET lasttime='$date' WHERE zid='{$row['zid']}'");
			exit('{"code":1,"msg":"信道握手成功，即将驶入用户中心！","url":"./"}');
		}else{
			exit('{"code":-1,"msg":"寻址失败：当前扫码的QQ不存在，请确认您已用此QQ注册过本站"}');
		}
	}else{
		exit('{"code":-2,"msg":"签名失效：验证凭据过期，请刷新二维码重新扫码"}');
	}
}

// 路由分支二：Oauth 跨域回调数据提取
elseif(($conf['login_qq']==1 || $conf['login_wx']==1) && $_GET['code'] && $_GET['type']){
	if($_GET['state'] != $_SESSION['Oauth_state']){
		sysmsg("<h2>The state does not match. You may be a victim of CSRF. (状态防伪校验失败)</h2>");
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
		sysmsg('<h3>核心异常：</h3>'.$arr['errcode'].'<h3>附加信息：</h3>'.$arr['msg']);
	}else{
		sysmsg('握手断裂：获取第三方跨站登录数据失败');
	}
	
	if($type=='wx'){
		$typename = '微信';
		$typecolumn = 'wx_openid';
	}else{
		$typename = 'QQ';
		$typecolumn = 'qq_openid';
	}

	$row=$DB->getRow("SELECT * FROM pre_site WHERE {$typecolumn}='{$openid}' limit 1");
	if($row){
		$user=$row['user'];
		$pass=$row['pwd'];
		if($islogin2==1){
			@header('Content-Type: text/html; charset=UTF-8');
			exit("<script language='javascript'>alert('安全冲突：当前获取的 {$typename} 凭据已绑定平台账号 [{$user}]，请勿交叉重复绑定！');window.location.href='./uset.php?mod=user';</script>");
		}
		$session=md5($user.$pass.$password_hash);
		$token=authcode("{$row['zid']}\t{$session}", 'ENCODE', SYS_KEY);
		ob_clean();
		setcookie("user_token", $token, time() + 604800, '/');
		log_result('分站登录', 'User:'.$user.' IP:'.$clientip, null, 1);
		$DB->exec("UPDATE pre_site SET lasttime='$date' WHERE zid='{$row['zid']}'");
		
		if(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='index')$redirect = '../';
		elseif(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='recharge')$redirect = './recharge.php';
		elseif(isset($_SESSION['Oauth_back']) && $_SESSION['Oauth_back']=='workorder')$redirect = './workorder.php';
		else $redirect = './';
		
		exit("<script language='javascript'>window.location.href='{$redirect}';</script>");
	}elseif($islogin2==1){
		$sds=$DB->exec("update `pre_site` set `{$typecolumn}`='$openid' where `zid`='{$userrow['zid']}'");
		@header('Content-Type: text/html; charset=UTF-8');
		exit("<script language='javascript'>alert('数据打通：已成功绑定 {$typename} 渠道！');window.location.href='./uset.php?mod=user';</script>");
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
}

// 路由分支三：登录态阻断与跳转过滤
elseif($islogin2==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('身份已确认，无需重复拉取登录！');window.location.href='./';</script>");
}elseif($conf['login_qq']!=2 && (!$_SESSION['Oauth_qq_openid'] || !$_SESSION['Oauth_qq_token'])){
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// ==== 以下进入前端视图层 ====
$type = $_GET['type'];
if($type=='wx'){
	$typename = '微信';
}elseif($type=='qq'){
	$typename = 'QQ';
}else{
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

$title = $typename.'授权快捷通道';

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8" />
  <title><?php echo $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script src="../assets/V7/tailwindcss.js"></script>
  <style>
    .loading-dot { display: inline-block; width: 6px; height: 6px; margin: 0 2px; border-radius: 50%; background-color: #6366f1; animation: loading 1.4s infinite ease-in-out both; }
    .loading-dot:nth-child(1) { animation-delay: -0.32s; }
    .loading-dot:nth-child(2) { animation-delay: -0.16s; }
    @keyframes loading { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
  </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center relative antialiased selection:bg-brand-200 selection:text-brand-900 px-4">

<?php 
if($_SESSION['Oauth_qq_faceimg']){
	$faceimg = $_SESSION['Oauth_qq_faceimg'];
}else{
	$faceimg = '/assets/img/user.png';
}

if($_GET['act'] == 'new'){
	$subtitle = '请完善底层户籍信息';
}elseif($_GET['act'] == 'bind'){
	$subtitle = '链路绑定已有老账户';
}else{
	$subtitle = $typename.'通道验证';
}

if($_SESSION['Oauth_back'])$addstr = '&back='.$_SESSION['Oauth_back'];
?>

<?php if($background_image){?>
<div class="fixed inset-0 z-0">
    <img src="<?php echo $background_image;?>" alt="Full Background" class="w-full h-full object-cover animate-pulse" ondragstart="return false;" oncontextmenu="return false;">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
</div>
<?php }?>

<div class="w-full max-w-[400px] bg-white/95 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden relative z-10 border border-white/40 transform transition-all hover:scale-[1.01]">
    
    <!-- 头部横幅与用户提取像 -->
    <div class="bg-gradient-to-r from-brand-600 to-indigo-600 h-32 relative">
        <!-- 半圆弧过渡 -->
        <div class="absolute -bottom-8 left-0 right-0 h-16 bg-white/95 rounded-t-[100%]"></div>
        <div class="absolute -bottom-6 left-1/2 -translate-x-1/2">
            <img src="<?php echo $faceimg?>" class="w-20 h-20 rounded-full border-4 border-white shadow-md bg-white object-cover" alt="Oauth Avatar" />
        </div>
    </div>

    <div class="px-8 pt-8 pb-8">
        
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-slate-800 m-0 tracking-tight"><?php echo $subtitle?></h2>
            <p class="text-xs text-slate-500 mt-2 font-medium">请确认授权信息的准确性</p>
        </div>

        <div id="loginframe">
            
            <?php if($type=='qq' && $conf['login_qq']==2){?>
            <!-- 情景 1：原生手机 QQ 扫码模式 -->
            <div class="space-y-6 text-center">
                <div class="bg-sky-50 border border-sky-100 rounded-xl p-3 shadow-sm" id="login">
                    <span id="loginmsg" class="text-sm font-bold text-sky-700">正在生成通讯二维码...</span>
                    <span id="loginload" class="text-sky-500 font-bold ml-1 animate-pulse">...</span>
                </div>
                
                <div id="qrimg" class="flex justify-center bg-white p-2 rounded-xl shadow-sm border border-slate-100 mx-auto w-fit">
                    <!-- 二维码图像会被 qrlogin.js 注入至此 -->
                </div>
                
                <div class="space-y-3" id="mobile" style="display:none;">
                    <button type="button" id="mlogin" onclick="mloginurl()" class="w-full h-11 rounded-xl bg-amber-500 text-white font-bold hover:bg-amber-600 transition-colors shadow-sm">
                        <i class="fa fa-external-link mr-1"></i> 跳转手机QQ客户端授权
                    </button>
                    <button type="button" onclick="qrlogin()" class="w-full h-11 rounded-xl bg-emerald-500 text-white font-bold hover:bg-emerald-600 transition-colors shadow-sm">
                        我已在手机端点击确认
                    </button>
                </div>
                
                <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                    <a href="login.php" class="flex-1 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors flex items-center justify-center font-bold text-sm"><i class="fa fa-reply mr-1"></i>常规登录</a>
                    <a href="reg.php" class="flex-1 h-10 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-100 transition-colors flex items-center justify-center font-bold text-sm">注册开户</a>
                </div>
            </div>

            <?php }elseif($_GET['act'] == 'bind'){?>
            <form class="space-y-5">
                <div class="space-y-1.5">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-user"></i></div>
                        <input type="text" name="user" class="w-full h-12 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" required placeholder="请输入主站用户名" />
                    </div>
                </div>
                <div class="space-y-1.5">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-lock"></i></div>
                        <input type="password" name="pass" class="w-full h-12 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" required placeholder="请输入登录安全密码" />
                    </div>
                </div>

                <!-- 验证码预留插槽 -->
                <?php if($conf['captcha_open_login']==1 && $conf['captcha_open']>=1){?>
                <input type="hidden" name="captcha_type" value="<?php echo $conf['captcha_open']?>"/>
                <?php if($conf['captcha_open']>=2){?><input type="hidden" name="appid" value="<?php echo $conf['captcha_id']?>"/><?php }?>
                <div id="captcha" class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex justify-center items-center h-12 shadow-inner">
                    <div id="captcha_text" class="text-xs font-bold text-slate-400 mr-2">人机盾层载入中</div>
                    <div id="captcha_wait">
                        <div class="loading">
                            <div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div>
                        </div>
                    </div>
                </div>
                <div id="captchaform"></div>
                <?php }?>
                
                <div class="pt-2">
                    <button type="button" id="submit_login" class="w-full h-12 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-600 text-white font-bold text-[15px] hover:from-brand-700 hover:to-indigo-700 shadow-md shadow-brand-500/20 transition-all duration-200 active:scale-[0.98]">
                        执行账户融合与绑定
                    </button>
                </div>
                
                <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
                    <a href="javascript:history.back(-1)" class="flex-1 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors flex items-center justify-center font-bold text-sm"><i class="fa fa-angle-left mr-1"></i>中止退回</a>
                    <a href="findpwd.php" class="flex-1 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-100 transition-colors flex items-center justify-center font-bold text-sm">寻回密码</a>
                </div>
            </form>

            <?php }else{?>
            <!-- 情景 3：Oauth 新旧身份选择交叉口 -->
            <div class="space-y-4">
                <a href="javascript:quickreg('<?php echo $type?>')" class="flex flex-col items-center justify-center py-4 rounded-xl bg-gradient-to-br from-brand-50 to-indigo-50 border border-brand-100 text-brand-700 hover:shadow-md transition-all active:scale-[0.98] group">
                    <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa fa-user-plus text-lg"></i>
                    </div>
                    <span class="font-bold text-sm">我是新客户，创建档案直入</span>
                </a>
                
                <a href="connect.php?act=bind&type=<?php echo $type.$addstr?>" class="flex flex-col items-center justify-center py-4 rounded-xl bg-slate-50 border border-slate-100 text-slate-600 hover:shadow-md transition-all active:scale-[0.98] group">
                    <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 mb-2 group-hover:scale-110 transition-transform">
                        <i class="fa fa-link text-lg"></i>
                    </div>
                    <span class="font-bold text-sm">我是老顾客，合并现有账号</span>
                </a>
                
                <div class="pt-4 text-center">
                    <a href="../" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors underline">暂不处理，撤回主站</a>
                </div>
            </div>
            <?php }?>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>

<?php if($type=='qq' && $conf['login_qq']==2){?>
<script> var islogin=1; </script>
<script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"></script>
<?php }else{?>
<script src="../assets/js/login.js?ver=<?php echo VERSION ?>"></script>
<?php }?>

</body>
</html>
