<?php

include("../includes/common.php");
$title = '指定地区加价';
include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('price', 1);
if ($islogin == 1) {
} else {
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

// 自动创建数据库表和字段
function autoCreateTables() {
    global $DB;
    
    // 检查并创建地区加价规则表
    $check_rules_table = $DB->getColumn("SHOW TABLES LIKE 'pre_region_price_rules'");
    if (!$check_rules_table) {
        $sql_rules = "CREATE TABLE IF NOT EXISTS `pre_region_price_rules` (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
            `region_name` varchar(100) NOT NULL COMMENT '地区名称',
            `region_keywords` text NOT NULL COMMENT '地区关键词，逗号分隔',
            `add_price_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '加价类型：1固定金额，2百分比',
            `add_price_value` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加价数值',
            `min_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '最低价格限制',
            `max_price` decimal(10,2) NOT NULL DEFAULT '999999.99' COMMENT '最高价格限制',
            `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用，0禁用',
            `sort_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
            `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
            `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
            PRIMARY KEY (`id`),
            KEY `idx_status` (`status`),
            KEY `idx_sort_order` (`sort_order`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区加价规则表'";
        $DB->exec($sql_rules);
    }
    
    // 检查并创建价格计算日志表
    $check_logs_table = $DB->getColumn("SHOW TABLES LIKE 'pre_region_price_logs'");
    if (!$check_logs_table) {
        $sql_logs = "CREATE TABLE IF NOT EXISTS `pre_region_price_logs` (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
            `order_id` varchar(50) NOT NULL COMMENT '订单号',
            `tid` int(11) NOT NULL COMMENT '商品ID',
            `tool_name` varchar(255) NOT NULL COMMENT '商品名称',
            `original_price` decimal(10,2) NOT NULL COMMENT '原价',
            `final_price` decimal(10,2) NOT NULL COMMENT '最终价格',
            `add_price_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加价金额',
            `region_id` int(11) DEFAULT NULL COMMENT '匹配的规则ID',
            `region_name` varchar(100) DEFAULT NULL COMMENT '地区名称',
            `add_price_type` tinyint(1) DEFAULT NULL COMMENT '加价类型',
            `add_price_value` decimal(10,2) DEFAULT NULL COMMENT '加价数值',
            `address` text COMMENT '收货地址',
            `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
            PRIMARY KEY (`id`),
            KEY `idx_order_id` (`order_id`),
            KEY `idx_tid` (`tid`),
            KEY `idx_region_id` (`region_id`),
            KEY `idx_create_time` (`create_time`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='地区加价计算日志表'";
        $DB->exec($sql_logs);
    }
    
    // 检查并添加pre_pay表的address字段
    $check_pay_address = $DB->getColumn("SHOW COLUMNS FROM `pre_pay` LIKE 'address'");
    if (!$check_pay_address) {
        $DB->exec("ALTER TABLE `pre_pay` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input`");
    }
    
    // 检查并添加pre_orders表的address字段
    $check_orders_address = $DB->getColumn("SHOW COLUMNS FROM `pre_orders` LIKE 'address'");
    if (!$check_orders_address) {
        $DB->exec("ALTER TABLE `pre_orders` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input5`");
    }
    
    // 检查并添加pre_cart表的address字段
    $check_cart_address = $DB->getColumn("SHOW COLUMNS FROM `pre_cart` LIKE 'address'");
    if (!$check_cart_address) {
        $DB->exec("ALTER TABLE `pre_cart` ADD COLUMN `address` VARCHAR(500) DEFAULT '' COMMENT '收货地址' AFTER `input`");
    }
}

// 执行自动创建
autoCreateTables();

use lib\RegionPrice;

// 处理类不存在的降级，防止系统崩溃
if (!class_exists('lib\RegionPrice')) {
    exit("<div style='padding:20px;text-align:center;font-family:sans-serif;'><h3>致命错误：找不到核心依赖类</h3><p>系统缺少 `lib\\RegionPrice` 类库。请确保底层扩展文件已正确上传并注册到自动加载中。</p></div>");
}

$regionPrice = new RegionPrice();

$my = isset($_GET['my']) ? $_GET['my'] : 'list';

if ($my == 'add') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'region_name' => trim($_POST['region_name']),
            'region_keywords' => trim($_POST['region_keywords']),
            'add_price_type' => intval($_POST['add_price_type']),
            'add_price_value' => floatval($_POST['add_price_value']),
            'min_price' => floatval($_POST['min_price']),
            'max_price' => floatval($_POST['max_price']),
            'status' => intval($_POST['status']),
            'sort_order' => intval($_POST['sort_order'])
        ];

        if (empty($data['region_name']) || empty($data['region_keywords'])) {
            exit("<script language='javascript'>alert('地区名称和关键词不能为空！');history.go(-1);</script>");
        }

        if ($regionPrice->addRule($data) !== false) {
            // 清除缓存
            $regionPrice->clearCache();
            exit("<script language='javascript'>alert('添加成功！');window.location.href='region_price.php';</script>");
        } else {
            exit("<script language='javascript'>alert('添加失败！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'edit') {
    $id = intval($_GET['id']);
    $rule = $regionPrice->getRuleById($id);

    if (!$rule) {
        exit("<script language='javascript'>alert('规则不存在！');window.location.href='region_price.php';</script>");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = [
            'region_name' => trim($_POST['region_name']),
            'region_keywords' => trim($_POST['region_keywords']),
            'add_price_type' => intval($_POST['add_price_type']),
            'add_price_value' => floatval($_POST['add_price_value']),
            'min_price' => floatval($_POST['min_price']),
            'max_price' => floatval($_POST['max_price']),
            'status' => intval($_POST['status']),
            'sort_order' => intval($_POST['sort_order'])
        ];

        if (empty($data['region_name']) || empty($data['region_keywords'])) {
            exit("<script language='javascript'>alert('地区名称和关键词不能为空！');history.go(-1);</script>");
        }

        if ($regionPrice->updateRule($id, $data) !== false) {
            // 清除缓存
            $regionPrice->clearCache();
            exit("<script language='javascript'>alert('修改成功！');window.location.href='region_price.php';</script>");
        } else {
            exit("<script language='javascript'>alert('修改失败！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'delete') {
    $id = intval($_GET['id']);
    if ($regionPrice->deleteRule($id) !== false) {
        exit("<script language='javascript'>alert('删除成功！');window.location.href='region_price.php';</script>");
    } else {
        exit("<script language='javascript'>alert('删除失败！');history.go(-1);</script>");
    }
} elseif ($my == 'import') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] == 0) {
            $file = $_FILES['import_file']['tmp_name'];
            $content = file_get_contents($file);

            if (pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION) == 'json') {
                $rules_data = json_decode($content, true);
            } else {
                $lines = explode("\n", $content);
                $rules_data = [];
                $headers = [];
                foreach ($lines as $index => $line) {
                    $line = trim($line);
                    if (empty($line)) continue;

                    $fields = explode(',', $line);
                    if ($index == 0) {
                        $headers = array_map('trim', $fields);
                    } else {
                        $row = [];
                        foreach ($headers as $i => $header) {
                            $row[$header] = isset($fields[$i]) ? trim($fields[$i]) : '';
                        }
                        $rules_data[] = $row;
                    }
                }
            }

            if (!empty($rules_data) && is_array($rules_data)) {
                $result = $regionPrice->importRules($rules_data);
                exit("<script language='javascript'>alert('导入完成！成功{$result['success']}条，失败{$result['error']}条');window.location.href='region_price.php';</script>");
            } else {
                exit("<script language='javascript'>alert('文件格式错误或内容为空！');history.go(-1);</script>");
            }
        } else {
            exit("<script language='javascript'>alert('请选择要导入的文件！');history.go(-1);</script>");
        }
    }
} elseif ($my == 'export') {
    $status = isset($_GET['status']) ? intval($_GET['status']) : null;
    $rules = $regionPrice->exportRules($status);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="region_price_rules_' . date('YmdHis') . '.json"');
    echo json_encode($rules, JSON_UNESCAPED_UNICODE);
    exit;
} elseif ($my == 'logs') {
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $pagesize = 20;
    $offset = ($page - 1) * $pagesize;

    $filters = [];
    if (!empty($_GET['order_id'])) {
        $filters['order_id'] = $_GET['order_id'];
    }
    if (!empty($_GET['tid'])) {
        $filters['tid'] = intval($_GET['tid']);
    }
    if (!empty($_GET['region_id'])) {
        $filters['region_id'] = intval($_GET['region_id']);
    }
    if (!empty($_GET['start_time'])) {
        $filters['start_time'] = $_GET['start_time'];
    }
    if (!empty($_GET['end_time'])) {
        $filters['end_time'] = $_GET['end_time'];
    }

    if (isset($_GET['clear_logs'])) {
        $before_date = !empty($_GET['before_date']) ? $_GET['before_date'] : null;
        $regionPrice->clearLogs($before_date);
        exit("<script language='javascript'>alert('日志清理成功！');window.location.href='region_price.php?my=logs';</script>");
    }

    $logs = $regionPrice->getLogs($pagesize, $offset, $filters);
    $total = $logs['total'];
    $log_list = $logs['list'];
    $total_pages = ceil($total / $pagesize);
}
?>

<div class="w-full max-w-[1400px] mx-auto space-y-6 pb-10">

    <?php if ($my == 'list') { ?>
    <!-- 规则列表页 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-map-marker text-brand-500 mr-2 text-lg"></i>指定地区加价规则管理
            </h3>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="region_price.php?my=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm">
                    <i class="fa fa-plus mr-1.5"></i>添加规则
                </a>
                <a href="region_price.php?my=import" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200 text-sm">
                    <i class="fa fa-upload mr-1.5"></i>批量导入
                </a>
                <a href="region_price.php?my=export" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-sky-50 text-sky-600 font-medium hover:bg-sky-500 hover:text-white shadow-sm transition-colors text-sm">
                    <i class="fa fa-download mr-1.5"></i>导出规则
                </a>
                <a href="region_price.php?my=logs" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-amber-50 text-amber-600 font-medium hover:bg-amber-500 hover:text-white shadow-sm transition-colors text-sm">
                    <i class="fa fa-list-alt mr-1.5"></i>查看日志
                </a>
            </div>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-20">ID</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[150px]">地区名称</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28">加价类型</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32 text-rose-500">加价数值</th>
                        <th class="px-6 py-3.5 font-semibold text-center min-w-[200px]">生效价格区间 (￥)</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">状态</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">排序</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-36">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php
                    $rules = $regionPrice->getRules();
                    if (!empty($rules)) {
                        foreach ($rules as $rule) {
                            $add_type_text = $rule['add_price_type'] == 1 
                                ? '<span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[11px] font-medium border border-slate-200">固定金额</span>' 
                                : '<span class="px-2 py-0.5 rounded bg-sky-50 text-sky-600 text-[11px] font-medium border border-sky-100">百分比</span>';
                            
                            $add_value_text = $rule['add_price_type'] == 1 
                                ? '<span class="text-xs mr-0.5 text-slate-400">¥</span>' . $rule['add_price_value'] 
                                : $rule['add_price_value'] . '<span class="text-xs ml-0.5 text-slate-400">%</span>';
                                
                            $status_text = $rule['status'] == 1 
                                ? '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">启用</span>' 
                                : '<span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-500 border border-rose-100">禁用</span>';
                    ?>
                    <tr class="hover:bg-brand-50/40 transition-colors duration-200">
                        <td class="px-6 py-4 text-center font-bold text-slate-700"><?php echo $rule['id']; ?></td>
                        <td class="px-6 py-4 font-bold text-slate-800"><?php echo htmlspecialchars($rule['region_name']); ?></td>
                        <td class="px-6 py-4 text-center"><?php echo $add_type_text; ?></td>
                        <td class="px-6 py-4 text-center font-mono font-bold text-rose-500 text-base"><?php echo $add_value_text; ?></td>
                        <td class="px-6 py-4 text-center font-mono text-slate-600"><?php echo $rule['min_price']; ?> <span class="text-slate-300 mx-1">-</span> <?php echo $rule['max_price']; ?></td>
                        <td class="px-6 py-4 text-center"><?php echo $status_text; ?></td>
                        <td class="px-6 py-4 text-center font-bold text-slate-500"><?php echo $rule['sort_order']; ?></td>
                        <td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="region_price.php?my=edit&id=<?php echo $rule['id']; ?>" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm">编辑</a>
                                <a href="javascript:if(confirm('警告：确定要彻底删除这条区域加价规则吗？此操作不可撤销！'))location.href='region_price.php?my=delete&id=<?php echo $rule['id']; ?>'" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm">删除</a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa fa-map-o text-5xl mb-3 text-slate-200 block"></i>
                            <p class="text-sm font-medium text-slate-500">当前没有配置任何地区加价规则</p>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php } elseif ($my == 'add' || $my == 'edit') { ?>
    <!-- 添加/编辑规则页 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa <?php echo $my == 'add' ? 'fa-plus-circle text-brand-500' : 'fa-pencil-square-o text-sky-500'; ?> mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0"><?php echo $my == 'add' ? '添加地区加价规则' : '编辑地区加价规则'; ?></h3>
        </div>

        <div class="p-6 sm:p-8">
            <form method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">地区名称 <span class="text-rose-500">*</span></label>
                        <input type="text" name="region_name" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" value="<?php echo $my == 'edit' ? htmlspecialchars($rule['region_name']) : ''; ?>" placeholder="如：广东、北京" required>
                        <p class="text-xs text-slate-400 mt-1">仅作为后台标识显示名称</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">排序权重</label>
                        <input type="number" name="sort_order" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" value="<?php echo $my == 'edit' ? $rule['sort_order'] : '0'; ?>" min="0">
                        <p class="text-xs text-slate-400 mt-1">数字越小匹配优先级越高</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">地区识别关键词 <span class="text-rose-500">*</span></label>
                    <textarea name="region_keywords" class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" rows="3" placeholder="如：广东,广州,深圳,珠海" required><?php echo $my == 'edit' ? htmlspecialchars($rule['region_keywords']) : ''; ?></textarea>
                    <p class="text-xs text-brand-600 mt-1 font-medium"><i class="fa fa-info-circle mr-1"></i>多个关键词请务必使用英文逗号 (,) 分隔。系统将扫描买家填写的收货地址中是否包含这些关键词进行匹配触发。</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-slate-50 border border-slate-100 rounded-xl">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">加价类型</label>
                        <select name="add_price_type" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <option value="1" <?php echo $my == 'edit' && $rule['add_price_type'] == 1 ? 'selected' : ''; ?>>增加固定金额 (元)</option>
                            <option value="2" <?php echo $my == 'edit' && $rule['add_price_type'] == 2 ? 'selected' : ''; ?>>按商品原价百分比 (%)</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">加价数值 <span class="text-rose-500">*</span></label>
                        <input type="number" name="add_price_value" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono text-rose-500 font-bold text-base" value="<?php echo $my == 'edit' ? $rule['add_price_value'] : '0.00'; ?>" step="0.01" min="0" required>
                        <p class="text-xs text-slate-400 mt-1">固定金额填具体数值；百分比填如 20 (表示20%)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">商品最低原价触发下限 (￥)</label>
                        <input type="number" name="min_price" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" value="<?php echo $my == 'edit' ? $rule['min_price'] : '0.00'; ?>" step="0.01" min="0">
                        <p class="text-xs text-slate-400 mt-1">商品售价<strong class="text-rose-500 mx-1">低于</strong>此值时不触发本加价规则</p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">商品最高原价触发上限 (￥)</label>
                        <input type="number" name="max_price" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" value="<?php echo $my == 'edit' ? $rule['max_price'] : '999999.99'; ?>" step="0.01" min="0">
                        <p class="text-xs text-slate-400 mt-1">商品售价<strong class="text-rose-500 mx-1">高于</strong>此值时不触发本加价规则</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">启用状态</label>
                    <select name="status" class="w-full sm:w-1/2 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                        <option value="1" <?php echo $my == 'edit' && $rule['status'] == 1 ? 'selected' : ''; ?>>立即生效 (启用)</option>
                        <option value="0" <?php echo $my == 'edit' && $rule['status'] == 0 ? 'selected' : ''; ?>>暂不生效 (禁用)</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存加价规则设定
                    </button>
                    <a href="region_price.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <?php } elseif ($my == 'import') { ?>
    <!-- 批量导入页 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-upload text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">批量导入地区加价规则</h3>
        </div>

        <div class="p-6 sm:p-8">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="p-8 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 text-center hover:bg-slate-100 transition-colors cursor-pointer relative" id="upload-zone">
                    <i class="fa fa-cloud-upload text-5xl text-slate-300 mb-4 block"></i>
                    <p class="text-slate-600 font-medium mb-1">点击此处选择文件</p>
                    <p class="text-xs text-slate-400">仅支持标准的 .csv 或 .json 格式文本文件</p>
                    <input type="file" name="import_file" id="file-input" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".csv,.json" required onchange="document.getElementById('file-name').innerHTML = '<i class=\'fa fa-file-text-o mr-1.5\'></i>已选择：' + this.files[0].name">
                </div>
                <div id="file-name" class="text-sm font-medium text-brand-600 text-center h-5"></div>

                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-play-circle mr-1.5"></i>开始解析导入
                    </button>
                    <a href="region_price.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        返回列表
                    </a>
                </div>
            </form>

            <div class="mt-10 border border-slate-100 rounded-xl overflow-hidden shadow-sm">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 font-bold text-slate-700">
                    <i class="fa fa-book text-brand-500 mr-1.5"></i>文件结构规范参考
                </div>
                <div class="p-5 space-y-6">
                    <div>
                        <h5 class="text-sm font-bold text-slate-700 mb-2">CSV 格式模板规范：</h5>
                        <pre class="bg-slate-800 text-slate-200 p-4 rounded-lg overflow-x-auto text-xs font-mono leading-relaxed shadow-inner">region_name,region_keywords,add_price_type,add_price_value,min_price,max_price,status,sort_order
广东,广东,广州,深圳,珠海,1,10.00,0.00,999999.99,1,1
北京,北京,北京市,1,15.00,0.00,999999.99,1,2</pre>
                    </div>

                    <div>
                        <h5 class="text-sm font-bold text-slate-700 mb-2">JSON 格式模板规范：</h5>
                        <pre class="bg-slate-800 text-slate-200 p-4 rounded-lg overflow-x-auto text-xs font-mono leading-relaxed shadow-inner">[
  {
    "region_name": "广东",
    "region_keywords": "广东,广州,深圳,珠海",
    "add_price_type": 1,
    "add_price_value": 10.00,
    "min_price": 0.00,
    "max_price": 999999.99,
    "status": 1,
    "sort_order": 1
  }
]</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php } elseif ($my == 'logs') { ?>
    <!-- 日志列表页 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-history text-amber-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">地区加价触发日志</h3>
        </div>

        <div class="p-6 border-b border-slate-50">
            <!-- 响应式多条件复合搜索栏 -->
            <form method="GET" class="flex flex-col md:flex-row flex-wrap gap-3">
                <input type="hidden" name="my" value="logs">
                <div class="flex-1 min-w-[150px]">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fa fa-hashtag text-slate-400"></i></div>
                        <input type="text" name="order_id" class="w-full h-10 pl-8 pr-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" placeholder="检索订单号" value="<?php echo isset($_GET['order_id']) ? htmlspecialchars($_GET['order_id']) : ''; ?>">
                    </div>
                </div>
                <div class="w-full md:w-32">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fa fa-shopping-bag text-slate-400"></i></div>
                        <input type="text" name="tid" class="w-full h-10 pl-8 pr-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" placeholder="商品ID" value="<?php echo isset($_GET['tid']) ? htmlspecialchars($_GET['tid']) : ''; ?>">
                    </div>
                </div>
                <div class="w-full md:w-40">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fa fa-calendar text-slate-400"></i></div>
                        <input type="text" name="start_time" class="w-full h-10 pl-8 pr-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" placeholder="起始时间" value="<?php echo isset($_GET['start_time']) ? htmlspecialchars($_GET['start_time']) : ''; ?>">
                    </div>
                </div>
                <div class="w-full md:w-40">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fa fa-calendar text-slate-400"></i></div>
                        <input type="text" name="end_time" class="w-full h-10 pl-8 pr-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" placeholder="截止时间" value="<?php echo isset($_GET['end_time']) ? htmlspecialchars($_GET['end_time']) : ''; ?>">
                    </div>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="flex-1 md:flex-none inline-flex items-center justify-center px-5 h-10 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-colors">检索日志</button>
                    <a href="region_price.php?my=logs" class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 shadow-sm transition-colors" title="重置搜索条件"><i class="fa fa-refresh"></i></a>
                    <a href="region_price.php" class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium text-sm hover:bg-slate-50 shadow-sm transition-colors">返回规则表</a>
                </div>
            </form>
            
            <div class="mt-4 flex flex-wrap gap-2 justify-end">
                <a href="javascript:if(confirm('警告：确定要彻底清理所有触发日志吗？此操作无法撤销！'))location.href='region_price.php?my=logs&clear_logs=1'" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm"><i class="fa fa-trash-o mr-1"></i>全部清理</a>
                <a href="javascript:if(confirm('确定要清理 30 天前的陈旧日志数据吗？'))location.href='region_price.php?my=logs&clear_logs=1&before_date=<?php echo date('Y-m-d H:i:s', strtotime('-30 days')); ?>'" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm"><i class="fa fa-eraser mr-1"></i>清理30天前旧数据</a>
            </div>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-16">ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-36">订单号</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[150px]">关联商品名称</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">订单原价</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28 text-sky-600">匹配触发地区</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32 text-amber-600">应用加价幅度</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28 text-emerald-600">落地终价</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[200px]">买家实际填报地址</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">触发生效时间</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php
                    $isEmpty = true;
                    if (!empty($log_list)) {
                        foreach ($log_list as $log) {
                            $isEmpty = false;
                            $add_type_badge = $log['add_price_type'] == 1 
                                ? '<span class="px-1.5 py-[1px] rounded bg-slate-100 text-slate-500 text-[10px] mr-1">固定</span>' 
                                : '<span class="px-1.5 py-[1px] rounded bg-sky-50 text-sky-500 text-[10px] mr-1">百分比</span>';
                            
                            $add_value_text = $log['add_price_type'] == 1 ? '¥' . $log['add_price_value'] : $log['add_price_value'] . '%';
                    ?>
                    <tr class="hover:bg-brand-50/40 transition-colors duration-200">
                        <td class="px-6 py-3 text-center text-xs text-slate-400 font-mono"><?php echo $log['id']; ?></td>
                        <td class="px-6 py-3 text-center font-bold text-slate-700 font-mono text-xs"><?php echo htmlspecialchars($log['order_id']); ?></td>
                        <td class="px-6 py-3 font-medium text-slate-700 truncate max-w-[200px]" title="<?php echo htmlspecialchars($log['tool_name']); ?>"><?php echo htmlspecialchars($log['tool_name']); ?></td>
                        <td class="px-6 py-3 text-center font-mono text-slate-500"><span class="text-[10px] mr-0.5">¥</span><?php echo $log['original_price']; ?></td>
                        <td class="px-6 py-3 text-center font-bold text-sky-600"><?php echo htmlspecialchars($log['region_name']); ?></td>
                        <td class="px-6 py-3 text-center font-mono text-amber-500 font-medium">
                            <?php echo $add_type_badge . $add_value_text; ?> 
                            <div class="text-[10px] text-slate-400 mt-0.5">(实际加收: ¥<?php echo $log['add_price_amount']; ?>)</div>
                        </td>
                        <td class="px-6 py-3 text-center font-mono font-bold text-emerald-500 text-base"><span class="text-[11px] font-normal mr-0.5 text-slate-400">¥</span><?php echo $log['final_price']; ?></td>
                        <td class="px-6 py-3 text-slate-500 text-xs truncate max-w-[200px]" title="<?php echo htmlspecialchars($log['address']); ?>"><?php echo htmlspecialchars($log['address']); ?></td>
                        <td class="px-6 py-3 text-center text-[11px] text-slate-400 font-mono"><?php echo $log['create_time']; ?></td>
                    </tr>
                    <?php
                        }
                    } else {
                    ?>
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa fa-file-text-o text-5xl mb-3 text-slate-200 block"></i>
                            <p class="text-sm font-medium text-slate-500">该条件区间下未检索到任何触发日志</p>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_pages > 1) { ?>
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
            <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 m-0 list-none p-0">
                <?php
                $btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block font-medium";
                $btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

                for ($i = 1; $i <= $total_pages; $i++) {
                    $activeClass = $i == $page ? $btn_active : $btn_normal;
                    $params = $_GET;
                    $params['page'] = $i;
                    $url = '?' . http_build_query($params);
                ?>
                <li><a href="<?php echo $url; ?>" class="<?php echo $activeClass; ?>"><?php echo $i; ?></a></li>
                <?php
                }
                ?>
            </ul>
        </div>
        <?php } ?>
    </div>
    <?php } ?>
</div>

<?php include './foot.php'; ?>
