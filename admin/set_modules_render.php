<?php
/**
 * 后台「系统设置」配置驱动模块的保存与渲染
 *
 * 配合 admin/set_modules.php 使用：由 set.php 先完成登录与权限校验，再调用这里的
 * saveSetModule() / renderSetModule()，即可为 20 多个设置模块提供统一的
 * 「表单渲染 + 保存 + 回显」能力，无需每个模块单独写页面。
 */

/**
 * 保存某个配置模块的所有字段到 pre_config
 *
 * @param string $mod
 * @param array  $cfg  模块定义（见 set_modules.php）
 * @return bool  是否执行了保存
 */
function saveSetModule($mod, $cfg)
{
    global $DB, $CACHE;

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['save_set_module'])) {
        return false;
    }
    if (!checkRefererHost()) {
        http_response_code(403);
        exit('请求来源校验失败');
    }

    foreach ($cfg['fields'] as $f) {
        $k = $f['k'];
        if ($f['type'] === 'switch') {
            $v = isset($_POST[$k]) ? '1' : '0';
        } else {
            $v = isset($_POST[$k]) ? trim((string) $_POST[$k]) : '';
        }
        saveSetting($k, $v);
    }
    $CACHE->update();
    header('Location: ./set.php?mod=' . urlencode($mod) . '&ok=1');
    exit;
}

/**
 * 渲染某个配置模块的表单
 *
 * @param string $mod
 * @param array  $cfg
 * @param bool   $ok     是否刚保存成功
 * @param string $error  错误提示
 */
function renderSetModule($mod, $cfg, $ok = false, $error = '')
{
    global $conf;

    $title = $cfg['title'];
    include __DIR__ . '/head.php';
    ?>
    <div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10">

        <?php if ($ok): ?>
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 text-sm flex items-center shadow-sm">
                <i class="fa fa-check-circle mr-2 text-lg"></i> 配置保存成功！
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm flex items-center shadow-sm">
                <i class="fa fa-exclamation-circle mr-2 text-lg"></i> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa <?php echo htmlspecialchars($cfg['icon'] ?: 'fa-cog', ENT_QUOTES, 'UTF-8'); ?> text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0"><?php echo htmlspecialchars($cfg['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            </div>
            <div class="p-6 sm:p-8">
                <?php if (!empty($cfg['desc'])): ?>
                    <p class="mb-6 text-sm text-slate-500 leading-6 bg-slate-50 border border-slate-100 rounded-xl px-4 py-3">
                        <?php echo htmlspecialchars($cfg['desc'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <form method="post" action="./set.php?mod=<?php echo htmlspecialchars($mod, ENT_QUOTES, 'UTF-8'); ?>" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($cfg['fields'] as $f):
                            $k    = $f['k'];
                            $val  = isset($conf[$k]) ? (string) $conf[$k] : '';
                            $ph   = isset($f['ph']) ? $f['ph'] : '';
                            $note = isset($f['note']) ? $f['note'] : '';
                            $type = $f['type'];
                            // 纵向占满一行的控件
                            $full = in_array($type, array('textarea'), true);
                            if ($full) {
                                echo '</div><div class="space-y-1.5">';
                            } else {
                                echo '<div class="space-y-1.5">';
                            }
                        ?>
                            <label class="text-sm font-semibold text-slate-700"><?php echo htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8'); ?></label>
                            <?php if ($type === 'textarea'): ?>
                                <textarea name="<?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?>" rows="<?php echo intval(isset($f['rows']) ? $f['rows'] : 4); ?>"
                                          class="w-full px-4 py-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white font-mono"
                                          placeholder="<?php echo htmlspecialchars($ph, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <?php elseif ($type === 'select'): ?>
                                <select name="<?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="w-full h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer">
                                    <?php
                                    $options = $f['options'];
                                    if ($options === '__TEMPLATE_LIST__') {
                                        $options = array();
                                        foreach (glob(ROOT . 'template/*', GLOB_ONLYDIR) as $dir) {
                                            $name = basename($dir);
                                            $options[$name] = $name;
                                        }
                                    }
                                    foreach ($options as $ov => $ot): ?>
                                        <option value="<?php echo htmlspecialchars((string) $ov, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((string) $ov === $val) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars((string) $ot, ENT_QUOTES, 'UTF-8'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            <?php elseif ($type === 'switch'): ?>
                                <label class="flex items-center space-x-2.5 cursor-pointer select-none p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition-colors">
                                    <input type="checkbox" name="<?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?>" value="1"
                                           class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" <?php echo ($val === '1') ? 'checked' : ''; ?>>
                                    <span class="text-sm text-slate-600">开启</span>
                                </label>
                            <?php else: ?>
                                <input type="<?php echo $type === 'number' ? 'number' : ($type === 'password' ? 'password' : 'text'); ?>"
                                       name="<?php echo htmlspecialchars($k, ENT_QUOTES, 'UTF-8'); ?>"
                                       value="<?php echo htmlspecialchars($val, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="<?php echo htmlspecialchars($ph, ENT_QUOTES, 'UTF-8'); ?>"
                                       class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white">
                            <?php endif; ?>
                            <?php if ($note): ?>
                                <p class="text-xs text-slate-400"><?php echo htmlspecialchars($note, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        <?php
                            echo '</div>';
                            if ($full) {
                                echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                            }
                        endforeach; ?>
                    </div>

                    <div class="pt-4">
                        <button type="submit" name="save_set_module" value="1"
                                class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-save mr-1.5"></i>保存配置
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/foot.php';
}

/**
 * 渲染「防CC攻击设置」入口页（真正的配置在 cc_protect.php）
 */
function renderDefendModule()
{
    global $conf;
    $title = '防CC攻击设置';
    include __DIR__ . '/head.php';
    $enabled = !empty($conf['cc_protect_enabled']);
    ?>
    <div class="w-full max-w-[800px] mx-auto space-y-4 pb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center mb-4">
                <i class="fa fa-shield text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">防CC攻击设置</h3>
            </div>
            <p class="text-sm text-slate-500 leading-6 mb-4">
                CC 防护的阈值、封禁时长、跳转地址等参数已迁移到独立页面维护，
                当前防护状态：<strong class="<?php echo $enabled ? 'text-emerald-600' : 'text-slate-500'; ?>"><?php echo $enabled ? '已启用' : '未启用'; ?></strong>。
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="./cc_protect.php" class="h-10 px-5 inline-flex items-center rounded-xl bg-brand-600 text-white text-sm font-medium">前往 CC 防护配置</a>
                <a href="./index.php" class="h-10 px-5 inline-flex items-center rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium">返回后台首页</a>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/foot.php';
}

/**
 * 渲染「扫码登录」入口页
 */
function renderBindModule()
{
    global $conf;
    $title = '扫码登录';
    include __DIR__ . '/head.php';
    ?>
    <div class="w-full max-w-[800px] mx-auto space-y-4 pb-10">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center mb-4">
                <i class="fa fa-qrcode text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-lg font-bold text-slate-800 m-0">扫码登录</h3>
            </div>
            <p class="text-sm text-slate-500 leading-6 mb-4">
                绑定管理员的 QQ / 微信后即可使用扫码快捷登录后台。绑定前请先到
                <a href="./set.php?mod=oauth" class="text-brand-600 hover:underline">快捷登录配置</a>
                填写 AppID / AppKey 并开启对应开关。
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="./bind.php?type=qq" class="h-10 px-5 inline-flex items-center rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium"><i class="fa fa-qq mr-2"></i>绑定 QQ</a>
                <a href="./bind.php?type=wx" class="h-10 px-5 inline-flex items-center rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium"><i class="fa fa-wechat mr-2"></i>绑定微信</a>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/foot.php';
}
