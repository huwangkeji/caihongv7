<?php
include "../includes/common.php";
$title = '上架日志管理与编辑';
include './head.php';

if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

adminpermission('site', 1);
$my = isset($_GET['my']) ? $_GET['my'] : null;

echo '<div class="w-full max-w-[800px] mx-auto space-y-6 pb-10 mt-4">';

if ($my == 'add') {
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">新增系统上架日志</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./toollogsedit.php?my=add_submit" method="POST" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">日志详细内容 <span class="text-rose-500">*</span></label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors min-h-[240px] leading-relaxed" name="content" placeholder="请输入系统功能升级、商品上架等具体说明内容..." required></textarea>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">标记上架日期</label>
                    <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="留空则默认为当天时间"/>
                </div>
                
                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认创建并发布日志
                    </button>
                    <a href="./toollogs.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php 
} elseif ($my == 'edit') {
	$id = $_GET['id'];
	$row = $DB->getRow("select * from pre_toollogs where id='" . $id . "' limit 1");
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改历史日志内容</h3>
            <span class="ml-3 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">日志 ID: #<?php echo htmlspecialchars($id); ?></span>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./toollogsedit.php?my=edit_submit&id=<?php echo htmlspecialchars($id); ?>" method="POST" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">日志详细内容 <span class="text-rose-500">*</span></label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors min-h-[240px] leading-relaxed" name="content" placeholder="请输入具体的更新或上架说明..." required><?php echo htmlspecialchars($row['content']); ?></textarea>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">标记上架日期</label>
                    <input type="date" name="date" value="<?php echo htmlspecialchars($row['date']); ?>" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="留空自动获取当天时间"/>
                </div>
                
                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>确认保存修改内容
                    </button>
                    <a href="./toollogs.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php 
} elseif ($my == 'add_submit') {
	$date = trim($_POST['date']);
	$content = trim($_POST['content']);
	$rows = $DB->getRow("select * from pre_toollogs where date='" . $date . "' limit 1");
	if ($rows) {
		showmsg($date . ' 对应日期的系统日志已存在，请勿重复添加！', 3);
	} else {
        $sql = "insert into `pre_toollogs` (`content`,`date`,`addtime`,`active`) values (:content, :date2,:date, 1)";
        $data = array(':content' => $content, ':date2' => $date, ':date' => $date);
        if ($DB->exec($sql, $data) !== false) {
            showmsg('添加系统日志成功！<br/><br/><a href="./toollogs.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-brand-50 text-brand-600 rounded-lg hover:bg-brand-100 transition-colors">>> 立即返回日志列表</a>', 1);
        } else {
            showmsg('添加系统日志失败！' . $DB->error(), 4);
        }
    }
} elseif ($my == 'edit_submit') {
	$id = trim($_GET['id']);
	$date = !empty($_POST['date']) ? $_POST['date'] : date('Y-m-d');
	$content = trim($_POST['content']);
	$rows = $DB->getRow("select * from pre_toollogs where id='" . $id . "' limit 1");
	if (!$rows) {
		showmsg('安全阻断：系统中不存在此历史记录！', 3);
	} else {
        if ($DB->exec("update pre_toollogs set content='" . $content . "',date='" . $date . "' where id='" . $id . "'") !== false) {
            showmsg('修改日志信息成功！<br/><br/><a href="./toollogs.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-100 transition-colors">>> 立即返回日志列表</a>', 1);
        } else {
            showmsg('修改日志信息失败异常！' . $DB->error(), 4);
        }
    }
} elseif ($my == 'delete') {
	$id = $_GET['id'];
	$sql = "DELETE FROM pre_toollogs WHERE id='" . $id . "'";
	if ($DB->exec($sql) !== false) {
		showmsg('该条记录彻底删除成功！<br/><br/><a href="./toollogs.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-brand-50 text-brand-600 rounded-lg hover:bg-brand-100 transition-colors">>> 立即返回日志列表</a>', 1);
	} else {
		showmsg('系统删除失败！' . $DB->error(), 4);
	}
}

echo '</div>'; // End global wrapper
?>
</body>
</html>
