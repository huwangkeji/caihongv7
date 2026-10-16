<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>投诉详情追踪处理</title>
  <link href="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/modernizr/2.8.3/modernizr.min.js"></script>
  <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/jquery/2.1.4/jquery.min.js"></script>
  <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/layer/3.1.1/layer.js"></script>
  <script src="../assets/V7/tailwindcss.js"></script>
  <!--[if lt IE 9]>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/lib.baomitu.com/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">

<?php 
include "../includes/common.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
adminpermission("workorder", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;

// 重写状态输出为 Tailwind 徽标
function display_status($status)
{
	if ($status == 1) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-100 text-xs font-semibold shadow-sm\"><i class=\"fa fa-circle-o-notch fa-spin mr-1\"></i>处理中</span>";
	} elseif ($status == 2) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 border border-emerald-100 text-xs font-semibold shadow-sm\"><i class=\"fa fa-check-circle mr-1\"></i>已结单归档</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded bg-rose-50 text-rose-600 border border-rose-100 text-xs font-semibold shadow-sm animate-pulse\"><i class=\"fa fa-exclamation-circle mr-1\"></i>待跟进处理</span>";
	}
}

if ($my == "view") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("当前记录不存在或已被系统抹除！", 3);
	}
	$contents = explode("*", $rows["content"]);
	$siterow = $DB->getRow("SELECT zid,qq,user FROM pre_site WHERE zid=:zid LIMIT 1", array(":zid" => $rows["zid"]));
	$myimg = '/assets/img/user.png';
	$kfimg = "https://imgcache.qq.com/open_proj/proj_qcloud_v2/mc_2014/work-order/css/img/custom-service-avatar.svg";
	?>

<div class="w-full max-w-[1000px] mx-auto space-y-6 pb-10 mt-4 px-4 sm:px-0">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        
        <!-- 头部看板 -->
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-exclamation-triangle text-rose-500 mr-2 text-lg"></i>投诉裁决详情
                <span class="ml-3 font-mono text-slate-400 text-sm">单号: #<?php echo $id; ?></span>
            </h3>
            <div class="flex items-center gap-3">
                <?php echo display_status($rows["status"]);?>
                <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200">
                    沟通回合: <?php echo count($contents);?> 轮
                </span>
            </div>
        </div>

        <div class="p-6">
            
            <!-- 客户首条投诉描述 (居左) -->
            <div class="flex gap-4 mb-8">
                <a href="./sitelist.php?zid=<?php echo $rows["zid"];?>" target="_blank" class="shrink-0 relative group">
                    <img src="<?php echo $myimg;?>" class="w-12 h-12 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition-transform" />
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
                </a>
                
                <div class="flex flex-col gap-1 max-w-[85%]">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-bold text-slate-700">用户发起投诉 <span class="font-normal text-slate-400 text-xs ml-1">(由 <?php echo $siterow ? "<a href=\"./sitelist.php?zid=" . $rows["zid"] . "\" target=\"_blank\" class=\"text-sky-500 hover:underline\">" . htmlspecialchars($siterow["user"], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</a>" : htmlspecialchars($rows["account"], ENT_QUOTES | ENT_HTML5, 'UTF-8');?> 提交)</span></span>
                        <span class="text-xs text-slate-400 font-mono"><?php echo $rows["addtime"];?></span>
                    </div>
                    
                    <div class="bg-slate-50 border border-slate-100 text-slate-700 p-4 rounded-2xl rounded-tl-sm text-sm leading-relaxed shadow-sm">
                        <div class="mb-3 space-y-1 pb-3 border-b border-slate-200/60">
                            <p class="m-0"><span class="text-slate-400 mr-2">投诉案卷归类:</span> <span class="font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded text-xs"><?php echo display_type($rows["type"]);?></span></p>
                            <p class="m-0"><span class="text-slate-400 mr-2">涉及纠纷单号:</span> <span class="font-mono text-slate-600 font-semibold"><?php echo $rows["orderid"] ? "<a href=\"./list.php?id=" . htmlspecialchars($rows["orderid"], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "\" target=\"_blank\" class=\"text-sky-500 hover:underline hover:text-sky-700\">" . htmlspecialchars($rows["orderid"], ENT_QUOTES | ENT_HTML5, 'UTF-8') . "</a>" : "<span class=\"text-slate-400 italic\">未关联任何订单号</span>";?></span></p>
                            <?php if($rows["picurl"]) { ?>
                            <p class="m-0 pt-1"><span class="text-slate-400 mr-2">上传的举证截图:</span> <a href="../<?php echo htmlspecialchars($rows["picurl"]); ?>" target="_blank" class="inline-flex items-center text-xs font-medium text-sky-600 hover:text-sky-800 hover:underline"><i class="fa fa-picture-o mr-1"></i>点此查看大图凭证</a></p>
                            <?php } ?>
                        </div>
                        <div class="whitespace-pre-wrap"><?php echo str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($contents[0], ENT_QUOTES | ENT_HTML5, 'UTF-8'));?></div>
                    </div>
                </div>
            </div>

            <!-- 后续跟进沟通时间轴 -->
            <div class="space-y-6 mb-8">
            <?php 
                for ($i = 1; $i < count($contents); $i++) {
                    $content = explode("^", $contents[$i]);
                    if (count($content) == 3) {
                        $isKf = $content[0] == 1;
                        if ($isKf) {
                            // 官方回复居右
                            echo '
                            <div class="flex gap-4 justify-end">
                                <div class="flex flex-col gap-1 max-w-[85%] items-end">
                                    <div class="flex items-center gap-2 mb-1 flex-row-reverse">
                                        <span class="text-sm font-bold text-slate-700">官方介入裁决 / 客服坐席</span>
                                        <span class="text-xs text-slate-400 font-mono">' . htmlspecialchars($content[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>
                                    </div>
                                    <div class="bg-brand-500 border border-brand-600 text-white p-4 rounded-2xl rounded-tr-sm text-sm leading-relaxed shadow-sm">
                                        <div class="whitespace-pre-wrap">' . str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($content[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</div>
                                    </div>
                                </div>
                                <div class="shrink-0 relative">
                                    <img src="' . $kfimg . '" class="w-12 h-12 rounded-full border-2 border-white shadow-sm" />
                                </div>
                            </div>';
                        } else {
                            // 用户追问居左
                            echo '
                            <div class="flex gap-4">
                                <a href="./sitelist.php?zid=' . $rows["zid"] . '" target="_blank" class="shrink-0 relative group">
                                    <img src="' . $myimg . '" class="w-12 h-12 rounded-full border-2 border-white shadow-sm group-hover:scale-105 transition-transform" />
                                </a>
                                <div class="flex flex-col gap-1 max-w-[85%]">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-bold text-slate-700">用户补充诉求 <span class="font-normal text-slate-400 text-xs ml-1">(' . htmlspecialchars($userrow["user"] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8') . ')</span></span>
                                        <span class="text-xs text-slate-400 font-mono">' . htmlspecialchars($content[1], ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</span>
                                    </div>
                                    <div class="bg-slate-50 border border-slate-100 text-slate-700 p-4 rounded-2xl rounded-tl-sm text-sm leading-relaxed shadow-sm">
                                        <div class="whitespace-pre-wrap">' . str_replace(array("\r\n", "\r", "\n"), "<br/>", htmlspecialchars($content[2], ENT_QUOTES | ENT_HTML5, 'UTF-8')) . '</div>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                }
            ?>
            </div>

            <!-- 工单状态及处理反馈区 -->
            <div class="pt-6 border-t border-slate-100">
            <?php if ($rows["status"] == 2) { ?>
                <div class="w-full py-8 text-center bg-slate-50 rounded-xl border border-slate-100 border-dashed">
                    <div class="w-16 h-16 mx-auto bg-slate-200 text-white rounded-full flex items-center justify-center mb-3 shadow-inner">
                        <i class="fa fa-lock text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-600 m-0 mb-1">该纠纷投诉案卷已被结单</h4>
                    <p class="text-sm text-slate-400 m-0">当前会话已冻结归档，如需继续处理纠纷请用户重新发起新的投诉卷宗。</p>
                </div>
            <?php } else { ?>
                
                <form action="./workorder-item.php?my=reply&id=<?php echo $id;?>" method="POST" class="space-y-4">
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">官方裁决与处理反馈回复 <span class="text-rose-500">*</span></label>
                        <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[160px] leading-relaxed" name="content" placeholder="输入回复内容后，此案卷状态将自动扭转为 [处理中]，并推送通知至分站站长..." required></textarea>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <label class="flex items-center cursor-pointer select-none group">
                            <input type="checkbox" name="email" id="email" value="1" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer transition-colors" />
                            <span class="ml-2 text-sm font-medium text-slate-600 group-hover:text-brand-600 transition-colors">同时触发送达提醒信件至该用户绑定的邮箱</span>
                        </label>
                    </div>

                    <!-- 快捷短语管理区 -->
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 shadow-inner space-y-3">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider"><i class="fa fa-bolt mr-1"></i>快捷回复模板库</span>
                            <div class="flex items-center gap-2">
                                <button type="button" class="inline-flex items-center justify-center px-3 py-1 rounded bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white font-medium text-xs transition-colors shadow-sm active:scale-95" id="addSpeedy">
                                    <i class="fa fa-save mr-1"></i>保存当前框内输入
                                </button>
                                <button type="button" class="inline-flex items-center justify-center px-3 py-1 rounded bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm active:scale-95" data-type="0" id="delSpeedy" title="开启移除模式以剔除无用短语模板">
                                    编辑/移除模板
                                </button>
                            </div>
                        </div>
                        
                        <div id="speedyList" data-ids="" class="flex flex-wrap gap-2 min-h-[32px]">
                            <?php 
                            $speedy_list = explode("^", $conf["speedy_list"]);
                            foreach ($speedy_list as $ikey => $icontent) {
                                if(empty(trim($icontent))) continue;
                                ?>		
                                <span class="speedy-text inline-flex cursor-pointer transition-transform hover:-translate-y-0.5">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-50 border border-sky-100 text-sky-600 font-medium text-[11px] shadow-sm select-none max-w-[200px] truncate transition-colors label-info" data-id="<?php echo $ikey;?>" title="<?php echo htmlspecialchars($icontent);?>"><?php echo htmlspecialchars($icontent);?></span>
                                </span>
                            <?php 
                            }
                            ?>	
                        </div>
                    </div>

                    <!-- 提交栏 -->
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" name="submit" class="flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold text-[15px] hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-paper-plane mr-1.5"></i>正式提交回复跟进
                        </button>
                        <button type="button" class="inline-flex items-center justify-center w-full sm:w-48 h-12 rounded-xl bg-emerald-500 text-white font-bold text-[15px] hover:bg-emerald-600 shadow-sm transition-all duration-200 active:scale-[0.98] shrink-0" onclick="window.location.href='./workorder-item.php?my=complete&id=<?php echo $id;?>'">
                            <i class="fa fa-check-square mr-1.5"></i>强行结单裁决
                        </button>
                    </div>

                </form>

<script>
// 快捷回复业务逻辑
$('#addSpeedy').on('click', function () {
	let text = $('textarea[name=content]').val();
	text = text.trim();
	if (text.length <= 0) {
		layer.msg('请在回复框内输入需要提取保存的模板内容片段', {icon: 7});
		return false;
	}
	layer.load(2, {shade: [0.1, '#fff']});
	$.ajax({
		url: 'ajax.php?act=add_speedy_text',
		type: 'POST',
		data: {content: text},
		dataType: 'json',
		success: function (res) {
			layer.closeAll('loading');
			if (res.code == 0) {
				let html = '<span class="speedy-text inline-flex cursor-pointer transition-transform hover:-translate-y-0.5"><span class="inline-flex items-center px-2.5 py-1 rounded-full bg-sky-50 border border-sky-100 text-sky-600 font-medium text-[11px] shadow-sm select-none max-w-[200px] truncate transition-colors label-info" data-id="'+res.id+'" title="'+res.content+'">'+res.content+'</span></span>';
				$('#speedyList').append(html);
				speedyTextClick();
                layer.msg('成功提取并追加至快捷库', {icon: 1});
			} else {
				layer.msg(res['msg'], {icon: 7});
			}
		},
		error: function (data) {
			layer.closeAll('loading');
			layer.msg('网络通信或核心发生异常', {icon: 7});
		}
	});
});

const speedyListDom = $('#speedyList');

$(document).ready(function () {
	speedyTextClick();
});

function speedyTextClick() {
	$('.speedy-text').off('click').on('click', function () {
		const type = $('#delSpeedy').attr('data-type');
		if (type === '0') { 
            // 写入文本模式
			$('textarea[name=content]').val($(this).children('span').text());
		} else { 
            // 标记删除模式
			let ids = speedyListDom.attr('data-ids');
			ids = ids.length > 0 ? ids.split(',') : [];
			ids.push($(this).children('span').attr('data-id'));
			speedyListDom.attr('data-ids', ids.join(','));
			$(this).fadeOut(200, function(){ $(this).remove(); });
		}
	});
}

$('#delSpeedy').on('click', function () {
	const type = $(this).attr('data-type');
	if (type === '0') {
		layer.tips('已开启移除态：点击下方短语即可标记准备删除', '#speedyList', {tips: [1, '#f43f5e']});
		$(this).html('<i class="fa fa-check mr-1"></i>提交剔除更改').attr('data-type', '1').removeClass('bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white').addClass('bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white');
		$('.label-info').removeClass('bg-sky-50 text-sky-600 border-sky-100').addClass('bg-rose-50 text-rose-600 border-rose-100 hover:bg-rose-100');
	} else {
		$(this).html('编辑/移除模板').attr('data-type', '0').removeClass('bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white').addClass('bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white');
		$('.label-info').removeClass('bg-rose-50 text-rose-600 border-rose-100 hover:bg-rose-100').addClass('bg-sky-50 text-sky-600 border-sky-100');
		const ids = speedyListDom.attr('data-ids');
		if (ids.length <= 0) return false;
		layer.load(2, {shade: [0.1, '#fff']});
		$.ajax({
			url: 'ajax.php?act=del_speedy_text',
			type: 'POST',
			data: {ids: ids},
			dataType: 'json',
			success: function (res) {
				layer.closeAll('loading');
				if (res.code == 0) {
					layer.msg('废弃短语模板已成功从系统库抹除', {icon: 1});
                    speedyListDom.attr('data-ids', '');
				} else {
					layer.msg(res.msg, {icon: 7});
				}
			},
			error: function (data) {
				layer.closeAll('loading');
				layer.msg('网络通信或核心发生异常', {icon: 7});
			}
		});
	}
});
</script>
            <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php 
} elseif ($my == "reply") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("该记录已被系统抹除或不存在！", 3);
	} elseif ($rows["status"] == 2) {
		showmsg("安全拦截：此投诉纠纷案卷已正式结单归档，拒绝覆盖写入！", 3);
	}
	$content = str_replace(array("*", "^", "|"), "", trim(strip_tags(daddslashes($_POST["content"]))));
	if (empty($content)) {
		showmsg("预检失败：请补充您所需下发的反馈信息正文！");
	} else {
		$content = $rows["content"] . "*1^" . $date . "^" . $content;
		if ($DB->exec("UPDATE pre_workorder SET content=:content,status=1 WHERE id=:id", array(":content" => $content, ":id" => $id)) !== false) {
			if ($_POST["email"] == 1) {
				send_msg_mail($id);
			}
			exit("<script language='javascript'>layer.msg('投诉反馈信息下发处理完成！正在回跳...', {icon:1, shade:[0.1,'#fff'], time:1500}, function(){history.go(-1);});</script>");
		} else {
			showmsg("底层异常：处理投诉回信写入主库流失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "complete") {
	$id = intval($_GET["id"]);
	$rows = $DB->getRow("SELECT * FROM pre_workorder WHERE id=:id LIMIT 1", array(":id" => $id));
	if (!$rows) {
		showmsg("该记录已被系统抹除或不存在！", 3);
	} elseif ($rows["status"] == 2) {
		showmsg("安全拦截：此投诉纠纷案卷本身即是完结归档状态！", 3);
	}
	if ($DB->exec("UPDATE pre_workorder SET status=2 WHERE id=:id", array(":id" => $id)) !== false) {
		exit("<script language='javascript'>layer.msg('强行完结封卷裁决操作成功！正在回跳...', {icon:1, shade:[0.1,'#fff'], time:1500}, function(){history.go(-1);});</script>");
	} else {
		showmsg("底层异常：强制覆盖完结状态失败！" . $DB->error(), 4);
	}
}

// 邮件投递提醒功能底层无缝挂载
function send_msg_mail($id)
{
	global $DB, $conf;
	$_var_0 = $DB->getRow("select * from pre_workorder where id=:id limit 1", array(':id' => $id));
	$_var_1 = $DB->getRow("select zid,user,qq from pre_site where zid=:zid limit 1", array(':zid' => $_var_0["zid"]));
	$_var_2 = $_var_1["qq"] . "@qq.com";
	$_var_3 = $conf["sitename"] . "售后支持投诉裁决待反馈提醒";
	$_var_4 = explode("*", $_var_0["content"]);
	$_var_4 = mb_substr($_var_4[0], 0, 16, "utf-8");
	$_var_5 = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"]);
	$_var_5 = substr($_var_5, 0, strrpos($_var_5, "/"));
	$_var_5 = substr($_var_5, 0, strrpos($_var_5, "/"));
	$_var_6 = (is_https() ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_var_5 . "/";
	$_var_7 = "尊敬的 " . $_var_1["user"] . "：<br/>您于 " . $_var_0["addtime"] . " 发起的售后投诉申诉卷宗 (处理案卷UID: " . $id . ") 需要您进一步配合提供或补充相关明细信息。请立即登录平台系统后台的“我的工单/投诉”模块查看裁决跟进详情并作出二次回复。系统风控机制：若超 72 小时您未提供进一步举证回信，该申诉将被视为自动和解妥投并由平台系统强行置为完结归档状态。<br/><br/>传送门直达链接：<a href=\"" . $_var_6 . "user/workorder.php?my=view&id=" . $id . "\" target=\"_blank\">点击此处跳转跟进记录</a><br/>申诉简述首条摘要：" . $_var_4 . "<br/><br/>----------------<br/>" . $conf["sitename"] . " 平台客服风控与合规中心系统发送，请勿直接回复此邮件。";
	if (checkEmail($_var_2)) {
		send_mail($_var_2, $_var_3, $_var_7);
	}
}

function display_type($type)
{
	global $conf;
	$_var_8 = explode("|", $conf["workorder_type"]);
	if ($type == 0 || !array_key_exists($type - 1, $_var_8)) {
		return "其他疑难杂症与违规投诉";
	} else {
		return $_var_8[$type - 1];
	}
}
?>
</body>
</html>
