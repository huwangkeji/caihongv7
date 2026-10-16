<?php

include "../includes/common.php";
$title = "发卡库存管理";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
if ($suprow["bond"] < $conf["sup_bond"]) {
	exit("<script>alert(\"您当前未缴纳保证金，正在为您跳转...\");window.location.href=\"./bond.php\";</script>");
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php 
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">所有</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "move") {
	$cid = $_POST["cid"];
	if (!$cid) {
		exit("<script language='javascript'>alert('请选择分类');history.go(-1);</script>");
	}
	$checkbox = $_POST["checkbox"];
	$i = 0;
	foreach ($checkbox as $tid) {
		if ($cid == -1) {
			$DB->exec("update pre_tools set active=1 where tid='" . $tid . "' limit 1");
		} elseif ($cid == -2) {
			$DB->exec("update pre_tools set active=0 where tid='" . $tid . "' limit 1");
		} elseif ($cid == -3) {
			$DB->exec("DELETE FROM pre_tools WHERE tid='" . $tid . "' limit 1");
		}
		$i++;
	}
	exit("<script language='javascript'>alert('成功移动" . $i . "个商品');history.go(-1);</script>");
} else {
	if (isset($_GET["cid"]) && $_GET["cid"]) {
		$cid = intval($_GET["cid"]);
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4 and cid='" . $cid . "' and goods_sid='" . $suprow["sid"] . "' and audit_status='1'");
		$sql = " is_curl=4 and cid='" . $cid . "' and goods_sid='" . $suprow["sid"] . "' and audit_status='1'";
		$link = "&cid=" . $cid;
	} elseif (isset($_GET["tid"]) && $_GET["tid"]) {
		$tid = intval($_GET["tid"]);
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4 and tid='" . $tid . "' and goods_sid='" . $suprow["sid"] . "' and audit_status='1'");
		$sql = " is_curl=4 and tid='" . $tid . "' and goods_sid='" . $suprow["sid"] . "' and audit_status='1'";
		$link = "&tid=" . $tid;
	} else {
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4 and goods_sid='" . $suprow["sid"] . "' and audit_status='1'");
		$sql = " is_curl=4 and goods_sid='" . $suprow["sid"] . "' and audit_status='1'";
	}
?>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部筛选区域 -->
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                发卡库存管理
            </h3>
            
            <form action="fakalist.php" method="GET" class="flex gap-2">
                <select name="cid" class="h-10 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-white text-slate-700 cursor-pointer min-w-[150px]" default="<?php echo htmlspecialchars(isset($_GET["cid"]) ? $_GET["cid"] : '0', ENT_QUOTES, 'UTF-8');?>">
                    <?php echo $select;?>
                </select>
                <button type="submit" class="h-10 px-5 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 shadow-sm transition-colors shrink-0">进入分类</button>
            </form>
        </div>
        
        <!-- 列表与批量操作区域 -->
        <form name="form1" method="post" action="fakalist.php?my=move">
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 font-semibold w-24">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="chkAll1" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-indigo-600 rounded border-slate-300">
                                    <span>ID</span>
                                </div>
                            </th>
                            <th class="px-5 py-4 font-semibold">商品名称</th>
                            <th class="px-5 py-4 font-semibold text-center">剩余卡密</th>
                            <th class="px-5 py-4 font-semibold text-center">已售出</th>
                            <th class="px-5 py-4 font-semibold text-center">状态</th>
                            <th class="px-5 py-4 font-semibold text-center min-w-[240px]">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT a.*,(select count(b.tid) from pre_faka as b where b.tid=a.tid and orderid=0) as leftcount,(select count(b.tid) from pre_faka as b where b.tid=a.tid and orderid!=0) as sellcount FROM pre_tools as a WHERE" . $sql . " order by sort asc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		echo "<tr class='hover:bg-slate-50 transition-colors group'>";
		echo "<td class='px-5 py-3.5'><div class='flex items-center gap-2'><input type=\"checkbox\" name=\"checkbox[]\" id=\"list1\" value=\"" . $res["tid"] . "\" onClick=\"unselectall1()\" class='w-4 h-4 text-indigo-600 rounded border-slate-300'><b class='font-mono'>" . $res["tid"] . "</b></div></td>";
		echo "<td class='px-5 py-3.5 font-bold text-slate-700 max-w-[250px] truncate' title='".htmlspecialchars($res["name"])."'>" . $res["name"] . "</td>";
		echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-emerald-600'>" . $res["leftcount"] . "</td>";
		echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-indigo-600'>" . $res["sellcount"] . "</td>";
		echo "<td class='px-5 py-3.5 text-center'>" . ($res["active"] == 1 ? "<button type=\"button\" class=\"px-2 py-1 rounded bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-bold hover:bg-emerald-500 hover:text-white transition-colors\" onclick=\"setActive(" . $res["tid"] . ",0)\">上架中</button>" : "<button type=\"button\" class=\"px-2 py-1 rounded bg-amber-50 text-amber-600 border border-amber-200 text-xs font-bold hover:bg-amber-500 hover:text-white transition-colors\" onclick=\"setActive(" . $res["tid"] . ",1)\">已下架</button>") . "</td>";
		echo "<td class='px-5 py-3.5 text-center flex items-center justify-center gap-1.5'>";
        echo "<a href=\"./fakakms.php?tid=" . $res["tid"] . "\" class=\"px-3 py-1 bg-sky-50 text-sky-600 rounded hover:bg-sky-500 hover:text-white transition-colors text-xs font-bold border border-sky-100\">查看卡密</a>";
        echo "<a href=\"./fakakms.php?my=add&tid=" . $res["tid"] . "\" class=\"px-3 py-1 bg-emerald-50 text-emerald-600 rounded hover:bg-emerald-500 hover:text-white transition-colors text-xs font-bold border border-emerald-100\">加卡</a>";
        echo "<a href=\"./list.php?tid=" . $res["tid"] . "\" class=\"px-3 py-1 bg-amber-50 text-amber-600 rounded hover:bg-amber-500 hover:text-white transition-colors text-xs font-bold border border-amber-100\">订单</a>";
        echo "<a href=\"./shopedit.php?my=delete&tid=" . $res["tid"] . "\" class=\"px-3 py-1 bg-rose-50 text-rose-600 rounded hover:bg-rose-500 hover:text-white transition-colors text-xs font-bold border border-rose-100\" onclick=\"return confirm('你确实要删除此商品吗？');\">删除</a>";
        echo "</td></tr>\r\n";
	}
?>
                    </tbody>
                </table>
            </div>
            
            <!-- 批量操作底栏 -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <select name="cid" class="h-10 px-3 text-sm font-bold rounded-lg border-slate-200 bg-white text-slate-700 focus:ring-0 cursor-pointer w-40 shadow-sm">
                        <option selected>批量操作</option>
                        <option value="-1">&gt;改为上架中</option>
                        <option value="-2">&gt;改为已下架</option>
                        <option value="-3">&gt;删除选中</option>
                    </select>
                    <button type="submit" name="Submit" class="h-10 px-5 rounded-lg bg-slate-800 text-white font-bold text-sm hover:bg-slate-900 transition-colors shadow-sm">确定</button>
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
    $link = isset($link) ? $link : '';
	if ($page > 1) {
		echo "<a href=\"fakalist.php?page=" . $first . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>首页</a>";
		echo "<a href=\"fakalist.php?page=" . $prev . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&laquo;</a>";
	} else {
		echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>首页</span>";
		echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&laquo;</span>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<a href=\"fakalist.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
	}
	echo "<span class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30'>" . $page . "</span>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<a href=\"fakalist.php?page=" . $i . $link . "\" class='min-w-[2.5rem] px-2 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm'>" . $i . "</a>";
	}
	if ($page < $pages) {
		echo "<a href=\"fakalist.php?page=" . $next . $link . "\" class='w-9 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm'>&raquo;</a>";
		echo "<a href=\"fakalist.php?page=" . $last . $link . "\" class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm text-sm font-bold'>尾页</a>";
	} else {
		echo "<span class='w-9 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed'>&raquo;</span>";
		echo "<span class='min-w-[3rem] px-3 h-9 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed text-sm font-bold'>尾页</span>";
	}
?>
            </nav>
        </div>
        
    </div>
<?php 
}
?>

</div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
var checkflag1 = "false";
function check1(field) {
    if (checkflag1 == "false") {
        for (i = 0; i < field.length; i++) {
            field[i].checked = true;
        }
        checkflag1 = "true";
        return "false"; 
    } else {
        for (i = 0; i < field.length; i++) {
            field[i].checked = false; 
        }
        checkflag1 = "false";
        return "true"; 
    }
}

function unselectall1() {
    if(document.form1.chkAll1.checked){
	    document.form1.chkAll1.checked = document.form1.chkAll1.checked&0;
	    checkflag1 = "false";
    } 	
}

function setActive(tid,active) {
	$.ajax({
		type : 'GET',
		url : 'ajax_shop.php?act=setTools&tid='+tid+'&active='+active,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

function sort(cid,tid,sort) {
	$.ajax({
		type : 'GET',
		url : 'ajax_shop.php?act=setToolSort&cid='+cid+'&tid='+tid+'&sort='+sort,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

$(document).ready(function(){
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
    	$(items[i]).val($(items[i]).attr("default")||0);
    }
});
</script>
</body>
</html>
