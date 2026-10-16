<?php
//请勿删除版权信息，否则出现问题将不再保持售后修复！
include "../includes/common.php";
$title = "商品管理";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<link rel="stylesheet" href="<?php echo $cdnpublic;?>select2/4.0.10/css/select2.min.css">
<script src="<?php echo $cdnpublic;?>select2/4.0.10/js/select2.min.js"></script>

<style type="text/tailwindcss">
@layer utilities {
    .select2-container .select2-selection--single {
        @apply h-10 border border-slate-200 rounded-xl px-3 bg-white flex items-center shadow-sm;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        @apply leading-normal text-slate-700 text-sm p-0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        @apply h-full right-3 flex items-center;
    }
    .select2-dropdown {
        @apply border border-slate-200 rounded-xl shadow-xl overflow-hidden mt-1 bg-white z-50;
    }
    .select2-search--dropdown .select2-search__field {
        @apply h-9 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-brand-500;
    }
    #GoodsInfo img { max-width: 100%; @apply rounded-xl shadow-sm my-2; }
}
</style>

<!-- 模态框 1：输入框标题说明 -->
<div class="modal fade fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden" align="left" id="inputabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog w-full max-w-lg m-4">
    <div class="modal-content bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
      <div class="modal-header px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="modal-title text-base font-bold text-slate-800 m-0" id="myModalLabel">输入框标题说明</h4>
        <button type="button" class="close text-slate-400 hover:text-slate-600 text-xl font-normal outline-none" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body p-6 text-sm text-slate-600 space-y-3">
	  <p>使用以下输入框标题可实现特殊的转换功能</p>
	  <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1.5 text-xs">
          <p class="font-semibold text-slate-700">自动从链接和文字取出链接：</p>
          <p><a href="javascript:changeinput('作品链接')" class="text-brand-600 hover:underline font-medium">作品链接</a>、<a href="javascript:changeinput('视频链接')" class="text-brand-600 hover:underline font-medium">视频链接</a>、<a href="javascript:changeinput('分享链接')" class="text-brand-600 hover:underline font-medium">分享链接</a>、<a href="javascript:changeinput('自定义[shareurl]')" class="text-brand-600 hover:underline font-medium">自定义[shareurl]</a></p>
      </div>
	  <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1.5 text-xs">
          <p class="font-semibold text-slate-700">自动获取音乐/视频ID：</p>
          <p><a href="javascript:changeinput('作品ID')" class="text-brand-600 hover:underline font-medium">作品ID</a>、<a href="javascript:changeinput('帖子ID')" class="text-brand-600 hover:underline font-medium">帖子ID</a>、<a href="javascript:changeinput('用户ID')" class="text-brand-600 hover:underline font-medium">用户ID</a>、<a href="javascript:changeinput('自定义[shareid]')" class="text-brand-600 hover:underline font-medium">自定义[shareid]</a></p>
      </div>
	  <p class="text-xs text-slate-500 pt-1 border-t border-slate-100">注：在输入框名称后面加[shareid]、[shareurl]可以分别有获取ID、获取URL功能</p>
	  </div>
      <div class="modal-footer px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex justify-end">
        <button type="button" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-medium transition-colors shadow-sm" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<!-- 模态框 2：更多输入框标题说明 -->
<div class="modal fade fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden" align="left" id="inputsabout" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog w-full max-w-lg m-4">
    <div class="modal-content bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
      <div class="modal-header px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h4 class="modal-title text-base font-bold text-slate-800 m-0" id="myModalLabel">更多输入框标题说明</h4>
        <button type="button" class="close text-slate-400 hover:text-slate-600 text-xl font-normal outline-none" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
      </div>
      <div class="modal-body p-6 text-sm text-slate-600 space-y-3">
	  <p>使用以下输入框标题可实现特殊的转换功能</p>
	  <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-2 text-xs">
          <p><strong class="text-slate-700">获取空间说说列表：</strong><a href="javascript:changeinputs('说说ID')" class="text-brand-600 hover:underline">说说ID</a>、<a href="javascript:changeinputs('说说ＩＤ')" class="text-brand-600 hover:underline">说说ＩＤ</a>、<a href="javascript:changeinputs('自定义[ssid]')" class="text-brand-600 hover:underline">自定义[ssid]</a></p>
          <p><strong class="text-slate-700">获取空间日志列表：</strong><a href="javascript:changeinputs('日志ID')" class="text-brand-600 hover:underline">日志ID</a>、<a href="javascript:changeinputs('日志ＩＤ')" class="text-brand-600 hover:underline">日志ＩＤ</a>、<a href="javascript:changeinputs('自定义[rzid]')" class="text-brand-600 hover:underline">自定义[rzid]</a></p>
          <p><strong class="text-slate-700">作品地址获取：</strong><a href="javascript:changeinputs('自定义[zpid]')" class="text-brand-600 hover:underline">自定义[zpid]</a></p>
          <p><strong class="text-slate-700">收货地址获取：</strong><a href="javascript:changeinputs('收货地址')" class="text-brand-600 hover:underline">收货地址</a>、<a href="javascript:changeinputs('收货人地址')" class="text-brand-600 hover:underline">收货人地址</a>、<a href="javascript:changeinputs('自定义[address]')" class="text-brand-600 hover:underline">自定义[address]</a></p>
      </div>
	  <p class="text-xs text-slate-500 pt-1 border-t border-slate-100">显示选择框，在名称后面加{选择1,选择2}，例如：<a href="javascript:changeinputs('分类名{普通,音乐,宠物}')" class="text-brand-600 font-medium hover:underline">分类名{普通,音乐,宠物}</a></p>
	  </div>
      <div class="modal-footer px-6 py-3 border-t border-slate-100 bg-slate-50/50 flex justify-end">
        <button type="button" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-medium transition-colors shadow-sm" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>

<?php 
adminpermission("shop", 1);
$my = isset($_GET["my"]) ? $_GET["my"] : null;
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$classes = [];
$subClasses = [];
$shua_class[0] = "未分类";

while ($res = $rs->fetch()) {
    $shua_class[$res["cid"]] = $res["name"];
    if ($res["pid"] == 0) {
        $classes[$res["cid"]] = $res;
    } else {
        $subClasses[$res["pid"]][] = $res;
    }
}

$select = "<option value=\"0\">未分类</option>";
foreach ($classes as $cid => $class) {
    $select .= "<option value=\"$cid\" style=\"font-weight:bold;color:#337ab7;\">" . $class["name"] . "</option>";
    if (isset($subClasses[$cid])) {
        foreach ($subClasses[$cid] as $subClass) {
            $select .= "<option value=\"{$subClass['cid']}\" style=\"padding-left:20px;color:#555;\">┗━ " . $subClass["name"] . "</option>";
        }
    }
}
$rs = $DB->query("SELECT * FROM pre_shequ order by id asc");
$shequselect = "";
while ($res = $rs->fetch()) {
	$getInfo = \lib\Plugin::getConfig("third_" . $res["type"]);
	$shequselect .= "<option value=\"" . $res["id"] . "\" type=\"" . $res["type"] . "\" domain=\"" . $res["url"] . "\">[<font color=blue>" . $getInfo["title"] . "</font>] " . $res["url"] . ($res["remark"] ? " (" . $res["remark"] . ")" : "") . "</option>";
}
if (isset($_SESSION["priceselect"])) {
	$priceselect = $_SESSION["priceselect"];
} else {
	$rs = $DB->query("SELECT * FROM pre_price order by id asc");
	$priceselect = "<option value=\"0\">不使用加价模板</option>";
	while ($res = $rs->fetch()) {
		$kind = $res["kind"] == 1 ? "元" : "倍";
		$priceselect .= "<option value=\"" . $res["id"] . "\" kind=\"" . $res["kind"] . "\" p_2=\"" . $res["p_2"] . "\" p_1=\"" . $res["p_1"] . "\" p_0=\"" . $res["p_0"] . "\" >" . $res["name"] . "(" . $res["p_2"] . $kind . "|" . $res["p_1"] . $kind . "|" . $res["p_0"] . $kind . ")</option>";
	}
}

if ($my == "add") {
	?><div class="w-full max-w-[1600px] mx-auto">
<form action="./shopedit.php?my=add_submit" method="POST" onsubmit="return checkinput()">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 左侧：商品类型与对接设置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-cogs text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-slate-800 m-0">商品类型与对接设置</h3>
        </div>
        <input type="hidden" name="backurl" value="<?php echo $_SERVER["HTTP_REFERER"] ?? '';?>"/>
        <div class="p-6 space-y-5 flex-1">
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">购买成功后的动作:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="is_curl">
                    <option value="0">0_无</option>
                    <option value="2">自动提交到社区/卡盟</option>
                    <option value="1">自定义访问URL/POST</option>
                    <option value="4">自动发卡密</option>
                    <option value="3">自动发送提醒邮件/微信</option>
                    <option value="5">直接显示指定内容</option>
                </select>
            </div>
            
            <div id="curl_display1" style="display:none;" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">URL:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="curl" id="curl" value="">
                </div>
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">POST:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="curl_post" id="curl_post" value="" placeholder="无POST内容请留空">
                </div>
                <div class="text-xs text-emerald-600 space-y-1 bg-emerald-50/50 p-3 rounded-lg border border-emerald-100">
                    <p class="font-semibold">无POST内容请留空，POST格式：a=123&b=456</p>
                    <p class="font-semibold pt-1">变量代码：</p>
                    <div class="flex flex-wrap gap-2 text-slate-600">
                        <a href="#" onclick="Addstr('curl','[input]');return false" class="underline hover:text-brand-600">[input] 第一个输入框</a>
                        <a href="#" onclick="Addstr('curl','[input2]');return false" class="underline hover:text-brand-600">[input2] 第二个</a>
                        <a href="#" onclick="Addstr('curl','[input3]');return false" class="underline hover:text-brand-600">[input3] 第三个</a>
                        <a href="#" onclick="Addstr('curl','[input4]');return false" class="underline hover:text-brand-600">[input4] 第四个</a>
                        <a href="#" onclick="Addstr('curl','[name]');return false" class="underline hover:text-brand-600">[name] 名称</a>
                        <a href="#" onclick="Addstr('curl','[price]');return false" class="underline hover:text-brand-600">[price] 价格</a>
                        <a href="#" onclick="Addstr('curl','[num]');return false" class="underline hover:text-brand-600">[num] 数量</a>
                        <a href="#" onclick="Addstr('curl','[time]');return false" class="underline hover:text-brand-600">[time] 时间戳</a>
                    </div>
                </div>
            </div>
            
            <div id="curl_display3" style="display:none;" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">购买后直接显示的内容:</label>
                    <textarea class="w-full p-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="showcontent" rows="3" placeholder="用户购买后直接在订单详情显示的内容，支持HTML代码"></textarea>
                </div>
                <div class="form-group flex items-center space-x-2">
                    <input type="checkbox" name="send_email" value="1" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">同时通过邮件发送此内容给用户</label>
                </div>
            </div>
            
            <div id="curl_display2" style="display:none;" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">选择对接网站:</label>&nbsp;(<a href="shequlist.php" target="_blank" class="text-brand-600 hover:underline">添加</a>)
                    <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="shequ"><?php echo $shequselect;?></select>
                </div>
                <div class="form-group space-y-1.5" id="show_goodslist">
                    <label class="text-sm font-semibold text-slate-700">选择对接商品:</label>
                    <div class="flex gap-2">
                        <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer flex-1" id="goodslist"></select>
                        <span class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer shrink-0" id="getGoods">获取</span>
                    </div>
                </div>
                <div class="form-group space-y-1.5" id="goods_id">
                    <label class="text-sm font-semibold text-slate-700">商品ID（goods_id）:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_id" value="">
                </div>
                <div class="form-group space-y-1.5" id="goods_type">
                    <label class="text-sm font-semibold text-slate-700">类型ID（goods_type）:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_type" value="">
                </div>
                <div id="kyxCommunity" style="display:none;" class="space-y-4">
                    <div class="form-group space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">请选择分类:</label>
                        <div class="flex gap-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer flex-1" id="kyxCategory"><option value="null">请选择分类</option></select>
                            <span class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer shrink-0" id="kyxGetCategory">获取</span>
                        </div>
                    </div>
                    <div class="form-group space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">请选择对接商品:</label>
                        <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="kyxProductList"><option value="null">请选择二级分类</option></select>
                    </div>
                </div>
                <div class="form-group space-y-1.5" id="goods_type_select_form" style="display:none;">
                    <label class="text-sm font-semibold text-slate-700">商品类型:</label>
                    <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="goods_type_select"><option value="0">代充</option><option value="1">发卡</option></select>
                </div>
                <div class="form-group space-y-1.5" id="goods_param">
                    <label class="text-sm font-semibold text-slate-700" id="goods_param_name">参数名:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_param" value="qq">
                    <p class="text-xs text-emerald-600 pt-1">对应输入框标题，多个参数请用|隔开；如果是对接卡盟，请直接填写下单页面地址</p>
                </div>
            </div>
            
            <div class="form-group space-y-1.5" id="show_value">
                <label class="text-sm font-semibold text-slate-700">默认数量信息:</label>
                <input type="number" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="value" id="value" value="" placeholder="用于对接社区使用或导出时显示" onkeyup="changeNum()">
                <input type="hidden" id="price" value="">
            </div>
            <div id="GoodsInfo" class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-800 text-sm" style="display:none;"></div>
        </div>
    </div>
    
    <!-- 右侧：商品基本信息设置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-info-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-slate-800 m-0">商品基本信息设置</h3>
        </div>
        <div class="p-6 space-y-5 flex-1">
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">*商品分类:</label>
                <select name="cid" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="categorySelect" default="<?php echo htmlspecialchars($_GET["cid"] ?? "");?>" style="width:100%">
                <?php echo $select;?>
                </select>
                <script>
                $(document).ready(function() {
                    $('#categorySelect').select2({
                        placeholder: '请选择商品分类或输入关键词搜索',
                        allowClear: true,
                        width: '100%'
                    });
                    var defaultCid = '<?php echo htmlspecialchars($_GET["cid"] ?? "");?>';
                    if(defaultCid && defaultCid != 0) {
                        $('#categorySelect').val(defaultCid).trigger('change');
                    }
                });
                </script>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">*商品名称:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="name" value="" required>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">加价模板:</label>&nbsp;(<a href="./price.php" target="_blank" class="text-brand-600 hover:underline">管理</a>)
                <select name="prid" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer"><?php echo $priceselect;?></select>
            </div>
            
            <div class="form-group space-y-1.5" id="prid1" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">*成本价格:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="price1" value="">
            </div>
            
            <div class="overflow-x-auto" id="prid0" style="display:none;">
                <table class="w-full text-sm text-left border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 text-xs">
                        <tr><th class="p-2.5 text-center border-b border-slate-100">*销售价格</th><th class="p-2.5 text-center border-b border-slate-100">普及版价格</th><th class="p-2.5 text-center border-b border-slate-100">专业版价格</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="price" value="" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm"/></td>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="cost" value="" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="不填写则同步销售价格"/></td>
                            <td class="p-2"><input type="text" name="cost2" value="" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="不填写则同步普及版价格"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">批发价格优惠设置:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="prices" placeholder="不懂请勿填写">
                <p class="text-xs text-emerald-600">填写格式：购满x个|减少x元单价,购满x个|减少x元单价 例如10|0.1,20|0.3,30|0.5</p>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">第一个输入框标题:</label>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" name="input" value="" placeholder="留空默认为“下单账号”">
                    <a href="#inputabout" data-toggle="modal" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a>
                </div>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">更多输入框标题:</label>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" name="inputs" value="" placeholder="留空则不显示更多输入框">
                    <a href="#inputsabout" data-toggle="modal" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a>
                </div>
                <p class="text-xs text-emerald-600">多个输入框请用|隔开(不能超过4个)</p>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">商品简介 <span class="text-xs font-normal text-slate-400">(没有请留空)</span>:</label>
                <textarea class="w-full p-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" id="editor_id" name="desc" rows="3" placeholder="当选择该商品时自动显示，支持HTML代码"></textarea>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">提示内容 <span class="text-xs font-normal text-slate-400">(没有请留空)</span>:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="alert" value="" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">商品图片:</label>
                <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" id="shopimg" name="shopimg" value="" placeholder="填写图片URL，没有请留空">
                    <a href="javascript:fileSelect()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors shadow-sm shrink-0" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a>
                    <a href="javascript:fileView()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors shadow-sm shrink-0" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a>
                </div>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">*显示数量选择框:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="multi"><option value="1">1_是</option><option value="0">0_否</option></select>
            </div>
            
            <div class="overflow-x-auto" id="multi0" style="display:none;">
                <table class="w-full text-sm text-left border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 text-xs">
                        <tr><th class="p-2.5 text-center border-b border-slate-100">最小下单数量</th><th class="p-2.5 text-center border-b border-slate-100">最大下单数量</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="min" value="" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="留空则默认为1"/></td>
                            <td class="p-2"><input type="text" name="max" value="" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="留空则不限数量"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">允许重复下单:</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                        <input type="checkbox" name="repeat_default" value="1" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 默认为开启
                    </label>
                </div>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer mt-2" name="repeat"><option value="0">0_否</option><option value="1">1_是</option></select>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">验证操作:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="validate"><option value="0">不开启验证</option><option value="1">验证QQ空间是否有访问权限</option><option value="2">验证已开通服务(符合则禁止下单)</option><option value="3">验证已开通服务(符合则不对接社区)</option></select>
            </div>
            
            <div class="form-group space-y-1.5" id="valiserv" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">需要验证的已开通服务:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="valiserv"><option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option></select>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">网盘投诉:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="ts"><option value="1">开启</option><option value="0">关闭</option></select>
            </div>
            
            <div class="pt-2">
                <input type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer" value="确定添加">
            </div>
            <div class="text-center pt-2">
                <a href="./shoplist.php?cid=<?php echo htmlspecialchars($_GET["cid"] ?? "", ENT_QUOTES, 'UTF-8');?>" class="text-sm font-medium text-brand-600 hover:underline">&gt;&gt;返回商品列表</a>
            </div>
        </div>
    </div>
</div>
</form>
</div>
<?php 
} elseif ($my == "edit") {
	$tid = intval($_GET["tid"] ?? 0);
	$row = $DB->getRow("select * from pre_tools where tid=:tid limit 1", array(":tid" => $tid));
	// 商品不存在时及时提示，避免后续模板用空 $row 渲染出一堆未定义索引告警 + 空表单
	if (!$row) {
		showmsg("当前商品不存在或已被删除！<br/><br/><a href='./shoplist.php'>>>返回商品列表</a>", 3);
	}
	?><div class="w-full max-w-[1600px] mx-auto">
<form action="./shopedit.php?my=edit_submit&tid=<?php echo $tid;?>" method="POST" onsubmit="return checkinput()">
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- 左侧：商品类型与对接设置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-cogs text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-slate-800 m-0">商品类型与对接设置</h3>
        </div>
        <div class="p-6 space-y-5 flex-1">
            <input type="hidden" name="backurl" value=""/>
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">购买成功后的动作:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="is_curl" default="<?php echo $row["is_curl"];?>">
                    <option value="0">0_无</option>
                    <option value="2">自动提交到社区/卡盟</option>
                    <option value="1">自定义访问URL/POST</option>
                    <option value="4">自动发卡密</option>
                    <option value="3">自动发送提醒邮件/微信</option>
                    <option value="5">直接显示指定内容</option>
                </select>
            </div>
            
            <div id="curl_display1" style="<?php echo $row["is_curl"] != 1 ? "display:none;" : NULL;?>" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">URL:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="curl" id="curl" value="<?php echo $row["curl"];?>">
                </div>
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">POST:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="curl_post" id="curl_post" value="<?php echo $row["goods_param"];?>" placeholder="无POST内容请留空">
                </div>
                <div class="text-xs text-emerald-600 space-y-1 bg-emerald-50/50 p-3 rounded-lg border border-emerald-100">
                    <p class="font-semibold">无POST内容请留空，POST格式：a=123&b=456</p>
                    <p class="font-semibold pt-1">变量代码：</p>
                    <div class="flex flex-wrap gap-2 text-slate-600">
                        <a href="#" onclick="Addstr('curl','[input]');return false" class="underline hover:text-brand-600">[input] 第一个输入框</a>
                        <a href="#" onclick="Addstr('curl','[input2]');return false" class="underline hover:text-brand-600">[input2] 第二个</a>
                        <a href="#" onclick="Addstr('curl','[input3]');return false" class="underline hover:text-brand-600">[input3] 第三个</a>
                        <a href="#" onclick="Addstr('curl','[input4]');return false" class="underline hover:text-brand-600">[input4] 第四个</a>
                        <a href="#" onclick="Addstr('curl','[name]');return false" class="underline hover:text-brand-600">[name] 名称</a>
                        <a href="#" onclick="Addstr('curl','[price]');return false" class="underline hover:text-brand-600">[price] 价格</a>
                        <a href="#" onclick="Addstr('curl','[num]');return false" class="underline hover:text-brand-600">[num] 数量</a>
                        <a href="#" onclick="Addstr('curl','[time]');return false" class="underline hover:text-brand-600">[time] 时间戳</a>
                    </div>
                </div>
            </div>
            
            <div id="curl_display3" style="<?php echo $row["is_curl"] != 5 ? "display:none;" : NULL;?>" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">购买后直接显示的内容:</label>
                    <textarea class="w-full p-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="showcontent" rows="3" placeholder="用户购买后直接在订单详情显示的内容，支持HTML代码"><?php echo htmlspecialchars($row["showcontent"]);?></textarea>
                </div>
                <div class="form-group flex items-center space-x-2">
                    <input type="checkbox" name="send_email" value="1" <?php echo isset($row['send_email']) && $row['send_email'] ? 'checked' : ''; ?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                    <label class="text-sm font-medium text-slate-700 cursor-pointer">同时通过邮件发送此内容给用户</label>
                </div>
            </div>
            
            <div id="curl_display2" style="<?php echo $row["is_curl"] != 2 ? "display:none;" : NULL;?>" class="space-y-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div class="form-group space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">选择对接网站:</label>
                    <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="shequ" default="<?php echo $row["shequ"];?>"><?php echo $shequselect;?></select>
                </div>
                <div class="form-group space-y-1.5" id="show_goodslist">
                    <label class="text-sm font-semibold text-slate-700">选择对接商品:</label>
                    <div class="flex gap-2">
                        <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer flex-1" id="goodslist" default="<?php echo $row["goods_id"];?>"></select>
                        <span class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer shrink-0" id="getGoods">获取</span>
                    </div>
                </div>
                <div class="form-group space-y-1.5" id="goods_id">
                    <label class="text-sm font-semibold text-slate-700">商品ID（goods_id）:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_id" value="<?php echo $row["goods_id"];?>">
                </div>
                <div class="form-group space-y-1.5" id="goods_type">
                    <label class="text-sm font-semibold text-slate-700">类型ID（goods_type）:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_type" value="<?php echo $row["goods_type"];?>">
                </div>
                <div id="kyxCommunity" style="display:none;" class="space-y-4">
                    <div class="form-group space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">请选择分类:</label>
                        <div class="flex gap-2">
                            <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer flex-1" id="kyxCategory"><option value="null">请选择分类</option></select>
                            <span class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-emerald-500 text-white font-medium hover:bg-emerald-600 shadow-sm transition-colors cursor-pointer shrink-0" id="kyxGetCategory">获取</span>
                        </div>
                    </div>
                    <div class="form-group space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">请选择对接商品:</label>
                        <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="kyxProductList"><option value="null">请选择二级分类</option></select>
                    </div>
                </div>
                <div class="form-group space-y-1.5" id="goods_type_select_form" style="display:none;">
                    <label class="text-sm font-semibold text-slate-700">商品类型:</label>
                    <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="goods_type_select" default="<?php echo isset($row['goods_type']) ? $row['goods_type'] : 0; ?>"><option value="0">代充</option><option value="1">发卡</option></select>
                </div>
                <div class="form-group space-y-1.5" id="goods_param">
                    <label class="text-sm font-semibold text-slate-700" id="goods_param_name">参数名:</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="goods_param" value="<?php echo $row["goods_param"];?>">
                    <p class="text-xs text-emerald-600 pt-1">对应输入框标题，多个参数请用|隔开；如果是对接卡盟，请直接填写下单页面地址</p>
                </div>
            </div>
            
            <div class="form-group space-y-1.5" id="show_value">
                <label class="text-sm font-semibold text-slate-700">默认数量信息:</label>
                <input type="number" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="value" id="value" value="<?php echo $row["value"];?>" placeholder="用于对接社区使用或导出时显示" onkeyup="changeNum()">
                <input type="hidden" id="price" value="">
            </div>
            <div id="GoodsInfo" class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-800 text-sm" style="display:none;"></div>
        </div>
    </div>
    
    <!-- 右侧：商品基本信息设置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-info-circle text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base font-bold text-slate-800 m-0">商品基本信息设置</h3>
        </div>
        <div class="p-6 space-y-5 flex-1">
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">商品分类:</label>
                <select name="cid" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" id="categorySelect" default="<?php echo $row["cid"];?>" style="width:100%">
                <?php echo $select;?>
                </select>
                <script>
                $(document).ready(function() {
                    $('#categorySelect').select2({
                        placeholder: '请选择商品分类或输入关键词搜索',
                        allowClear: true,
                        width: '100%'
                    });
                    var defaultCid = '<?php echo $row["cid"];?>';
                    if(defaultCid && defaultCid != 0) {
                        $('#categorySelect').val(defaultCid).trigger('change');
                    }
                });
                </script>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">*商品名称:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="name" value="<?php echo $row["name"];?>" required>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">加价模板:</label>&nbsp;(<a href="./price.php" target="_blank" class="text-brand-600 hover:underline">管理</a>)
                <select name="prid" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" default="<?php echo $row["prid"];?>"><?php echo $priceselect;?></select>
            </div>
            
            <div class="form-group space-y-1.5" id="prid1" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">*成本价格:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="price1" value="<?php echo $row["price"];?>">
            </div>
            
            <div class="overflow-x-auto" id="prid0" style="display:none;">
                <table class="w-full text-sm text-left border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 text-xs">
                        <tr><th class="p-2.5 text-center border-b border-slate-100">*销售价格</th><th class="p-2.5 text-center border-b border-slate-100">普及版价格</th><th class="p-2.5 text-center border-b border-slate-100">专业版价格</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="price" value="<?php echo $row["price"];?>" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm"/></td>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="cost" value="<?php echo $row["cost"];?>" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="不填写则同步销售价格"/></td>
                            <td class="p-2"><input type="text" name="cost2" value="<?php echo $row["cost2"];?>" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" placeholder="不填写则同步普及版价格"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">批发价格优惠设置:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="prices" value="<?php echo $row["prices"];?>">
                <p class="text-xs text-emerald-600">填写格式：购满x个|减少x元单价,购满x个|减少x元单价 例如10|0.1,20|0.3,30|0.5</p>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">第一个输入框标题:</label>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" name="input" value="<?php echo $row["input"];?>" placeholder="留空默认为“下单账号”">
                    <a href="#inputabout" data-toggle="modal" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a>
                </div>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">更多输入框标题:</label>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" name="inputs" value="<?php echo $row["inputs"];?>" placeholder="留空则不显示更多输入框">
                    <a href="#inputsabout" data-toggle="modal" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0" title="说明"><i class="glyphicon glyphicon-exclamation-sign"></i></a>
                </div>
                <p class="text-xs text-emerald-600">多个输入框请用|隔开(不能超过4个)</p>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">商品简介 <span class="text-xs font-normal text-slate-400">(没有请留空)</span>:</label>
                <textarea class="w-full p-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" id="editor_id" name="desc" rows="3" placeholder="当选择该商品时自动显示，支持HTML代码"><?php echo htmlspecialchars($row["desc"]);?></textarea>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">提示内容 <span class="text-xs font-normal text-slate-400">(没有请留空)</span>:</label>
                <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white" name="alert" value="<?php echo htmlspecialchars($row["alert"]);?>" placeholder="当选择该商品时自动弹出提示，不支持HTML代码">
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">商品图片:</label>
                <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                <div class="flex gap-2">
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white flex-1" id="shopimg" name="shopimg" value="<?php echo $row["shopimg"];?>" placeholder="填写图片URL，没有请留空">
                    <a href="javascript:fileSelect()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-colors shadow-sm shrink-0" title="上传图片"><i class="glyphicon glyphicon-upload"></i></a>
                    <a href="javascript:fileView()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors shadow-sm shrink-0" title="查看图片"><i class="glyphicon glyphicon-picture"></i></a>
                </div>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">显示数量选择框:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="multi" default="<?php echo $row["multi"];?>"><option value="1">1_是</option><option value="0">0_否</option></select>
            </div>
            
            <div class="overflow-x-auto" id="multi0" style="display:none;">
                <table class="w-full text-sm text-left border border-slate-100 rounded-xl overflow-hidden">
                    <thead class="bg-slate-50 text-slate-700 text-xs">
                        <tr><th class="p-2.5 text-center border-b border-slate-100">最小下单数量</th><th class="p-2.5 text-center border-b border-slate-100">最大下单数量</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="p-2 border-r border-slate-100"><input type="text" name="min" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" value="<?php echo $row["min"];?>" placeholder="留空则默认为1"/></td>
                            <td class="p-2"><input type="text" name="max" class="w-full h-9 px-2 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm" value="<?php echo $row["max"];?>" placeholder="留空则不限数量"/></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">允许重复下单:</label>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center text-sm font-medium text-slate-600 cursor-pointer">
                        <input type="checkbox" name="repeat_default" value="1" <?php echo $row["repeat"] == 1 ? 'checked' : ''; ?> class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer mr-2"> 默认为开启
                    </label>
                </div>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer mt-2" name="repeat" default="<?php echo $row["repeat"];?>"><option value="0">0_否</option><option value="1">1_是</option></select>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">验证操作:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="validate" default="<?php echo $row["validate"];?>"><option value="0">不开启验证</option><option value="1">验证QQ空间是否有访问权限</option><option value="2">验证已开通服务(符合则禁止下单)</option><option value="3">验证已开通服务(符合则不对接社区)</option></select>
            </div>
            
            <div class="form-group space-y-1.5" id="valiserv" style="display:none;">
                <label class="text-sm font-semibold text-slate-700">需要验证的已开通服务:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="valiserv" default="<?php echo $row["valiserv"];?>"><option value="vip">QQ会员</option><option value="svip">超级会员</option><option value="red">红钻贵族</option><option value="green">绿钻贵族</option><option value="sgreen">绿钻豪华版</option><option value="yellow">黄钻贵族</option><option value="syellow">豪华黄钻</option><option value="hollywood">腾讯视频VIP</option><option value="qqmsey">付费音乐包</option><option value="qqmstw">豪华付费音乐包</option><option value="weiyun">微云会员</option><option value="sweiyun">微云超级会员</option></select>
            </div>
            
            <div class="form-group space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">网盘投诉:</label>
                <select class="w-full h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer" name="ts" default="<?php echo $row["ts"];?>"><option value="1">开启</option><option value="0">关闭</option></select>
            </div>
            
            <div class="pt-2">
                <input type="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm hover:shadow-md transition-all duration-200 cursor-pointer" value="确定修改">
            </div>
            <div class="text-center pt-2">
                <a href="./shoplist.php?cid=<?php echo $row["cid"];?>" class="text-sm font-medium text-brand-600 hover:underline">&gt;&gt;返回商品列表</a>
            </div>
        </div>
    </div>
</div>
</form>
</div>
<?php 
} elseif ($my == "add_submit") {
	$cid = trim($_POST["cid"] ?? '');
	$name = trim($_POST["name"] ?? '');
	$prid = trim($_POST["prid"] ?? '');
	$price = $prid == "0" ? $_POST["price"] ?? '' : $_POST["price1"] ?? '';
	$cost = trim($_POST["cost"] ?? '');
	$cost2 = trim($_POST["cost2"] ?? '');
	if ($prid == 0) {
		if ($cost2 > $cost) {
			showmsg("专业版加价不能高于普及版加价", 3);
		} elseif ($cost2 > $price) {
			showmsg("专业版加价不能高于普通用户加价", 3);
		} elseif ($cost > $price) {
			showmsg("普及版加价不能高于普通用户加价", 3);
		}
	}
	$prices = trim($_POST["prices"] ?? '');
	$input = trim($_POST["input"] ?? '');
	$inputs = trim($_POST["inputs"] ?? '');
	$desc = trim($_POST["desc"] ?? '');
	$alert = trim($_POST["alert"] ?? '');
	$shopimg = trim($_POST["shopimg"] ?? '');
	$value = trim($_POST["value"] ?? '');
	$multi = trim($_POST["multi"] ?? '');
	$min = trim($_POST["min"] ?? '');
	$max = trim($_POST["max"] ?? '');
	$validate = trim($_POST["validate"] ?? '');
	$valiserv = trim($_POST["valiserv"] ?? '');
	$is_curl = trim($_POST["is_curl"] ?? '');
	$curl = trim($_POST["curl"] ?? '');
	$showcontent = trim($_POST["showcontent"] ?? '');
	$shequ = trim($_POST["shequ"] ?? '');
	$goods_id = trim($_POST["goods_id"] ?? '');
	$goods_type = trim($_POST["goods_type"] ?? '');
	$goods_param = $is_curl == 1 ? $_POST["curl_post"] ?? '' : $_POST["goods_param"] ?? '';
	$repeat_default = isset($_POST["repeat_default"]) ? 1 : 0;
	$repeat = $repeat_default == 1 ? 1 : $_POST["repeat"] ?? '';
	$ts = trim($_POST["ts"] ?? '');
	if(empty($cid)) $cid = 0;
	if(empty($shequ)) $shequ = 0;
	if(empty($goods_id)) $goods_id = 0;
	if(empty($goods_type)) $goods_type = 0;
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} elseif ($is_curl == 2 && !$shequ) {
		showmsg("请选择对接社区！", 3);
	} else {
		$sort = $DB->getColumn("select sort from pre_tools order by sort desc limit 1");
		$sql = "INSERT INTO `pre_tools` (`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`showcontent`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`sort`,`active`,`ts`,`addtime`) VALUES (:cid,:name,:price,:cost,:cost2,:prid,:prices,:input,:inputs,:desc,:alert,:shopimg,:value,:is_curl,:curl,:showcontent,:shequ,:goods_id,:goods_type,:goods_param,:repeat,:multi,:min,:max,:validate,:valiserv,:sort,:active,:ts,:addtime)";
		$params = array(
			":cid" => $cid, ":name" => $name, ":price" => $price, ":cost" => $cost, ":cost2" => $cost2, ":prid" => $prid, ":prices" => $prices, ":input" => $input, ":inputs" => $inputs, ":desc" => $desc, ":alert" => $alert, ":shopimg" => $shopimg, ":value" => $value, ":is_curl" => $is_curl, ":curl" => $curl, ":showcontent" => $showcontent, ":shequ" => $shequ, ":goods_id" => $goods_id, ":goods_type" => $goods_type, ":goods_param" => $goods_param, ":repeat" => $repeat, ":multi" => $multi, ":min" => $min, ":max" => $max, ":validate" => $validate, ":valiserv" => $valiserv, ":sort" => $sort + 1, ":active" => 1, ":ts" => $ts, ":addtime" => $date
		);
		if ($DB->exec($sql, $params) !== false) {
			$tid = $DB->lastInsertId();
			$content = "上架：{$name}";
			$DB->exec("INSERT INTO `pre_toollogs` (`content`,`date`,`addtime`,`active`) VALUES ('{$content}', CURDATE(), NOW(), 1)");
			showmsg("添加商品成功！<br/><br/><a href='./shoplist.php?cid=$cid'>>>返回商品列表</a>", 1);
		} else {
			showmsg("添加商品失败！" . $DB->error(), 4);
		}
	}
} elseif ($my == "edit_submit") {
	$tid = intval($_GET["tid"] ?? 0);
	$rows = $DB->getRow("select * from pre_tools where tid=:tid limit 1", array(":tid" => $tid));
	if (!$rows) {
		showmsg("当前记录不存在！", 3);
	}
	$cid = trim($_POST["cid"] ?? '');
	$name = trim($_POST["name"] ?? '');
	$prid = trim($_POST["prid"] ?? '');
	$price = $prid == "0" ? $_POST["price"] ?? '' : $_POST["price1"] ?? '';
	$cost = trim($_POST["cost"] ?? '');
	$cost2 = trim($_POST["cost2"] ?? '');
	if ($prid == 0) {
		if ($cost2 > $cost) {
			showmsg("专业版加价不能高于普及版加价", 3);
		} elseif ($cost2 > $price) {
			showmsg("专业版加价不能高于普通用户加价", 3);
		} elseif ($cost > $price) {
			showmsg("普及版加价不能高于普通用户加价", 3);
		}
	}
	$prices = trim($_POST["prices"] ?? '');
	$input = trim($_POST["input"] ?? '');
	$inputs = trim($_POST["inputs"] ?? '');
	$desc = trim($_POST["desc"] ?? '');
	$alert = trim($_POST["alert"] ?? '');
	$shopimg = trim($_POST["shopimg"] ?? '');
	$value = trim($_POST["value"] ?? '');
	$multi = trim($_POST["multi"] ?? '');
	$min = trim($_POST["min"] ?? '');
	$max = trim($_POST["max"] ?? '');
	$validate = trim($_POST["validate"] ?? '');
	$valiserv = trim($_POST["valiserv"] ?? '');
	$is_curl = trim($_POST["is_curl"] ?? '');
	$curl = trim($_POST["curl"] ?? '');
	$showcontent = trim($_POST["showcontent"] ?? '');
	$shequ = trim($_POST["shequ"] ?? '');
	$goods_id = trim($_POST["goods_id"] ?? '');
	$goods_type = trim($_POST["goods_type"] ?? '');
	$goods_param = $is_curl == 1 ? $_POST["curl_post"] ?? '' : $_POST["goods_param"] ?? '';
	$repeat_default = isset($_POST["repeat_default"]) ? 1 : 0;
	$repeat = $repeat_default == 1 ? 1 : $_POST["repeat"] ?? '';
	$ts = trim($_POST["ts"] ?? '');
	if(empty($cid)) $cid = 0;
	if(empty($shequ)) $shequ = 0;
	if(empty($goods_id)) $goods_id = 0;
	if(empty($goods_type)) $goods_type = 0;
	if ($name == NULL || $price == NULL) {
		showmsg("保存错误，商品名称和价格不能为空！", 3);
	} elseif ($is_curl == 2 && !$shequ) {
		showmsg("请选择对接社区！", 3);
	} else {
		$sql = "UPDATE `pre_tools` SET `cid`=:cid,`name`=:name,`price`=:price,`cost`=:cost,`cost2`=:cost2,`prid`=:prid,`prices`=:prices,`input`=:input,`inputs`=:inputs,`desc`=:desc,`alert`=:alert,`shopimg`=:shopimg,`value`=:value,`is_curl`=:is_curl,`curl`=:curl,`showcontent`=:showcontent,`shequ`=:shequ,`goods_id`=:goods_id,`goods_type`=:goods_type,`goods_param`=:goods_param,`repeat`=:repeat,`multi`=:multi,`min`=:min,`max`=:max,`validate`=:validate,`valiserv`=:valiserv,`ts`=:ts WHERE `tid`=:tid";
		$params = array(
			":cid" => $cid, ":name" => $name, ":price" => $price, ":cost" => $cost, ":cost2" => $cost2, ":prid" => $prid, ":prices" => $prices, ":input" => $input, ":inputs" => $inputs, ":desc" => $desc, ":alert" => $alert, ":shopimg" => $shopimg, ":value" => $value, ":is_curl" => $is_curl, ":curl" => $curl, ":showcontent" => $showcontent, ":shequ" => $shequ, ":goods_id" => $goods_id, ":goods_type" => $goods_type, ":goods_param" => $goods_param, ":repeat" => $repeat, ":multi" => $multi, ":min" => $min, ":max" => $max, ":validate" => $validate, ":valiserv" => $valiserv, ":ts" => $ts, ":tid" => $tid
		);
		if ($DB->exec($sql, $params) !== false) {
			showmsg("修改商品成功！<br/><br/><a href=\"./shoplist.php?cid=$cid\">>>返回商品列表</a>", 1);
		} else {
			showmsg("修改商品失败！" . $DB->error(), 4);
		}
	}
}
?>
<script>
var isAdd = true;
</script>
<script src="<?php echo $cdnpublic;?>layer/3.1.1/layer.js"></script>
<script src="assets/js/shopedit.js?ver=<?php echo VERSION;?>"></script>
<?php 
if ($conf["shopdesc_editor"]) {
	?><script charset="utf-8" src="../assets/kindeditor/kindeditor-all-min.js"></script>
<script charset="utf-8" src="../assets/kindeditor/zh-CN.js"></script>
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
// 原生模态框简单关闭修复支持
$(document).ready(function() {
    $('[data-dismiss="modal"]').on('click', function() {
        $(this).closest('.modal').addClass('hidden');
    });
    $('[data-toggle="modal"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $(target).removeClass('hidden');
    });
});
</script>
</body>
</html>
