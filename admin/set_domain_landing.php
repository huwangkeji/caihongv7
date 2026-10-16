<?php
include("../includes/common.php");
$title='域名落地页配置';
$mod='domain_landing';  // 用于菜单选中
include './head.php';
// 功能权限校验（超级管理员不受限制，仅限制无此权限的员工账号）
adminpermission('set', 1);
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

// 自动创建数据表
$DB->exec("CREATE TABLE IF NOT EXISTS `pre_domain_landing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_domain` varchar(255) NOT NULL,
  `new_domain` varchar(255) NOT NULL,
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `old_domain` (`old_domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 添加或修改域名
if(isset($_POST['submit'])) {
    $old_domain = trim(strtolower($_POST['old_domain']));
    $new_domain = trim(strtolower($_POST['new_domain']));
    
    if(empty($old_domain) || empty($new_domain)) {
        showmsg('域名不能为空',3);
    }
    
    // 验证新旧域名不能相同
    if($old_domain === $new_domain) {
        showmsg('新旧域名不能相同',3);
    }
    
    // 去除http(s)://前缀
    $old_domain = preg_replace('/^https?:\/\//', '', $old_domain);
    $new_domain = preg_replace('/^https?:\/\//', '', $new_domain);
    
    // 再次验证去除前缀后的域名不能相同
    if($old_domain === $new_domain) {
        showmsg('新旧域名不能相同',3);
    }
    
    $sds = $DB->exec("INSERT INTO `pre_domain_landing` (`old_domain`, `new_domain`, `addtime`) VALUES (:old_domain, :new_domain, NOW()) ON DUPLICATE KEY UPDATE `new_domain`=:new_domain2", [
        ':old_domain' => $old_domain,
        ':new_domain' => $new_domain,
        ':new_domain2' => $new_domain
    ]);
    
    showmsg('设置保存成功！',1);
}

// 删除域名
if(isset($_GET['act']) && $_GET['act']=='del'){
    $id=intval($_GET['id']);
    $sql="DELETE FROM pre_domain_landing WHERE id='$id' limit 1";
    if($DB->exec($sql)){
        showmsg('删除成功！',1);
    }else{
        showmsg('删除失败！',4);
    }
}

// 获取域名列表
$list = $DB->getAll("SELECT * FROM pre_domain_landing ORDER BY id DESC");
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">
    
    <!-- 规则添加卡片 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <i class="fa fa-exchange text-brand-500 mr-2 text-lg"></i>
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0">域名落地页跳转配置</h3>
        </div>
        
        <div class="p-6 sm:p-8 space-y-6">
            <!-- 提示说明区域 -->
            <div class="p-5 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm flex items-start shadow-sm">
                <i class="fa fa-info-circle mt-0.5 mr-2 text-lg shrink-0"></i>
                <div class="leading-relaxed">
                    <strong class="font-bold text-sky-800 mb-2 block">高级通配符使用说明：</strong>
                    <ul class="space-y-1.5 list-disc list-inside">
                        <li>支持使用 <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-bold">*</code> 作为通配符，例如：<code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-mono">*.example.com</code> 可以匹配所有 example.com 的子域名。</li>
                        <li>新域名也可以使用 <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-bold">*</code>，它会自动替换为原域名中 <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-bold">*</code> 匹配的动态部分。</li>
                        <li>例如配置：<code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-mono">*.old.com</code> -> <code class="px-1.5 py-0.5 bg-white text-sky-600 rounded font-mono">*.new.com</code>，此时访问 <span class="font-bold">abc</span>.old.com 将会自动跳转到 <span class="font-bold">abc</span>.new.com。</li>
                        <li>规则优先级按添加时间<span class="font-bold text-sky-800">倒序排列</span>，即最新添加的规则拥有最高匹配优先级。</li>
                    </ul>
                </div>
            </div>

            <!-- 配置表单 -->
            <form action="./set_domain_landing.php" method="post" role="form" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">旧域名 (待跳转来源)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa fa-globe"></i>
                            </div>
                            <input type="text" name="old_domain" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="例如: *.old.com" required>
                        </div>
                    </div>
                    
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700">新域名 (目标落地页)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-brand-400">
                                <i class="fa fa-link"></i>
                            </div>
                            <input type="text" name="new_domain" class="w-full h-11 pl-10 pr-4 text-sm font-mono rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" placeholder="例如: *.new.com" required>
                        </div>
                    </div>
                </div>
                
                <div class="pt-2">
                    <button type="submit" name="submit" class="w-full sm:w-auto px-8 h-11 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-plus-circle mr-1.5"></i>添加或更新映射规则
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 规则列表卡片 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800 m-0">已配置的跳转规则列表</h3>
            <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-xs font-medium">共 <?php echo count($list); ?> 条</span>
        </div>
        
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
            <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold text-center w-20">ID</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[250px]">旧域名 (来源)</th>
                        <th class="px-6 py-3.5 font-semibold min-w-[250px] text-brand-600">新域名 (目标落地页)</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-40">添加时间</th>
                        <th class="px-6 py-3.5 font-semibold text-center w-28">操作</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                <?php
                if(!empty($list)) {
                    foreach($list as $res){
                        echo '<tr class="hover:bg-brand-50/40 transition-colors duration-200">
                            <td class="px-6 py-4 text-center font-mono text-slate-400 text-xs">'.$res['id'].'</td>
                            <td class="px-6 py-4 font-bold text-slate-700 font-mono">'.$res['old_domain'].'</td>
                            <td class="px-6 py-4 font-bold text-brand-600 font-mono">
                                <div class="flex items-center"><i class="fa fa-long-arrow-right text-slate-300 mr-2"></i>'.$res['new_domain'].'</div>
                            </td>
                            <td class="px-6 py-4 text-center font-mono text-slate-500 text-xs">'.$res['addtime'].'</td>
                            <td class="px-6 py-4 text-center">
                                <a href="./set_domain_landing.php?act=del&id='.$res['id'].'" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm" onclick="return confirm(\'确定要彻底删除此域名映射记录吗？\');">删除</a>
                            </td>
                        </tr>';
                    }
                } else {
                    echo '<tr><td colspan="5" class="px-6 py-16 text-center text-slate-400">
                            <i class="fa fa-exchange text-5xl mb-3 text-slate-200 block"></i>
                            <p class="text-sm font-medium text-slate-500">当前没有配置任何域名落地页跳转规则</p>
                          </td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
    
</div>

<?php include './foot.php';?>
