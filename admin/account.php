<?php
include "../includes/common.php";
$title = "员工管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="w-full max-w-[1400px] mx-auto">
<?php 
adminpermission("account", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;

if ($my == "add") {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-user-plus text-brand-500 mr-2"></i>添加一个用户
            </h3>
        </div>
        <div class="p-6 sm:p-8">
          <form action="./account.php?my=add_submit" method="post" role="form" class="space-y-6">
            <div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">用户名</label>
          	  <input type="text" name="username" value="" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="请输入员工登录用户名" required/>
          	</div>
          	<div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">密码</label>
          	  <input type="text" name="password" value="" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" placeholder="请输入登录密码" required/>
          	</div>
          	<div class="form-group space-y-2">
          	  <label class="text-sm font-semibold text-slate-700 block mb-2">选择可用的功能模块</label>
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="order" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 订单管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="refund" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 订单退款</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="shop" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 商品管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="price" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 加价模板</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="faka" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 发卡管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="site" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 分站/用户</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="tixian" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 余额提现</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="workorder" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 工单管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="message" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 站内通知</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="article" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 文章管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="shequ" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 对接管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="set" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 系统设置</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="account" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 员工管理</label>
              </div>
          	</div>
          	<div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">是否激活</label>
          	  <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="active">
                  <option value="1">是</option>
                  <option value="0">否</option>
              </select>
          	</div>
          	<div class="pt-2">
          	  <input type="submit" name="submit" value="确认添加" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"/>
          	</div>
          </form>
          <div class="mt-4 pt-4 border-t border-slate-100 text-center">
              <a href="./account.php" class="text-sm font-medium text-brand-600 hover:underline">&gt;&gt;返回员工列表</a>
          </div>
        </div>
    </div>
<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_account where id='" . $id . "' limit 1");
	$permission = explode(",", $row["permission"]);
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-pencil-square text-brand-500 mr-2"></i>修改员工信息
            </h3>
        </div>
        <div class="p-6 sm:p-8">
          <form action="./account.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-6">
            <div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">用户名</label>
          	  <input type="text" name="username" value="<?php echo $row["username"];?>" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" required/>
          	</div>
          	<div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">密码</label>
          	  <input type="text" name="password" value="<?php echo $row["password"];?>" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" required/>
          	</div>
          	<div class="form-group space-y-2">
          	  <label class="text-sm font-semibold text-slate-700 block mb-2">选择可用的功能模块</label>
              <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="order" <?php echo in_array("order", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 订单管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="refund" <?php echo in_array("refund", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 订单退款</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="shop" <?php echo in_array("shop", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 商品管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="price" <?php echo in_array("price", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 加价模板</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="faka" <?php echo in_array("faka", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 发卡管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="site" <?php echo in_array("site", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 分站/用户</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="tixian" <?php echo in_array("tixian", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 余额提现</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="workorder" <?php echo in_array("workorder", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 工单管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="message" <?php echo in_array("message", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 站内通知</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="article" <?php echo in_array("article", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 文章管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="shequ" <?php echo in_array("shequ", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 对接管理</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="set" <?php echo in_array("set", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 系统设置</label>
                <label class="inline-flex items-center text-sm text-slate-600 cursor-pointer"><input type="checkbox" name="permission[]" value="account" <?php echo in_array("account", $permission) ? "checked" : "";?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 员工管理</label>
              </div>
          	</div>
          	<div class="form-group space-y-1.5">
          	  <label class="text-sm font-semibold text-slate-700">是否激活</label>
          	  <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="active" default="<?php echo $row["active"];?>">
                  <option value="1">是</option>
                  <option value="0">否</option>
              </select>
          	</div>
          	<div class="pt-2">
          	  <input type="submit" name="submit" value="确认修改" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer"/>
          	</div>
          </form>
          <div class="mt-4 pt-4 border-t border-slate-100 text-center">
              <a href="./account.php" class="text-sm font-medium text-brand-600 hover:underline">&gt;&gt;返回员工列表</a>
          </div>
        </div>
    </div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<?php 
} elseif ($my == "add_submit") {
	$username = $_POST["username"];
	$password = $_POST["password"];
	$permission = isset($_POST["permission"]) ? implode(",", $_POST["permission"]) : "";
	$active = intval($_POST["active"]);
	if ($username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from pre_account where username='" . $username . "' limit 1");
		if ($rows) {
			showmsg("用户名已存在！", 3);
		}
		$sql = "insert into `pre_account` (`username`,`password`,`permission`,`addtime`,`active`) values ('" . $username . "','" . $password . "','" . $permission . "','" . $date . "','" . $active . "')";
		if ($DB->exec($sql) !== false) {
			showmsg("添加用户成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
		} else {
			showmsg("添加用户失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_account where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$username = $_POST["username"];
	$password = $_POST["password"];
	$permission = isset($_POST["permission"]) ? implode(",", $_POST["permission"]) : "";
	$active = intval($_POST["active"]);
	if ($username == NULL || $password == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_account` SET `username`='" . $username . "',`password`='" . $password . "',`permission`='" . $permission . "',`active`='" . $active . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改用户成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
		} else {
			showmsg("修改用户失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = $_GET["id"];
	$sql = "DELETE FROM pre_account WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./account.php\">>>返回用户列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_account");
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/50 gap-4">
            <h2 class="text-base sm:text-lg font-bold text-slate-800 m-0">
                系统共有 <b class="text-brand-600 mx-1"><?php echo $numrows;?></b> 个员工帐号
            </h2>
            <a href="./account.php?my=add" class="inline-flex items-center justify-center h-10 px-5 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200">
                <i class="fa fa-plus mr-2"></i>添加一个用户
            </a>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
              <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5 font-semibold text-center">ID</th>
                    <th class="px-6 py-3.5 font-semibold text-left">用户名</th>
                    <th class="px-6 py-3.5 font-semibold text-left">权限</th>
                    <th class="px-6 py-3.5 font-semibold text-center">添加时间 / 上次登录</th>
                    <th class="px-6 py-3.5 font-semibold text-center">状态</th>
                    <th class="px-6 py-3.5 font-semibold text-center">操作</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_account WHERE 1 order by id desc limit " . $offset . "," . $pagesize);
	while ($res = $rs->fetch()) {
		echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center font-bold text-slate-700\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 font-medium text-slate-800\">" . $res["username"] . "</td>
            <td class=\"px-6 py-4 max-w-[300px] truncate text-slate-500\" title=\"" . $res["permission"] . "\">" . $res["permission"] . "</td>
            <td class=\"px-6 py-4 text-center text-xs text-slate-500\">" . $res["addtime"] . "<br/><span class=\"text-slate-400\">" . ($res["lasttime"] ?: '从未登录') . "</span></td>
            <td class=\"px-6 py-4 text-center\">" . ($res["active"] == 1 ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">正常</span>" : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100\">封禁</span>") . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center gap-1.5\">
                    <a href=\"./account.php?my=edit&id=" . $res["id"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
                    <a href=\"./account.php?my=delete&id=" . $res["id"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('你确实要删除此记录吗？');\">删除</a>
                </div>
            </td>
        </tr>";
	}
	?>          </tbody>
            </table>
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
		echo "<li><a href=\"account.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
		echo "<li><a href=\"account.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
	}
	$start = $page - 10 > 1 ? $page - 10 : 1;
	$end = $page + 10 < $pages ? $page + 10 : $pages;
	for ($i = $start; $i < $page; $i++) {
		echo "<li><a href=\"account.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
	for ($i = $page + 1; $i <= $end; $i++) {
		echo "<li><a href=\"account.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
	}
	if ($page < $pages) {
		echo "<li><a href=\"account.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
		echo "<li><a href=\"account.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
	} else {
		echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
		echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
	}
	?></ul>
<?php 
}
?>    </div>
</div>
