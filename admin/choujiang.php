<?php
include "../includes/common.php";
$title = "抽奖配置";
include "head.php";
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$fl = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = '<option value="0">请选择分类</option>';
$shua_class[0] = '默认分类';
while ($res = $fl->fetch()) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= '<option value="' . $res["cid"] . '">' . $res["name"] . '</option>';
}
$select2 = '<option value="0">请选择商品</option>';
?>

<!-- 模态框 1：添加奖项 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="myModal" role="dialog" aria-hidden="true">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h4 class="text-base font-bold text-slate-800 m-0" id="myModalLabel"><i class="fa fa-gift text-brand-500 mr-2"></i>添加奖项</h4>
            <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#myModal">
                <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">奖品名称</label>
                <input type="text" id="name" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请填写奖项名称">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">中奖几率 (百分比)</label>
                <input type="number" id="rate" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="例如：10 代表 10%">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">对应分类</label>
                <select id="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"><?php echo $select;?></select>
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">对应商品</label>
                <select id="tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"><?php echo $select2;?></select>
            </div>
            <div class="pt-4">
                <a class="inline-flex items-center justify-center w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 cursor-pointer" id="submit">确定添加</a>
            </div>
        </div>
    </div>
</div>

<!-- 模态框 2：编辑奖项 -->
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="edit_modal" role="dialog" aria-hidden="true">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h4 class="text-base font-bold text-slate-800 m-0" id="edit_title"><i class="fa fa-pencil-square text-sky-500 mr-2"></i>编辑奖项</h4>
            <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#edit_modal">
                <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="edit_val">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">奖品名称</label>
                <input type="text" id="edit_name" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请填写奖项名称">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">中奖几率 (百分比)</label>
                <input type="number" id="edit_rate" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="例如：10 代表 10%">
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">对应分类</label>
                <select id="edit_cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default=""><?php echo $select;?></select>
            </div>
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">对应商品</label>
                <select id="edit_tid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors"><?php echo $select2;?></select>
            </div>
            <div class="pt-4">
                <a class="inline-flex items-center justify-center w-full h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-all duration-200 cursor-pointer" onclick="edit_ok($('#edit_val').val())">确定修改</a>
            </div>
        </div>
    </div>
</div>

<div class="w-full max-w-[1200px] mx-auto space-y-6" id="main">
<?php adminpermission('set', 1);?>

    <!-- 配置区域 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-cogs text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">抽奖配置</h3>
        </div>
        <div class="p-6">
            <div class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <label class="text-sm font-semibold text-slate-700 md:text-right">是否开启抽奖</label>
                    <div class="md:col-span-2">
                        <select id="gift_open" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $conf["gift_open"]?>">
                            <option value="0">0_关闭</option>
                            <option value="1">1_开启</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <label class="text-sm font-semibold text-slate-700 md:text-right">每日每人抽奖次数</label>
                    <div class="md:col-span-2">
                        <input class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" type="number" id="cishu" value="<?php echo $conf["cjcishu"];?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <label class="text-sm font-semibold text-slate-700 md:text-right">抽奖上限提示信息</label>
                    <div class="md:col-span-2">
                        <input class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" type="text" id="cjmsg" value="<?php echo $conf["cjmsg"];?>">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <label class="text-sm font-semibold text-slate-700 md:text-right">抽奖付费金额</label>
                    <div class="md:col-span-2">
                        <input class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" type="text" id="cjmoney" value="<?php echo $conf["cjmoney"];?>" placeholder="填0则不需要付费">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                    <label class="text-sm font-semibold text-slate-700 md:text-right">是否显示中奖记录</label>
                    <div class="md:col-span-2">
                        <select id="gift_log" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $conf["gift_log"]?>">
                            <option value="0">0_关闭</option>
                            <option value="1">1_开启</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                    <div class="md:col-start-2 md:col-span-2">
                        <a class="inline-flex items-center justify-center w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 cursor-pointer" id="cishu_submit"><i class="fa fa-floppy-o mr-2"></i>保存全局配置</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 列表区域 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-50 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-gift text-brand-500 mr-2 text-lg"></i>抽奖商品列表</h3>
            <div class="flex gap-2 w-full sm:w-auto">
                <a class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-500 text-white font-medium text-sm hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer open-modal" data-target="#myModal">
                    <i class="fa fa-plus-circle mr-1.5"></i>添加奖项
                </a>
                <a class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 h-10 rounded-xl bg-amber-500 text-white font-medium text-sm hover:bg-amber-600 shadow-sm transition-colors" href="choujiang_list.php">
                    <i class="fa fa-history mr-1.5"></i>中奖记录
                </a>
            </div>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap" id="tab">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">奖品名称</th>
                        <th class="px-6 py-3.5 font-semibold">对应商品</th>
                        <th class="px-6 py-3.5 font-semibold text-center">中奖几率</th>
                        <th class="px-6 py-3.5 font-semibold text-center">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php
                $rs = $DB->query("SELECT a.*,(SELECT b.name FROM pre_tools as b WHERE a.tid=b.tid) as shopname FROM pre_gift as a");
                $isEmpty = true;
                while ($row = $rs->fetch()) {
                    $isEmpty = false;
                    echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">';
                    echo '<td class="px-6 py-4 font-bold text-brand-600">'.$row["name"].'</td>';
                    echo '<td class="px-6 py-4 font-medium text-slate-700 max-w-[250px] truncate" title="'.$row["shopname"].'">'.$row["shopname"].'</td>';
                    echo '<td class="px-6 py-4 text-center font-bold text-orange-500">'.$row["rate"].'%</td>';
                    echo '<td class="px-6 py-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center gap-1.5">
                                <a href="javascript:void(0)" onclick="editmember(\'' . $row["id"] . '\')" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm">编辑</a>
                                <a href="javascript:void(0)" class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm" onclick="del_member(\'' . $row["id"] . '\')">删除</a>
                            </div>
                          </td></tr>';
                }
                
                if($isEmpty) {
                    echo '<tr><td colspan="4" class="px-6 py-12 text-center text-slate-400"><i class="fa fa-gift text-4xl mb-2 text-slate-300 block"></i>当前未配置任何奖项</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script>
// 控制原生模态框的显示与隐藏逻辑
function openModal(modalId) {
    var modal = document.querySelector(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    setTimeout(function() {
        modal.classList.remove('opacity-0');
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeModal(modalId) {
    var modal = document.querySelector(modalId);
    if (!modal) return;
    modal.classList.add('opacity-0');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(function() {
        modal.classList.add('hidden');
    }, 300);
}

document.addEventListener('DOMContentLoaded', function() {
    var openBtns = document.querySelectorAll('.open-modal');
    openBtns.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            openModal(this.getAttribute('data-target'));
        });
    });
    
    var closeBtns = document.querySelectorAll('.close-modal');
    closeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            closeModal(this.getAttribute('data-target'));
        });
    });
    
    // 点击背景关闭 (支持两个模态框)
    document.querySelectorAll('[role="dialog"]').forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal('#' + this.id);
            }
        });
    });
});

var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}

$("#cishu_submit").click(function(){
	ii=layer.load(1,{shade:0.3});
	$.ajax({
		type:"get",
		url:"ajax.php?act=cishu&cishu="+$("#cishu").val()+"&gift_open="+$("#gift_open").val()+"&gift_log="+$("#gift_log").val()+"&cjmsg="+$("#cjmsg").val()+"&cjmoney="+$("#cjmoney").val(),
		dataType:"json",
		success:function(cishu){
			layer.close(ii);
			if(cishu.code==0){
				layer.msg('配置保存成功',{icon:1,time:1000,shade:0.3});
			}else{
				layer.alert(cishu.msg);
			}
		}
	});
});

function editmember(id){
	ii=layer.load(1);
	$.ajax({
		type:"post",
		url:"ajax.php?act=edit_cj",
		data:{
			id:id
		},
		dataType:"json",
		success:function(edit){
			layer.close(ii);
			if(edit.code==0){
				$("#edit_val").val(edit.id);
				$("#edit_name").val(edit.name);
				$("#edit_rate").val(edit.rate);
				$("#edit_cid").val(edit.cid);
				$("#edit_tid").attr('default',edit.tid);
				
                openModal('#edit_modal');
				$("#edit_cid").change();
			}else{
				layer.alert(edit.msg);
			}
		}
	});
}

function del_member(id){
    layer.confirm('确定要删除该奖项吗？', {icon: 3, title:'系统提示'}, function(index){
        layer.close(index);
    	ii=layer.load(1);
    	$.ajax({
    		type:"post",
    		url:"ajax.php?act=del_member",
    		data:{
    			id:id
    		},
    		dataType:"json",
    		success:function(del){
    			layer.close(ii);
    			if(del.code==0){
    				layer.msg(del.msg,{icon:1,time:1500,shade:0.3});
    				$.ajax({
    					type:"get",
    					url:"choujiang.php",
    					dataType:"html",
    					success:function(html){
    						$("#tab").html($(html).find('#tab').html());
    					}
    				});
    			}else{
    				layer.alert(del.msg);
    			}
    		}
    	});
    });
}

function edit_ok(id){
	ii=layer.load(1);
	var name=$("#edit_name").val();
	var cid=$("#edit_cid").val();
	var tid=$("#edit_tid").val();
	var rate=$("#edit_rate").val();
	if(!name||tid==0 || rate==''){
		layer.msg("请输入完整信息！",{icon:2,time:1000,shade:0.3});
		layer.close(ii);
		return false;
	}
	$.ajax({
		type:"post",
		url:"ajax.php?act=edit_cj_ok",
		data:{
			id:id,name:name,tid:tid,rate:rate
		},
		dataType:"json",
		success:function(add){
            closeModal('#edit_modal');
			if(add.code==0){
				layer.close(ii);
				layer.msg(add.msg,{icon:1,shade:0.3,time:1500});
				window.location.reload();
			}else{
				layer.close(ii);
				layer.alert(add.msg);
			}
		}
	});
}

$("#cid").change(function () {
	var cid = $(this).val();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$("#tid").empty();
	$("#tid").append('<option value="0">请选择商品</option>');
	$.ajax({
		type : "GET",
		url : "../ajax.php?act=gettool&cid="+cid,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var num = 0;
				$.each(data.data, function (i, res) {
					$("#tid").append('<option value="'+res.tid+'">'+res.name+'</option>');
					num++;
				});
				$("#tid").val(0);
				if(num==0 && cid!=0)layer.alert('该分类下没有可用商品');
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('服务器网络错误');
			return false;
		}
	});
});

$("#edit_cid").change(function () {
	var cid = $(this).val();
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$("#edit_tid").empty();
	$("#edit_tid").append('<option value="0">请选择商品</option>');
	$.ajax({
		type : "GET",
		url : "../ajax.php?act=gettool&cid="+cid,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var num = 0;
				$.each(data.data, function (i, res) {
					$("#edit_tid").append('<option value="'+res.tid+'">'+res.name+'</option>');
					num++;
				});
				$("#edit_tid").val($("#edit_tid").attr('default'));
				if(num==0 && cid!=0)layer.alert('该分类下没有可用商品');
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
            layer.close(ii);
			layer.msg('服务器网络错误');
			return false;
		}
	});
});

window.onload=$("#cid").change();

$("#submit").click(function(){
	ii=layer.load(1);
	var name=$("#name").val();
	var cid=$("#cid").val();
	var tid=$("#tid").val();
	var rate=$("#rate").val();
	if(!name){
		layer.msg("请输入完整奖品名称！",{icon:2,time:1000,shade:0.3});
		layer.close(ii);
		return false;
	}
	$.ajax({
		type:"post",
		url:"ajax.php?act=add_member",
		data:{
			name:name,tid:tid,rate:rate
		},
		dataType:"json",
		success:function(add){
			if(add.code==0){
				closeModal('#myModal');
				layer.close(ii);
				layer.msg(add.msg,{icon:1,shade:0.3,time:1500});
				$.ajax({
					type:"get",
					url:"choujiang.php",
					dataType:"html",
					success:function(html){
						$("#tab").html($(html).find('#tab').html());
                        
                        // 重置表单以便下次添加
                        $("#name").val('');
                        $("#rate").val('');
					}
				});
			}else{
				layer.close(ii);
				layer.alert(add.msg);
			}
		}
	});
});
</script>
</body>
</html>
