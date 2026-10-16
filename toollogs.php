<?php
include './includes/common.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, maximum-scale=1.0, user-scalable=0">
    <title><?php echo $conf['sitename'];?> - 上架日志</title>
    
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    
    <script src="./assets/V7/tailwindcss.js"></script>
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

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
        
        <!-- 头部导航与标题区 -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 sm:mb-10 gap-4 animate-[fadeIn_0.5s_ease-out]">
            <h1 class="text-2xl sm:text-3xl font-black text-slate-800 tracking-tight flex items-center">
                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-500 flex items-center justify-center mr-3 shadow-sm">
                    <i class="fa fa-history"></i>
                </div>
                系统上架日志
            </h1>
            
            <a href="/" class="group inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-white border border-slate-200 hover:border-brand-300 hover:bg-brand-50 rounded-full font-bold text-slate-600 hover:text-brand-600 transition-all duration-300 shadow-sm hover:shadow-md shrink-0">
                <i class="fa fa-arrow-left transition-transform duration-300 group-hover:-translate-x-1"></i> 
                返回首页
            </a>
        </div>
        
        <!-- 日志面板容器 -->
        <div class="space-y-5">
            <?php
            // 按日期分组日志
            $logs_by_date = array();
            $rs = $DB->query("SELECT * FROM pre_toollogs ORDER BY date DESC");
            while ($res = $rs->fetch()) {
                $date = $res['date'];
                if (!isset($logs_by_date[$date])) {
                    $logs_by_date[$date] = array();
                }
                $logs_by_date[$date][] = $res['content'];
            }

            // 遍历分组后的日志
            foreach ($logs_by_date as $date => $contents) {
                // 合并同一天的所有商品
                $all_products = array();
                foreach ($contents as $content) {
                    $products = explode("\n", trim($content));
                    $products = array_filter($products);
                    $all_products = array_merge($all_products, $products);
                }
                $count = count($all_products);

                // 分离可见和隐藏的商品
                $visible_products = array_slice($all_products, 0, 30); // 显示前30个
                $hidden_products = array_slice($all_products, 30); // 隐藏剩余的
                
                // 智能判断是否为今日上架
                $is_today = ($date == date('Y-m-d'));
            ?>
                
            <!-- 独立日期面板 -->
            <div class="panel-container bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 overflow-hidden transition-all duration-300 animate-[translate-y-[-10px]_0.3s_ease-out]">
                
                <!-- 面板触发头 -->
                <div class="panel-header cursor-pointer bg-slate-50/50 hover:bg-slate-50 px-5 sm:px-6 py-4 flex items-center justify-between border-b border-slate-100 transition-colors select-none" onclick="togglePanel(this)">
                    <div class="flex items-center flex-wrap gap-2 sm:gap-3">
                        <span class="text-base sm:text-lg font-bold text-slate-800 font-mono tracking-tight"><?php echo htmlspecialchars($date); ?></span>
                        
                        <?php if($is_today): ?>
                            <span class="inline-flex items-center px-2.5 py-1 bg-rose-100 text-rose-600 text-xs font-bold rounded-lg border border-rose-200">
                                <i class="fa fa-fire mr-1"></i> 今日上架
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-500 text-xs font-bold rounded-lg border border-slate-200">
                                历史记录
                            </span>
                        <?php endif; ?>
                        
                        <span class="inline-flex items-center text-xs font-medium text-brand-600 bg-brand-50 border border-brand-100 px-2.5 py-1 rounded-lg">
                            共计 <?php echo $count; ?> 件商品
                        </span>
                    </div>
                    
                    <div class="toggle-btn w-8 h-8 shrink-0 flex items-center justify-center rounded-full bg-slate-100 text-slate-400 transition-all duration-300">
                        <i class="fa fa-chevron-down text-sm"></i>
                    </div>
                </div>
                
                <!-- 面板折叠体 -->
                <div class="panel-body max-h-0 opacity-0 overflow-hidden transition-all duration-500 ease-in-out">
                    <div class="p-5 sm:p-6 bg-white">
                        
                        <!-- 默认可见商品区 (网格布局) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                            <?php foreach ($visible_products as $product): ?>
                                <?php
                                if (trim($product)) {
                                    $goods_name = str_replace('上架：', '', $product);
                                    $goods = $DB->getRow("SELECT tid, cid FROM pre_tools WHERE name = :name LIMIT 1", array(':name' => $goods_name));
                                    $link = $goods ? "./?cid={$goods['cid']}&tid={$goods['tid']}" : "./";
                                ?>
                                    <a href="<?php echo $link; ?>" class="group flex items-center p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-brand-50 hover:border-brand-200 transition-all duration-300">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0 shadow-sm border border-slate-100 group-hover:border-brand-200 group-hover:text-brand-500 transition-colors mr-3">
                                            <i class="fa fa-cube text-slate-400 group-hover:text-brand-500 text-xs"></i>
                                        </div>
                                        <span class="text-[13px] font-medium text-slate-600 group-hover:text-brand-700 truncate" title="<?php echo htmlspecialchars($product); ?>">
                                            <?php echo htmlspecialchars($product); ?>
                                        </span>
                                    </a>
                                <?php } ?>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- 折叠隐藏商品区 -->
                        <?php if (!empty($hidden_products)): ?>
                            <div class="hidden-items hidden">
                                <div class="mt-4 pt-4 border-t border-dashed border-slate-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4">
                                    <?php foreach ($hidden_products as $product): ?>
                                        <?php
                                        if (trim($product)) {
                                            $goods_name = str_replace('上架：', '', $product);
                                            $goods = $DB->getRow("SELECT tid, cid FROM pre_tools WHERE name = :name LIMIT 1", array(':name' => $goods_name));
                                            $link = $goods ? "./?cid={$goods['cid']}&tid={$goods['tid']}" : "./";
                                        ?>
                                            <a href="<?php echo $link; ?>" class="group flex items-center p-3 bg-slate-50 border border-slate-100 rounded-xl hover:bg-brand-50 hover:border-brand-200 transition-all duration-300">
                                                <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shrink-0 shadow-sm border border-slate-100 group-hover:border-brand-200 group-hover:text-brand-500 transition-colors mr-3">
                                                    <i class="fa fa-cube text-slate-400 group-hover:text-brand-500 text-xs"></i>
                                                </div>
                                                <span class="text-[13px] font-medium text-slate-600 group-hover:text-brand-700 truncate" title="<?php echo htmlspecialchars($product); ?>">
                                                    <?php echo htmlspecialchars($product); ?>
                                                </span>
                                            </a>
                                        <?php } ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <!-- 查看更多提示按钮 -->
                            <div class="mt-5 text-center view-more-wrapper">
                                <button type="button" onclick="showHiddenItems(this, event)" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 rounded-full text-xs font-bold transition-colors">
                                    <i class="fa fa-angle-double-down"></i> 展开剩余的 <?php echo count($hidden_products); ?> 个商品
                                </button>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            
            <?php } ?>
        </div>
        
        <!-- 底部说明 -->
        <div class="mt-8 text-center text-slate-400 text-xs font-medium">
            <p>已展示所有商品上架记录</p>
        </div>

    </div>
    
    <!-- 交互脚本 -->
    <script>
        // 面板展开与收起
        function togglePanel(header) {
            const container = header.closest('.panel-container');
            const body = container.querySelector('.panel-body');
            const btn = container.querySelector('.toggle-btn');
            
            if (body.classList.contains('max-h-0')) {
                // 执行展开
                body.classList.remove('max-h-0', 'opacity-0');
                body.classList.add('max-h-[5000px]', 'opacity-100');
                
                // 按钮样式变换
                btn.style.transform = 'rotate(180deg)';
                btn.classList.add('bg-brand-100', 'text-brand-600');
                btn.classList.remove('bg-slate-100', 'text-slate-400');
                header.classList.add('bg-slate-50');
            } else {
                // 执行收起
                body.classList.add('max-h-0', 'opacity-0');
                body.classList.remove('max-h-[5000px]', 'opacity-100');
                
                // 按钮样式还原
                btn.style.transform = 'rotate(0deg)';
                btn.classList.remove('bg-brand-100', 'text-brand-600');
                btn.classList.add('bg-slate-100', 'text-slate-400');
                header.classList.remove('bg-slate-50');
            }
        }
        
        // 隐藏商品展示逻辑
        function showHiddenItems(btn, event) {
            event.stopPropagation(); // 阻止事件冒泡触发面板收起
            const container = btn.closest('.panel-body');
            const hiddenItems = container.querySelector('.hidden-items');
            
            if(hiddenItems) {
                hiddenItems.classList.remove('hidden');
                hiddenItems.classList.add('animate-[fadeIn_0.5s_ease-out]');
                btn.closest('.view-more-wrapper').remove(); // 移除查看更多按钮
            }
        }
        
        // 默认自动展开第一天的面板
        document.addEventListener('DOMContentLoaded', function() {
            const firstHeader = document.querySelector('.panel-header');
            if (firstHeader) {
                togglePanel(firstHeader);
            }
        });
    </script>
</body>
</html>
