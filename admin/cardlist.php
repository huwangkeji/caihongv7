<?php

include "../includes/common.php";
$title = "商品兑换卡密列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<!-- 搜索卡密模态框 (HuWangV7 重构) -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="searchModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="text-base font-bold text-slate-800 m-0" id="myModalLabel">搜索卡密</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form action="cardlist.php" method="GET" class="space-y-4">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa fa-search text-slate-400 text-sm"></i>
                </div>
                <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入卡密">
            </div>
            <div class="pt-2">
                <input type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer" value="搜 索">
            </div>
        </form>
      </div>
  </div>
</div>

<div class="w-full max-w-[1400px] mx-auto">
<?php 
adminpermission("site", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	$rs = $DB->query("SELECT * FROM pre_class WHERE 1 order by sort asc");
	$select = "<option value=\"0\">请选择商品分类</option>";
	while ($res = $rs->fetch()) {
		$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
	}
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">生成卡密</h3>
        </div>
        <div class="p-6 sm:p-8">
            <form action="./cardlist.php?my=add_submit" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">选择商品</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer"><?php echo $select;?></select>
                        <select id="tid" name="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer"></select>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">商品份数</label>
                    <input type="number" min="1" max="1000" name="value" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="一张卡密可兑换几份该商品"/>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">生成数量</label>
                    <input type="number" min="1" max="1000" name="num" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="输入生成卡密的数量"/>
                </div>
                
                <div class="pt-2">
                    <input type="submit" name="submit" value="确认添加" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"/>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <a href="./cardlist.php" class="text-sm font-medium text-brand-600 hover:underline"><i class="fa fa-angle-double-left mr-1"></i>返回卡密列表</a>
            </div>
        </div>
    </div>
    
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
$(document).ready(function(){
	$("#cid").change(function () {
		var cid = $(this).val();
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$("#tid").empty();
		$("#tid").append('<option value="0">请选择商品</option>');
		$.ajax({
			type : "GET",
			url : "./ajax.php?act=gettool&cid="+cid,
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
					if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有商品</option>');
				}else{
					layer.alert(data.msg);
				}
			},
			error:function(data){
                layer.close(ii);
				layer.msg('服务器错误');
				return false;
			}
		});
	});
	if($("#cid").length>0){
		$("#cid").change();
	}
});
</script>
<?php 
} elseif ($my == "add_submit") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_POST["tid"]);
	$value = intval($_POST["value"]);
	$num = intval($_POST["num"]);
	if ($value == 0) {
		showmsg("购买份数不能为空", 3);
	}
    
    echo "<div class=\"max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden\">";
	echo "<div class=\"px-6 py-4 bg-emerald-500 text-white font-bold flex items-center\"><i class=\"fa fa-check-circle mr-2 text-xl\"></i>成功生成以下卡密</div>";
    echo "<div class=\"p-6\">";
    echo "<div class=\"bg-slate-50 border border-slate-200 rounded-xl max-h-96 overflow-y-auto p-4 space-y-2 font-mono text-sm text-slate-700 break-all select-all\">";
	for ($i = 0; $i < $num; $i++) {
		$km = getkm(18);
		$sql = $DB->exec("insert into `pre_kms` (`type`,`km`,`tid`,`num`,`addtime`) values (1,'" . $km . "','" . $tid . "','" . $value . "','" . $date . "')");
		if ($sql) {
			echo "<div>" . $km . "</div>";
		}
	}
    echo "</div>";
	echo "<div class=\"mt-6\"><a href=\"./cardlist.php\" class=\"inline-flex items-center justify-center w-full h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-medium hover:bg-slate-200 shadow-sm transition-all duration-200\"><i class=\"fa fa-angle-double-left mr-2\"></i>返回卡密列表</a></div>";
    echo "</div></div>";
    
} elseif ($my == "del") {
	if (!checkRefererHost()) {
		exit;
	}
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 font-bold text-slate-800">系统提示</div>
        <div class="p-8 text-center space-y-6">
        <?php 
        $id = intval($_GET["id"]);
        $sql = $DB->exec("DELETE FROM pre_kms WHERE kid=:id", array(':id' => $id));
        if ($sql) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">删除成功！</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">删除失败！</p>";
        }
        ?>
            <a href="./cardlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php 
} elseif ($my == "qk") {
	if (!checkRefererHost()) {
		exit;
	}
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-rose-50/80 border-b border-rose-100 font-bold text-rose-600 flex items-center"><i class="fa fa-exclamation-triangle mr-2"></i>操作警告</div>
        <div class="p-8 text-center space-y-6">
            <i class="fa fa-trash text-5xl text-rose-400 mb-2 block"></i>
            <p class="text-lg font-medium text-slate-700">您确认要清空所有卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后数据将无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./cardlist.php?my=qk2" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors flex-1">确认清空</a>
            </div>
        </div>
    </div>
    <?php 
} elseif ($my == "qk2") {
	if (!checkRefererHost()) {
		exit;
	}
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 font-bold text-slate-800">系统提示</div>
        <div class="p-8 text-center space-y-6">
        <?php 
        if ($DB->exec("DELETE FROM pre_kms WHERE type=1") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">所有卡密已成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./cardlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php 
} elseif ($my == "qkuse") {
	if (!checkRefererHost()) {
		exit;
	}
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-amber-50/80 border-b border-amber-100 font-bold text-amber-600 flex items-center"><i class="fa fa-exclamation-triangle mr-2"></i>操作警告</div>
        <div class="p-8 text-center space-y-6">
            <i class="fa fa-eraser text-5xl text-amber-400 mb-2 block"></i>
            <p class="text-lg font-medium text-slate-700">您确认要清空所有<span class="text-amber-500">已使用</span>的卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后记录将无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./cardlist.php?my=qkuse2" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-colors flex-1">确认清空</a>
            </div>
        </div>
    </div>
    <?php 
} elseif ($my == "qkuse2") {
	if (!checkRefererHost()) {
		exit;
	}
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 font-bold text-slate-800">系统提示</div>
        <div class="p-8 text-center space-y-6">
        <?php 
        if ($DB->exec("DELETE FROM pre_kms WHERE type=1 AND status=1") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">已使用的卡密已成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./cardlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php 
} else {
	if (isset($_GET["kw"])) {
		$kw = daddslashes($_GET["kw"]);
		$numrows = $DB->getColumn("SELECT count(*) from pre_kms WHERE type=1 AND `km`=:km", array(':km' => $kw));
		$con = "包含 <span class=\"font-bold text-brand-600\">" . htmlspecialchars($kw) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个卡密";
	} else {
		$kw = '';
		$numrows = $DB->getColumn("SELECT count(*) from pre_kms WHERE type=1");
		$con = "系统共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个兑换卡密";
	}
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- 头部统计与操作区 -->
        <div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-base font-medium text-slate-700 m-0"><?php echo $con;?></h2>
            
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <button type="button" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-sky-50 text-sky-600 font-medium text-[13px] sm:text-sm hover:bg-sky-500 hover:text-white shadow-sm transition-colors cursor-pointer open-search">
                    <i class="fa fa-search mr-1.5"></i>搜索
                </button>
                <a href="cardlist.php?my=add" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-emerald-500 text-white font-medium text-[13px] sm:text-sm hover:bg-emerald-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-plus-circle mr-1.5"></i>生成卡密
                </a>
                <a href="cardlist.php?my=qkuse" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-amber-500 text-white font-medium text-[13px] sm:text-sm hover:bg-amber-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-eraser mr-1.5"></i>清空已使用
                </a>
                <a href="cardlist.php?my=qk" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-rose-500 text-white font-medium text-[13px] sm:text-sm hover:bg-rose-600 shadow-sm transition-all duration-200">
                    <i class="fa fa-trash-o mr-1.5"></i>全部清空
                </a>
            </div>
        </div>

        <!-- 响应式表格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
              <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5 font-semibold text-left min-w-[200px]">卡密</th>
                    <th class="px-6 py-3.5 font-semibold text-left">商品</th>
                    <th class="px-6 py-3.5 font-semibold text-center">状态</th>
                    <th class="px-6 py-3.5 font-semibold text-center">添加时间</th>
                    <th class="px-6 py-3.5 font-semibold text-center">使用时间</th>
                    <th class="px-6 py-3.5 font-semibold text-center">操作</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	if($kw){
		$rs = $DB->getAll("SELECT A.*,B.name FROM pre_kms A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE type=1 AND `km`=:km order by kid desc limit ".$offset.",".$pagesize, array(':km' => $kw));
	} else {
		$rs = $DB->getAll("SELECT A.*,B.name FROM pre_kms A LEFT JOIN pre_tools B ON A.tid=B.tid WHERE type=1 order by kid desc limit ".$offset.",".$pagesize);
	}
	if($rs === false) {
		$rs = array();
	}
	foreach ($rs as $res) {
		if ($res["status"] == 0) {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">未使用</span>";
		} else {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-500 border border-rose-100\">已使用</span> <a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class=\"text-xs text-brand-600 hover:underline ml-1\" title=\"查看对应订单\">(#" . $res["orderid"] . ")</a>";
		}
		$shopname = mb_substr($res["name"], 0, 20, "utf-8");
		echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-3 font-mono font-bold text-slate-700 tracking-tight\">" . $res["km"] . "</td>
            <td class=\"px-6 py-3 text-slate-600 max-w-[200px] truncate\" title=\"".$res["name"]."\">" . $shopname . ($res["num"] > 1 ? " <span class=\"font-semibold text-rose-500\">×" . $res["num"] . "</span>" : "") . "</td>
            <td class=\"px-6 py-3 text-center\">" . $isuse . "</td>
            <td class=\"px-6 py-3 text-center text-xs text-slate-500\">" . $res["addtime"] . "</td>
            <td class=\"px-6 py-3 text-center text-xs text-slate-400\">" . ($res["usetime"] ?: "-") . "</td>
            <td class=\"px-6 py-3 text-center\"><a href=\"./cardlist.php?my=del&id=" . $res["kid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('你确实要删除此卡密吗？');\">删除</a></td>
        </tr>";
	}
	?>          </tbody>
        </table>
      </div>
      
      <!-- 底部提示信息区 -->
      <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 space-y-1.5">
          <div class="flex items-center">
              <i class="fa fa-info-circle text-brand-500 mr-2 text-sm shrink-0"></i> 
              <span>卡密兑换页面地址：<a href="/?mod=cardbuy" target="_blank" class="text-brand-600 hover:underline font-medium">/?mod=cardbuy</a></span>
          </div>
          <div class="flex items-center">
              <i class="fa fa-cog text-brand-500 mr-2 text-sm shrink-0"></i> 
              <span>开启卡密兑换功能：<a href="./set.php?mod=site" class="text-brand-600 hover:underline font-medium">系统设置 -> 网站信息配置</a> 中找到"开启卡密兑换功能"选项</span>
          </div>
      </div>
    </div>
    
    <!-- 分页器组件 -->
    <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-6 mb-2 list-none p-0">
<?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;

	$btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
	$btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
	$btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

	if ($page > 1) {
		echo "<li><a href=\"cardlist.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
		echo "<li><a href=\"cardlist.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"cardlist.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"cardlist.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"cardlist.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
		echo "<li><a href=\"cardlist.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
		echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
	}
	?></ul>
<?php 
}
?>    
</div>

<script>
// 控制搜索弹窗显示隐藏的纯 JS 逻辑
document.addEventListener('DOMContentLoaded', function() {
    var searchModal = document.getElementById('searchModal');
    if (!searchModal) return;
    
    // 打开弹窗
    var openBtns = document.querySelectorAll('.open-search');
    openBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            searchModal.classList.remove('hidden');
            setTimeout(function() {
                searchModal.classList.remove('opacity-0');
                searchModal.querySelector('.transform').classList.remove('scale-95');
            }, 10);
        });
    });
    
    // 关闭弹窗
    var closeBtns = document.querySelectorAll('.close-modal');
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            searchModal.classList.add('opacity-0');
            searchModal.querySelector('.transform').classList.add('scale-95');
            setTimeout(function() {
                searchModal.classList.add('hidden');
            }, 300);
        });
    });
    
    // 点击背景层关闭
    searchModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.querySelector('.close-modal').click();
        }
    });
});
</script>

<?php 
function getkm($len = 18)
{
	$_var_0 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	$_var_1 = strlen($_var_0);
	$_var_2 = "";
	for ($_var_3 = 0; $_var_3 < $len; $_var_3++) {
		$_var_2 .= $_var_0[mt_rand(0, $_var_1 - 1)];
	}
	return $_var_2;
}
?>
