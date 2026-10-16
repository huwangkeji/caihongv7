<?php
/**
 * 分站与用户综合配置中枢
**/
require '../includes/common.php';

// 登录防泄漏隔离
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$title='配置中心';
include 'head.php';

// 确保底价逻辑稳定
if($conf['fenzhan_cost2']<=0)$conf['fenzhan_cost2']=$conf['fenzhan_price2'];
?>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
$mod=isset($_GET['mod'])?$_GET['mod']:null;

// ==========================================
// 逻辑处理分支：更新个人资料 (提交操作)
// ==========================================
if($mod=='user_n'){
	if(!checkRefererHost())exit();
	$qq=daddslashes(htmlspecialchars(strip_tags($_POST['qq'])));
	$pay_type=daddslashes(intval($_POST['pay_type']));
	$pay_account=daddslashes(htmlspecialchars(strip_tags($_POST['pay_account'])));
	$pay_name=daddslashes(htmlspecialchars(strip_tags($_POST['pay_name'])));
	$pwd=daddslashes(htmlspecialchars(strip_tags($_POST['pwd'])));
	
	if(!empty($pwd) && !preg_match('/^[a-zA-Z0-9\_\!\@\#\$~\%\^\&\*.,]+$/',$pwd)){
		exit("<script language='javascript'>layer.msg('拦截：安全密钥仅支持英文字母与数字组合', {icon:2, time:2000}, function(){history.go(-1);});</script>");
	}elseif(!preg_match('/^[0-9]{5,11}+$/', $qq)){
		exit("<script language='javascript'>layer.msg('拦截：溯源QQ格式解析失败！', {icon:2, time:2000}, function(){history.go(-1);});</script>");
	}else{
		$DB->exec("UPDATE pre_site SET qq=:qq,pay_type=:pay_type,pay_account=:pay_account,pay_name=:pay_name WHERE zid=:zid", [':qq'=>$qq, ':pay_type'=>$pay_type, ':pay_account'=>$pay_account, ':pay_name'=>$pay_name, ':zid'=>$userrow['zid']]);
		if(!empty($pwd))$DB->exec("update pre_site set pwd=:pwd where zid=:zid" ,[':pwd'=>$pwd, ':zid'=>$userrow['zid']]);
		exit("<script language='javascript'>layer.msg('您的私有资料库已成功更迭落盘！', {icon:1, time:1500}, function(){history.go(-1);});</script>");
	}
}

// ==========================================
// 视图分支：个人资料修改面板
// ==========================================
elseif($mod=='user'){
	$url = 'https://api.fcypay.com/';
    $m = md5(rand(1000000,9999999).date('YmdHis').uniqid());
    $code_url = $url.'get_openid_qrcode?mark='.$m;
    $cron_url = $url.'get_openid_status?mark='.$m;
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-id-card-o text-indigo-500 mr-2 text-xl"></i>站长建档资料维护
            </h3>
        </div>
        
        <div class="p-6">
            <form action="./uset.php?mod=user_n" method="post" class="space-y-5">
                
                <!-- 快捷登录授权区 -->
                <div class="space-y-3 pb-5 border-b border-slate-100">
                    <label class="text-sm font-semibold text-slate-700 ml-1">第三方凭证授信</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <?php if($conf['login_qq']==1){?>
                        <div class="flex-1 bg-sky-50 border border-sky-100 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="D:/wangzhan/127.0.0.2/assets/vendor/qzonestyle.gtimg.cn/qzone/vas/opensns/res/img/bt_blue_24X24.png" class="w-5 h-5">
                                <span class="text-sm font-bold text-sky-800">QQ直登</span>
                            </div>
                            <?php if($userrow['qq_openid']){?>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded"><i class="fa fa-check mr-1"></i>已接入</span>
                                    <a href="javascript:unbind('qq')" class="text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors">限制</a>
                                </div>
                            <?php }else{?>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-rose-500 bg-rose-100 px-2 py-0.5 rounded"><i class="fa fa-times mr-1"></i>未接通</span>
                                    <a href="javascript:connect('qq')" class="px-2 py-1 rounded bg-sky-500 text-white font-bold text-[11px] shadow-sm hover:bg-sky-600">一键绑定</a>
                                </div>
                            <?php }?>
                        </div>
                        <?php }?>
                        
                        <?php if($conf['login_wx']==1){?>
                        <div class="flex-1 bg-emerald-50 border border-emerald-100 rounded-xl p-3 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <img src="D:/wangzhan/127.0.0.2/assets/vendor/res.wx.qq.com/a/wx_fed/assets/res/OTE0YTAw.png" class="w-5 h-5">
                                <span class="text-sm font-bold text-emerald-800">微信直登</span>
                            </div>
                            <?php if($userrow['wx_openid']){?>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded"><i class="fa fa-check mr-1"></i>已接入</span>
                                    <a href="javascript:unbind('wx')" class="text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors">限制</a>
                                </div>
                            <?php }else{?>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold text-rose-500 bg-rose-100 px-2 py-0.5 rounded"><i class="fa fa-times mr-1"></i>未接通</span>
                                    <a href="javascript:connect('wx')" class="px-2 py-1 rounded bg-emerald-500 text-white font-bold text-[11px] shadow-sm hover:bg-emerald-600">一键绑定</a>
                                </div>
                            <?php }?>
                        </div>
                        <?php }?>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">超管名 (登录身份)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-user-circle"></i></div>
                            <input type="text" value="<?php echo htmlspecialchars($userrow['user']); ?>" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed focus:ring-0" disabled/>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">总台 QQ</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-sky-500"><i class="fa fa-qq"></i></div>
                            <input type="text" name="qq" value="<?php echo htmlspecialchars($userrow['qq']); ?>" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm transition-colors bg-white" placeholder="用于密码寻回与验证" required/>
                        </div>
                    </div>
                </div>

                <!-- 财务结算区 -->
                <?php if($userrow['power']>0){?>
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <label class="text-sm font-semibold text-slate-700 ml-1">结算配置</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <select name="pay_type" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-slate-50" data-default="<?php echo $userrow['pay_type']?>">
                                <?php if($conf['fenzhan_tixian_alipay']==1){?><option value="0">支付宝</option><?php }?>
                                <?php if($conf['fenzhan_tixian_wx']==1){?><option value="1">微信</option><?php }?>
                                <?php if($conf['fenzhan_tixian_qq']==1){?><option value="2">QQ钱包</option><?php }?>
                            </select>
                        </div>
                        
                        <div class="space-y-1.5">
                            <div class="relative flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                                <div class="pl-4 flex items-center pointer-events-none text-amber-500 font-bold"><i class="fa fa-credit-card"></i></div>
                                <input type="text" name="pay_account" value="<?php echo htmlspecialchars($userrow['pay_account']); ?>" class="flex-1 h-11 px-3 text-sm font-mono font-bold border-0 focus:ring-0 placeholder:text-slate-300 placeholder:font-normal" placeholder="下发目标账号">
                                <a href="javascript:getopenid()" id="getopenid" class="hidden px-3 bg-slate-50 border-l border-slate-200 text-indigo-600 font-bold text-xs hover:bg-slate-100 transition-colors flex items-center justify-center whitespace-nowrap">自动嗅探</a>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-address-card-o"></i></div>
                                <input type="text" name="pay_name" value="<?php echo htmlspecialchars($userrow['pay_name']); ?>" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-white" placeholder="核实打款实名"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php }?>
                
                <?php if(substr($userrow['user'],0,3)!='qq_'){?>
                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                    <label class="text-sm font-semibold text-slate-700 ml-1">安全锁 (重置后台密码)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500"><i class="fa fa-key"></i></div>
                        <input type="text" name="pwd" value="" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition-colors bg-rose-50/20 placeholder:text-slate-300 placeholder:font-normal" placeholder="无需变动此项请留空"/>
                    </div>
                </div>
                <?php }?>

                <div class="pt-4">
                    <button type="submit" name="submit" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold text-[15px] hover:from-indigo-600 hover:to-sky-600 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>上传档案并生效
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 异常身份补全通道 -->
    <?php if(substr($userrow['user'],0,3)=='qq_'){?>
    <div class="bg-amber-50 rounded-2xl shadow-sm border border-amber-200 overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-amber-200 bg-amber-100/50 flex items-center">
            <i class="fa fa-exclamation-triangle text-amber-600 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-amber-800 m-0 tracking-tight">身份档案补全</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-amber-700 font-medium leading-relaxed mb-4">当前系统探测到您仅依赖三方件授权访问。设定此处的独立用户名与密码后，您可以脱离三方依赖，并开启完整接口权限（且不会破坏现有的快捷授权链路）。</p>
            <form onsubmit="return setpwd()" method="post" class="space-y-4">
                <div class="space-y-1.5">
                    <div class="flex justify-between items-end">
                        <label class="text-sm font-semibold text-amber-800 ml-1">独立管理账号</label>
                        <span class="text-[10px] font-bold text-rose-500 bg-rose-100 px-1.5 py-0.5 rounded">仅可创建一次不可篡改</span>
                    </div>
                    <input type="text" name="user" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-amber-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white text-amber-900" placeholder="录入独享用户名" required/>
                </div>
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-amber-800 ml-1">独立登入安全锁</label>
                    <input type="text" name="pwd" class="w-full h-11 px-4 text-sm font-mono font-bold rounded-xl border-amber-300 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white text-amber-900" placeholder="设置不少于6位的密匙" required/>
                </div>
                <div class="pt-2">
                    <button type="submit" class="w-full h-12 rounded-xl bg-amber-500 text-white font-bold text-[15px] hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all active:scale-95">
                        <i class="fa fa-anchor mr-1.5"></i>定稿账号档案
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php }?>

<?php
}
// ==========================================
// 逻辑处理分支：更新站点配置 (提交操作)
// ==========================================
elseif($mod=='site_n' && $userrow['power']>0){
	if(!checkRefererHost())exit();
	$sitename=trim(htmlspecialchars(strip_tags($_POST['sitename'])));
	$title=trim(htmlspecialchars(strip_tags($_POST['title'])));
	$keywords=trim(htmlspecialchars(strip_tags($_POST['keywords'])));
	$description=trim(htmlspecialchars(strip_tags($_POST['description'])));
	$kfqq=isset($_POST['kfqq'])?trim(htmlspecialchars(strip_tags($_POST['kfqq']))):null;
	$kfwx=isset($_POST['kfwx'])?trim(htmlspecialchars(strip_tags($_POST['kfwx']))):null;
	$anounce=isset($_POST['anounce'])?$_POST['anounce']:'';
	$modal=isset($_POST['modal'])?$_POST['modal']:'';
	$bottom=isset($_POST['bottom'])?$_POST['bottom']:'';
	$alert=isset($_POST['alert'])?$_POST['alert']:'';
	$ktfz_price=trim($_POST['ktfz_price']);
	$ktfz_price2=trim($_POST['ktfz_price2']);
	$ktfz_domain=trim($_POST['ktfz_domain']);
	$template=isset($_POST['template'])?trim($_POST['template']):null;
	$appurl=trim($_POST['appurl']);
	
	if($sitename==null){
		exit("<script language='javascript'>layer.msg('阻断：站点名不允许被抹为空白！', {icon:2, time:2000}, function(){history.go(-1);});</script>");
	}else{
		if(!empty($template) && (!preg_match('/^[a-zA-Z0-9]+$/',$template) || \lib\Template::exists($template)==false)) exit("<script language='javascript'>layer.msg('探测失败：指定的前端容器模版包缺失！', {icon:2}); history.go(-1);</script>");
		
		// 严苛验证合伙人下发权限规则
		if($userrow['power']==2){
			if(!is_numeric($ktfz_price) || !preg_match('/^[0-9.]+$/', $ktfz_price) || $ktfz_price<0) exit("<script language='javascript'>layer.msg('普及版开通门槛价格校验失败', {icon:2}); history.go(-1);</script>");
			if(!is_numeric($ktfz_price2) || !preg_match('/^[0-9.]+$/', $ktfz_price2) || $ktfz_price2<0) exit("<script language='javascript'>layer.msg('专业版开通门槛价格校验失败', {icon:2}); history.go(-1);</script>");
			if($ktfz_price2<$conf['fenzhan_cost2']) exit("<script language='javascript'>layer.msg('倒挂阻断：专业版代理放单价严禁低于您的拿货底池！', {icon:2}); history.go(-1);</script>");
			if($ktfz_price2<$ktfz_price) exit("<script language='javascript'>layer.msg('定价荒谬：专业版不得低于普及版身价！', {icon:2}); history.go(-1);</script>");
			
			if($conf['fenzhan_edithtml']==1){
				$sql="UPDATE `pre_site` SET `sitename`=:sitename,`title`=:title,`keywords`=:keywords,`description`=:description,`kfqq`=:kfqq,`kfwx`=:kfwx,`anounce`=:anounce,`modal`=:modal,`bottom`=:bottom,`alert`=:alert,`ktfz_price`=:ktfz_price,`ktfz_price2`=:ktfz_price2,`ktfz_domain`=:ktfz_domain,`template`=:template,`appurl`=:appurl WHERE `zid`=:zid";
				$data = [':sitename'=>$sitename, ':title'=>$title, ':keywords'=>$keywords, ':description'=>$description, ':kfqq'=>$kfqq, ':kfwx'=>$kfwx, ':anounce'=>$anounce, ':modal'=>$modal, ':bottom'=>$bottom, ':alert'=>$alert, ':ktfz_price'=>$ktfz_price, ':ktfz_price2'=>$ktfz_price2, ':ktfz_domain'=>$ktfz_domain, ':template'=>$template, ':appurl'=>$appurl, ':zid'=>$userrow['zid']];
			}else{
				$sql="UPDATE `pre_site` SET `sitename`=:sitename,`title`=:title,`keywords`=:keywords,`description`=:description,`kfqq`=:kfqq,`kfwx`=:kfwx,`ktfz_price`=:ktfz_price,`ktfz_price2`=:ktfz_price2,`ktfz_domain`=:ktfz_domain,`template`=:template,`appurl`=:appurl WHERE `zid`=:zid";
				$data = [':sitename'=>$sitename, ':title'=>$title, ':keywords'=>$keywords, ':description'=>$description, ':kfqq'=>$kfqq, ':kfwx'=>$kfwx, ':ktfz_price'=>$ktfz_price, ':ktfz_price2'=>$ktfz_price2, ':ktfz_domain'=>$ktfz_domain, ':template'=>$template, ':appurl'=>$appurl, ':zid'=>$userrow['zid']];
			}
			$sds=$DB->exec($sql, $data);
		}else{
			if($conf['fenzhan_edithtml']==1){
				$sql="UPDATE `pre_site` SET `sitename`=:sitename,`title`=:title,`keywords`=:keywords,`description`=:description,`kfqq`=:kfqq,`kfwx`=:kfwx,`anounce`=:anounce,`modal`=:modal,`bottom`=:bottom,`alert`=:alert,`template`=:template,`appurl`=:appurl WHERE `zid`=:zid";
				$data = [':sitename'=>$sitename, ':title'=>$title, ':keywords'=>$keywords, ':description'=>$description, ':kfqq'=>$kfqq, ':kfwx'=>$kfwx, ':anounce'=>$anounce, ':modal'=>$modal, ':bottom'=>$bottom, ':alert'=>$alert, ':template'=>$template, ':appurl'=>$appurl, ':zid'=>$userrow['zid']];
			}else{
				$sql="UPDATE `pre_site` SET `sitename`=:sitename,`title`=:title,`keywords`=:keywords,`description`=:description,`kfqq`=:kfqq,`kfwx`=:kfwx,`template`=:template,`appurl`=:appurl WHERE `zid`=:zid";
				$data = [':sitename'=>$sitename, ':title'=>$title, ':keywords'=>$keywords, ':description'=>$description, ':kfqq'=>$kfqq, ':kfwx'=>$kfwx, ':template'=>$template, ':appurl'=>$appurl, ':zid'=>$userrow['zid']];
			}
			$sds=$DB->exec($sql, $data);
		}
		
		if($sds!==false) exit("<script language='javascript'>layer.msg('外显装潢配置成功下发！', {icon:1, time:1500}, function(){history.go(-1);});</script>");
		else exit("<script language='javascript'>layer.alert('落盘溃散:".$DB->error()."', {icon:2}); history.go(-1);</script>");
	}
}

// ==========================================
// 视图分支：站点前台运营配置面板
// ==========================================
elseif($mod=='site' && $userrow['power']>0){
	$mblist = \lib\Template::getList();
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center shrink-0">
                <i class="fa fa-desktop text-sky-500 mr-2 text-xl"></i>站点配置与运营中枢
            </h3>
            <div class="flex gap-2 text-xs font-bold text-slate-500">
                <a href="http://www.w3school.com.cn/tiy/t.asp?f=html_basic" target="_blank" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-code mr-1"></i>HTML实验室</a>
                <a href="https://picui.cn" target="_blank" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-picture-o mr-1"></i>图床</a>
            </div>
        </div>
        
        <div class="p-6">
            <form action="./uset.php?mod=site_n" method="post" class="space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">独立站点名称</label>
                        <input type="text" name="sitename" value="<?php echo htmlspecialchars($userrow['sitename']); ?>" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white" required/>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">名称后缀 (Title)</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($userrow['title']); ?>" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white"/>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">SEO关键词 (Keywords)</label>
                    <input type="text" name="keywords" value="<?php echo htmlspecialchars($userrow['keywords']); ?>" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white"/>
                </div>

                <div class="space-y-1.5 border-b border-slate-100 pb-5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">SEO描述 (Description)</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($userrow['description']); ?>" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white"/>
                </div>

                <!-- 联络总线 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <?php if($conf['fenzhan_kfqq']==1){?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">售后 Q Q</label>
                        <input type="text" name="kfqq" value="<?php echo htmlspecialchars($userrow['kfqq']); ?>" class="w-full h-11 px-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white placeholder:font-normal placeholder:text-slate-300" placeholder="留空则默认主站配置"/>
                    </div>
                    <?php if($newuserhead){?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">微信售后链接</label>
                        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-sky-500 focus-within:ring-1 focus-within:ring-sky-500 transition-all bg-white">
                            <input type="text" name="kfwx" value="<?php echo htmlspecialchars($userrow['kfwx']); ?>" class="flex-1 h-11 px-4 text-sm font-mono font-bold border-0 focus:ring-0 placeholder:font-normal placeholder:text-slate-300" placeholder="留空则默认主站配置"/>
                            <a href="./uset.php?mod=upwxqrcode" class="px-4 h-11 flex items-center justify-center bg-slate-50 border-l border-slate-200 text-sky-600 font-bold text-xs hover:bg-slate-50 transition-colors whitespace-nowrap">上传二维码</a>
                        </div>
                    </div>
                    <?php }}?>
                </div>

                <!-- 高定 HTML 区块 -->
                <?php if($conf['fenzhan_edithtml']==1){?>
                <div class="space-y-5 pt-4 border-t border-slate-100">
                    <div class="text-xs font-bold text-indigo-500 bg-indigo-50 px-3 py-1.5 rounded-lg w-fit"><i class="fa fa-html5 mr-1.5"></i>底层 HTML 渲染矩阵授权开启</div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">外显广播告示</label>
                        <textarea name="anounce" rows="4" class="w-full p-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 leading-relaxed placeholder:font-normal placeholder:text-slate-300" placeholder="若留白，该维面将被主站公文完全覆盖"><?php echo htmlspecialchars($userrow['anounce']);?></textarea>
                    </div>
                    
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700 ml-1">前台公告弹窗</label>
                            <select name="modal_show_type" class="h-8 px-2 text-xs font-bold rounded-lg border-slate-200 focus:border-indigo-500 shadow-sm bg-white" data-default="<?php echo isset($userrow['modal_show_type']) ? $userrow['modal_show_type'] : '0';?>">
                                <option value="0">每次都弹出</option>
                                <option value="1">仅弹出一次</option>
                            </select>
                        </div>
                        <textarea name="modal" rows="4" class="w-full p-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 leading-relaxed placeholder:font-normal placeholder:text-slate-300" placeholder="空置则由主站配置..."><?php echo htmlspecialchars($userrow['modal']);?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">页脚代码</label>
                            <textarea name="bottom" rows="4" class="w-full p-3 text-xs font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 leading-relaxed placeholder:font-normal placeholder:text-slate-300"><?php echo htmlspecialchars($userrow['bottom']);?></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">下单提示代码</label>
                            <textarea name="alert" rows="4" class="w-full p-3 text-xs font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-inner bg-slate-50 leading-relaxed placeholder:font-normal placeholder:text-slate-300"><?php echo htmlspecialchars($userrow['alert']);?></textarea>
                        </div>
                    </div>
                </div>
                <?php }?>
                
                <!-- 架构级财务下发设定 (仅限合伙人 Pro 版) -->
                <?php if($userrow['power']==2){?>
                <div class="space-y-5 pt-4 border-t border-slate-100">
                    <div class="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1.5 rounded-lg w-fit border border-amber-100"><i class="fa fa-diamond mr-1.5"></i>分站类别定价</div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">基础版定价</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="ktfz_price" value="<?php echo $userrow['ktfz_price']>0?$userrow['ktfz_price']:$conf['fenzhan_price']; ?>" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white" required/>
                            </div>
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700 ml-1">专业版定价</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500 font-bold"><i class="fa fa-cny"></i></div>
                                <input type="text" name="ktfz_price2" value="<?php echo $userrow['ktfz_price2']>$conf['fenzhan_cost2']?$userrow['ktfz_price2']:$conf['fenzhan_price2']; ?>" class="w-full h-11 pl-9 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white" required/>
                            </div>
                            <p class="text-[10px] text-rose-500 ml-1 font-medium"><i class="fa fa-warning mr-0.5"></i>限制：不可低于硬成本 ¥ <?php echo $conf['fenzhan_cost2']?></p>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">下级分裂限定域名池分配</label>
                        <input type="text" name="ktfz_domain" value="<?php echo htmlspecialchars($userrow['ktfz_domain']); ?>" class="w-full h-11 px-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm bg-white placeholder:text-slate-300 placeholder:font-normal" placeholder="留空则继承主域名；输入多域用小写逗号区隔"/>
                    </div>
                </div>
                <?php }?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2 border-t border-slate-100">
                    <?php if($conf['fenzhan_template']==1){?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">前台模版配置</label>
                        <select name="template" class="w-full h-11 px-4 text-sm font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white">
                            <option value="0">默认</option>
                            <?php foreach($mblist as $row){
                                echo '<option value="'.$row.'" '.($userrow['template']==$row?'selected':null).'>前台模版：'.$row.'</option>';
                            }?>
                        </select>
                    </div>
                    <?php }?>
                    
                    <?php if($conf['fenzhan_editd']>0){?>
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">您的域名</label>
                        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 transition-all bg-slate-50">
                            <input type="text" name="domain" value="<?php echo htmlspecialchars($userrow['domain']); ?>" class="flex-1 h-11 px-4 text-sm font-mono font-bold border-0 bg-transparent text-slate-500 shadow-inner cursor-not-allowed focus:ring-0" disabled/>
                            <a href="cdomain.php" class="px-4 h-11 flex items-center justify-center bg-white border-l border-slate-200 text-sky-600 font-bold text-xs hover:bg-slate-50 transition-colors whitespace-nowrap">更换</a>
                        </div>
                    </div>
                    <?php }?>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">客户端下载链接 (APP URL)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-android"></i></div>
                        <input type="text" name="appurl" value="<?php echo htmlspecialchars($userrow['appurl']); ?>" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm bg-white placeholder:font-normal placeholder:text-slate-300" placeholder="留空则隐藏分发入口"/>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" name="submit" class="w-full h-14 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 text-white font-bold text-lg shadow-md shadow-sky-500/20 hover:from-sky-600 hover:to-indigo-700 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-upload mr-2"></i>保存
                    </button>
                </div>

            </form>
        </div>
    </div>
<?php
}

// ==========================================
// 视图分支：前置 LOGO 图片覆写上传
// ==========================================
elseif($mod=='logo' && $userrow['power']>0 && $conf['fenzhan_edithtml']==1){
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <i class="fa fa-picture-o text-indigo-500 mr-2 text-xl"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">LOGO配置</h3>
        </div>
        
        <div class="p-6">
            <div class="mb-5 bg-sky-50 border border-sky-100 p-4 rounded-xl text-sm text-sky-800 font-medium flex items-start">
                <i class="fa fa-lightbulb-o text-xl text-amber-500 mr-3 mt-0.5 shrink-0"></i>
                <p class="m-0 leading-relaxed">请注意：部分采用极简框架的模板可能在结构上移除了 LOGO 的代码，若替换后未展现属正常现象。<br><b class="text-rose-500 mt-1 block">若更换后无效果，请按下 `Ctrl+F5` 进行强制刷新（缓存清理）。</b></p>
            </div>
            
            <?php
            if(isset($_POST['s']) && $_POST['s']==1){
            	if(!checkRefererHost())exit();
            	
            	$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
            	$file_type = $_FILES["file"]["type"];
            	$file_ext = strtolower(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
            	$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            	$max_size = 2 * 1024 * 1024;
            	
            	if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>解构失败：仅能解析 JPG, PNG, GIF, WEBP 流！</div>";
            	} elseif ($_FILES["file"]["size"] > $max_size) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>容量阻断：文件体积越界，顶格限额 2MB！</div>";
            	} elseif (!getimagesize($_FILES["file"]["tmp_name"])) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>伪造拦截：未监测到合法的光栅特征！</div>";
            	} else {
            	    copy($_FILES['file']['tmp_name'], ROOT.'assets/img/logo_'.$userrow['zid'].'.png');
            	    echo "<div class='mb-4 p-3 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-bold border border-emerald-100'><i class='fa fa-check-circle mr-1.5'></i>视觉数据推流完成，门面已迭代更新！</div>";
            	}
            }
            
            $logo = file_exists(ROOT.'assets/img/logo_'.$userrow['zid'].'.png') ? '../assets/img/logo_'.$userrow['zid'].'.png' : '../assets/img/logo.png';
            ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <form action="uset.php?mod=logo" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="s" value="1" />
                    <div class="w-full relative border-2 border-dashed border-indigo-200 rounded-2xl p-6 text-center hover:bg-indigo-50/50 transition-colors">
                        <input type="file" name="file" id="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required/>
                        <i class="fa fa-cloud-upload text-4xl text-indigo-300 mb-2"></i>
                        <p class="text-sm font-bold text-indigo-700 m-0">拖拽或点击唤醒本地库进行图文替换</p>
                        <p class="text-[10px] text-indigo-400 mt-1 m-0">限幅 2MB 图像流 (PNG/JPG/WEBP)</p>
                    </div>
                    <button type="submit" class="w-full h-12 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-md shadow-indigo-500/20 active:scale-95 transition-all">
                        <i class="fa fa-bolt mr-1.5"></i>上传
                    </button>
                </form>
                
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-100 rounded-2xl">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2 w-full text-center">当前快照展示</span>
                    <div class="bg-slate-800/10 p-4 rounded-xl checkerboard-bg">
                        <img src="<?php echo $logo; ?>?v=<?php echo time(); ?>" class="max-w-full max-h-32 object-contain filter drop-shadow-md">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
<?php
}

// ==========================================
// 视图分支：提现收款图覆盖传输
// ==========================================
elseif($mod=='skimg' && $userrow['power']>0){
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center">
            <i class="fa fa-money text-emerald-500 mr-2 text-xl"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">收款图配置</h3>
        </div>
        
        <div class="p-6">
            <?php
            if(isset($_POST['s']) && $_POST['s']==1){
            	if(!checkRefererHost())exit();
            	
            	$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
            	$file_type = $_FILES["shoukuan"]["type"];
            	$file_ext = strtolower(pathinfo($_FILES["shoukuan"]["name"], PATHINFO_EXTENSION));
            	$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            	$max_size = 5 * 1024 * 1024;
            	
            	if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>解构失败：仅能解析 JPG, PNG, GIF, WEBP 流！</div>";
            	} elseif ($_FILES["shoukuan"]["size"] > $max_size) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>容量阻断：文件体积越界，顶格限额 5MB！</div>";
            	} elseif (!getimagesize($_FILES["shoukuan"]["tmp_name"])) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>伪造拦截：未监测到合法的光栅特征！</div>";
            	} else {
            	    copy($_FILES['shoukuan']['tmp_name'], ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png');
            	    echo "<div class='mb-4 p-3 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-bold border border-emerald-100'><i class='fa fa-check-circle mr-1.5'></i>结算终端上传完毕，图码已录入中枢！</div>";
            	}
            }
            
            $logo = file_exists(ROOT.'assets/img/skimg/sk_'.$userrow['zid'].'.png') ? '../assets/img/skimg/sk_'.$userrow['zid'].'.png' : '../assets/img/skimg/sk.png';
            ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <form action="uset.php?mod=skimg" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div class='mb-4 p-3 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-bold border border-emerald-100'><i class='fa fa-check-circle mr-1.5'></i>请选择好图片后等待5秒点击确认上传！</div>
                    <input type="hidden" name="s" value="1" />
                    
                    <div class="w-full relative border-2 border-dashed border-emerald-200 rounded-2xl p-6 text-center hover:bg-emerald-50/50 transition-colors">
                    
                        <input type="file" name="shoukuan" id="shoukuan" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required/>
                        <i class="fa fa-qrcode text-4xl text-emerald-300 mb-2"></i>
                        <p class="text-sm font-bold text-emerald-700 m-0">上传您的收款二维码</p>
                        <p class="text-[10px] text-emerald-500 mt-1 m-0">图片文件5MB，系统自动压缩入库</p>
                    </div>
                    <button type="submit" class="w-full h-12 rounded-xl bg-emerald-500 text-white font-bold text-[15px] hover:bg-emerald-600 shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                        <i class="fa fa-upload mr-1.5"></i>确认上传
                    </button>
                </form>
                
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-100 rounded-2xl h-full">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2 w-full text-center">当前二维码</span>
                    <div class="p-2 bg-white rounded-xl shadow-sm border border-slate-200">
                        <img src="<?php echo $logo; ?>?v=<?php echo time(); ?>" class="max-w-full max-h-40 object-contain rounded-lg">
                    </div>
                </div>
            </div>
            
        </div>
    </div>
<?php
}

// ==========================================
// 视图分支：微信联络网关二维码设定
// ==========================================
elseif($mod=='upwxqrcode' && $userrow['power']>0 && $conf['fenzhan_kfqq']==1){
	if(isset($_GET['del']) && $_GET['del']==1){
		if(file_exists(ROOT.'assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png')){
			unlink(ROOT.'assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png');
		}
		exit("<script language='javascript'>layer.msg('物理销毁确认', {icon:1, time:1000}, function(){window.location.href='./uset.php?mod=upwxqrcode';});</script>");
	}
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-weixin text-emerald-500 mr-2 text-xl"></i>前台微信配置
            </h3>
            <a href="./uset.php?mod=site" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-reply mr-1"></i>返回配置台</a>
        </div>
        
        <div class="p-6">
            <?php
            if(isset($_POST['s']) && $_POST['s']==1){
            	if(!checkRefererHost())exit();
            	
            	$allowed_types = array('image/jpeg', 'image/png', 'image/gif', 'image/webp');
            	$file_type = $_FILES["wxqrcode"]["type"];
            	$file_ext = strtolower(pathinfo($_FILES["wxqrcode"]["name"], PATHINFO_EXTENSION));
            	$allowed_exts = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            	$max_size = 2 * 1024 * 1024;
            	
            	if (!in_array($file_type, $allowed_types) || !in_array($file_ext, $allowed_exts)) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>解构失败：受限的文件格式流！</div>";
            	} elseif ($_FILES["wxqrcode"]["size"] > $max_size) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>拦截：负荷超越了2MB的预设天花板！</div>";
            	} elseif (!getimagesize($_FILES["wxqrcode"]["tmp_name"])) {
            		echo "<div class='mb-4 p-3 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold border border-rose-100'><i class='fa fa-ban mr-1.5'></i>虚假文件阻断。</div>";
            	} else {
            	    copy($_FILES['wxqrcode']['tmp_name'], ROOT.'assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png');
            	    echo "<div class='mb-4 p-3 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-bold border border-emerald-100'><i class='fa fa-check-circle mr-1.5'></i>通讯码点接驳完毕，终端页面同步渲染！</div>";
            	}
            }
            ?>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <form action="uset.php?mod=upwxqrcode" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="s" value="1" />
                    <div class="w-full relative border-2 border-dashed border-emerald-200 rounded-2xl p-6 text-center hover:bg-emerald-50/50 transition-colors">
                        <input type="file" name="wxqrcode" id="wxqrcode" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required/>
                        <i class="fa fa-wechat text-4xl text-emerald-300 mb-2"></i>
                        <p class="text-sm font-bold text-emerald-700 m-0">拖挂您的微信个人交友码</p>
                        <p class="text-[10px] text-emerald-500 mt-1 m-0">仅在总台准许时才会暴露在客户界面</p>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 h-11 rounded-xl bg-emerald-500 text-white font-bold text-sm hover:bg-emerald-600 shadow-md shadow-emerald-500/20 active:scale-95 transition-all">
                            <i class="fa fa-upload mr-1.5"></i>提交
                        </button>
                        <?php if(file_exists(ROOT.'assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png')){ ?>
                        <a href="javascript:void(0)" onclick="delWxQrcode()" class="px-4 h-11 flex items-center justify-center rounded-xl bg-rose-50 text-rose-600 font-bold text-sm hover:bg-rose-500 hover:text-white transition-colors border border-rose-100 shadow-sm shrink-0">
                            <i class="fa fa-trash"></i>
                        </a>
                        <?php } ?>
                    </div>
                </form>
                
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-100 rounded-2xl h-full">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 border-b border-slate-200 pb-2 w-full text-center">解析器当前确定的链接</span>
                    <div class="text-center w-full">
                    <?php
                    if(file_exists(ROOT.'assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png')){
                    	echo '<div class="p-2 bg-white rounded-xl shadow-sm border border-slate-200 mx-auto w-fit"><img src="../assets/img/qrcode/wxqrcode_'.$userrow['zid'].'.png?v='.time().'" class="max-w-[120px] max-h-[120px] object-contain rounded-lg"></div>';
                    }elseif(!empty($userrow['kfqq'])){
                    	echo '<div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa fa-qq text-3xl text-sky-500"></i></div><span class="text-sm font-bold text-sky-700 bg-sky-50 px-3 py-1.5 rounded-lg border border-sky-100">将借由QQ通道动态运算</span>';
                    }else{
                    	echo '<div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa fa-sitemap text-3xl text-slate-400"></i></div><span class="text-sm font-bold text-slate-600 bg-white px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm">继承总台全局配置参数</span>';
                    }
                    ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
<?php } ?>

</div>

<?php include './foot.php';?>

<script src="<?php echo $cdnpublic?>jquery.qrcode/1.0/jquery.qrcode.min.js"></script>

<?php if($mod=='user'){?>
<script>
// 初始化默认选中的 select
var items = $("select[data-default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("data-default")||0);
}

<?php if($conf['fenzhan_daifu']==1){?>
// 三方网关收款获取
var getopenid = function () {
    var open = layer.open({
        type: 1,
        title: false,
        closeBtn: 0,
        skin: 'layui-layer-rim rounded-2xl overflow-hidden',
        content: '<div class="p-6 bg-white flex flex-col items-center"><h3 class="text-lg font-bold text-emerald-600 mb-4 tracking-tight"><i class="fa fa-wechat mr-2"></i>端点握手: 请切至微信扫码</h3><div class="p-2 bg-slate-50 border border-slate-200 rounded-xl shadow-inner"><div id="qrcode"></div></div><button onclick="layer.closeAll()" class="mt-5 px-6 py-2 rounded-lg bg-slate-100 text-slate-600 font-bold text-sm hover:bg-slate-200 transition-colors">取消握手</button></div>',
        cancel: function(index, layero){ 
            layer.close(open);
            window.clearInterval(cron); 
        },
        success: function(){ 
			var code_url = '<?php echo $code_url?>';
			$('#qrcode').qrcode({
				text: code_url,
				width: 200,
				height: 200,
				foreground: "#059669",
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
                    layer.msg('探测仪捕获目标串码，已成功填充', {icon:1});
                }
            }
        });
    }, 3000);
}

$("select[name='pay_type']").change(function(){
	if($(this).val() == 1){
		$("#getopenid").removeClass('hidden').addClass('flex');
		$("input[name=pay_account]").attr("readOnly","readOnly");
	}else{
		$("#getopenid").removeClass('flex').addClass('hidden');
		$("input[name=pay_account]").removeAttr("readOnly");
	}
});
$("select[name='pay_type']").change();
<?php }?>

// 独立账户设定校验
function setpwd(){
	var user = $("input[name='user']").val();
	var pwd = $("input[name='pwd']").val();
	
	if(user=='' || pwd==''){ layer.msg('参数残缺，执行驳回！', {icon: 2}); return false; }
	if(user.length<3){ layer.msg('账号字长不达标 (≥3)', {icon: 2}); return false; }
	else if(user.length>20){ layer.msg('账号越过了字符边境 (≤20)', {icon: 2}); return false; }
	else if(pwd.length<6){ layer.msg('保险锁太弱，加长位数 (≥6)', {icon: 2}); return false; }
	else if(pwd.length>30){ layer.msg('密钥过于冗杂 (≤30)', {icon: 2}); return false; }
	
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : "POST",
		url : "ajax_user.php?act=setpwd",
		data : {user:user, pwd:pwd},
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				layer.alert(data.msg,{ icon:1, closeBtn: 0, title:'身份解绑与独立' }, function(){
				  window.location.reload();
				});
			}else{
				layer.alert(data.msg, {icon:2});
			}
		} 
	});
	return false;
}

// Oauth2.0 第三方接入信道握手
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
				layer.alert(data.msg, {icon: 2, title:'握手被拒绝'});
			}
		} 
	});
}

// 熔断与解绑
function unbind(type){
	var confirmobj = layer.confirm('<div class="text-center mt-2">斩断此通信链路后，将丧失相关一键登入系统的特权。<br>系统二次确认：是否要进行脱钩操作？</div>', {
	    title: '特权链路物理熔断',
	    icon: 3,
	    btn: ['立即执行', '暂且搁置'],
	    skin: 'layui-layer-rim rounded-2xl'
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
					layer.msg(data.msg, {icon: 1, time:1500}, function(){ window.location.reload();});
				}else{
					layer.alert(data.msg, {icon: 2});
				}
			} 
		});
	}, function(){
	    layer.close(confirmobj);
	});
}
</script>
<?php }elseif($mod=='upwxqrcode'){ ?>
<script>
// 删除微信二维码
function delWxQrcode() {
    layer.confirm('抹除确权：该动作将销毁服务器上的图形锚点！', {icon: 3, title:'物理抹除指令', skin:'layui-layer-rim rounded-2xl'}, function(){
        window.location.href = './uset.php?mod=upwxqrcode&del=1';
    });
}
</script>
<?php } ?>

<!-- 内联专属样式：修饰图床预览的透明底背景 -->
<style>
.checkerboard-bg {
    background-image: linear-gradient(45deg, #f1f5f9 25%, transparent 25%), linear-gradient(-45deg, #f1f5f9 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f1f5f9 75%), linear-gradient(-45deg, transparent 75%, #f1f5f9 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
}
</style>
</body>
</html>
