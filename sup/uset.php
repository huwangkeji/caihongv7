<?php

require "../includes/common.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
$title = "网站设置";
include "head.php";
if ($conf["sup_cost2"] <= 0) {
	$conf["sup_cost2"] = $conf["sup_price2"];
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php 
$mod = isset($_GET["mod"]) ? $_GET["mod"] : null;

// ----------------------------------------------------
// 逻辑处理：资料更新
// ----------------------------------------------------
if ($mod == "user_n") {
	if (!checkRefererHost()) {
		exit;
	}
	$qq = daddslashes(htmlspecialchars(strip_tags($_POST["qq"])));
	$pay_type = daddslashes(intval($_POST["pay_type"]));
	$pay_account = daddslashes(htmlspecialchars(strip_tags($_POST["pay_account"])));
	$pay_name = daddslashes(htmlspecialchars(strip_tags($_POST["pay_name"])));
	$pwd = daddslashes(htmlspecialchars(strip_tags($_POST["pwd"])));
	if (!empty($pwd) && !preg_match("/^[a-zA-Z0-9\\_\\!\\@\\#\\\$~\\%\\^\\&\\*.,]+\$/", $pwd)) {
		exit("<script language='javascript'>layer.alert('密码只能为英文与数字！', {icon:2}, function(){history.go(-1);});</script>");
	} elseif (!preg_match("/^[0-9]{5,11}+\$/", $qq)) {
		exit("<script language='javascript'>layer.alert('QQ格式不正确！', {icon:2}, function(){history.go(-1);});</script>");
	} else {
		$DB->exec("UPDATE pre_supplier SET qq=:qq,pay_type=:pay_type,pay_account=:pay_account,pay_name=:pay_name WHERE sid=:sid", array(":qq" => $qq, ":pay_type" => $pay_type, ":pay_account" => $pay_account, ":pay_name" => $pay_name, ":sid" => $suprow["sid"]));
		if (!empty($pwd)) {
			$DB->exec("update pre_supplier set pwd=:pwd where sid=:sid", array(":pwd" => $pwd, ":sid" => $suprow["sid"]));
		}
		exit("<script language='javascript'>layer.msg('资料更新成功！', {icon:1, time:2000}, function(){history.go(-1);});</script>");
	}

// ----------------------------------------------------
// 视图：资料设置主界面
// ----------------------------------------------------
} elseif ($mod == "user") {
	$url = "https://api.fcypay.com/";
	$m = md5(rand(1000000, 9999999) . date("YmdHis") . uniqid());
	$code_url = $url . "get_openid_qrcode?mark=" . $m;
	$cron_url = $url . "get_openid_status?mark=" . $m;
?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 左侧：用户资料设置 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center shrink-0">
                <i class="fa fa-user-circle text-indigo-500 mr-2 text-xl"></i>
                <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">用户资料设置</h3>
            </div>
            
            <div class="p-6 flex-1 flex flex-col">
                <form action="./uset.php?mod=user_n" method="post" role="form" class="space-y-5">
                    
                    <?php if ($conf["login_qq"] == 1) { ?>  
                    <div class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-xl mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center text-sky-500 shadow-sm border border-sky-200">
                                <i class="fa fa-qq text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 m-0">QQ快捷登录</h4>
                                <?php if ($suprow["qq_openid"]) { ?>
                                    <span class="text-xs font-bold text-emerald-500 flex items-center mt-1"><i class="fa fa-check-circle mr-1"></i>已绑定</span>
                                <?php } else { ?>
                                    <span class="text-xs font-bold text-rose-500 flex items-center mt-1"><i class="fa fa-exclamation-circle mr-1"></i>未绑定</span>
                                <?php } ?>
                            </div>
                        </div>
                        <div>
                            <?php if ($suprow["qq_openid"]) { ?>
                                <a href="javascript:unbind('qq')" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-colors shadow-sm">解绑</a>
                            <?php } else { ?>
                                <a href="javascript:connect('qq')" class="px-3 py-1.5 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 font-bold text-xs hover:bg-sky-500 hover:text-white transition-colors shadow-sm">立即绑定</a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">登录用户名</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-user"></i></div>
                            <input type="text" value="<?php echo $suprow["user"];?>" class="w-full h-12 pl-11 pr-4 text-sm font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed shadow-inner" disabled/>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">联系QQ <span class="text-rose-500">*</span></label>
                        <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                            <div class="pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-qq"></i></div>
                            <input type="text" name="qq" value="<?php echo $suprow["qq"];?>" class="flex-1 h-12 pl-3 pr-4 text-sm font-mono font-bold border-0 focus:ring-0 placeholder:font-normal placeholder:text-slate-400" placeholder="用于联系与找回密码" required/>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">提现方式</label>
                        <select name="pay_type" default="<?php echo $suprow["pay_type"];?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                            <?php if ($conf["sup_tixian_alipay"] == 1) { ?><option value="0">支付宝</option><?php } ?>
                            <?php if ($conf["sup_tixian_wx"] == 1) { ?><option value="1">微信</option><?php } ?>
                            <?php if ($conf["sup_tixian_qq"] == 1) { ?><option value="2">QQ钱包</option><?php } ?>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">提现账号</label>
                        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                            <div class="pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-credit-card"></i></div>
                            <input type="text" name="pay_account" value="<?php echo $suprow["pay_account"];?>" class="flex-1 h-12 pl-3 pr-4 text-sm font-mono font-bold border-0 focus:ring-0"/>
                            <a href="javascript:getopenid()" id="getopenid" class="px-4 border-l border-slate-200 bg-emerald-50 text-emerald-600 font-bold text-sm hover:bg-emerald-100 transition-colors flex items-center shrink-0" style="display:none"><i class="fa fa-qrcode mr-1.5"></i>自动获取</a>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">提现真实姓名</label>
                        <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                            <div class="pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-id-card-o"></i></div>
                            <input type="text" name="pay_name" value="<?php echo $suprow["pay_name"];?>" class="flex-1 h-12 pl-3 pr-4 text-sm font-bold border-0 focus:ring-0"/>
                        </div>
                    </div>

                    <?php if (substr($suprow["user"], 0, 3) != "qq_") { ?>
                    <div class="space-y-1.5 pt-4 border-t border-slate-100">
                        <label class="text-sm font-semibold text-slate-700 ml-1">重置密码 (可选)</label>
                        <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                            <div class="pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-unlock-alt"></i></div>
                            <input type="text" name="pwd" value="" class="flex-1 h-12 pl-3 pr-4 text-sm font-bold border-0 focus:ring-0 placeholder:font-normal placeholder:text-slate-400" placeholder="不修改请留空"/>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <div class="pt-4 mt-auto">
                        <button type="submit" name="submit" class="w-full h-14 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all">保存修改</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 右侧：附属设置区 (密码设置/或者占位) -->
        <div class="flex flex-col gap-6">
            <?php if (substr($suprow["user"], 0, 3) == "qq_") { ?>
            <!-- QQ直接登录后的补全表单 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center shrink-0">
                    <i class="fa fa-key text-amber-500 mr-2 text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">配置独立登录凭证</h3>
                </div>
                <div class="p-6">
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6 flex items-start text-amber-700 font-medium text-sm leading-relaxed shadow-inner">
                        <i class="fa fa-info-circle text-amber-500 mr-2 mt-0.5 text-lg"></i>
                        提示：设置独立的用户名与密码后，可使用账密直接登录，该操作不会影响已有的QQ快捷登录通道。
                    </div>
                    
                    <form onsubmit="return setpwd()" method="post" role="form" class="space-y-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">登录用户名 <span class="text-rose-500">*</span></label>
                            <input type="text" name="user" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="输入登录用户名"/>
                            <p class="text-xs text-rose-500 mt-1.5 ml-1 font-medium"><i class="fa fa-exclamation-triangle mr-1"></i>注意：登录用户名一经设置将永久绑定，无法修改。</p>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">登录密码 <span class="text-rose-500">*</span></label>
                            <input type="text" name="pwd" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="输入6位以上的复杂密码"/>
                        </div>
                        
                        <div class="pt-2">
                            <button type="submit" name="submit" class="w-full h-12 rounded-xl bg-amber-500 text-white font-bold text-sm shadow-md shadow-amber-500/20 hover:bg-amber-600 active:scale-[0.98] transition-all">确立凭证</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php } else { ?>
            <!-- 若已补全或无需补全，展示提示或预留广告位 -->
            <div class="bg-gradient-to-br from-indigo-500 to-sky-500 rounded-2xl shadow-md border border-indigo-400 overflow-hidden relative h-48 flex items-center justify-center p-6 text-center">
                <div class="absolute -right-10 -bottom-10 opacity-10"><i class="fa fa-shield text-9xl"></i></div>
                <div class="relative z-10 text-white">
                    <i class="fa fa-check-square-o text-4xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-bold m-0 tracking-tight">安全防护运作中</h3>
                    <p class="text-indigo-100 text-sm mt-1">您的账户由系统安全内核实时守护</p>
                </div>
            </div>
            <?php } ?>
        </div>
        
    </div>

<?php 
// ----------------------------------------------------
// 视图与逻辑：收款图上传
// ----------------------------------------------------
} elseif ($mod == "skimg") {
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center shrink-0">
            <i class="fa fa-qrcode text-emerald-500 mr-2 text-xl"></i>
            <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">提现收款二维码设置</h3>
        </div>
        
        <div class="p-6">
            <?php 
            // 上传处理逻辑
            if (isset($_POST["s"]) && $_POST["s"] == 1) {
                if (!checkRefererHost()) {
                    exit;
                }
                $allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp');
                $file_type = $_FILES["shoukuan"]["type"];
                $file_ext = strtolower(pathinfo($_FILES["shoukuan"]["name"], PATHINFO_EXTENSION));
                $allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp');
                $max_size = 2 * 1024 * 1024;
                
                if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
                    echo "<div class='bg-rose-50 border border-rose-100 text-rose-600 p-4 rounded-xl mb-6 font-bold text-sm text-center'><i class='fa fa-times-circle mr-1.5'></i>只允许上传 JPG, PNG, GIF, WEBP, BMP 格式的图片文件！</div>";
                } elseif ($_FILES["shoukuan"]["size"] > $max_size) {
                    echo "<div class='bg-rose-50 border border-rose-100 text-rose-600 p-4 rounded-xl mb-6 font-bold text-sm text-center'><i class='fa fa-times-circle mr-1.5'></i>文件大小不能超过2MB！</div>";
                } else {
                    $image_info = getimagesize($_FILES["shoukuan"]["tmp_name"]);
                    if (!$image_info) {
                        echo "<div class='bg-rose-50 border border-rose-100 text-rose-600 p-4 rounded-xl mb-6 font-bold text-sm text-center'><i class='fa fa-times-circle mr-1.5'></i>请上传真实的图片文件！</div>";
                    } else {
                        copy($_FILES["shoukuan"]["tmp_name"], ROOT . "assets/img/skimg/sk_sup" . $suprow["sid"] . ".png");
                        echo "<div class='bg-emerald-50 border border-emerald-100 text-emerald-600 p-4 rounded-xl mb-6 font-bold text-sm text-center'>
                                <i class='fa fa-check-circle text-lg mb-1 block'></i>上传成功！<br>
                                <span class='text-xs font-normal opacity-80'>(若图片未改变，请按 Ctrl+F5 刷新浏览器缓存)</span>
                              </div>";
                    }
                }
            }
            
            // 展示逻辑
            if (file_exists(ROOT . "assets/img/skimg/sk_sup" . $suprow["sid"] . ".png")) {
                $logo = "../assets/img/skimg/sk_sup" . $suprow["sid"] . ".png?" . time(); // 加时间戳防止缓存
            } else {
                $logo = "../assets/img/skimg/sk.png";
            }
            ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 items-center">
                
                <!-- 上传表单 -->
                <div>
                    <form action="uset.php?mod=skimg" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="s" value="1" />
                        
                        <div class="relative group cursor-pointer">
                            <input type="file" name="shoukuan" id="shoukuan" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required accept="image/*" onchange="document.getElementById('fileName').innerText = this.files[0] ? this.files[0].name : '重新选择图片'">
                            <div class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 flex flex-col items-center justify-center py-10 px-4 group-hover:border-emerald-500 group-hover:bg-emerald-50/50 transition-colors">
                                <i class="fa fa-cloud-upload text-4xl text-slate-400 group-hover:text-emerald-500 transition-colors mb-3"></i>
                                <span id="fileName" class="text-sm font-bold text-slate-600 group-hover:text-emerald-700 text-center truncate w-full px-2">点击或拖拽选择图片</span>
                                <span class="text-xs text-slate-400 mt-1">支持 JPG/PNG/WEBP，最大 2MB</span>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full h-12 rounded-xl bg-emerald-500 text-white font-bold text-[15px] shadow-md shadow-emerald-500/20 hover:bg-emerald-600 active:scale-[0.98] transition-all">确认上传并覆盖</button>
                    </form>
                </div>
                
                <!-- 预览区 -->
                <div class="flex flex-col items-center justify-center border-t sm:border-t-0 sm:border-l border-slate-100 pt-6 sm:pt-0 sm:pl-8">
                    <p class="text-sm font-bold text-slate-500 mb-4 tracking-widest uppercase">当前展示快照</p>
                    <div class="p-2 bg-white rounded-2xl shadow-sm border border-slate-200">
                        <img src="<?php echo $logo; ?>" class="w-48 h-auto max-h-[300px] object-contain rounded-xl">
                    </div>
                </div>
                
            </div>
            
            <div class="mt-8 text-center border-t border-slate-100 pt-4">
                <a href="uset.php?mod=user" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors"><i class="fa fa-angle-left mr-1"></i>返回用户资料设置</a>
            </div>
            
        </div>
    </div>
<?php 
}
?>

</div>

<?php 
include "./foot.php";
?>

<script src="<?php echo $cdnpublic;?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

<?php 
if ($mod == "user") {
?>
<script>
$(document).ready(function(){
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
    	$(items[i]).val($(items[i]).attr("default")||0);
    }
    
    $("select[name='pay_type']").change(function(){
    	if($(this).val() == 1){
    		$("#getopenid").show();
    		$("input[name=pay_account]").attr("readOnly","readOnly").addClass("bg-slate-50 text-slate-500");
    	}else{
    		$("#getopenid").hide();
    		$("input[name=pay_account]").removeAttr("readOnly").removeClass("bg-slate-50 text-slate-500");
    	}
    });
    
    $("select[name='pay_type']").change();
});

<?php if ($conf["sup_daifu"] == 1) { ?>
var getopenid = function () {
    var open = layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800 text-center"><i class="fa fa-weixin text-emerald-500 mr-2"></i>微信授权提取验证</div>',
        skin: 'layui-layer-rim rounded-2xl',
        area: ['300px', 'auto'],
        content: '<div class="p-6 flex flex-col items-center justify-center"><div id="qrcode" class="p-2 bg-white border border-slate-200 shadow-sm rounded-xl mb-4"></div><p class="text-sm font-bold text-slate-600">请使用微信扫一扫以确认身份</p></div>',
        cancel: function(index, layero){ 
            layer.close(open);
            window.clearInterval(cron); 
        },
        success: function(){ 
			var code_url = '<?php echo $code_url;?>';
			$('#qrcode').qrcode({
				text: code_url,
				width: 200,
				height: 200,
				foreground: "#0f172a", // slate-900
				background: "#ffffff",
				typeNumber: -1
			});
        }
    });
    
    var cron = setInterval(function(){
        $.ajax({
            type: "GET",
            url: '<?php echo $cron_url;?>'+'&r='+Math.random(),
            dataType: "json",
            success: function(data){
                if (data.code) {
                    $("input[name=pay_account]").val(data.data);
                    layer.close(open);
                    window.clearInterval(cron); 
                    layer.msg('账号提取成功！', {icon: 1});
                }
            }
        });
    }, 3000);
}
<?php } ?>

function setpwd(){
	var user = $("input[name='user']").val();
	var pwd = $("input[name='pwd']").val();
	if(user=='' || pwd==''){
        layer.msg('请确保每项不能为空！', {icon: 2});
        return false;
    }
	if(user.length<3){
		layer.msg('用户名太短', {icon: 2}); return false;
	}else if(user.length>20){
		layer.msg('用户名太长', {icon: 2}); return false;
	}else if(pwd.length<6){
		layer.msg('密码不能低于6位', {icon: 2}); return false;
	}else if(pwd.length>30){
		layer.msg('密码太长', {icon: 2}); return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "ajax_user.php?act=setpwd",
		data : {user:user, pwd:pwd},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert(data.msg,{
				  icon: 1,
				  closeBtn: 0,
                  skin: 'layui-layer-rim rounded-2xl'
				}, function(){
				  window.location.reload();
				});
			}else{
				layer.alert(data.msg, {icon: 2, skin: 'layui-layer-rim rounded-2xl'});
			}
		},
        error: function() {
            layer.close(ii);
            layer.msg('服务器响应异常', {icon: 2});
        }
	});
	return false;
}

function connect(type){
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "ajax.php?act=connect",
		data : {type:type},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				window.location.href = data.url;
			}else{
				layer.alert(data.msg, {icon: 2, skin: 'layui-layer-rim rounded-2xl'});
			}
		} 
	});
}

function unbind(type){
	var confirmobj = layer.confirm('解绑后将无法通过QQ一键登录，是否确定解绑？', {
        title: '解除绑定确认',
        icon: 3,
        skin: 'layui-layer-rim rounded-2xl',
        btn: ['确认解绑', '取消']
    }, function () {
	    var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=unbind",
			data : {type:type},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					layer.msg(data.msg, {icon: 1, time: 2000}, function(){ window.location.reload();});
				}else{
					layer.alert(data.msg, {icon: 2, skin: 'layui-layer-rim rounded-2xl'});
				}
			} 
		});
	}, function(){
	    layer.close(confirmobj);
	});
}
</script>
<?php 
}
?>
</body>
</html>
