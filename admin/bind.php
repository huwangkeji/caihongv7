<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 1);
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
// 原写法在缺少 type 参数时直接 exit，浏览器得到 0 字节纯白屏且无任何提示
$type = isset($_GET["type"]) ? trim($_GET["type"]) : '';
if (!in_array($type, array('qq', 'wx'), true)) {
	exit('<!DOCTYPE html><html lang="zh-cn"><head><meta charset="utf-8"/><title>参数错误</title></head>'
		. '<body style="font-family:Microsoft YaHei;padding:40px;color:#333">'
		. '<h3>参数错误</h3><p>缺少或无效的 type 参数，请使用：bind.php?type=qq 或 bind.php?type=wx</p>'
		. '<p><a href="javascript:history.go(-1)">返回上一页</a></p></body></html>');
}
$typename = $type == "qq" ? "QQ" : "微信";
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width">
  <meta name="renderer" content="webkit"/>
  <title>扫码登录 - <?php echo $typename;?></title>
  
  <!-- 引入 Tailwind CSS -->
  <script src="../assets/V7/tailwindcss.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { 50: '#eef2ff', 100: '#e0e7ff', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca' }
          }
        }
      }
    }
  </script>
  
  <link href="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  
  <!-- 针对 JS 动态渲染的 canvas 二维码居中 -->
  <style type="text/tailwindcss">
    @layer utilities {
      #qrimg canvas {
        @apply mx-auto rounded-lg;
      }
    }
  </style>
  
  <!--[if lt IE 9]>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4 font-sans antialiased">
  
  <!-- 主体卡片容器 -->
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden transform transition-all duration-300">
    
    <!-- 卡片头部 -->
    <div class="px-6 py-4 border-b border-slate-50 bg-slate-50/80 flex justify-center items-center">
        <h3 class="text-base font-bold text-slate-800 m-0 flex items-center">
            <i class="fa <?php echo $type == 'qq' ? 'fa-qq text-sky-500' : 'fa-weixin text-emerald-500'; ?> mr-2 text-lg"></i>
            绑定 <?php echo $typename;?>
        </h3>
    </div>

    <!-- 卡片内容区 -->
    <div class="p-6 sm:p-8 flex flex-col items-center space-y-6">
        
        <!-- 动态提示信息 -->
        <div id="login" class="text-center w-full p-3.5 bg-brand-50 rounded-xl border border-brand-100 text-brand-700 text-sm font-medium shadow-sm transition-colors">
            <span id="loginmsg">请使用 <?php echo $typename;?> 扫描二维码</span><span id="loginload" class="ml-1 animate-pulse font-bold text-brand-500 tracking-widest">...</span>
        </div>

        <!-- 二维码渲染区 -->
        <div class="p-3 bg-white border-2 border-dashed border-slate-200 rounded-2xl hover:border-brand-300 hover:bg-slate-50 transition-colors cursor-pointer shadow-sm relative group w-48 h-48 flex items-center justify-center" id="qrimg" title="点击刷新二维码">
            <!-- jquery.qrcode 会在此处动态插入 canvas -->
            
            <!-- 悬浮刷新蒙层 -->
            <div class="absolute inset-0 bg-white/70 opacity-0 group-hover:opacity-100 flex flex-col items-center justify-center transition-opacity duration-200 rounded-2xl backdrop-blur-[2px]">
                <div class="w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center mb-2 text-brand-500">
                    <i class="fa fa-refresh text-xl"></i>
                </div>
                <span class="text-xs font-semibold text-brand-600">点击刷新</span>
            </div>
        </div>

        <!-- 移动端额外操作 (默认隐藏) -->
        <div id="mobile" class="w-full space-y-3" style="display:none;">
            <button type="button" id="mlogin" onclick="mloginurl()" class="w-full h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                <i class="fa fa-paper-plane-o mr-1.5"></i>跳转 <?php echo $typename;?> 快捷登录
            </button>
            <button type="button" onclick="qrlogin()" class="w-full h-11 rounded-xl bg-emerald-500 text-white font-medium text-sm hover:bg-emerald-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                <i class="fa fa-check-circle mr-1.5"></i>我已完成登录
            </button>
            <div class="pt-3 px-2">
                <p class="text-xs text-slate-400 leading-relaxed text-center"><i class="fa fa-lightbulb-o text-amber-500 mr-1 text-sm"></i>提示：手机用户如需微信扫码，可截图保存二维码，在微信内扫一扫，从相册识别二维码。</p>
            </div>
        </div>
        
    </div>
  </div>

<script>
    // 注入给 JS 使用的全局变量
    var isbind = true;
    var bindtype = '<?php echo $type;?>';
    var bindtypename = '<?php echo $typename;?>';
</script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
<script src="./assets/js/qrlogin.js"></script>
</body>
</html>
