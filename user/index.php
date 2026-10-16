<?php
/**
 * 用户中心首页（可读离线实现）
 *
 * 替代原 eval/base64/rot13/strrev/hex 多层混淆版本。
 * 页面仅读取本地用户与配置数据，不拉取远程配置、不接收远程指令，
 * 不执行心跳或数据上报。
 */
require __DIR__ . '/../includes/common.php';

if ($islogin2 != 1 || !is_array($userrow)) {
    header('Location: ./login.php');
    exit;
}
if ((int) ($userrow['status'] ?? 0) !== 1) {
    setcookie('user_token', '', time() - 604800, '/');
    exit('账号已被禁用');
}

$siteName = (string) ($conf['sitename'] ?? '用户中心');
$nickname = trim((string) ($userrow['nickname'] ?? ''));
if ($nickname === '') {
    $nickname = (string) ($userrow['user'] ?? '用户');
}
$faceimg = trim((string) ($userrow['faceimg'] ?? ''));
if ($faceimg === '') {
    $faceimg = '/assets/img/user.png';
}
$power = (int) ($userrow['power'] ?? 0);
$powerName = $power >= 2 ? '高级代理' : ($power === 1 ? '普通代理' : '普通会员');
$isExpired = $power > 0
    && (int) ($conf['fenzhan_expiry'] ?? 0) > 0
    && !empty($userrow['endtime'])
    && (string) $userrow['endtime'] < (string) $date;

function userIndexEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>用户中心 - <?php echo userIndexEscape($siteName); ?></title>
    <script src="../assets/V7/tailwindcss.js"></script>
    <link href="/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 antialiased">
    <div class="max-w-5xl mx-auto px-4 py-6 sm:py-10 space-y-6">
        <?php if ($isExpired): ?>
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm">
                <i class="fa fa-exclamation-triangle mr-2"></i>您的分站已到期，请联系管理员续费。
            </div>
        <?php endif; ?>

        <section class="bg-gradient-to-r from-indigo-600 to-sky-500 rounded-3xl shadow-xl text-white p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <img src="<?php echo userIndexEscape($faceimg); ?>" alt="头像" class="w-16 h-16 rounded-2xl object-cover bg-white/20 border border-white/30">
                    <div>
                        <div class="text-sm text-white/75">欢迎回来</div>
                        <h1 class="text-2xl font-extrabold mt-1"><?php echo userIndexEscape($nickname); ?></h1>
                        <div class="mt-2 text-xs text-white/80">UID: <?php echo (int) $userrow['zid']; ?> · <?php echo userIndexEscape($powerName); ?></div>
                    </div>
                </div>
                <div class="sm:text-right">
                    <div class="text-sm text-white/75">账户余额</div>
                    <div class="text-3xl font-extrabold mt-1">¥<?php echo number_format((float) ($userrow['rmb'] ?? 0), 2); ?></div>
                    <a href="recharge.php" class="inline-flex mt-3 px-4 py-2 rounded-xl bg-white text-indigo-600 font-bold text-sm">立即充值</a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php
            $quickLinks = [
                ['list.php', 'fa-list-alt', '订单管理', '查看与处理订单'],
                ['record.php', 'fa-credit-card', '收支明细', '查看资金流水'],
                ['message.php', 'fa-bell-o', '消息通知', '查看站内消息'],
                ['uset.php?mod=user', 'fa-cog', '账号设置', '资料与安全设置'],
            ];
            foreach ($quickLinks as $link):
            ?>
                <a href="<?php echo userIndexEscape($link[0]); ?>" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 hover:shadow-md transition-shadow">
                    <i class="fa <?php echo userIndexEscape($link[1]); ?> text-indigo-500 text-xl"></i>
                    <div class="font-bold text-slate-800 mt-3"><?php echo userIndexEscape($link[2]); ?></div>
                    <div class="text-xs text-slate-400 mt-1"><?php echo userIndexEscape($link[3]); ?></div>
                </a>
            <?php endforeach; ?>
        </section>

        <?php if ($power > 0): ?>
            <section class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="font-bold text-slate-800"><i class="fa fa-globe text-indigo-500 mr-2"></i>站点管理</h2>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 p-5">
                    <?php
                    $siteLinks = [
                        ['siteinfo.php', '站点信息'],
                        ['classlist.php', '分类管理'],
                        ['shoplist.php', '商品管理'],
                        ['sitelist.php', '分站列表'],
                        ['userlist.php', '用户列表'],
                        ['rank.php', '销售排行'],
                    ];
                    foreach ($siteLinks as $link):
                        if ($power < 2 && in_array($link[0], ['sitelist.php', 'userlist.php'], true)) {
                            continue;
                        }
                    ?>
                        <a href="<?php echo userIndexEscape($link[0]); ?>" class="px-4 py-3 rounded-xl bg-slate-50 text-slate-700 text-sm font-semibold text-center hover:bg-indigo-50 hover:text-indigo-700">
                            <?php echo userIndexEscape($link[1]); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php else: ?>
            <a href="regsite.php" class="block bg-white rounded-3xl border border-indigo-100 shadow-sm p-6 text-center text-indigo-600 font-bold">
                <i class="fa fa-diamond mr-2"></i>申请开通分站
            </a>
        <?php endif; ?>

        <section class="grid sm:grid-cols-3 gap-4">
            <a href="workorder.php" class="bg-white rounded-2xl border border-slate-100 p-5 font-bold text-slate-700"><i class="fa fa-check-square-o text-sky-500 mr-2"></i>我的工单</a>
            <a href="faq.php" class="bg-white rounded-2xl border border-slate-100 p-5 font-bold text-slate-700"><i class="fa fa-question-circle text-sky-500 mr-2"></i>常见问题</a>
            <a href="login.php?logout=1" class="bg-white rounded-2xl border border-slate-100 p-5 font-bold text-rose-600"><i class="fa fa-sign-out mr-2"></i>退出登录</a>
        </section>

        <footer class="text-center text-xs text-slate-400 py-4">© <?php echo userIndexEscape($siteName); ?></footer>
    </div>
</body>
</html>
