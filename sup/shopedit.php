<?php

include "../includes/common.php";
$title = "商品管理";
include "./head.php";
if ($islogin3 == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
if ($suprow["bond"] < $conf["sup_bond"]) {
	exit("<script>layer.msg('您当前未缴纳保证金，正在为您跳转...', {icon: 0, time: 2000}, function(){window.location.href='./bond.php';});</script>");
}
?>
<link rel="stylesheet" href="<?php echo $cdnpublic;?>select2/4.0.10/css/select2.min.css">
<script src="<?php echo $cdnpublic;?>select2/4.0.10/js/select2.min.js"></script>

<style>
	/* 覆盖 Select2 样式以匹配 HuWangV7 风格 */
	.select2-container .select2-selection--single {
		height: 48px;
		border-radius: 0.75rem;
		border-color: #e2e8f0;
		display: flex;
		align-items: center;
        background-color: #f8fafc;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		color: #334155;
        font-weight: 700;
        font-size: 0.875rem;
		padding-left: 1rem;
	}
	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 46px;
		right: 8px;
	}
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #6366f1;
        outline: 0;
        box-shadow: 0 0 0 1px #6366f1;
        background-color: #ffffff;
    }
    
    /* 屏蔽原生高亮 */
    * { -webkit-tap-highlight-color: transparent; }
</style>

<!-- 隐藏舱：输入框标题说明 -->
<div id="inputaboutContent" style="display: none;" class="p-6">
    <div class="text-sm text-slate-600 leading-relaxed font-medium space-y-4">
        <p class="font-bold text-slate-800">使用以下输入框标题可实现特殊的转换功能：</p>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <p class="mb-2"><strong>自动从链接和文字取出链接：</strong></p>
            <div class="flex flex-wrap gap-2">
                <a href="javascript:changeinput('作品链接')" class="text-indigo-600 hover:underline">作品链接</a>、
                <a href="javascript:changeinput('视频链接')" class="text-indigo-600 hover:underline">视频链接</a>、
                <a href="javascript:changeinput('分享链接')" class="text-indigo-600 hover:underline">分享链接</a>、
                <a href="javascript:changeinput('自定义[shareurl]')" class="text-indigo-600 hover:underline">自定义[shareurl]</a>
            </div>
        </div>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
            <p class="mb-2"><strong>自动获取音乐/视频ID：</strong></p>
            <div class="flex flex-wrap gap-2">
                <a href="javascript:changeinput('作品ID')" class="text-indigo-600 hover:underline">作品ID</a>、
                <a href="javascript:changeinput('帖子ID')" class="text-indigo-600 hover:underline">帖子ID</a>、
                <a href="javascript:changeinput('用户ID')" class="text-indigo-600 hover:underline">用户ID</a>、
                <a href="javascript:changeinput('自定义[shareid]')" class="text-indigo-600 hover:underline">自定义[shareid]</a>
            </div>
        </div>
        <p class="text-xs text-rose-500 bg-rose-50 p-3 rounded-lg"><i class="fa fa-info-circle mr-1"></i>注：在输入框名称后面加[shareid]、[shareurl]可以分别拥有获取ID、获取URL的功能。</p>
    </div>
</div>

<!-- 隐藏舱：更多输入框标题说明 -->
<div id="inputsaboutContent" style="display: none;" class="p-6">
    <div class="text-sm text-slate-600 leading-relaxed font-medium space-y-4">
        <p class="font-bold text-slate-800">使用以下输入框标题可实现特殊的转换功能：</p>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-3">
            <div>
                <strong>获取空间说说列表：</strong>
                <a href="javascript:changeinputs('说说ID')" class="text-indigo-600 hover:underline mx-1">说说ID</a>、
                <a href="javascript:changeinputs('说说ＩＤ')" class="text-indigo-600 hover:underline mx-1">说说ＩＤ</a>、
                <a href="javascript:changeinputs('自定义[ssid]')" class="text-indigo-600 hover:underline mx-1">自定义[ssid]</a>
            </div>
            <div>
                <strong>获取空间日志列表：</strong>
                <a href="javascript:changeinputs('日志ID')" class="text-indigo-600 hover:underline mx-1">日志ID</a>、
                <a href="javascript:changeinputs('日志ＩＤ')" class="text-indigo-600 hover:underline mx-1">日志ＩＤ</a>、
                <a href="javascript:changeinputs('自定义[rzid]')" class="text-indigo-600 hover:underline mx-1">自定义[rzid]</a>
            </div>
            <div>
                <strong>作品地址获取：</strong>
                <a href="javascript:changeinputs('自定义[zpid]')" class="text-indigo-600 hover:underline mx-1">自定义[zpid]</a>
            </div>
            <div>
                <strong>收货地址获取：</strong>
                <a href="javascript:changeinputs('收货地址')" class="text-indigo-600 hover:underline mx-1">收货地址</a>、
                <a href="javascript:changeinputs('收货人地址')" class="text-indigo-600 hover:underline mx-1">收货人地址</a>、
                <a href="javascript:changeinputs('自定义[address]')" class="text-indigo-600 hover:underline mx-1">自定义[address]</a>
            </div>
        </div>
        <p class="text-xs text-emerald-600 bg-emerald-50 p-3 rounded-lg">
            <i class="fa fa-lightbulb-o mr-1"></i>显示选择框：在名称后面加{选择1,选择2}。<br>例如：<a href="javascript:changeinputs('分类名{普通,音乐,宠物}')" class="font-bold hover:underline">分类名{普通,音乐,宠物}</a>
        </p>
    </div>
</div>

<div class="w-full max-w-[800px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php 
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select = "<option value=\"0\">未分类</option>";
$shua_class[0] = "未分类";
while ($res = $rs->fetch()) {
	$shua_class[$res["cid"]] = $res["name"];
	$select .= "<option value=\"" . $res["cid"] . "\">" . $res["name"] . "</option>";
}

// ----------------------------------------------------
// 视图：添加商品
// ----------------------------------------------------
if ($my == "add") {
?>
    <form action="./shopedit.php?my=add_submit" method="POST" onsubmit="return checkinput()">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <i class="fa fa-plus-circle text-indigo-500 mr-2 text-xl"></i>上架新商品
                </h3>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- 基础信息 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">商品分类 <span class="text-rose-500">*</span></label>
                        <select name="cid" class="w-full" default="<?php echo htmlspecialchars(@$_GET["cid"], ENT_QUOTES, 'UTF-8');?>"><?php echo $select;?></select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">商品名称 <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="输入清晰的商品名称">
                    </div>
                </div>

                <!-- 价格与数量 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5" id="prid1">
                        <label class="text-sm font-semibold text-slate-700 ml-1">结算价格 (元) <span class="text-rose-500">*</span></label>
                        <input type="text" name="price" class="w-full h-12 px-4 text-sm font-mono font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" required placeholder="0.00">
                    </div>
                    
                    <div class="space-y-1.5" id="show_value">
                        <label class="text-sm font-semibold text-slate-700 ml-1">默认数量</label>
                        <input type="number" name="value" id="value" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" placeholder="填写1默认下单1个" onkeyup="changeNum()">
                        <input type="hidden" id="price" value="">
                    </div>
                </div>

                <div class="space-y-1.5 border-b border-slate-100 pb-6">
                    <label class="text-sm font-semibold text-slate-700 ml-1">批发价格优惠设置</label>
                    <input type="text" name="prices" class="w-full h-12 px-4 text-sm font-mono font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:font-normal placeholder:text-slate-400" placeholder="例如：10|0.1,20|0.3">
                    <p class="text-xs text-emerald-600 mt-1 ml-1 font-medium"><i class="fa fa-info-circle mr-1"></i>填写格式：购满x个|减少x元单价,购满x个|减少x元单价</p>
                </div>

                <!-- 输入框定制 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">第一个输入框标题</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50 focus-within:bg-white">
                        <input type="text" name="input" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 placeholder:font-normal placeholder:text-slate-400" placeholder="留空默认为“下单账号”">
                        <button type="button" onclick="showInputRule1()" class="w-12 border-l border-slate-200 bg-white text-indigo-500 hover:bg-indigo-50 transition-colors shrink-0"><i class="fa fa-question-circle"></i></button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">更多输入框标题</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50 focus-within:bg-white">
                        <input type="text" name="inputs" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 placeholder:font-normal placeholder:text-slate-400" placeholder="留空则不显示更多输入框">
                        <button type="button" onclick="showInputRule2()" class="w-12 border-l border-slate-200 bg-white text-indigo-500 hover:bg-indigo-50 transition-colors shrink-0"><i class="fa fa-question-circle"></i></button>
                    </div>
                    <p class="text-xs text-emerald-600 mt-1 ml-1 font-medium"><i class="fa fa-info-circle mr-1"></i>多个输入框请用|隔开(不能超过4个)</p>
                </div>

                <!-- 描述与提示 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">商品简介 (没有请留空)</label>
                    <textarea id="editor_id" name="desc" class="w-full p-4 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400" rows="3" placeholder="当选择该商品时自动显示，支持HTML代码"></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">提示内容 (没有请留空)</label>
                    <input type="text" name="alert" class="w-full h-12 px-4 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
                </div>

                <!-- 媒体上传 -->
                <div class="space-y-1.5 border-b border-slate-100 pb-6">
                    <label class="text-sm font-semibold text-slate-700 ml-1">商品图片</label>
                    <input type="file" id="file" onchange="fileUpload()" class="hidden"/>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <input type="text" id="shopimg" name="shopimg" class="flex-1 h-12 px-4 text-sm font-mono border-0 focus:ring-0 placeholder:text-slate-400" placeholder="填写图片URL，没有请留空">
                        <button type="button" onclick="fileSelect()" class="px-4 border-l border-slate-200 bg-sky-50 text-sky-600 font-bold text-xs hover:bg-sky-100 transition-colors shrink-0"><i class="fa fa-cloud-upload mr-1.5"></i>上传</button>
                        <button type="button" onclick="fileView()" class="px-4 border-l border-slate-200 bg-emerald-50 text-emerald-600 font-bold text-xs hover:bg-emerald-100 transition-colors shrink-0"><i class="fa fa-picture-o mr-1.5"></i>预览</button>
                    </div>
                </div>

                <!-- 进阶控制 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">显示数量选择框 <span class="text-rose-500">*</span></label>
                        <select name="multi" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                            <option value="1">1_是</option>
                            <option value="0">0_否</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">允许重复下单</label>
                        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50">
                            <select name="repeat" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 cursor-pointer">
                                <option value="0">0_否</option>
                                <option value="1">1_是</option>
                            </select>
                            <button type="button" class="w-12 border-l border-slate-200 bg-white text-slate-400 hover:text-indigo-500 transition-colors shrink-0" onclick="layer.tips('是指相同下单输入内容（非同一用户）当天只能下单一次，或上一条订单未处理的情况下不能重复下单', this, {tips: [1, '#6366f1']})"><i class="fa fa-info-circle"></i></button>
                        </div>
                    </div>
                </div>

                <!-- 动态展开区域：多选数量约束 -->
                <div id="multi0" style="display:none;" class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 space-y-4">
                    <h4 class="text-xs font-bold text-indigo-800 uppercase tracking-wider text-center">数量选择约束边界</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="min" class="w-full h-10 px-3 text-sm font-mono rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" placeholder="最小数量 (留空默认1)"/>
                        </div>
                        <div>
                            <input type="text" name="max" class="w-full h-10 px-3 text-sm font-mono rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" placeholder="最大数量 (留空不限)"/>
                        </div>
                    </div>
                </div>

                <!-- 验证控制 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">验证操作</label>
                    <select name="validate" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                        <option value="0">不开启验证</option>
                        <option value="1">验证QQ空间是否有访问权限</option>
                        <option value="2">验证已开通服务(符合则禁止下单)</option>
                        <option value="3">验证已开通服务(符合则不对接社区)</option>
                    </select>
                </div>

                <div id="valiserv" style="display:none;" class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">需要验证的已开通服务</label>
                    <select name="valiserv" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                        <option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full h-14 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all">确定添加商品</button>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="shoplist.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors"><i class="fa fa-angle-left mr-1"></i>返回商品列表</a>
        </div>
    </form>

<?php 
// ----------------------------------------------------
// 视图：编辑商品
// ----------------------------------------------------
} elseif ($my == "edit") {
	$tid = $_GET["tid"];
	$row = $DB->getRow("select * from pre_tools where tid='" . $tid . "' limit 1");
?>
    <form action="./shopedit.php?my=edit_submit&tid=<?php echo $tid;?>" method="POST" onsubmit="return checkinput()">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between shrink-0">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                    <i class="fa fa-edit text-indigo-500 mr-2 text-xl"></i>编辑商品配置
                </h3>
                <span class="px-2 py-1 bg-slate-200 text-slate-600 rounded font-mono text-xs font-bold shadow-sm">ID: <?php echo $tid; ?></span>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- 基础信息 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">商品分类</label>
                        <select name="cid" class="w-full" default="<?php echo $row["cid"];?>"><?php echo $select;?></select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">商品名称 <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($row["name"]);?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors" required>
                    </div>
                </div>

                <!-- 价格与数量 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5" id="prid1">
                        <label class="text-sm font-semibold text-slate-700 ml-1">结算价格 (元) <span class="text-rose-500">*</span></label>
                        <input type="text" name="price" value="<?php echo $row["sup_price"];?>" class="w-full h-12 px-4 text-sm font-mono font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors" required>
                    </div>
                    
                    <div class="space-y-1.5" id="show_value">
                        <label class="text-sm font-semibold text-slate-700 ml-1">默认数量</label>
                        <input type="number" name="value" id="value" value="<?php echo $row["value"];?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400" placeholder="用于对接或导出时显示" onkeyup="changeNum()">
                        <input type="hidden" id="price" value="">
                    </div>
                </div>

                <div class="space-y-1.5 border-b border-slate-100 pb-6">
                    <label class="text-sm font-semibold text-slate-700 ml-1">批发价格优惠设置</label>
                    <input type="text" name="prices" value="<?php echo htmlspecialchars($row["prices"]);?>" class="w-full h-12 px-4 text-sm font-mono font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors placeholder:text-slate-400" placeholder="例如：10|0.1,20|0.3">
                    <p class="text-xs text-emerald-600 mt-1 ml-1 font-medium"><i class="fa fa-info-circle mr-1"></i>填写格式：购满x个|减少x元单价,购满x个|减少x元单价</p>
                </div>

                <!-- 输入框定制 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">第一个输入框标题</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50 focus-within:bg-white">
                        <input type="text" name="input" value="<?php echo htmlspecialchars($row["input"]);?>" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 placeholder:text-slate-400" placeholder="留空默认为“下单账号”">
                        <button type="button" onclick="showInputRule1()" class="w-12 border-l border-slate-200 bg-white text-indigo-500 hover:bg-indigo-50 transition-colors shrink-0"><i class="fa fa-question-circle"></i></button>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">更多输入框标题</label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50 focus-within:bg-white">
                        <input type="text" name="inputs" value="<?php echo htmlspecialchars($row["inputs"]);?>" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 placeholder:text-slate-400" placeholder="留空则不显示更多输入框">
                        <button type="button" onclick="showInputRule2()" class="w-12 border-l border-slate-200 bg-white text-indigo-500 hover:bg-indigo-50 transition-colors shrink-0"><i class="fa fa-question-circle"></i></button>
                    </div>
                    <p class="text-xs text-emerald-600 mt-1 ml-1 font-medium"><i class="fa fa-info-circle mr-1"></i>多个输入框请用|隔开(不能超过4个)</p>
                </div>

                <!-- 描述与提示 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">商品简介 (没有请留空)</label>
                    <textarea id="editor_id" name="desc" class="w-full p-4 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors" rows="3"><?php echo htmlspecialchars($row["desc"]);?></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">提示内容 (没有请留空)</label>
                    <input type="text" name="alert" value="<?php echo htmlspecialchars($row["alert"]);?>" class="w-full h-12 px-4 text-sm font-medium rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 focus:bg-white transition-colors" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
                </div>

                <!-- 媒体上传 -->
                <div class="space-y-1.5 border-b border-slate-100 pb-6">
                    <label class="text-sm font-semibold text-slate-700 ml-1">商品图片</label>
                    <input type="file" id="file" onchange="fileUpload()" class="hidden"/>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <input type="text" id="shopimg" name="shopimg" value="<?php echo htmlspecialchars($row["shopimg"]);?>" class="flex-1 h-12 px-4 text-sm font-mono border-0 focus:ring-0 placeholder:text-slate-400" placeholder="填写图片URL，没有请留空">
                        <button type="button" onclick="fileSelect()" class="px-4 border-l border-slate-200 bg-sky-50 text-sky-600 font-bold text-xs hover:bg-sky-100 transition-colors shrink-0"><i class="fa fa-cloud-upload mr-1.5"></i>上传</button>
                        <button type="button" onclick="fileView()" class="px-4 border-l border-slate-200 bg-emerald-50 text-emerald-600 font-bold text-xs hover:bg-emerald-100 transition-colors shrink-0"><i class="fa fa-picture-o mr-1.5"></i>预览</button>
                    </div>
                </div>

                <!-- 进阶控制 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">显示数量选择框 <span class="text-rose-500">*</span></label>
                        <select name="multi" default="<?php echo $row["multi"];?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                            <option value="1">1_是</option>
                            <option value="0">0_否</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">允许重复下单</label>
                        <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-slate-50">
                            <select name="repeat" default="<?php echo $row["repeat"];?>" class="flex-1 h-12 px-4 text-sm font-bold border-0 bg-transparent focus:ring-0 cursor-pointer">
                                <option value="0">0_否</option>
                                <option value="1">1_是</option>
                            </select>
                            <button type="button" class="w-12 border-l border-slate-200 bg-white text-slate-400 hover:text-indigo-500 transition-colors shrink-0" onclick="layer.tips('是指相同下单输入内容（非同一用户）当天只能下单一次，或上一条订单未处理的情况下不能重复下单', this, {tips: [1, '#6366f1']})"><i class="fa fa-info-circle"></i></button>
                        </div>
                    </div>
                </div>

                <!-- 动态展开区域：多选数量约束 -->
                <div id="multi0" style="display:none;" class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 space-y-4">
                    <h4 class="text-xs font-bold text-indigo-800 uppercase tracking-wider text-center">数量选择约束边界</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="min" value="<?php echo $row["min"];?>" class="w-full h-10 px-3 text-sm font-mono rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" placeholder="最小数量 (留空默认1)"/>
                        </div>
                        <div>
                            <input type="text" name="max" value="<?php echo $row["max"];?>" class="w-full h-10 px-3 text-sm font-mono rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm" placeholder="最大数量 (留空不限)"/>
                        </div>
                    </div>
                </div>

                <!-- 验证控制 -->
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">验证操作</label>
                    <select name="validate" default="<?php echo $row["validate"];?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                        <option value="0">不开启验证</option>
                        <option value="1">验证QQ空间是否有访问权限</option>
                        <option value="2">验证已开通服务(符合则禁止下单)</option>
                        <option value="3">验证已开通服务(符合则不对接社区)</option>
                    </select>
                </div>

                <div id="valiserv" style="display:none;" class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">需要验证的已开通服务</label>
                    <select name="valiserv" default="<?php echo $row["valiserv"];?>" class="w-full h-12 px-4 text-sm font-bold rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-sm bg-slate-50 cursor-pointer">
                        <option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option>
                    </select>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button type="submit" class="w-full h-14 rounded-xl bg-indigo-600 text-white font-bold text-[15px] shadow-md shadow-indigo-500/20 hover:bg-indigo-700 active:scale-[0.98] transition-all">确定修改配置</button>
                </div>
            </div>
        </div>
        
        <div class="mt-4 text-center">
            <a href="shoplist.php" class="text-sm font-bold text-slate-500 hover:text-indigo-600 transition-colors"><i class="fa fa-angle-left mr-1"></i>返回商品列表</a>
        </div>
    </form>

<?php 
// ----------------------------------------------------
// 逻辑处理：添加/修改入库
// ----------------------------------------------------
} elseif ($my == "add_submit") {
	$cid = $_POST["cid"];
	$name = $_POST["name"];
	$price = $_POST["price"];
	$prices = $_POST["prices"];
	$input = $_POST["input"];
	$inputs = $_POST["inputs"];
	$desc = $_POST["desc"];
	$alert = $_POST["alert"];
	$shopimg = $_POST["shopimg"];
	$value = $_POST["value"] ?? 1;
	$multi = $_POST["multi"];
	$min = $_POST["min"];
	$max = $_POST["max"];
	$validate = $_POST["validate"];
	$valiserv = $_POST["valiserv"];
	$repeat = $_POST["repeat"];
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} else {
		if ($conf["sup_audit_free"] == 1) {
			if ($suprow["bond"] >= $conf["pass_sup_bond"]) {
				$audit_status = 1;
				$active = 1;
			} else {
				$audit_status = 0;
				$active = 0;
			}
		} else {
			$audit_status = 0;
			$active = 0;
		}
		$sort = $DB->getColumn("select sort from pre_tools order by sort desc limit 1");
		$sql = "INSERT INTO `pre_tools` (`cid`,`name`,`sup_price`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`active`,`goods_sid`,`stock`,`sort`,`is_curl`,`prices`,`audit_status`) VALUES ('" . $cid . "','" . $name . "','" . $price . "','" . $input . "','" . $inputs . "','" . addslashes($desc) . "','" . addslashes($alert) . "','" . $shopimg . "','" . $value . "','" . $repeat . "','" . $multi . "','" . $min . "','" . $max . "','" . $validate . "','" . $valiserv . "','" . $active . "','" . $suprow["sid"] . "','" . $stock . "','" . $sort . "','4','" . $prices . "','" . $audit_status . "')";
		if ($DB->exec($sql) !== false) {
			$tid = $DB->lastInsertId();
			showmsg("添加商品成功，请等待管理员审核！<br/><br/><a href=\"./shoplist.php\" class=\"text-indigo-600 hover:underline\">>> 返回商品列表</a>", 1);
		} else {
			showmsg("添加商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$tid = $_GET["tid"];
	$rows = $DB->getRow("select * from pre_tools where tid='" . $tid . "' and goods_sid = '" . $suprow["sid"] . "' limit 1");
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$cid = $_POST["cid"];
	$name = $_POST["name"];
	$price = $_POST["price"];
	$prices = $_POST["prices"];
	$input = $_POST["input"];
	$inputs = $_POST["inputs"];
	$desc = $_POST["desc"];
	$alert = $_POST["alert"];
	$shopimg = $_POST["shopimg"];
	$value = $_POST["value"] ?? 1;
	$multi = $_POST["multi"];
	$min = $_POST["min"];
	$max = $_POST["max"];
	$validate = $_POST["validate"];
	$valiserv = $_POST["valiserv"];
	$repeat = $_POST["repeat"];
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} else {
		if ($conf["sup_audit_free"] == 1) {
			if ($suprow["bond"] >= $conf["pass_sup_bond"]) {
				$audit_status = 1;
				$active = 1;
			} else {
				$audit_status = 0;
				$active = 0;
			}
		} else {
			$audit_status = 0;
			$active = 0;
		}
		if ($DB->exec("UPDATE `pre_tools` SET `cid`='" . $cid . "',`name`='" . $name . "',`sup_price`='" . $price . "',`input`='" . $input . "',`inputs`='" . $inputs . "',`desc`='" . addslashes($desc) . "',`alert`='" . addslashes($alert) . "',`shopimg`='" . $shopimg . "',`value`='" . $value . "',`repeat`='" . $repeat . "',`multi`='" . $multi . "',`min`='" . $min . "',`max`='" . $max . "',`validate`='" . $validate . "',`valiserv`='" . $valiserv . "',`active`='" . $active . "',`audit_status`='" . $audit_status . "',`is_curl`='4',`prices`='" . $prices . "' WHERE `tid`='" . $tid . "'") !== false) {
			showmsg("修改商品成功！<br/><br/><a href=\"./shoplist.php\" class=\"text-indigo-600 hover:underline\">>> 返回商品列表</a>", 1);
		} else {
			showmsg("修改商品失败！" . $DB->error(), 4);
		}
	}
}
?>

</div>

<script>
var isAdd = true;
</script>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script>
    function showInputRule1() {
        layer.open({
            type: 1,
            title: '<div class="font-bold text-slate-800"><i class="fa fa-info-circle text-indigo-500 mr-2"></i>输入框标题说明</div>',
            area: [$(window).width() > 500 ? '500px' : '90%', 'auto'],
            skin: 'layui-layer-rim rounded-2xl',
            shadeClose: true,
            content: $('#inputaboutContent')
        });
    }

    function showInputRule2() {
        layer.open({
            type: 1,
            title: '<div class="font-bold text-slate-800"><i class="fa fa-info-circle text-indigo-500 mr-2"></i>更多输入框标题说明</div>',
            area: [$(window).width() > 500 ? '500px' : '90%', 'auto'],
            skin: 'layui-layer-rim rounded-2xl',
            shadeClose: true,
            content: $('#inputsaboutContent')
        });
    }

    function checkinput(){
        if($("input[name='name']").val()==''){
            layer.alert("商品名称不能为空", {icon: 2});
            return false;
        }
        if($("select[name='prid']").val()=='0' && $("input[name='price']").val()=='' || $("select[name='prid']").val()!='0' && $("input[name='price1']").val()==''){
            layer.alert("商品价格不能为空", {icon: 2});
            return false;
        }
        return true;
    }
    
    function setDesc(str){
        $("textarea[name='desc']").val(str);
        window.editor !== undefined && window.editor.html(str);
    }
    
    function changeinput(str){
        $("input[name='input']").val(str);
        layer.msg('已填入');
    }
    
    function changeinputs(str){
        $("input[name='inputs']").val(str);
        layer.msg('已填入');
    }
    
    function getFloat(number, n) {
        n = n ? parseInt(n) : 0;
        if (n <= 0) return Math.ceil(number);
        number = Math.round(number * Math.pow(10, n)) / Math.pow(10, n);
        return number;
    }
    
    function changeNum(){
        var num = parseInt($("#value").val());
        var price = parseFloat($("#price").val());
        var min = parseInt($("#value").attr('min'));
        var max = parseInt($("#value").attr('max'));
        if(num == 0 || isNaN(price))return false;
        $("input[name='price1']").val(getFloat(num * price, 2));
        $("input[name='price']").val(getFloat(num * price, 2));
        if(min == max || num >= max){
            $("select[name='multi']").val(0);
            $("input[name='min']").val('');
            $("input[name='max']").val('');
        }else{
            $("select[name='multi']").val(1);
            $("input[name='min']").val('');
            $("input[name='max']").val(Math.floor(max/num));
        }
        $("select[name='multi']").change();
    }
    
    function fileSelect(){
        $("#file").trigger("click");
    }
    
    function fileView(){
        var shopimg = $("#shopimg").val();
        if(shopimg=='') {
            layer.alert("请先上传图片，才能预览", {icon: 0});
            return;
        }
        if(shopimg.indexOf('http') == -1)shopimg = '../'+shopimg;
        layer.open({
            type: 1,
            title: false,
            closeBtn: 0,
            skin: 'layui-layer-nobg',
            shadeClose: true,
            content: '<img src="'+shopimg+'" class="max-w-[90vw] max-h-[90vh] rounded-2xl shadow-2xl border-4 border-white">'
        });
    }
    
    function fileUpload(){
        var fileObj = $("#file")[0].files[0];
        if (typeof (fileObj) == "undefined" || fileObj.size <= 0) {
            return;
        }
        var formData = new FormData();
        formData.append("do","upload");
        formData.append("type","shop");
        formData.append("file",fileObj);
        var ii = layer.load(2, {shade:[0.1,'#fff']});
        $.ajax({
            url: "ajax.php?act=uploadimg",
            data: formData,
            type: "POST",
            dataType: "json",
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                layer.close(ii);
                if(data.code == 0){
                    layer.msg('上传图片成功', {icon: 1});
                    $("#shopimg").val(data.url);
                }else{
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error:function(data){
                layer.close(ii);
                layer.msg('服务器错误', {icon: 2});
                return false;
            }
        })
    }
    
    function Addstr(id, str) {
        $("#"+id).val($("#"+id).val()+str);
    }
    
    $(document).ready(function(){
        // 初始化 Select2 (将其挂载到分类选择框上)
        $("select[name='cid']").select2();

        $("select[name='multi']").change(function(){
            if($(this).val() == 1){
                $("#multi0").slideDown(200);
            }else{
                $("#multi0").slideUp(200);
            }
        });
        $("select[name='validate']").change(function(){
            if($(this).val() >= 2){
                $("#valiserv").slideDown(200);
            }else{
                $("#valiserv").slideUp(200);
            }
        });
        
        var items = $("select[default]");
        for (i = 0; i < items.length; i++) {
            $(items[i]).val($(items[i]).attr("default")||0);
            // 同步更新 Select2 状态
            if($(items[i]).hasClass('select2-hidden-accessible')) {
                $(items[i]).trigger('change');
            }
        }
        
        $("select[name='shequ']").change();
        $("select[name='prid']").change();
        $("select[name='multi']").change();
        $("select[name='validate']").change();
        $("input[name='goods_id']").blur();

    });
</script>

<?php 
if ($conf["shopdesc_editor"]) {
?>
<script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
<style>
/* 微调富文本编辑器的边框样式以适应现代设计 */
.ke-container { border-radius: 0.75rem !important; overflow: hidden; border-color: #e2e8f0 !important; }
.ke-toolbar { background-color: #f8fafc !important; border-bottom-color: #e2e8f0 !important; }
</style>
<script>
KindEditor.ready(function(K) {
	window.editor = K.create('#editor_id', {
		resizeType : 1,
		allowUpload : false,
		allowPreviewEmoticons : false,
		uploadJson : './ajax.php?act=article_upload',
		items : [
			'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
			'removeformat','formatblock','hr', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
			'insertunorderedlist', '|', 'image', 'link','unlink', 'code', '|','fullscreen','source','preview']
	});
});
</script>
<?php 
}
?>

<script>
<?php 
\lib\Plugin::showThirdPluginsEditJs();
?>
</script>
</body>
</html>
