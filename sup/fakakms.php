<?php

include "../includes/common.php";
$title = "卡密列表";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
if ($suprow["bond"] < $conf["sup_bond"]) {
	exit("<script>alert(\"您当前未缴纳保证金，正在为您跳转...\");window.location.href=\"./bond.php\";</script>");
}

$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$my = isset($_GET["my"]) ? $_GET["my"] : null;
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
if ($my == "add") {
	if (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"]);
		$row = $DB->getRow("select cid,name from pre_tools where tid='" . $tid . "' limit 1");
		$shopname = "<option value=\"" . $tid . "\">" . $row["name"] . "</option>";
		$cid = $row["cid"];
	} else {
		$cid = 0;
	}
?>
    <!-- 添加卡密视图 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                添加卡密
            </h3>
        </div>
        <div class="p-6">
            <form action="./fakakms.php?my=add_submit" method="POST" onsubmit="return checkAdd()" class="space-y-5">
                <input type="hidden" name="backurl" value="<?php echo $_SERVER["HTTP_REFERER"] ?? '';?>"/>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">选择商品</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200">
                        <select id="cid" class="flex-1 h-12 px-4 text-sm font-bold border-0 border-r border-slate-200 bg-slate-50 text-slate-700 focus:ring-0 cursor-pointer" default="<?php echo $cid;?>"><?php echo $select;?></select>
                        <select id="tid" name="tid" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-white text-slate-700 focus:ring-0 cursor-pointer" default="<?php echo $tid;?>"><?php echo $shopname;?></select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">卡密列表</label>
                    <textarea class="w-full p-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 placeholder:font-normal placeholder:text-slate-400" id="kms" name="kms" rows="8" placeholder="一行一张卡"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">分隔符</label>
                    <input type="text" name="split" value="" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white placeholder:font-normal placeholder:text-slate-400" placeholder="可自定义卡号和密码之间的分隔符，默认留空为空格"/>
                </div>

                <div class="flex items-center pt-2">
                    <input type="checkbox" id="is_check_repeat" name="is_check_repeat" value="1" class="w-5 h-5 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                    <label for="is_check_repeat" class="ml-2 text-sm font-bold text-slate-700 cursor-pointer">检查重复的卡密</label>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button type="submit" class="h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all">确认提交</button>
                    <button type="reset" class="h-12 rounded-xl bg-slate-100 text-slate-600 font-bold text-[15px] hover:bg-slate-200 shadow-sm active:scale-[0.98] transition-all border border-slate-200">重新填写</button>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 bg-sky-50 border-t border-sky-100 text-xs text-sky-800 font-medium leading-relaxed">
            注意：卡密格式：卡号+空格+密码，一行一张卡，如：ABCDEFG 123456789<br/>
            只有商品设置里面购买成功后的动作选择自动发卡，该商品才会显示在当前列表
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <a href="<?php echo isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "fakalist.php";?>" class="text-indigo-600 font-bold hover:underline">>>返回发卡库存列表</a>
    </div>

<?php 
} elseif ($my == "add_submit") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_POST["tid"]);
	$row = $DB->getRow("select * from pre_tools where tid='" . $tid . "' and goods_sid='" . $suprow["sid"] . "' and audit_status='1' limit 1");
	if (!$row) {
		showmsg("不存在此商品！", 3);
	}
	$kms = $_POST["kms"];
	$split = $_POST["split"];
	$is_check_repeat = $_POST["is_check_repeat"];
	if ($tid == NULL || $kms == NULL) {
		showmsg("请确保各项不能为空！", 3);
	} else {
		$kms = str_replace(array("\r\n", "\r", "\n"), "[br]", $kms);
		$match = explode("[br]", $kms);
		$c = 0;
		foreach ($match as $val) {
			if (empty($split)) {
				$km_arr = explode(" ", $val);
			} else {
				$km_arr = explode($split, $val);
			}
			$km = trim(daddslashes($km_arr[0]));
			$pw = trim(daddslashes($km_arr[1]));
			if ($km == "") {
				continue;
			}
			if ($is_check_repeat == 1) {
				if ($DB->getRow("select * from pre_faka where km='" . $km . "' limit 1")) {
					continue;
				}
			}
			$sql = $DB->exec("INSERT INTO `pre_faka` (`tid`,`sid`,`km`,`pw`,`addtime`) VALUES ('" . $tid . "','" . $suprow["sid"] . "','" . $km . "','" . $pw . "',NOW())");
			if ($sql) {
				$c++;
			} else {
				showmsg("添加卡密失败！" . $DB->error());
			}
		}
		showmsg("成功添加<b>" . $c . "</b>张卡密！<br/><br/><a href=\"" . $_POST["backurl"] . "\">>>返回发卡库存列表</a>", 1);
	}
} elseif ($my == "del") {
	if (!checkRefererHost()) {
		exit;
	}
	$id = intval($_GET["id"]);
	$sql = $DB->exec("DELETE FROM pre_faka WHERE kid='" . $id . "' and sid='" . $suprow["sid"] . "'");
	exit("<script language='javascript'>history.go(-1);</script>");
} elseif ($my == "qk") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	echo "<div class=\"bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-lg mx-auto\">\r\n<div class=\"px-6 py-5 border-b border-slate-100 bg-slate-50/50\"><h3 class=\"text-base font-bold text-slate-800 m-0\">清空卡密</h3></div>\r\n<div class=\"p-6 text-center\">\r\n<p class='text-sm text-slate-600 font-bold mb-6'>您确认要清空该商品的所有卡密吗？清空后无法恢复！</p><div class='flex gap-4 justify-center'><a href=\"./fakakms.php?my=qk2&tid=" . $tid . "\" class='px-6 py-2 bg-rose-500 text-white rounded-lg font-bold hover:bg-rose-600 transition-colors'>确认</a><a href=\"javascript:history.back();\" class='px-6 py-2 bg-slate-100 text-slate-600 rounded-lg font-bold hover:bg-slate-200 transition-colors'>返回</a></div></div></div>";
} elseif ($my == "qk2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	?><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-lg mx-auto">
<div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50"><h3 class="text-base font-bold text-slate-800 m-0">清空卡密</h3></div>
<div class="p-6 text-center text-sm font-bold text-slate-700"><?php 
	if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "' and sid='" . $suprow["sid"] . "'") !== false) {
		echo "<div class=\"mb-6\">清空成功.</div>";
	} else {
		echo "<div class=\"mb-6 text-rose-500\">清空失败.</div>";
	}
	echo "<a href=\"./fakakms.php?tid=" . $tid . "\" class='text-indigo-600 hover:underline'>>>返回卡密列表</a></div></div>";
} elseif ($my == "qkuse") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	echo "<div class=\"bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-lg mx-auto\">\r\n<div class=\"px-6 py-5 border-b border-slate-100 bg-slate-50/50\"><h3 class=\"text-base font-bold text-slate-800 m-0\">清空卡密</h3></div>\r\n<div class=\"p-6 text-center\">\r\n<p class='text-sm text-slate-600 font-bold mb-6'>您确认要清空所有卡密吗？清空后无法恢复！</p><div class='flex gap-4 justify-center'><a href=\"./fakakms.php?my=qkuse2&tid=" . $tid . "\" class='px-6 py-2 bg-rose-500 text-white rounded-lg font-bold hover:bg-rose-600 transition-colors'>确认</a><a href=\"javascript:history.back();\" class='px-6 py-2 bg-slate-100 text-slate-600 rounded-lg font-bold hover:bg-slate-200 transition-colors'>返回</a></div></div></div>";
} elseif ($my == "qkuse2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"]);
	?><div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-lg mx-auto">
<div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50"><h3 class="text-base font-bold text-slate-800 m-0">清空卡密</h3></div>
<div class="p-6 text-center text-sm font-bold text-slate-700"><?php 
	if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "' and sid='" . $suprow["sid"] . "' and orderid!=0") !== false) {
		echo "<div class=\"mb-6\">清空成功.</div>";
	} else {
		echo "<div class=\"mb-6 text-rose-500\">清空失败.</div>";
	}
	echo "<a href=\"./fakakms.php?tid=" . $tid . "\" class='text-indigo-600 hover:underline'>>>返回卡密列表</a></div></div>";
} elseif ($my == "del2") {
	if (!checkRefererHost()) {
		exit;
	}
	$checkbox = $_POST["checkbox"];
	$i = 0;
	foreach ($checkbox as $kid) {
		$DB->exec("DELETE FROM pre_faka WHERE kid='" . $kid . "' and sid='" . $suprow["sid"] . "' limit 1");
		$i++;
	}
	exit("<script language='javascript'>alert('成功删除" . $i . "张卡密');history.go(-1);</script>");
} else {
	if (isset($_GET["kw"])) {
		$tid = intval($_GET["tid"]);
		$sql = " `tid`='" . $tid . "' and `sid`='" . $suprow["sid"] . "' and (`km`='" . str_replace("'"," ",$_GET["kw"]) . "' or `pw`='" . str_replace("'"," ",$_GET["kw"]) . "')";
		$link = "&tid=" . $tid . "&kw=" . str_replace("'"," ",$_GET["kw"]);
	} elseif (isset($_GET["kid"])) {
		$sql = " `kid`='" . intval($_GET["kid"]) . "' and `sid`='" . $suprow["sid"] . "'";
		$link = "&kid=" . intval($_GET["kid"]);
	} elseif (isset($_GET["orderid"])) {
		$sql = " `orderid`='" . intval($_GET["orderid"]) . "' and `sid`='" . $suprow["sid"] . "'";
		$link = "&orderid=" . intval($_GET["orderid"]);
	} elseif (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"]);
		$row = $DB->getRow("select * from pre_tools where tid='" . $tid . "' limit 1");
		if (!$row) {
			showmsg("商品不存在", 3);
		}
		$sql = " `tid`='" . $tid . "' and `sid`='" . $suprow["sid"] . "'";
		$link = "&tid=" . $tid;
	} else {
		showmsg("商品不存在", 3);
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_faka WHERE" . $sql);
?>

    <!-- 卡密库存列表主视图 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部标题与操作区 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <?php echo $row["name"];?> - 卡密库存列表
            </h3>
            <div class="flex flex-wrap items-center gap-2">
                <a href="fakakms.php?my=add&tid=<?php echo $tid;?>" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-sm hover:bg-emerald-500 hover:text-white transition-colors"><i class="fa fa-plus mr-1"></i>加卡</a>
                <a href="fakakms.php?my=qk&tid=<?php echo $tid;?>" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 font-bold text-sm hover:bg-rose-500 hover:text-white transition-colors">清空</a>
                <a href="fakakms.php?my=qkuse&tid=<?php echo $tid;?>" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 border border-rose-200 font-bold text-sm hover:bg-rose-500 hover:text-white transition-colors">清空已使用</a>
                
                <div class="relative inline-block text-left" id="exportDropdownContainer">
                    <button type="button" onclick="toggleExportDropdown()" class="px-3 py-1.5 rounded-lg bg-sky-50 text-sky-600 border border-sky-200 font-bold text-sm hover:bg-sky-500 hover:text-white transition-colors flex items-center">
                        导出 <i class="fa fa-caret-down ml-1.5"></i>
                    </button>
                    <div id="exportDropdownMenu" class="origin-top-right absolute right-0 mt-2 w-32 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-slate-100 focus:outline-none hidden z-50">
                        <div class="py-1">
                            <a href="download.php?act=kms<?php echo $link;?>&use=0" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 font-bold">未使用</a>
                            <a href="download.php?act=kms<?php echo $link;?>&use=1" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 font-bold">已使用</a>
                            <a href="download.php?act=kms<?php echo $link;?>" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 font-bold">全部</a>
                            <a href="javascript:void(0);" onclick="showOutputModal()" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-indigo-600 font-bold">指定数量</a>
                        </div>
                    </div>
                </div>
                
                <button onclick="showSearchModal()" class="px-4 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors shadow-sm">搜索</button>
            </div>
        </div>
        
        <!-- 列表区 -->
        <form name="form1" method="post" action="fakakms.php?my=del2">
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 font-semibold w-8"><input type="checkbox" name="chkAll1" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-indigo-600 rounded border-slate-300"></th>
                            <th class="px-5 py-4 font-semibold">卡号</th>
                            <th class="px-5 py-4 font-semibold">密码</th>
                            <th class="px-5 py-4 font-semibold">状态</th>
                            <th class="px-5 py-4 font-semibold">添加时间</th>
                            <th class="px-5 py-4 font-semibold">使用时间</th>
                            <th class="px-5 py-4 font-semibold">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_faka WHERE" . $sql . " order by kid desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		if ($res["usetime"] == null) {
			$isuse = "<span class='text-emerald-500 font-bold'>未使用</span>";
		} else {
			$isuse = "<span class='text-rose-500 font-bold'>已使用</span>(<a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class='text-indigo-500 hover:underline'>" . $res["orderid"] . "</a>)";
		}
		echo "<tr class='hover:bg-slate-50 transition-colors group'>";
		echo "<td class='px-5 py-3'><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["kid"] . "\" onClick=\"unselectall1()\" class='w-4 h-4 text-indigo-600 rounded border-slate-300'></td>";
		echo "<td class='px-5 py-3 max-w-[200px] truncate cursor-pointer font-mono font-bold text-slate-700' onclick=\"showkms(this)\" title='点击展开全貌'>" . $res["km"] . "</td>";
		echo "<td class='px-5 py-3 font-mono text-slate-500'>" . $res["pw"] . "</td>";
		echo "<td class='px-5 py-3'>" . $isuse . "</td>";
		echo "<td class='px-5 py-3 text-xs text-slate-400 font-mono'>" . $res["addtime"] . "</td>";
		echo "<td class='px-5 py-3 text-xs text-slate-400 font-mono'>" . $res["usetime"] . "</td>";
		echo "<td class='px-5 py-3'><a href=\"./fakakms.php?my=del&id=" . $res["kid"] . "\" class=\"px-3 py-1 bg-rose-50 text-rose-600 rounded hover:bg-rose-500 hover:text-white transition-colors text-xs font-bold\" onclick=\"return confirm('你确实要删除此卡密吗？');\">删除</a></td>";
		echo "</tr>";
	}
?>
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center text-sm font-bold text-slate-600">
                    <label for="chkAll1" class="cursor-pointer ml-2 mr-4">全选</label>
                    <button type="submit" name="Submit" class="px-4 py-1.5 rounded-lg bg-rose-500 text-white text-xs hover:bg-rose-600 transition-colors shadow-sm">删除选中</button>
                </div>
            </div>
        </form>

        <!-- 分页包裹器 -->
        <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
<?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;
	if ($page > 1) {
		echo "<a href=\"fakakms.php?page=" . $first . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>首页</a>";
		echo "<a href=\"fakakms.php?page=" . $prev . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&laquo;</a>";
	} else {
		echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>首页</span>";
		echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&laquo;</span>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<a href=\"fakakms.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
	}
	echo "<span class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30'>" . $page . "</span>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<a href=\"fakakms.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
	}
	if ($page < $pages) {
		echo "<a href=\"fakakms.php?page=" . $next . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&raquo;</a>";
		echo "<a href=\"fakakms.php?page=" . $last . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>尾页</a>";
	} else {
		echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&raquo;</span>";
		echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>尾页</span>";
	}
?>
            </nav>
        </div>
    </div>

    <!-- 隐藏区域：搜索卡密弹窗内容 -->
    <div id="searchModalContent" style="display: none;" class="p-6">
        <form action="fakakms.php" method="GET" class="space-y-4">
            <input type="hidden" name="tid" value="<?php echo htmlspecialchars(@$_GET["tid"], ENT_QUOTES, 'UTF-8');?>">
            <div>
                <input type="text" name="kw" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 placeholder:font-normal" placeholder="请输入卡号或密码">
            </div>
            <button type="submit" class="w-full h-11 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-sm active:scale-95 transition-all">搜索</button>
        </form>
    </div>

    <!-- 隐藏区域：导出未使用的卡密弹窗内容 -->
    <div id="outputModalContent" style="display: none;" class="p-6">
        <form action="download.php" method="GET" class="space-y-4">
            <input type="hidden" name="act" value="kms">
            <input type="hidden" name="tid" value="<?php echo $tid;?>">
            <input type="hidden" name="use" value="0">
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200">
                <input type="number" name="num" class="flex-1 h-11 px-4 text-sm font-bold border-0 border-r border-slate-200 bg-slate-50 focus:ring-0 placeholder:font-normal" placeholder="请输入要导出的数量">
                <select name="isuse" class="w-36 h-11 px-2 text-sm font-bold border-0 bg-white text-slate-700 focus:ring-0 cursor-pointer">
                    <option value="0">不改为已使用</option>
                    <option value="1">同时改为已使用</option>
                </select>
            </div>
            <button type="submit" class="w-full h-11 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-sm active:scale-95 transition-all">导出</button>
        </form>
    </div>

<?php 
}
?>
</div>

<script>
    var checkflag1 = "false";
    function check1(field) {
        if (checkflag1 == "false") {
            for (i = 0; i < field.length; i++) {
                field[i].checked = true;}
            checkflag1 = "true";
            return "false"; }
        else {
            for (i = 0; i < field.length; i++) {
                field[i].checked = false; }
            checkflag1 = "false";
            return "true"; }
    }

    function unselectall1()
    {
        if(document.form1.chkAll1.checked){
            document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
            checkflag1 = "false";
        }
    }

    function showkms(obj) {
        $(obj).css("white-space","normal");
        $(obj).css("word-break","break-all");
    }

    function checkAdd(){
        if($("#tid").val()==0||$("#tid").val()==null){
            layer.alert('请先选择商品');return false;
        }
        if($("#kms").val()==''){
            layer.alert('卡密列表不能为空');return false;
        }
    }
    
    // 导出下拉菜单控制
    function toggleExportDropdown() {
        const menu = document.getElementById('exportDropdownMenu');
        menu.classList.toggle('hidden');
    }
    
    // 点击外部区域关闭导出下拉
    document.addEventListener('click', function(event) {
        const container = document.getElementById('exportDropdownContainer');
        const menu = document.getElementById('exportDropdownMenu');
        if (container && menu && !container.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // 搜索弹窗
    function showSearchModal() {
        layer.open({
            type: 1,
            title: '<div class="font-bold text-slate-800">搜索卡密</div>',
            area: [$(window).width() > 400 ? '400px' : '90%', 'auto'],
            skin: 'layui-layer-rim rounded-2xl',
            shadeClose: true,
            content: $('#searchModalContent')
        });
    }

    // 导出卡密弹窗
    function showOutputModal() {
        // 关闭可能打开的下拉菜单
        document.getElementById('exportDropdownMenu').classList.add('hidden');
        layer.open({
            type: 1,
            title: '<div class="font-bold text-slate-800">导出未使用的卡密</div>',
            area: [$(window).width() > 450 ? '450px' : '90%', 'auto'],
            skin: 'layui-layer-rim rounded-2xl',
            shadeClose: true,
            content: $('#outputModalContent')
        });
    }

    $(document).ready(function(){
        $("#cid").change(function () {
            var cid = $(this).val();
            var ii = layer.load(2, {shade:[0.1,'#fff']});
            $("#tid").empty();
            $("#tid").append('<option value="0">请选择商品</option>');
            $.ajax({
                type : "GET",
                url : "./ajax_user.php?act=getfakatool&cid="+cid,
                dataType : 'json',
                success : function(data) {
                    layer.close(ii);
                    if(data.code == 0){
                        var num = 0;
                        $.each(data.data, function (i, res) {
                            $("#tid").append('<option value="'+res.tid+'">'+res.name+'</option>');
                            num++;
                        });
                        $("#tid").val(0);
                        if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有发卡类商品</option>');
                    }else{
                        layer.alert(data.msg);
                    }
                },
                error:function(data){
                    layer.msg('服务器错误');
                    return false;
                }
            });
        });
        var items = $("select[default]");
        for (i = 0; i < items.length; i++) {
            $(items[i]).val($(items[i]).attr("default")||0);
        }
    });
</script>
</body>
</html>
