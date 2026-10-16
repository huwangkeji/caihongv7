<?php
/**
 * 扫码找回密码通行凭证
**/
$is_defend=true;
include("../includes/common.php");

// 路由分支：轮询验签与权限下发
if(isset($_GET['act']) && $_GET['act']=='qrlogin'){
	if(isset($_SESSION['findpwd_qq']) && $qq=$_SESSION['findpwd_qq']){
		$row=$DB->getRow("SELECT zid,user,pwd,status FROM pre_site WHERE qq=:qq LIMIT 1", [':qq'=>$qq]);
		unset($_SESSION['findpwd_qq']);
		if($row['user']){
			if($row['status']==0){
				exit('{"code":-1,"msg":"授权阻断：当前请求的账号已被系统封禁限制！"}');
			}
			$session=md5($row['user'].$row['pwd'].$password_hash);
			$token=authcode("{$row['zid']}\t{$session}", 'ENCODE', SYS_KEY);
			setcookie("user_token", $token, time() + 604800, '/');
			log_result('分站找回密码', 'User:'.$row['user'].' IP:'.$clientip, null, 1);
			$DB->exec("UPDATE pre_site SET lasttime='$date' WHERE zid='{$row['zid']}'");
			exit('{"code":1,"msg":"握手成功，请在控制台个人资料页重置您的安全密码","url":"./"}');
		}else{
			@header('Content-Type: application/json; charset=UTF-8');
			exit('{"code":-1,"msg":"寻址失败：当前扫码的QQ不存在，请确认您已通过该QQ开通或注册过平台"}');
		}
	}else{
		@header('Content-Type: application/json; charset=UTF-8');
        exit('{"code":-2,"msg":"验证过期或失败，请刷新二维码重新获取指令"}');
	}
}elseif($islogin2==1){
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('鉴权：您当前已处于登录态，无需找回密码！');window.location.href='./';</script>");
}

$title='找回密码';
include './head.php';
?>

<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-sky-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute top-1/4 -right-24 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-24 left-1/3 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-pulse" style="animation-delay: 4s;"></div>
    </div>

    <div class="max-w-md w-full space-y-8 bg-white/80 backdrop-blur-xl p-8 sm:p-10 rounded-[2rem] shadow-2xl border border-white/60 relative z-10">
        
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-sky-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-sky-500/30 mb-6 transform -rotate-6 hover:rotate-0 transition-transform duration-300">
                <i class="fa fa-unlock-alt text-white text-3xl"></i>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight">找回通行凭证</h2>
            <p class="mt-2 text-sm text-slate-500 font-medium">使用您注册时绑定的 QQ 手机版扫描下方安全码进行身份验签</p>
        </div>

        <div class="mt-8 space-y-6">
            
            <!-- 核心交互区：二维码扫描 -->
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50/60 border border-slate-100 rounded-2xl shadow-inner">
                <div class="text-sm font-bold text-sky-700 mb-4 flex items-center">
                    <i class="fa fa-qrcode mr-2"></i>
                    <span id="loginmsg">正在生成安全加密通讯码</span>
                    <span id="loginload" class="animate-pulse ml-1 text-sky-500">...</span>
                </div>
                
                <!-- 二维码挂载插槽 -->
                <div id="qrimg" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 min-h-[160px] min-w-[160px] flex items-center justify-center relative overflow-hidden">
                    <!-- 骨架屏加载过渡效果 -->
                    <div class="absolute inset-0 flex items-center justify-center bg-slate-50/90 z-0 transition-opacity duration-300" id="qr-spinner">
                        <i class="fa fa-circle-o-notch fa-spin text-sky-500 text-3xl"></i>
                    </div>
                </div>
                
                <button type="button" onclick="qrlogin()" class="mt-6 w-full flex items-center justify-center px-4 py-3 border border-transparent text-[15px] font-bold rounded-xl text-white bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 shadow-md shadow-emerald-500/20 transition-all duration-200 active:scale-[0.98]">
                    <i class="fa fa-check-circle mr-2"></i>我已在手机端授权确认
                </button>
            </div>

            <?php if($conf['login_qq']==1){?>
            <!-- 寻回限制策略通告 -->
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-[13px] text-amber-800 font-medium leading-relaxed m-0">
                            <strong class="block mb-1 text-amber-900">寻回范围受限说明：</strong>
                            该通道仅支持找回注册时明确绑定了实体 QQ 号码的账户。通过「QQ快捷登录」直接生成且未补充资料的账户暂不支持此法寻回。
                        </p>
                    </div>
                </div>
            </div>
            <?php }?>

            <!-- 底部分流链接 -->
            <div class="flex items-center justify-center gap-4 pt-4 border-t border-slate-100">
                <a href="login.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors flex items-center">
                    <i class="fa fa-arrow-left mr-1.5"></i>退回常规登录
                </a>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <a href="reg.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors flex items-center">
                    前往注册开户<i class="fa fa-arrow-right ml-1.5"></i>
                </a>
            </div>
            
        </div>
    </div>
</div>

<script>
// UI 增强：监听二维码插入动作，隐去骨架屏加载圈
document.addEventListener("DOMContentLoaded", function() {
    const qrContainer = document.getElementById('qrimg');
    const spinner = document.getElementById('qr-spinner');
    
    if(qrContainer && spinner) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    spinner.style.opacity = '0';
                    setTimeout(() => spinner.style.display = 'none', 300);
                }
            });
        });
        observer.observe(qrContainer, { childList: true });
    }
});
</script>

<!-- 注入负责通信轮询的专属钩子 -->
<script src="../assets/js/qrlogin.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
