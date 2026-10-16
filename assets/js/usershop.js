var $_GET = (function(){
	var url = window.document.location.href.toString();
	var u = url.split("?");
	if(typeof(u[1]) == "string"){
		u = u[1].split("&");
		var get = {};
		for(var i in u){
			var j = u[i].split("=");
			get[j[0]] = j[1];
		}
		return get;
	} else {
		return {};
	}
})();
function changepwd(id,skey) {
	pwdlayer = layer.open({
		type: 1,
		title: '修改密码',
		skin: 'layui-layer-rim',
		content: '<div class="form-group"><div class="input-group"><div class="input-group-addon">密码</div><input type="text" id="pwd" value="" class="form-control" placeholder="请填写新的密码" required/></div></div><input type="submit" id="save" onclick="saveOrderPwd('+id+',\''+skey+'\')" class="btn btn-primary btn-block" value="保存">'
	});
}
function saveOrderPwd(id,skey) {
	var pwd=$("#pwd").val();
	if(pwd==''){layer.alert('请确保每项不能为空！');return false;}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=changepwd",
		data : {id:id,pwd:pwd,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.msg('保存成功！');
				layer.close(pwdlayer);
			}else{
				layer.alert(data.msg);
			}
		}
	});
}
function getPoint() {
	if($('#tid option:selected').val()==undefined || $('#tid option:selected').val()=="0"){
		$('#inputsname').html("");
		$('#need').val('');
		$('#display_price').hide();
		$('#display_num').hide();
		$('#display_left').hide();
		$('#alert_frame').hide();
		return false;
	}
	history.replaceState({}, null, './shop.php?cid='+$('#cid').val()+'&tid='+$('#tid option:selected').val());
	var multi = $('#tid option:selected').attr('multi');
	var count = $('#tid option:selected').attr('count');
	var price = $('#tid option:selected').attr('price');
	var shopimg = $('#tid option:selected').attr('shopimg');
	var close = $('#tid option:selected').attr('close');
	$('#display_price').show();
	if(multi==1 && count>1){
		$('#need').val('￥'+price +"元 ➠ "+count+"个");
	}else{
		$('#need').val('￥'+price +"元");
	}
	if(close == 1){
		$('#submit_buy').val('当前商品已停止下单');
		$('#submit_buy').html('当前商品已停止下单');
		layer.alert('当前商品维护中，停止下单！');
	}else if(price == 0){
		$('#submit_buy').val('立即免费领取');
		$('#submit_buy').html('立即免费领取');
	}else{
		$('#submit_buy').val('立即购买');
		$('#submit_buy').html('立即购买');
	}
	if(multi == 1){
		$('#display_num').show();
	}else{
		$('#display_num').hide();
	}
	var desc = $('#tid option:selected').attr('desc');
	if(desc!='' && alert!='null'){
		$('#alert_frame').show();
		$('#alert_frame').html(unescape(desc));
	}else{
		$('#alert_frame').hide();
	}
	var inputnametype = '';
	$('#inputsname').html("");
	var inputname = $('#tid option:selected').attr('inputname');
	if(inputname=='hide'){
		$('#inputsname').append('<input type="hidden" name="inputvalue" id="inputvalue" value="'+$.cookie('mysid')+'"/>');
	}else{
		if(inputname=='')inputname='下单账号';
		if(inputname.indexOf('[')>0 && inputname.indexOf(']')>0){
			inputnametype = inputname.split('[')[1].split(']')[0];
			inputname = inputname.split('[')[0];
		}
		//使用纯净  HuWangV7 类名进行重构，抛弃 form-group 和 input-group
		$('#inputsname').append('<div class="mb-4"><label id="inputname" class="block text-[15px] font-bold text-slate-800 mb-2">' + inputname + '</label><input type="text" name="inputvalue" id="inputvalue" value="' + ($_GET['qq'] ? $_GET['qq'] : '') + '" class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all duration-200 text-slate-800 font-bold text-[15px] shadow-sm" required onblur="checkInput()"/></div>');
	}
	var inputsname = $('#tid option:selected').attr('inputsname');
	if(inputsname!=''){
		$.each(inputsname.split('|'), function(i, value) {
			var inputsnametype = '';
			if(value.indexOf('[')>0 && value.indexOf(']')>0){
				inputsnametype = value.split('[')[1].split(']')[0];
				value = value.split('[')[0];
			}
			if(value.indexOf('{')>0 && value.indexOf('}')>0){
				var addstr = '';
				var selectname = value.split('{')[0];
				var selectstr = value.split('{')[1].split('}')[0];
				$.each(selectstr.split(','), function(i, v) {
					if(v.indexOf(':')>0){
						i = v.split(':')[0];
						v = v.split(':')[1];
					}else{
						i = v;
					}
					addstr += '<option value="'+i+'">'+v+'</option>';
				});
				// HuWangV7 重构 Select 框
				$('#inputsname').append('<div class="mb-4"><label id="inputname' + (i + 2) + '" class="block text-[15px] font-bold text-slate-800 mb-2">' + selectname + '</label><select name="inputvalue' + (i + 2) + '" id="inputvalue' + (i + 2) + '" class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all duration-200 text-slate-800 font-bold text-[15px] shadow-sm">' + addstr + '</select></div>');
			}else if(inputsnametype=='multi'){
				// HuWangV7 重构 Multi 框
				$('#inputsname').append('<div class="mb-4"><label id="inputname' + (i + 2) + '" gettype="' + inputsnametype + '" class="block text-[15px] font-bold text-slate-800 mb-2">' + value + '</label><input type="number" name="inputvalue' + (i + 2) + '" id="inputvalue' + (i + 2) + '" value="1" class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all duration-200 text-slate-800 font-bold text-[15px] shadow-sm" required min="1" max="99999" onchange="getmulti(this)" act="getmulti"/></div>');
			}else{
				if(value=='说说ID'||value=='说说ＩＤ'||inputsnametype=='ssid')
					var addstr='<button type="button" class="absolute right-2 top-[38px] px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-[13px] font-bold transition-colors shadow-sm" onclick="get_shuoshuo(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</button>';
				else if(value=='日志ID'||value=='日志ＩＤ'||inputsnametype=='rzid')
					var addstr='<button type="button" class="absolute right-2 top-[38px] px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-[13px] font-bold transition-colors shadow-sm" onclick="get_rizhi(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</button>';
				else if(value=='作品ID'||value=='作品ＩＤ'||inputsnametype=='zpid')
					var addstr='<button type="button" class="absolute right-2 top-[38px] px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-[13px] font-bold transition-colors shadow-sm" onclick="getshareid2(\'inputvalue'+(i+2)+'\',$(\'#inputvalue\').val())">自动获取</button>';
				else if(value=='收货地址'||value=='收货人地址'||inputsnametype=='address')
					var addstr='<button type="button" class="absolute right-2 top-[38px] px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-[13px] font-bold transition-colors shadow-sm" onclick="getCity(\'inputvalue'+(i+2)+'\')">点此选择</button>';
				else
					var addstr='';
				// HuWangV7 重构普通输入框 + 绝对定位的自动获取按钮
				$('#inputsname').append('<div class="mb-4 relative"><label id="inputname' + (i + 2) + '" gettype="' + inputsnametype + '" class="block text-[15px] font-bold text-slate-800 mb-2">' + value + '</label><input type="text" name="inputvalue' + (i + 2) + '" id="inputvalue' + (i + 2) + '" value="" class="w-full px-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 outline-none transition-all duration-200 text-slate-800 font-bold text-[15px] shadow-sm pr-24" required/>' + addstr + '</div>');
			}
		});
	}
	if($("#inputname2").html() == '说说ID'||$("#inputname2").html() == '说说ＩＤ'||$("#inputname2").attr('gettype')=='ssid'){
		$('#inputvalue2').attr("disabled", true);
		$('#inputvalue2').attr("placeholder", "填写QQ账号后点击自动获取");
	}else if($("#inputname").html() == '作品ID'||$("#inputname").html() == '作品ＩＤ'||$("#inputname").html() == '帖子ID'||$("#inputname").html() == '用户ID'||$("#inputname").html() == '用户ＩＤ'||inputnametype=='shareid'){
		$('#inputvalue').attr("placeholder", "在此输入分享链接 可自动获取");
		$('#inputname').attr("gettype", "shareid");
		if($("#inputname2").html() == '作品ID'||$("#inputname2").html() == '作品ＩＤ'||$("#inputname2").attr('gettype')=='zpid'){
			$('#inputvalue2').attr("placeholder", "填写作品链接后点击自动获取");
			$("#inputvalue2").attr('disabled', true);
		}
	}else if($("#inputname").html() == '作品链接'||$("#inputname").html() == '视频链接'||$("#inputname").html() == '分享链接'||inputnametype=='shareurl'){
		$('#inputvalue').attr("placeholder", "在此输入复制后的链接 可自动转换");
		$('#inputname').attr("gettype", "shareurl");
	}else if(inputnametype=='pinduoduo'){
		$('#inputvalue').attr("placeholder", "在此粘贴你的拼多多助力口令");
		$('#inputname').attr("gettype", "pinduoduo");
	}else{
		$('#inputvalue').removeAttr("placeholder");
		$('#inputvalue2').removeAttr("placeholder");
	}
	var stock = $('#tid option:selected').attr('stock');
	if($('#tid option:selected').attr('isfaka')==1){
		$('#inputvalue').attr("placeholder", "用于接收卡密以及查询订单使用");
		$('#display_left').show();
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=getleftcount",
			data : {tid:$('#tid option:selected').val()},
			dataType : 'json',
			success : function(data) {
				$('#leftcount').val(data.count)
			}
		});
		if($.cookie('email'))$('#inputvalue').val($.cookie('email'));
	}else if(stock!=null && stock!='' && stock!='null'){
		$('#display_left').show();
		$('#leftcount').val(stock);
	}else{
		$('#display_left').hide();
	}
	var alert = $('#tid option:selected').attr('alert');
	if(alert!='' && alert!='null'){
		var ii=layer.alert(''+unescape(alert)+'',{
			btn:['我知道了'],
			title:'商品提示'
		},function(){
			layer.close(ii);
		});
	}
}
function get_shuoshuo(id,uin,km,page){
	km = km || 0;
	page = page || 1;
	if(uin==''){
		layer.alert('请先填写QQ号！');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "GET",
		url : "../ajax.php?act=getshuoshuo&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var addstr='';
				$.each(data.data, function(i, item){
					addstr+='<option value="'+item.tid+'">'+item.content+'</option>';
				});
				var nextpage = page+1;
				var lastpage = page>1?page-1:1;
				
				// HuWangV7 重构：说说分页选择器
				var selectorHtml = '<div class="flex items-center gap-2 mt-2">' + 
					'<button type="button" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shadow-sm" title="上一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></button>' +
					'<select id="shuoid" class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[14px] text-slate-800 outline-none focus:border-brand-500 shadow-sm truncate" onchange="set_shuoshuo(\''+id+'\');">'+addstr+'</select>' +
					'<button type="button" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shadow-sm" title="下一页" onclick="get_shuoshuo(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></button>' +
					'</div>';
				
				if($('#show_shuoshuo').length > 0){
					$('#show_shuoshuo').html(selectorHtml);
				}else{
					$('#inputsname').append('<div class="mb-4" id="show_shuoshuo">' + selectorHtml + '</div>');
				}
				set_shuoshuo(id);
			}else{
				layer.alert(data.msg);
			}
		}
	});
}
function set_shuoshuo(id){
	var shuoid = $('#shuoid').val();
	$('#'+id).val(shuoid);
}
function get_rizhi(id,uin,km,page){
	km = km || 0;
	page = page || 1;
	if(uin==''){
		layer.alert('请先填写QQ号！');return false;
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "GET",
		url : "../ajax.php?act=getrizhi&uin="+uin+"&page="+page+"&hashsalt="+hashsalt,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var addstr='';
				$.each(data.data, function(i, item){
					addstr+='<option value="'+item.blogId+'">'+item.title+'</option>';
				});
				var nextpage = page+1;
				var lastpage = page>1?page-1:1;
				
				// HuWangV7 重构：日志分页选择器
				var selectorHtml = '<div class="flex items-center gap-2 mt-2">' + 
					'<button type="button" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shadow-sm" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+lastpage+')"><i class="fa fa-chevron-left"></i></button>' +
					'<select id="blogid" class="flex-1 px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[14px] text-slate-800 outline-none focus:border-brand-500 shadow-sm truncate" onchange="set_rizhi(\''+id+'\');">'+addstr+'</select>' +
					'<button type="button" class="w-10 h-10 flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shadow-sm" onclick="get_rizhi(\''+id+'\',$(\'#inputvalue\').val(),'+km+','+nextpage+')"><i class="fa fa-chevron-right"></i></button>' +
					'</div>';
					
				if($('#show_rizhi').length > 0){
					$('#show_rizhi').html(selectorHtml);
				}else{
					$('#inputsname').append('<div class="mb-4" id="show_rizhi">' + selectorHtml + '</div>');
				}
				set_rizhi(id);
			}else{
				layer.alert(data.msg);
			}
		}
	});
}
function set_rizhi(id){
	var blogid = $('#blogid').val();
	$('#'+id).val(blogid);
}
function fillOrder(id,skey){
	if(!confirm('是否确定补交订单？'))return;
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=fill",
		data : {orderid:id,skey:skey},
		dataType : 'json',
		success : function(data) {
			layer.alert(data.msg);
			$("#submit_query").click();
		}
	});
}
function getsongid(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('.qq.com')<0){layer.alert('请输入正确的歌曲的分享链接！');return false;}
	try{
		var songid = songurl.split('s=')[1].split('&')[0];
		layer.msg('ID获取成功！下单即可');
	}catch(e){
		layer.alert('请输入正确的歌曲的分享链接！');return false;
	}
	$('#inputvalue').val(songid);
}
function getsharelink(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){layer.alert('请输入正确的内容！');return false;}
	try{
		if(songurl.indexOf('http://')>=0){
			var songid = 'http://' + songurl.split('http://')[1].split(' ')[0].split('，')[0];
		}else if(songurl.indexOf('https://')>=0){
			var songid = 'https://' + songurl.split('https://')[1].split(' ')[0].split('，')[0];
		}
		if(songid != $("#inputvalue").val())layer.msg('链接转换成功！下单即可');
	}catch(e){
		layer.alert('请输入正确的内容！');return false;
	}
	$('#inputvalue').val(songid);
}
function getshareid(){
	var songurl=$("#inputvalue").val();
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){layer.alert('请输入正确的内容！');return false;}
	try{
		if(songurl.indexOf('http://')>=0){
			var songurl = 'http://' + songurl.split('http://')[1].split(' ')[0].split('，')[0];
		}else if(songurl.indexOf('https://')>=0){
			var songurl = 'https://' + songurl.split('https://')[1].split(' ')[0].split('，')[0];
		}else{
			throw false;
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=getshareid",
			data : {url:songurl, hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					$('#inputvalue').val(data.songid);
					if(typeof data.songid2 != "undefined" && $('#inputvalue2').length>0)$('#inputvalue2').val(data.songid2);
					layer.msg('ID获取成功！下单即可');
				}else{
					layer.alert(data.msg);return false;
				}
			}
		});
	}catch(e){
		layer.alert('请输入正确的内容！');return false;
	}
}
function getshareid2(id, songurl){
	if(songurl==''){layer.alert('请确保每项不能为空！');return false;}
	if(songurl.indexOf('http')<0){return false;}
	getshareid();
}
function getpddinput() {
	var result = "";
	var pddinput = $("#inputvalue").val();
	if (pddinput == '') {
		return false;
	}
	if (pddinput.indexOf("PinDuoDuo") != -1 && pddinput.indexOf("http") === -1) {
		pddinput = pddinput.replace("PinDuoDuo", "");
	}
	var pattresult = (/[a-zA-Z0-9=_\&\?\-\/]?[a-zA-Z0-9]{16}[a-zA-Z0-9=_\&\?\-\/]?/).exec(pddinput);
	var patt_str = (/τ[a-zA-Z0-9]{13}τ/).exec(pddinput);
	var pattresult1 = (/(^[a-zA-Z0-9][a-zA-Z0-9]+)点+/).exec(pddinput);
	var pattresult2 = (/(^[0-9]{15})/).exec(pddinput);
	var pattresult3 = (/([0-9]{15}$)/).exec(pddinput);
	var pattresult4 = (/[0-9]{6,20}/).exec(pddinput);
	var pattresult5 = (/(http|https):\/\/[\w\.\=\_\/\-\$\&\!\?\(\)#%+:;]+/).exec(pddinput);
	var pattresult6 = (/([0-9]{8})/).exec(pddinput);
	var pattresult7 = (/[a-zA-Z0-9=_\&\?\-\/]?[a-zA-Z0-9]{15}[a-zA-Z0-9=_\&\?\-\/]?/).exec(pddinput);
	var pattresult12 = (/^[a-zA-Z0-9]{16}/).exec(pddinput);

	var pattresult10 = (/[\ud83a-\ud83f][\u0000-\uFFFF]/).exec(pddinput);
	var no_emoji_input = pddinput.replace(/[\ud83a-\ud83f][\u0000-\uFFFF]/g, "");
	no_emoji_input = no_emoji_input.replace(/[\ufe00-\ufe0f]/g, "");
	no_emoji_input = no_emoji_input.replace(/[\u0000-\uffff][\u20aa-\u20ff]/g, "");

	var pattresult13 = (/[a-zA-Z0-9]{13}/).exec(no_emoji_input);
	var pattresult14 = (/[a-zA-Z0-9]{14}/).exec(no_emoji_input);
	var status = false;
	if (exec_succ(patt_str)) {
		result = patt_str[0];
	} else if (exec_succ(pattresult1) && pattresult1.length > 1) {
		result = pattresult1[1];
	} else if (exec_succ(pattresult2) && pattresult2.length > 1) {
		result = pattresult2[1];
	} else if (exec_succ(pattresult3) && pattresult3.length > 1) {
		result = pattresult3[1];
	} else if (exec_succ(pattresult) && pattresult[0].length == 16) {
		var a = pattresult[0].length;
		result = pattresult[0];
	} else if (exec_succ(pattresult5) && pattresult5.length > 1) {
		var a = pattresult5[0].length;
		result = pattresult5[0];
	} else if (pddinput.indexOf("⇥") != -1 && pddinput.indexOf("⇤") != -1) {
		result = pddinput.substring(pddinput.indexOf("⇥"), pddinput.indexOf("⇤") + 1);
		layer.msg('ID获取成功！提交下单即可');
	} else if (exec_succ(pattresult4) && (pattresult4[0].length == 9 || pattresult4[0].length == 13 || pattresult4[0].length == 15)) {
		result = pattresult4[0];
		status = true;
	} else if (pddinput.indexOf("口令") != -1 && exec_succ(pattresult6) && pattresult6.length > 1) {
		result = pattresult6[1];
	} else if (!exec_succ(pattresult10) && exec_succ(pattresult7) && pattresult7[0].length == 15) {
		var a = pattresult7[0].length;
		result = pattresult7[0];
	} else if (exec_succ(pattresult12)) {
		result = pattresult12[0];
	} else if (exec_succ(pattresult13) && !exec_succ(pattresult14)) {
		var password = "\ud83d\ude42" + pattresult13[0].slice(0, 6) + "\ud83d\ude42" + pattresult13[0].slice(6) + "\ud83d\ude42";
		result = password;
		$('#inputvalue').prop('readonly', true);
	} else {
		result = pddinput;
	}
	$('#inputvalue').val(result);
	return status;
}
function exec_succ(pattresult) {
	if (typeof(pattresult) == 'object' && pattresult != null && pattresult.length > 0) {
		return true;
	} else {
		return false;
	}
}
function getmulti(obj){
	var num = parseInt($(obj).val());
	if(num<1){ num=1; $(obj).val('1'); }

	var mult = 1;
	$("input[act='getmulti']").each(function () {
		mult = mult * parseInt($(this).val());
	});

	var i = parseInt($("#num").val());
	if(isNaN(i))return false;
	var price = parseFloat($('#tid option:selected').attr('price'));
	var count = parseInt($('#tid option:selected').attr('count'));
	var prices = $('#tid option:selected').attr('prices');
	if(i<1) $("#num").val(1);
	if(prices!='' || prices!='null'){
		var discount = 0;
		$.each(prices.split(','), function(index, item){
			if(i>=parseInt(item.split('|')[0]))discount = parseFloat(item.split('|')[1]);
		});
		price = price - discount;
	}
	price = price * i * mult;
	count = count * i;
	if(count>1)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
	else $('#need').val('￥'+price.toFixed(2) +"元");
}

function showOrder(id, skey) {
  var ii = layer.load(2, { shade: [0.1, "#fff"] });
  
  var status = [
    '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 text-xs font-bold">正在提交</span>',
    '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-bold"><i class="fa fa-check-circle mr-1"></i>已完成</span>',
    '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-600 border border-amber-100 text-xs font-bold"><i class="fa fa-clock-o mr-1"></i>处理中</span>',
    '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 border border-rose-100 text-xs font-bold"><i class="fa fa-times-circle mr-1"></i>订单异常</span>',
    '<span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 border border-slate-200 text-xs font-bold">已退款 (后台)</span>',
  ];

  $.ajax({
    type: "POST",
    url: "../ajax.php?act=order",
    data: { id: id, skey: skey },
    dataType: "json",
    success: function (data) {
      layer.close(ii);
      if (data.code == 0) {

        // 外层容器
        var item = '<style>#qy-order-modal::-webkit-scrollbar{display:none;} #qy-order-modal{-ms-overflow-style:none;scrollbar-width:none;}</style>';
        item += '<div id="qy-order-modal" class="max-h-[75vh] overflow-y-auto bg-slate-50/80 p-3 sm:p-5 font-sans">';

        // --- 模块 1：基础信息 ---
        item += '<div class="flex items-center text-[14px] font-bold text-slate-800 mb-3"><span class="w-1 h-3.5 bg-brand-500 rounded-full mr-2"></span>基础信息</div>';
        item += '<div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-5 flex flex-col">';

        // 订单编号
        item += '<div class="flex items-center justify-between p-4 border-b border-slate-50">';
        item += '<div class="text-slate-500 text-[13px] font-medium shrink-0">订单编号</div>';
        item += '<div class="flex items-center justify-end"><span class="font-mono text-[14px] text-slate-800 font-medium">' + id + '</span>';
        item += '<button onclick="copyText(\'' + id + '\')" class="ml-2 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-xs font-bold transition-colors flex items-center"><i class="fa fa-copy mr-1"></i>复制</button></div></div>';

        // 商品名称
        item += '<div class="flex items-center justify-between p-4 border-b border-slate-50"><div class="text-slate-500 text-[13px] font-medium shrink-0 w-20">商品名称</div><div class="text-slate-800 text-[14px] font-bold text-right break-all">' + data.name + '</div></div>';
        // 付款金额
        item += '<div class="flex items-center justify-between p-4 border-b border-slate-50"><div class="text-slate-500 text-[13px] font-medium shrink-0">付款金额</div><div class="text-rose-500 text-[16px] font-black">￥' + data.money + '</div></div>';
        // 购买时间
        item += '<div class="flex items-center justify-between p-4 border-b border-slate-50"><div class="text-slate-500 text-[13px] font-medium shrink-0">购买时间</div><div class="text-slate-700 text-[13px] font-medium">' + data.date + '</div></div>';
        // 下单信息
        item += '<div class="flex items-start justify-between p-4 border-b border-slate-50"><div class="text-slate-500 text-[13px] font-medium shrink-0 mt-0.5">下单信息</div><div class="bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100 text-slate-700 text-[13px] break-all text-right">' + data.inputs + '</div></div>';
        // 订单状态
        item += '<div class="flex items-center justify-between p-4 border-b border-slate-50"><div class="text-slate-500 text-[13px] font-medium shrink-0">订单状态</div><div>' + status[data.status] + '</div></div>';

        // 售后与退款操作
        if (data.complain) {
          item += '<div class="flex items-center justify-between p-4"><div class="text-slate-500 text-[13px] font-medium shrink-0">操作订单</div>';
          item += '<div class="flex items-center gap-2">';
          item += '<a href="./user/workorder.php?my=add&orderid=' + id + '&skey=' + skey + '" target="_blank" onclick="return checklogin(' + data.islogin + ')" class="px-3.5 py-1.5 bg-white border border-brand-200 text-brand-600 hover:bg-brand-50 rounded-lg text-xs font-bold transition-colors">申请售后</a>';
          if (data.selfrefund == 1 && data.islogin == 1 && (data.status == 0 || data.status == 3)) {
            item += '<a onclick="return apply_refund(' + id + ',\'' + skey + '\')" class="px-3.5 py-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg text-xs font-bold transition-colors cursor-pointer shadow-sm">申请退款</a>';
          }
          item += '</div></div>';
        }
        item += '</div>'; // 关闭基础信息卡片

        // --- 模块 2：卡密信息 ---
        if (data.kminfo) {
          item += '<div class="flex items-center text-[14px] font-bold text-emerald-600 mb-3 px-1"><span class="w-1 h-3.5 bg-emerald-500 rounded-full mr-2"></span>卡密信息</div>';
          item += '<div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 mb-5 p-4 text-[13px] text-emerald-700 font-mono break-all whitespace-pre-wrap leading-relaxed">' + data.kminfo + '</div>';
        }
        
        // --- 模块 3：订单实时状态 ---
        if (data.list && typeof data.list === "object") {
          var showRealTimeStatus = false;
          if (typeof data.list.order_state !== "undefined" && data.list.order_state && typeof data.list.now_num !== "undefined") {
            var defaultNum = parseInt(data.list.start_num) || 0;
            if (defaultNum > 100) { showRealTimeStatus = true; }
          } else {
            var hasLargeNum = false;
            $.each(data.list, function (i, v) {
              if ((i.includes('num') || i.includes('数量')) && parseInt(v) > 100) { hasLargeNum = true; return false; }
            });
            if (hasLargeNum) { showRealTimeStatus = true; }
          }
          
          if (showRealTimeStatus) {
            item += '<div class="flex items-center text-[14px] font-bold text-amber-500 mb-3 px-1"><span class="w-1 h-3.5 bg-amber-400 rounded-full mr-2"></span>实时进度</div>';
            item += '<div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-5 flex flex-col">';

            if (typeof data.list.order_state !== "undefined" && data.list.order_state) {
              if (typeof data.list.num !== "undefined") item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">下单数量</div><div class="text-slate-800 font-bold">' + data.list.num + '</div></div>';
              if (typeof data.list.start_num !== "undefined") item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">初始数量</div><div class="text-slate-800 font-bold">' + data.list.start_num + '</div></div>';
              if (typeof data.list.now_num !== "undefined") item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">当前数量</div><div class="text-amber-500 font-bold">' + data.list.now_num + '</div></div>';
              if (typeof data.list.add_time !== "undefined") item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">下单时间</div><div class="text-slate-700 font-medium">' + formatTimestamp(data.list.add_time) + '</div></div>';
              item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">状态</div><div class="text-brand-500 font-bold">' + data.list.order_state + '</div></div>';
              if (typeof data.list.result !== "undefined" && data.list.result) {
                item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">异常信息</div><div class="text-rose-500 font-medium">' + data.list.result + '</div></div>';
              }
            } else {
              $.each(data.list, function (i, v) {
                item += '<div class="flex justify-between p-3.5 border-b border-slate-50"><div class="text-slate-500 text-[13px]">' + i + '</div><div class="text-slate-800 font-medium">' + v + '</div></div>';
              });
            }
            item += '</div>';
          }
        } else if (data.result && !data.kminfo) {
          // --- 模块 4：处理结果 ---
          item += '<div class="flex items-center text-[14px] font-bold text-cyan-600 mb-3 px-1"><span class="w-1 h-3.5 bg-cyan-500 rounded-full mr-2"></span>处理结果</div>';
          item += '<div class="bg-cyan-50 rounded-2xl border border-cyan-100 mb-5 p-4 text-[13px] text-cyan-700 font-medium">' + data.result + '</div>';
        }
        
        // --- 模块 5：商品简介 ---
        if (data.desc) {
            item += '<div class="flex items-center text-[14px] font-bold text-purple-600 mb-3 px-1"><span class="w-1 h-3.5 bg-purple-500 rounded-full mr-2"></span>商品简介</div>';
            item += '<div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-[13px] text-slate-600 leading-relaxed break-all">' + data.desc + '</div>';
        }

        item += "</div>"; // 关闭整个滚动容器

        var area = [$(window).width() > 500 ? "520px" : "95%", "auto"];
        
        layer.open({
          type: 1,
          area: area,
          title: '<div class="flex items-center text-slate-800 font-bold text-[15px]"><i class="fa fa-file-text-o text-brand-500 mr-2"></i>订单详情</div>',
          content: item,
          closeBtn: 1,
          shadeClose: true, 
          btn: ['好的'], 
          yes: function(index){ layer.close(index); },
          success: function (layero) {
            // 用纯净的设定剔除 layui 自带的毒瘤边框
            $(layero).removeClass('layui-layer-rim').css({
              "border-radius": "1.25rem", //  HuWangV7 rounded-xl 级圆角
              "box-shadow": "0 25px 50px -12px rgba(0, 0, 0, 0.25)", //  HuWangV7 shadow-2xl
              "border": "none",
              "overflow": "hidden",
              "background": "#f8fafc" // bg-slate-50
            });
            
            // 头尾部分也配合  HuWangV7 风格进行修饰
            $(layero).find('.layui-layer-title').css({ "background": "#fff", "border-bottom": "1px solid #f1f5f9" });
            $(layero).find('.layui-layer-btn').css({ "background": "#fff", "border-top": "1px solid #f1f5f9", "padding": "12px 16px" });
            $(layero).find('.layui-layer-btn0').css({ "background": "#10b981", "border-color": "#10b981", "border-radius": "0.5rem", "font-weight": "bold", "padding": "0 1.5rem" });
            $(layero).find(".layui-layer-content").css({ "overflow": "hidden", "padding": "0" });
          },
        });
      } else {
        layer.alert(data.msg, { icon: 2 });
      }
    },
  });
}

function queryOrder(type,content,page){
	$('#submit_query').val('Loading');
	$('#result2').hide();
	$('#list').html('');
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=query",
		data : {type:type, qq:content, page:page},
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				var status;
				$.each(data.data, function(i, item){
					if(item.status==1)
						status='<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">已完成</span>';
					else if(item.status==2)
						status='<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">处理中</span>';
					else if(item.status==3)
						status='<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-100 text-rose-800">异常</span>&nbsp;<button type="submit" class="px-2 py-1 bg-sky-500 text-white rounded text-xs hover:bg-sky-600 transition-colors" onclick="fillOrder('+item.id+',\''+item.skey+'\')">补交</button>';
					else if(item.status==4)
						status='<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">已退款</span>';
					else
						status='<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">待处理</span>';
					// HuWangV7 重构查询列表的表格行
					$('#list').append('<tr orderid='+item.id+' class="hover:bg-slate-50 transition-colors duration-150"><td class="px-4 py-3 border-b border-slate-100 text-sm">'+item.input+'</td><td class="px-4 py-3 border-b border-slate-100 text-sm font-medium text-slate-800">'+item.name+'</td><td class="px-4 py-3 border-b border-slate-100 text-sm">'+item.value+'</td><td class="hidden-xs px-4 py-3 border-b border-slate-100 text-sm text-slate-500">'+item.addtime+'</td><td class="px-4 py-3 border-b border-slate-100">'+status+'</td><td class="px-4 py-3 border-b border-slate-100"><a onclick="showOrder('+item.id+',\''+item.skey+'\')" title="查看订单详细" class="cursor-pointer px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-xs font-bold transition-colors shadow-sm">详细</a></td></tr>');
					if(item.result!=null){
						if(item.status==3){
							$('#list').append('<tr><td colspan=6 class="px-4 py-2 bg-rose-50 border-b border-rose-100"><div class="text-rose-600 text-sm font-medium"><i class="fa fa-warning mr-1"></i>异常原因：'+item.result+'</div></td></tr>');
						}
					}
				});
				var addstr = '';
				if(data.islast==true) addstr += '<button class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-colors" onclick="queryOrder(\''+data.type+'\',\''+data.content+'\','+(data.page-1)+')"><i class="fa fa-chevron-left mr-1"></i>上一页</button>';
				if(data.isnext==true) addstr += '<button class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition-colors float-right" onclick="queryOrder(\''+data.type+'\',\''+data.content+'\','+(data.page+1)+')">下一页<i class="fa fa-chevron-right ml-1"></i></button>';
				if(addstr != '') $('#list').append('<tr><td colspan=6 class="px-4 py-3">'+addstr+'</td></tr>');
				
				if($(window).width() > 768){
					if($('#list2').length>0){
						$('#list2').html($('#list').html());
					}else{
						// HuWangV7 重构桌面端查询列表弹窗
						layer.open({
							type: 1,
							shadeClose: true,
							shade: [0.3, '#000'],
							zIndex: 90,
							area: [";max-width:90%;min-width:800px",";max-height:90vh"],
							title: '<div class="text-slate-800 font-bold text-[15px]"><i class="fa fa-search text-brand-500 mr-2"></i>查询结果</div>',
							content: '<div class="overflow-x-auto p-4"><table class="w-full text-left border-collapse"><thead><tr><th class="px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm rounded-tl-xl">下单账号</th><th class="px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm">商品名称</th><th class="px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm">数量</th><th class="hidden-xs px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm">购买时间</th><th class="px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm">状态</th><th class="px-4 py-3 bg-slate-50 text-slate-600 font-semibold text-sm rounded-tr-xl">操作</th></tr></thead><tbody id="list2">'+$('#list').html()+'</tbody></table></div>',
							success: function (layero) {
								$(layero).css({
									"border-radius": "1rem",
									"box-shadow": "0 25px 50px -12px rgba(0, 0, 0, 0.25)",
									"border": "none",
								});
								$(layero).find('.layui-layer-title').css({ "border-bottom": "1px solid #f1f5f9", "background": "#fff" });
							}
						});
					}
				}else{
					$("#result2").slideDown();
				}
				if($_GET['buyok']){
					showOrder(data.data[0].id,data.data[0].skey)
				}
			}else{
				layer.alert(data.msg);
			}
			$('#submit_query').val('立即查询');
		}
	});
}
function apply_refund(id,skey){
	var confirmobj = layer.confirm('待处理或异常状态订单可以申请退款，退款之后资金会退到用户余额，是否确认退款？', {
		btn: ['确认退款','取消']
	}, function(){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=apply_refund",
			data : {id:id,skey:skey},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					layer.alert('成功退款'+data.money+'元到余额！', {icon:1}, function(){ window.location.reload(); });
				}else{
					layer.alert(data.msg, {icon:2});
				}
			}
		});
	}, function(){
		layer.close(confirmobj);
	});
}
var handlerEmbed = function (captchaObj) {
	captchaObj.appendTo('#captcha');
	captchaObj.onReady(function () {
		$("#captcha_wait").hide();
	}).onSuccess(function () {
		var result = captchaObj.getValidate();
		if (!result) {
			return alert('请完成验证');
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=pay",
			data : {tid:$("#tid").val(),inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt,geetest_challenge:result.geetest_challenge,geetest_validate:result.geetest_validate,geetest_seccode:result.geetest_seccode},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code >= 0){
					$('#alert_frame').hide();
					alert('领取成功！');
					window.location.href='?buyok=1';
				}else{
					layer.alert(data.msg);
					captchaObj.reset();
				}
			}
		});
	});
};
var handlerEmbed2 = function (token) {
	if (!token) {
		return alert('请完成验证');
	}
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=pay",
		data : {tid:$("#tid").val(),inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt,token:token},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code >= 0){
				$('#alert_frame').hide();
				alert('领取成功！');
				window.location.href='?buyok=1';
			}else{
				layer.alert(data.msg);
			}
		}
	});
};
var handlerEmbed3 = function (vaptchaObj) {
	vaptchaObj.render();
	$('#captcha_text').hide();
	vaptchaObj.listen('pass', function() {
		var token = vaptchaObj.getToken();
		if (!token) {
			return alert('请完成验证');
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=pay",
			data : {tid:$("#tid").val(),inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt,token:token},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code >= 0){
					$('#alert_frame').hide();
					alert('领取成功！');
					window.location.href='?buyok=1';
				}else{
					layer.alert(data.msg);
					vaptchaObj.reset();
				}
			}
		});
	});
};
function toTool(cid,tid){
	history.replaceState({}, null, './shop.php?cid='+cid+'&tid='+tid);
	$("#recommend").modal('hide');
	$_GET['tid']=tid;
	$_GET["cid"]=cid;
	$("#cid").val(cid);
	$("#cid").change();
	$("#goodType").hide('normal');
	$("#goodTypeContent").show('normal');
}
function dopay(type,orderid){
	if(type == 'rmb'){
		var ii = layer.msg('正在提交订单请稍候...', {icon: 16,shade: 0.5,time: 15000});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=payrmb",
			data : {orderid: orderid},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					// 支付成功后，更新购物车计数
					$.ajax({
						type : "GET",
						url : "../ajax.php?act=getcount",
						dataType : 'json',
						async: true,
						success : function(countData) {
							if(countData.cart_count != null){
								$('#cart_count').html(countData.cart_count);
								if(countData.cart_count == 0){
									$('#alert_cart').slideUp();
								}
							}
						}
					});
					alert(data.msg);
					window.location.href='?buyok=1';
				}else if(data.code == -2){
					// 支付成功后，更新购物车计数
					$.ajax({
						type : "GET",
						url : "../ajax.php?act=getcount",
						dataType : 'json',
						async: true,
						success : function(countData) {
							if(countData.cart_count != null){
								$('#cart_count').html(countData.cart_count);
								if(countData.cart_count == 0){
									$('#alert_cart').slideUp();
								}
							}
						}
					});
					alert(data.msg);
					window.location.href='?buyok=1';
				}else if(data.code == -3){
					var confirmobj = layer.confirm('你的余额不足，请充值！', {
						btn: ['立即充值','取消']
					}, function(){
						window.location.href='./recharge.php';
					}, function(){
						layer.close(confirmobj);
					});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	}else{
		window.location.href='../other/submit.php?type='+type+'&orderid='+orderid;
	}
}
function cancel(orderid){
	layer.closeAll();
	$.ajax({
		type : "POST",
		url : "../ajax.php?act=cancel",
		data : {orderid: orderid, hashsalt: hashsalt},
		dataType : 'json',
		async : true,
		success : function(data) {
			if(data.code == 0){
			}else{
				layer.msg(data.msg);
				window.location.reload();
			}
		},
		error:function(data){
			window.location.reload();
		}
	});
}
function checkInput() {
	if($('#inputname').attr("gettype")=='shareid'){
		if($("#inputvalue").val()!='' && $("#inputvalue").val().indexOf('http')>=0){
			getshareid();
		}
	}
	else if($('#inputname').attr("gettype")=='shareurl'){
		if($("#inputvalue").val()!='' && $("#inputvalue").val().indexOf('http')>=0){
			getsharelink();
		}
	}
	else if($('#inputname').attr("gettype")=='pinduoduo'){
		if($("#inputvalue").val()!=''){
			getpddinput();
		}
	}
}
function getCity(inputid,fid,i){
	i = i || 0;
	fid = fid || 0;
	if(i == 0){
		// HuWangV7 重构级联选择器
		var options='<select class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl mb-3 text-[14px] outline-none focus:border-brand-500" id="biaozhi_'+(i+1)+'" onchange="getCity(\''+inputid+'\',this.value,'+(i+1)+')">';
		options+='<option>请选择地址</option>';
		$.each("\u5317\u4eac|1|72|1,\u4e0a\u6d77|2|78|1,\u5929\u6d25|3|51035|1,\u91cd\u5e86|4|113|1,\u6cb3\u5317|5|142,\u5c71\u897f|6|303,\u6cb3\u5357|7|412,\u8fbd\u5b81|8|560,\u5409\u6797|9|639,\u9ed1\u9f99\u6c5f|10|698,\u5185\u8499\u53e4|11|799,\u6c5f\u82cf|12|904,\u5c71\u4e1c|13|1000,\u5b89\u5fbd|14|1116,\u6d59\u6c5f|15|1158,\u798f\u5efa|16|1303,\u6e56\u5317|17|1381,\u6e56\u5357|18|1482,\u5e7f\u4e1c|19|1601,\u5e7f\u897f|20|1715,\u6c5f\u897f|21|1827,\u56db\u5ddd|22|1930,\u6d77\u5357|23|2121,\u8d35\u5dde|24|2144,\u4e91\u5357|25|2235,\u897f\u85cf|26|2951,\u9655\u897f|27|2376,\u7518\u8083|28|2487,\u9752\u6d77|29|2580,\u5b81\u590f|30|2628,\u65b0\u7586|31|2652,\u6e2f\u6fb3|52993|52994,\u53f0\u6e7e|32|2768,\u9493\u9c7c\u5c9b|84|84".split(","), function(a, c) {
			c = c.split("|"),
				options+='<option value="'+c[1]+'">'+c[0]+'</option>'
		});
		options+='</select>';
		layer.alert('<div id="layer_button" class="p-2">'+options+'</div>',{
			title: '选择收货地址',
			skin: 'layui-layer-rim',
			btn: ['确定选择']
		},function(index){
			var con='';
			$("#layer_button select").each(function(){
				if(this.selectedIndex > 0) { // 避免拼接"请选择地址"
					con+=$(this.options[this.selectedIndex]).text();
				}
			});
			if($("#more_dizhi").length>0)con+=$("#more_dizhi").val();
			if(con.length<7)return layer.alert('请选择完整的收货地址！');
			$("#"+inputid).val(con).show();
			$("#button_"+inputid).hide();
			layer.close(index);
		});
	}else{
		$.ajax({
			type:"get",
			url:"https://fts.jd.com/area/get?fid="+fid,
			dataType:"jsonp",
			success:function(data){
				if(data.length<1){
					if($("#layer_button").html().indexOf("getCity('"+inputid+"',this.value,"+(i+1)+")")!=-1){
						$("#biaozhi_"+(i+1)).remove();
					}
					// HuWangV7 重构详细地址输入框
					if($("#more_dizhi").length>0){}else $("#layer_button").append('<input class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[14px] outline-none focus:border-brand-500 mt-2" id="more_dizhi" placeholder="详细地址(村、街道、门牌号)">');
					return false;
				}
				var options='<select class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl mb-3 text-[14px] outline-none focus:border-brand-500" id="biaozhi_'+(i+1)+'" onchange="getCity(\''+inputid+'\',this.value,'+(i+1)+')">';
				options+='<option>请选择地址</option>';
				$.each(data,function(index,res){
					options+='<option value="'+res.id+'">'+res.name+'</option>';
				});
				options+='</select>';
				if($("#layer_button").html().indexOf("getCity('"+inputid+"',this.value,"+(i+1)+")")!=-1){
					$("#more_dizhi").remove();
					$("#biaozhi_"+(i+1)).html(options);
				}else{
					$("#layer_button").append(options);
				}
			}
		});
	}
}
function openCart(){
	var area = [$(window).width() > 640 ? '640px' : '95%', $(window).height() > 600 ? '600px' : '90%'];
	var options = {
		type: 2,
		title: '我的购物车',
		shadeClose: true,
		shade: false,
		maxmin: true,
		moveOut: true,
		area: area,
		content: '../?mod=cart',
		zIndex: layer.zIndex,
		success: function (layero, index) {
			var that = this;
			$(layero).data("callback", that.callback);
			layer.setTop(layero);
			if ($(layero).height() > $(window).height()) {
				layer.style(index, {
					top: 0,
					height: $(window).height()
				});
			}
			//为购物车 iframe 弹窗应用现代圆角样式
			$(layero).css({
				"border-radius": "1rem",
				"box-shadow": "0 25px 50px -12px rgba(0, 0, 0, 0.25)",
				"border": "none",
				"overflow": "hidden"
			});
			$(layero).find('.layui-layer-title').css({ "border-bottom": "1px solid #f1f5f9", "background": "#fff" });
		},
		cancel: function(){
			window.location.reload();
		}
	}
	if ($(window).width() < 480 || (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream && top.$("body").size() > 0)) {
		options.area = [top.$("body").width() + "px", top.$("body").height() + "px"];
		options.offset = [top.$("body").scrollTop() + "px", "0px"];
	}
	layer.open(options);
}
$(document).ready(function(){
	$('.goodTypeChange').click(function(){
		var id = $(this).data('id');
		var img = $(this).data('img');
		history.replaceState({}, null, './shop.php?cid='+id);
		$("#cid").val(id);
		$("#cid").change();
		$("#goodType").hide('normal');
		$("#goodTypeContent").show('normal');
	});
	$(".nav-tabs,.backType").click(function(){
		history.replaceState({}, null, './shop.php');
		$("#goodType").show('normal');
		$("#goodTypeContent").hide('normal');
	})
	$("#showSearchBar").click(function () {
		$("#display_selectclass").slideToggle();
		$("#display_searchBar").slideToggle();
	});
	$("#closeSearchBar").click(function () {
		$("#display_searchBar").slideToggle();
		$("#display_selectclass").slideToggle();
	});
	$("#doSearch").click(function () {
		var kw = $("#searchkw").val();
		if(kw==''){layer.msg('请先输入要搜索的内容', {time: 500});return;}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$("#tid").empty();
		$("#tid").append('<option value="0">请选择商品</option>');
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=gettool",
			data : {kw:kw},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					var num = 0;
					$.each(data.data, function (i, res) {
						$("#tid").append('<option value="'+res.tid+'" cid="'+res.cid+'" price="'+res.price+'" desc="'+escape(res.desc)+'" alert="'+escape(res.alert)+'" inputname="'+res.input+'" inputsname="'+res.inputs+'" multi="'+res.multi+'" isfaka="'+res.isfaka+'" count="'+res.value+'" close="'+res.close+'" prices="'+res.prices+'" max="'+res.max+'" min="'+res.min+'" stock="'+res.stock+'">'+res.name+'</option>');
						num++;
					});
					$("#tid").val(0);
					getPoint();
					if(num==0 && cid!=0)layer.msg('<option value="0">没有搜索到相关商品</option>', {icon: 2, time: 500});
					else layer.msg('成功搜索到'+num+'个商品', {icon: 1, time: 1000});
				}else{
					layer.alert(data.msg);
				}
			},
			error:function(data){
				layer.msg('加载失败，请刷新重试');
				return false;
			}
		});
	});
	$("#cid").change(function () {
		var cid = $(this).val();
		if(cid>0)history.replaceState({}, null, './shop.php?cid='+cid);
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$("#tid").empty();
		$("#tid").append('<option value="0">请选择商品</option>');
		$.ajax({
			type : "GET",
			url : "../ajax.php?act=gettool&cid="+cid+"&info=1",
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				$("#tid").empty();
				$("#tid").append('<option value="0">请选择商品</option>');
				if(data.code == 0){
					if(data.info!=null){
						$("#className").html(data.info.name);
						if(data.info.shopimg)
							$("#classImg").attr('src',data.info.shopimg.indexOf("://")>0?data.info.shopimg:'../'+data.info.shopimg);
						else
							$("#classImg").attr('src','');
					}
					var num = 0;
					$.each(data.data, function (i, res) {
						$("#tid").append('<option value="'+res.tid+'" cid="'+res.cid+'" price="'+res.price+'" desc="'+escape(res.desc)+'" alert="'+escape(res.alert)+'" inputname="'+res.input+'" inputsname="'+res.inputs+'" multi="'+res.multi+'" isfaka="'+res.isfaka+'" count="'+res.value+'" close="'+res.close+'" prices="'+res.prices+'" max="'+res.max+'" min="'+res.min+'" stock="'+res.stock+'">'+res.name+'</option>');
						num++;
					});
					if($_GET["tid"] && $_GET["cid"]==cid){
						var tid = parseInt($_GET["tid"]);
						$("#tid").val(tid);
					}else{
						$("#tid").val(0);
					}
					getPoint();
					if(num==0 && cid!=0)$("#tid").html('<option value="0">该分类下没有商品</option>');
				}else{
					layer.alert(data.msg);
				}
			},
			error:function(data){
				layer.msg('加载失败，请刷新重试');
				return false;
			}
		});
	});
	$("#submit_buy").click(function(){
		var tid=$("#tid").val();
		if(tid==0){layer.alert('请选择商品！');return false;}
		var inputvalue=$("#inputvalue").val();
		if(inputvalue=='' || tid==''){layer.alert('请确保每项不能为空！');return false;}
		if($("#inputvalue2").val()=='' || $("#inputvalue3").val()=='' || $("#inputvalue4").val()=='' || $("#inputvalue5").val()==''){layer.alert('请确保每项不能为空！');return false;}
		if(($('#inputname').html()=='下单ＱＱ' || $('#inputname').html()=='ＱＱ账号' || $("#inputname").html() == 'QQ账号') && (inputvalue.length<5 || inputvalue.length>11 || isNaN(inputvalue))){layer.alert('请输入正确的QQ号！');return false;}
		var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if($('#inputname').html()=='你的邮箱' && !reg.test(inputvalue)){layer.alert('邮箱格式不正确！');return false;}
		reg=/^[1][0-9]{10}$/;
		if($('#inputname').html()=='手机号码' && !reg.test(inputvalue)){layer.alert('手机号码格式不正确！');return false;}
		if($("#inputname2").html() == '说说ID'||$("#inputname2").html() == '说说ＩＤ'){
			if($("#inputvalue2").val().length != 24){layer.alert('说说必须是原创说说！');return false;}
		}
		checkInput();
		if($("#inputname").html() == '抖音作品ID'||$("#inputname").html() == '火山作品ID'||$("#inputname").html() == '火山直播ID'){
			if($("#inputvalue").val().length != 19){layer.alert('您输入的作品ID有误！');return false;}
		}
		if($("#inputname2").html() == '抖音评论ID'){
			if($("#inputvalue2").val().length != 19){layer.alert('您输入的评论ID有误！请点击自动获取手动选择评论！');return false;}
		}
		if($('#inputname').attr("gettype")=='shareurl'){
			if($("#inputvalue").val().indexOf('http://')==-1 && $("#inputvalue").val().indexOf('https://')==-1){
				layer.alert('您输入的链接有误！请重新输入！');return false;
			}
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=pay",
			data : {tid:tid,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					if($('#inputname').html()=='你的邮箱'){
						$.cookie('email', inputvalue);
					}
					var paymsg = '';
					if(data.pay_alipay>0){
						paymsg+='<button class="w-full h-11 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold transition-colors shadow-sm mb-3" onclick="dopay(\'alipay\',\''+data.trade_no+'\')"><img src="../assets/img/alipay.png" class="w-5 h-5">支付宝支付</button>';
					}
					if(data.pay_qqpay>0){
						paymsg+='<button class="w-full h-11 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold transition-colors shadow-sm mb-3" onclick="dopay(\'qqpay\',\''+data.trade_no+'\')"><img src="../assets/img/qqpay.png" class="w-5 h-5">QQ钱包支付</button>';
					}
					if(data.pay_wxpay>0){
						paymsg+='<button class="w-full h-11 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold transition-colors shadow-sm mb-3" onclick="dopay(\'wxpay\',\''+data.trade_no+'\')"><img src="../assets/img/wxpay.png" class="w-5 h-5">微信支付</button>';
					}
					if (data.pay_rmb>0) {
						paymsg+='<button class="w-full h-11 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-bold transition-colors shadow-sm mb-3" onclick="dopay(\'rmb\',\''+data.trade_no+'\')"><img src="../assets/img/rmb.png" class="w-5 h-5">余额支付</button>';
					}
					if(data.paymsg!=null)paymsg+='<div class="mt-2 text-sm text-slate-600">' + data.paymsg + '</div>';
					
					// HuWangV7 重构支付弹窗
					layer.open({
						type: 1,
						title: '<div class="text-[15px] font-bold text-slate-800">确认付款</div>',
						skin: 'layui-layer-rim',
						area: ['320px', 'auto'],
						content: '<div class="p-5 text-center"><div class="text-[28px] font-black text-rose-500 mb-6">￥' + data.need + '</div>' + paymsg + '</div>',
						btn: ['取消订单'],
						yes: function(index){
							cancel(data.trade_no);
							layer.close(index);
						},
						success: function(layero) {
							$(layero).css({ "border-radius": "1rem", "box-shadow": "0 20px 25px -5px rgba(0, 0, 0, 0.1)", "border": "none" });
							$(layero).find('.layui-layer-title').css({ "border-bottom": "1px solid #f1f5f9", "background": "#fff" });
							$(layero).find('.layui-layer-btn').css({ "text-align": "center", "padding": "10px", "background": "#f8fafc", "border-top": "1px solid #f1f5f9" });
							$(layero).find('.layui-layer-btn0').css({ "background": "#fff", "border": "1px solid #e2e8f0", "color": "#64748b", "border-radius": "0.5rem", "width": "100%" });
						}
					});
					$.cookie('user_order', data.trade_no, { path: '/' });
				}else if(data.code == 1){
					$('#alert_frame').hide();
					if($('#inputname').html()=='你的邮箱'){
						$.cookie('email', inputvalue);
					}
					alert('领取成功！');
					window.location.href='?buyok=1';
				}else if(data.code == 2){
					if(data.type == 1){
						layer.open({
							type: 1,
							title: '完成验证',
							skin: 'layui-layer-rim',
							area: ['320px', '100px'],
							content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div><div id="captcha_wait"><div class="loading"><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div><div class="loading-dot"></div></div></div></div>',
							success: function(){
								$.getScript("D:/wangzhan/127.0.0.2/assets/vendor/static.geetest.com/static/tools/gt.js", function() {
									$.ajax({
										url: "../ajax.php?act=captcha&t=" + (new Date()).getTime(),
										type: "get",
										dataType: "json",
										success: function (data) {
											$('#captcha_text').hide();
											$('#captcha_wait').show();
											initGeetest({
												gt: data.gt,
												challenge: data.challenge,
												new_captcha: data.new_captcha,
												product: "popup",
												width: "100%",
												offline: !data.success
											}, handlerEmbed);
										}
									});
								});
							}
						});
					}else if(data.type == 2){
						layer.open({
							type: 1,
							title: '完成验证',
							skin: 'layui-layer-rim',
							area: ['320px', '260px'],
							content: '<div id="captcha" style="margin: auto;"><div id="captcha_text">正在加载验证码</div></div>',
							success: function(){
								$.getScript("D:/wangzhan/127.0.0.2/assets/vendor/cdn.dingxiang-inc.com/ctu-group/captcha-ui/index.js", function() {
									var myCaptcha = _dx.Captcha(document.getElementById('captcha'), {
										appId: data.appid,
										type: 'basic',
										style: 'embed',
										success: handlerEmbed2
									})
									myCaptcha.on('ready', function () {
										$('#captcha_text').hide();
									})
								});
							}
						});
					}else if(data.type == 3){
						layer.open({
							type: 1,
							title: '完成验证',
							skin: 'layui-layer-rim',
							area: ['320px', '231px'],
							content: '<div id="captcha"><div id="captcha_text">正在加载验证码</div></div>',
							success: function(){
								$.getScript("/assets/vendor/v.vaptcha.com/v3.js", function() {
									vaptcha({
										vid: data.appid,
										type: 'embed',
										container: '#captcha',
										offline_server: 'https://management.vaptcha.com/api/v3/demo/offline'
									}).then(handlerEmbed3);
								});
							}
						});
					}
				}else if(data.code == 3){
					layer.alert(data.msg, {
						closeBtn: false
					}, function(){
						window.location.reload();
					});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#submit_cart_shop").click(function(){
		var tid=$("#tid").val();
		if(tid==0){layer.alert('请选择商品！');return false;}
		var inputvalue=$("#inputvalue").val();
		if(inputvalue=='' || tid==''){layer.alert('请确保每项不能为空！');return false;}
		if($("#inputvalue2").val()=='' || $("#inputvalue3").val()=='' || $("#inputvalue4").val()=='' || $("#inputvalue5").val()==''){layer.alert('请确保每项不能为空！');return false;}
		if(($('#inputname').html()=='下单ＱＱ' || $('#inputname').html()=='ＱＱ账号' || $("#inputname").html() == 'QQ账号') && (inputvalue.length<5 || inputvalue.length>11 || isNaN(inputvalue))){layer.alert('请输入正确的QQ号！');return false;}
		var reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if($('#inputname').html()=='你的邮箱' && !reg.test(inputvalue)){layer.alert('邮箱格式不正确！');return false;}
		reg=/^[1][0-9]{10}$/;
		if($('#inputname').html()=='手机号码' && !reg.test(inputvalue)){layer.alert('手机号码格式不正确！');return false;}
		if($("#inputname2").html() == '说说ID'||$("#inputname2").html() == '说说ＩＤ'){
			if($("#inputvalue2").val().length != 24){layer.alert('说说必须是原创说说！');return false;}
		}
		checkInput();
		if($("#inputname").html() == '抖音作品ID'||$("#inputname").html() == '火山作品ID'||$("#inputname").html() == '火山直播ID'){
			if($("#inputvalue").val().length != 19){layer.alert('您输入的作品ID有误！');return false;}
		}
		if($("#inputname2").html() == '抖音评论ID'){
			if($("#inputvalue2").val().length != 19){layer.alert('您输入的评论ID有误！请点击自动获取手动选择评论！');return false;}
		}
		if($('#inputname').attr("gettype")=='shareurl'){
			if($("#inputvalue").val().indexOf('http://')==-1 && $("#inputvalue").val().indexOf('https://')==-1){
				layer.alert('您输入的链接有误！请重新输入！');return false;
			}
		}
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "../ajax.php?act=pay&method=cart_add",
			data : {tid:tid,inputvalue:$("#inputvalue").val(),inputvalue2:$("#inputvalue2").val(),inputvalue3:$("#inputvalue3").val(),inputvalue4:$("#inputvalue4").val(),inputvalue5:$("#inputvalue5").val(),num:$("#num").val(),hashsalt:hashsalt},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 0){
					if($('#inputname').html()=='你的邮箱'){
						$.cookie('email', inputvalue);
					}
					$('#cart_count').html(data.cart_count);
					$('#alert_cart').slideDown();
					layer.msg('添加至购物车成功~点击下方进入购物车列表结算');
				}else if(data.code == 3){
					layer.alert(data.msg, {
						closeBtn: false
					}, function(){
						window.location.reload();
					});
				}else{
					layer.alert(data.msg);
				}
			}
		});
	});
	$("#submit_query").click(function(){
		var qq=$("#qq3").val();
		var type=$("#searchtype").val();
		queryOrder(type,qq,1);
	});

	$("#num_add").click(function () {
		var i = parseInt($("#num").val());
		if ($("#need").val() == ''){
			layer.alert('请先选择商品');
			return false;
		}
		var multi = $('#tid option:selected').attr('multi');
		var count = parseInt($('#tid option:selected').attr('count'));
		if (multi == '0'){
			layer.alert('该商品不支持选择数量');
			return false;
		}
		i++;
		$("#num").val(i);
		var price = parseFloat($('#tid option:selected').attr('price'));
		var prices = $('#tid option:selected').attr('prices');
		if(prices!='' || prices!='null'){
			var discount = 0;
			$.each(prices.split(','), function(index, item){
				if(i>=parseInt(item.split('|')[0]))discount = parseFloat(item.split('|')[1]);
			});
			price = price - discount;
		}

		var mult = 1;
		$("input[act='getmulti']").each(function () {
			mult = mult * parseInt($(this).val());
		});

		price = price * i * mult;
		count = count * i;
		if(count>1)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
		else $('#need').val('￥'+price.toFixed(2) +"元");
	});
	$("#num_min").click(function (){
		var i = parseInt($("#num").val());
		if(i<=1){
			layer.msg('最低下单一份哦！');
			return false;
		}
		if ($("#need").val() == ''){
			layer.alert('请先选择商品');
			return false;
		}
		var multi = $('#tid option:selected').attr('multi');
		var count = parseInt($('#tid option:selected').attr('count'));
		if (multi == '0'){
			layer.alert('该商品不支持选择数量');
			return false;
		}
		i--;
		if (i <= 0) i = 1;
		$("#num").val(i);
		var price = parseFloat($('#tid option:selected').attr('price'));
		var prices = $('#tid option:selected').attr('prices');
		if(prices!='' || prices!='null'){
			var discount = 0;
			$.each(prices.split(','), function(index, item){
				if(i>=parseInt(item.split('|')[0]))discount = parseFloat(item.split('|')[1]);
			});
			price = price - discount;
		}

		var mult = 1;
		$("input[act='getmulti']").each(function () {
			mult = mult * parseInt($(this).val());
		});

		price = price * i * mult;
		count = count * i;
		if(count>1)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
		else $('#need').val('￥'+price.toFixed(2) +"元");
	});
	$("#num").keyup(function () {
		var i = parseInt($("#num").val());
		if(isNaN(i))return false;
		var price = parseFloat($('#tid option:selected').attr('price'));
		var count = parseInt($('#tid option:selected').attr('count'));
		var prices = $('#tid option:selected').attr('prices');
		if(i<1) $("#num").val(1);
		if(prices!='' || prices!='null'){
			var discount = 0;
			$.each(prices.split(','), function(index, item){
				if(i>=parseInt(item.split('|')[0]))discount = parseFloat(item.split('|')[1]);
			});
			price = price - discount;
		}

		var mult = 1;
		$("input[act='getmulti']").each(function () {
			mult = mult * parseInt($(this).val());
		});

		price = price * i * mult;
		count = count * i;
		if(count>1)$('#need').val('￥'+price.toFixed(2) +"元 ➠ "+count+"个");
		else $('#need').val('￥'+price.toFixed(2) +"元");
	});


	if($_GET['buyok']){
		var orderid = $_GET['orderid'];
		$("#tab-query").tab('show');
		$("#submit_query").click();
		isModal=false;
	}else if($_GET['chadan']){
		$("#tab-query").tab('show');
		isModal=false;
	}
	if($_GET['cid']){
		var cid = parseInt($_GET['cid']);
		$("#cid").val(cid);
	}
	$("#cid").change();


});
