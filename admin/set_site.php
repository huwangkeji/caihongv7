<?php
/**
 * 网站信息配置（独立页面）
 *
 * 原 set.php?mod=site 因 set.php 混淆层内部逻辑缺失而渲染空壳。
 * 本页面以可读实现替代，配置项保存到 pre_config 表，与原系统完全兼容。
 *
 * 修复来源：后台体检报告_只读审计.md — 中危#4
 */
include '../includes/common.php';
$title = '网站信息配置';
$mod   = 'site';
include './head.php';
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
// 网站信息配置属于系统设置范畴，避免无权限员工账号修改（超级管理员不受限制）
adminpermission('set', 1);

/* ---------- POST 保存 ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_site'])) {
    $fields = [
        'sitename', 'keywords', 'description', 'kfqq',
        'anounce', 'bottom', 'paymsg', 'faka_mail',
    ];
    foreach ($fields as $k) {
        $v = isset($_POST[$k]) ? $_POST[$k] : '';
        $DB->exec("UPDATE pre_config SET v=:v WHERE k=:k", [':v' => $v, ':k' => $k]);
    }
    // 开关项
    $switches = [
        'captcha_open_free', 'captcha_open_reg',
        'search_open', 'gift_open', 'shoppingcart',
    ];
    foreach ($switches as $k) {
        $v = isset($_POST[$k]) ? '1' : '0';
        $DB->exec("UPDATE pre_config SET v=:v WHERE k=:k", [':v' => $v, ':k' => $k]);
    }
    $CACHE->update();
    echo '<script>location.href="./set_site.php?ok=1";</script>';
    exit;
}

$ok = isset($_GET['ok']) ? 1 : 0;
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10">

    <?php if ($ok): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center shadow-sm">
        <i class="fa fa-check-circle mr-2 text-lg"></i> 配置保存成功！
    </div>
    <?php endif; ?>

    <!-- 网站基本信息 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-globe text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">网站基本信息</h3>
        </div>
        <div class="p-6 sm:p-8">
            <form action="./set_site.php" method="post" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">网站名称</label>
                        <input type="text" name="sitename" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo htmlspecialchars($conf['sitename'] ?? ''); ?>">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">客服QQ号</label>
                        <input type="text" name="kfqq" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo htmlspecialchars($conf['kfqq'] ?? ''); ?>">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">SEO 关键词</label>
                    <input type="text" name="keywords" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo htmlspecialchars($conf['keywords'] ?? ''); ?>">
                    <p class="text-xs text-slate-400">多个关键词用英文逗号分隔</p>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">网站描述</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white"><?php echo htmlspecialchars($conf['description'] ?? ''); ?></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">首页公告内容</label>
                    <textarea name="anounce" rows="6" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono"><?php echo htmlspecialchars($conf['anounce'] ?? ''); ?></textarea>
                    <p class="text-xs text-slate-400">支持 HTML 标签</p>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">底部版权内容</label>
                    <textarea name="bottom" rows="3" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono"><?php echo htmlspecialchars($conf['bottom'] ?? ''); ?></textarea>
                </div>

                <!-- 功能开关 -->
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-sm font-bold text-slate-700 mb-4">功能开关</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <?php
                        $toggles = [
                            'captcha_open_free' => '免费下单验证码',
                            'captcha_open_reg'  => '注册验证码',
                            'search_open'       => '搜索功能',
                            'gift_open'         => '礼品功能',
                            'shoppingcart'      => '购物车功能',
                        ];
                        foreach ($toggles as $k => $label):
                            $checked = ($conf[$k] ?? '0') === '1' ? 'checked' : '';
                        ?>
                        <label class="flex items-center space-x-2.5 cursor-pointer select-none p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition-colors">
                            <input type="checkbox" name="<?php echo $k; ?>" value="1" class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" <?php echo $checked; ?>>
                            <span class="text-sm text-slate-600"><?php echo $label; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" name="save_site" value="1" class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>保存配置
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 支付与邮件模板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-envelope-o text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">支付提示与邮件模板</h3>
        </div>
        <div class="p-6 sm:p-8">
            <form action="./set_site.php" method="post" class="space-y-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">支付提示信息</label>
                    <textarea name="paymsg" rows="3" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono"><?php echo htmlspecialchars($conf['paymsg'] ?? ''); ?></textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">发卡邮件模板</label>
                    <textarea name="faka_mail" rows="6" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono"><?php echo htmlspecialchars($conf['faka_mail'] ?? ''); ?></textarea>
                    <p class="text-xs text-slate-400">可用变量：[name] [date] [kmdata] [alert] [domain]</p>
                </div>
                <div class="pt-4">
                    <button type="submit" name="save_site" value="1" class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>保存配置
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php
// 尝试包含 foot.php，不存在则补闭合标签
if (file_exists(__DIR__ . '/foot.php')) {
    include __DIR__ . '/foot.php';
} else {
    echo "</div>\n";
}
