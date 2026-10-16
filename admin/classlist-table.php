<?php
include '../includes/common.php';
if ($islogin == 1) {
} else {
exit('<script language="javascript">window.location.href="./login.php";</script>');
}
adminpermission('shop', 1);
$numrows = $DB->getColumn('SELECT count(*) from shua_class');

// 添加pid字段到分类表
$column_exists = $DB->getColumn('SHOW COLUMNS FROM shua_class LIKE \'pid\'');
if(!$column_exists){
	$DB->exec('ALTER TABLE shua_class ADD COLUMN pid int(11) unsigned NOT NULL DEFAULT \'0\' COMMENT \'父分类ID，0表示一级分类\' AFTER `zid`');
}

// 添加notice字段到分类表（如果不存在）用于存储二级分类提示语
$notice_column_exists = $DB->getColumn('SHOW COLUMNS FROM shua_class LIKE \'notice\'');
if(!$notice_column_exists){
	$DB->exec('ALTER TABLE shua_class ADD COLUMN notice text COMMENT \'分类提示语，前台选中时弹窗显示\' AFTER `blockpay`');
}

// 获取所有分类，包括一级和二级
$rs = $DB->query('SELECT * FROM shua_class WHERE 1 order by sort asc');
$classes = [];
$subClasses = [];
while ($res = $rs->fetch()) {
	if ($res['pid'] == 0) {
		$classes[] = $res; // 一级分类
	} else {
		$subClasses[$res['pid']][] = $res; // 二级分类，按父分类ID分组
	}
}

// 获取所有一级分类列表，用于添加二级分类时选择父分类
$parentClasses = $DB->query('SELECT cid, name FROM shua_class WHERE pid = 0 order by sort asc');
$parentClassOptions = '';
while ($parent = $parentClasses->fetch()) {
	$parentClassOptions .= "<option value='{$parent['cid']}'>{$parent['name']}</option>";
}
?>
<script>
var checkflag = "false";

function checkAll(field) {
	if (checkflag == "false") {
		$('input[name="checkbox[]"]').each(function() {
			$(this).prop('checked', true);
		});
		checkflag = "true";
	} else {
		$('input[name="checkbox[]"]').each(function() {
			$(this).prop('checked', false);
		});
		checkflag = "false";
	}
}

function check1(field) {
	if (checkflag == "false") {
		for (i = 0; i < field.length; i++) {
			field[i].checked = true;
		}
		checkflag = "true";
		return "false";
	} else {
		for (i = 0; i < field.length; i++) {
			field[i].checked = false;
		}
		checkflag = "false";
		return "true";
	}
}

function unselectall() {
	if(document.classlist.chkAll1.checked){
		document.classlist.chkAll1.checked = document.classlist.chkAll1.checked&0;
		checkflag = "false";
	}
}

function batchOperation() {
	var aid = $("select[name='aid']").val();
	var checkbox = $("input[name='checkbox[]']:checked");
	
	if(checkbox.length == 0){
		layer.msg('请至少选择一个分类', {icon: 2});
		return false;
	}
	
	if(aid == 3){
		layer.confirm('确定要删除选中的 ' + checkbox.length + ' 个分类吗？删除后将无法恢复！', {
			btn: ['确定', '取消'],
			icon: 3
		}, function(index) {
			layer.close(index);
			executeBatchOperation();
		});
	} else {
		executeBatchOperation();
	}
}

function executeBatchOperation() {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=batchOperation',
		data : $('#classlist').serialize(),
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error:function(data){
			layer.close(ii);
			layer.msg('服务器错误', {icon: 2});
			return false;
		}
	});
}

function addSubClass(parentId) {
	layer.prompt({
		formType: 0,
		title: '请输入子分类名称：',
		area: ['400px', '100px']
	}, function(name, index) {
		if (name && name.trim() != '') {
			var loading = layer.load(2);
			$.ajax({
				url: 'ajax_class.php?act=addClass',
				data: {name: name, pid: parentId},
				type: 'POST',
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					layer.close(index);
					if (data.code == 0) {
						layer.msg(data.msg, {icon: 1});
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						layer.msg(data.msg, {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.close(index);
					layer.msg('添加失败，请重试', {icon: 2});
				}
			});
		}
	});
}

function sort(cid, type) {
	var loading = layer.load(2);
	$.ajax({
		type: 'GET',
		url: './ajax_class.php?act=setClassSort&cid=' + cid + '&sort=' + type,
		data: {},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function editClass(cid) {
	layer.prompt({
		formType: 0,
		title: '请输入新的分类名称：',
		area: ['400px', '100px']
	}, function(name, index) {
		if (name && name.trim() != '') {
			if (name.indexOf('┗━ ') === 0) {
				name = name.substring(3);
			}
			var loading = layer.load(2);
			$.ajax({
				type: 'POST',
				url: './ajax_class.php?act=editClass&cid=' + cid,
				data: {
					name: name
				},
				dataType: 'json',
				success: function(data) {
					layer.close(loading);
					layer.close(index);
					if (data.code == 0) {
						layer.msg('修改成功', {icon: 1});
						setTimeout(function() {
							location.reload();
						}, 1000);
					} else {
						layer.msg(data.msg, {icon: 2});
					}
				},
				error: function() {
					layer.close(loading);
					layer.close(index);
					layer.msg('操作失败，请重试', {icon: 2});
				}
			});
		}
	});
}

function setActive(cid, type) {
	var loading = layer.load(2);
	$.ajax({
		type: 'GET',
		url: './ajax_class.php?act=setClass',
		data: {
			cid: cid,
			active: type
		},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg(data.msg, {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function delClass(cid) {
	layer.confirm('确定要删除该分类吗？删除后将无法恢复！', {
		btn: ['确定', '取消'],
		icon: 3
	}, function(index) {
		layer.close(index);
		var loading = layer.load(2);
		$.ajax({
			type: 'POST',
			url: './ajax_class.php?act=delClass&cid=' + cid,
			data: {},
			dataType: 'json',
			success: function(data) {
				layer.close(loading);
				if (data.code == 0) {
					layer.msg(data.msg, {icon: 1});
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					layer.msg(data.msg, {icon: 2});
				}
			},
			error: function() {
				layer.close(loading);
				layer.msg('操作失败，请重试', {icon: 2});
			}
		});
	});
}

function saveAll() {
	var sortData = {};
	var nameData = {};
	
	$('input[name^="sort"]').each(function() {
		var cid = $(this).attr('name').match(/\d+/)[0];
		sortData[cid] = $(this).val();
	});
	
	$('input[name^="name"]').each(function() {
		var cid = $(this).attr('name').match(/\d+/)[0];
		var name = $(this).val();
		if (name.indexOf('┗━ ') === 0) {
			name = name.substring(3);
		}
		nameData[cid] = name;
	});
	
	var loading = layer.load(2);
	$.ajax({
		type: 'POST',
		url: './ajax_class.php?act=editClassAll',
		data: {
			sort: sortData,
			name: nameData
		},
		dataType: 'json',
		success: function(data) {
			layer.close(loading);
			if (data.code == 0) {
				layer.msg('保存成功', {icon: 1});
				setTimeout(function() {
					location.reload();
				}, 1000);
			} else {
				layer.msg(data.msg, {icon: 2});
			}
		},
		error: function() {
			layer.close(loading);
			layer.msg('操作失败，请重试', {icon: 2});
		}
	});
}

function addClass() {
	var name = $('input[name="addname"]').val();
	if (name && name.trim() != '') {
		var loading = layer.load(2);
		$.ajax({
			type: 'POST',
			url: './ajax_class.php?act=addClass',
			data: {
				name: name,
				pid: 0
			},
			dataType: 'json',
			success: function(data) {
				layer.close(loading);
				if (data.code == 0) {
					layer.msg(data.msg, {icon: 1});
					setTimeout(function() {
						location.reload();
					}, 1000);
				} else {
					layer.msg(data.msg, {icon: 2});
				}
			},
			error: function() {
				layer.close(loading);
				layer.msg('添加失败，请重试', {icon: 2});
			}
		});
	} else {
		layer.msg('分类名称不能为空', {icon: 2});
	}
}

function setBlockPay(cid) {
	$.ajax({
		type : 'POST',
		url : 'ajax_class.php?act=getBlockPay',
		data : {cid:cid},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.open({
					area: ['360px'],
					title: '设置此分类商品禁用支付方式',
					content: '<div class="form-group"><div class="checkbox"><label><input type="checkbox" name="paytype" value="alipay" '+($.inArray('alipay',data.data)>-1?'checked':null)+'> 禁用支付宝</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="qqpay" '+($.inArray('qqpay',data.data)>-1?'checked':null)+'> 禁用QQ钱包</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="wxpay" '+($.inArray('wxpay',data.data)>-1?'checked':null)+'> 禁用微信支付</label></div><div class="checkbox"><label><input type="checkbox" name="paytype" value="rmb" '+($.inArray('rmb',data.data)>-1?'checked':null)+'> 禁用余额</label></div></div>',
					yes: function(){
						var paytype = [];
						$.each($("input[name='paytype']:checked"),function(){
							paytype.push($(this).val());
						});
						$.ajax({
							type : 'POST',
							url : 'ajax_class.php?act=setBlockPay',
							data : {cid:cid,paytype: paytype},
							dataType : 'json',
							success : function(data) {
								if(data.code == 0){
									layer.msg(data.msg, {icon:1});
								}else{
									layer.alert(data.msg);
								}
							},
							error:function(data){
								layer.msg('服务器错误');
								return false;
							}
						});
					}
				});
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

function setClass(cid) {
	layer.msg('设置不可售地区功能暂未实现', {icon: 2});
}

function setNotice(cid) {
	$.ajax({
		type: 'POST',
		url: './ajax_class.php?act=getNotice',
		data: {cid: cid},
		dataType: 'json',
		success: function(data) {
			var currentNotice = '';
			if(data.code == 0 && data.data) {
				currentNotice = data.data;
			}
			
			layer.prompt({
				formType: 2,
				title: '设置二级分类提示语（前台选中时弹窗显示）',
				area: ['500px', '250px'],
				value: currentNotice,
				placeholder: '请输入提示语内容，留空则不显示提示'
			}, function(text, index) {
				layer.close(index);
				var loading = layer.load(2);
				$.ajax({
					type: 'POST',
					url: './ajax_class.php?act=setNotice',
					data: {cid: cid, notice: text},
					dataType: 'json',
					success: function(res) {
						layer.close(loading);
						if(res.code == 0) {
							layer.msg('设置成功！', {icon: 1});
						} else {
							layer.msg(res.msg || '设置失败', {icon: 2});
						}
					},
					error: function() {
						layer.close(loading);
						layer.msg('请求失败，请重试', {icon: 2});
					}
				});
			});
		},
		error: function() {
			layer.msg('获取数据失败', {icon: 2});
		}
	});
}

// 专门处理下拉菜单的防冲突逻辑
$(document).ready(function() {
    $(document).on('click', '.custom-dropdown-toggle', function(e) {
        e.stopPropagation();
        var $menu = $(this).siblings('.dropdown-menu');
        $('.dropdown-menu').not($menu).addClass('hidden'); // 关闭其他菜单
        $menu.toggleClass('hidden'); // 切换当前菜单状态
    });
    
    // 点击空白处关闭菜单
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').addClass('hidden');
        }
    });
});
</script>

<form name="classlist" id="classlist">
  <!-- 响应式表格容器 -->
  <div class="w-full overflow-x-auto rounded-t-xl border-x border-t border-slate-100 shadow-sm bg-white scrollbar-thin scrollbar-thumb-slate-200">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
      <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
        <tr>
            <th class="px-4 py-3.5 font-semibold text-center w-10"><input type="checkbox" name="chkAll" id="chkAll" onClick="checkAll(this)" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer"></th>
            <th class="px-4 py-3.5 font-semibold text-center">排序操作</th>
            <th class="px-4 py-3.5 font-semibold text-left min-w-[200px]">名称（<?php echo $numrows;?>）</th>
            <th class="px-4 py-3.5 font-semibold text-center">分类类型</th>
            <th class="px-4 py-3.5 font-semibold text-center">操作</th>
        </tr>
      </thead>
      <tbody id="classlisttbody" class="divide-y divide-slate-50">
<?php 
foreach ($classes as $res) {
	$activeBtn = $res['active'] == 1 ? '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="setActive('.$res['cid'].',0)">显示</span>' : '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="setActive('.$res['cid'].',1)">隐藏</span>';
	$classBlockBtn = $conf['classblock'] > 0 ? '<li><a href="javascript:setClass('.$res['cid'].')" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">设置不可售地区</a></li>' : '';
	
	 echo '<tr data-cid="' . $res['cid'] . '" class="hover:bg-brand-50/40 transition-colors duration-200">
	<td class="px-4 py-3 text-center"><input type="checkbox" name="checkbox[]" id="list1" value="' . $res['cid'] . '" onClick="unselectall()" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer"></td>
	<td class="px-4 py-3 text-center">
		<div class="inline-flex items-center gap-1">
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到顶部" onclick="sort('.$res['cid'].',0)"><i class="fa fa-long-arrow-up"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到上一行" onclick="sort('.$res['cid'].',1)"><i class="fa fa-chevron-circle-up"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到下一行" onclick="sort('.$res['cid'].',2)"><i class="fa fa-chevron-circle-down"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到底部" onclick="sort('.$res['cid'].',3)"><i class="fa fa-long-arrow-down"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-slate-50 text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="拖动排序"><i class="fa fa-sort"></i></a>
        </div>
		<input type="hidden" name="sort['.$res['cid'].']" value="' . $res['sort'] . '">
	</td>
	<td class="px-4 py-3"><input type="text" class="w-full h-9 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="name['.$res['cid'].']" value="' . $res['name'] . '" placeholder="分类名称" required></td>
	<td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-600 border border-brand-100">一级分类</span></td>
	<td class="px-4 py-3 text-center whitespace-nowrap">
		<div class="inline-flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="editClass('.$res['cid'].')">修改</span>
            ' . $activeBtn . '
            <a href="./shoplist.php?cid='.$res['cid'].'" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white font-medium text-xs transition-colors shadow-sm">商品</a>
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="delClass('.$res['cid'].')">删除</span>
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="addSubClass('.$res['cid'].')">添加子分类</span>
            <div class="relative inline-block text-left dropdown">
                <button type="button" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition-colors shadow-sm custom-dropdown-toggle">更多 <span class="caret ml-1"></span></button>
                <ul class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-1 z-50 hidden">
                    <li><a href="./list.php?cid='.$res['cid'].'" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">查看该分类的订单</a></li>
                    <li><a href="javascript:setBlockPay('.$res['cid'].')" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">设置禁用支付方式</a></li>
                    ' . $classBlockBtn . '
                </ul>
            </div>
        </div>
	</td>
  </tr>';
  
  if (isset($subClasses[$res['cid']])) {
    foreach ($subClasses[$res['cid']] as $subRes) {
	    $subActiveBtn = $subRes['active'] == 1 ? '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="setActive('.$subRes['cid'].',0)">显示</span>' : '<span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="setActive('.$subRes['cid'].',1)">隐藏</span>';
	    $subClassBlockBtn = $conf['classblock'] > 0 ? '<li><a href="javascript:setClass('.$subRes['cid'].')" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">设置不可售地区</a></li>' : '';
	    
	    echo '<tr data-cid="' . $subRes['cid'] . '" class="bg-slate-50/60 hover:bg-brand-50/40 transition-colors duration-200">
	<td class="px-4 py-3 text-center"><input type="checkbox" name="checkbox[]" id="list1" value="' . $subRes['cid'] . '" onClick="unselectall()" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer"></td>
	<td class="px-4 py-3 text-center pl-8">
        <div class="inline-flex items-center gap-1">
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到顶部" onclick="sort('.$subRes['cid'].',0)"><i class="fa fa-long-arrow-up"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到上一行" onclick="sort('.$subRes['cid'].',1)"><i class="fa fa-chevron-circle-up"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到下一行" onclick="sort('.$subRes['cid'].',2)"><i class="fa fa-chevron-circle-down"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="移到底部" onclick="sort('.$subRes['cid'].',3)"><i class="fa fa-long-arrow-down"></i></a>
            <a class="inline-flex items-center justify-center w-7 h-7 rounded bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 transition-colors text-xs shadow-sm cursor-pointer" title="拖动排序"><i class="fa fa-sort"></i></a>
        </div>
		<input type="hidden" name="sort['.$subRes['cid'].']" value="' . $subRes['sort'] . '">
	</td>
	<td class="px-4 py-3 pl-8"><input type="text" class="w-full h-9 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="name['.$subRes['cid'].']" value="┗━ ' . $subRes['name'] . '" placeholder="子分类名称" required></td>
	<td class="px-4 py-3 text-center"><span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100">二级分类</span></td>
	<td class="px-4 py-3 text-center whitespace-nowrap">
		<div class="inline-flex items-center gap-1.5">
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="editClass('.$subRes['cid'].')">修改</span>
            ' . $subActiveBtn . '
            <a href="./shoplist.php?cid='.$subRes['cid'].'" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white font-medium text-xs transition-colors shadow-sm">商品</a>
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm cursor-pointer" onclick="delClass('.$subRes['cid'].')">删除</span>
            <div class="relative inline-block text-left dropdown">
                <button type="button" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 font-medium text-xs transition-colors shadow-sm custom-dropdown-toggle">更多 <span class="caret ml-1"></span></button>
                <ul class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-xl py-1 z-50 hidden">
                    <li><a href="./list.php?cid='.$subRes['cid'].'" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">查看该分类的订单</a></li>
                    <li><a href="javascript:setBlockPay('.$subRes['cid'].')" class="block px-4 py-2 hover:bg-slate-50 text-slate-700">设置禁用支付方式</a></li>
                    <li><a href="javascript:setNotice('.$subRes['cid'].')" class="block px-4 py-2 hover:bg-slate-50 text-emerald-600 font-semibold">设置提示语</a></li>
                    ' . $subClassBlockBtn . '
                </ul>
            </div>
        </div>
	</td>
      </tr>';
    }
  }
}?>
      </tbody>
    </table>
  </div>
  
  <!-- 底部批量与添加操作区 -->
  <div class="flex flex-col sm:flex-row flex-wrap items-center justify-between gap-4 p-4 sm:p-5 bg-slate-50/80 border border-t-0 border-slate-100 rounded-b-xl shadow-sm">
      <div class="flex items-center gap-3 w-full sm:w-auto">
          <span class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white border border-slate-200 shadow-sm cursor-pointer hover:bg-brand-50 hover:text-brand-600 transition-colors" onclick="saveAll()">
              <i class="fa fa-floppy-o mr-2 text-brand-500"></i> 保存全部
          </span>
      </div>
      
      <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
          <input type="text" class="h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="addname" placeholder="分类名称" required>
          <span class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-all duration-200 cursor-pointer" onclick="addClass()">
              <span class="glyphicon glyphicon-plus mr-1"></span> 添加分类
          </span>
          <a href="./classlist.php?my=classimg" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200">
              <i class="fa fa-picture-o mr-1.5"></i> 修改分类图片
          </a>
      </div>
      
      <div class="flex items-center gap-2 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
          <label class="inline-flex items-center cursor-pointer m-0">
              <input name="chkAll1" type="checkbox" id="chkAll1" onClick="this.value=check1(this.form.list1)" value="checkbox" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
              <span class="ml-2 text-sm font-semibold text-slate-700">全选</span>
          </label>
          <select name="aid" class="h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer">
              <option selected>批量操作</option>
              <option value="1">&gt;改为显示</option>
              <option value="2">&gt;改为隐藏</option>
              <option value="3">&gt;删除选中</option>
          </select>
          <button type="button" onclick="batchOperation()" class="inline-flex items-center justify-center h-10 px-5 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200">
              执行
          </button>
      </div>
  </div>
</form>
