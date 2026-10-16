<?php
/**
 * 自动同步设置
 *
 * 【背景】后台菜单「系统设置 → 自动同步设置」指向本文件，原文件不存在导致 404。
 *
 * 【现状】shua_sync_config 表结构完整（按对接站点保存同步开关、间隔、字段同步选项等），
 * 但当前版本没有同步执行器（无人调用该表）。本页先把「配置」这一半做成可用的：
 * 为每个对接站点维护同步参数，并明确提示同步执行逻辑仍需另行实现，避免伪造功能。
 */
include '../includes/common.php';
$title = '自动同步设置';
include './head.php';
if ($islogin != 1) {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission('set', 1);

$message = '';
$messageType = ''; // ok | err

// ---------- 表结构探测（某些环境可能未建表） ----------
$tableReady = false;
try {
    $tableReady = (bool) $DB->getColumn("SHOW TABLES LIKE 'pre_sync_config'");
} catch (Exception $e) {
    $tableReady = false;
}

// ---------- 保存 ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_sync'])) {
    if (!checkRefererHost()) {
        http_response_code(403);
        exit('请求来源校验失败');
    }
    if (!$tableReady) {
        $message = '同步配置表不存在，无法保存。';
        $messageType = 'err';
    } else {
        $shequ_id   = trim((string) ($_POST['shequ_id'] ?? ''));
        $shequ_type = trim((string) ($_POST['shequ_type'] ?? ''));
        if ($shequ_id === '') {
            $message = '请选择对接站点。';
            $messageType = 'err';
        } else {
            $intFields = [
                'status', 'sync_interval', 'sync_limit', 'auto_update', 'delete_rule',
                'sync_class', 'sync_sort', 'sync_goods_sort', 'sync_log', 'sync_name',
                'sync_price', 'sync_cost', 'sync_desc', 'sync_image', 'sync_workorder',
                'add_class', 'add_goods', 'markup_template',
            ];
            $data = [':shequ_id' => $shequ_id, ':shequ_type' => $shequ_type];
            foreach ($intFields as $k) {
                $data[':' . $k] = isset($_POST[$k]) ? (int) $_POST[$k] : 0;
            }
            // 数值范围保护：间隔 1~1440 分钟，每批 1~1000 条
            $data[':sync_interval'] = min(1440, max(1, $data[':sync_interval']));
            $data[':sync_limit']    = min(1000, max(1, $data[':sync_limit']));

            $cols = array_merge(['shequ_id', 'shequ_type'], $intFields);
            $sql = "REPLACE INTO pre_sync_config (`" . implode('`,`', $cols) . "`) VALUES (:" . implode(',:', $cols) . ")";
            if ($DB->exec($sql, $data) !== false) {
                $CACHE->update();
                $message = '同步配置已保存（站点ID：' . htmlspecialchars($shequ_id, ENT_QUOTES, 'UTF-8') . '）。';
                $messageType = 'ok';
            } else {
                $message = '保存失败：数据库写入异常。';
                $messageType = 'err';
            }
        }
    }
}

// ---------- 删除 ----------
if (isset($_GET['my']) && $_GET['my'] === 'del' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($tableReady && $DB->exec("DELETE FROM pre_sync_config WHERE id=:id limit 1", [':id' => $id]) > 0) {
        $CACHE->update();
        $message = '已删除该同步配置。';
        $messageType = 'ok';
    }
}

// ---------- 编辑 ----------
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$edit   = null;
if ($editId && $tableReady) {
    $edit = $DB->getRow("SELECT * FROM pre_sync_config WHERE id=:id limit 1", [':id' => $editId]);
}

// ---------- 数据 ----------
$shequs = $DB->getAll("SELECT * FROM pre_shequ ORDER BY id ASC");
$prices = $DB->getAll("SELECT * FROM pre_price ORDER BY id ASC");
$configs = [];
if ($tableReady) {
    $configs = $DB->getAll("SELECT * FROM pre_sync_config ORDER BY id ASC");
}
$configMap = [];
foreach ($configs as $c) {
    $configMap[$c['shequ_id']] = $c;
}

$toggles = [
    'status'          => '启用同步',
    'auto_update'     => '自动更新已存在商品',
    'delete_rule'     => '同步时删除本地多余商品',
    'sync_class'      => '同步分类',
    'sync_sort'       => '同步分类排序',
    'sync_goods_sort' => '同步商品排序',
    'sync_log'        => '记录同步日志',
    'sync_name'       => '同步商品名称',
    'sync_price'      => '同步商品价格',
    'sync_cost'       => '同步成本价',
    'sync_desc'       => '同步商品描述',
    'sync_image'      => '同步商品图片',
    'sync_workorder'  => '同步售后说明',
    'add_class'       => '自动新增分类',
    'add_goods'       => '自动新增商品',
];
?>
<div class="w-full max-w-[1100px] mx-auto space-y-6 pb-10">

    <?php if ($message): ?>
        <div class="p-4 <?php echo $messageType === 'ok' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700'; ?> border rounded-xl text-sm flex items-center shadow-sm">
            <i class="fa <?php echo $messageType === 'ok' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2 text-lg"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm leading-6">
        <i class="fa fa-info-circle mr-1"></i>
        本页维护的是<strong>同步参数</strong>。当前版本尚未包含定时同步执行器（无人读取本表去真正拉取商品），
        参数保存后需要配套实现同步任务才会生效。
    </div>

    <!-- 已有配置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-list text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">已配置的对接站点</h3>
        </div>
        <div class="p-6">
            <?php if (empty($configs)): ?>
                <p class="text-sm text-slate-500">还没有任何同步配置，请在下方新增。</p>
            <?php else: ?>
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                        <thead class="text-xs uppercase bg-slate-50 text-slate-500">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">对接站点</th>
                                <th class="px-4 py-3">类型</th>
                                <th class="px-4 py-3">状态</th>
                                <th class="px-4 py-3">间隔(分)</th>
                                <th class="px-4 py-3">每批条数</th>
                                <th class="px-4 py-3">加价模板</th>
                                <th class="px-4 py-3">更新时间</th>
                                <th class="px-4 py-3">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($configs as $r): ?>
                                <tr class="hover:bg-slate-50/60">
                                    <td class="px-4 py-3"><?php echo intval($r['id']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars((string) $r['shequ_id']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars((string) $r['shequ_type']); ?></td>
                                    <td class="px-4 py-3">
                                        <?php echo $r['status'] ? '<span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 text-xs">已启用</span>' : '<span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 text-xs">已停用</span>'; ?>
                                    </td>
                                    <td class="px-4 py-3"><?php echo intval($r['sync_interval']); ?></td>
                                    <td class="px-4 py-3"><?php echo intval($r['sync_limit']); ?></td>
                                    <td class="px-4 py-3"><?php echo intval($r['markup_template']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars((string) $r['uptime']); ?></td>
                                    <td class="px-4 py-3">
                                        <a href="./cx-synchronization.php?id=<?php echo intval($r['id']); ?>" class="text-brand-600 hover:underline">编辑</a>
                                        <span class="text-slate-300 mx-1">|</span>
                                        <a href="./cx-synchronization.php?my=del&id=<?php echo intval($r['id']); ?>"
                                           onclick="return confirm('确定删除该同步配置吗？');"
                                           class="text-rose-500 hover:underline">删除</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- 新增 / 编辑 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-refresh text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0"><?php echo $edit ? '编辑同步配置 #' . intval($edit['id']) : '新增同步配置'; ?></h3>
        </div>
        <div class="p-6 sm:p-8">
            <?php if (empty($shequs)): ?>
                <p class="text-sm text-slate-500">还没有添加对接站点，请先到
                    <a href="./shequlist.php" class="text-brand-600 hover:underline">对接站点管理</a> 添加。</p>
            <?php elseif (!$tableReady): ?>
                <p class="text-sm text-rose-600">同步配置表不存在，无法新增配置。</p>
            <?php else: ?>
                <form method="post" action="./cx-synchronization.php" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">对接站点</label>
                            <select name="shequ_id" id="shequ_id" class="w-full h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer">
                                <?php foreach ($shequs as $s): ?>
                                    <option value="<?php echo htmlspecialchars((string) $s['id']); ?>"
                                            data-type="<?php echo htmlspecialchars((string) $s['type']); ?>"
                                        <?php echo ($edit && (string) $edit['shequ_id'] === (string) $s['id']) ? 'selected' : ''; ?>>
                                        #<?php echo intval($s['id']); ?> <?php echo htmlspecialchars((string) $s['url']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">站点类型（自动带入）</label>
                            <input type="text" name="shequ_type" id="shequ_type" readonly
                                   value="<?php echo htmlspecialchars((string) ($edit['shequ_type'] ?? ($shequs[0]['type'] ?? ''))); ?>"
                                   class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">同步间隔（分钟）</label>
                            <input type="number" name="sync_interval" min="1" max="1440"
                                   value="<?php echo intval($edit['sync_interval'] ?? 5); ?>"
                                   class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">每次同步条数</label>
                            <input type="number" name="sync_limit" min="1" max="1000"
                                   value="<?php echo intval($edit['sync_limit'] ?? 50); ?>"
                                   class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">应用加价模板</label>
                            <select name="markup_template" class="w-full h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer">
                                <option value="0">不使用加价模板</option>
                                <?php foreach ($prices as $p): ?>
                                    <option value="<?php echo intval($p['id']); ?>" <?php echo ($edit && intval($edit['markup_template']) === intval($p['id'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars((string) $p['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-sm font-bold text-slate-700 mb-4">同步选项</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <?php foreach ($toggles as $k => $label): ?>
                                <label class="flex items-center space-x-2.5 cursor-pointer select-none p-3 rounded-xl border border-slate-200 hover:border-brand-300 transition-colors">
                                    <input type="checkbox" name="<?php echo $k; ?>" value="1"
                                           class="w-4 h-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                        <?php echo (isset($edit[$k]) ? intval($edit[$k]) : ($k === 'status' || strpos($k, 'sync_') === 0 || strpos($k, 'add_') === 0 ? 1 : 0)) ? 'checked' : ''; ?>>
                                    <span class="text-sm text-slate-600"><?php echo htmlspecialchars($label); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pt-4 flex flex-wrap gap-3">
                        <button type="submit" name="save_sync" value="1"
                                class="px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-save mr-1.5"></i>保存配置
                        </button>
                        <?php if ($edit): ?>
                            <a href="./cx-synchronization.php" class="h-11 px-6 inline-flex items-center rounded-xl bg-white border border-slate-200 text-slate-600 text-sm font-medium">取消编辑</a>
                        <?php endif; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
// 选择对接站点时自动带入类型
(function () {
    var sel = document.getElementById('shequ_id');
    var typeInput = document.getElementById('shequ_type');
    if (!sel || !typeInput) { return; }
    sel.addEventListener('change', function () {
        var opt = sel.options[sel.selectedIndex];
        typeInput.value = opt ? (opt.getAttribute('data-type') || '') : '';
    });
})();
</script>
<?php
if (file_exists(__DIR__ . '/foot.php')) {
    include __DIR__ . '/foot.php';
}
