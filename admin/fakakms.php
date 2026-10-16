<?php

include "../includes/common.php";
$title = "卡密列表";
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
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#searchModal">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form action="fakakms.php" method="GET" class="space-y-4">
            <input type="hidden" name="tid" value="<?php echo htmlspecialchars(@$_GET["tid"], ENT_QUOTES, 'UTF-8');?>">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa fa-search text-slate-400 text-sm"></i>
                </div>
                <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入卡号或密码" required>
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
adminpermission("faka", 1);
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">请选择商品分类</option>";
while ($res = $rs->fetch()) {
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	if (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"] ?? 0);
		$row = $DB->getRow("select cid,name from pre_tools where tid='" . $tid . "' limit 1");
		$shopname = "<option value=\"" . $tid . "\">" . $row["name"] . "</option>";
		$cid = $row["cid"];
	} else {
		$cid = 0;
		$tid = 0;
		$shopname = '';
	}
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">添加卡密</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./fakakms.php?my=add_submit" method="POST" onsubmit="return checkAdd()" class="space-y-6">
                <input type="hidden" name="backurl" value="<?php echo $_SERVER["HTTP_REFERER"] ?? '';?>"/>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">选择商品</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <select id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $cid;?>"><?php echo $select;?></select>
                        <select id="tid" name="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $tid;?>"><?php echo $shopname;?></select>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">卡密列表</label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" id="kms" name="kms" rows="8" placeholder="一行一张卡，或使用下方随机生成功能"></textarea>
                </div>
                
                <div class="p-5 bg-slate-50 border border-slate-100 rounded-xl space-y-4">
                    <div class="text-sm font-semibold text-slate-700 flex items-center mb-1"><i class="fa fa-magic text-brand-500 mr-1.5"></i>辅助随机生成工具</div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">生成数量</label>
                            <input type="number" id="gen_num" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 shadow-sm" value="10" min="1" max="1000">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">卡号长度</label>
                            <input type="number" id="gen_km_len" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 shadow-sm" value="16" min="4" max="64">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">密码长度</label>
                            <input type="number" id="gen_pw_len" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 shadow-sm" value="16" min="4" max="64">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">分隔符</label>
                            <input type="text" id="gen_split" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 shadow-sm" value=" " maxlength="10" placeholder="空格">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-4 pt-1">
                        <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                            <input type="checkbox" id="gen_use_num" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer" checked>
                            <span class="ml-2 text-sm text-slate-700">数字 (0-9)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                            <input type="checkbox" id="gen_use_low" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer" checked>
                            <span class="ml-2 text-sm text-slate-700">小写字母 (a-z)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                            <input type="checkbox" id="gen_use_up" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer" checked>
                            <span class="ml-2 text-sm text-slate-700">大写字母 (A-Z)</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                            <input type="checkbox" id="gen_use_spec" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                            <span class="ml-2 text-sm text-slate-700">特殊字符</span>
                        </label>
                    </div>
                    
                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-10 rounded-lg bg-indigo-50 text-indigo-600 font-medium text-sm hover:bg-indigo-500 hover:text-white transition-colors shadow-sm" onclick="generatekms()"><i class="fa fa-magic mr-1.5"></i>生成卡密</button>
                        <button type="button" class="w-full sm:w-auto inline-flex items-center justify-center px-5 h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium text-sm hover:bg-slate-50 hover:text-rose-500 transition-colors shadow-sm" onclick="clearkms()"><i class="fa fa-trash-o mr-1.5"></i>清空内容</button>
                        <span class="text-xs text-slate-400 mt-2 sm:mt-0 sm:ml-auto">提示：生成的卡密会自动填入上方列表框</span>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">自定义分隔符 <span class="font-normal text-slate-400 text-xs ml-1">(针对上方手动输入的列表有效)</span></label>
                    <input type="text" name="split" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="可自定义卡号和密码之间的分隔符，默认留空为空格"/>
                </div>
                
                <div class="space-y-1.5">
                    <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                        <input id="is_check_repeat" name="is_check_repeat" type="checkbox" value="1" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                        <span class="ml-2 text-sm font-semibold text-slate-700">提交前自动检查并过滤重复的卡密</span>
                    </label>
                </div>
                
                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        确认提交添加
                    </button>
                    <button type="reset" class="w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        重新填写
                    </button>
                </div>
            </form>
            
            <div class="mt-6 p-4 bg-slate-50 border border-slate-100 rounded-xl text-xs text-slate-500 space-y-1.5">
                <p><i class="fa fa-info-circle text-brand-500 mr-1.5 text-sm"></i><span class="font-bold text-slate-700">卡密格式：</span>卡号+分隔符+密码，一行一张卡，如：<code class="bg-white border border-slate-200 px-1 rounded mx-1">ABCDEFG 123456789</code></p>
                <p><i class="fa fa-info-circle text-brand-500 mr-1.5 text-sm"></i><span class="font-bold text-slate-700">展示限制：</span>只有商品设置里面购买成功后的动作选择自动发卡，该商品才会显示在当前列表选项中。</p>
            </div>
            
            <div class="mt-6 text-center">
                <a href="<?php echo isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "fakalist.php";?>" class="text-sm font-medium text-brand-600 hover:underline"><i class="fa fa-angle-double-left mr-1"></i>返回发卡库存列表</a>
            </div>
        </div>
    </div>
<?php 
} elseif ($my == "add_submit") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_POST["tid"]);
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
			$sql = $DB->exec("INSERT INTO `pre_faka` (`tid`,`km`,`pw`,`addtime`) VALUES ('" . $tid . "','" . $km . "','" . $pw . "',NOW())");
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
	$id = intval($_GET["id"] ?? 0);
	$sql = $DB->exec("DELETE FROM pre_faka WHERE kid='" . $id . "'");
	exit("<script language='javascript'>history.go(-1);</script>");
} elseif ($my == "qk") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"] ?? 0);
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-rose-50/80 border-b border-rose-100 font-bold text-rose-600 flex items-center"><i class="fa fa-exclamation-triangle mr-2"></i>操作警告</div>
        <div class="p-8 text-center space-y-6">
            <i class="fa fa-trash text-5xl text-rose-400 mb-2 block"></i>
            <p class="text-lg font-medium text-slate-700">您确认要彻底清空该商品的所有卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后数据将永远无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./fakakms.php?my=qk2&tid=<?php echo $tid; ?>" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors flex-1">确认强制清空</a>
            </div>
        </div>
    </div>
    <?php
} elseif ($my == "qk2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"] ?? 0);
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 font-bold text-slate-800">系统提示</div>
        <div class="p-8 text-center space-y-6">
        <?php 
        if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "'") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">卡密已全部成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./fakakms.php?tid=<?php echo $tid; ?>" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php
} elseif ($my == "qkuse") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"] ?? 0);
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-amber-50/80 border-b border-amber-100 font-bold text-amber-600 flex items-center"><i class="fa fa-exclamation-triangle mr-2"></i>操作警告</div>
        <div class="p-8 text-center space-y-6">
            <i class="fa fa-eraser text-5xl text-amber-400 mb-2 block"></i>
            <p class="text-lg font-medium text-slate-700">您确认要清空该商品所有<span class="text-amber-500">已使用</span>的卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后记录将无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./fakakms.php?my=qkuse2&tid=<?php echo $tid; ?>" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-colors flex-1">确认清空</a>
            </div>
        </div>
    </div>
    <?php
} elseif ($my == "qkuse2") {
	if (!checkRefererHost()) {
		exit;
	}
	$tid = intval($_GET["tid"] ?? 0);
	?>
    <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 font-bold text-slate-800">系统提示</div>
        <div class="p-8 text-center space-y-6">
        <?php 
        if ($DB->exec("DELETE FROM pre_faka WHERE tid='" . $tid . "' and orderid!=0") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">已使用的卡密已成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./fakakms.php?tid=<?php echo $tid; ?>" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php
} elseif ($my == "del2") {
	if (!checkRefererHost()) {
		exit;
	}
	$checkbox = $_POST["checkbox"];
	$i = 0;
	foreach ($checkbox as $kid) {
		$DB->exec("DELETE FROM pre_faka WHERE kid='" . $kid . "' limit 1");
		$i++;
	}
	exit("<script language='javascript'>alert('成功删除" . $i . "张卡密');history.go(-1);</script>");
} else {
	if (isset($_GET["kw"])) {
		$tid = intval($_GET["tid"] ?? 0);
		$sql = " `tid`='" . $tid . "' and (`km`='" . $_GET["kw"] . "' or `pw`='" . $_GET["kw"] . "')";
		$link = "&tid=" . $tid . "&kw=" . $_GET["kw"];
	} elseif (isset($_GET["kid"])) {
		$sql = " `kid`='" . $_GET["kid"] . "'";
		$link = "&kid=" . $_GET["kid"];
	} elseif (isset($_GET["orderid"])) {
		$sql = " `orderid`='" . $_GET["orderid"] . "'";
		$link = "&orderid=" . $_GET["orderid"];
	} elseif (isset($_GET["tid"])) {
		$tid = intval($_GET["tid"] ?? 0);
		$row = $DB->getRow("select * from pre_tools where tid='" . $tid . "' limit 1");
		if (!$row) {
			showmsg("商品不存在", 3);
		}
		$sql = " `tid`='" . $tid . "'";
		$link = "&tid=" . $tid;
	} else {
		showmsg("商品不存在", 3);
	}
	$numrows = $DB->getColumn("SELECT count(*) from pre_faka WHERE" . $sql);
	?>
<!-- 导出未使用的卡密模态框 (HuWangV7 重构) -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="outputModal" role="dialog" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="text-base font-bold text-slate-800 m-0">导出指定数量未使用的卡密</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#outputModal">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form action="download.php" method="GET" class="space-y-4">
            <input type="hidden" name="act" value="kms">
            <input type="hidden" name="tid" value="<?php echo $tid;?>">
            <input type="hidden" name="use" value="0">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="number" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="num" placeholder="请输入要导出的数量" required>
                </div>
                <select name="isuse" class="w-full sm:w-40 h-11 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors bg-slate-50 shrink-0">
                    <option value="0">不改为已使用</option>
                    <option value="1">同时改为已使用</option>
                </select>
            </div>
            <div class="pt-2">
                <input type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer" value="确认导出并下载">
            </div>
        </form>
      </div>
  </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
	<div class="px-4 sm:px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
		<h2 class="text-base font-bold text-slate-800 m-0 max-w-[300px] truncate" title="<?php echo $row["name"];?>">
            <?php echo $row["name"];?> <span class="font-normal text-slate-500 text-sm ml-1">- 卡密库存列表</span>
        </h2>
        
        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <a href="fakakms.php?my=add&tid=<?php echo $tid;?>" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-emerald-500 text-white font-medium text-[13px] sm:text-sm hover:bg-emerald-600 shadow-sm transition-all duration-200">
                <i class="fa fa-plus mr-1.5"></i>加卡
            </a>
            <a href="#" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-sky-50 text-sky-600 font-medium text-[13px] sm:text-sm hover:bg-sky-500 hover:text-white shadow-sm transition-colors cursor-pointer open-modal" data-target="#searchModal">
                <i class="fa fa-search mr-1.5"></i>搜索
            </a>
            
            <!-- HuWangV7 原生相对定位下拉菜单 -->
            <div class="relative group flex-1 md:flex-none">
                <button type="button" class="w-full inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-indigo-500 text-white font-medium text-[13px] sm:text-sm hover:bg-indigo-600 shadow-sm transition-all duration-200">
                    导出 <i class="fa fa-caret-down ml-1.5"></i>
                </button>
                <div class="absolute right-0 mt-1 w-32 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <ul class="py-1.5 list-none m-0 p-0 text-sm">
                        <li><a href="download.php?act=kms<?php echo $link;?>&use=0" class="block px-4 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">未使用</a></li>
                        <li><a href="download.php?act=kms<?php echo $link;?>&use=1" class="block px-4 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">已使用</a></li>
                        <li><a href="download.php?act=kms<?php echo $link;?>" class="block px-4 py-2 text-slate-700 hover:bg-brand-50 hover:text-brand-600 font-medium transition-colors">全部</a></li>
                        <li class="border-t border-slate-100 my-1"></li>
                        <li><a href="#" class="block px-4 py-2 text-sky-600 hover:bg-sky-50 font-medium transition-colors open-modal" data-target="#outputModal">指定数量</a></li>
                    </ul>
                </div>
            </div>
            
            <a href="fakakms.php?my=qkuse&tid=<?php echo $tid;?>" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-amber-500 text-white font-medium text-[13px] sm:text-sm hover:bg-amber-600 shadow-sm transition-all duration-200">
                清空已用
            </a>
            <a href="fakakms.php?my=qk&tid=<?php echo $tid;?>" class="flex-1 md:flex-none inline-flex items-center justify-center h-9 sm:h-10 px-4 rounded-xl bg-rose-500 text-white font-medium text-[13px] sm:text-sm hover:bg-rose-600 shadow-sm transition-all duration-200">
                全部清空
            </a>
        </div>
	</div>
    
	<form name="form1" method="post" action="fakakms.php?my=del2">
      <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
          <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="px-6 py-3.5 font-semibold">卡号</th>
              <th class="px-6 py-3.5 font-semibold">密码</th>
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
	$rs = $DB->query("SELECT * FROM pre_faka WHERE" . $sql . " order by kid desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		if ($res["usetime"] == null) {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">未使用</span>";
		} else {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-500 border border-rose-100\">已使用</span> <a href=\"./list.php?id=" . $res["orderid"] . "\" target=\"_blank\" class=\"text-xs text-brand-600 hover:underline ml-1\" title=\"查看订单\">(#" . $res["orderid"] . ")</a>";
		}
		echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200 cursor-pointer\" onclick=\"showkms(this, event)\">
                <td class=\"px-6 py-3 flex items-center\">
                    <label class=\"inline-flex items-center cursor-pointer m-0\" onclick=\"event.stopPropagation();\">
                        <input type=\"checkbox\" name=\"checkbox[]\" class=\"list1-checkbox w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer\" value=\"" . $res["kid"] . "\">
                    </label>
                    <b class=\"font-mono text-slate-700 tracking-tight ml-3 max-w-[200px] truncate\" title=\"".$res["km"]."\">" . $res["km"] . "</b>
                </td>
                <td class=\"px-6 py-3 font-mono text-slate-500 max-w-[150px] truncate\" title=\"".$res["pw"]."\">" . $res["pw"] . "</td>
                <td class=\"px-6 py-3 text-center\">" . $isuse . "</td>
                <td class=\"px-6 py-3 text-center text-xs text-slate-500\">" . $res["addtime"] . "</td>
                <td class=\"px-6 py-3 text-center text-xs text-slate-400\">" . ($res["usetime"] ?: "-") . "</td>
                <td class=\"px-6 py-3 text-center\" onclick=\"event.stopPropagation();\">
                    <a href=\"./fakakms.php?my=del&id=" . $res["kid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('你确实要删除此卡密吗？');\">删除</a>
                </td>
              </tr>";
	}
	?>          </tbody>
        </table>
      </div>
      
      <!-- 批量操作控制区 -->
      <div class="px-6 py-4 bg-slate-50/50 border-t border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
          <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
              <input type="checkbox" id="chkAll1" class="w-4.5 h-4.5 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer" onclick="toggleAllCheckboxes(this)">
              <span class="ml-2 text-sm font-bold text-slate-700">全选本页</span>
          </label>
          <button type="submit" name="Submit" class="inline-flex items-center justify-center h-9 px-5 rounded-lg bg-rose-500 text-white font-medium text-sm hover:bg-rose-600 shadow-sm transition-all duration-200" onclick="return confirm('确认要删除所有选中的卡密吗？此操作不可逆！');">
              <i class="fa fa-trash mr-1.5"></i>删除选中记录
          </button>
      </div>
    </form>
    
    <!-- 分页器组件 -->
    <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-5 mb-5 list-none p-0">
<?php 
	$first = 1;
	$prev = $page - 1;
	$next = $page + 1;
	$last = $pages;

	$btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
	$btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
	$btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

	if ($page > 1) {
		echo "<li><a href=\"fakakms.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
		echo "<li><a href=\"fakakms.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"fakakms.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"fakakms.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"fakakms.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
		echo "<li><a href=\"fakakms.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
		echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
	}
	?></ul>
</div>
<?php 
}
?>
</div>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/fakakms.js?ver=<?php echo VERSION;?>"></script>
<script>
// 原生模态框控制逻辑
document.addEventListener('DOMContentLoaded', function() {
    var openBtns = document.querySelectorAll('.open-modal');
    openBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('data-target');
            var modal = document.querySelector(targetId);
            if (modal) {
                modal.classList.remove('hidden');
                setTimeout(function() {
                    modal.classList.remove('opacity-0');
                    modal.querySelector('.transform').classList.remove('scale-95');
                }, 10);
            }
        });
    });
    
    var closeBtns = document.querySelectorAll('.close-modal');
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-target');
            var modal = document.querySelector(targetId);
            if (modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(function() {
                    modal.classList.add('hidden');
                }, 300);
            }
        });
    });
    
    document.querySelectorAll('[role="dialog"]').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('.close-modal').click();
            }
        });
    });
});

// 优化后的整行点击勾选与全选控制
function toggleAllCheckboxes(source) {
    var checkboxes = document.querySelectorAll('.list1-checkbox');
    for(var i=0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
        updateRowStyle(checkboxes[i]);
    }
}

function showkms(row, event) {
    // 确保点击的不是 checkbox 或其直接相关的父级
    if(event.target.tagName.toLowerCase() !== 'input' && event.target.tagName.toLowerCase() !== 'label') {
        var checkbox = row.querySelector('.list1-checkbox');
        if(checkbox) {
            checkbox.checked = !checkbox.checked;
            updateRowStyle(checkbox);
            
            // 同步全选按钮状态
            syncSelectAllStatus();
        }
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
    cb.addEventListener('change', function() {
        updateRowStyle(this);
        syncSelectAllStatus();
    });
});

// 生成卡密逻辑
function generateRandomString(length, options) {
	var chars = '';
	if (options.useNum) chars += '0123456789';
	if (options.useLow) chars += 'abcdefghijklmnopqrstuvwxyz';
	if (options.useUp) chars += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if (options.useSpec) chars += '!@#$%^&*()_+-=[]{}|;:,.<>?';
	if (chars === '') chars = '0123456789';
	var result = '';
	for (var i = 0; i < length; i++) {
		result += chars.charAt(Math.floor(Math.random() * chars.length));
	}
	return result;
}
function generatekms() {
	var num = parseInt($("#gen_num").val()) || 10;
	var kmLen = parseInt($("#gen_km_len").val()) || 16;
	var pwLen = parseInt($("#gen_pw_len").val()) || 16;
	var split = $("#gen_split").val() || " ";
	var useNum = $("#gen_use_num").prop("checked");
	var useLow = $("#gen_use_low").prop("checked");
	var useUp = $("#gen_use_up").prop("checked");
	var useSpec = $("#gen_use_spec").prop("checked");
	if (num < 1 || num > 1000) {
		layer.msg('数量必须在1-1000之间', {icon: 2});
		return;
	}
	if (kmLen < 4 || kmLen > 64 || pwLen < 4 || pwLen > 64) {
		layer.msg('卡号和密码长度必须在4-64之间', {icon: 2});
		return;
	}
	var kmOptions = {useNum: useNum, useLow: useLow, useUp: useUp, useSpec: useSpec};
	var kms = [];
	for (var i = 0; i < num; i++) {
		var km = generateRandomString(kmLen, kmOptions);
		var pw = generateRandomString(pwLen, kmOptions);
		kms.push(km + split + pw);
	}
	var existing = $("#kms").val();
	if (existing.trim()) {
		$("#kms").val(existing + "\n" + kms.join("\n"));
	} else {
		$("#kms").val(kms.join("\n"));
	}
	$("input[name='split']").val(split);
	layer.msg('成功生成 ' + num + ' 张卡密', {icon: 1});
}
function clearkms() {
	$("#kms").val('');
}
</script>
</body>
</html>
