<?php
/**
 * 分站管理
**/
include("../includes/common.php");
$title='分站管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<!-- 弹窗：搜索分站 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="search" role="dialog" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-search text-brand-500 mr-2 text-lg"></i>搜索分站</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#search">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6 space-y-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fa fa-search"></i>
            </div>
            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" name="kw" placeholder="请输入分站ID或用户名或域名或站长QQ">
        </div>
        <button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]" id="search_submit">立即检索</button>
      </div>
  </div>
</div>

<!-- 弹窗：分类查看 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="search2" role="dialog" aria-hidden="true">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-filter text-amber-500 mr-2 text-lg"></i>分类筛查</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#search2">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6 space-y-4">
        <select name="power" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 cursor-pointer transition-colors">
            <option value="1">按 普及版 筛选</option>
            <option value="2">按 专业版 筛选</option>
        </select>
        <button type="button" class="w-full h-11 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-all duration-200 active:scale-[0.98]" id="search2_submit">确定筛选</button>
      </div>
  </div>
</div>

<!-- 弹窗：余额充值 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="modal-rmb" role="dialog" aria-hidden="true">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-money text-emerald-500 mr-2 text-lg"></i>站点余额调整</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#modal-rmb">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form id="form-rmb" class="space-y-4">
            <input type="hidden" name="zid" value="">
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <select name="do" class="w-24 h-11 px-3 text-sm bg-slate-50 border-0 border-r border-slate-200 focus:ring-0 cursor-pointer font-medium text-slate-700">
                    <option value="0">增加(+)</option>
                    <option value="1">扣除(-)</option>
                </select>
                <input type="number" class="flex-1 h-11 px-3 text-sm bg-white border-0 focus:ring-0 font-mono font-bold text-emerald-600" name="rmb" placeholder="输入金额">
                <span class="w-10 h-11 flex items-center justify-center bg-slate-50 border-0 border-l border-slate-200 text-sm font-medium text-slate-500">元</span>
            </div>
        </form>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0">
        <button type="button" class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm close-modal" data-target="#modal-rmb">取消</button>
        <button type="button" class="px-5 py-2 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200 active:scale-[0.98]" id="recharge">确认操作</button>
      </div>
  </div>
</div>

<div class="w-full max-w-[1400px] mx-auto space-y-6 pb-10">

<?php

adminpermission('site', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='replace')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-retweet text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">分站域名批量全量替换</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitelist.php?my=replace_do" method="POST" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">旧域名 (待替换)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <i class="fa fa-globe"></i>
                        </div>
                        <input type="text" name="olddomain" value="" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" placeholder="只填写主域名，例如：domain.com" required/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">新域名 (新节点)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500">
                            <i class="fa fa-link"></i>
                        </div>
                        <input type="text" name="newdomain" value="" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="只填写主域名，例如：domain.com" required/>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认全库替换域名
                    </button>
                    <a href="./sitelist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php
}
elseif($my=='replace_do')
{
$olddomain=trim($_POST['olddomain']);
$newdomain=trim($_POST['newdomain']);
if($olddomain==NULL or $newdomain==NULL){
	showmsg('请确保每项都不为空！',3);
} elseif($olddomain==$newdomain){
	showmsg('原域名和新域名不能一样！',3);
} elseif(!preg_match('/^[a-zA-Z0-9\.\-\_]+$/',$newdomain) || strpos($newdomain,'.')===false){
	showmsg('新域名不合法',3);
} else {
$sql="update pre_site set domain = replace(domain,'".$olddomain."','".$newdomain."'), domain2 = replace(domain2,'".$olddomain."','".$newdomain."')";
if($DB->exec($sql)!==false){
	showmsg('批量修改域名成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
}else
	showmsg('批量修改域名失败！'.$DB->error(),4);
}
}
elseif($my=='add')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">为主站添加一个新分站</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitelist.php?my=add_submit" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">分站代理层级类型</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="power">
                            <option value="1">普及版代理站</option>
                            <option value="2">专业版总控站</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">分站专属绑定域名</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="domain" value="" placeholder="输入欲挂载的公网域名" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">超级管理员账号</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono font-bold" name="user" value="" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">管理员初始密码</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="pwd" value="123456" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">站点初始余额账户</label>
                        <div class="relative">
                            <input type="text" class="w-full h-11 pl-4 pr-8 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors font-mono text-emerald-600 font-bold" name="rmb" value="0" required>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-medium text-slate-400 text-xs pointer-events-none">元</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">代理站长联系 QQ</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">站点合约到期时间</label>
                        <input type="date" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="endtime" value="<?php echo date("Y-m-d",strtotime("+1 years"));?>" required>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确定生成新分站
                    </button>
                    <a href="./sitelist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php
}
elseif($my=='add2')
{
$zid=$_GET['zid'];
$row=$DB->getRow("select * from pre_site where zid='$zid' limit 1");
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-sitemap text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">向指定 ZID 挂载扩展分站节点</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitelist.php?my=add2_submit&zid=<?php echo $zid;?>" method="POST" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">分站代理层级类型</label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="power">
                        <option value="1">普及版代理站</option>
                        <option value="2">专业版总控站</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">分站专属绑定域名</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="domain" value="" placeholder="输入欲挂载的公网主域名" required>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">站点合约到期时间</label>
                    <input type="date" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="endtime" value="<?php echo date("Y-m-d",strtotime("+1 years"));?>" required>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确定生成关联节点
                    </button>
                    <a href="./sitelist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php
}
elseif($my=='edit')
{
$zid=$_GET['zid'];
$row=$DB->getRow("select * from pre_site where zid='$zid' limit 1");
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-4xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>修改分站底层核心信息 (ZID: <?php echo $zid;?>)
            </h3>
            <a href="./siteprice.php?zid=<?php echo $zid;?>" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm text-xs shrink-0">
                <i class="fa fa-cogs mr-1.5"></i>自定义密价规则
            </a>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./sitelist.php?my=edit_submit&zid=<?php echo $zid;?>" method="POST" role="form" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">当前站点从属架构</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 cursor-pointer transition-colors" name="power" default="<?php echo $row['power'];?>">
                            <option value="1">普及版 (子站级)</option>
                            <option value="2">专业版 (总控级)</option>
                        </select>
                    </div>

                    <?php if($row['power']==1): ?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">溯源上级总控 ZID</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-100 text-slate-500 font-mono shadow-inner cursor-not-allowed" name="upzid" value="<?php echo $row['upzid'];?>" disabled>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">主要入站入口域名 (绑定主域)</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="domain" value="<?php echo $row['domain'];?>" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">次级解析备用域名 (附加域)</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="domain2" value="<?php echo $row['domain2'];?>" placeholder="没有可留空">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-emerald-50/50 border border-emerald-100 rounded-xl shadow-inner">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">当前站点可用总额度</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-semibold">¥</div>
                            <input type="text" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono font-bold text-emerald-600" name="rmb" value="<?php echo $row['rmb'];?>" required>
                        </div>
                    </div>

                    <?php if($conf['tixian_limit']): ?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">其中冻结/提现可支配基准额</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-semibold">¥</div>
                            <input type="text" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 bg-slate-100 text-slate-500 font-mono shadow-inner cursor-not-allowed" value="<?php echo ($row['rmbtc']>$row['rmb']?$row['rmb']:$row['rmbtc']);?>" disabled>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">对客显示站点名称 (Title)</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-bold" name="sitename" value="<?php echo htmlspecialchars($row['sitename']);?>">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">代理长驻联系方式 (QQ)</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="<?php echo $row['qq'];?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">底层结算路由账号</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="pay_account" value="<?php echo $row['pay_account'];?>" placeholder="例如：支付宝或微信账号">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">结算核验实名认证</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="pay_name" value="<?php echo $row['pay_name'];?>" placeholder="例如：张三">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">合约期限与到期限制网关</label>
                        <input type="date" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" name="endtime" value="<?php echo date("Y-m-d",strtotime($row['endtime']));?>" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">重置站长控制台密码</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono placeholder:text-slate-300" name="pwd" value="" placeholder="无需重置请保持此项为空">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-12 rounded-xl bg-sky-500 text-white font-bold text-[15px] hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>确认保存并下发节点更新
                    </button>
                    <a href="./sitelist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<script>
// 默认下拉项回显补偿
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>
<?php
}
elseif($my=='add_submit')
{
$power=intval($_POST['power']);
$user=trim($_POST['user']);
$pwd=trim($_POST['pwd']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
$endtime=$_POST['endtime'];
$sitename=$conf['sitename'];
$keywords=$conf['keywords'];
$description=$conf['description'];
if($user==NULL or $pwd==NULL or $domain==NULL or $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select user from pre_site where user='$user' limit 1");
if($rows)
	showmsg('用户名已存在！',3);
$rows=$DB->getRow("select * from pre_site where domain='$domain' limit 1");
if($rows || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode('|',$conf['fenzhan_remain'])))
	showmsg('域名已存在！',3);
if($conf['fenzhan_html']==1){
	$anounce=$conf['anounce'];
	$alert=$conf['alert'];
}
$sql="insert into `pre_site` (`power`,`domain`,`domain2`,`user`,`pwd`,`rmb`,`qq`,`sitename`,`keywords`,`description`,`anounce`,`alert`,`addtime`,`endtime`,`status`) values ('".$power."','".$domain."','".$domain2."','".$user."','".$pwd."','".$rmb."','".$qq."','".$sitename."','".$keywords."','".$description."','".addslashes($anounce)."','".addslashes($alert)."','".$date."','".$endtime."','1')";
if($DB->exec($sql)!==false){
	showmsg('添加分站成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
}else
	showmsg('添加分站失败！'.$DB->error(),4);
}
}
elseif($my=='add2_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$power=intval($_POST['power']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$endtime=$_POST['endtime'];
$sitename=$conf['sitename'];
$keywords=$conf['keywords'];
$description=$conf['description'];
if($domain==NULL || $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select zid from pre_site where domain='$domain' limit 1");
if($rows || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode('|',$conf['fenzhan_remain'])))
	showmsg('域名已存在！',3);
if($DB->exec("update pre_site set power='$power',domain='$domain',domain2='$domain2',qq='$qq',sitename='$sitename',keywords='$keywords',description='$description',endtime='$endtime' where zid='{$zid}'")!==false)
	showmsg('添加分站成功！<br/><br/><a href="./userlist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100 mr-2">>>返回用户列表</a><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
else
	showmsg('添加分站失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid,rmb,rmbtc from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$power=intval($_POST['power']);
$domain=trim(strtolower($_POST['domain']));
$domain2=trim(strtolower($_POST['domain2']));
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
$endtime=$_POST['endtime'];
$sitename=$_POST['sitename'];
$pay_account=$_POST['pay_account'];
$pay_name=$_POST['pay_name'];
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($sitename==NULL or $rmb==NULL or $domain==NULL or $endtime==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($rmb<$rows['rmbtc'])$rmbtc=$rmb;
else $rmbtc=$rows['rmbtc'];
if($DB->exec("update pre_site set power='$power',domain='$domain',domain2='$domain2',rmb='$rmb',rmbtc='$rmbtc',qq='$qq',sitename='$sitename',pay_account='$pay_account',pay_name='$pay_name',endtime='$endtime'{$sql} where zid='{$zid}'")!==false)
	showmsg('修改分站成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
else
	showmsg('修改分站失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$zid=$_GET['zid'];
$sql="DELETE FROM pre_site WHERE zid='$zid'";
if($DB->exec($sql)!==false)
	showmsg('删除成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
elseif($my=='anounce')
{
if($DB->exec("update pre_site set anounce=NULL")!==false)
	showmsg('清空成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
else
	showmsg('清空失败！'.$DB->error(),4);
}
elseif($my=='price')
{
if($DB->exec("update pre_site set price=NULL")!==false)
	showmsg('清空成功！<br/><br/><a href="./sitelist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回分站列表</a>',1);
else
	showmsg('清空失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) FROM pre_site WHERE power>0");

?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-sitemap text-brand-500 mr-2 text-lg"></i>代理分站控制面板
                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows?> 个站点</span>
            </h3>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="./sitelist.php?my=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm">
                    <i class="fa fa-plus mr-1.5"></i>新建子站
                </a>
                <button type="button" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors text-sm open-modal" data-target="#search">
                    <i class="fa fa-search mr-1.5"></i>检索查询
                </button>
                <button type="button" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-amber-500 text-white font-medium hover:bg-amber-600 shadow-sm transition-colors text-sm open-modal" data-target="#search2">
                    <i class="fa fa-filter mr-1.5"></i>筛查过滤
                </button>
                
                <div class="relative group dropdown-container z-50">
                    <button type="button" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-sky-50 text-sky-600 font-medium hover:bg-sky-500 hover:text-white shadow-sm transition-colors text-sm">
                        批量操作 <i class="fa fa-angle-down ml-1.5"></i>
                    </button>
                    <!-- Tailwind 下拉菜单 -->
                    <div class="absolute left-0 top-10 w-60 bg-white border border-slate-100 shadow-xl rounded-xl overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right transform scale-95 group-hover:scale-100">
                        <div class="py-1">
                            <a href="./sitelist.php?my=replace" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 hover:text-brand-600 font-medium">
                                <i class="fa fa-retweet w-5 text-slate-400"></i> 分站二级域名批量替换
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="./sitelist.php?my=anounce" onclick="return confirm('⚠️ 确定要清空所有分站各自的公告代码吗？清空后将强制继承并显示主站公告内容。');" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-rose-50 hover:text-rose-600 font-medium">
                                <i class="fa fa-eraser w-5 text-slate-400"></i> 一键抹除下级自建公告
                            </a>
                            <a href="./sitelist.php?my=price" onclick="return confirm('⚠️ 确定要清空所有分站设置的专属商品加价/改价规则吗？清空后将强制恢复为主站全局价格体系。');" class="block px-4 py-2.5 text-sm text-slate-700 hover:bg-rose-50 hover:text-rose-600 font-medium">
                                <i class="fa fa-chain-broken w-5 text-slate-400"></i> 一键抹除下级专属定价
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-slate-600 shrink-0">余额列排序规则</span>
                <select id="tabSort" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors w-32">
                    <option value="">自然顺序</option>
                    <option value="0">升序 (从低到高)</option>
                    <option value="1">降序 (从高到低)</option>
                </select>
            </div>
            
            <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm shrink-0" title="无刷新重载分站节点集">
                <i class="fa fa-refresh"></i>
            </a>
        </div>
        
        <!-- 异步列表挂载点 -->
        <div id="listTable" class="w-full"></div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/sitelist.js?ver=<?php echo VERSION ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 处理原生模态框展示逻辑
    document.querySelectorAll('.open-modal').forEach(function(btn) {
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

    // 处理原生模态框关闭逻辑
    document.querySelectorAll('.close-modal').forEach(function(btn) {
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
    
    // 点击遮罩层外侧关闭
    document.querySelectorAll('[role="dialog"]').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('.close-modal').click();
            }
        });
    });
});
</script>
<?php }?>
</body>
</html>
