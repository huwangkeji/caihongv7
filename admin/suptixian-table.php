<?php

include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("suptixian", 1);
$sql = " 1";
$link = "";
if (isset($_GET["status"]) && $_GET["status"] != "-1") {
	$status = intval($_GET["status"]);
	$sql .= " AND status='" . $status . "'";
	$link .= "&status=" . $status;
}
if (isset($_GET["type"]) && $_GET["type"] != "-1") {
	$type = intval($_GET["type"]);
	$sql .= " AND pay_type='" . $type . "'";
	$link .= "&type=" . $type;
}
if (isset($_GET["kw"])) {
	$kw = daddslashes($_GET["kw"]);
	$sql .= " AND (`pay_account`='" . $kw . "' or `pay_name`='" . $kw . "')";
	$link .= "&kw=" . $_GET["kw"];
}
$numrows = $DB->getColumn("SELECT count(*) from pre_suptixian WHERE" . $sql);

function display_zt($zt, $id)
{
	if ($zt == 2) {
		return "<a onclick=\"setResult(" . $id . ")\" title=\"点此修改失败原因\" class=\"inline-flex items-center px-2.5 py-1 rounded bg-rose-50 text-rose-600 border border-rose-100 text-[11px] font-semibold cursor-pointer hover:bg-rose-500 hover:text-white transition-colors\"><i class=\"fa fa-times-circle mr-1\"></i>交易失败 (驳回)</a>";
	} elseif ($zt == 1) {
		return "<span class=\"inline-flex items-center px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-semibold\"><i class=\"fa fa-check-circle mr-1\"></i>处理已完成</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2.5 py-1 rounded bg-amber-50 text-amber-600 border border-amber-100 text-[11px] font-semibold animate-pulse\"><i class=\"fa fa-clock-o mr-1\"></i>排队处理中</span>";
	}
}

function display_type($type)
{
	if ($type == 1) {
		return "<span class=\"inline-flex items-center text-emerald-600 font-bold text-xs\"><i class=\"fa fa-wechat mr-1\"></i>微信钱包</span>";
	} elseif ($type == 2) {
		return "<span class=\"inline-flex items-center text-sky-500 font-bold text-xs\"><i class=\"fa fa-qq mr-1\"></i>QQ 钱包</span>";
	} else {
		return "<span class=\"inline-flex items-center text-blue-600 font-bold text-xs\"><i class=\"fa fa-paypal mr-1\"></i>支付宝</span>";
	}
}
?>

<!-- 响应式表格容器 -->
<div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm bg-white mt-2">
    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-6 py-3.5 w-12 text-center">
                    <input type="checkbox" id="checkbox" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors" />
                </th>
                <th class="px-6 py-3.5 font-semibold text-center w-16">单号ID</th>
                <th class="px-6 py-3.5 font-semibold text-center w-20">供货商SID</th>
                <th class="px-6 py-3.5 font-semibold text-center w-28 text-emerald-600">申请提现额</th>
                <th class="px-6 py-3.5 font-semibold text-center w-28 text-rose-500">实际应到账</th>
                <th class="px-6 py-3.5 font-semibold text-center w-28">打款方式</th>
                <th class="px-6 py-3.5 font-semibold min-w-[150px]">收款账号</th>
                <th class="px-6 py-3.5 font-semibold text-center w-24">收款人姓名</th>
                <?php echo ($conf["sup_skimg"] ?? 0) == 1 ? "<th class=\"px-6 py-3.5 font-semibold text-center w-24\">预留收款码</th>" : null;?>
                <th class="px-6 py-3.5 font-semibold text-center w-36">发起时间</th>
                <th class="px-6 py-3.5 font-semibold text-center w-36">结单时间</th>
                <th class="px-6 py-3.5 font-semibold text-center w-32">当前状态</th>
                <th class="px-6 py-3.5 font-semibold text-center min-w-[120px]">代付回传信息</th>
                <th class="px-6 py-3.5 font-semibold text-center min-w-[280px]">动作与管理</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
<?php 
$pagesize = 30;
$pagesize = max(1, intval($pagesize));
$pages = max(1, (int)ceil($numrows / $pagesize));
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
if ($page < 1) { $page = 1; }
if ($page > $pages) { $page = $pages; }
$offset = $pagesize * ($page - 1);
$rs = $DB->query("SELECT * FROM pre_suptixian WHERE" . $sql . " ORDER BY id DESC limit " . $offset . "," . $pagesize);

$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	$addstr = "";
	if ($res["status"] == 0) {
		$addstr = "<a href=\"javascript:operation(" . $res["id"] . ",'complete')\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm\">核销完成</a> " . ($conf["sup_daifu"] > 0 && isset($_SESSION["transfer"]) ? "<a href=\"javascript:transfer(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded bg-brand-50 text-brand-600 hover:bg-brand-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm transfer_do\">接口代付</a> " : "") . "<a href=\"javascript:setfail(" . $res["id"] . ",'" . $res["money"] . "')\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm\">驳回失败</a> ";
	} elseif ($res["status"] == 1) {
		$addstr = "<a href=\"javascript:operation(" . $res["id"] . ",'reset')\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm\">撤销状态</a> ";
	}

	echo "<tr data-id=\"" . $res["id"] . "\" class=\"hover:bg-brand-50/40 transition-colors duration-200\">
            <td class=\"px-6 py-4 text-center\">
                <input type=\"checkbox\" id=\"" . $res["id"] . "\" value=\"\" name=\"data\" class=\"w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors\" />
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-700 font-mono\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono\">
                <a href=\"sitelist.php?sid=" . $res["sid"] . "\" target=\"_blank\" class=\"text-sky-600 hover:text-sky-800 hover:underline font-bold\">#" . $res["sid"] . "</a>
            </td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-emerald-600\">" . $res["money"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-rose-500 text-base\">" . $res["realmoney"] . "</td>
            <td class=\"px-6 py-4 text-center\">" . display_type($res["pay_type"]) . "</td>
            <td class=\"px-6 py-4 font-mono font-semibold text-slate-700\">
                <span onclick=\"inputInfo(" . $res["id"] . ")\" title=\"点击修改收款信息\" class=\"cursor-pointer hover:text-brand-600 hover:underline\">" . $res["pay_account"] . "</span>
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-800\">
                <span onclick=\"inputInfo(" . $res["id"] . ")\" title=\"点击修改身份信息\" class=\"cursor-pointer hover:text-brand-600 hover:underline\">" . $res["pay_name"] . "</span>
            </td>
            " . ($conf["sup_skimg"] == 1 ? "<td class=\"px-6 py-4 text-center\"><a href=\"javascript:skimg(" . $res["sid"] . ")\" class=\"text-xs text-sky-600 hover:underline font-medium\"><i class=\"fa fa-qrcode mr-1\"></i>预览</a></td>" : "") . "
            <td class=\"px-6 py-4 text-center font-mono text-xs text-slate-500\">" . $res["addtime"] . "</td>
            <td class=\"px-6 py-4 text-center font-mono text-xs text-brand-600\">" . ($res["status"] == 1 ? $res["endtime"] : "<span class=\"text-slate-300\">-</span>") . "</td>
            <td class=\"px-6 py-4 text-center status\">" . display_zt($res["status"], $res["id"]) . "</td>
            <td class=\"px-6 py-4 text-center text-xs font-mono text-slate-500 transfer_info truncate max-w-[150px]\"></td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\">
                <div class=\"inline-flex items-center gap-1.5 justify-center\">
                    " . $addstr . "
                    <a href=\"./suprecord.php?sid=" . $res["sid"] . "\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm\" target=\"_blank\">账单明细</a>
                    <a href=\"javascript:delItem(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-[11px] transition-colors shadow-sm\">抹除记录</a>
                </div>
            </td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="14" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-file-text-o text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">未检索到任何相关的提现提报记录</p>
          </td></tr>';
}
?>
        </tbody>
    </table>
</div>

<!-- 底部分页控制区 -->
<?php if(!$isEmpty): ?>
<div class="py-5 flex justify-center">
    <ul class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 m-0 list-none p-0">
    <?php 
    $first = 1;
    $prev = $page - 1;
    $next = $page + 1;
    $last = $pages;

    $btn_normal = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-200 transition-colors text-sm shadow-sm inline-block cursor-pointer font-medium";
    $btn_disabled = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-slate-100 bg-slate-50 text-slate-400 text-sm inline-block cursor-not-allowed font-medium opacity-60";
    $btn_active = "px-3 py-1.5 min-w-[32px] text-center rounded-lg border border-brand-500 bg-brand-500 text-white font-bold text-sm shadow-md inline-block cursor-default";

    if ($page > 1) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $first . $link . "')\" class=\"".$btn_normal."\">首页</a></li>";
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $prev . $link . "')\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-left\"></i></a></li>";
    } else {
        echo "<li><span class=\"".$btn_disabled."\">首页</span></li>";
        echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-left\"></i></span></li>";
    }
    
    $start = $page - 10 > 1 ? $page - 10 : 1;
    $end = $page + 10 < $pages ? $page + 10 : $pages;
    
    for ($i = $start; $i < $page; $i++) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
    }
    
    echo "<li><span class=\"".$btn_active."\">" . $page . "</span></li>";
    
    for ($i = $page + 1; $i <= $end; $i++) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $i . $link . "')\" class=\"".$btn_normal."\">" . $i . "</a></li>";
    }
    
    if ($page < $pages) {
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $next . $link . "')\" class=\"".$btn_normal."\"><i class=\"fa fa-angle-right\"></i></a></li>";
        echo "<li><a href=\"javascript:void(0)\" onclick=\"listTable('page=" . $last . $link . "')\" class=\"".$btn_normal."\">尾页</a></li>";
    } else {
        echo "<li><span class=\"".$btn_disabled."\"><i class=\"fa fa-angle-right\"></i></span></li>";
        echo "<li><span class=\"".$btn_disabled."\">尾页</span></li>";
    }
    ?>
    </ul>
</div>
<?php endif; ?>

<script type="text/html" id="config">
    <div class="p-6 bg-white space-y-5">
        <form action="" id="editform" method="post" role="form" class="space-y-5">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">通讯授权 Api_Id</label>
                <input type="text" name="id" value="<?php echo isset($conf["transfer_id"]) ? htmlspecialchars($conf["transfer_id"]) : "";?>" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入对接的主站授权 ID"/>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">通讯鉴权 Api_Key</label>
                <input type="text" name="key" value="<?php echo isset($conf["transfer_key"]) ? htmlspecialchars($conf["transfer_key"]) : "";?>" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="输入对接加密 Key"/>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">本站最高级支付密码</label>
                <input type="password" name="pass" value="" class="w-full h-11 px-4 text-sm font-mono rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm bg-white transition-colors" placeholder="输入当前控制台资金支付密码"/>
            </div>
            
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">代付通道汇款校验策略</label>
                <select name="check" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 cursor-pointer transition-colors">
                    <option value="FORCE_CHECK" <?php echo isset($conf["transfer_check"]) && $conf["transfer_check"] == "FORCE_CHECK" ? "selected=\"selected\"" : "";?> >强制进行收款实名一致性校验 (推荐)</option>
                    <option value="NO_CHECK" <?php echo isset($conf["transfer_check"]) && $conf["transfer_check"] == "NO_CHECK" ? "selected=\"selected\"" : "";?> >跳过收款真实姓名验证 (高风险)</option>
                </select>
            </div>
            
            <div class="pt-3">
                <button type="button" onclick="configEdit();" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium text-[15px] hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                    立即确认写入授权配置
                </button>
            </div>
        </form>
    </div>
</script>

<script type="text/javascript">
// 保留原始的 Javascript / AJAX / Layer 交互钩子
var transfer = function(id){
	var transfer = '<?php echo isset($_SESSION["transfer"]) ? $_SESSION["transfer"] : "";?>';
	if (!transfer) {
		layer.alert('前置阻断：请先配置转账接口授权信息', {icon:0});
		return false;
	}
	var load= layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type: "POST",
		url: "./ajax.php?act=transfer",
		data: {"id":id},
		dataType: "json",
		success: function(res){
			layer.close(load);
			layer.alert(res.msg, function(index){
				layer.close(index);
				if (res.code) listTable();
			});
		},error: function(){
			layer.close(load);
			layer.msg('服务器网络通信异常断开', {icon:2});
		}
	});
}

var config = function(){
	var html = $("#config").html()
	layer.open({
		type: 1,
		title: "自动转账接口授权配置中心",
        shade: 0.4,
        area: ['420px', 'auto'],
		content: html
	})
}

var configEdit = function () {
    var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type: "POST",
		url: "./ajax.php?act=transfer_config",
		data: $("#editform").serialize(),
		dataType: "json",
		success: function(res){
            layer.close(ii);
			layer.alert(res.msg, function(index){
				if (res.code) {
					location.reload();
				}else{
					layer.close(index);
				}
			});
		}
	});
}

var pl_config = function () {
	var transfer = '<?php echo isset($_SESSION["transfer"]) ? $_SESSION["transfer"] : "";?>';
	if (!transfer) {
		layer.alert('前置阻断：请先配置转账接口授权信息', {icon:0});
		return false;
	}
	if(confirm('危险操作提示：\n正在执行多路并发资金结算流，该操作不可逆、不可暂停！确认继续批量转账吗？')){
		var arrChk=$("input[name='data']:checked"); 
		if(arrChk.length<=0){
			layer.msg('请在列表中至少勾选一项待结算单', {icon:0});
			return false;
		}
		$(arrChk).each(function(){ 
			var id = this.id;
			$("table").find('tr[data-id="'+id+'"]').find('.transfer_info').html('<span class="text-amber-500 font-bold animate-pulse"><i class="fa fa-spinner fa-spin mr-1"></i>通讯排队处理中...</span>');
		});
		$(arrChk).each(function(){ 
			var id = this.id;
			$.ajax({
				type: "POST",
				url: "./ajax.php?act=transfer",
				data: {"id":id},
				dataType: "json",
				async: true,
				success: function(res){
					if (res.code) {
						$("table").find('tr[data-id="'+id+'"]').find('.status').html("<span class=\"inline-flex items-center px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 text-[11px] font-semibold\"><i class=\"fa fa-check-circle mr-1\"></i>处理已完成</span>");
						$("table").find('tr[data-id="'+id+'"]').find('.transfer_do').hide();
						var html = '<span class="text-emerald-500 text-xs">'+res.msg+'</span>';
					}else{
						var html = '<span class="text-rose-500 font-bold text-xs">'+res.msg+'</span>';
					}
					$("table").find('tr[data-id="'+id+'"]').find('.transfer_info').html(html);
				},error: function(){
					$("table").find('tr[data-id="'+id+'"]').find('.transfer_info').html('<span class="text-rose-500 font-bold text-xs">网络阻断/超时</span>');
				}
			});
		});
		return false;
	}else{
		return false;
	}
}

$(document).ready(function(){
	var checkboxes = document.getElementsByName('data');
	$("#checkbox").click(function() {
		for (var i = 0; i < checkboxes.length; i++) {
			var checkbox = checkboxes[i];
			if (!$(this).get(0).checked) {
				checkbox.checked = false;
			} else {
				checkbox.checked = true;
			}
		}
	});
})
</script>
