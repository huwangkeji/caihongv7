<?php
/**
 * 系统数据清理
**/
include("../includes/common.php");
$title='系统数据清理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<div class="w-full max-w-[1200px] mx-auto">
<?php
adminpermission('super', 1);
$mod=isset($_GET['mod'])?$_GET['mod']:null;
if($mod=='cleancache'){
$CACHE->clear();
if(function_exists("opcache_reset"))@opcache_reset();
showmsg('清理系统设置缓存成功！',1);
}elseif($mod=='cleanlog'){
$DB->exec("TRUNCATE TABLE `pre_logs`");
showmsg('清空社区对接日志成功！',1);
}elseif($mod=='cleanpay'){
$DB->exec("DELETE FROM `pre_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("DELETE FROM `pre_pay` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-3 hours"))."' and status=0");
$DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("DELETE FROM `pre_cart` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-12 hours"))."' and status<2");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除30天前支付记录成功！',1);
}elseif($mod=='cleanorders'){
$DB->exec("DELETE FROM `pre_orders` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_orders`");
showmsg('删除30天前订单记录成功！',1);
}elseif($mod=='cleanqiandao'){
$DB->exec("DELETE FROM `pre_qiandao` WHERE time<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_qiandao`");
showmsg('删除30天前签到记录成功！',1);
}elseif($mod=='cleanwork'){
$DB->exec("DELETE FROM `pre_workorder` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_workorder`");
showmsg('删除30天前工单记录成功！',1);
}elseif($mod=='cleanpoints'){
$DB->exec("DELETE FROM `pre_points` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-7 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_points`");
showmsg('删除7天前收支明细成功！',1);
}elseif($mod=='cleangift'){
$DB->exec("DELETE FROM `pre_giftlog` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-1 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_giftlog`");
showmsg('删除1天前中奖记录成功！',1);
}elseif($mod=='cleaninvite'){
$DB->exec("DELETE FROM `pre_invitelog` WHERE date<'".date("Y-m-d H:i:s",strtotime("-1 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_invitelog`");
showmsg('删除1天前推广记录成功！',1);
}elseif($mod=='cleanpayi' && $_POST['do']=='submit'){
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_pay` WHERE money<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除支付记录成功！',1);
}elseif($mod=='cleanordersi' && $_POST['do']=='submit'){
if(!checkRefererHost())exit();
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_orders` WHERE money<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_orders`");
showmsg('删除订单记录成功！',1);
}elseif($mod=='cleansite' && $_POST['do']=='submit'){
if(!checkRefererHost())exit();
$days = intval($_POST['days']);
$money = daddslashes($_POST['money']);
if($days<=0 || $money==null)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_site` WHERE rmb<='$money' and addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."' and (lasttime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."' or lasttime is null)");
$DB->exec("OPTIMIZE TABLE `pre_pay`");
showmsg('删除分站记录成功！',1);
}else{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-hdd-o text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">系统快捷清理</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                
                <!-- 缓存清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-brand-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-database"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">缓存清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理系统全局配置及模版产生的冗余缓存数据，释放内存。</p>
                    <a href="./clean.php?mod=cleancache" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-refresh mr-1.5"></i>清理设置缓存
                    </a>
                </div>

                <!-- 日志清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-rose-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-eraser"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">日志清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">彻底清空社区对接日志，释放数据库空间，加速检索。</p>
                    <a href="./clean.php?mod=cleanlog" onclick="return confirm('警告：确认要彻底清空所有的社区对接日志吗？此操作不可逆！');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-rose-600 font-medium hover:bg-rose-50 hover:border-rose-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>清空对接日志
                    </a>
                </div>

                <!-- 支付记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-amber-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-credit-card"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">支付记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理大于 30 天的已支付记录及未支付订单，优化结算表。</p>
                    <a href="./clean.php?mod=cleanpay" onclick="return confirm('确认要永久删除 30 天前的所有支付记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除30天前记录
                    </a>
                </div>

                <!-- 订单记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-emerald-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">订单记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理大于 30 天的所有历史订单数据，提升后台订单加载速度。</p>
                    <a href="./clean.php?mod=cleanorders" onclick="return confirm('确认要永久删除 30 天前的所有订单记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除30天前订单
                    </a>
                </div>

                <!-- 收支明细清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-sky-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-money"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">收支明细清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理大于 7 天的资金流水变动明细，减轻财务表负担。</p>
                    <a href="./clean.php?mod=cleanpoints" onclick="return confirm('确认要永久删除 7 天前的所有收支明细记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-sky-50 hover:text-sky-600 hover:border-sky-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除7天前明细
                    </a>
                </div>

                <!-- 中奖记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-orange-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-gift"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">中奖记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">快速清理大于 1 天的历史中奖活动留存记录。</p>
                    <a href="./clean.php?mod=cleangift" onclick="return confirm('确认要删除 1 天前的中奖记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除1天前记录
                    </a>
                </div>

                <!-- 推广记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-fuchsia-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-fuchsia-100 text-fuchsia-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-share-alt"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">推广记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理大于 1 天的分享推广点击及拉新流水日志。</p>
                    <a href="./clean.php?mod=cleaninvite" onclick="return confirm('确认要删除 1 天前的推广拉新记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-fuchsia-50 hover:text-fuchsia-600 hover:border-fuchsia-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除1天前记录
                    </a>
                </div>

                <!-- 签到记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-teal-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">签到记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">清理大于 30 天的所有用户历史日常签到签到记录。</p>
                    <a href="./clean.php?mod=cleanqiandao" onclick="return confirm('确认要永久删除 30 天前的签到记录吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-teal-50 hover:text-teal-600 hover:border-teal-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除30天前签到
                    </a>
                </div>

                <!-- 工单记录清理 -->
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-5 hover:border-cyan-300 hover:shadow-md transition-all duration-300 flex flex-col group">
                    <div class="flex items-center mb-3">
                        <div class="w-10 h-10 rounded-full bg-cyan-100 text-cyan-500 flex items-center justify-center text-lg mr-3 group-hover:scale-110 transition-transform">
                            <i class="fa fa-tasks"></i>
                        </div>
                        <h4 class="font-bold text-slate-700 m-0">工单记录清理</h4>
                    </div>
                    <p class="text-xs text-slate-500 mb-4 flex-1">自动清空超过 30 天以上的历史客诉工单及回复内容。</p>
                    <a href="./clean.php?mod=cleanwork" onclick="return confirm('确认要彻底删除 30 天前的客诉工单吗？');" class="inline-flex items-center justify-center w-full h-10 rounded-lg bg-white border border-slate-200 text-slate-600 font-medium hover:bg-cyan-50 hover:text-cyan-600 hover:border-cyan-200 transition-colors shadow-sm text-sm">
                        <i class="fa fa-trash-o mr-1.5"></i>删除30天前工单
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- 自定义深度清理面板 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-sliders text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">自定义深度清理</h3>
        </div>
        <div class="p-6">
            <div class="p-4 mb-5 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-sm flex items-start shadow-sm">
                <i class="fa fa-exclamation-triangle mt-0.5 mr-2 shrink-0"></i> 
                <span>警告：自定义清理模块将根据您填写的条件，强行且永久地从数据库中抹除匹配数据，一旦执行无法回退，请三思而后行。</span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- 支付记录自定义 -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <form action="./clean.php?mod=cleanpayi" method="post" role="form" class="space-y-4">
                        <input type="hidden" name="do" value="submit"/>
                        <h4 class="font-bold text-slate-700 flex items-center m-0"><i class="fa fa-credit-card text-slate-400 mr-2"></i>自定义支付记录</h4>
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">清理</span>
                                <input type="number" name="days" value="" class="w-full h-10 pl-12 pr-10 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">天前</span>
                            </div>
                            
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">且小于</span>
                                <input type="number" step="0.01" name="money" value="" class="w-full h-10 pl-16 pr-10 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">元</span>
                            </div>
                        </div>
                        
                        <input type="submit" name="submit" value="执行强力删除" class="w-full h-10 rounded-lg bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors cursor-pointer text-sm mt-2" onclick="return confirm('即将删除符合条件的所有支付记录，确定执行吗？');"/>
                    </form>
                </div>

                <!-- 订单记录自定义 -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <form action="./clean.php?mod=cleanordersi" method="post" role="form" class="space-y-4">
                        <input type="hidden" name="do" value="submit"/>
                        <h4 class="font-bold text-slate-700 flex items-center m-0"><i class="fa fa-shopping-cart text-slate-400 mr-2"></i>自定义订单记录</h4>
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">清理</span>
                                <input type="number" name="days" value="" class="w-full h-10 pl-12 pr-10 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">天前</span>
                            </div>
                            
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">且小于</span>
                                <input type="number" step="0.01" name="money" value="" class="w-full h-10 pl-16 pr-10 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">元</span>
                            </div>
                        </div>
                        
                        <input type="submit" name="submit" value="执行强力删除" class="w-full h-10 rounded-lg bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors cursor-pointer text-sm mt-2" onclick="return confirm('即将删除符合条件的所有订单记录，确定执行吗？');"/>
                    </form>
                </div>

                <!-- 分站记录自定义 -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <form action="./clean.php?mod=cleansite" method="post" role="form" class="space-y-4">
                        <input type="hidden" name="do" value="submit"/>
                        <h4 class="font-bold text-slate-700 flex items-center m-0"><i class="fa fa-sitemap text-slate-400 mr-2"></i>沉睡分站清理</h4>
                        
                        <div class="space-y-3">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">超过</span>
                                <input type="number" name="days" value="30" class="w-full h-10 pl-12 pr-28 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">天未登录分站</span>
                            </div>
                            
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 text-xs font-semibold">且余额少于</span>
                                <input type="number" step="0.01" name="money" value="0" class="w-full h-10 pl-20 pr-10 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" required/>
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">元</span>
                            </div>
                        </div>
                        
                        <input type="submit" name="submit" value="执行强力删除" class="w-full h-10 rounded-lg bg-rose-500 text-white font-medium hover:bg-rose-600 shadow-sm transition-colors cursor-pointer text-sm mt-2" onclick="return confirm('即将永久注销并删除符合条件的沉睡分站，操作极其危险，请谨慎确认！');"/>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-center">
            <i class="fa fa-info-circle text-brand-500 mr-2 text-sm"></i>温馨提示：定期进行系统数据清理与优化，有助于减轻服务器负载并显著提升网站响应速度。
        </div>
    </div>
<?php }?>
</div>
