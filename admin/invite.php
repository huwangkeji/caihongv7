<?php

include "../includes/common.php";
$title = "推广商品列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6">
<?php 
adminpermission("shop", 1);
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
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">添加推广商品</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./invite.php?my=add_submit" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">关联商品</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <?php echo $select;?>
                        </select>
                        <select id="tid" name="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"></select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">奖励触发条件</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="type" name="type" class="w-full sm:w-48 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors shrink-0">
                            <option value="0">满足下单金额</option>
                            <option value="1">满足累计访问</option>
                        </select>
                        <input type="text" id="value" name="value" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入具体数值（如：金额数 或 访问次数）"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单用户奖励次数上限</label>
                        <select id="times" name="times" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <option value="0">一次性 (仅发一次)</option>
                            <option value="1">可多次 (重复触发)</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">排序权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="数字越小越靠前"/>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认添加商品
                    </button>
                    <a href="./invite.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        返回列表
                    </a>
                </div>
            </form>
            
            <!-- 配置帮助指南 -->
            <div class="mt-8 p-5 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
                <h4 class="text-sm font-bold text-slate-700 m-0 flex items-center"><i class="fa fa-info-circle text-brand-500 mr-2"></i>配置帮助指南</h4>
                
                <div class="space-y-2">
                    <div class="text-sm font-bold text-slate-600">■ 奖励条件说明：</div>
                    <ul class="list-none space-y-1.5 ml-1 text-xs text-slate-500">
                        <li><span class="inline-flex items-center justify-center bg-sky-100 text-sky-600 px-1.5 py-0.5 rounded font-bold mr-1">下单金额</span> 被推广用户打开链接下单达到该金额值，推广用户获得该商品奖励。</li>
                        <li><span class="inline-flex items-center justify-center bg-sky-100 text-sky-600 px-1.5 py-0.5 rounded font-bold mr-1">累计访问</span> 打开推广链接的 IP 数量达到该数值，推广用户获得该商品奖励。</li>
                    </ul>
                </div>
                
                <div class="space-y-2 pt-2 border-t border-slate-200/60">
                    <div class="text-sm font-bold text-slate-600">■ 奖励次数说明：</div>
                    <ul class="list-none space-y-1.5 ml-1 text-xs text-slate-500">
                        <li><span class="inline-flex items-center justify-center bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-bold mr-1">一次性</span> 一个推广链接只能发放一次奖励（通常适用于钻和会员等商品）。</li>
                        <li><span class="inline-flex items-center justify-center bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded font-bold mr-1">可多次</span> 一个推广链接可发放多次奖励，只要达到条件即可循环触发（适用于名片赞等可叠加商品）。</li>
                        <li class="mt-2 text-rose-500 font-medium"><i class="fa fa-exclamation-triangle mr-1"></i>注：若奖励条件选择为“累计访问”，则奖励次数只能固定为“一次性”。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/inviteedit.js?ver=<?php echo VERSION;?>"></script>

<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_inviteshop where id='" . $id . "' limit 1");
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $row["tid"] . "' LIMIT 1");
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改推广商品</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./invite.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">当前关联商品名称</label>
                    <input type="text" id="tid" value="<?php echo $toolname;?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed" disabled/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">奖励触发条件</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select id="type" name="type" class="w-full sm:w-48 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors shrink-0" default="<?php echo $row["type"];?>">
                            <option value="0">满足下单金额</option>
                            <option value="1">满足累计访问</option>
                        </select>
                        <input type="text" id="value" name="value" value="<?php echo $row["value"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入具体数值（如：金额数 或 访问次数）"/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单用户奖励次数上限</label>
                        <select id="times" name="times" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $row["times"];?>">
                            <option value="0">一次性 (仅发一次)</option>
                            <option value="1">可多次 (重复触发)</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">排序权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="<?php echo $row["sort"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="数字越小越靠前"/>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存修改信息
                    </button>
                    <a href="./invite.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
            
            <!-- 配置帮助指南 (复用) -->
            <div class="mt-8 p-5 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
                <h4 class="text-sm font-bold text-slate-700 m-0 flex items-center"><i class="fa fa-info-circle text-brand-500 mr-2"></i>配置帮助指南</h4>
                <div class="space-y-2">
                    <div class="text-sm font-bold text-slate-600">■ 奖励条件说明：</div>
                    <ul class="list-none space-y-1.5 ml-1 text-xs text-slate-500">
                        <li><span class="inline-flex items-center justify-center bg-sky-100 text-sky-600 px-1.5 py-0.5 rounded font-bold mr-1">下单金额</span> 被推广用户打开链接下单达到该金额值，推广用户获得该商品奖励。</li>
                        <li><span class="inline-flex items-center justify-center bg-sky-100 text-sky-600 px-1.5 py-0.5 rounded font-bold mr-1">累计访问</span> 打开推广链接的 IP 数量达到该数值，推广用户获得该商品奖励。</li>
                    </ul>
                </div>
                <div class="space-y-2 pt-2 border-t border-slate-200/60">
                    <div class="text-sm font-bold text-slate-600">■ 奖励次数说明：</div>
                    <ul class="list-none space-y-1.5 ml-1 text-xs text-slate-500">
                        <li><span class="inline-flex items-center justify-center bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-bold mr-1">一次性</span> 一个推广链接只能发放一次奖励（通常适用于钻和会员等商品）。</li>
                        <li><span class="inline-flex items-center justify-center bg-emerald-100 text-emerald-600 px-1.5 py-0.5 rounded font-bold mr-1">可多次</span> 一个推广链接可发放多次奖励，只要达到条件即可循环触发（适用于名片赞等可叠加商品）。</li>
                        <li class="mt-2 text-rose-500 font-medium"><i class="fa fa-exclamation-triangle mr-1"></i>注：若奖励条件选择为“累计访问”，则奖励次数只能固定为“一次性”。</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/inviteedit.js?ver=<?php echo VERSION;?>"></script>

<?php 
} elseif ($my == "add_submit") {
	$tid = intval($_POST["tid"]);
	$type = intval($_POST["type"]);
	$value = $_POST["value"];
	$times = intval($_POST["times"]);
	$sort = $_POST["sort"];
	if ($value == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "insert into `pre_inviteshop` (`tid`,`type`,`value`,`times`,`sort`,`addtime`,`active`) values ('" . $tid . "','" . $type . "','" . $value . "','" . $times . "','" . $sort . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加推广商品成功！<br/><br/><a href=\"./invite.php\">>>返回推广商品列表</a>", 1);
		} else {
			showmsg("添加推广商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_inviteshop where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$type = intval($_POST["type"]);
	$value = $_POST["value"];
	$times = intval($_POST["times"]);
	$sort = $_POST["sort"];
	if ($value == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_inviteshop` SET `type`='" . $type . "',`value`='" . $value . "',`times`='" . $times . "',`sort`='" . $sort . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改推广商品成功！<br/><br/><a href=\"./invite.php\">>>返回推广商品列表</a>", 1);
		} else {
			showmsg("修改推广商品失败！" . $DB->error(), 4);
		}
	}
} else {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0">
                <!-- 这里的文字将由 JS 的 searchItem 回调动态填充 -->
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">显示条数</span>
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
                <a href="./invite.php?my=add" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0">
                    <i class="fa fa-plus-circle mr-1.5"></i>新增推广商品
                </a>
                
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入你要搜索的商品名称关键字">
                </div>
                
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        检索
                    </button>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="重置并刷新列表">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>
            
            <!-- AJAX 异步列表挂载点 -->
            <div id="listTable" class="w-full"></div>
        </div>
    </div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/invite.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?>
</div>
</body>
</html>
