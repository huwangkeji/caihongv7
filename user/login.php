<?php
/**
 * 用户登录页（可读离线实现）
 *
 * 替代原 eval/base64/rot13/strrev/hex 多层混淆版本。
 * 登录动作继续复用 user/ajax.php?act=login 与 assets/js/login.js，
 * 不包含远程配置、远程指令、心跳或数据上报逻辑。
 */
$is_defend = true;
require __DIR__ . '/../includes/common.php';

if (isset($_GET['logout'])) {
    if (!checkRefererHost()) {
        http_response_code(403);
        exit('请求来源校验失败');
    }
    setcookie('user_token', '', time() - 604800, '/');
    header('Content-Type: text/html; charset=UTF-8');
    exit("<script>alert('您已成功退出登录');window.location.href='./login.php';</script>");
}

if ($islogin2 == 1) {
    header('Location: ./');
    exit;
}

header('Content-Type: text/html; charset=UTF-8');
$title = '用户登录 - ' . (string) ($conf['sitename'] ?? '');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars((string) ($conf['description'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="shortcut icon" href="<?php echo htmlspecialchars((string) ($conf['default_ico_url'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
    <script src="../assets/V7/tailwindcss.js"></script>
    <link href="/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased flex items-center justify-center p-4 relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-200 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-sky-200 rounded-full blur-3xl opacity-50"></div>
    </div>

    <a href="../" class="hidden lg:flex absolute top-8 left-8 items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-600 font-bold z-20">
        <i class="fa fa-angle-left mr-2"></i>返回首页
    </a>

    <main class="relative z-10 w-full max-w-[430px] bg-white border border-slate-100 rounded-[28px] shadow-2xl p-8 sm:p-10">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-gradient-to-tr from-indigo-500 to-sky-500 rounded-2xl flex items-center justify-center shadow-lg mb-5">
                <i class="fa fa-shield text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800">欢迎登录</h1>
            <p class="mt-2 text-sm text-slate-500">请输入账号信息进入用户中心</p>
        </div>

        <form id="loginForm" class="space-y-5" onsubmit="return false;">
            <div>
                <label for="user" class="block text-sm font-semibold text-slate-700 mb-1.5">账号</label>
                <div class="relative">
                    <i class="fa fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" id="user" name="user" class="w-full h-12 pl-11 pr-4 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="请输入用户名" required autocomplete="username">
                </div>
            </div>

            <div>
                <label for="pass" class="block text-sm font-semibold text-slate-700 mb-1.5">密码</label>
                <div class="relative">
                    <i class="fa fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="password" id="pass" name="pass" class="w-full h-12 pl-11 pr-12 text-sm rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" placeholder="请输入密码" required autocomplete="current-password">
                    <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400" aria-label="显示或隐藏密码">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
            </div>

            <?php if ((int) ($conf['captcha_open_login'] ?? 0) === 1 && (int) ($conf['captcha_open'] ?? 0) >= 1): ?>
                <input type="hidden" name="captcha_type" value="<?php echo (int) $conf['captcha_open']; ?>">
                <?php if ((int) $conf['captcha_open'] >= 2): ?>
                    <input type="hidden" name="appid" value="<?php echo htmlspecialchars((string) ($conf['captcha_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <div id="captcha" class="bg-slate-50 border border-slate-200 rounded-xl p-4 min-h-[72px] flex items-center justify-center">
                    <div id="captcha_text" class="text-sm text-slate-500">正在加载验证码</div>
                    <div id="captcha_wait" class="ml-2"><i class="fa fa-spinner fa-spin text-indigo-500"></i></div>
                </div>
                <div id="captchaform"></div>
            <?php endif; ?>

            <div class="flex items-center justify-between text-sm">
                <a href="findpwd.php" class="font-bold text-indigo-600 hover:text-indigo-800">忘记密码？</a>
                <a href="<?php echo (int) ($conf['user_open'] ?? 0) === 1 ? 'reg.php' : 'regsite.php'; ?>" class="font-bold text-slate-600 hover:text-slate-800">申请账号</a>
            </div>

            <button type="button" id="submit_login" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold shadow-md active:scale-[0.98] transition-transform">
                <i class="fa fa-sign-in mr-2"></i>登录
            </button>
        </form>

        <?php if ((int) ($conf['login_qq'] ?? 0) >= 1 || (int) ($conf['login_wx'] ?? 0) >= 1): ?>
            <div class="mt-7 pt-6 border-t border-slate-100 text-center">
                <div class="text-xs text-slate-400 mb-4">快捷登录</div>
                <div class="flex justify-center gap-4">
                    <?php if ((int) ($conf['login_qq'] ?? 0) >= 1): ?>
                        <button type="button" onclick="connect('qq')" class="w-11 h-11 rounded-full bg-sky-50 text-sky-500"><i class="fa fa-qq"></i></button>
                    <?php endif; ?>
                    <?php if ((int) ($conf['login_wx'] ?? 0) >= 1): ?>
                        <button type="button" onclick="connect('wx')" class="w-11 h-11 rounded-full bg-emerald-50 text-emerald-500"><i class="fa fa-wechat"></i></button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="/assets/vendor/lib.baomitu.com/jquery/1.12.4/jquery.min.js"></script>
    <script src="/assets/vendor/lib.baomitu.com/layer/3.1.1/layer.js"></script>
    <script src="../assets/js/login.js?ver=<?php echo rawurlencode((string) VERSION); ?>"></script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var input = document.getElementById('pass');
        var icon = this.querySelector('i');
        var visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        icon.className = visible ? 'fa fa-eye' : 'fa fa-eye-slash';
    });
    </script>
</body>
</html>
