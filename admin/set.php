<?php
/**
 * 后台系统设置（可读离线实现）
 *
 * 该文件替代原 eval/base64/rot13/hex 多层混淆版本。
 * 仅保留项目中仍有入口引用的 site、chat、cron、rewrite、cloneset 模块，
 * 不包含授权校验、远程配置、远程指令、心跳或数据上报逻辑。
 */
$is_defend = true;
require __DIR__ . '/../includes/common.php';

if ($islogin != 1) {
    header('Location: ./login.php');
    exit;
}

$mod = isset($_GET['mod']) ? preg_replace('/[^a-z0-9_-]/i', '', (string) $_GET['mod']) : 'site';

if ($mod === 'site') {
    header('Location: ./set_site.php');
    exit;
}
if ($mod === 'chat') {
    header('Location: ./set_chat.php');
    exit;
}

// 权限校验提前到模块分发之前：此前未授权用户可绕过 adminpermission 直达模块判断
adminpermission('super', 1);

// ---------------------------------------------------------------- 配置驱动模块
// 离线化改造后只保留了 site / chat / cron / rewrite / cloneset，其余设置项（支付、
// 模板、邮件、快捷登录、分站、美化…）全部失效。这里用一份配置数组描述各模块的
// 配置项，统一渲染表单并保存到 pre_config，一次性把这些设置入口恢复为可用状态。
$configModules = require __DIR__ . '/set_modules.php';
require_once __DIR__ . '/set_modules_render.php';

if (isset($configModules[$mod])) {
    $error = '';
    saveSetModule($mod, $configModules[$mod]);
    renderSetModule($mod, $configModules[$mod], isset($_GET['ok']), $error);
    exit;
}

// 防CC攻击：参数已迁移到独立页面 cc_protect.php
if ($mod === 'defend') {
    renderDefendModule();
    exit;
}

// 扫码登录：绑定入口
if ($mod === 'bind') {
    renderBindModule();
    exit;
}

// 密码修改：单独处理（写 pre_account 而不是 pre_config）
if ($mod === 'account') {
    $title = '密码修改';
    $acctError = '';
    $acctOk = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_account'])) {
        if (!checkRefererHost()) {
            http_response_code(403);
            exit('请求来源校验失败');
        }
        $oldpwd = trim((string) ($_POST['oldpwd'] ?? ''));
        $newpwd = trim((string) ($_POST['newpwd'] ?? ''));
        $newpwd2 = trim((string) ($_POST['newpwd2'] ?? ''));
        $row = $DB->getRow("SELECT * FROM pre_account WHERE id=:id LIMIT 1", [':id' => $adminuserrow['id'] ?? 0]);
        if ($oldpwd === '' || $newpwd === '' || $newpwd2 === '') {
            $acctError = '请填写完整的新旧密码。';
        } elseif (!$row || !password_verify($oldpwd, $row['password']) && md5($oldpwd . ($row['salt'] ?? '')) !== $row['password'] && $oldpwd !== $row['password']) {
            $acctError = '原密码不正确。';
        } elseif ($newpwd !== $newpwd2) {
            $acctError = '两次输入的新密码不一致。';
        } elseif (strlen($newpwd) < 6) {
            $acctError = '新密码长度不能少于 6 位。';
        } else {
            $newHash = password_hash($newpwd, PASSWORD_DEFAULT);
            $DB->exec("UPDATE pre_account SET password=:pwd WHERE id=:id", [':pwd' => $newHash, ':id' => $row['id']]);
            $acctOk = true;
        }
    }
    include __DIR__ . '/head.php';
    ?>
    <div class="w-full max-w-[700px] mx-auto space-y-4 pb-10">
        <?php if ($acctOk): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center shadow-sm">
                <i class="fa fa-check-circle mr-2 text-lg"></i> 密码修改成功，下次登录请使用新密码。
            </div>
        <?php endif; ?>
        <?php if ($acctError): ?>
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center shadow-sm">
                <i class="fa fa-exclamation-circle mr-2 text-lg"></i> <?php echo htmlspecialchars($acctError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-key text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改当前管理员密码</h3>
            </div>
            <div class="p-6 sm:p-8">
                <form method="post" action="./set.php?mod=account" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">原密码</label>
                        <input type="password" name="oldpwd" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" autocomplete="current-password">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">新密码</label>
                        <input type="password" name="newpwd" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" autocomplete="new-password">
                        <p class="text-xs text-slate-400">长度不少于 6 位</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">确认新密码</label>
                        <input type="password" name="newpwd2" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" autocomplete="new-password">
                    </div>
                    <button type="submit" name="save_account" value="1" class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>保存修改
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/foot.php';
    exit;
}

$allowedModules = ['cron', 'rewrite', 'cloneset'];
if (!in_array($mod, $allowedModules, true)) {
    // 离线化改造后只有上述三个模块保留实现，其余 mod（pay / template / mail / oauth …）
    // 原先一律 302 跳到 set_site.php，用户点菜单后页面"莫名跳走"，不知道设置项已下线。
    // 这里改为渲染明确说明页，并给出仍然可用的入口。
    $title = '设置项不可用';
    include __DIR__ . '/head.php';
    $modNameMap = [
        'account' => '密码修改', 'bind' => '扫码登录', 'pay' => '支付接口配置',
        'template' => '首页模板设置', 'mail' => '邮箱与提醒配置', 'mailcon' => '发信模板',
        'oauth' => '快捷登录配置', 'captcha' => '验证与IP配置', 'defend' => '防CC攻击设置',
        'gonggao' => '网站公告配置', 'invite' => '推广链接设置', 'fenzhan' => '分站相关配置',
        'upimg' => 'Logo与背景设置', 'dwz' => '防红接口设置', 'qiandao' => '每日签到设置',
        'rebaterecharge' => '充值返利设置', 'sup' => '供货商相关配置',
        'beautify' => '前台美化', 'beautify_admin' => '后台美化', 'beautify_sidebar' => '后台侧边栏框架美化',
        'beautify_font' => '字体美化', 'beautify_background' => '背景美化',
    ];
    $modLabel = isset($modNameMap[$mod]) ? $modNameMap[$mod] : $mod;
    ?>
    <div class="w-full max-w-[800px] mx-auto space-y-4 pb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
            <div class="text-amber-500 text-4xl mb-3"><i class="fa fa-exclamation-triangle"></i></div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">「<?php echo htmlspecialchars($modLabel, ENT_QUOTES, 'UTF-8'); ?>」暂不可用</h3>
            <p class="text-sm text-slate-500 leading-6">
                该设置模块（<code class="px-1.5 py-0.5 bg-slate-100 rounded text-xs">mod=<?php echo htmlspecialchars($mod, ENT_QUOTES, 'UTF-8'); ?></code>）
                在当前离线版本中没有对应实现，因此无法打开。<br/>
                如果你确实需要这项功能，请联系技术人员单独恢复。
            </p>
            <div class="mt-6 flex flex-wrap gap-3 justify-center">
                <a href="./set_site.php" class="h-10 px-5 inline-flex items-center rounded-xl bg-brand-600 text-white text-sm font-medium">前往网站信息配置</a>
                <a href="./index.php" class="h-10 px-5 inline-flex items-center rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium">返回后台首页</a>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/foot.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkRefererHost()) {
        http_response_code(403);
        exit('请求来源校验失败');
    }

    if ($mod === 'cron' && isset($_POST['save_cron'])) {
        $cronkey = trim((string) ($_POST['cronkey'] ?? ''));
        if ($cronkey === '') {
            $cronkey = (string) random_int(100000, 999999);
        }
        if (!preg_match('/^[A-Za-z0-9_-]{6,64}$/', $cronkey)) {
            $error = '监控密钥只能包含字母、数字、下划线和短横线，长度 6-64 位。';
        } else {
            saveSetting('cronkey', $cronkey);
            $CACHE->update();
            header('Location: ./set.php?mod=cron&ok=1');
            exit;
        }
    }

    if ($mod === 'cloneset' && isset($_POST['save_clone'])) {
        $apikey = trim((string) ($_POST['apikey'] ?? ''));
        if ($apikey === '') {
            $apikey = bin2hex(random_bytes(16));
        }
        if (!preg_match('/^[A-Za-z0-9_-]{16,64}$/', $apikey)) {
            $error = 'API 密钥只能包含字母、数字、下划线和短横线，长度 16-64 位。';
        } else {
            saveSetting('apikey', $apikey);
            $CACHE->update();
            header('Location: ./set.php?mod=cloneset&ok=1');
            exit;
        }
    }
}

$titles = [
    'cron' => '定时任务配置',
    'rewrite' => '伪静态配置',
    'cloneset' => '克隆密钥配置',
];
$title = $titles[$mod];
include __DIR__ . '/head.php';

function setPageEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$ok = isset($_GET['ok']);
$baseUrl = rtrim((string) $siteurl, '/') . '/';
?>
<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10">
    <?php if ($ok): ?>
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm">
            <i class="fa fa-check-circle mr-2"></i>配置保存成功。
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm">
            <i class="fa fa-exclamation-circle mr-2"></i><?php echo setPageEscape($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($mod === 'cron'): ?>
        <?php $cronkey = (string) ($conf['cronkey'] ?? ''); ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fa fa-clock-o text-brand-500 mr-2"></i>本地定时任务</h3>
            </div>
            <div class="p-6 sm:p-8 space-y-6">
                <form method="post" action="./set.php?mod=cron" class="space-y-3">
                    <label class="text-sm font-semibold text-slate-700">监控密钥</label>
                    <input type="text" name="cronkey" value="<?php echo setPageEscape($cronkey); ?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200" placeholder="留空则自动生成">
                    <button type="submit" name="save_cron" value="1" class="h-11 px-6 rounded-xl bg-brand-600 text-white font-medium">保存密钥</button>
                </form>
                <div class="space-y-3 text-sm">
                    <?php
                    $cronUrls = [
                        '支付漏单监控' => $baseUrl . 'cron.php?key=' . rawurlencode($cronkey),
                        '价格监控' => $baseUrl . 'cron.php?key=' . rawurlencode($cronkey) . '&do=pricejk',
                        '订单状态监控' => $baseUrl . 'cron.php?key=' . rawurlencode($cronkey) . '&do=updatestatus',
                        '每日维护任务' => $baseUrl . 'cron.php?key=' . rawurlencode($cronkey) . '&do=daily',
                    ];
                    foreach ($cronUrls as $label => $url):
                    ?>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-slate-700 mb-1"><?php echo setPageEscape($label); ?></div>
                            <code class="break-all text-xs text-slate-600"><?php echo setPageEscape($url); ?></code>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-slate-500">以上任务只调用本站 cron.php；支付、供货等业务接口仍按现有配置工作，不连接任何授权或云控服务器。</p>
            </div>
        </div>
    <?php elseif ($mod === 'rewrite'): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fa fa-random text-brand-500 mr-2"></i>伪静态参考</h3>
            </div>
            <div class="p-6 sm:p-8 space-y-5 text-sm text-slate-700">
                <p>当前项目主要入口使用查询参数路由，默认不依赖 URL 重写。若需要隐藏 index.php，可按服务器类型人工配置并先在测试环境验证。</p>
                <div>
                    <div class="font-bold mb-2">Nginx</div>
                    <pre class="p-4 bg-slate-900 text-slate-100 rounded-xl overflow-x-auto text-xs">location / {
    try_files $uri $uri/ /index.php?$query_string;
}</pre>
                </div>
                <div>
                    <div class="font-bold mb-2">Apache</div>
                    <pre class="p-4 bg-slate-900 text-slate-100 rounded-xl overflow-x-auto text-xs">RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]</pre>
                </div>
            </div>
        </div>
    <?php elseif ($mod === 'cloneset'): ?>
        <?php
        $apikey = (string) ($conf['apikey'] ?? '');
        $cloneKey = $apikey === '' ? '' : md5($password_hash . md5(SYS_KEY) . $apikey);
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800 m-0"><i class="fa fa-key text-brand-500 mr-2"></i>站点克隆密钥</h3>
            </div>
            <div class="p-6 sm:p-8 space-y-6">
                <form method="post" action="./set.php?mod=cloneset" class="space-y-3">
                    <label class="text-sm font-semibold text-slate-700">API 基础密钥</label>
                    <input type="text" name="apikey" value="<?php echo setPageEscape($apikey); ?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 font-mono" placeholder="留空则自动生成">
                    <button type="submit" name="save_clone" value="1" class="h-11 px-6 rounded-xl bg-brand-600 text-white font-medium">保存并重新生成</button>
                </form>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="font-bold text-slate-700 mb-2">对外克隆密钥</div>
                    <code class="break-all text-xs text-slate-600"><?php echo setPageEscape($cloneKey ?: '请先保存 API 基础密钥'); ?></code>
                </div>
                <p class="text-xs text-slate-500">该密钥由本地 SYS_KEY、管理员口令派生值和 API 密钥计算，只用于 api.php?act=clone 的站点间业务克隆，不连接云控服务器。</p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
if (is_file(__DIR__ . '/foot.php')) {
    include __DIR__ . '/foot.php';
}
