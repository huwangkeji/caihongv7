<?php
/**
 * 分类管理
**/
include("../includes/common.php");
$title='分类管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$my=isset($_GET['my'])?$_GET['my']:null;
?>
<script src="assets/js/jquery.dragsort-0.5.2.min.js"></script>

<div class="w-full max-w-[1400px] mx-auto">
<?php
adminpermission('shop', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='qk2')
{
$sql="TRUNCATE TABLE `pre_class`";
if($DB->exec($sql)!==false)
	exit("<script language='javascript'>alert('清空成功！');window.location.href='classlist.php';</script>");
else
	exit("<script language='javascript'>alert('清空失败！".$DB->error()."');history.go(-1);</script>");
}
elseif($my=='classimg'){
$numrows=$DB->getColumn("SELECT count(*) from pre_class");
?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
	<div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
		<h2 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
			修改分类图片&nbsp;
			<a href="./classlist.php" class="text-xs font-semibold text-brand-600 hover:underline ml-2">[返回]</a>
		</h2>
	</div>
      
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
          <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 font-semibold text-left">分类名称</th>
                <th class="px-6 py-3.5 font-semibold text-left min-w-[280px]">图片URL</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <form id="classlist">
<?php
$rs=$DB->query("SELECT * FROM pre_class WHERE 1 order by sort asc");
while($res = $rs->fetch())
{
	echo '<tr class="hover:bg-brand-50/40 transition-colors"><td class="px-6 py-4 font-medium text-slate-700">'.$res['name'].'</td><td class="px-6 py-4"><div class="flex items-center gap-2"><input type="file" id="file'.$res['cid'].'" onchange="fileUpload('.$res['cid'].')" style="display:none;"/><input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="img['.$res['cid'].']" value="'.$res['shopimg'].'" placeholder="填写图片URL" required><div class="inline-flex items-center gap-1 shrink-0"><a href="javascript:fileSelect('.$res['cid'].')" class="inline-flex items-center justify-center w-9 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors shadow-sm" title="上传图片"><i class="fa fa-upload"></i></a><a href="javascript:getImage('.$res['cid'].')" class="inline-flex items-center justify-center w-9 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm" title="自动获取图片"><i class="fa fa-search"></i></a><a href="javascript:fileView('.$res['cid'].')" class="inline-flex items-center justify-center w-9 h-10 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors shadow-sm" title="查看图片"><i class="fa fa-picture-o"></i></a></div></div></td></tr>';
}
echo '<tr><td colspan="2" class="px-6 py-4 bg-slate-50/50"><button type="button" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200" onclick="saveAllImages()">保存全部</button></td></tr>';
?>
			</form>
          </tbody>
        </table>
      </div>
      
	  <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 flex items-center">
	      <i class="fa fa-info-circle text-brand-500 mr-2 text-sm"></i>当前图片仅适用于部分首页模板
	  </div>
	</div>
<?php
}else{
?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
	<div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
		<h2 class="text-base sm:text-lg font-bold text-slate-800 m-0">商品分类</h2>
		<a href="javascript:listTable()" class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-brand-600 hover:bg-brand-50 hover:border-brand-200 shadow-sm transition-all duration-200" title="刷新分类列表">
		    <i class="fa fa-refresh"></i>
		</a>
	</div>
    <div id="listTable" class="p-4 sm:p-6"></div>
</div>
<?php }?>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/classlist.js?ver=<?php echo VERSION?>"></script>
</body>
</html>
