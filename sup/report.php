<?php

include "../includes/common.php";
$title = "举报供货商";
if ($conf["cdnpublic"] == 1) {
	$cdnpublic = "/assets/vendor/lib.baomitu.com/";
} elseif ($conf["cdnpublic"] == 2) {
	$cdnpublic = "/assets/vendor/lib.baomitu.com/";
} elseif ($conf["cdnpublic"] == 4) {
	$cdnpublic = "/assets/vendor/lib.baomitu.com/";
} else {
	$cdnpublic = "/assets/vendor/lib.baomitu.com/";
}
if (!empty($conf["staticurl"])) {
	$cdnserver = "//" . $conf["staticurl"] . "/";
} else {
	$cdnserver = "../";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <title><?php echo $title;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    
    <!-- 引入 Tailwind CSS 核心引擎 -->
    <script src="../assets/V7/tailwindcss.js"></script>
    <link href="<?php echo $cdnpublic;?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <script src="<?php echo $cdnpublic;?>jquery/2.1.4/jquery.min.js"></script>
    <script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>

    <style>
        /* 屏蔽原生高亮，提升触控体验 */
        * { -webkit-tap-highlight-color: transparent; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex items-center justify-center p-4 selection:bg-indigo-100 selection:text-indigo-700 relative">

    <!-- 全局背景层 -->
    <div class="fixed inset-0 z-0 bg-gradient-to-br from-slate-50 to-slate-100"></div>

    <div class="relative z-10 w-full max-w-[400px]">
        <!-- 卡片主体 -->
        <div class="bg-white/90 backdrop-blur-xl border border-white/60 rounded-[28px] shadow-2xl overflow-hidden transition-all">
            
            <!-- 头部标题 -->
            <div class="px-6 py-6 border-b border-slate-100/80 bg-slate-50/50 flex flex-col items-center justify-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3 shadow-sm border border-indigo-100">
                    <i class="fa fa-phone-square text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 m-0 tracking-tight">联系方式</h3>
                <p class="text-sm font-medium text-slate-500 mt-1 mb-0">获取协助或发起投诉举报</p>
            </div>
            
            <!-- 内容区 -->
            <div class="p-6">
                <?php 
                if (!$conf["sup_report"]) {
                	exit("<script>layer.alert('站长暂未设置联系方式', {icon:0, title:'提示'}, function(){location.href='/';});</script>");
                }
                
                echo '<div class="flex flex-col gap-3">';
                $rs = explode("\n", $conf["sup_report"]);
                foreach ($rs as $res) {
                    $res = trim($res);
                    if(empty($res)) continue;
                    
                	$arr = explode("|", $res);
                    if(count($arr) < 2) continue; // 容错处理
                    
                	echo "<a href=\"" . htmlspecialchars($arr[0]) . "\" target=\"_blank\" class=\"flex items-center justify-center w-full h-12 rounded-xl bg-slate-50 text-slate-700 font-bold text-[15px] border border-slate-200 hover:bg-indigo-500 hover:text-white hover:border-indigo-500 shadow-sm transition-all active:scale-[0.98] group\">
                            <i class=\"fa fa-comments-o mr-2 text-slate-400 group-hover:text-white transition-colors\"></i>" . htmlspecialchars($arr[1]) . "
                          </a>";
                }
                echo '</div>';
                ?>
            </div>
        </div>
        
        <!-- 底部返回首页 -->
        <div class="mt-8 text-center">
            <a href="/" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors">
                <i class="fa fa-angle-left mr-1.5 text-lg"></i>返回首页
            </a>
        </div>
    </div>

</body>
</html>
