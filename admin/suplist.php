<?php
/**
 * 供货商管理
**/
include("../includes/common.php");
$title='供货商管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='../sup/login.php';</script>");
?>

<!-- 弹窗：搜索供货商 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="search" role="dialog" aria-hidden="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-search text-brand-500 mr-2 text-lg"></i>搜索供货商</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#search">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6 space-y-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="fa fa-search"></i>
            </div>
            <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" name="kw" placeholder="请输入供货商名、QQ 或 ID">
        </div>
        <button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]" id="search_submit">立即检索</button>
      </div>
  </div>
</div>

<!-- 弹窗：余额充值 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="modal-rmb" role="dialog" aria-hidden="true">
  <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-money text-emerald-500 mr-2 text-lg"></i>供货商余额调整</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#modal-rmb">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="p-6">
        <form id="form-rmb" class="space-y-4">
            <input type="hidden" name="sid" value="">
            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-brand-500 focus-within:ring-1 focus-within:ring-brand-500 transition-all">
                <select name="do" class="w-24 h-11 px-3 text-sm bg-slate-50 border-0 border-r border-slate-200 focus:ring-0 cursor-pointer font-medium text-slate-700">
                    <option value="0">充值(+)</option>
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

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">

<?php

adminpermission('site', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-plus-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">新增入驻供货商</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./suplist.php?my=add_submit" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">登录用户名</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-bold" name="user" value="" required>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">初始登录密码</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="pwd" value="123456" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">初始资金余额</label>
                        <div class="relative">
                            <input type="text" class="w-full h-11 pl-4 pr-8 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors font-mono text-emerald-600 font-bold" name="rmb" value="0" required>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-medium text-slate-400 text-xs pointer-events-none">元</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">供货商联系 QQ</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="" required>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-check mr-1.5"></i>确定创建供货商
                    </button>
                    <a href="./suplist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-11 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
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
$sid=$_GET['sid'];
$rows=$DB->getRow("select * from pre_supplier where sid='$sid' limit 1");

if(!$rows)showmsg('当前记录不存在！',3);
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-pencil-square-o text-sky-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">修改供货商核心信息</h3>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./suplist.php?my=edit_submit&sid=<?php echo $sid;?>" method="POST" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">供货商实时资金余额</label>
                        <div class="relative">
                            <input type="text" class="w-full h-11 pl-4 pr-8 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors font-mono text-emerald-600 font-bold" name="rmb" value="<?php echo $rows['rmb'];?>" required>
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center font-medium text-slate-400 text-xs pointer-events-none">元</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">供货商联系 QQ</label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="qq" value="<?php echo $rows['qq'];?>" required>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">强制重置密码</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono placeholder:text-slate-300" name="pwd" value="" placeholder="无需重置请保持此项为空白">
                </div>

                <div class="pt-6 border-t border-slate-50 flex flex-col sm:flex-row gap-3">
                    <button type="submit" class="flex-1 h-12 rounded-xl bg-sky-500 text-white font-bold text-[15px] hover:bg-sky-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-floppy-o mr-1.5"></i>确认保存修改信息
                    </button>
                    <a href="./suplist.php" class="inline-flex items-center justify-center w-full sm:w-32 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors shadow-sm shrink-0">
                        放弃返回
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php
}
elseif($my=='add_submit')
{
$user=trim($_POST['user']);
$pwd=trim($_POST['pwd']);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if($user==NULL or $pwd==NULL or $qq==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
$rows=$DB->getRow("select user from pre_supplier where user='$user' limit 1");
if($rows)
	showmsg('用户名已存在！',3);
$sql="insert into `pre_supplier` (`user`,`pwd`,`rmb`,`qq`,`addtime`,`status`) values (:user, :pwd, :rmb, :qq, :date, 1)";
$data = [':user'=>$user, ':pwd'=>$pwd, ':rmb'=>$rmb, ':qq'=>$qq, ':date'=>$date];
if($DB->exec($sql, $data)!==false){
	showmsg('添加供货商成功！<br/><br/><a href="./suplist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回供货商列表</a>',1);
}else
	showmsg('添加供货商失败！'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
$sid=intval($_GET['sid']);
$rows=$DB->getRow("select sid from pre_supplier where sid='$sid' limit 1");
if(!$rows)
	showmsg('当前记录不存在！',3);
$rmb=$_POST['rmb'];
$qq=trim($_POST['qq']);
if(!empty($_POST['pwd']))$sql=",pwd='{$_POST['pwd']}'";
if($rmb==NULL){
showmsg('保存错误,请确保每项都不为空!',3);
} else {
if($DB->exec("update pre_supplier set rmb='$rmb',qq='$qq'{$sql} where sid='{$sid}'")!==false)
	showmsg('修改供货商成功！<br/><br/><a href="./suplist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回供货商列表</a>',1);
else
	showmsg('修改供货商失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
$sid=$_GET['sid'];
$sql="DELETE FROM pre_supplier WHERE sid='$sid'";
if($DB->exec($sql)!==false)
	showmsg('删除成功！<br/><br/><a href="./suplist.php" class="inline-flex px-4 py-2 bg-brand-50 text-brand-600 rounded-lg mt-3 hover:bg-brand-100">>>返回供货商列表</a>',1);
else
	showmsg('删除失败！'.$DB->error(),4);
}
else
{

$numrows=$DB->getColumn("SELECT count(*) from pre_supplier WHERE 1");

?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-truck text-brand-500 mr-2 text-lg"></i>内部供货商矩阵池
                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows?> 家</span>
            </h3>
            
            <div class="flex flex-wrap items-center gap-2">
                <a href="./suplist.php?my=add" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm">
                    <i class="fa fa-plus mr-1.5"></i>新建供货商
                </a>
                <button type="button" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-colors text-sm open-modal" data-target="#search">
                    <i class="fa fa-search mr-1.5"></i>检索查询
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
<script src="assets/js/suplist.js?ver=<?php echo VERSION ?>"></script>
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
