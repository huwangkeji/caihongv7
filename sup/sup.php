<?php

include "../includes/common.php";
$title = "供货商店铺主页";
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

$sid = intval($_GET["sid"]);
$rows = $DB->getRow("select * from pre_supplier where sid='" . $sid . "' and status = '1' limit 1");
if (!$rows) {
	exit("<script>alert(\"不存在此供货商！\");location.href=\"/\"</script>");
}
if ($rows["bond"] < $conf["sup_bond"]) {
	exit("<script>alert(\"此供货商未缴纳保证金！\");location.href=\"/\"</script>");
}

$numrows = $DB->getColumn("SELECT count(*) FROM pre_tools WHERE active=1 and goods_sid='" . $sid . "' and audit_status='1'");
$rs = $DB->query("SELECT * FROM pre_tools WHERE active=1 and goods_sid='" . $sid . "' and audit_status='1' ORDER BY sort ASC");

if (empty($rows["qq"]) && !empty($rows["faceimg"])) {
	$faceimg = htmlspecialchars($rows["faceimg"]);
} elseif (!empty($rows["qq"])) {
	$faceimg = '/assets/img/user.png';
} else {
	$faceimg = "/assets/img/user.png";
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
        
        /* 滚动条美化 */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex items-center justify-center p-4 selection:bg-indigo-100 selection:text-indigo-700 relative">

    <!-- 全局背景层 -->
    <?php if($conf['background_image_enable'] == 1 && $conf['background_type'] == 'image'){ ?>
        <div class="fixed inset-0 z-0 bg-cover bg-center transition-all duration-700" style="background-image: url('/assets/img/bj.png'); filter: brightness(0.9);"></div>
    <?php } else { ?>
        <div class="fixed inset-0 z-0 bg-gradient-to-br from-slate-50 to-slate-100"></div>
    <?php } ?>

    <div class="relative z-10 w-full max-w-[500px]">
        <!-- 卡片主体 -->
        <div class="bg-white/95 backdrop-blur-xl border border-white/60 rounded-[24px] shadow-2xl overflow-hidden transition-all">
            
            <!-- 顶部横幅与头像 -->
            <div class="relative h-32 bg-gradient-to-r from-indigo-500/10 to-sky-500/10 flex justify-center items-end pb-8">
                <!-- 悬浮头像 -->
                <img src="<?php echo $faceimg;?>" class="absolute -bottom-10 w-20 h-20 rounded-2xl border-4 border-white shadow-md object-cover bg-white hover:scale-105 transition-transform">
            </div>

            <!-- 供货商信息面板 -->
            <div class="pt-12 pb-5 px-6 text-center border-b border-slate-100/80 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800 mb-2">供货商专营店</h2>
                <div class="flex justify-center items-center gap-3">
                    <span class="inline-flex items-center bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-lg text-xs font-bold shadow-sm border border-indigo-100">
                        <i class="fa fa-cubes mr-1.5 opacity-80"></i>在售商品: <?php echo $numrows;?> 件
                    </span>
                    <span class="inline-flex items-center bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded-lg text-xs font-bold shadow-sm border border-emerald-100">
                        <i class="fa fa-shield mr-1.5 opacity-80"></i>已缴保证金: ¥<?php echo $rows["bond"];?>
                    </span>
                </div>
            </div>
            
            <!-- 内容区 -->
            <div class="p-6">
                
                <!-- 担保与警告提示 -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-5 shadow-sm space-y-2">
                    <div class="flex items-start">
                        <i class="fa fa-check-circle text-emerald-500 mt-0.5 mr-2 text-[15px]"></i>
                        <p class="text-xs text-slate-700 font-bold m-0 leading-relaxed">以下业务由供货商提供，平台担保交易！</p>
                    </div>
                    <div class="flex items-start">
                        <i class="fa fa-exclamation-triangle text-rose-500 mt-0.5 mr-2 text-[15px]"></i>
                        <p class="text-[11px] text-rose-600 font-bold m-0 leading-relaxed">请勿脱离平台交易，谨防受骗！（线下交易平台概不负责）</p>
                    </div>
                </div>

                <!-- 商品列表 -->
                <div class="space-y-2.5 max-h-[50vh] overflow-y-auto pr-1">
                    <?php 
                    while ($res = $rs->fetch()) { 
                    ?>
                        <div class="flex items-center justify-between p-3.5 rounded-xl bg-white hover:bg-slate-50 border border-slate-100 hover:border-indigo-100 transition-all group shadow-sm">
                            <a href="/?cid=<?php echo $res["cid"]; ?>&tid=<?php echo $res["tid"]; ?>" target="_blank" class="font-bold text-slate-700 group-hover:text-indigo-600 transition-colors truncate flex-1 mr-4 text-sm" title="<?php echo htmlspecialchars($res["name"]); ?>">
                                <?php echo $res["name"]; ?>
                            </a>
                            <a href="/?cid=<?php echo $res["cid"]; ?>&tid=<?php echo $res["tid"]; ?>" target="_blank" class="shrink-0 px-4 py-1.5 rounded-lg bg-indigo-50 text-indigo-600 font-bold text-xs hover:bg-indigo-500 hover:text-white transition-colors shadow-sm border border-indigo-100">
                                查看详情
                            </a>
                        </div>
                    <?php 
                    } 
                    if($numrows == 0) {
                    ?>
                        <div class="text-center py-10 text-slate-400">
                            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
                            <span class="text-sm font-bold">该供货商暂无在售商品</span>
                        </div>
                    <?php 
                    } 
                    ?>
                </div>

            </div>
        </div>
        
        <!-- 底部返回首页 -->
        <div class="mt-6 text-center">
            <a href="/" class="inline-flex items-center text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors bg-white/60 backdrop-blur px-4 py-1.5 rounded-full shadow-sm">
                <i class="fa fa-angle-left mr-1.5 text-lg"></i>返回平台首页
            </a>
        </div>
    </div>

</body>
</html>
