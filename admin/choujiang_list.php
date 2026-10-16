<?php

include "../includes/common.php";
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('shop', 1);
$title = "中奖记录";
include "head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="w-full max-w-[1200px] mx-auto">
	<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- 头部标题区 -->
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-trophy text-brand-500 mr-2 text-lg"></i>中奖记录
            </h3>
            <a href="choujiang.php" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 font-medium text-sm transition-colors shadow-sm">
                <i class="fa fa-reply mr-1.5"></i>返回抽奖配置
            </a>
        </div>

        <!-- 响应式表格容器 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-24">站点ID</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28">中奖商品ID</th>
                        <th class="px-6 py-3.5 font-semibold text-left min-w-[200px]">奖品名称</th>
                        <th class="px-6 py-3.5 font-semibold text-center">订单号</th>
                        <th class="px-6 py-3.5 font-semibold text-center">中奖时间</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php 
                $list = $DB->query("SELECT a.*,(select b.name from pre_gift as b where a.gid=b.id) as name FROM pre_giftlog as a WHERE status=1 ORDER BY id DESC");
                $isEmpty = true;
                while ($cjlist = $list->fetch()) {
                    $isEmpty = false;
                	?>
                    <tr class="hover:bg-brand-50/40 transition-colors duration-200">
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2.5rem] h-7 px-2 rounded bg-sky-50 text-sky-600 font-bold text-xs shadow-sm">
                                <?php echo $cjlist["zid"];?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2.5rem] h-7 px-2 rounded bg-indigo-50 text-indigo-600 font-bold text-xs shadow-sm">
                                <?php echo $cjlist["tid"];?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-brand-600">
                            <?php echo $cjlist["name"];?>
                        </td>
                        <td class="px-6 py-4 text-center font-mono">
                            <a href="./list.php?kw=<?php echo $cjlist["tradeno"];?>&type=0" target="_blank" class="text-slate-500 hover:text-brand-600 hover:underline transition-colors" title="点击查看订单详情">
                                <?php echo $cjlist["tradeno"];?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-center text-xs text-slate-500">
                            <?php echo $cjlist["addtime"];?>
                        </td>
                    </tr>
                <?php 
                }
                
                // 如果没有数据，显示缺省提示
                if ($isEmpty) {
                    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
                            <p class="text-sm font-medium text-slate-500">暂无任何中奖记录</p>
                          </td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
	</div>
</div>
