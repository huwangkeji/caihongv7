<?php

include "../includes/common.php";
$title = "发卡库存管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1400px] mx-auto">
<?php 
adminpermission("faka", 1);
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">所有</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "move") {
	$cid = $_POST["cid"];
	if (!$cid || $cid == "批量操作") {
		exit("<script language='javascript'>alert('请选择要执行的批量操作');history.go(-1);</script>");
	}
    if (empty($_POST["checkbox"])) {
        exit("<script language='javascript'>alert('请至少选中一个商品');history.go(-1);</script>");
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
	exit("<script language='javascript'>alert('成功操作" . $i . "个商品');history.go(-1);</script>");
} else {
	if (isset($_GET["cid"]) && $_GET["cid"] != 0) {
		$cid = intval($_GET["cid"]);
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4 and cid='" . $cid . "'");
		$sql = " is_curl=4 and cid='" . $cid . "'";
		$link = "&cid=" . $cid;
	} elseif (isset($_GET["tid"]) && $_GET["tid"] != "") {
		$tid = intval($_GET["tid"]);
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4 and tid='" . $tid . "'");
		$sql = " is_curl=4 and tid='" . $tid . "'";
		$link = "&tid=" . $tid;
	} else {
		$numrows = $DB->getColumn("SELECT count(*) from pre_tools where is_curl=4");
		$sql = " is_curl=4";
        $link = "";
	}
	?>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-credit-card text-brand-500 mr-2 text-lg"></i>发卡库存管理
            </h3>
            
            <form action="fakalist.php" method="GET" class="flex w-full sm:w-auto gap-2">
                <select name="cid" class="w-full sm:w-48 h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo htmlspecialchars(isset($_GET["cid"]) ? $_GET["cid"] : 0, ENT_QUOTES, 'UTF-8');?>">
                    <?php echo $select;?>
                </select>
                <button type="submit" class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 shrink-0 text-sm">
                    进入分类
                </button>
            </form>
        </div>
        
        <form name="form1" id="form1" method="post" action="fakalist.php?my=move">
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold text-center w-24">
                                <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity" onclick="event.stopPropagation();">
                                    <input type="checkbox" id="chkAll1" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer" onclick="toggleAllCheckboxes(this)">
                                    <span class="ml-2 font-bold text-slate-700">全选</span>
                                </label>
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-left min-w-[200px]">商品名称</th>
                            <th class="px-6 py-3.5 font-semibold text-center text-emerald-600">剩余卡密</th>
                            <th class="px-6 py-3.5 font-semibold text-center text-rose-500">已售出</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-24">状态</th>
                            <th class="px-6 py-3.5 font-semibold text-center">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                    <?php 
                    $pagesize = 30;
                    $pages = ceil($numrows / $pagesize);
                    $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
                    $offset = $pagesize * ($page - 1);
                    $rs = $DB->query("SELECT a.*,(select count(b.tid) from pre_faka as b where b.tid=a.tid and orderid=0) as leftcount,(select count(b.tid) from pre_faka as b where b.tid=a.tid and orderid!=0) as sellcount FROM pre_tools as a WHERE" . $sql . " order by sort asc limit " . $offset . "," . $pagesize);
                    $isEmpty = true;
                    
                    while ($res = $rs->fetch()) {
                        $isEmpty = false;
                        $statusBadge = $res["active"] == 1 
                            ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["tid"] . ",0, event)\" title=\"点击下架\">上架中</span>" 
                            : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["tid"] . ",1, event)\" title=\"点击上架\">已下架</span>";
                            
                        echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200 cursor-pointer\" onclick=\"toggleRowSelection(this)\">
                                <td class=\"px-6 py-4 text-center\">
                                    <label class=\"inline-flex items-center cursor-pointer m-0\" onclick=\"event.stopPropagation();\">
                                        <input type=\"checkbox\" name=\"checkbox[]\" class=\"list1-checkbox w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer\" value=\"" . $res["tid"] . "\">
                                        <b class=\"ml-3 text-slate-700 font-mono\">#" . $res["tid"] . "</b>
                                    </label>
                                </td>
                                <td class=\"px-6 py-4 font-bold text-brand-600 max-w-[300px] truncate\" title=\"" . $res["name"] . "\">" . $res["name"] . "</td>
                                <td class=\"px-6 py-4 text-center font-bold text-emerald-500 text-base\">" . $res["leftcount"] . "</td>
                                <td class=\"px-6 py-4 text-center font-bold text-rose-500 text-base\">" . $res["sellcount"] . "</td>
                                <td class=\"px-6 py-4 text-center\" onclick=\"event.stopPropagation();\">" . $statusBadge . "</td>
                                <td class=\"px-6 py-4 text-center whitespace-nowrap\" onclick=\"event.stopPropagation();\">
                                    <div class=\"inline-flex items-center gap-1.5\">
                                        <a href=\"./fakakms.php?tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\"><i class=\"fa fa-list-alt mr-1\"></i>卡密</a>
                                        <a href=\"./fakakms.php?my=add&tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm\"><i class=\"fa fa-plus mr-1\"></i>加卡</a>
                                        <a href=\"./list.php?tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm\"><i class=\"fa fa-shopping-cart mr-1\"></i>订单</a>
                                        <a href=\"./shopedit.php?my=delete&tid=" . $res["tid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('确定要彻底删除该发卡商品吗？\\n警告：删除后该商品下的所有卡密也将被丢弃！');\"><i class=\"fa fa-trash-o\"></i></a>
                                    </div>
                                </td>
                              </tr>";
                    }
                    
                    if($isEmpty) {
                        echo '<tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
                                <p class="text-sm font-medium text-slate-500">当前分类下暂无任何发卡商品</p>
                              </td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            
            <!-- 批量操作控制区 -->
            <?php if(!$isEmpty): ?>
            <div class="px-6 py-4 bg-slate-50/50 border-t border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <i class="fa fa-level-up fa-rotate-90 text-slate-300 ml-2"></i>
                    <select name="cid" class="h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors w-40">
                        <option selected value="0">请选择批量操作</option>
                        <option value="-1" class="text-emerald-600 font-medium">➡️ 批量改为上架中</option>
                        <option value="-2" class="text-amber-600 font-medium">➡️ 批量改为已下架</option>
                        <option value="-3" class="text-rose-600 font-bold">➡️ 删除选中商品</option>
                    </select>
                </div>
                <button type="submit" name="Submit" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200" onclick="return confirmBatchAction()">
                    确认执行操作
                </button>
            </div>
            <?php endif; ?>
        </form>
        
        <!-- 分页器组件 -->
        <?php if(!$isEmpty): ?>
        <div class="py-5">
            <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 m-0 list-none p-0">
            <?php 
                $first = 1;
                $prev = $page - 1;
                $next = $page + 1;
                $last = $pages;

                $btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
                $btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
                $btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

                if ($page > 1) {
                    echo "<li><a href=\"fakalist.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
                    echo "<li><a href=\"fakalist.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
                } else {
                    echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
                    echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
                }
                $start = $page - 10 > 1 ? $page - 10 : 1;
                $end = $page + 10 < $pages ? $page + 10 : $pages;
                for ($i = $start; $i < $page; $i++) {
                    echo "<li><a href=\"fakalist.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
                }
                echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo "<li><a href=\"fakalist.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
                }
                if ($page < $pages) {
                    echo "<li><a href=\"fakalist.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
                    echo "<li><a href=\"fakalist.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
                } else {
                    echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
                    echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
                }
            ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
<?php 
}
?>
</div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
// 优化后的整行点击勾选与全选控制
function toggleAllCheckboxes(source) {
    var checkboxes = document.querySelectorAll('.list1-checkbox');
    for(var i=0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
        updateRowStyle(checkboxes[i]);
    }
}

function toggleRowSelection(row) {
    var checkbox = row.querySelector('.list1-checkbox');
    if(checkbox) {
        checkbox.checked = !checkbox.checked;
        updateRowStyle(checkbox);
        syncSelectAllStatus();
    }
}

function updateRowStyle(checkbox) {
    var tr = checkbox.closest('tr');
    if(checkbox.checked) {
        tr.classList.add('bg-brand-50/60');
    } else {
        tr.classList.remove('bg-brand-50/60');
    }
}

function syncSelectAllStatus() {
    var allCheckboxes = document.querySelectorAll('.list1-checkbox');
    var checkedCheckboxes = document.querySelectorAll('.list1-checkbox:checked');
    var selectAllBtn = document.getElementById('chkAll1');
    if(selectAllBtn && allCheckboxes.length > 0) {
        selectAllBtn.checked = (allCheckboxes.length === checkedCheckboxes.length);
    }
}

// 绑定单独点击复选框时的样式更新
document.querySelectorAll('.list1-checkbox').forEach(function(cb) {
    cb.addEventListener('change', function(e) {
        updateRowStyle(this);
        syncSelectAllStatus();
    });
    // 阻止复选框的点击事件冒泡，防止触发行的点击事件导致状态被翻转回来
    cb.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});

// 设置商品上/下架状态
function setActive(tid, active, event) {
    if(event) event.stopPropagation(); // 阻止触发整行的勾选事件
	$.ajax({
		type : 'GET',
		url : 'ajax_shop.php?act=setTools&tid='+tid+'&active='+active,
		dataType : 'json',
		success : function(data) {
			window.location.reload();
		},
		error:function(data){
			layer.msg('服务器网络错误', {icon: 2});
			return false;
		}
	});
}

// 批量操作的表单确认
function confirmBatchAction() {
    var action = document.querySelector('select[name="cid"]').value;
    if(action === '0' || action === '批量操作') {
        layer.msg('请先选择需要执行的批量操作项', {icon: 0});
        return false;
    }
    
    var checkedCheckboxes = document.querySelectorAll('.list1-checkbox:checked');
    if(checkedCheckboxes.length === 0) {
        layer.msg('请至少在列表中勾选一个商品', {icon: 0});
        return false;
    }
    
    if(action === '-3') {
        return confirm('确定要删除选中的 ' + checkedCheckboxes.length + ' 个商品吗？\n警告：删除后商品及其包含的所有卡密信息将永久丢失！');
    }
    
    return true;
}

// 默认值初始化
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
