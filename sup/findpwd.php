<?php
/**
 * 找回密码
**/
$is_defend=true;
include("../includes/common.php");
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
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
			log_result('分站找回密码', 'User:'.$row['user'].' IP:'.$clientip, null, 1);
			$DB->exec("UPDATE pre_supplier SET lasttime='$date' WHERE sid='{$row['sid']}'");
			exit('{"code":1,"msg":"登录成功，请在用户资料设置里重置密码","url":"./"}');
		}else{
			@header('Content-Type: application/json; charset=UTF-8');
			exit('{"code":-1,"msg":"当前QQ不存在，请确认你已注册过账号或开通过分站"}');
		}
	}else{
		@header('Content-Type: application/json; charset=UTF-8');
		exit('{"code":-2,"msg":"验证失败，请重新扫码"}');
	}
}elseif($islogin3==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登陆！');window.location.href='./';</script>");
}
$title='找回密码';
include './head2.php';
?>

<?php if($background_image){?>
<img src="<?php echo $background_image;?>" alt="Full Background" class="fixed inset-0 w-full h-full object-cover z-[-1] animate-pulse">
<?php }else{ ?>
<div class="fixed inset-0 w-full h-full bg-slate-50 z-[-1]"></div>
<?php } ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-[400px]">
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden relative">
            
            <div class="px-6 pt-8 pb-8">
                
                <div class="flex items-center justify-between mb-8 border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                        <i class="fa fa-unlock text-indigo-500 mr-2 text-xl"></i><b>找回密码</b>
                    </h2>
                    <a href="../" class="px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 font-bold text-xs hover:bg-slate-100 transition-colors shadow-sm">
                        返回首页
                    </a>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col items-center">
                        <div class="bg-sky-50 border border-sky-100 px-4 py-2.5 rounded-xl text-sky-800 font-bold text-sm mb-6 flex items-center shadow-sm" id="login">
                            <span id="loginmsg">请使用QQ手机版扫描二维码</span><span id="loginload" class="text-rose-500 ml-2 animate-pulse">.</span>
                        </div>
                        
                        <div id="qrimg" class="p-3 bg-white border border-slate-200 shadow-sm rounded-2xl mb-4 min-h-[150px] flex items-center justify-center">
                            
                        </div>
                        
                        <button type="button" onclick="qrlogin()" class="w-full h-12 rounded-xl bg-emerald-500 text-white font-bold text-[15px] shadow-md shadow-emerald-500/20 hover:bg-emerald-600 transition-all active:scale-[0.98]">我已完成登录</button>
                    </div>
                    
                    <?php if($conf['login_qq']==1){?>
                    <div class="p-3 bg-sky-50 border border-sky-100 rounded-xl text-xs text-sky-700 font-medium leading-relaxed">
                        提示：只能找回注册时填写了QQ号码的帐号密码，QQ快捷登录的暂不持支该方式找回密码。
                    </div>
                    <?php }?>
                    
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                        <a href="login.php" class="flex items-center justify-center h-11 rounded-xl bg-indigo-50 text-indigo-600 font-bold text-sm hover:bg-indigo-100 transition-colors shadow-sm">
                            <i class="fa fa-user mr-1.5"></i>返回登录
                        </a>
                        <a href="reg.php" class="flex items-center justify-center h-11 rounded-xl bg-rose-50 text-rose-500 font-bold text-sm hover:bg-rose-100 transition-colors shadow-sm">
                            <i class="fa fa-user-plus mr-1.5"></i>注册用户
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="../assets/V7/tailwindcss.js"></script>
<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
