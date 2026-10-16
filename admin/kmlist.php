<?php

include "../includes/common.php";
$title = "加款卡密列表";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="searchModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="text-base font-bold text-slate-800 m-0" id="myModalLabel">搜索加款卡密</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#searchModal">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form action="kmlist.php" method="GET" class="space-y-4">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa fa-search text-slate-400 text-sm"></i>
                </div>
                <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="请输入完整的卡密信息" required>
            </div>
            <div class="pt-2">
                <input type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98] cursor-pointer" value="搜 索">
            </div>
        </form>
      </div>
  </div>
</div>

<div class="w-full max-w-[1200px] mx-auto">
<?php 
adminpermission("site", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
	if (!checkRefererHost()) {
		exit;
	}
	$money = trim($_POST["money"]);
	$num = intval($_POST["num"]);
	if (!is_numeric($money) || !preg_match("/^[0-9.]+\$/", $money)) {
		showmsg("金额输入不规范", 3);
	}
    
    echo "<div class=\"max-w-2xl mx-auto bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden\">";
	echo "<div class=\"px-6 py-4 bg-emerald-500 text-white font-bold flex items-center\"><i class=\"fa fa-check-circle mr-2 text-xl\"></i>成功生成以下充值卡密</div>";
    echo "<div class=\"p-6\">";
    echo "<div class=\"bg-slate-50 border border-slate-200 rounded-xl max-h-[400px] overflow-y-auto p-4 space-y-2 font-mono text-sm text-slate-700 break-all select-all\">";
	for ($i = 0; $i < $num; $i++) {
		$km = getkm(18);
		$sql = $DB->exec("insert into `pre_kms` (`type`,`km`,`money`,`addtime`) values (0,'" . $km . "','" . $money . "','" . $date . "')");
		if ($sql) {
			echo "<div>" . $km . "</div>";
		}
	}
    echo "</div>";
	echo "<div class=\"mt-6\"><a href=\"./kmlist.php\" class=\"inline-flex items-center justify-center w-full h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-medium hover:bg-slate-200 shadow-sm transition-all duration-200\"><i class=\"fa fa-angle-double-left mr-2\"></i>返回卡密列表</a></div>";
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
        $id = $_GET["id"];
        $sql = $DB->exec("DELETE FROM pre_kms WHERE kid='" . $id . "'");
        if ($sql) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">删除成功！</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">删除失败！</p>";
        }
        ?>
            <a href="./kmlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
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
            <p class="text-lg font-medium text-slate-700">您确认要清空<span class="text-rose-600 font-bold mx-1">所有</span>加款卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后数据将永远无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./kmlist.php?my=qk2" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors flex-1">确认强制清空</a>
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
        if ($DB->exec("DELETE FROM pre_kms WHERE type=0") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">所有卡密已成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./kmlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
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
            <p class="text-lg font-medium text-slate-700">您确认要清空所有<span class="text-amber-500 font-bold mx-1">已使用</span>的加款卡密吗？<br><span class="text-rose-500 text-sm mt-2 block">清空后记录将无法恢复！</span></p>
            <div class="flex items-center justify-center gap-4 mt-6">
                <a href="javascript:history.back();" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors flex-1">返回上一步</a>
                <a href="./kmlist.php?my=qkuse2" class="inline-flex items-center justify-center h-11 px-6 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-colors flex-1">确认清空</a>
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
        if ($DB->exec("DELETE FROM pre_kms WHERE type=0 AND status=1") !== false) {
            echo "<i class=\"fa fa-check-circle text-5xl text-emerald-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">已使用的卡密已成功清空。</p>";
        } else {
            echo "<i class=\"fa fa-times-circle text-5xl text-rose-500 mb-2 block\"></i><p class=\"text-lg font-medium text-slate-700\">清空失败。</p>";
        }
        ?>
            <a href="./kmlist.php" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-50 text-brand-600 font-medium hover:bg-brand-100 transition-colors">返回卡密列表</a>
        </div>
    </div>
    <?php 
} else {
	if (isset($_GET["kw"])) {
		$sql = " type=0 AND `km`='" . $_GET["kw"] . "'";
		$numrows = $DB->getColumn("SELECT count(*) from pre_kms WHERE" . $sql);
		$con = "包含 <span class=\"font-bold text-brand-600 mx-1\">" . htmlspecialchars($_GET["kw"]) . "</span> 的共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个卡密";
	} else {
		$sql = " type=0";
		$numrows = $DB->getColumn("SELECT count(*) from pre_kms WHERE" . $sql);
		$con = "共有 <b class=\"text-lg text-slate-800 mx-1\">" . $numrows . "</b> 个充值卡密";
	}
	?>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-medium text-slate-700 m-0 flex items-center">
                <i class="fa fa-credit-card text-brand-500 mr-2 text-lg"></i>
                <?php echo $con;?>
            </h3>
            
            <div class="flex items-center gap-2">
                <a href="#" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-sky-50 text-sky-600 font-medium text-sm hover:bg-sky-500 hover:text-white shadow-sm transition-colors cursor-pointer open-modal" data-target="#searchModal">
                    <i class="fa fa-search mr-1.5"></i>搜索卡密
                </a>
            </div>
        </div>

        <div class="p-6 border-b border-slate-50">
            <form action="kmlist.php?my=add" method="POST" class="flex flex-col md:flex-row gap-3 items-end md:items-center w-full">
                <div class="flex items-center gap-2 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg"><i class="fa fa-plus"></i></div>
                    <span class="font-bold text-slate-700 mr-1">生成卡密:</span>
                </div>
                
                <div class="flex-1 w-full md:w-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                    <input type="text" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors" name="money" placeholder="设置充值面额 (元)" required>
                </div>
                
                <div class="flex-1 w-full md:w-auto relative">
                    <input type="text" class="w-full h-11 pl-4 pr-10 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors" name="num" placeholder="生成的个数" required>
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 text-sm pointer-events-none">个</span>
                </div>
                
                <div class="flex w-full md:w-auto gap-2">
                    <button type="submit" class="flex-1 md:flex-none inline-flex items-center justify-center px-6 h-11 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200">
                        确定生成
                    </button>
                    <a href="kmlist.php?my=qkuse" class="inline-flex items-center justify-center px-4 h-11 rounded-xl bg-amber-500 text-white font-medium text-sm hover:bg-amber-600 shadow-sm transition-all duration-200" title="清空已使用的卡密">
                        清空已用
                    </a>
                    <a href="kmlist.php?my=qk" class="inline-flex items-center justify-center px-4 h-11 rounded-xl bg-rose-500 text-white font-medium text-sm hover:bg-rose-600 shadow-sm transition-all duration-200" title="清空所有卡密">
                        全部清空
                    </a>
                </div>
            </form>
        </div>

        <!-- 响应式表格 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-left min-w-[250px]">卡密内容</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">面额</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-32">状态</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">添加时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">使用时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_kms WHERE" . $sql . " order by kid desc limit " . $offset . "," . $pagesize);
    
    $isEmpty = true;
	while ($res = $rs->fetch()) {
        $isEmpty = false;
		if ($res["status"] == 0) {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">未使用</span>";
		} else {
			$isuse = "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-50 text-rose-500 border border-rose-100\">已使用</span> <span class=\"text-xs text-slate-400 ml-1\">(ZID:" . $res["zid"] . ")</span>";
		}
		echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
                <td class=\"px-6 py-3 font-mono font-bold text-slate-700 tracking-tight select-all\">" . $res["km"] . "</td>
                <td class=\"px-6 py-3 text-center font-bold text-brand-600\"><span class=\"text-xs\">¥</span>" . $res["money"] . "</td>
                <td class=\"px-6 py-3 text-center\">" . $isuse . "</td>
                <td class=\"px-6 py-3 text-center text-xs text-slate-500\">" . $res["addtime"] . "</td>
                <td class=\"px-6 py-3 text-center text-xs text-slate-400\">" . ($res["usetime"] ?: "-") . "</td>
                <td class=\"px-6 py-3 text-center\">
                    <a href=\"./kmlist.php?my=del&id=" . $res["kid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('你确实要删除此卡密吗？');\">删除</a>
                </td>
              </tr>";
	}
    
    if($isEmpty) {
        echo '<tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
                <i class="fa fa-ticket text-5xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">当前没有符合条件的加款卡密记录</p>
              </td></tr>';
    }
	?>          </tbody>
        </table>
      </div>
      
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
                echo "<li><a href=\"kmlist.php?page=" . $first . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\">首页</a></li>";
                echo "<li><a href=\"kmlist.php?page=" . $prev . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
            }
            $start = $page - 10 > 1 ? $page - 10 : 1;
            $end = $page + 10 < $pages ? $page + 10 : $pages;
            for ($i = $start; $i < $page; $i++) {
                echo "<li><a href=\"kmlist.php?page=" . $i . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
            for ($i = $page + 1; $i <= $end; $i++) {
                echo "<li><a href=\"kmlist.php?page=" . $i . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            if ($page < $pages) {
                echo "<li><a href=\"kmlist.php?page=" . $next . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
                echo "<li><a href=\"kmlist.php?page=" . $last . (isset($link)?$link:'') . "\" class=\"".$btn_normal."\">尾页</a></li>";
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

<script>
// 控制原生模态框的显示与隐藏逻辑
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
    
    // 点击背景关闭
    document.querySelectorAll('[role="dialog"]').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('.close-modal').click();
            }
        });
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
