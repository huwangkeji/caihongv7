<?php
include('../includes/common.php');
$title = '优惠券发放规则管理';
include_once('head.php');
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);

if($islogin==1) {
    // 设置 zid，管理员默认为 0
    $zid = 0;
    $action = isset($_GET['action']) ? $_GET['action'] : 'list';
    
    echo '<div class="w-full max-w-[1200px] mx-auto space-y-6">';

    // 发放规则列表
    if($action == 'list') {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $pageSize = 20;
        $start = ($page - 1) * $pageSize;
        
        // 查询所有规则，不区分zid，因为优惠券规则是全局的
        $total = $DB->getColumn("SELECT COUNT(*) FROM `shua_coupon_rules`");
        $rules = $DB->getAll("SELECT r.*, c.name as coupon_name FROM `shua_coupon_rules` r LEFT JOIN `shua_coupons` c ON r.cid = c.cid ORDER BY rid DESC LIMIT {$start}, {$pageSize}");
        
        $scene_text = ['每日签到', '推广链接', '抽奖商品'];
        
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                    <i class="fa fa-gift text-brand-500 mr-2 text-lg"></i>优惠券发放规则列表
                </h3>
                <a href="?action=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-colors text-sm">
                    <i class="fa fa-plus mr-1.5"></i>添加发放规则
                </a>
            </div>
            
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold text-center w-16">ID</th>
                            <th class="px-6 py-3.5 font-semibold text-center">场景类型</th>
                            <th class="px-6 py-3.5 font-semibold min-w-[150px]">优惠券</th>
                            <th class="px-6 py-3.5 font-semibold">参数配置</th>
                            <th class="px-6 py-3.5 font-semibold text-center">添加时间</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-28">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    <?php
                    foreach($rules as $rule) {
                        $params = json_decode($rule['params'], true);
                        $params_text = '';
                        if($rule['scene'] == 0) {
                            $params_text = '<span class="font-medium text-slate-700">连续签到 <b class="text-brand-600">' . $params['continue_days'] . '</b> 天</span>';
                        } elseif($rule['scene'] == 1) {
                            $params_text = '<span class="font-medium text-slate-700">推广链接注册</span>';
                        } elseif($rule['scene'] == 2) {
                            $params_text = '<span class="font-medium text-slate-700">抽奖商品</span>';
                        }
                        
                        // 处理添加时间，确保能正确显示
                        $add_time = $rule['add_time'];
                        if(!is_numeric($add_time)) {
                            $add_time_display = $add_time;
                        } else {
                            $add_time_display = date('Y-m-d H:i:s', $add_time);
                        }
                        
                        // 场景类型的徽章样式
                        $scene_badge = '';
                        if($rule['scene'] == 0) $scene_badge = '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100">' . $scene_text[$rule['scene']] . '</span>';
                        elseif($rule['scene'] == 1) $scene_badge = '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">' . $scene_text[$rule['scene']] . '</span>';
                        elseif($rule['scene'] == 2) $scene_badge = '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100">' . $scene_text[$rule['scene']] . '</span>';
                        
                        echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">
                            <td class="px-6 py-4 text-center font-bold text-slate-700">' . $rule['rid'] . '</td>
                            <td class="px-6 py-4 text-center">' . $scene_badge . '</td>
                            <td class="px-6 py-4 font-bold text-brand-600 max-w-[200px] truncate" title="'.htmlspecialchars($rule['coupon_name']).'">' . ($rule['coupon_name'] ?: '<span class="text-slate-400 font-normal">未知优惠券</span>') . '</td>
                            <td class="px-6 py-4">' . $params_text . '</td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500">' . $add_time_display . '</td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="?action=edit&rid=' . $rule['rid'] . '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm" title="编辑"><i class="fa fa-pencil"></i></a>
                                    <a href="?action=delete&rid=' . $rule['rid'] . '" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors shadow-sm" title="删除" onclick="return confirm(\'确定要删除此发放规则吗？\');"><i class="fa fa-times"></i></a>
                                </div>
                            </td>
                        </tr>';
                    }
                    
                    if(empty($rules)) {
                        echo '<tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <i class="fa fa-file-text-o text-5xl mb-3 text-slate-200 block"></i>
                                <p class="text-sm font-medium text-slate-500">暂无任何发放规则</p>
                              </td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(!empty($rules)): ?>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
                <ul class="flex items-center gap-1 list-none p-0 m-0">
                    <?php echo str_replace(['<ul class="pagination">', '</ul>', '<li>', '</li>', '<a', '<span', 'class="active"'], ['', '', '<li class="inline-flex">', '</li>', '<a class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium"', '<span class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium"', 'class="px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default"'], page($page, $pageSize, $total, '?page=')); ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    // 添加规则
    elseif($action == 'add') {
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE zid = ? AND active = 1 ORDER BY cid DESC", [$zid]);
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">添加优惠券发放规则</h3>
            </div>
            
            <div class="p-6 sm:p-8">
                <form action="?action=save" method="post" class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="scene">场景类型</label>
                        <select id="scene" name="scene" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                            <option value="0">每日签到</option>
                            <option value="1">推广链接</option>
                            <option value="2">抽奖商品</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="cid">选择要发放的优惠券</label>
                        <select id="cid" name="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                            <option value="">请选择优惠券</option>
                            <?php foreach($coupons as $coupon): ?>
                                <option value="<?php echo $coupon['cid']; ?>"><?php echo htmlspecialchars($coupon['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 动态参数表单组 -->
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl" id="params_container">
                        
                        <div id="sign_params" class="space-y-1.5 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700" for="continue_days">连续签到天数要求</label>
                            <div class="relative">
                                <input type="number" id="continue_days" name="continue_days" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入连续签到天数" min="1">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">天</span>
                            </div>
                        </div>
                        
                        <div id="invite_params" style="display: none;" class="space-y-2 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700">推广链接参数说明</label>
                            <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 flex items-start">
                                <i class="fa fa-info-circle text-brand-500 mt-0.5 mr-2"></i>
                                <span>新用户通过推广链接注册账号后，系统将自动向其发放此优惠券。</span>
                            </div>
                        </div>
                        
                        <div id="lottery_params" style="display: none;" class="space-y-2 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700">抽奖商品参数说明</label>
                            <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 flex items-start">
                                <i class="fa fa-info-circle text-brand-500 mt-0.5 mr-2"></i>
                                <span>此优惠券将作为抽奖活动的有效奖品，请在「抽奖配置」中选择此奖项。</span>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="pt-4 border-t border-slate-50 flex gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-check mr-1.5"></i>确认保存
                        </button>
                        <button type="button" class="px-6 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm" onclick="window.location.href='coupon_rules.php'">
                            返回列表
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        $(function() {
            $("#scene").change(function() {
                var scene = $(this).val();
                $("#sign_params, #invite_params, #lottery_params").hide();
                if(scene == "0") {
                    $("#sign_params").show();
                } else if(scene == "1") {
                    $("#invite_params").show();
                } else if(scene == "2") {
                    $("#lottery_params").show();
                }
            });
        });
        </script>
        <?php
    }
    
    // 编辑规则
    elseif($action == 'edit') {
        $rid = intval($_GET['rid']);
        $rule = $DB->getRow("SELECT * FROM `shua_coupon_rules` WHERE rid = ?", [$rid]);
        
        if(!$rule) {
            showmsg('规则不存在', 'coupon_rules.php');
        }
        
        $params = json_decode($rule['params'], true);
        $coupons = $DB->getAll("SELECT cid, name FROM `shua_coupons` WHERE active = 1 ORDER BY cid DESC");
        
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
                <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">编辑优惠券发放规则</h3>
            </div>
            
            <div class="p-6 sm:p-8">
                <form action="?action=save" method="post" class="space-y-6">
                    <input type="hidden" name="rid" value="<?php echo $rule['rid']; ?>">
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="scene">场景类型</label>
                        <select id="scene" name="scene" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                            <option value="0" <?php echo ($rule['scene'] == 0 ? 'selected' : ''); ?>>每日签到</option>
                            <option value="1" <?php echo ($rule['scene'] == 1 ? 'selected' : ''); ?>>推广链接</option>
                            <option value="2" <?php echo ($rule['scene'] == 2 ? 'selected' : ''); ?>>抽奖商品</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700" for="cid">选择要发放的优惠券</label>
                        <select id="cid" name="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" required>
                            <option value="">请选择优惠券</option>
                            <?php foreach($coupons as $coupon): ?>
                                <option value="<?php echo $coupon['cid']; ?>" <?php echo ($coupon['cid'] == $rule['cid'] ? 'selected' : ''); ?>><?php echo htmlspecialchars($coupon['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- 动态参数表单组 -->
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl" id="params_container">
                        
                        <div id="sign_params" <?php echo ($rule['scene'] != 0 ? 'style="display: none;"' : ''); ?> class="space-y-1.5 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700" for="continue_days">连续签到天数要求</label>
                            <div class="relative">
                                <input type="number" id="continue_days" name="continue_days" class="w-full h-11 pl-4 pr-12 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入连续签到天数" min="1" value="<?php echo isset($params['continue_days']) ? $params['continue_days'] : ''; ?>">
                                <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">天</span>
                            </div>
                        </div>
                        
                        <div id="invite_params" <?php echo ($rule['scene'] != 1 ? 'style="display: none;"' : ''); ?> class="space-y-2 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700">推广链接参数说明</label>
                            <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 flex items-start">
                                <i class="fa fa-info-circle text-brand-500 mt-0.5 mr-2"></i>
                                <span>新用户通过推广链接注册账号后，系统将自动向其发放此优惠券。</span>
                            </div>
                        </div>
                        
                        <div id="lottery_params" <?php echo ($rule['scene'] != 2 ? 'style="display: none;"' : ''); ?> class="space-y-2 animate-[fadeIn_0.3s_ease-out]">
                            <label class="text-sm font-semibold text-slate-700">抽奖商品参数说明</label>
                            <div class="p-3 bg-white border border-slate-200 rounded-lg text-sm text-slate-600 flex items-start">
                                <i class="fa fa-info-circle text-brand-500 mt-0.5 mr-2"></i>
                                <span>此优惠券将作为抽奖活动的有效奖品，请在「抽奖配置」中选择此奖项。</span>
                            </div>
                        </div>
                        
                    </div>
                    
                    <div class="pt-4 border-t border-slate-50 flex gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-check mr-1.5"></i>保存修改
                        </button>
                        <button type="button" class="px-6 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm" onclick="window.location.href='coupon_rules.php'">
                            取消返回
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <script>
        $(function() {
            $("#scene").change(function() {
                var scene = $(this).val();
                $("#sign_params, #invite_params, #lottery_params").hide();
                if(scene == "0") {
                    $("#sign_params").show();
                } else if(scene == "1") {
                    $("#invite_params").show();
                } else if(scene == "2") {
                    $("#lottery_params").show();
                }
            });
        });
        </script>
        <?php
    }
    
    // 保存规则
    elseif($action == 'save') {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $scene = intval($_POST['scene']);
            $cid = intval($_POST['cid']);
            
            // 构建参数
            $params = [];
            if($scene == 0) {
                $params['continue_days'] = intval($_POST['continue_days']);
            }
            
            $params_json = json_encode($params);
            
            // 使用正确的datetime格式保存时间
            $add_time = date('Y-m-d H:i:s');
            
            if(isset($_POST['rid'])) {
                // 编辑
                $rid = intval($_POST['rid']);
                $DB->exec("UPDATE `shua_coupon_rules` SET scene = ?, cid = ?, params = ?, add_time = ? WHERE rid = ?", [$scene, $cid, $params_json, $add_time, $rid]);
            } else {
                // 添加
                $DB->exec("INSERT INTO `shua_coupon_rules` (zid, scene, cid, params, add_time, active) VALUES (?, ?, ?, ?, ?, ?)", [$zid, $scene, $cid, $params_json, $add_time, 1]);
            }
            
            showmsg('规则保存成功', 'coupon_rules.php');
        } else {
            showmsg('非法请求', 'coupon_rules.php');
        }
    }
    
    // 删除规则
    elseif($action == 'delete') {
        $rid = intval($_GET['rid']);
        $DB->exec("DELETE FROM `shua_coupon_rules` WHERE rid = ?", [$rid]);
        showmsg('规则删除成功', 'coupon_rules.php');
    }
    
    echo '</div>'; // End wrapper max-w-[1200px]
} else {
    showmsg('登录失败，可能是密码错误或者账号不存在！', 'login.php');
}

?>
</body>
</html>
