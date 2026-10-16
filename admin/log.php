<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shequ', 1);
$title = "社区/卡盟对接日志";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-history text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">社区/卡盟对接日志</h3>
        </div>
        
        <div class="divide-y divide-slate-100">
<?php 
$numrows = $DB->getColumn("SELECT count(*) from pre_logs WHERE action='社区对接' OR action='自动访问URL'");
$pagesize = 30;
$pages = ceil($numrows / $pagesize);
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_logs WHERE action='社区对接' OR action='自动访问URL' order by id desc limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
    echo "<div class=\"p-6 hover:bg-slate-50 transition-colors duration-200\">
            <div class=\"flex items-center text-xs font-bold text-slate-500 mb-3\">
                <i class=\"fa fa-clock-o mr-1.5\"></i>" . $res["addtime"] . "
            </div>
            <div class=\"space-y-3\">
                <div class=\"bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700 break-all font-mono shadow-sm\">
                    <div class=\"font-bold text-brand-600 mb-1.5 flex items-center\"><i class=\"fa fa-paper-plane mr-1.5\"></i>提交参数：</div>
                    <div class=\"leading-relaxed whitespace-pre-wrap\">" . $res["param"] . "</div>
                </div>
                <div class=\"bg-slate-50 border border-slate-200 rounded-lg p-4 text-sm text-slate-700 break-all font-mono shadow-sm\">
                    <div class=\"font-bold text-emerald-600 mb-1.5 flex items-center\"><i class=\"fa fa-reply mr-1.5\"></i>返回结果：</div>
                    <div class=\"leading-relaxed whitespace-pre-wrap\">" . $res["result"] . "</div>
                </div>
            </div>
          </div>";
}

if($isEmpty) {
    echo '<div class="py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有系统对接日志记录</p>
          </div>';
}
?>      
        </div>
        
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
                echo "<li><a href=\"log.php?page=" . $first . $link . "\" class=\"".$btn_normal."\">首页</a></li>";
                echo "<li><a href=\"log.php?page=" . $prev . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
            }
            $start = $page - 10 > 1 ? $page - 10 : 1;
            $end = $page + 10 < $pages ? $page + 10 : $pages;
            for ($i = $start; $i < $page; $i++) {
                echo "<li><a href=\"log.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
            for ($i = $page + 1; $i <= $end; $i++) {
                echo "<li><a href=\"log.php?page=" . $i . $link . "\" class=\"".$btn_normal."\">" . $i . "</a></li>";
            }
            if ($page < $pages) {
                echo "<li><a href=\"log.php?page=" . $next . $link . "\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
                echo "<li><a href=\"log.php?page=" . $last . $link . "\" class=\"".$btn_normal."\">尾页</a></li>";
            } else {
                echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
                echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
            }
            ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>
