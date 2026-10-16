<?php
if (!defined('IN_CRONLITE')) {
    exit();
}
$kw = !empty($_GET['kw']) ? trim(daddslashes($_GET['kw'])) : null;
if ($kw) {
    $sql = " title LIKE '%$kw%'";
    $link = "&kw=".$kw;
} else {
    $sql = " 1";
}
$msgcount = $DB->getColumn("SELECT count(*) FROM " . DBQZ . "article WHERE active=1");
$pagesize = 10;
$pages = ceil($msgcount / $pagesize);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT id,title,content,addtime FROM " . DBQZ . "article WHERE{$sql} AND active=1 ORDER BY top DESC,id DESC LIMIT $offset,$pagesize");
$msgrow = array();
while ($res = $rs->fetch()) {
    $msgrow[] = $res;
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $conf['sitename']?> - 文章列表</title>
    <?php if (!empty($conf['favicon'])) {?>
    <link rel="icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?php echo $conf['favicon']?>" type="image/x-icon" />
    <?php }?>
    
    <!-- 引入 Font Awesome 图标库 -->
    <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    
    <!-- 引入 Tailwind CSS 引擎 -->
    <script src="../../assets/V7/tailwindcss.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased text-slate-800">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
        
        <!-- 主面板 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden animate-[translate-y-[-10px]_0.3s_ease-out]">
            
            <!-- 头部标题区 -->
            <div class="px-6 sm:px-8 py-5 sm:py-6 bg-slate-50/80 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-500 flex items-center justify-center mr-3 shadow-sm shrink-0">
                        <i class="fa fa-newspaper-o text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight m-0">平台公告与资讯</h2>
                        <p class="text-xs text-slate-500 mt-1">共收录 <?php echo $msgcount; ?> 篇文章内容</p>
                    </div>
                </div>
                
                <!-- 搜索框重构 -->
                <div class="relative w-full sm:w-64 shrink-0">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa fa-search"></i>
                    </div>
                    <input type="text" name="kw" value="" class="w-full h-10 pl-10 pr-12 text-sm rounded-xl border border-slate-200 bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 shadow-sm transition-all outline-none text-slate-700" placeholder="输入关键词搜索..." onkeydown="if(event.keyCode==13){document.getElementById('doSearch').click()}" required>
                    <button type="button" id="doSearch" class="absolute inset-y-0 right-1.5 top-1.5 bottom-1.5 px-3 bg-slate-100 hover:bg-brand-50 text-slate-500 hover:text-brand-600 rounded-lg text-xs font-bold transition-colors">
                        搜索
                    </button>
                </div>
            </div>

            <!-- 文章列表区 -->
            <div class="p-6 sm:p-8">
                <div class="space-y-4" id="msglist">
                    <?php if ($msgcount == 0): ?>
                        <div class="py-16 flex flex-col items-center justify-center text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                <i class="fa fa-folder-open-o text-4xl text-slate-300"></i>
                            </div>
                            <h3 class="text-slate-500 font-bold text-base mb-1">文章列表空空如也</h3>
                            <p class="text-slate-400 text-sm">当前还没有发布任何文章或公告</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($msgrow as $row): 
                            $content = strip_tags($row['content']);
                            if (mb_strlen($content) > 80) {
                                $content = mb_substr($content, 0, 80, 'utf-8') . '......';
                            }
                        ?>
                            <!-- 现代文章卡片项 -->
                            <a href="<?php echo article_url($row['id']); ?>" class="group block p-5 rounded-2xl bg-white border border-slate-100 hover:border-brand-200 hover:shadow-md hover:shadow-brand-500/5 transition-all duration-300 relative overflow-hidden">
                                
                                <!-- 左侧品牌色装饰线 -->
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-2 sm:gap-4 mb-3">
                                    <h3 class="text-base font-bold text-slate-800 group-hover:text-brand-600 transition-colors leading-snug line-clamp-2 sm:line-clamp-1 pr-4">
                                        <?php echo strip_tags($row['title']); ?>
                                    </h3>
                                    <div class="shrink-0 flex items-center text-xs font-medium text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                                        <i class="fa fa-clock-o mr-1.5 text-slate-300"></i>
                                        <?php echo date('Y-m-d', strtotime($row['addtime'])); ?>
                                    </div>
                                </div>
                                
                                <p class="text-[13px] text-slate-500 leading-relaxed line-clamp-2 mb-0">
                                    <?php echo $content; ?>
                                </p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 分页控制 -->
                <?php if ($msgcount > $pagesize): ?>
                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between">
                    <?php if ($page > 1): ?>
                        <a href="<?php echo article_url(0, 'page='.($page - 1).$link); ?>" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 rounded-xl text-sm transition-all shadow-sm">
                            <i class="fa fa-arrow-left mr-2"></i> 上一页
                        </a>
                    <?php else: ?>
                        <div></div> <!-- 占位保持 flex 布局平衡 -->
                    <?php endif; ?>
                    
                    <span class="text-xs font-bold text-slate-400 px-4 py-1.5 bg-slate-50 rounded-lg border border-slate-100">
                        第 <?php echo $page; ?> / <?php echo $pages; ?> 页
                    </span>

                    <?php if ($page < $pages): ?>
                        <a href="<?php echo article_url(0, 'page='.($page + 1).$link); ?>" class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:text-slate-800 rounded-xl text-sm transition-all shadow-sm">
                            下一页 <i class="fa fa-arrow-right ml-2"></i>
                        </a>
                    <?php else: ?>
                        <div></div> <!-- 占位保持 flex 布局平衡 -->
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- 底部导航栏 -->
            <div class="px-6 sm:px-8 py-5 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <a href="./" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl text-[14px] font-bold transition-all shadow-sm">
                    <i class="fa fa-home mr-1.5"></i> 返回首页
                </a>
                
                <a href="./user/" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 bg-brand-50 border border-brand-100 text-brand-600 hover:bg-brand-100 hover:text-brand-700 rounded-xl text-[14px] font-bold transition-all shadow-sm">
                    <i class="fa fa-user mr-1.5"></i> 用户中心
                </a>
            </div>

        </div>
    </div>

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script>
// 解析 GET 参数
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            var j = u[i].split("=");
            get[j[0]] = j[1];
        }
        return get;
    } else {
        return {};
    }
})();

$(document).ready(function(){
    // 回显搜索关键词
    if($_GET['kw']){
        $("input[name='kw']").val(decodeURIComponent($_GET['kw']));
    }
    
    // 执行搜索跳转
    $("#doSearch").click(function () {
        var kw = $("input[name='kw']").val();
        // 构造友好的动画体验
        var icon = $(this).find('i');
        var originalText = $(this).html();
        $(this).html('<i class="fa fa-spinner fa-spin"></i>');
        window.location.href="./?mod=articlelist&kw="+encodeURIComponent(kw);
    });
});
</script>
</body>
</html>
