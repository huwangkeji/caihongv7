<?php

include "../includes/common.php";
$title = "克隆站点";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="w-full max-w-[1000px] mx-auto space-y-6">
<?php 
adminpermission("super", 1);
if (isset($_POST["submit"])) {
	$url = trim($_POST["url"]);
	$key = trim($_POST["key"]);
	$url_arr = parse_url($url);
	if ($url_arr["host"] == $_SERVER["HTTP_HOST"]) {
		showmsg("无法自己克隆自己", 3);
	}
	$data = get_curl($url . "api.php?act=clone&key=" . $key);
	$arr = json_decode($data, true);
	if (array_key_exists("code", $arr) && $arr["code"] == 1) {
		$success = 0;
		if (count($arr["class"]) < 2 || count($arr["tools"]) < 5) {
			showmsg("对方网站数据量过少。", 3);
		}
		if (!isset($arr["price"])) {
			showmsg("对方网站程序版本较低", 3);
		}
		if ($_POST["type"] == 1) {
			if (isset($_POST["tid"])) {
				$classa = array();
				$pricea = array();
				$shequa = array();
				foreach ($arr["tools"] as $row) {
					if (!in_array($row["tid"], $_POST["tid"])) {
						continue;
					}
					if (!array_key_exists($row["cid"], $classa)) {
						$classa[$row["cid"]] = 0;
					}
					if (!array_key_exists($row["prid"], $pricea)) {
						$pricea[$row["prid"]] = 0;
					}
					if (!array_key_exists($row["shequ"], $shequa)) {
						$shequa[$row["shequ"]] = 0;
					}
				}
				foreach ($arr["class"] as $row) {
					if (!array_key_exists($row["cid"], $classa)) {
						continue;
					}
					$srow = $DB->getRow("select cid from pre_class where name='" . $row["name"] . "' limit 1");
					if ($srow) {
						$classa[$row["cid"]] = $srow["cid"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_class` (`name`,`sort`,`active`,`shopimg`) VALUES ('" . $row["name"] . "','" . $row["sort"] . "','" . $row["active"] . "','" . $row["shopimg"] . "')");
						$classa[$row["cid"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["shequ"] as $row) {
					if (!array_key_exists($row["id"], $shequa)) {
						continue;
					}
					$srow = $DB->getRow("select id from pre_shequ where url='" . $row["url"] . "' limit 1");
					if ($srow) {
						$shequa[$row["id"]] = $srow["id"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_shequ` (`url`,`username`,`password`,`type`) VALUES ('" . $row["url"] . "',NULL,NULL,'" . $row["type"] . "')");
						$shequa[$row["id"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["price"] as $row) {
					if (!array_key_exists($row["prid"], $pricea)) {
						continue;
					}
					$srow = $DB->getRow("select id from pre_price where kind='" . $row["kind"] . "' and name='" . $row["name"] . "' limit 1");
					if ($srow) {
						$pricea[$row["id"]] = $srow["id"];
					} else {
						$id = $DB->exec("INSERT INTO `pre_price` (`kind`,`name`,`p_0`,`p_1`,`p_2`) VALUES ('" . $row["kind"] . "','" . $row["name"] . "','" . $row["p_0"] . "','" . $row["p_1"] . "','" . $row["p_2"] . "')");
						$pricea[$row["id"]] = $DB->lastInsertId();
						$success++;
					}
				}
				foreach ($arr["tools"] as $row) {
					if (!in_array($row["tid"], $_POST["tid"])) {
						continue;
					}
					if (!$DB->getRow("select tid from pre_tools where name='" . $row["name"] . "' and is_curl='" . $row["is_curl"] . "' limit 1")) {
						$DB->exec("INSERT INTO `pre_tools` (`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`valiserv`,`min`,`max`,`validate`,`sort`,`close`,`active`) VALUES ('" . $classa[$row["cid"]] . "','" . $row["name"] . "','" . $row["price"] . "','" . $row["cost"] . "','" . $row["cost2"] . "','" . $pricea[$row["prid"]] . "','" . $row["prices"] . "','" . $row["input"] . "','" . $row["inputs"] . "','" . addslashes($row["desc"]) . "','" . addslashes($row["alert"]) . "','" . $row["shopimg"] . "','" . $row["value"] . "','" . $row["is_curl"] . "','" . $row["curl"] . "','" . $shequa[$row["shequ"]] . "','" . $row["goods_id"] . "','" . $row["goods_type"] . "','" . $row["goods_param"] . "','" . $row["repeat"] . "','" . $row["multi"] . "','" . $row["min"] . "','" . $row["max"] . "','" . $row["validate"] . "','" . $row["valiserv"] . "','" . $row["sort"] . "','" . $row["close"] . "','" . $row["active"] . "')");
						$success++;
					}
				}
				$CACHE->clear("pricerules");
				showmsg("克隆完成，SQL成功执行" . $success . "句。", 1, "./clone.php");
			} else {
				?>	  
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-list-alt text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">请选择要增量克隆的商品</h3>
        </div>
        <div class="p-6">
          <form action="?" method="POST" role="form" class="space-y-4">
		  <input type="hidden" name="url" value="<?php echo $url;?>">
		  <input type="hidden" name="key" value="<?php echo $key;?>">
		  <input type="hidden" name="type" value="1">
          
          <div class="space-y-3">
		  <?php 
				foreach ($arr["class"] as $row) {
					?>
            <details class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden group">
                <summary class="px-5 py-4 cursor-pointer font-bold text-slate-700 bg-white hover:bg-slate-50 flex justify-between items-center transition-colors list-none select-none">
                    <span class="flex items-center"><i class="fa fa-folder-open-o text-brand-500 mr-2"></i><?php echo $row["name"];?></span>
                    <i class="fa fa-angle-down text-slate-400 group-open:rotate-180 transition-transform duration-300"></i>
                </summary>
                <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 bg-white border-t border-slate-100">
                    <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                        <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3 font-semibold w-24">
                                    <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                                        <input type="checkbox" onclick="SelectAll(<?php echo $row["cid"];?>,this)" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                        <span class="ml-2 font-bold text-slate-700">全选</span>
                                    </label>
                                </th>
                                <th class="px-4 py-3 font-semibold min-w-[200px]">商品名称</th>
                                <th class="px-4 py-3 font-semibold text-center w-24">状态</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                        <?php 
                        foreach ($arr["tools"] as $rows) {
                            if ($row["cid"] == $rows["cid"]) {
                                echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200\">
                                        <td class=\"px-4 py-3 text-center\">
                                            <label class=\"inline-flex items-center cursor-pointer m-0\">
                                                <input name=\"tid[]\" type=\"checkbox\" class=\"class" . $rows["cid"] . " w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer\" id=\"tid\" value=\"" . $rows["tid"] . "\">
                                                <span class=\"ml-2 text-slate-500 font-mono text-xs\">#" . $rows["tid"] . "</span>
                                            </label>
                                        </td>
                                        <td class=\"px-4 py-3 font-medium text-slate-700\">" . $rows["name"] . "</td>
                                        <td class=\"px-4 py-3 text-center\">" . ($rows["close"] == 1 ? "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100\">已下架</span>" : "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">上架中</span>") . " " . ($rows["active"] == 1 ? "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100\">显示</span>" : "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200\">隐藏</span>") . "</td>
                                      </tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </details>
		  <?php 
				}
				?>
          </div>
          <div class="pt-4 mt-4 border-t border-slate-50">
              <button type="submit" name="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                  执行增量克隆
              </button>
          </div>
          </form>
        </div>
      </div>
<?php 
			}
		} else {
			$DB->exec("TRUNCATE TABLE `pre_class`");
			$success++;
			foreach ($arr["class"] as $row) {
				$DB->exec("INSERT INTO `pre_class` (`cid`,`name`,`sort`,`active`,`shopimg`) VALUES ('" . $row["cid"] . "','" . $row["name"] . "','" . $row["sort"] . "','" . $row["active"] . "','" . $row["shopimg"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_tools`");
			$success++;
			foreach ($arr["tools"] as $row) {
				$DB->exec("INSERT INTO `pre_tools` (`tid`,`cid`,`name`,`price`,`cost`,`cost2`,`prid`,`prices`,`input`,`inputs`,`desc`,`alert`,`shopimg`,`value`,`is_curl`,`curl`,`shequ`,`goods_id`,`goods_type`,`goods_param`,`repeat`,`multi`,`min`,`max`,`validate`,`valiserv`,`sort`,`close`,`active`) VALUES ('" . $row["tid"] . "','" . $row["cid"] . "','" . $row["name"] . "','" . $row["price"] . "','" . $row["cost"] . "','" . $row["cost2"] . "','" . $row["prid"] . "','" . $row["prices"] . "','" . $row["input"] . "','" . $row["inputs"] . "','" . addslashes($row["desc"]) . "','" . addslashes($row["alert"]) . "','" . $row["shopimg"] . "','" . $row["value"] . "','" . $row["is_curl"] . "','" . $row["curl"] . "','" . $row["shequ"] . "','" . $row["goods_id"] . "','" . $row["goods_type"] . "','" . $row["goods_param"] . "','" . $row["repeat"] . "','" . $row["multi"] . "','" . $row["min"] . "','" . $row["max"] . "','" . $row["validate"] . "','" . $row["valiserv"] . "','" . $row["sort"] . "','" . $row["close"] . "','" . $row["active"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_shequ`");
			$success++;
			foreach ($arr["shequ"] as $row) {
				$DB->exec("INSERT INTO `pre_shequ` (`id`,`url`,`username`,`password`,`type`) VALUES ('" . $row["id"] . "','" . $row["url"] . "',NULL,NULL,'" . $row["type"] . "')");
				$success++;
			}
			$DB->exec("TRUNCATE TABLE `pre_price`");
			$success++;
			foreach ($arr["price"] as $row) {
				$DB->exec("INSERT INTO `pre_price` (`id`,`kind`,`name`,`p_0`,`p_1`,`p_2`) VALUES ('" . $row["id"] . "','" . $row["kind"] . "','" . $row["name"] . "','" . $row["p_0"] . "','" . $row["p_1"] . "','" . $row["p_2"] . "')");
				$success++;
			}
			$CACHE->clear("pricerules");
			showmsg("克隆完成，SQL成功执行" . $success . "句。", 1);
		}
	} elseif (array_key_exists("code", $arr)) {
		showmsg("克隆失败，原因：" . $arr["msg"], 4);
	} else {
		showmsg("克隆失败，返回数据解析错误。", 4);
	}
} else {
	?>

	  <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-50 flex items-center bg-slate-50/50">
            <i class="fa fa-clone text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">克隆站点</h3>
        </div>
        <div class="p-6">
            
            <div class="space-y-3 mb-6">
                <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start shadow-sm">
                    <i class="fa fa-info-circle mt-0.5 mr-2 shrink-0 text-lg"></i>
                    <span class="leading-relaxed">使用此功能可一键克隆目标站点的分类、商品及社区对接数据（除社区账号密码外），方便站长快速丰富网站内容。</span>
                </div>
                
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-sm flex items-start shadow-sm">
                    <i class="fa fa-exclamation-triangle mt-0.5 mr-2 shrink-0 text-lg"></i>
                    <span class="leading-relaxed font-bold">警告：使用全量克隆将会直接清空本站所有现有的商品和分类数据，请谨慎操作！</span>
                </div>
            </div>
            
            <form action="?" method="POST" role="form" class="space-y-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">目标站点 URL</label>
                    <div class="flex gap-2">
                        <input type="text" name="url" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors flex-1" placeholder="例如：http://www.qq.com/" required/>
                        <button type="button" class="inline-flex items-center justify-center px-4 h-11 rounded-xl bg-sky-500 text-white font-medium hover:bg-sky-600 shadow-sm transition-colors shrink-0 text-sm" onclick="checkurl()">
                            <i class="fa fa-wifi mr-1.5"></i>连通性检测
                        </button>
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">克隆授权密钥</label>
                    <input type="text" name="key" value="" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="请联系目标站点管理员取得" required/>
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">请选择克隆模式</label>
                    <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="type">
                        <option value="1">增量克隆（安全：挑选克隆，不影响本站现有数据）</option>
                        <option value="0" class="text-rose-600 font-bold">全量克隆（危险：强行覆盖，清空本站现有所有商品与分类）</option>
                    </select>
                </div>
                
                <div class="pt-2">
                    <button type="submit" name="submit" class="w-full h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        确定执行克隆
                    </button>
                </div>
            </form>
            
        </div>
		<div class="px-6 py-4 bg-slate-50 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-center">
          <i class="fa fa-key text-brand-500 mr-2 text-sm"></i> 
          <span>若要允许他人克隆您的站点，请前往本站的克隆密钥：<a href="./set.php?mod=cloneset" class="text-brand-600 font-medium hover:underline ml-1">点此获取与设置</a></span>
        </div>
      </div>
<?php 
}
?>
</div>

<!-- 补充折叠组件自定义样式支持 -->
<style type="text/tailwindcss">
@layer utilities {
    details > summary {
        list-style: none;
    }
    details > summary::-webkit-details-marker {
        display: none;
    }
}
</style>

<script src="<?php echo $cdnpublic;?>layer/3.0.1/layer.js"></script>
<script>
function SelectAll(cid,chkAll) {
	var items = $('.class'+cid);
	for (i = 0; i < items.length; i++) {
		if (items[i].id.indexOf("tid") != -1 && items[i].type == "checkbox") {
			items[i].checked = chkAll.checked;
		}
	}
}

function checkurl(){
	var url = $("input[name='url']").val();
	if(url.indexOf('http')>=0 && url.substr(-1) == '/'){
		var ii = layer.load(2, {shade:[0.1,'#fff']});
		$.ajax({
			type : "POST",
			url : "ajax.php?act=checkclone",
			data : {url:url},
			dataType : 'json',
			success : function(data) {
				layer.close(ii);
				if(data.code == 1){
					layer.msg('连通性良好，可以安全克隆', {icon: 1});
				}else if(data.code == 2){
					layer.alert('系统错误：无法自己克隆自己', {icon: 2});
				}else if(data.code == 3){
					layer.alert('提示：对方网站的源码可能被用记事本改过，请先在对方网站清理 BOM 头部', {icon: 0});
				}else{
					layer.alert('连接失败：对方网站无法连接或存在金盾、云锁等防火墙拦截', {icon: 2});
				}
			} ,
			error:function(data){
				layer.close(ii);
				layer.msg('请求发送失败，目标 URL 连接超时', {icon: 2});
				return false;
			}
		});
	}else{
		layer.alert('URL 格式不合法：必须以 http:// 或 https:// 开头，并以 / 结尾', {icon: 2});
	}
}
</script>
