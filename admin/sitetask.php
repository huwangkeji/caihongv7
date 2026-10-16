<?php

include "../includes/common.php";

// 检查并创建站点任务表
function createSiteTaskTables() {
    global $DB;
    
    // 创建pre_sitetask表
    $pre_table_sql = "CREATE TABLE IF NOT EXISTS `pre_sitetask` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `task` tinyint(1) NOT NULL DEFAULT 0,
      `type` tinyint(1) NOT NULL DEFAULT 0,
      `tid` int(11) unsigned NOT NULL DEFAULT 0,
      `value` varchar(32) NOT NULL,
      `money` varchar(32) NOT NULL,
      `quantity` int(11) NOT NULL,
      `desc` text,
      `sort` int(11) NOT NULL DEFAULT 10,
      `addtime` datetime DEFAULT NULL,
      `active` tinyint(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点任务表';";
    
    // 创建shua_sitetask表
    $shua_table_sql = "CREATE TABLE IF NOT EXISTS `shua_sitetask` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `task` tinyint(1) NOT NULL DEFAULT 0,
      `type` tinyint(1) NOT NULL DEFAULT 0,
      `tid` int(11) unsigned NOT NULL DEFAULT 0,
      `value` varchar(32) NOT NULL,
      `money` varchar(32) NOT NULL,
      `quantity` int(11) NOT NULL,
      `desc` text,
      `sort` int(11) NOT NULL DEFAULT 10,
      `addtime` datetime DEFAULT NULL,
      `active` tinyint(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='站点任务表';";
    
    try {
        $DB->exec($pre_table_sql);
        $DB->exec($shua_table_sql);
    } catch (Exception $e) {
        // 忽略错误，继续执行
    }
}

// 执行表创建
createSiteTaskTables();

$title = "分站任务列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
<?php 
adminpermission("site", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}

if ($my == "add") {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">添加新的分站任务</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitetask.php?my=add_submit" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5 p-5 bg-slate-50 border border-slate-100 rounded-xl shadow-inner">
                    <label class="text-sm font-bold text-slate-700 block mb-2"><i class="fa fa-cog mr-1.5 text-brand-500"></i>任务核心定位</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5 md:col-span-1">
                            <label class="text-xs font-semibold text-slate-600">任务类型标的</label>
                            <select id="task" name="task" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                                <option value="0">商品推广</option>
                                <option value="1">余额充值</option>
                                <option value="2">订单数量</option>
                                <option value="3">销售金额</option>
                                <option value="4">邀新开户</option>
                                <option value="5">连续签到</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <label class="text-xs font-semibold text-slate-600">时间计算跨度</label>
                            <select id="type" name="type" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                                <option value="0">限定今日</option>
                                <option value="1">历史总计</option>
                            </select>
                        </div>
                        <div class="space-y-1.5 md:col-span-1">
                            <label class="text-xs font-semibold text-slate-600">自定义对客显示名称</label>
                            <input type="text" id="name" name="name" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入展现任务名称"/>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5" id="goods" style="display:none;">
                    <label class="text-sm font-semibold text-slate-700">关联对应商品</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="cid" class="w-full sm:w-1/2 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <?php echo $select;?>
                        </select>
                        <select id="tid" name="tid" class="w-full sm:w-1/2 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"></select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">需达成的任务条件阀值</label>
                        <input type="text" id="value" name="value" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white transition-colors font-mono font-bold text-slate-700" placeholder="达到此目标即算完成"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">完成后结算的奖励金额</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-semibold">¥</div>
                            <input type="text" id="money" name="money" value="" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white transition-colors font-mono font-bold text-emerald-600" placeholder="奖励金额额度"/>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">名额发放总数量</label>
                        <input type="number" min="1" name="quantity" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="此任务限制可接取的总人数"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">系统排序展示权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="数字越小列表排名越靠前"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">用户侧任务规则介绍</label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[100px]" id="desc" name="desc" placeholder="简短向用户描述此任务，暂不支持 HTML 语法"></textarea>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认创建上架任务
                    </button>
                    <a href="./sitetask.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetaskedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_sitetask where id='" . $id . "' limit 1");
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $row["tid"] . "' LIMIT 1");
	$type = "今日";
	if ($row["task"] == 1) {
		$type = "总计";
	}
	$tasktype = sitetask_type($row["task"]);
	if ($row["task"] == 5) {
		$tasktype = $tasktype . "任务";
	} else {
		$tasktype = $type . $tasktype . "任务";
	}
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">调整编辑分站任务规则</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitetask.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">当前任务底层类型</label>
                        <input type="text" id="taskname" value="<?php echo $tasktype;?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed" disabled/>
                    </div>
                    
                    <?php if ($row["task"] == 0) { ?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">绑定的指定商品</label>
                        <input type="text" id="tid" value="<?php echo htmlspecialchars($toolname);?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed truncate" disabled/>
                    </div>
                    <?php } ?>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">对客显示任务名称</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row["name"]);?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入任务名称"/>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-emerald-50/50 border border-emerald-100 rounded-xl">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">需达成的任务条件阀值</label>
                        <input type="text" id="value" name="value" value="<?php echo $row["value"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white transition-colors font-mono font-bold text-slate-700" placeholder="输入任务条件"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">完成后结算的奖励金额</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-semibold">¥</div>
                            <input type="text" id="money" name="money" value="<?php echo $row["money"];?>" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white transition-colors font-mono font-bold text-emerald-600" placeholder="输入任务完成奖励金额"/>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">名额发放总数量</label>
                        <input type="number" min="1" name="quantity" value="<?php echo $row["quantity"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入任务发放数量"/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">系统排序展示权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="<?php echo $row["sort"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="数字越小越靠前"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">用户侧任务规则介绍</label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[100px]" id="desc" name="desc" placeholder="任务简单介绍，不支持HTML代码，可留空"><?php echo htmlspecialchars($row["desc"]);?></textarea>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>确认保存修改信息
                    </button>
                    <a href="./sitetask.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetaskedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
} elseif ($my == "add_submit") {
	$name = $_POST["name"];
	$task = intval($_POST["task"]);
	$type = intval($_POST["type"]);
	$tid = intval($_POST["tid"]);
	$value = $_POST["value"];
	$money = $_POST["money"];
	$quantity = $_POST["quantity"];
	$desc = addslashes($_POST["desc"]);
	$sort = $_POST["sort"];
	if ($name == NULL || $value == NULL || $money == NULL || $quantity == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "insert into `pre_sitetask` (`name`,`task`,`type`,`tid`,`value`,`money`,`quantity`,`desc`,`sort`,`addtime`,`active`) values ('" . $name . "','" . $task . "','" . $type . "','" . $tid . "','" . $value . "','" . $money . "','" . $quantity . "','" . $desc . "','" . $sort . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加分站任务成功！<br/><br/><a href=\"./sitetask.php\">>>返回分站任务列表</a>", 1);
		} else {
			showmsg("添加分站任务失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_sitetask where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$name = $_POST["name"];
	$value = $_POST["value"];
	$money = $_POST["money"];
	$quantity = $_POST["quantity"];
	$desc = addslashes($_POST["desc"]);
	$sort = $_POST["sort"];
	if ($name == NULL || $value == NULL || $money == NULL || $quantity == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_sitetask` SET `name`='" . $name . "',`value`='" . $value . "',`money`='" . $money . "',`quantity`='" . $quantity . "',`desc`='" . $desc . "',`sort`='" . $sort . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改分站任务成功！<br/><br/><a href=\"./sitetask.php\">>>返回分站任务列表</a>", 1);
		} else {
			showmsg("修改分站任务失败！" . $DB->error(), 4);
		}
	}
} else {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0">
                <!-- 预留由 sitetask.js 自动填充标题 -->
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页显示数量</span>
                <select id="pagesize" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                    <option value="30">30</option>
                    <option value="50">50</option>
                    <option value="60">60</option>
                    <option value="80">80</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        
        <div class="p-6">
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
                <a href="./sitetask.php?my=add" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0">
                    <i class="fa fa-plus mr-1.5"></i>新建营销任务
                </a>
                
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="输入任务名称进行模糊搜索">
                </div>
                
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        定位检索
                    </button>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="重置刷新列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>
            
            <div id="listTable" class="w-full"></div>
        </div>
    </div>
</div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitetask.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?>
</body>
</html>
