<?php
/**
 * 裂变下级分站管理总控中心
**/
include("../includes/common.php");
$title='分站管理';
include './head.php';

// 登录态阻断
if($islogin2==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-12 px-4 sm:px-0 mt-4">

<?php
// 权限拦截：仅限专业版合伙人及以上
if($userrow['power']<2){
	showmsg('权限阻断：仅全功能专业版合伙人支持无限搭建并管理下级分站！',3);
}

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
    $domains=explode(',',$conf['fenzhan_domain']);
    $select='';
    foreach($domains as $domain){
        $select.='<option value="'.$domain.'">'.$domain.'</option>';
    }
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-3xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-sitemap text-indigo-500 mr-2 text-xl"></i>手工添加下级分站
            </h3>
            <a href="./sitelist.php" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-reply mr-1"></i>返回分站列表</a>
        </div>
        
        <div class="p-6">
            <form action="./sitelist.php?my=add_submit" method="POST" class="space-y-5">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 border-b border-slate-100 pb-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">下级管理员账号 <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-user"></i></div>
                            <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-white placeholder:font-normal" name="user" required placeholder="建议填QQ号">
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">初始登录密码 <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-lock"></i></div>
                            <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-slate-50" name="pwd" value="123456" required>
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">分配域名 <span class="text-rose-500">*</span></label>
                    <div class="flex shadow-sm rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 transition-all bg-white">
                        <input type="text" class="flex-1 h-11 px-4 text-sm font-mono font-bold border-0 focus:ring-0 placeholder:text-slate-300 placeholder:font-normal" name="qz" required placeholder="输入二级域名前缀 (字母/数字)">
                        <div class="h-11 flex items-center justify-center text-slate-400 bg-white font-mono font-bold px-2">.</div>
                        <select name="domain" class="w-1/3 h-11 px-2 text-sm bg-slate-50 border-0 border-l border-slate-200 focus:ring-0 cursor-pointer font-mono font-bold text-slate-700">
                            <?php echo $select?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">站点名称</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-tag"></i></div>
                            <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-white" name="sitename" value="<?php echo htmlspecialchars($conf['sitename']); ?>">
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 ml-1">站长Q Q</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-qq"></i></div>
                            <input type="number" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm transition-colors bg-white" name="qq" placeholder="选填，用于下级找回密码">
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 border-t border-slate-100 pt-5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">授权到期界限 <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500"><i class="fa fa-calendar"></i></div>
                        <input type="date" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors bg-emerald-50/30 text-emerald-700" name="endtime" value="<?php echo date("Y-m-d",strtotime("+1 years")); ?>" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-xl bg-gradient-to-r from-indigo-500 to-sky-500 text-white font-bold text-[15px] hover:from-indigo-600 hover:to-sky-600 shadow-md shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-plus-circle mr-1.5"></i>确认参数并创建下级
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
}

elseif($my=='edit')
{
    $zid=intval($_GET['zid']);
    $row=$DB->getRow("SELECT * FROM pre_site WHERE zid='$zid' AND upzid='{$userrow['zid']}' AND power=1 LIMIT 1");
    if(!$row) showmsg('查无此档：未找到该下属站点的历史归档！',3);
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden max-w-2xl mx-auto">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight flex items-center">
                <i class="fa fa-edit text-amber-500 mr-2 text-xl"></i>修改分站站点配置
            </h3>
            <a href="./sitelist.php" class="px-4 py-2 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition-colors shadow-sm"><i class="fa fa-reply mr-1"></i>返回分站列表</a>
        </div>
        
        <div class="p-6">
            <form action="./sitelist.php?my=edit_submit&zid=<?php echo $zid; ?>" method="POST" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">原始主域名</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-globe"></i></div>
                        <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 bg-slate-50 text-slate-500 shadow-inner cursor-not-allowed focus:ring-0" name="domain" value="<?php echo htmlspecialchars($row['domain']); ?>" disabled>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">额外备用信道 (备用域名)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-sky-500"><i class="fa fa-link"></i></div>
                        <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-mono font-bold rounded-xl border-slate-200 focus:border-sky-500 focus:ring-sky-500 shadow-sm transition-colors bg-white placeholder:font-normal placeholder:text-slate-300" name="domain2" value="<?php echo htmlspecialchars($row['domain2']); ?>" placeholder="无备用需求请留空">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">站点招牌名称</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-tag"></i></div>
                        <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 shadow-sm transition-colors bg-white" name="sitename" value="<?php echo htmlspecialchars($row['sitename']); ?>">
                    </div>
                </div>

                <div class="space-y-1.5 border-t border-slate-100 pt-5">
                    <label class="text-sm font-semibold text-slate-700 ml-1">生命周期界定 (到期时间) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-rose-500"><i class="fa fa-calendar-times-o"></i></div>
                        <input type="date" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition-colors bg-rose-50/30 text-rose-700" name="endtime" value="<?php echo date("Y-m-d",strtotime($row['endtime'])); ?>" required>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-12 rounded-xl bg-amber-500 text-white font-bold text-[15px] hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-save mr-1.5"></i>覆写配置并落盘
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
}
// ==========================================
// 视图分支 3：添加执行动作
// ==========================================
elseif($my=='add_submit')
{
    if(!$conf['fenzhan_adds']) showmsg('阻断：系统已关闭后台手工发站，请引导客户前往前台自助开通。');
    
    $user=trim(htmlspecialchars(strip_tags(daddslashes($_POST['user']))));
    $pwd=trim(htmlspecialchars(strip_tags(daddslashes($_POST['pwd']))));
    $qz = trim(htmlspecialchars(strtolower(daddslashes($_POST['qz']))));
    $domain = trim(htmlspecialchars(strtolower(strip_tags(daddslashes($_POST['domain'])))));
    $qq=trim(htmlspecialchars(strip_tags(daddslashes($_POST['qq']))));
    $endtime=trim(htmlspecialchars(strip_tags(daddslashes($_POST['endtime']))));
    $sitename=trim(htmlspecialchars(strip_tags(daddslashes($_POST['sitename']))));
    
    $keywords=$conf['keywords'];
    $description=$conf['description'];
    $domain = $qz . '.' . $domain;
    $thtime =date("Y-m-d").' 00:00:00';
    
    // 严苛的安全防伪与冲突校验
    if($user==NULL or $pwd==NULL or $qz==NULL or $domain==NULL or $endtime==NULL){
    	showmsg('入参残缺：请确保核对每项红星必填字段！',3);
    } elseif (!in_array($_POST['domain'],explode(',',$conf['fenzhan_domain']))) {
    	showmsg('风控阻断：非法域名后缀注入企图！');
    } elseif (strlen($qz) < 2 || strlen($qz) > 10 || !preg_match('/^[a-z0-9\-]+$/',$qz)) {
    	showmsg('解析拒批：域名前缀格式破损，必须为2-10位的小写字母、数字或横杠组合！');
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/',$user)) {
    	showmsg('格式阻断：管理员用户名仅容许承载字母与数字！');
    } elseif (!preg_match('/^[a-zA-Z0-9\_\-\.]+$/',$domain)) {
    	showmsg('寻址异常：总域名格式验证不通！');
    } elseif ($DB->getRow("SELECT zid FROM pre_site WHERE user=:user LIMIT 1", [':user'=>$user])) {
    	showmsg('冲突：此管理账号已在全域库中存在归属记录！');
    } elseif (strlen($pwd) < 6) {
    	showmsg('弱秘钥警告：管理密钥强度不够，必须大于6位！');
    } elseif (strlen($sitename) < 2) {
    	showmsg('招牌长度下限：网站名不得少于2个字符！');
    } elseif (strlen($qq) < 5 || !preg_match('/^[0-9]+$/',$qq)) {
    	showmsg('溯源异常：QQ 号码编排规则不成立！');
    } elseif ($DB->getRow("SELECT zid FROM pre_site WHERE domain=:domain OR domain2=:domain LIMIT 1", [':domain'=>$domain]) || $qz=='www' || $domain==$_SERVER['HTTP_HOST'] || in_array($domain,explode(',',$conf['fenzhan_remain']))) {
    	showmsg('寻址冲突：此网域资产已被前人占用或已被总台列为保留对象！');
    } elseif ($DB->getColumn("SELECT count(*) FROM pre_site WHERE upzid='{$userrow['zid']}' and addtime>'$thtime'")>20) {
    	showmsg('高频限流：您今日手工作业孵化的分站数已触碰阈值，请指引客户走前台自助开通流水线！',3);
    } else {
        if($conf['fenzhan_html']==1){
        	$anounce=$conf['anounce'];
        	$alert=$conf['alert'];
        }
        
        $sql="INSERT INTO `pre_site` (`upzid`,`power`,`domain`,`domain2`,`user`,`pwd`,`rmb`,`qq`,`sitename`,`keywords`,`description`,`anounce`,`alert`,`addtime`,`endtime`,`status`) VALUES (:upzid, :power, :domain, NULL, :user, :pwd, :rmb, :qq, :sitename, :keywords, :description, :anounce, :alert, NOW(), :endtime, 1)";
        $data = [':upzid'=>$userrow['zid'], ':power'=>1, ':domain'=>$domain, ':user'=>$user, ':pwd'=>$pwd, ':rmb'=>'0.00', ':qq'=>$qq, ':sitename'=>$sitename, ':keywords'=>$keywords, ':description'=>$description, ':anounce'=>$anounce, ':alert'=>$alert, ':endtime'=>$endtime];
        
        if($DB->exec($sql, $data)){
        	showmsg('下级资源位建立与派发成功！<br/><br/><a href="./sitelist.php" class="text-indigo-600 font-bold hover:underline">>> 点击返回分站图谱</a>',1);
        }else{
        	showmsg('引擎崩溃导致落盘失败：'.$DB->error(),4);
        }
    }
}
// ==========================================
// 视图分支 4：修改执行动作
// ==========================================
elseif($my=='edit_submit')
{
    $zid=intval($_GET['zid']);
    $rows=$DB->getRow("SELECT * FROM pre_site WHERE zid='$zid' AND upzid='{$userrow['zid']}' AND power=1 LIMIT 1");
    if(!$rows) showmsg('查无此档，篡改失效！',3);
    
    $domain2=trim(strtolower(htmlspecialchars(strip_tags(daddslashes($_POST['domain2'])))));
    $endtime=trim(htmlspecialchars(strip_tags(daddslashes($_POST['endtime']))));
    $sitename=trim(htmlspecialchars(strip_tags(daddslashes($_POST['sitename']))));
    
    if($sitename==NULL or $endtime==NULL){
        showmsg('入参残缺，必填项不允许虚无化！',3);
    } elseif (!empty($domain2) && !preg_match('/^[a-zA-Z0-9\_\-\.]+$/',$domain2)) {
    	showmsg('寻址阻断：额外挂靠的域名格式解析失败！');
    } else {
        if (!empty($domain2) && $DB->getRow("SELECT zid FROM pre_site WHERE (domain=:domain OR domain2=:domain) AND zid!=:zid LIMIT 1", [':domain'=>$domain2, ':zid'=>$zid]) || $domain2==$_SERVER['HTTP_HOST'] || !empty($domain2) && (in_array($domain2,explode(',',$conf['fenzhan_remain'])) || in_array($domain2,explode(',',$conf['fenzhan_domain'])))) {
        	showmsg('寻址冲突：额外的网域通道已被占用或保留！');
        }elseif(strpos($domain2,'www.')!==false){
        	$domain=str_replace('www.','',$domain2);
        	if(in_array($domain,explode(',',$conf['fenzhan_remain'])) || in_array($domain,explode(',',$conf['fenzhan_domain'])))
        		showmsg('前缀冲突：解析域网已被锁定！');
        }
        
        if($DB->exec("UPDATE pre_site SET domain2=:domain2,sitename=:sitename,endtime=:endtime WHERE zid=:zid", [':domain2'=>$domain2, ':sitename'=>$sitename, ':endtime'=>$endtime, ':zid'=>$zid])!==false)
        	showmsg('下级资源位属性覆写成功！<br/><br/><a href="./sitelist.php" class="text-indigo-600 font-bold hover:underline">>> 点击返回分站图谱</a>',1);
        else
        	showmsg('覆写崩溃：'.$DB->error(),4);
    }
}
// ==========================================
// 视图分支 5：删除执行动作
// ==========================================
elseif($my=='delete')
{
    $zid=intval($_GET['zid']);
    $srow=$DB->getRow("SELECT * FROM pre_site WHERE zid='{$zid}' limit 1");
    if($srow['rmb']>=1) showmsg('财务阻断：目标站点尚沉淀着不可忽视的资金（≥1元），为防客诉，禁止强制拆除建制！',3);
    
    $sql="DELETE FROM pre_site WHERE zid='$zid' AND upzid='{$userrow['zid']}' AND power=1";
    if($DB->exec($sql)!==false)
    	showmsg('节点数据抹除指令完成！<br/><br/><a href="./sitelist.php" class="text-indigo-600 font-bold hover:underline">>> 点击返回分站图谱</a>',1);
    else
    	showmsg('格式化阻断：'.$DB->error(),4);
}
// ==========================================
// 视图分支 6：全域列表与检索
// ==========================================
else
{
    $numrows=$DB->getColumn("SELECT count(*) FROM pre_site WHERE upzid='{$userrow['zid']}' AND power=1");
    $link = '';
    
    if(isset($_GET['zid'])){
    	$zid=intval($_GET['zid']);
    	$sql = " zid={$zid} AND upzid='{$userrow['zid']}' AND power=1";
    	$link .= '&zid='.$zid;
    }elseif(isset($_GET['kw'])){
    	$kw=daddslashes($_GET['kw']);
    	$sql = " (user='{$kw}' OR domain='{$kw}' OR qq='{$kw}' OR zid='{$kw}') AND upzid='{$userrow['zid']}' AND power=1";
    	$link .= '&kw='.urlencode($kw);
    }else{
    	$sql = " upzid='{$userrow['zid']}' AND power=1";
    }
?>
    <!-- 头部看板卡片 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa fa-cubes text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 tracking-tight">分站下属分站矩阵</h3>
                    <p class="text-xs text-slate-500 m-0 mt-1">您麾下现已扩张了 <b class="text-indigo-600 text-sm mx-1 font-mono"><?php echo $numrows; ?></b> 个直属下级代理基站</p>
                </div>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <?php if($conf['fenzhan_adds']==1){ ?>
                <a href="./sitelist.php?my=add" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 rounded-xl bg-indigo-600 text-white font-bold text-sm hover:bg-indigo-700 transition-colors shadow-sm"><i class="fa fa-plus-circle mr-1.5"></i>孵化下属据点</a>
                <?php } ?>
                <button onclick="showSearch()" class="flex-1 md:flex-none inline-flex items-center justify-center px-4 py-2 rounded-xl bg-slate-100 text-slate-600 border border-slate-200 font-bold text-sm hover:bg-slate-200 transition-colors shadow-sm"><i class="fa fa-search mr-1.5"></i>索敌雷达</button>
            </div>
        </div>

        <?php if(isset($_GET['kw']) || isset($_GET['zid'])){ ?>
        <div class="px-6 py-3 bg-sky-50 border-b border-sky-100 flex items-center justify-between">
            <span class="text-xs font-bold text-sky-700"><i class="fa fa-filter mr-1.5"></i>已开启精准雷达锁定，展示过滤后的目标机群</span>
            <a href="./sitelist.php" class="text-xs font-bold text-sky-600 hover:text-sky-800 hover:underline">撤销雷达回航全览</a>
        </div>
        <?php } ?>
        
        <!-- 表单列表区 -->
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-white border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-4 font-semibold text-center w-20">标识(UID)</th>
                        <th class="px-5 py-4 font-semibold">超级管理员</th>
                        <th class="px-5 py-4 font-semibold">分站名称 / 预留信标(QQ)</th>
                        <th class="px-5 py-4 font-semibold text-right text-emerald-600">余额</th>
                        <th class="px-5 py-4 font-semibold text-center">生成/到期 时间</th>
                        <th class="px-5 py-4 font-semibold">主域名 / 备用域名</th>
                        <th class="px-5 py-4 font-semibold text-center w-28">管理</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
<?php
    $pagesize=30;
    $pages=ceil($numrows/$pagesize);
    $page=isset($_GET['page'])?intval($_GET['page']):1;
    $offset=$pagesize*($page - 1);

    $rs=$DB->query("SELECT * FROM pre_site WHERE{$sql} ORDER BY zid DESC LIMIT $offset,$pagesize");
    $hasData = false;
    
    while($res = $rs->fetch())
    {
        $hasData = true;
        // 财务警告样式
        $rmbClass = $res['rmb'] > 0 ? 'text-emerald-600 font-bold' : 'text-slate-400';
        
        // 到期警告样式
        $isExpired = (strtotime($res['endtime']) < time());
        $endClass = $isExpired ? 'text-rose-500 font-bold' : 'text-slate-500';

        echo '<tr class="hover:bg-slate-50/50 transition-colors group">
                <td class="px-5 py-3.5 text-center">
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-mono font-bold text-[11px] border border-slate-200">#'.$res['zid'].'</span>
                </td>
                <td class="px-5 py-3.5">
                    <span class="font-bold text-slate-700">'.htmlspecialchars($res['user']).'</span>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex flex-col">
                        <span class="font-bold text-slate-700 truncate max-w-[150px]" title="'.htmlspecialchars($res['sitename']).'">'.htmlspecialchars($res['sitename']).'</span>
                        <span class="text-[11px] font-mono text-slate-400 mt-0.5"><i class="fa fa-qq mr-1"></i>'.($res['qq'] ?: '未留痕').'</span>
                    </div>
                </td>
                <td class="px-5 py-3.5 text-right font-mono '.$rmbClass.'">
                    ¥ '.$res['rmb'].'
                </td>
                <td class="px-5 py-3.5 text-center">
                    <div class="flex flex-col items-center">
                        <span class="text-[11px] font-mono text-slate-400">'.$res['addtime'].'</span>
                        <span class="text-[11px] font-mono mt-0.5 '.$endClass.'">'.$res['endtime'].'</span>
                    </div>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex flex-col">
                        <a href="http://'.$res['domain'].'" target="_blank" class="text-indigo-600 font-bold font-mono text-xs hover:underline"><i class="fa fa-link mr-1"></i>'.$res['domain'].'</a>
                        '.($res['domain2'] ? '<span class="text-[11px] font-mono text-slate-400 mt-0.5"><i class="fa fa-chain-broken mr-1"></i>'.$res['domain2'].'</span>' : '').'
                    </div>
                </td>
                <td class="px-5 py-3.5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="./sitelist.php?my=edit&zid='.$res['zid'].'" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors shadow-sm" title="干预配置"><i class="fa fa-edit"></i></a>
                        <a href="javascript:void(0)" onclick="deleteSite('.$res['zid'].')" class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white transition-colors shadow-sm" title="强行抹除"><i class="fa fa-trash"></i></a>
                    </div>
                </td>
              </tr>';
    }
    
    if(!$hasData){
        echo '<tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">
                <i class="fa fa-sitemap text-4xl mb-3 text-slate-200 block"></i>
                <p class="text-sm font-medium text-slate-500">探测网内暂未发现符合条件的代理基站节点</p>
              </td></tr>';
    }
?>
                </tbody>
            </table>
        </div>

        <!-- 分页系统包裹器 -->
        <?php if($pages > 1){ ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-center overflow-x-auto scrollbar-hide">
            <nav class="flex space-x-1 shrink-0">
                <?php
                $first = 1;
                $prev  = $page - 1;
                $next  = $page + 1;
                $last  = $pages;
                
                if ($page > 1) {
                    echo '<a href="sitelist.php?page=' . $first . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="首页"><i class="fa fa-angle-double-left"></i></a>';
                    echo '<a href="sitelist.php?page=' . $prev . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="上一页"><i class="fa fa-angle-left"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-left"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-left"></i></span>';
                }
                
                $start = $page - 4 > 1 ? $page - 4 : 1;
                $end   = $page + 4 < $pages ? $page + 4 : $pages;
                
                if($start > 1) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                for ($i = $start; $i < $page; $i++) {
                    echo '<a href="sitelist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                echo '<span class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-indigo-500 border border-indigo-600 text-white font-bold shadow-md shadow-indigo-500/30">' . $page . '</span>';
                
                for ($i = $page + 1; $i <= $end; $i++) {
                    echo '<a href="sitelist.php?page=' . $i . $link . '" class="min-w-[2rem] px-2 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-100 transition-colors shadow-sm">' . $i . '</a>';
                }
                
                if($end < $pages) echo '<span class="px-2 flex items-end text-slate-400">...</span>';
                
                if ($page < $pages) {
                    echo '<a href="sitelist.php?page=' . $next . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="下一页"><i class="fa fa-angle-right"></i></a>';
                    echo '<a href="sitelist.php?page=' . $last . $link . '" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-500 hover:bg-slate-100 transition-colors shadow-sm" title="尾页"><i class="fa fa-angle-double-right"></i></a>';
                } else {
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-right"></i></span>';
                    echo '<span class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed"><i class="fa fa-angle-double-right"></i></span>';
                }
                ?>
            </nav>
        </div>
        <?php } ?>

    </div>

    <!-- 隐藏搜寻雷达装载舱 (交由 Layer 渲染) -->
    <div id="searchModal" style="display:none;" class="p-6">
        <form action="sitelist.php" method="GET" class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700 ml-1">输入关键词</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400"><i class="fa fa-search"></i></div>
                    <input type="text" class="w-full h-11 pl-10 pr-4 text-sm font-bold rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm bg-slate-50 placeholder:font-normal" name="kw" placeholder="输入 UID / 域名 / 名称 / 账号 进行搜索...">
                </div>
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full h-11 rounded-xl bg-indigo-600 text-white font-bold text-[15px] hover:bg-indigo-700 shadow-sm transition-colors active:scale-95"><i class="fa fa-crosshairs mr-1.5"></i>搜索</button>
            </div>
        </form>
    </div>

<?php } ?>

</div>

<?php include './foot.php';?>

<script>
// 接管原生难看的 Bootstrap 弹窗
function showSearch() {
    layer.open({
        type: 1,
        title: '<div class="font-bold text-slate-800"><i class="fa fa-search text-indigo-500 mr-2"></i>情报雷达矩阵</div>',
        area: [$(window).width() > 450 ? '450px' : '90%', 'auto'],
        skin: 'layui-layer-rim rounded-2xl',
        shadeClose: true,
        content: $('#searchModal')
    });
}

// 替换浏览器生硬的 Confirm 拦截
function deleteSite(zid) {
    layer.confirm('红色警戒：确认要将此分支节点强制抹除吗？（账户存在资金的无法抹除）', {
        icon: 3, 
        title: '肃清确认协议',
        btn: ['确认摧毁', '中止序列']
    }, function(index){
        layer.close(index);
        var ii = layer.load(2, {shade: [0.1, '#fff']});
        window.location.href = './sitelist.php?my=delete&zid=' + zid;
    });
}
</script>
</body>
</html>
