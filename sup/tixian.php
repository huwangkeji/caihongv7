<?php

//decode by 安享
include "../includes/common.php";
$title = "余额提现";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

function display_zt($zt, $id)
{
	if ($zt == 2) {
		return "<a href=\"javascript:void(0)\" onclick=\"getResult(" . $id . ")\" class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-100 shadow-sm hover:bg-rose-500 hover:text-white transition-colors\">查看失败原因</a>";
	} elseif ($zt == 1) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 shadow-sm\">已完成</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-sky-50 text-sky-600 border border-sky-100 shadow-sm\">处理中</span>";
	}
}
function display_type($type)
{
	if ($type == 1) {
		return "<span class=\"text-emerald-600 font-bold\"><i class=\"fa fa-weixin mr-1\"></i>微信</span>";
	} elseif ($type == 2) {
		return "<span class=\"text-sky-500 font-bold\"><i class=\"fa fa-qq mr-1\"></i>QQ钱包</span>";
	} else {
		return "<span class=\"text-blue-500 font-bold\"><i class=\"fa fa-alipay mr-1\"></i>支付宝</span>";
	}
}

if ($conf["sup_tixian"] == 0) {
	showmsg("当前站点未开放提现功能！");
}

if (isset($_POST["money"])) {
	if (!checkRefererHost()) {
		exit;
	}
	$money = daddslashes(strip_tags($_POST["money"]));
	if (!is_numeric($money) || !preg_match("/^[0-9.]+\$/", $money)) {
		showmsg("提现金额输入不规范", 3);
	}
	$realmoney = round($money * $conf["sup_tixian_rate"] / 100, 2);
	if ($conf["sup_skimg"] == 1 && !file_exists(ROOT . "assets/img/skimg/sk_sup" . $suprow["sid"] . ".png")) {
		exit("<script>layer.msg('您还未上传收款图！', {icon:0, time:2000}, function(){window.location.href='uset.php?mod=skimg';});</script>");
	} elseif (empty($suprow["pay_account"]) || empty($suprow["pay_name"])) {
		exit("<script>layer.msg('您还未设置收款账号！', {icon:0, time:2000}, function(){history.go(-1);});</script>");
	}
	
	$DB->beginTransaction();
	$suprow = $DB->getRow("SELECT * FROM pre_supplier WHERE sid='" . $suprow["sid"] . "' LIMIT 1 FOR UPDATE");
	
	if ($money < $conf["sup_tixian_min"]) {
		exit("<script>layer.msg('单笔提现金额不能低于" . $conf["sup_tixian_min"] . "元！', {icon:2}, function(){history.go(-1);});</script>");
	}
	if ($money > $suprow["rmb"]) {
		exit("<script>layer.msg('提现金额不能大于您的余额！', {icon:2}, function(){history.go(-1);});</script>");
	}
	
	$sql = "INSERT INTO `pre_suptixian` (`sid`, `money`, `realmoney`, `pay_type`, `pay_account`, `pay_name`, `status`, `addtime`) VALUES (:sid, :money, :realmoney, :pay_type, :pay_account, :pay_name, 0, NOW())";
	$data = array(":sid" => $suprow["sid"], ":money" => $money, ":realmoney" => $realmoney, ":pay_type" => $suprow["pay_type"], ":pay_account" => $suprow["pay_account"], ":pay_name" => $suprow["pay_name"]);
	
	if ($DB->exec($sql, $data)) {
		if ($DB->exec("UPDATE pre_supplier SET rmb=rmb-:money WHERE sid=:sid", array(":money" => $money, ":sid" => $suprow["sid"]))) {
			addSupPointRecord($suprow["sid"], $money, "提现", "余额提现" . $money . "元");
			$DB->commit();
			exit("<script>layer.alert('提现操作成功！<br>本次实际到账金额: <b class=\"text-emerald-500 font-mono\">" . $realmoney . "</b> 元。<br>请等待管理员人工转账。', {icon:1, title:'提现申请已提交'}, function(){window.location.href='tixian.php';});</script>");
		} else {
			$DB->rollBack();
			exit("<script>layer.msg('提现操作失败！', {icon:2}, function(){location.href='tixian.php';});</script>");
		}
	} else {
		exit("<script>layer.msg('提现失败！', {icon:2}, function(){history.go(-1);});</script>");
	}
}

$numrows = $DB->getColumn("SELECT count(*) FROM pre_suptixian WHERE sid='" . $suprow["sid"] . "'");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

    <!-- 顶部布局：提现表单区与提示信息区双列 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- 左侧/主体：提现表单 -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden h-full flex flex-col">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center shrink-0">
                    <i class="fa fa-credit-card text-indigo-500 mr-2 text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">余额提现</h3>
                </div>
                
                <div class="p-6 flex-1 flex flex-col justify-center">
                    
                    <form method="post" class="space-y-6">
                        <input type="hidden" name="action" value="tixian">
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 ml-1">当前账户余额</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-bold">¥</div>
                                <input type="text" name="tmoney" class="w-full h-12 pl-10 pr-4 text-lg font-mono font-bold rounded-xl border border-slate-200 bg-slate-50 text-slate-500 cursor-not-allowed shadow-inner" value="<?php echo $suprow["rmb"];?>" disabled>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-700 ml-1">提取金额</label>
                            <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                                <div class="pl-4 flex items-center pointer-events-none text-slate-400 font-bold">¥</div>
                                <input type="text" name="money" class="flex-1 h-12 pl-2 pr-4 text-lg font-mono font-bold border-0 focus:ring-0 text-indigo-600 placeholder:text-slate-300 placeholder:font-normal" placeholder="输入提取金额" required>
                                <a href="javascript:inputMoney()" class="px-4 border-l border-slate-200 bg-slate-50 text-indigo-600 font-bold text-sm hover:bg-indigo-50 transition-colors flex items-center shrink-0">全部提取</a>
                            </div>
                        </div>

                        <div class="pt-4 mt-auto">
                            <button class="w-full h-14 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:from-indigo-600 hover:to-sky-700 active:scale-[0.98] transition-all" type="submit">
                                <i class="fa fa-paper-plane mr-1.5"></i>确认提现申请
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

        <!-- 右侧：状态与提示信息 -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- 提现费率提示卡片 -->
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start">
                    <i class="fa fa-info-circle text-amber-500 text-xl mt-0.5 mr-3 shrink-0"></i>
                    <div>
                        <h4 class="text-sm font-bold text-amber-800 mb-2">提现规则</h4>
                        <ul class="text-sm text-amber-700 space-y-1.5 font-medium m-0 pl-0 list-none">
                            <li class="flex items-center"><i class="fa fa-angle-right text-amber-400 mr-1.5"></i> 单笔提现最低金额：<b class="text-amber-800 ml-1">¥<?php echo $conf["sup_tixian_min"];?></b></li>
                            <li class="flex items-center"><i class="fa fa-angle-right text-amber-400 mr-1.5"></i> 平台收取手续费率：<b class="text-amber-800 ml-1"><?php echo 100 - $conf["sup_tixian_rate"];?>%</b></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 收款信息状态卡片 -->
            <div class="bg-sky-50 border border-sky-100 rounded-2xl p-6 shadow-sm flex-1">
                <div class="flex items-start mb-4">
                    <i class="fa fa-shield text-sky-500 text-xl mt-0.5 mr-3 shrink-0"></i>
                    <h4 class="text-sm font-bold text-sky-800 m-0 leading-relaxed">结算账号配置状态</h4>
                </div>
                
                <div class="text-sm text-sky-800 font-medium">
                <?php 
                if ($conf["sup_skimg"] == 1 && file_exists(ROOT . "assets/img/skimg/sk_sup" . $suprow["sid"] . ".png")) {
                ?>
                    <div class="bg-white/60 p-4 rounded-xl border border-sky-100 mb-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">结算方式</span>
                            <span><?php echo display_type($suprow["pay_type"]);?></span>
                        </div>
                        <div class="flex flex-col gap-2 pt-2 border-t border-sky-100/50">
                            <span class="text-slate-500 text-xs">当前收款图快照：</span>
                            <img onclick="img('<?php echo $suprow["sid"];?>')" src="<?php echo "../assets/img/skimg/sk_sup" . $suprow["sid"] . ".png";?>" class="h-24 w-auto rounded-lg border border-sky-200 shadow-sm cursor-pointer hover:opacity-80 transition-opacity object-contain bg-white">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="uset.php?mod=skimg" class="flex-1 text-center py-2 bg-amber-500 text-white rounded-lg text-xs font-bold hover:bg-amber-600 transition-colors shadow-sm">修改收款图</a>
                        <a href="uset.php?mod=user" class="flex-1 text-center py-2 bg-sky-600 text-white rounded-lg text-xs font-bold hover:bg-sky-700 transition-colors shadow-sm">修改提现方式</a>
                    </div>
                <?php 
                } elseif ($conf["sup_skimg"] == 1) {
                ?>
                    <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-100 mb-4 text-rose-600 text-center">
                        <i class="fa fa-exclamation-triangle mr-1"></i> 您还未上传收款图
                    </div>
                    <a href="uset.php?mod=skimg" class="block w-full text-center py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-colors shadow-sm">立即上传收款图</a>
                <?php 
                } elseif (!empty($suprow["pay_account"]) && !empty($suprow["pay_name"])) {
                ?>
                    <div class="bg-white/60 p-4 rounded-xl border border-sky-100 mb-4 space-y-2.5">
                        <div class="flex justify-between items-center border-b border-sky-100/50 pb-2">
                            <span class="text-slate-500">结算通道</span>
                            <span><?php echo display_type($suprow["pay_type"]);?></span>
                        </div>
                        <div class="flex justify-between items-center border-b border-sky-100/50 pb-2">
                            <span class="text-slate-500">收款账号</span>
                            <span class="font-mono text-slate-700"><?php echo $suprow["pay_account"];?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">真实姓名</span>
                            <span class="font-bold text-slate-700"><?php echo $suprow["pay_name"];?></span>
                        </div>
                    </div>
                    <a href="uset.php?mod=user" class="block w-full text-center py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-colors shadow-sm"><i class="fa fa-edit mr-1"></i>修改绑定信息</a>
                <?php 
                } else {
                ?>
                    <div class="bg-rose-50/50 p-4 rounded-xl border border-rose-100 mb-4 text-rose-600 text-center">
                        <i class="fa fa-exclamation-triangle mr-1"></i> 您还未绑定收款账号
                    </div>
                    <a href="uset.php?mod=user" class="block w-full text-center py-2 bg-amber-500 text-white rounded-lg text-sm font-bold hover:bg-amber-600 transition-colors shadow-sm">立即绑定账号</a>
                <?php 
                }
                ?>
                </div>
            </div>
            
        </div>
    </div>

    <!-- 底部：提现记录表格 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <i class="fa fa-history text-emerald-500 mr-2 text-xl"></i>
            <h3 class="text-base font-bold text-slate-800 m-0 tracking-tight">提现流水记录</h3>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center">流水号</th>
                        <th class="px-5 py-4 font-semibold text-center">申请金额</th>
                        <th class="px-5 py-4 font-semibold text-center">实际到账</th>
                        <th class="px-5 py-4 font-semibold text-center">通道</th>
                        <th class="px-5 py-4 font-semibold">收款账号</th>
                        <th class="px-5 py-4 font-semibold">姓名</th>
                        <th class="px-5 py-4 font-semibold text-center">申请时空</th>
                        <th class="px-5 py-4 font-semibold text-center">完结时空</th>
                        <th class="px-5 py-4 font-semibold text-center">当前状态</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php 
$rs = $DB->query("SELECT * FROM pre_suptixian WHERE sid='" . $suprow["sid"] . "' ORDER BY id DESC LIMIT 10");
$hasData = false;
while ($res = $rs->fetch()) {
    $hasData = true;
	echo "<tr class='hover:bg-slate-50/50 transition-colors group'>";
    echo "<td class='px-5 py-3.5 text-center'><span class='font-mono font-bold text-slate-500'>{$res['id']}</span></td>";
    echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-slate-700'>{$res['money']}</td>";
    echo "<td class='px-5 py-3.5 text-center font-mono font-bold text-emerald-600'>{$res['realmoney']}</td>";
    echo "<td class='px-5 py-3.5 text-center'>" . display_type($res["pay_type"]) . "</td>";
    echo "<td class='px-5 py-3.5 font-mono text-slate-600'>{$res['pay_account']}</td>";
    echo "<td class='px-5 py-3.5 font-bold text-slate-700'>{$res['pay_name']}</td>";
    echo "<td class='px-5 py-3.5 text-center font-mono text-[11px] text-slate-400'>{$res['addtime']}</td>";
    echo "<td class='px-5 py-3.5 text-center font-mono text-[11px] text-slate-400'>" . ($res["status"] == 1 ? $res["endtime"] : "<span class='text-slate-300'>-</span>") . "</td>";
    echo "<td class='px-5 py-3.5 text-center'>" . display_zt($res["status"], $res["id"]) . "</td>";
    echo "</tr>";
}

if(!$hasData){
    echo '<tr><td colspan="9" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-folder-open-o text-5xl mb-4 text-slate-200 block"></i>
            <p class="text-sm font-medium m-0">暂无提现流水记录</p>
          </td></tr>';
}
?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php 
include "./foot.php";
?>

<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
// 提取全部金额快捷填充
function inputMoney(){
	var tmoney = $("input[name='tmoney']").val();
	$("input[name='money']").val(tmoney.split(' ')[0]);
}

// 提取失败原因查询引擎
function getResult(id) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'POST',
		url : 'ajax_user.php?act=sup_tixian_note',
		data : {id:id},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.result != null){
				layer.alert(data.result, {
                    title: '<i class="fa fa-exclamation-circle text-rose-500 mr-1"></i>提取阻断报告',
                    skin: 'layui-layer-rim rounded-2xl',
                    icon: 0
                });
            } else {
				layer.alert('数据穿透失败，未能解析失败原因，请联系中枢节点（站长）处理。', {icon: 2, skin: 'layui-layer-rim rounded-2xl'});
            }
		},
		error:function(data){
            layer.close(ii);
			layer.msg('中枢节点响应异常', {icon: 2});
			return false;
		}
	});
}

// 图片预览查看器
function img(sid) {
    var imgSrc = "../assets/img/skimg/sk_sup" + sid + ".png";
    layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        skin: 'layui-layer-nobg',
        shadeClose: true,
        content: '<img src="' + imgSrc + '" class="max-w-[90vw] max-h-[90vh] rounded-2xl shadow-2xl border-4 border-white object-contain">'
    });
}
</script>
</body>
</html>
