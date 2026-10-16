<?php
if (!defined('IN_CRONLITE')) {
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : sysmsg('文章ID不存在');
$row = $DB->getRow("select * from " . DBQZ . "article where id='$id' and active=1 limit 1");
if (!$row) {
    sysmsg('当前文章不存在！');
}
$downResult = $DB->getRow("SELECT * FROM " . DBQZ . "article WHERE id<'$id' AND active=1 ORDER BY id DESC LIMIT 1");
$upResult = $DB->getRow("SELECT * FROM " . DBQZ . "article WHERE id>'$id' AND active=1 ORDER BY id DESC LIMIT 1");
$DB->exec("UPDATE `" . DBQZ . "article` SET `count`=`count`+1 WHERE id='$id'");

// 调试信息
echo "<!-- 调试: 数据库前缀=" . DBQZ . ", 文章ID=" . $id . ", 文章标题=" . $row['title'] . " -->";
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
    <title><?php echo $row['title']?> - <?php echo $conf['sitename']?></title>
    <meta name="description" content="<?php echo $row['description']?>"/>
    <meta name="keywords" content="<?php echo $row['keywords']?>"/>
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
    
    <!-- 针对富文本内容的排版美化 -->
    <style type="text/tailwindcss">
        @layer utilities {
            .article-content { @apply text-slate-700 leading-relaxed tracking-wide; }
            .article-content p { @apply mb-5; }
            .article-content img { @apply max-w-full h-auto rounded-xl shadow-sm mx-auto my-6 border border-slate-100; }
            .article-content a { @apply text-brand-500 hover:text-brand-600 underline underline-offset-4 transition-colors; }
            .article-content h1, .article-content h2, .article-content h3, .article-content h4 { @apply font-bold text-slate-800 mt-8 mb-4; }
            .article-content h1 { @apply text-2xl; }
            .article-content h2 { @apply text-xl; }
            .article-content h3 { @apply text-lg; }
            .article-content ul, .article-content ol { @apply pl-6 mb-5 space-y-2; }
            .article-content ul { @apply list-disc; }
            .article-content ol { @apply list-decimal; }
            .article-content blockquote { @apply border-l-4 border-brand-500 bg-brand-50/50 p-4 rounded-r-xl text-slate-600 my-6 text-[15px]; }
            .article-content table { @apply w-full text-left border-collapse mb-6; }
            .article-content th, .article-content td { @apply border border-slate-200 px-4 py-2; }
            .article-content th { @apply bg-slate-50 font-bold text-slate-700; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen font-sans antialiased text-slate-800 selection:bg-brand-100 selection:text-brand-900">

    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
        
        <!-- 文章主卡片 -->
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 overflow-hidden transition-all duration-300 animate-[translate-y-[-10px]_0.3s_ease-out]">
            
            <!-- 面包屑导航 -->
            <div class="px-6 sm:px-8 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center text-[13px] text-slate-500">
                <a href="./" class="hover:text-brand-500 transition-colors flex items-center font-medium"><i class="fa fa-home mr-1.5 text-sm"></i>首页</a>
                <span class="mx-2.5 text-slate-300">/</span>
                <a href="<?php echo article_url()?>" class="hover:text-brand-500 transition-colors font-medium">文章列表</a>
                <span class="mx-2.5 text-slate-300">/</span>
                <span class="text-slate-700 font-bold truncate"><?php echo $row['title']?></span>
            </div>

            <!-- 文章头部 -->
            <div class="px-6 sm:px-10 pt-10 pb-8 text-center border-b border-slate-50">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-800 mb-5 leading-snug"><?php echo $row['title']?></h1>
                
                <div class="flex items-center justify-center gap-5 sm:gap-8 text-[13px] text-slate-500 font-medium">
                    <span class="flex items-center bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                        <i class="fa fa-clock-o mr-1.5 text-slate-400"></i><?php echo $row['addtime']?>
                    </span>
                    <span class="flex items-center bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
                        <i class="fa fa-eye mr-1.5 text-slate-400"></i><?php echo $row['count']?> 次阅读
                    </span>
                </div>
            </div>

            <!-- 文章正文 -->
            <div class="px-6 sm:px-10 py-8 sm:py-10 article-content text-[15px] sm:text-base">
                <?php echo $row['content']?>
            </div>

            <!-- 上下篇导航 -->
            <div class="px-6 sm:px-10 py-6 bg-slate-50/50 border-t border-slate-100 flex flex-col gap-3.5">
                <div class="flex items-center text-[14px]">
                    <span class="text-slate-400 w-[60px] shrink-0 font-medium"><i class="fa fa-angle-left mr-1"></i>上一篇</span>
                    <?php echo empty($upResult) ? '<span class="text-slate-400">没有了~</span>' : ('<a href="'.article_url($upResult['id']).'" class="text-slate-700 hover:text-brand-600 font-bold transition-colors truncate">' . $upResult['title'] . '</a>'); ?>
                </div>
                <div class="flex items-center text-[14px]">
                    <span class="text-slate-400 w-[60px] shrink-0 font-medium"><i class="fa fa-angle-right mr-1"></i>下一篇</span>
                    <?php echo empty($downResult) ? '<span class="text-slate-400">没有了~</span>' : ('<a href="'.article_url($downResult['id']).'" class="text-slate-700 hover:text-brand-600 font-bold transition-colors truncate">' . $downResult['title'] . '</a>'); ?>
                </div>
            </div>

            <!-- 底部操作按钮 -->
            <div class="px-6 sm:px-10 py-5 bg-white border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="./" class="inline-flex items-center justify-center px-6 py-2.5 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-slate-800 rounded-xl text-[14px] font-bold transition-all duration-300 shadow-sm flex-1 sm:flex-none">
                    <i class="fa fa-home mr-1.5"></i>返回首页
                </a>
                
                <a href="<?php echo article_url()?>" class="inline-flex items-center justify-center px-6 py-2.5 bg-brand-50 border border-brand-100 text-brand-600 hover:bg-brand-100 hover:text-brand-700 rounded-xl text-[14px] font-bold transition-all duration-300 shadow-sm flex-1 sm:flex-none">
                    <i class="fa fa-list mr-1.5"></i>文章列表
                </a>
            </div>

        </div>
        
    </div>

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
</body>
</html>
