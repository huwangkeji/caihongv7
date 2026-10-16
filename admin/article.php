<?php
// 文章管理页面
// 虎王云自助下单系统 V4
// 开发：Leiong
// 功能：文章的添加、编辑、删除和列表展示
// 安全措施：使用PDO预处理语句防止SQL注入，使用daddslashes函数过滤用户输入

include "../includes/common.php";
$title = "文章管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>



<style type="text/tailwindcss">
    @layer utilities {
        /* 表单控件样式 */
        .tw-input { @apply w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all duration-200 text-slate-700 text-sm; }
        .tw-label { @apply block text-sm font-semibold text-slate-700 mb-2; }
        
        /* 按钮样式 */
        .tw-btn { @apply px-5 py-2.5 rounded-xl font-medium transition-all duration-200 inline-flex items-center justify-center shadow-sm text-sm; }
        .tw-btn-primary { @apply bg-brand-500 text-white hover:bg-brand-600 hover:-translate-y-0.5 shadow-md; }
        .tw-btn-secondary { @apply bg-white border border-slate-200 text-slate-600 hover:bg-slate-50; }
        .tw-btn-danger { @apply bg-rose-500 text-white hover:bg-rose-600 shadow-md; }
        .tw-btn-info { @apply bg-sky-500 text-white hover:bg-sky-600 shadow-md; }
        .tw-btn-warning { @apply bg-amber-500 text-white hover:bg-amber-600 shadow-md; }
        .tw-btn-xs { @apply px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 shadow-sm hover:-translate-y-0.5; }
        
        /* 容器样式 */
        .tw-card { @apply bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6 w-full; }
        .tw-card-header { @apply px-6 py-4 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between; }
        .tw-card-title { @apply text-base font-bold text-slate-800 m-0 flex items-center; }
        .tw-card-body { @apply p-6; }
        
        /* 表格样式 */
        .tw-table { @apply w-full text-left text-sm text-slate-600; }
        .tw-th { @apply px-4 py-3 font-semibold text-slate-700 bg-slate-50/80 border-b border-slate-100 whitespace-nowrap; }
        .tw-td { @apply px-4 py-3 border-b border-slate-50 align-middle; }
        .tw-tr { @apply hover:bg-slate-50/50 transition-colors duration-150; }
        
        /* 分页样式 */
        .tw-pagination { @apply flex flex-wrap items-center justify-center gap-1.5 mt-6; }
        .tw-page-link { @apply px-3.5 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium; }
        .tw-page-active { @apply border-brand-500 bg-brand-50 text-brand-600 font-bold; }
        .tw-page-disabled { @apply text-slate-400 bg-slate-50 border-slate-100 cursor-not-allowed hover:bg-slate-50; }
    }
</style>

<div class="w-full px-4 sm:px-6 lg:px-8 py-6 font-sans text-slate-700 max-w-7xl mx-auto">
<?php 
adminpermission("article", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
if ($my == "add") {
?>
    <div class="tw-card">
        <div class="tw-card-header">
            <h3 class="tw-card-title"><i class="fa fa-pencil-square-o text-brand-500 mr-2 text-lg"></i>添加文章</h3>
            <a href="./article.php" class="tw-btn tw-btn-secondary tw-btn-xs"><i class="fa fa-reply mr-1"></i>返回列表</a>
        </div>
        <div class="tw-card-body">
            <form action="./article.php?my=add_submit" method="post" role="form" class="space-y-5 max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">文章标题</label>
                    <div class="md:col-span-3">
                        <input type="text" name="title" value="" class="tw-input" required/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">SEO关键词</label>
                    <div class="md:col-span-3">
                        <input type="text" name="keywords" value="" class="tw-input" placeholder="多个关键词用英文逗号分隔，可留空"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <label class="tw-label md:text-right mt-3 md:mb-0">SEO描述</label>
                    <div class="md:col-span-3">
                        <textarea id="description" class="tw-input min-h-[80px]" name="description" rows="2" placeholder="文章内容的简短描述，可留空"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <label class="tw-label md:text-right mt-3 md:mb-0">文章内容</label>
                    <div class="md:col-span-3">
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <textarea id="editor_id" name="content" rows="15" class="w-full border-0 focus:ring-0"></textarea>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">是否置顶</label>
                    <div class="md:col-span-3">
                        <select class="tw-input" name="top">
                            <option value="0">否 (正常显示)</option>
                            <option value="1">是 (显示在最前面)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">发布时间</label>
                    <div class="md:col-span-3">
                        <input type="date" name="addtime" value="<?php echo date("Y-m-d", strtotime("+1 years"));?>" class="tw-input"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center pt-4 border-t border-slate-100 mt-6">
                    <div class="md:col-start-2 md:col-span-3">
                        <button type="submit" name="submit" class="tw-btn tw-btn-primary w-full sm:w-auto px-8"><i class="fa fa-paper-plane-o mr-2"></i>发布文章</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
    KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id', {
                width: '100%',
                resizeType : 1,
                allowUpload : false,
                allowPreviewEmoticons : false,
                allowImageUpload : false,
                items : [
                    'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                    'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
            });
    });
</script>
<?php 
} elseif ($my == "edit") {
	$id = intval($_GET["id"]);
	$row = $DB->getRow("select * from " . DBQZ . "article where id=:id limit 1", array(':id' => $id));
?>
    <div class="tw-card">
        <div class="tw-card-header">
            <h3 class="tw-card-title"><i class="fa fa-edit text-sky-500 mr-2 text-lg"></i>修改文章</h3>
            <a href="./article.php" class="tw-btn tw-btn-secondary tw-btn-xs"><i class="fa fa-reply mr-1"></i>返回列表</a>
        </div>
        <div class="tw-card-body">
            <form action="./article.php?my=edit_submit&id=<?php echo $id;?>" method="post" role="form" class="space-y-5 max-w-4xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">文章标题</label>
                    <div class="md:col-span-3">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($row["title"]);?>" class="tw-input" required/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">SEO关键词</label>
                    <div class="md:col-span-3">
                        <input type="text" name="keywords" value="<?php echo htmlspecialchars($row["keywords"]);?>" class="tw-input" placeholder="多个关键词用英文逗号分隔，可留空"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <label class="tw-label md:text-right mt-3 md:mb-0">SEO描述</label>
                    <div class="md:col-span-3">
                        <textarea id="description" class="tw-input min-h-[80px]" name="description" rows="2" placeholder="文章内容的简短描述，可留空"><?php echo htmlspecialchars($row["description"]);?></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                    <label class="tw-label md:text-right mt-3 md:mb-0">文章内容</label>
                    <div class="md:col-span-3">
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <textarea id="editor_id" name="content" rows="15" class="w-full border-0 focus:ring-0"><?php echo htmlspecialchars($row["content"]);?></textarea>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">是否置顶</label>
                    <div class="md:col-span-3">
                        <select class="tw-input" name="top" default="<?php echo $row["top"];?>">
                            <option value="0">否 (正常显示)</option>
                            <option value="1">是 (显示在最前面)</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <label class="tw-label md:text-right md:mb-0">发布时间</label>
                    <div class="md:col-span-3">
                        <input type="date" name="addtime" value="<?php echo $row["addtime"];?>" class="tw-input"/>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center pt-4 border-t border-slate-100 mt-6">
                    <div class="md:col-start-2 md:col-span-3">
                        <button type="submit" name="submit" class="tw-btn tw-btn-primary w-full sm:w-auto px-8"><i class="fa fa-save mr-2"></i>保存修改</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
<script>
// 设置select的默认值
var items = document.querySelectorAll("select[default]");
for (var i = 0; i < items.length; i++) {
	items[i].value = items[i].getAttribute("default") || 0;
}
</script>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<script>
    KindEditor.ready(function(K) {
            window.editor = K.create('#editor_id', {
                width: '100%',
                resizeType : 1,
                allowUpload : false,
                allowPreviewEmoticons : false,
                allowImageUpload : false,
                items : [
                    'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
                    'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                    'insertunorderedlist', '|', 'emoticons', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
            });
    });
</script>
<?php 
} elseif ($my == "add_submit") {
	// 直接创建表，不使用异常捕获，确保表一定存在
	$createTableSql = "CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
		id INT AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(255) NOT NULL,
		keywords VARCHAR(255) DEFAULT '',
		description VARCHAR(255) DEFAULT '',
		content TEXT NOT NULL,
		top TINYINT DEFAULT 0,
		addtime DATE NOT NULL,
		count INT DEFAULT 0,
		active TINYINT DEFAULT 1,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)";
	$DB->exec($createTableSql);
	
	$title = daddslashes($_POST["title"]);
	$keywords = daddslashes($_POST["keywords"]);
	$description = daddslashes($_POST["description"]);
	$content = daddslashes($_POST["content"]);
	$top = intval($_POST["top"]);
	$addtime = $_POST["addtime"];
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$rows = $DB->getRow("select * from " . DBQZ . "article where title=:title limit 1", array(':title' => $title));
		if ($rows) {
			showmsg("文章标题已存在！", 3);
		}
		$sql = "insert into `" . DBQZ . "article` (`title`,`keywords`,`description`,`content`,`top`,`addtime`,`active`) values (:title, :keywords, :description, :content, :top, :addtime, '1')";
		$data = array(':title' => $title, ':keywords' => $keywords, ':description' => $description, ':content' => $content, ':top' => $top, ':addtime' => $addtime);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("添加文章成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
		} else {
			showmsg("添加文章失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	// 直接创建表，不使用异常捕获，确保表一定存在
	$createTableSql = "CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
		id INT AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(255) NOT NULL,
		keywords VARCHAR(255) DEFAULT '',
		description VARCHAR(255) DEFAULT '',
		content TEXT NOT NULL,
		top TINYINT DEFAULT 0,
		addtime DATE NOT NULL,
		count INT DEFAULT 0,
		active TINYINT DEFAULT 1,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)";
	$DB->exec($createTableSql);
	
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("select * from " . DBQZ . "article where id=:id limit 1", array(':id' => $id));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$title = daddslashes($_POST["title"]);
	$keywords = daddslashes($_POST["keywords"]);
	$description = daddslashes($_POST["description"]);
	$content = daddslashes($_POST["content"]);
	$top = intval($_POST["top"]);
	$addtime = $_POST["addtime"];
	if ($title == NULL || $content == NULL) {
		showmsg("保存错误,请确保每项都不为空!", 3);
	} else {
		$sql = "UPDATE `" . DBQZ . "article` SET `title`=:title,`keywords`=:keywords,`description`=:description,`content`=:content,`top`=:top,`addtime`=:addtime WHERE `id`=:id";
		$data = array(':title' => $title, ':keywords' => $keywords, ':description' => $description, ':content' => $content, ':top' => $top, ':addtime' => $addtime, ':id' => $id);
		if ($DB->exec($sql, $data) !== false) {
			showmsg("修改文章成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
		} else {
			showmsg("修改文章失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "delete") {
	// 直接创建表，不使用异常捕获，确保表一定存在
	$createTableSql = "CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
		id INT AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(255) NOT NULL,
		keywords VARCHAR(255) DEFAULT '',
		description VARCHAR(255) DEFAULT '',
		content TEXT NOT NULL,
		top TINYINT DEFAULT 0,
		addtime DATE NOT NULL,
		count INT DEFAULT 0,
		active TINYINT DEFAULT 1,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)";
	$DB->exec($createTableSql);
	
	$id = intval($_GET["id"]);
	$sql = "DELETE FROM " . DBQZ . "article WHERE id=:id";
	if ($DB->exec($sql, array(':id' => $id)) !== false) {
		showmsg("删除成功！<br/><br/><a href=\"./article.php\">>>返回文章列表</a>", 1);
	} else {
		showmsg("删除失败！" . $DB->error(), 4);
	}
} else {
	// 直接创建表，不使用异常捕获，确保表一定存在
	$createTableSql = "CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
		id INT AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(255) NOT NULL,
		keywords VARCHAR(255) DEFAULT '',
		description VARCHAR(255) DEFAULT '',
		content TEXT NOT NULL,
		top TINYINT DEFAULT 0,
		addtime DATE NOT NULL,
		count INT DEFAULT 0,
		active TINYINT DEFAULT 1,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)";
	$DB->exec($createTableSql);
	
	if (isset($_GET["kw"])) {
		$kw = trim(daddslashes($_GET["kw"]));
		$numrows = $DB->getColumn("SELECT count(*) from " . DBQZ . "article where title LIKE :title", array(':title' => '%' . $kw . '%'));
		$con = "包含 <b>" . htmlspecialchars($kw) . "</b> 的共有 <b>" . $numrows . "</b> 个文章";
		$link = "&kw=" . urlencode($kw);
	} else {
		$kw = '';
		$numrows = $DB->getColumn("SELECT count(*) from " . DBQZ . "article");
		$con = "系统共有 <b>" . $numrows . "</b> 个文章";
	}
	
	// 调试信息
    echo "<!-- 调试: 数据库前缀=" . DBQZ . ", 文章总数=" . $numrows . " -->";
?>
    <div class="tw-card">
        <div class="tw-card-header flex-col sm:flex-row gap-4 sm:gap-0 items-start sm:items-center">
            <h3 class="tw-card-title">
                <i class="fa fa-file-text-o text-brand-500 mr-2 text-lg"></i>
                <span><?php echo $con;?></span>
            </h3>
            
            <form action="article.php" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-48">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="tw-input pl-9 py-2 rounded-lg text-sm" name="kw" placeholder="搜索文章标题..." value="<?php echo htmlspecialchars($kw); ?>">
                </div>
                <button type="submit" class="tw-btn tw-btn-info py-2 px-4 rounded-lg"><i class="fa fa-search mr-1 hidden sm:inline-block"></i>搜索</button>
            </form>
        </div>
        
        <div class="tw-card-body p-4 sm:p-6">
            <div class="mb-5 flex flex-wrap gap-2">
                <a href="./article.php?my=add" class="tw-btn tw-btn-primary py-2"><i class="fa fa-plus mr-2"></i>添加文章</a>
                <a href="./set.php?mod=rewrite" class="tw-btn tw-btn-secondary py-2"><i class="fa fa-cogs mr-2 text-slate-400"></i>伪静态配置</a>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="tw-table">
                    <thead>
                        <tr>
                            <th class="tw-th w-16 text-center">ID</th>
                            <th class="tw-th min-w-[200px]">文章标题</th>
                            <th class="tw-th w-32">发布时间</th>
                            <th class="tw-th w-24 text-center">浏览量</th>
                            <th class="tw-th w-24 text-center">状态</th>
                            <th class="tw-th w-44 text-right">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
<?php 
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);

// 实现自动表创建功能和数据查询
try {
    // 首先检查表是否存在
    $DB->getColumn("SELECT count(*) FROM " . DBQZ . "article");
    
    // 表存在，查询数据
    $has_data = false;
    try {
        $pagesize = 30;
        $page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
        $offset = $pagesize * ($page - 1);
        
        // 使用直接的SQL查询，避免复杂的参数绑定
        if($kw){
            $kw_escaped = addslashes($kw);
            $sql = "SELECT * FROM " . DBQZ . "article WHERE title LIKE '%{$kw_escaped}%' ORDER BY id DESC LIMIT {$offset}, {$pagesize}";
        } else {
            $sql = "SELECT * FROM " . DBQZ . "article ORDER BY id DESC LIMIT {$offset}, {$pagesize}";
        }
        
        // 执行查询
        $rows = $DB->query($sql);
        
        // 检查是否有数据
        while ($row = $rows->fetch()) {
            $has_data = true;
            $id = $row['id'];
            $title = htmlspecialchars($row['title']);
            $addtime = $row['addtime'];
            $count = $row['count'] ?? 0;
            $active = $row['active'];
            $isTop = $row['top'] == 1 ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-600 ml-2">置顶</span>' : '';
            
            // 输出表格行
            echo "<tr class=\"tw-tr\">";
            echo "<td class=\"tw-td text-center font-medium text-slate-800\">{$id}</td>";
            echo "<td class=\"tw-td font-medium text-slate-800\">{$title}{$isTop}</td>";
            echo "<td class=\"tw-td text-slate-500 text-xs\">{$addtime}</td>";
            echo "<td class=\"tw-td text-center\"><span class=\"bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md text-xs\">{$count}</span></td>";
            
            if ($active == 1) {
                echo "<td class=\"tw-td text-center\"><button type=\"button\" class=\"tw-btn-xs bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100\" onclick=\"setActive({$id},0)\"><i class=\"fa fa-check-circle mr-1\"></i>显示</button></td>";
            } else {
                echo "<td class=\"tw-td text-center\"><button type=\"button\" class=\"tw-btn-xs bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100\" onclick=\"setActive({$id},1)\"><i class=\"fa fa-eye-slash mr-1\"></i>隐藏</button></td>";
            }
            
            echo "<td class=\"tw-td text-right space-x-1\">
                <a href=\"../?mod=article&id={$id}\" target=\"_blank\" class=\"tw-btn-xs bg-brand-50 text-brand-600 border border-brand-100 hover:bg-brand-100\" title=\"查看\"><i class=\"fa fa-external-link\"></i></a>
                <a href=\"./article.php?my=edit&id={$id}\" class=\"tw-btn-xs bg-sky-50 text-sky-600 border border-sky-100 hover:bg-sky-100\" title=\"编辑\"><i class=\"fa fa-edit\"></i></a>
                <a href=\"./article.php?my=delete&id={$id}\" class=\"tw-btn-xs bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-100\" onclick=\"return confirm('确定要永久删除这篇文章吗？');\" title=\"删除\"><i class=\"fa fa-trash-o\"></i></a>
            </td>";
            echo "</tr>";
        }
        
        // 如果没有数据，显示提示
        if (!$has_data) {
            echo "<tr><td colspan=6 class=\"tw-td text-center py-10\">
                <div class=\"flex flex-col items-center justify-center text-slate-400\">
                    <i class=\"fa fa-file-text-o text-4xl mb-3 opacity-50\"></i>
                    <p>暂无文章数据，请点击上方'添加文章'按钮创建</p>
                </div>
            </td></tr>";
        }
    } catch (Exception $e) {
        // 显示错误信息
        echo "<tr><td colspan=6 class=\"tw-td text-center text-rose-500 py-6\"><i class=\"fa fa-warning mr-2\"></i>查询错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
} catch (PDOException $e) {
    // 如果表不存在，创建表
    if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), "表 '" . DBQZ . "article' 不存在") !== false) {
        // 创建文章表
        $createTableSql = "CREATE TABLE IF NOT EXISTS " . DBQZ . "article (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            keywords VARCHAR(255) DEFAULT '',
            description VARCHAR(255) DEFAULT '',
            content TEXT NOT NULL,
            top TINYINT DEFAULT 0,
            addtime DATE NOT NULL,
            count INT DEFAULT 0,
            active TINYINT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $DB->exec($createTableSql);
        
        // 插入示例数据
        $sampleData = [
            [
                'title' => '欢迎使用文章管理系统',
                'keywords' => '文章管理,系统,欢迎',
                'description' => '这是系统自动创建的第一篇文章，用于测试显示功能。',
                'content' => '<div class=\"block\"><h2>欢迎使用文章管理系统</h2><p>这是系统自动创建的第一篇文章，用于测试显示功能。</p><p>您可以通过后台管理界面添加、编辑和删除文章。</p><p>系统支持文章置顶、SEO设置等功能。</p></div>',
                'top' => 1,
                'addtime' => date('Y-m-d')
            ],
            [
                'title' => 'div 元素使用指南',
                'keywords' => 'div,元素,使用指南',
                'description' => '本文介绍如何在系统中正确使用 div 元素展示内容。',
                'content' => '<div class=\"block\"><h2>div 元素使用指南</h2><p>在 HTML 中，div 元素是一个块级元素，用于组织和布局页面内容。</p><p>使用 div 元素可以：</p><ul><li>创建页面布局结构</li><li>组织相关内容</li><li>应用 CSS 样式</li><li>实现响应式设计</li></ul><p>在本系统中，您可以在文章内容中自由使用 div 元素来组织您的内容。</p></div>',
                'top' => 0,
                'addtime' => date('Y-m-d')
            ]
        ];
        
        foreach ($sampleData as $data) {
            $sql = "INSERT INTO " . DBQZ . "article (title, keywords, description, content, top, addtime, active) VALUES (:title, :keywords, :description, :content, :top, :addtime, '1')";
            $DB->exec($sql, [
                ':title' => $data['title'],
                ':keywords' => $data['keywords'],
                ':description' => $data['description'],
                ':content' => $data['content'],
                ':top' => $data['top'],
                ':addtime' => $data['addtime']
            ]);
        }
        
        // 重新查询数据
        if($kw){
            $reslist = $DB->query("SELECT * FROM " . DBQZ . "article WHERE title LIKE :title order by id desc limit :offset,:pagesize", array(':title' => '%' . $kw . '%', ':offset' => $offset, ':pagesize' => $pagesize))->fetchAll();
        } else {
            $reslist = $DB->query("SELECT * FROM " . DBQZ . "article WHERE 1=1 order by id desc limit :offset,:pagesize", array(':offset' => $offset, ':pagesize' => $pagesize))->fetchAll();
        }
        
        // 显示示例数据
        $has_data = false;
        if (is_array($reslist) && count($reslist) > 0) {
            $has_data = true;
            foreach ($reslist as $res) {
                $isTop = $res['top'] == 1 ? '<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-600 ml-2">置顶</span>' : '';
                echo "<tr class=\"tw-tr\">";
                echo "<td class=\"tw-td text-center font-medium text-slate-800\">" . $res["id"] . "</td>";
                echo "<td class=\"tw-td font-medium text-slate-800\">" . htmlspecialchars($res["title"]) . $isTop . "</td>";
                echo "<td class=\"tw-td text-slate-500 text-xs\">" . $res["addtime"] . "</td>";
                echo "<td class=\"tw-td text-center\"><span class=\"bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md text-xs\">" . ($res["count"] ?? 0) . "</span></td>";
                
                if ($res["active"] == 1) {
                    echo "<td class=\"tw-td text-center\"><button type=\"button\" class=\"tw-btn-xs bg-emerald-50 text-emerald-600 border border-emerald-100 hover:bg-emerald-100\" onclick=\"setActive(" . $res["id"] . ",0)\"><i class=\"fa fa-check-circle mr-1\"></i>显示</button></td>";
                } else {
                    echo "<td class=\"tw-td text-center\"><button type=\"button\" class=\"tw-btn-xs bg-amber-50 text-amber-600 border border-amber-100 hover:bg-amber-100\" onclick=\"setActive(" . $res["id"] . ",1)\"><i class=\"fa fa-eye-slash mr-1\"></i>隐藏</button></td>";
                }
                
                echo "<td class=\"tw-td text-right space-x-1\">
                    <a href=\"../?mod=article&id=" . $res["id"] . "\" target=\"_blank\" class=\"tw-btn-xs bg-brand-50 text-brand-600 border border-brand-100 hover:bg-brand-100\" title=\"查看\"><i class=\"fa fa-external-link\"></i></a>
                    <a href=\"./article.php?my=edit&id=" . $res["id"] . "\" class=\"tw-btn-xs bg-sky-50 text-sky-600 border border-sky-100 hover:bg-sky-100\" title=\"编辑\"><i class=\"fa fa-edit\"></i></a>
                    <a href=\"./article.php?my=delete&id=" . $res["id"] . "\" class=\"tw-btn-xs bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-100\" onclick=\"return confirm('确定要永久删除这篇文章吗？');\" title=\"删除\"><i class=\"fa fa-trash-o\"></i></a>
                </td>";
                echo "</tr>";
            }
        }
    } else {
        // 其他数据库错误
        echo "<tr><td colspan=6 class=\"tw-td text-center text-rose-500 py-6\"><i class=\"fa fa-warning mr-2\"></i>数据库错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
    }
} catch (Exception $e) {
    // 捕获其他所有错误
    echo "<tr><td colspan=6 class=\"tw-td text-center text-rose-500 py-6\"><i class=\"fa fa-warning mr-2\"></i>系统错误：" . htmlspecialchars($e->getMessage()) . "</td></tr>";
}
?>
                    </tbody>
                </table>
            </div>

            <!-- 分页区域 -->
            <?php if ($pages > 1): ?>
            <div class="tw-pagination">
                <?php 
                $first = 1;
                $prev = $page - 1;
                $next = $page + 1;
                $last = $pages;
                
                if ($page > 1) {
                    echo "<a href=\"article.php?page=" . $first . (isset($link) ? $link : '') . "\" class=\"tw-page-link\"><i class=\"fa fa-angle-double-left\"></i></a>";
                    echo "<a href=\"article.php?page=" . $prev . (isset($link) ? $link : '') . "\" class=\"tw-page-link\"><i class=\"fa fa-angle-left\"></i></a>";
                } else {
                    echo "<span class=\"tw-page-link tw-page-disabled\"><i class=\"fa fa-angle-double-left\"></i></span>";
                    echo "<span class=\"tw-page-link tw-page-disabled\"><i class=\"fa fa-angle-left\"></i></span>";
                }
                
                $start = $page - 3 > 1 ? $page - 3 : 1;
                $end = $page + 3 < $pages ? $page + 3 : $pages;
                
                if ($start > 1) {
                    echo "<span class=\"tw-page-link border-transparent bg-transparent text-slate-400 pointer-events-none\">...</span>";
                }
                
                for ($i = $start; $i < $page; $i++) {
                    echo "<a href=\"article.php?page=" . $i . (isset($link) ? $link : '') . "\" class=\"tw-page-link\">" . $i . "</a>";
                }
                
                echo "<span class=\"tw-page-link tw-page-active\">" . $page . "</span>";
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo "<a href=\"article.php?page=" . $i . (isset($link) ? $link : '') . "\" class=\"tw-page-link\">" . $i . "</a>";
                }
                
                if ($end < $pages) {
                    echo "<span class=\"tw-page-link border-transparent bg-transparent text-slate-400 pointer-events-none\">...</span>";
                }
                
                if ($page < $pages) {
                    echo "<a href=\"article.php?page=" . $next . (isset($link) ? $link : '') . "\" class=\"tw-page-link\"><i class=\"fa fa-angle-right\"></i></a>";
                    echo "<a href=\"article.php?page=" . $last . (isset($link) ? $link : '') . "\" class=\"tw-page-link\"><i class=\"fa fa-angle-double-right\"></i></a>";
                } else {
                    echo "<span class=\"tw-page-link tw-page-disabled\"><i class=\"fa fa-angle-right\"></i></span>";
                    echo "<span class=\"tw-page-link tw-page-disabled\"><i class=\"fa fa-angle-double-right\"></i></span>";
                }
                ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php 
}
?>
</div>
<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/article.js?ver=<?php echo VERSION;?>"></script>
</body>
</html>