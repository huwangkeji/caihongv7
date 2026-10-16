<?php
/**
 * 供货商商品审核管理
 **/
include("../includes/common.php");
$title='供货商商品审核管理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");


adminpermission('shop', 1);

$my=isset($_GET['my'])?$_GET['my']:null;

$rs=$DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$select='<option value="0">未归类</option>';
$shua_class[0]='未归类';
while($res = $rs->fetch()){
    $shua_class[$res['cid']]=$res['name'];
    $select.='<option value="'.$res['cid'].'">'.$res['name'].'</option>';
}

if ($_SESSION['priceselect']) {
    $priceselect=$_SESSION['priceselect'];
} else {
    $rs=$DB->query("SELECT * FROM pre_price order by id asc");
    $priceselect='<option value="0">独立定价 (不使用模板)</option>';
    while($res = $rs->fetch()){
        $kind = $res['kind']==1?'元':'倍';
        $priceselect.='<option value="'.$res['id'].'" kind="'.$res['kind'].'" p_2="'.$res['p_2'].'" p_1="'.$res['p_1'].'" p_0="'.$res['p_0'].'" >'.$res['name'].'('.$res['p_2'].$kind.'|'.$res['p_1'].$kind.'|'.$res['p_0'].$kind.')</option>';
    }
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">

<?php
if($my=='edit')
{
$tid=$_GET['tid'];
$row=$DB->getRow("select * from pre_tools where tid='$tid' limit 1");
?>
<form action="./supshoplist.php?my=edit_submit&tid=<?php echo $tid?>" method="POST" onsubmit="return checkinput()">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-gavel text-brand-500 mr-2 text-lg"></i>供货商货源审核与参数补全
            </h3>
            <span class="ml-3 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-600 border border-amber-100">待审批 TID: #<?php echo $tid; ?></span>
        </div>
        
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- 基础信息板块 -->
            <div class="space-y-6">
                <h4 class="text-sm font-bold text-brand-600 m-0 border-b border-brand-100 pb-2 flex items-center"><i class="fa fa-info-circle mr-1.5"></i>商品基础归类</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">业务划分层级分类</label>
                        <select name="cid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $row['cid']?>">
                            <?php echo $select?>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">对客展示商品名称 <span class="text-rose-500">*</span></label>
                        <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors font-bold" name="name" value="<?php echo htmlspecialchars($row['name'])?>" required>
                    </div>
                </div>
            </div>

            <!-- 定价控制板块 -->
            <div class="space-y-6">
                <h4 class="text-sm font-bold text-brand-600 m-0 border-b border-brand-100 pb-2 flex items-center"><i class="fa fa-money mr-1.5"></i>利润及销售定价审核</h4>
                
                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-sm font-semibold text-slate-700">绑定平台加价策略模板</label>
                        <a href="./price.php" target="_blank" class="text-xs text-brand-600 hover:underline font-medium">配置新模板 <i class="fa fa-external-link"></i></a>
                    </div>
                    <select name="prid" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" default="<?php echo $row['prid']?>">
                        <?php echo $priceselect?>
                    </select>
                </div>

                <div class="space-y-1.5" id="prid1" style="display:none;">
                    <label class="text-sm font-semibold text-slate-700">供货商提报底价 (成本)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 font-semibold">¥</div>
                        <input type="text" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-100 text-slate-500 transition-colors font-mono font-bold cursor-not-allowed" name="price1" value="<?php echo $row['sup_price']?>" readonly>
                    </div>
                </div>

                <div id="prid0" class="p-5 bg-slate-50 border border-slate-200 rounded-xl shadow-inner space-y-4">
                    <div class="flex items-center text-slate-600 text-sm font-semibold mb-2">
                        <i class="fa fa-info-circle mr-1 text-slate-400"></i>独立定制定价 (当前供货商提报底价: <span class="text-rose-500 font-mono font-bold mx-1">¥ <?php echo $row['sup_price']?></span>)
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">普通用户售价 <span class="text-rose-500">*</span></label>
                            <input type="text" name="price" value="<?php echo $row['sup_price']?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm font-mono font-bold text-emerald-600 transition-colors"/>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">普及版代理价</label>
                            <input type="text" name="cost" value="<?php echo $row['sup_price']?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm font-mono text-sky-600 transition-colors" placeholder="不填则同售价"/>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">专业版代理价</label>
                            <input type="text" name="cost2" value="<?php echo $row['sup_price']?>" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm font-mono text-purple-600 transition-colors" placeholder="不填则同普及版"/>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">大客户批发阶梯立减优惠</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors font-mono" name="prices" value="<?php echo htmlspecialchars($row['prices'])?>">
                    <p class="text-xs text-slate-400 mt-1">解析格式示例：<code class="text-brand-600 bg-slate-50 px-1 rounded font-bold">10|0.1,20|0.3</code> (满10件单价降1毛)</p>
                </div>
            </div>

            <!-- 下单参数与规则设置 -->
            <div class="space-y-6">
                <h4 class="text-sm font-bold text-brand-600 m-0 border-b border-brand-100 pb-2 flex items-center"><i class="fa fa-keyboard-o mr-1.5"></i>前端展示与入参控制</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">主输入框标题</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="input" value="<?php echo htmlspecialchars($row['input'])?>" placeholder="留空默认为“下单账号”">
                            <button type="button" class="w-11 h-11 flex items-center justify-center rounded-xl bg-sky-50 text-sky-500 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0 border border-sky-100" data-toggle="modal" data-target="#inputabout" title="查看智能获取配置">
                                <i class="fa fa-question-circle text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">附加输入框组</label>
                        <div class="flex gap-2">
                            <input type="text" class="flex-1 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="inputs" value="<?php echo htmlspecialchars($row['inputs'])?>" placeholder="无需更多输入框则留空">
                            <button type="button" class="w-11 h-11 flex items-center justify-center rounded-xl bg-sky-50 text-sky-500 hover:bg-sky-500 hover:text-white transition-colors shadow-sm shrink-0 border border-sky-100" data-toggle="modal" data-target="#inputsabout" title="查看单选及获取配置">
                                <i class="fa fa-question-circle text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">业务详情说明 (支持 HTML)</label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[120px]" id="editor_id" name="desc" placeholder="用户选择该商品时自动展示的介绍内容"><?php echo htmlspecialchars($row['desc'])?></textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">强提示模态拦截警告</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-rose-50 transition-colors placeholder:text-rose-300 text-rose-600 font-medium" name="alert" value="<?php echo htmlspecialchars($row['alert'])?>" placeholder="选购即弹出的居中 Alert (不支持 HTML)">
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">公网商品主图 URL</label>
                    <input type="file" id="file" onchange="fileUpload()" style="display:none;"/>
                    <div class="flex gap-2">
                        <input type="text" class="flex-1 h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" id="shopimg" name="shopimg" value="<?php echo $row['shopimg']?>" placeholder="无封面图则默认调用分类图标">
                        <button type="button" onclick="fileSelect()" class="inline-flex items-center justify-center px-4 h-11 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors shadow-sm shrink-0 font-medium" title="本地上传">上传</button>
                        <button type="button" onclick="fileView()" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50 text-amber-500 hover:bg-amber-500 hover:text-white transition-colors shadow-sm shrink-0" title="预览图片">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 风控与限制板块 -->
            <div class="space-y-6">
                <h4 class="text-sm font-bold text-brand-600 m-0 border-b border-brand-100 pb-2 flex items-center"><i class="fa fa-shield mr-1.5"></i>订单风控与额度限制</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">开放前台数量加减器</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="multi" default="<?php echo $row['multi']?>">
                            <option value="1">允许更改数量</option>
                            <option value="0">锁定数量为 1</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">防重复投单限制拦截</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="repeat" default="<?php echo $row['repeat']?>">
                            <option value="0">阻断重单</option>
                            <option value="1">允许并存</option>
                        </select>
                    </div>
                </div>

                <div id="multi0" style="display:none;" class="p-5 bg-slate-50 border border-slate-200 rounded-xl shadow-inner space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">强制单笔起订底量</label>
                            <input type="text" name="min" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm font-mono text-slate-700 transition-colors" value="<?php echo $row['min']?>" placeholder="留空默认基数为 1"/>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600">强制单笔购量封顶</label>
                            <input type="text" name="max" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm font-mono text-slate-700 transition-colors" value="<?php echo $row['max']?>" placeholder="留空则不限上限"/>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">关联自动探测风控接口</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="validate" default="<?php echo $row['validate']?>">
                            <option value="0">关闭跳过验证</option>
                            <option value="1">验证目标QQ空间开放状态</option>
                            <option value="2">探测目标业务存在(阻断提交)</option>
                            <option value="3">探测目标业务存在(转入本地库)</option>
                        </select>
                    </div>
                    
                    <div class="space-y-1.5" id="valiserv" style="display:none;">
                        <label class="text-sm font-semibold text-slate-700">探测的具体钻包标识</label>
                        <select class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" name="valiserv" default="<?php echo $row['valiserv']?>">
                            <option value="vip">基础QQ会员</option>
                            <option value="svip">超级QQ会员(SVIP)</option>
                            <option value="red">红钻</option>
                            <option value="green">绿钻</option>
                            <option value="sgreen">豪华绿钻</option>
                            <option value="yellow">黄钻</option>
                            <option value="syellow">豪华黄钻</option>
                            <option value="hollywood">腾讯视频VIP</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-4">
                <button type="submit" class="w-full sm:w-auto flex-1 h-12 rounded-xl bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-md hover:shadow-lg transition-all duration-200 active:scale-[0.98]">
                    <i class="fa fa-gavel mr-1.5"></i>最终确认，审核通过该商品
                </button>
                <a href="shoplist.php" class="inline-flex items-center justify-center w-full sm:w-40 h-12 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm shrink-0">
                    放弃并返回列表
                </a>
            </div>

        </div>
    </div>
</form>

<?php
}
elseif($my=='edit_submit')
{
$tid=$_GET['tid'];
$rows=$DB->getRow("select * from pre_tools where tid='$tid' and goods_sid != 0 and audit_status='0' limit 1");
if(!$rows)
    showmsg('当前记录不存在或该商品无需审核！',3);
$cid=$_POST['cid'];
$name=$_POST['name'];
$prid=$_POST['prid'];
$price=($prid=='0' ? $_POST['price'] : $_POST['price1']);
if($price < $rows['sup_price'])showmsg('商品对客销售价格绝不能低于供货商的供货底价！',3);
$cost=$_POST['cost'];
$cost2=$_POST['cost2'];
$prices=$_POST['prices'];
$input=$_POST['input'];
$inputs=$_POST['inputs'];
$desc=$_POST['desc'];
$alert=$_POST['alert'];
$shopimg=$_POST['shopimg'];
$multi=$_POST['multi'];
$min=$_POST['min'];
$max=$_POST['max'];
$validate=$_POST['validate'];
$valiserv=$_POST['valiserv'];
$repeat=$_POST['repeat'];

if(($name==NULL || $price==NULL)){
    showmsg('保存错误，商品展示名称和销售价格绝对不能为空！',3);
} else {
    // 根据原版逻辑，这里去除了未声明的 $is_curl / $shequ 报错
    if($DB->exec("UPDATE `pre_tools` SET `cid`='".$cid."',`name`='".$name."',`price`='".$price."',`cost`='".$cost."',`cost2`='".$cost2."',`prid`='".$prid."',`prices`='".$prices."',`input`='".$input."',`inputs`='".$inputs."',`desc`='".addslashes($desc)."',`alert`='".addslashes($alert)."',`shopimg`='".$shopimg."',`repeat`='".$repeat."',`multi`='".$multi."',`min`='".$min."',`max`='".$max."',`validate`='".$validate."',`valiserv`='".$valiserv."',`active`='1',`audit_status`='1' WHERE `tid`='".$tid."'")!==false)
        showmsg('供货商品安全审计通过，已成功投入库列生效！<br/><br/><a href="./supshoplist.php" class="inline-flex mt-4 px-4 py-2 bg-brand-50 text-brand-600 rounded-lg hover:bg-brand-100">>>返回待审核池继续操作</a>',1);
    else
        showmsg('商品审核入库失败异常！'.$DB->error(),4);
}
}else{
?>

    <?php
    adminpermission('shop', 1);

    $rs=$DB->query("SELECT * FROM pre_price order by id asc");
    $priceselect='<option value="0">不使用加价模板</option>';
    $price_class[0]='不加价';
    while($res = $rs->fetch()){
        $kind = $res['kind']==1?'元':'倍';
        $priceselect.='<option value="'.$res['id'].'" kind="'.$res['kind'].'" p_2="'.$res['p_2'].'" p_1="'.$res['p_1'].'" p_0="'.$res['p_0'].'" >'.$res['name'].'('.$res['p_2'].$kind.'|'.$res['p_1'].$kind.'|'.$res['p_0'].$kind.')</option>';
        $price_class[$res['id']]=$res['name'];
    }
    $_SESSION['priceselect']=$priceselect;
    $_SESSION['price_class']=$price_class;

    $my=isset($_GET['my'])?$_GET['my']:null;

    if($my=='qk2')
    {
        $sql="TRUNCATE TABLE `pre_tools`";
        if($DB->exec($sql)!==false)
            exit("<script language='javascript'>alert('高危：表数据截断清空执行成功！');window.location.href='shoplist.php';</script>");
        else
            exit("<script language='javascript'>alert('致命故障：数据截断清空失败！".$DB->error()."');history.go(-1);</script>");
    }
    else
    {

    $cid = isset($_GET['cid'])?intval($_GET['cid']):0;
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-4">
        
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h2 id="blocktitle" class="text-base sm:text-lg font-bold text-slate-800 m-0">
                <!-- 预留由 js 渲染标题 -->
            </h2>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-sm font-semibold text-slate-600 shrink-0">单页载入数量</span>
                <select id="pagesize" class="h-9 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" title="控制每页渲染行数">
                    <option value="30">30行 / 页</option>
                    <option value="50">50行 / 页</option>
                    <option value="60">60行 / 页</option>
                    <option value="80">80行 / 页</option>
                    <option value="100">100行 / 页</option>
                </select>
            </div>
        </div>
        
        <div class="p-6">
            <form onsubmit="return searchItem()" method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-slate-400"></i>
                    </div>
                    <input type="text" class="w-full h-11 pl-9 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm transition-colors" name="kw" placeholder="输入待审核的商品关键特征进行搜索定位">
                </div>
                <div class="flex gap-2 shrink-0">
                    <button type="submit" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-sky-500 text-white font-medium text-sm hover:bg-sky-600 shadow-sm transition-colors">
                        精准检索
                    </button>
                    <a href="javascript:listTable('start')" class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-brand-600 transition-colors shadow-sm" title="无痕重刷审批池">
                        <i class="fa fa-refresh"></i>
                    </a>
                </div>
            </form>

            <div id="listTable" class="w-full"></div>
            
            <?php if(!isset($_GET['cid'])) echo '<div class="mt-4 p-4 bg-slate-50 border border-slate-100 rounded-xl text-slate-500 text-sm shadow-sm flex items-start"><i class="fa fa-info-circle mr-2 mt-0.5 text-slate-400"></i><span>温馨提示：只有在查看具体单个分类的商品列表时，才可以进行自由的商品排序拖拽操作。</span></div>';?>
        </div>
    </div>
    <?php }?>

<?php } ?>

</div>

<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.js"></script>
<script src="assets/js/supshoplist.js?ver=<?php echo VERSION ?>"></script>
<script>
// 初始化下拉默认选项绑定
$(document).ready(function(){
    var items = $("select[default]");
    for (i = 0; i < items.length; i++) {
        $(items[i]).val($(items[i]).attr("default")||0);
    }
});
</script>
</body>
</html>
