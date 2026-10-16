<?php

include "../includes/common.php";
$title = "站内通知";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6">
<?php 
adminpermission("message", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;

if ($my == "add") {
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-bullhorn text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">发布新通知</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./message.php?my=add_submit" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">通知标题</label>
                    <input type="text" name="title" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请输入简明扼要的通知标题" required/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">接收用户类别</label>
                    <select class="w-full sm:w-1/2 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="type">
                        <option value="0">全部用户</option>
                        <option value="1">普通用户</option>
                        <option value="2">所有分站站长</option>
                        <option value="3">普及版站长</option>
                        <option value="4">专业版站长</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">通知内容</label>
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <textarea id="editor_id" name="content" class="w-full" style="height:400px;"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-paper-plane mr-1.5"></i>确认发布
                    </button>
                    <a href="./message.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        返回列表
                    </a>
                </div>
            </form>
        </div>
    </div>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
                    width: '100%',
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>

<?php 
} elseif ($my == "edit") {
	$id = $_GET["id"];
	$row = $DB->getRow("select * from pre_message where id='" . $id . "' limit 1");
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改通知信息</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./message.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">通知标题</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($row["title"]);?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" required/>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">接收用户类别</label>
                    <select class="w-full sm:w-1/2 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="type" default="<?php echo $row["type"];?>">
                        <option value="0">全部用户</option>
                        <option value="1">普通用户</option>
                        <option value="2">所有分站站长</option>
                        <option value="3">普及版站长</option>
                        <option value="4">专业版站长</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">通知内容</label>
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <textarea id="editor_id" name="content" class="w-full" style="height:400px;"><?php echo htmlspecialchars($row["content"]);?></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" name="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>保存修改
                    </button>
                    <a href="./message.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('#editor_id', {
					resizeType : 1,
					allowUpload : false,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
                    width: '100%',
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
				});
        });
</script>

<?php 
} elseif ($my == "add_submit") {
	$title = daddslashes($_POST["title"]);
	$type = intval($_POST["type"]);
	$content = daddslashes($_POST["content"]);
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from pre_message where type='" . $type . "' and title='" . $title . "' limit 1");
		if ($rows) {
			showmsg("通知标题已存在！", 3);
		}
		$sql = "insert into `pre_message` (`type`,`title`,`content`,`addtime`,`active`) values ('" . $type . "','" . $title . "','" . $content . "','" . $date . "','1')";
		if ($DB->exec($sql) !== false) {
			showmsg("发布站内通知成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
		} else {
			showmsg("发布站内通知失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$id = $_GET["id"];
	$rows = $DB->getRow("select * from pre_message where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$title = daddslashes($_POST["title"]);
	$type = intval($_POST["type"]);
	$content = daddslashes($_POST["content"]);
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		if ($DB->exec("UPDATE `pre_message` SET `type`='" . $type . "',`title`='" . $title . "',`content`='" . $content . "' WHERE `id`='" . $id . "'") !== false) {
			showmsg("修改站内通知成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
		} else {
			showmsg("修改站内通知失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	$id = $_GET["id"];
	$sql = "DELETE FROM pre_message WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./message.php\">>>返回通知列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	$numrows = $DB->getColumn("SELECT count(*) from pre_message");
	?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-bell-o text-brand-500 mr-2 text-lg"></i>
                站内通知管理
                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows;?> 条</span>
            </h3>
            
            <a href="./message.php?my=add" class="inline-flex items-center justify-center px-5 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm shrink-0">
                <i class="fa fa-plus mr-1.5"></i>发布新通知
            </a>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-16">ID</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[200px]">通知标题</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">发布时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28 text-sky-600">已查阅人数</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">状态</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-44">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
	$pagesize = 30;
	$pages = ceil($numrows / $pagesize);
	$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
	$offset = $pagesize * ($page - 1);
	$rs = $DB->query("SELECT * FROM pre_message WHERE 1 order by id desc limit " . $offset . "," . $pagesize);
    
    $isEmpty = true;
	while ($res = $rs->fetch()) {
        $isEmpty = false;
        
        $statusBadge = $res["active"] == 1 
            ? "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["id"] . ",0)\">显示</span>" 
            : "<span class=\"inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100 transition-colors cursor-pointer\" onclick=\"setActive(" . $res["id"] . ",1)\">隐藏</span>";

		echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
                <td class=\"px-6 py-4 text-center font-bold text-slate-700\">" . $res["id"] . "</td>
                <td class=\"px-6 py-4 font-bold text-slate-800 max-w-[300px] truncate\" title=\"" . htmlspecialchars($res["title"]) . "\">" . $res["title"] . "</td>
                <td class=\"px-6 py-4 text-center text-xs text-slate-500 font-mono\">" . $res["addtime"] . "</td>
                <td class=\"px-6 py-4 text-center font-bold text-sky-500 text-base\">" . $res["count"] . "</td>
                <td class=\"px-6 py-4 text-center\">" . $statusBadge . "</td>
                <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                    <div class=\"inline-flex items-center gap-1.5\">
                        <span class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer\" onclick=\"show(" . $res["id"] . ")\">查看</span>
                        <a href=\"./message.php?my=edit&id=" . $res["id"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
                        <a href=\"./message.php?my=delete&id=" . $res["id"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\" onclick=\"return confirm('确定要删除此站内通知吗？该操作不可恢复！');\">删除</a>
                    </div>
                </td>
              </tr>";
	}
    
    if($isEmpty) {
        echo '<tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
                <i class="fa fa-envelope-o text-5xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">当前没有发布任何站内通知</p>
              </td></tr>';
    }
	?>          </tbody>
        </table>
      </div>
      
      <!-- 分页组件 -->
      <?php if(!$isEmpty): ?>
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center">
        <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 m-0 list-none p-0">
        <?php 
        $link = isset($link) ? $link : '';
        $first = 1;
        $prev = $page - 1;
        $next = $page + 1;
        $last = $pages;

        $btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
        $btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
        $btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

        if ($page > 1) {
            echo "<li><a href=\"message.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
            echo "<li><a href=\"message.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
        } else {
            echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
            echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
        }
        $start = $page - 10 > 1 ? $page - 10 : 1;
        $end = $page + 10 < $pages ? $page + 10 : $pages;
        for ($i = $start; $i < $page; $i++) {
            echo "<li><a href=\"message.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
        }
        echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
        for ($i = $page + 1; $i <= $end; $i++) {
            echo "<li><a href=\"message.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
        }
        if ($page < $pages) {
            echo "<li><a href=\"message.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
            echo "<li><a href=\"message.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
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
<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/message.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>
