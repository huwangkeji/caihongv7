<?php
/**
 * 客服聊天系统配置（独立页面）
 *
 * 原 set.php?mod=chat 因 set.php 混淆层内部逻辑缺失而渲染空壳。
 * 本页面以可读实现替代，配置项保存到 pre_config 表，与原系统完全兼容。
 *
 * 修复来源：后台体检报告_只读审计.md — 中危#4
 */
include '../includes/common.php';
$title = '客服系统配置';
$mod   = 'chat';
include './head.php';
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
// 客服系统配置属于系统设置范畴，避免无权限员工账号修改（超级管理员不受限制）
adminpermission('set', 1);

/* ---------- POST 保存 ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_chat'])) {
    $chatframe  = isset($_POST['chatframe'])  ? $_POST['chatframe']  : '';
    $chat_sound = isset($_POST['chat_sound']) ? intval($_POST['chat_sound']) : 1;
    $kfqq       = isset($_POST['kfqq'])       ? trim($_POST['kfqq']) : '';

    $DB->exec("UPDATE pre_config SET v=:v WHERE k='chatframe'",  [':v' => $chatframe]);
    $DB->exec("UPDATE pre_config SET v=:v WHERE k='chat_sound'", [':v' => $chat_sound]);
    $DB->exec("UPDATE pre_config SET v=:v WHERE k='kfqq'",       [':v' => $kfqq]);
    $CACHE->update();

    echo '<script>location.href="./set_chat.php?ok=1";</script>';
    exit;
}

$ok = isset($_GET['ok']) ? 1 : 0;
$chat_sound = intval($conf['chat_sound'] ?? 1);
?>

<div class="w-full max-w-[900px] mx-auto space-y-6 pb-10">

    <?php if ($ok): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center shadow-sm">
        <i class="fa fa-check-circle mr-2 text-lg"></i> 配置保存成功！
    </div>
    <?php endif; ?>

    <!-- 客服聊天框架配置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-comments text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">客服聊天系统配置</h3>
        </div>
        <div class="p-6 sm:p-8">
            <form action="./set_chat.php" method="post" class="space-y-5">

                <div class="p-5 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-info-circle mt-0.5 mr-2 text-lg shrink-0"></i>
                    <div class="leading-relaxed">
                        在下方填入第三方客服插件（如商务通、美洽、53客服等）提供的 <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-mono">&lt;script&gt;</code> 或 <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-mono">&lt;iframe&gt;</code> 代码，系统会自动在前台页面底部注入该代码以加载客服聊天窗口。
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">客服QQ号</label>
                    <input type="text" name="kfqq" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" value="<?php echo htmlspecialchars($conf['kfqq'] ?? ''); ?>" placeholder="例如: 123456">
                    <p class="text-xs text-slate-400">用于前台显示"联系客服"按钮</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">客服聊天框架代码</label>
                    <textarea name="chatframe" rows="10" class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono" placeholder="粘贴第三方客服插件的嵌入代码..."><?php echo htmlspecialchars($conf['chatframe'] ?? ''); ?></textarea>
                    <p class="text-xs text-slate-400">支持 HTML / JavaScript 代码，留空则不注入</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">客服通知提示音</label>
                    <select name="chat_sound" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white">
                        <option value="1" <?php echo $chat_sound === 1 ? 'selected' : ''; ?>>开启提示音</option>
                        <option value="0" <?php echo $chat_sound === 0 ? 'selected' : ''; ?>>关闭提示音</option>
                    </select>
                    <p class="text-xs text-slate-400">新消息到达时是否播放提示音</p>
                </div>

                <div class="pt-4">
                    <button type="submit" name="save_chat" value="1" class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>保存配置
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 快捷入口 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-external-link text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">相关功能</h3>
        </div>
        <div class="p-6 sm:p-8 flex flex-wrap gap-3">
            <a href="./chatwork.php" class="inline-flex items-center justify-center h-10 px-5 rounded-xl bg-brand-50 text-brand-600 hover:bg-brand-600 hover:text-white font-medium text-sm transition-colors shadow-sm">
                <i class="fa fa-headphones mr-1.5"></i>客服工作台
            </a>
            <a href="./set_site.php" class="inline-flex items-center justify-center h-10 px-5 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium text-sm transition-colors shadow-sm">
                <i class="fa fa-globe mr-1.5"></i>网站信息配置
            </a>
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
