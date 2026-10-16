<?php
/**
 * 注册
**/
$is_defend=true;
include("../includes/common.php");
if($islogin3==1) {
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('您已登录！');window.location.href='./';</script>");
}
if($conf['sup_reg']==0) {
	@header('Content-Type: text/html; charset=UTF-8');
	exit("<script language='javascript'>alert('暂不开放供货商注册！');window.location.href='./login.php';</script>");
}
$title='供货商注册';

// 生成hashsalt
if($conf['verify_open']==1){
	$_SESSION['addsalt'] = $addsalt = random(32);
}

@header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title><?php echo $title ?></title>
    
    <!-- 引入 Tailwind CSS 核心引擎 -->
    <script src="../assets/V7/tailwindcss.js"></script>
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    
    <style>
        /* 屏蔽原生高亮，提升触控体验 */
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans selection:bg-indigo-100 selection:text-indigo-700 min-h-screen relative flex items-center justify-center p-4 overflow-x-hidden">

    <!-- 全局背景层 -->
    <?php if($conf['background_image_enable'] == 1 && $conf['background_type'] == 'image'){ ?>
        <div class="fixed inset-0 z-0 bg-cover bg-center transition-all duration-700" style="background-image: url('../assets/img/bj.png'); filter: brightness(0.9);"></div>
    <?php } else { ?>
        <div class="fixed inset-0 z-0 bg-slate-50"></div>
    <?php } ?>

    <!-- 电脑端：返回首页悬浮按钮 -->
    <a href="../" class="hidden lg:flex absolute top-8 left-8 items-center px-5 py-2.5 bg-white/80 backdrop-blur-md border border-white rounded-xl shadow-sm text-slate-600 font-bold hover:bg-white hover:text-indigo-600 hover:shadow-md hover:-translate-y-0.5 transition-all z-20">
        <i class="fa fa-angle-left mr-2 text-lg"></i>返回首页
    </a>

    <!-- 注册卡片主体 -->
    <div class="relative z-10 w-full max-w-[420px] bg-white/90 backdrop-blur-xl border border-white/60 rounded-[28px] shadow-2xl p-8 sm:p-10 transition-all my-8">
        
        <!-- Logo 与 标题区 -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight mb-2">供货商入驻</h1>
            <p class="text-sm font-medium text-slate-500">欢迎加入我们，成为核心供应商</p>
        </div>
        
        <form class="space-y-5">
            <!-- 账号输入 -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-user"></i>
                </div>
                <input type="text" name="user" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-white/80 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="请设置登录账号">
            </div>
            
            <!-- 密码输入 -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-lock"></i>
                </div>
                <input type="password" name="pass" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-white/80 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="请设置登录密码">
            </div>
            
            <!-- QQ号输入 -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <i class="fa fa-qq"></i>
                </div>
                <input type="text" name="qq" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-white/80 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="请输入真实QQ号码">
            </div>
            
            <?php if($conf['verify_open']==1){?>
                <input type="hidden" name="hashsalt" value="<?php echo $addsalt?>"/>
            <?php }?>
            
            <!-- 简单图形验证码 (当未开启第三方滑块验证时) -->
            <?php if(!$conf['captcha_open'] || $conf['captcha_open']>3){?>
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                        <i class="fa fa-shield"></i>
                    </div>
                    <input type="text" name="code" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-white/80 focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm transition-all placeholder:font-normal placeholder:text-slate-400" required placeholder="输入验证码">
                </div>
                <img src="./code.php" onclick="this.src='./code.php?'+Math.random()" class="w-28 h-12 rounded-xl border border-slate-200 shadow-sm cursor-pointer hover:opacity-80 transition-opacity" title="点击刷新验证码"/>
            </div>
            <?php }?>
            
            <!-- 第三方/系统高级验证码容器 -->
            <?php if($conf['captcha_open']>=1){?>
                <input type="hidden" name="captcha_type" value="<?php echo $conf['captcha_open']?>"/>
                <?php if($conf['captcha_open']>=2){?><input type="hidden" name="appid" value="<?php echo $conf['captcha_id']?>"/><?php }?>
                <div id="captcha" class="flex flex-col items-center justify-center bg-white/60 border border-slate-200 rounded-xl p-4 min-h-[80px] shadow-sm backdrop-blur-sm">
                    <div id="captcha_text" class="text-sm font-bold text-slate-500 flex items-center gap-2">
                        正在加载验证组件 <i class="fa fa-spinner fa-spin text-indigo-500"></i>
                    </div>
                    <div id="captchaform" class="mt-2"></div>
                </div>
            <?php }?>
            
            <!-- 注册按钮 -->
            <div class="pt-2">
                <button type="button" id="submit_reg" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold text-[15px] shadow-md shadow-indigo-500/30 hover:from-indigo-600 hover:to-sky-600 hover:shadow-lg active:scale-[0.98] transition-all">
                    立即注册入驻
                </button>
            </div>
            
            <!-- 附属操作链接 -->
            <div class="flex justify-between items-center pt-2">
                <a href="findpwd.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 hover:underline transition-colors">找回密码</a>
                <a href="login.php" class="text-sm font-bold text-indigo-500 hover:text-indigo-600 hover:underline transition-colors">已有账号，去登录</a>
            </div>
            
            <!-- 底部版权/协议 -->
            <?php if($conf['forceloginhome']==1){ 
                echo '<div class="pt-4 border-t border-slate-100/50 mt-4"><p class="text-center text-xs font-medium text-slate-400 leading-relaxed">'.$conf['footer'].'</p></div>';
            }?>
        </form>
        
        <!-- 移动端：底部返回首页 -->
        <div class="mt-8 text-center lg:hidden border-t border-slate-100 pt-4">
            <a href="../" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                <i class="fa fa-angle-left mr-1.5 text-lg"></i>返回首页
            </a>
        </div>

    </div>
    
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script src="../assets/js/reguser.js?ver=<?php echo VERSION ?>"></script>
</body>
</html>
