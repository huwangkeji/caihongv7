<?php

include "../includes/common.php";

$title = "秒杀商品列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
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
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">添加秒杀商品</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./seckill.php?my=add_submit" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">关联秒杀商品</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors">
                            <?php echo $select;?>
                        </select>
                        <select id="tid" name="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"></select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">秒杀设定价格 (￥)</label>
                        <input type="text" id="price" name="price" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono text-rose-500 font-bold" placeholder="输入秒杀价格" required/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单笔下单数量</label>
                        <input type="number" min="1" id="num" name="num" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="此价格对应下单多少份" required/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">总共秒杀限额 (库存)</label>
                        <input type="number" min="1" id="value" name="value" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="共放出多少份额参与抢购" required/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">系统排序权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="数字越小排名越靠前"/>
                    </div>
                </div>

                <div class="space-y-1.5 p-4 bg-slate-50 border border-slate-100 rounded-xl">
                    <label class="text-sm font-semibold text-slate-700 mb-2 block"><i class="fa fa-clock-o mr-1.5 text-brand-500"></i>设定抢购时间区间</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <span class="text-xs text-slate-500 font-bold">开始抢购时间：</span>
                            <div class="flex gap-2">
                                <input type="date" class="w-3/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="starttime" value="" required>
                                <input type="time" class="w-2/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="starttimes" value="" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <span class="text-xs text-slate-500 font-bold">结束抢购时间：</span>
                            <div class="flex gap-2">
                                <input type="date" class="w-3/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="endtime" value="" required>
                                <input type="time" class="w-2/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="endtimes" value="" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认创建秒杀活动
                    </button>
                    <a href="./seckill.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/seckilledit.js?ver=<?php echo VERSION;?>"></script>

<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_seckillshop where id='" . $id . "' limit 1");
	$toolname = $DB->getColumn("SELECT name FROM pre_tools WHERE tid='" . $row["tid"] . "' LIMIT 1");
	$start_date = explode(" ", $row["starttime"]);
	$start_time = explode(":", $start_date[1]);
	$start_time = $start_time[0] . ":" . $start_time[1];
	$end_date = explode(" ", $row["endtime"]);
	$end_time = explode(":", $end_date[1]);
	$end_time = $end_time[0] . ":" . $end_time[1];
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改秒杀商品信息</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./seckill.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">当前关联商品</label>
                    <input type="text" id="tid" value="<?php echo htmlspecialchars($toolname);?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed" disabled/>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">秒杀设定价格 (￥)</label>
                        <input type="text" id="price" name="price" value="<?php echo $row["price"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono text-rose-500 font-bold" required/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">单笔下单数量</label>
                        <input type="number" min="1" id="num" name="num" value="<?php echo $row["num"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" required/>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">总共秒杀限额 (库存)</label>
                        <input type="number" min="1" id="value" name="value" value="<?php echo $row["value"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" required/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">系统排序权重</label>
                        <input type="number" min="1" max="1000" name="sort" value="<?php echo $row["sort"];?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors"/>
                    </div>
                </div>

                <div class="space-y-1.5 p-4 bg-slate-50 border border-slate-100 rounded-xl">
                    <label class="text-sm font-semibold text-slate-700 mb-2 block"><i class="fa fa-clock-o mr-1.5 text-brand-500"></i>设定抢购时间区间</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <span class="text-xs text-slate-500 font-bold">开始抢购时间：</span>
                            <div class="flex gap-2">
                                <input type="date" class="w-3/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="starttime" value="<?php echo $start_date[0];?>" required>
                                <input type="time" class="w-2/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="starttimes" value="<?php echo $start_time;?>" required>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <span class="text-xs text-slate-500 font-bold">结束抢购时间：</span>
                            <div class="flex gap-2">
                                <input type="date" class="w-3/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="endtime" value="<?php echo $end_date[0];?>" required>
                                <input type="time" class="w-2/5 h-11 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="endtimes" value="<?php echo $end_time;?>" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存修改配置
                    </button>
                    <a href="./seckill.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/seckilledit.js?ver=<?php echo VERSION;?>"></script>

<?php 
} elseif ($my == "add_submit") {
	$tid = intval($_POST["tid"]);
	$price = $_POST["price"];
	$value = $_POST["value"];
	$num = $_POST["num"];
	$starttime = $_POST["starttime"];
	$starttimes = $_POST["starttimes"];
	$starttime = $starttime . " " . $starttimes . ":00";
	$endtime = $_POST["endtime"];
	$endtimes = $_POST["endtimes"];
	$endtime = $endtime . " " . $endtimes . ":00";
	$sort = $_POST["sort"];
	if ($price == NULL || $value == NULL || $num == NULL || $starttime == NULL || $endtime == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "insert into `pre_seckillshop` (`tid`,`price`,`value`,`num`,`sort`,`starttime`,`endtime`,`addtime`,`active`) values ('" . $tid . "','" . $price . "','" . $value . "','" . $num . "','" . $sort . "','" . $starttime . "','" . $endtime . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加秒杀商品成功！<br/><br/><a href=\"./seckill.php\">>>返回秒杀商品列表</a>", 1);
		} else {
			showmsg("添加秒杀商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_seckillshop where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$price = $_POST["price"];
	$value = $_POST["value"];
	$num = $_POST["num"];
	$starttime = $_POST["starttime"];
	$starttimes = $_POST["starttimes"];
	$starttime = $starttime . " " . $starttimes . ":00";
	$endtime = $_POST["endtime"];
	$endtimes = $_POST["endtimes"];
	$endtime = $endtime . " " . $endtimes . ":00";
	$sort = $_POST["sort"];
	if ($price == NULL || $value == NULL || $num == NULL || $starttime == NULL || $endtime == NULL || $sort == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_seckillshop` SET `price`='" . $price . "',`value`='" . $value . "',`num`='" . $num . "',`sort`='" . $sort . "',`starttime`='" . $starttime . "',`endtime`='" . $endtime . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改秒杀商品成功！<br/><br/><a href=\"./seckill.php\">>>返回秒杀商品列表</a>", 1);
		} else {
			showmsg("修改秒杀商品失败！" . $DB->error(), 4);
		}
	}
} else {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0">
                <!-- 由 AJAX listTable() 更新 -->
            </h2>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页显示条数</span>
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
                <a href="./seckill.php?my=add" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0">
                    <i class="fa fa-plus mr-1.5"></i>新增秒杀商品
                </a>
                
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入要检索的商品名称">
                </div>
                
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        检索
                    </button>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="重置列表状态">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>
            
            <!-- AJAX 异步列表挂载点 -->
            <div id="listTable" class="w-full"></div>
        </div>
    </div>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/seckill.js?ver=<?php echo VERSION;?>"></script>
<?php 
}
?>
</div>
</body>
</html>
