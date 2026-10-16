<?php
/**
 * 用户管理
**/
include("../includes/common.php");
$title='用户管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<!-- 弹窗：搜索用户 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 custom-modal" id="search" role="dialog" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-search text-brand-500 mr-2 text-lg"></i>精准检索用户</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#search">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6 space-y-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fa fa-user"></i>
            </div>
            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" name="kw" placeholder="请输入精确的用户名、UID 或 QQ">
        </div>
        <button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]" id="search_submit">立即检索提取</button>
      </div>
  </div>
</div>

<!-- 弹窗：余额充值扣款 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300 custom-modal" id="modal-rmb" role="dialog" aria-hidden="true">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-google-wallet text-emerald-500 mr-2 text-lg"></i>用户资金余额变更</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#modal-rmb">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form id="form-rmb" class="space-y-4">
            <input type="hidden" name="zid" value="">
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <select name="do" class="w-24 h-11 px-3 text-sm bg-slate-50 border-0 border-r border-slate-200 focus:ring-0 cursor-pointer font-medium text-slate-700">
                    <option value="0">充值入账(+)</option>
                    <option value="1">违规扣除(-)</option>
                </select>
                <input type="number" class="flex-1 h-11 px-3 text-sm bg-white border-0 focus:ring-0 font-mono font-bold text-emerald-600" name="rmb" placeholder="输入变动额度">
                <span class="w-10 h-11 flex items-center justify-center bg-slate-50 border-0 border-l border-slate-200 text-sm font-medium text-slate-500">元</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 text-center">资金变更将实时生效并记录底层流水</p>
        </form>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3 shrink-0">
        <button type="button" class="px-5 py-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm close-modal" data-target="#modal-rmb">放弃操作</button>
        <button type="button" class="px-5 py-2 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200 active:scale-[0.98]" id="recharge">确认执行</button>
      </div>
  </div>
</div>


<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10 mt-4">

<?php

adminpermission('site', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-user-plus text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">登记开通新用户</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./userlist.php?my=add_submit" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">唯一登录用户名 <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-bold" name="user" value="" placeholder="输入用户账号" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">初始账户密码 <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="pwd" value="123456" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">预存初始资金 <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 font-semibold">¥</div>
                            <input type="text" class="w-full h-11 pl-8 pr-8 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-emerald-50/30 transition-colors font-mono text-emerald-600 font-bold" name="rmb" value="0" required>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-medium text-slate-400 text-xs pointer-events-none">元</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">用户联系 QQ</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="" placeholder="选填，建议填写真实QQ">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确认创建账户
                    </button>
                    <a href="./userlist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
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
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改平台用户信息</h3>
            <span class="ml-3 px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">UID: #<?php echo $zid; ?></span>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./userlist.php?my=edit_submit&zid=<?php echo $zid;?>" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">从属上级站点 UID</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 bg-slate-100 text-slate-500 shadow-inner font-mono cursor-not-allowed" name="upzid" value="<?php echo $row['upzid'];?>" disabled>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">联系绑定 QQ</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="<?php echo $row['qq'];?>">
                    </div>
                </div>

                <div class="space-y-1.5 p-5 bg-slate-50 border border-slate-200 rounded-xl">
                    <label class="text-sm font-semibold text-slate-700">账户现存资金</label>
                    <div class="relative max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-semibold">¥</div>
                        <input type="text" class="w-full h-11 pl-8 pr-8 text-sm rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm bg-white transition-colors font-mono text-emerald-600 font-bold" name="rmb" value="<?php echo $row['rmb'];?>" required>
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-medium text-slate-400 text-xs pointer-events-none">元</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-2"><i class="fa fa-info-circle mr-1"></i> 直接修改余额不上账财务流水，建议使用列表页的资金变更功能。</p>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">强制重置账户密码</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono placeholder:text-slate-300" name="pwd" value="" placeholder="无需重置密码请务必保持此项为空白">
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>确认保存修改信息
                    </button>
                    <a href="./userlist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
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
<?php
}
elseif($my=='add_submit')
{
$user=trim($_POST['user']);
$pwd=trim($_POST['pwd']);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if($user==NULL or $pwd==NULL or $qq==NULL){
showmsg('保存拦截：请确保用户名、密码和QQ等必填项都不为空!',3);
} else {
$rows=$DB->getRow("select user from pre_site where user='$user' limit 1");
if($rows)
	showmsg('重名冲突：该用户名在系统内已存在！',3);
$sql="insert into `pre_site` (`power`,`user`,`pwd`,`rmb`,`qq`,`addtime`,`status`) values (0, :user, :pwd, :rmb, :qq, :date, 1)";
$data = [':user'=>$user, ':pwd'=>$pwd, ':rmb'=>$rmb, ':qq'=>$qq, ':date'=>$date];
if($DB->exec($sql, $data)!==false){
	showmsg('添加新用户成功！<br/><br/><a href="./userlist.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-brand-50 text-brand-600 rounded-lg hover:bg-brand-100 transition-colors">>> 立即返回用户列表</a>',1);
}else
	showmsg('添加用户失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$zid=intval($_GET['zid']);
$rows=$DB->getRow("select zid from pre_site where zid='$zid' limit 1");
if(!$rows)
	showmsg('非法篡改：当前编辑的用户记录不存在！',3);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($rmb==NULL){
showmsg('保存拦截：请确保各项资产数据都不为空!',3);
} else {
if($DB->exec("update pre_site set rmb='$rmb',qq='$qq'{$sql} where zid='{$zid}'")!==false)
	showmsg('修改用户信息成功！<br/><br/><a href="./userlist.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-sky-50 text-sky-600 rounded-lg hover:bg-sky-100 transition-colors">>> 返回用户列表</a>',1);
else
	showmsg('修改用户信息失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$zid=$_GET['zid'];
$sql="DELETE FROM pre_site WHERE zid='$zid'";
if($DB->exec($sql)!==false)
	showmsg('数据抹除成功！<br/><br/><a href="./userlist.php" class="inline-flex px-4 py-2 mt-4 text-sm bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 transition-colors">>> 返回用户列表</a>',1);
else
	showmsg('数据删除失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) from pre_site WHERE power=0");

?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-users text-brand-500 mr-2 text-lg"></i>平台普通账户矩阵池
                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows?> 个</span>
            </h3>
            
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="./userlist.php?my=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm active:scale-[0.98]">
                    <i class="fa fa-plus mr-1.5"></i>新建用户
                </a>
                <button type="button" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-colors text-sm open-modal active:scale-[0.98]" data-target="#search">
                    <i class="fa fa-search mr-1.5"></i>高级检索
                </button>
                <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm shrink-0" title="无刷新重载列表">
                    <i class="fa fa-refresh"></i>
                </a>
            </div>
        </div>

        <!-- 异步列表挂载点 -->
        <div id="listTable" class="w-full"></div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="assets/js/userlist.js?ver=<?php echo VERSION ?>"></script>
<script>
// 兼容性与原生模态框控制逻辑
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
    document.querySelectorAll('.custom-modal').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.querySelector('.close-modal').click();
            }
        });
    });
});

// Polyfill: 解决彻底脱离 Bootstrap 后 userlist.js 内可能调用的 $('#modal').modal('hide') 抛错或失效问题
if (typeof jQuery !== 'undefined') {
    (function ($) {
        var originalModal = $.fn.modal;
        $.fn.modal = function (action) {
            if (this.hasClass('custom-modal')) {
                if (action === 'hide') {
                    this.addClass('opacity-0');
                    this.find('.transform').addClass('scale-95');
                    var that = this;
                    setTimeout(function() {
                        that.addClass('hidden');
                    }, 300);
                } else if (action === 'show') {
                    this.removeClass('hidden');
                    var that = this;
                    setTimeout(function() {
                        that.removeClass('opacity-0');
                        that.find('.transform').removeClass('scale-95');
                    }, 10);
                }
                return this;
            }
            if (typeof originalModal === 'function') {
                return originalModal.apply(this, arguments);
            }
        };
    })(jQuery);
}
</script>
<?php }?>
</body>
</html>
