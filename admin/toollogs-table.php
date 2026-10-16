<?php
include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission('shop', 1);
$sqls = "";
$links = '';
$sql = " 1";
$sql .= $sqls;
$link = $links;
$numrows = $DB->getColumn("SELECT count(*) FROM pre_toollogs WHERE " . $sql);
$con = '系统共有 <b class="text-lg text-slate-800 mx-1">' . $numrows . '</b> 条上架日志记录';
?>

<form name="form1" id="form1">
    <!-- 响应式表格容器 -->
    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5 w-12 text-center">
                        <input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll(this)" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors">
                    </th>
                    <th class="px-6 py-3.5 font-semibold text-center w-20">日志ID</th>
                    <th class="px-6 py-3.5 font-semibold min-w-[300px]">上架内容摘要</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-44">上架时间</th>
                    <th class="px-6 py-3.5 font-semibold text-center w-36">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = isset($_GET['num']) ? intval($_GET['num']) : 30;
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM `pre_toollogs` WHERE " . $sql . " ORDER BY id DESC LIMIT " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">
            <td class="px-6 py-4 text-center">
                <input type="checkbox" name="checkbox[]" id="list1" value="' . $res['id'] . '" onClick="unselectall()" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors">
            </td>
            <td class="px-6 py-4 text-center font-bold text-slate-700 font-mono"><b>' . $res['id'] . '</b></td>
            <td class="px-6 py-4 font-medium text-slate-800 max-w-[400px] whitespace-normal break-words leading-snug">' . htmlspecialchars(mb_substr($res['content'], 0, 16, 'utf-8')) . '</td>
            <td class="px-6 py-4 text-center font-mono text-xs text-slate-500">' . $res['date'] . '</td>
            <td class="px-6 py-4 text-center whitespace-nowrap">
                <div class="inline-flex items-center justify-center gap-1.5">
                    <a class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm" href="./toollogsedit.php?my=edit&id=' . $res['id'] . '">编辑</a>
                    <span class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" href="javascript:delOrder(\'' . $res['id'] . '\')" onclick="delOrder(\'' . $res['id'] . '\')">删除</span>
                </div>
            </td>
          </tr>';
}

if($isEmpty) {
    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-history text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有检索到任何上架日志记录</p>
          </td></tr>';
}
?>
            </tbody>
        </table>
    </div>

    <!-- 底部批量操作工作台 -->
    <?php if(!$isEmpty): ?>
    <div class="mt-4 p-5 bg-slate-50 border border-slate-100 rounded-xl shadow-inner flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700 select-none">
                <input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors">
                <span>当页全选</span>
            </label>
            
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <select name="aid" class="h-10 px-3 text-sm bg-white border-0 border-r border-slate-200 focus:ring-0 cursor-pointer text-slate-700">
                    <option selected>批量操作选择</option>
                    <option value="1">&gt; 彻底删除选中项</option>
                </select>
                <button type="button" onclick="batchOperation()" class="px-5 h-10 bg-brand-600 hover:bg-brand-700 text-white font-medium text-sm transition-colors border-0">
                    确认执行
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</form>

<!-- 底部分页控制区 -->
<?php if(!$isEmpty): ?>
<div class="py-5 flex justify-center">
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
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $first . $link . '\')" class="'.$btn_normal.'">首页</a></li>';
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $prev . $link . '\')" class="'.$btn_normal.'"><i class="fa fa-angle-left"></i></a></li>';
    } else {
        echo '<li><span class="'.$btn_disabled.'">首页</span></li>';
        echo '<li><span class="'.$btn_disabled.'"><i class="fa fa-angle-left"></i></span></li>';
    }
    
    $start = $page - 10 > 1 ? $page - 10 : 1;
    $end = $page + 10 < $pages ? $page + 10 : $pages;
    
    for ($i = $start; $i < $page; $i++) {
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $i . $link . '\')" class="'.$btn_normal.'">' . $i . '</a></li>';
    }
    
    echo '<li><span class="'.$btn_active.'">' . $page . '</span></li>';
    
    for ($i = $page + 1; $i <= $end; $i++) {
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $i . $link . '\')" class="'.$btn_normal.'">' . $i . '</a></li>';
    }
    
    if ($page < $pages) {
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $next . $link . '\')" class="'.$btn_normal.'"><i class="fa fa-angle-right"></i></a></li>';
        echo '<li><a href="javascript:void(0)" onclick="listTable(\'page=' . $last . $link . '\')" class="'.$btn_normal.'">尾页</a></li>';
    } else {
        echo '<li><span class="'.$btn_disabled.'"><i class="fa fa-angle-right"></i></span></li>';
        echo '<li><span class="'.$btn_disabled.'">尾页</span></li>';
    }
    ?>
    </ul>
</div>
<?php endif; ?>

<!-- 动态通知父级页面更新总记录标题 -->
<script>
if($("#blocktitle").length > 0) {
    $("#blocktitle").html('<?php echo $con;?>');
}
</script>
