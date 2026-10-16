<?php
//请勿删除版权信息，否则出现问题将不再保持售后修复！
include "../includes/common.php";
$title = "商品介绍批量替换";
include "./head.php";

if($islogin != 1){
    exit("<script language='javascript'>window.location.href='./login.php';</script>");
}

//权限验证
adminpermission("shop", 1);

$my = isset($_GET["my"]) ? $_GET["my"] : "index";

//核心替换功能
function performReplace($search, $replace, $preview = true){
    global $DB;
    
    if(empty($search)){
        return ["status"=>0, "msg"=>"查找内容不能为空"];
    }
    
    //查找匹配记录
    $rows = $DB->getAll("SELECT tid, name, `desc` FROM pre_tools WHERE `desc` LIKE :pattern", 
        [':pattern' => "%{$search}%"]
    );
    
    if(!$rows){
        return ["status"=>0, "msg"=>"未找到匹配的商品介绍"];
    }
    
    $results = [];
    $affected = 0;
    
    foreach($rows as $row){
        $newDesc = str_replace($search, $replace, $row['desc']);
        $isChanged = ($newDesc !== $row['desc']);
        
        $results[] = [
            "tid" => $row['tid'],
            "name" => $row['name'],
            "old_desc" => $row['desc'],
            "new_desc" => $newDesc,
            "changed" => $isChanged
        ];
        
        if($isChanged && !$preview){
            //执行更新
            $DB->exec("UPDATE pre_tools SET `desc`=:newDesc, `uptime`=NOW() WHERE tid=:tid",
                [':newDesc'=>$newDesc, ':tid'=>$row['tid']]
            );
            $affected++;
        }
    }
    
    return [
        "status" => 1,
        "msg" => $preview ? "找到 ".count($rows)." 条匹配记录" : "成功替换 {$affected} 条记录",
        "data" => $results,
        "affected" => $affected
    ];
}

//显示操作结果
function showResult($result){
    if($result['status']){
        showmsg($result['msg'] . "<br/><br/><a href='./shopnoo.php'>>>返回继续操作</a><br/><a href='./shoplist.php'>>>返回商品列表</a>", 1);
    }else{
        showmsg($result['msg'], 3);
    }
}
?>

<div class="w-full max-w-[1200px] mx-auto space-y-6 pb-10">

<?php
if($my == "index"){
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-exchange text-brand-500 mr-2 text-lg"></i>商品介绍批量替换
            </h3>
            <span class="text-xs font-medium text-brand-600 bg-brand-50 border border-brand-100 px-2.5 py-1 rounded-md shadow-sm">
                <i class="fa fa-code-fork mr-1"></i>插件贡献者：小杰云
            </span>
        </div>
        
        <div class="p-6 sm:p-8">
            <form action="./shopnoo.php?my=preview" method="POST" class="space-y-6">
                
                <div class="space-y-1.5">
                    <label for="search" class="text-sm font-semibold text-slate-700 flex items-center">
                        查找内容（替换前）<span class="text-rose-500 ml-1">*</span>
                    </label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[120px]" id="search" name="search" rows="5" placeholder="请输入要查找的文本内容，支持任意字符及 HTML 标签"><?php echo htmlspecialchars($_POST['search'] ?? '');?></textarea>
                </div>
                
                <div class="space-y-1.5">
                    <label for="replace" class="text-sm font-semibold text-slate-700">
                        替换为（替换后）
                    </label>
                    <textarea class="w-full p-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors min-h-[120px]" id="replace" name="replace" rows="5" placeholder="请输入替换后的新内容。若留空，则相当于批量删除上述查找的内容"><?php echo htmlspecialchars($_POST['replace'] ?? '');?></textarea>
                </div>
                
                <div class="p-5 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm shadow-sm space-y-2">
                    <strong class="font-bold text-sky-800 flex items-center text-base"><i class="fa fa-info-circle mr-1.5"></i>温馨提示：</strong>
                    <ul class="list-disc list-inside ml-1 space-y-1.5 marker:text-sky-400">
                        <li>请先使用下方的 <strong>"预览替换结果"</strong> 查看匹配列表，确认无误后再执行最终操作。</li>
                        <li>完全支持替换 HTML 代码和普通文本内容，区分大小写和空格。</li>
                        <li><span class="text-rose-500 font-bold">该操作将直接覆写数据库内容，不可逆且无法撤销，请务必谨慎操作！</span></li>
                    </ul>
                </div>
                
                <div class="pt-4 border-t border-slate-50">
                    <button type="submit" class="w-full flex items-center justify-center h-12 rounded-xl bg-brand-600 text-white font-bold text-[15px] hover:bg-brand-700 shadow-sm transition-all duration-200 active:scale-[0.98]">
                        <i class="fa fa-search mr-2"></i> 预览批量替换结果
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php
    
}elseif($my == "preview"){
    $search = $_POST['search'] ?? '';
    $replace = $_POST['replace'] ?? '';
    
    if(empty($search)){
        showmsg('查找内容不能为空！', 3);
        exit;
    }
    
    $result = performReplace($search, $replace, true);
    
    if(!$result['status']){
        showmsg($result['msg'], 3);
        exit;
    }
    
    $changedItems = array_filter($result['data'], function($item){
        return $item['changed'];
    });
?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-eye text-brand-500 mr-2 text-lg"></i>替换预览确认
            </h3>
        </div>
        
        <div class="p-6">
            <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 text-sm shadow-sm mb-6 flex items-center">
                <i class="fa fa-check-circle mr-2 text-lg text-sky-500"></i>
                <span>扫描完成：共匹配到 <strong><?php echo count($result['data']); ?></strong> 条记录，其中 <strong class="text-rose-500"><?php echo count($changedItems); ?></strong> 条内容发生实质性改变，将被执行修改。</span>
            </div>
            
            <?php if(!empty($changedItems)): ?>
                <form action="./shopnoo.php?my=execute" method="POST" onsubmit="return confirmReplace()">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search);?>" />
                    <input type="hidden" name="replace" value="<?php echo htmlspecialchars($replace);?>" />
                    
                    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200 rounded-xl border border-slate-100 shadow-sm mb-6">
                        <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                            <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5 font-semibold text-center w-20">商品ID</th>
                                    <th class="px-6 py-3.5 font-semibold min-w-[150px] w-48">商品名称</th>
                                    <th class="px-6 py-3.5 font-semibold min-w-[250px]">替换前内容 (截断展示)</th>
                                    <th class="px-6 py-3.5 font-semibold min-w-[250px] text-brand-600">替换后预览 (截断展示)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach($changedItems as $item): ?>
                                    <tr class="hover:bg-brand-50/40 transition-colors duration-200">
                                        <td class="px-6 py-4 text-center font-bold text-slate-700 font-mono"><?php echo $item['tid'];?></td>
                                        <td class="px-6 py-4 font-bold text-slate-800 truncate max-w-[200px]" title="<?php echo htmlspecialchars($item['name']);?>"><?php echo htmlspecialchars($item['name']);?></td>
                                        <td class="px-6 py-4">
                                            <div class="max-h-[80px] overflow-y-auto whitespace-pre-wrap break-all text-xs bg-slate-50 p-3 rounded-lg border border-slate-100 text-slate-500 scrollbar-thin"><?php echo htmlspecialchars($item['old_desc']);?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="max-h-[80px] overflow-y-auto whitespace-pre-wrap break-all text-xs bg-brand-50 p-3 rounded-lg border border-brand-100 text-brand-700 scrollbar-thin"><?php echo htmlspecialchars($item['new_desc']);?></div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <button type="submit" class="flex-1 flex items-center justify-center h-12 rounded-xl bg-rose-500 text-white font-bold text-[15px] hover:bg-rose-600 shadow-sm transition-all duration-200 active:scale-[0.98]">
                            <i class="fa fa-exclamation-triangle mr-2"></i> 确认无误，立即执行批量替换
                        </button>
                        
                        <a href="./shopnoo.php" class="flex-1 sm:flex-none sm:w-48 flex items-center justify-center h-12 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm">
                            <i class="fa fa-reply mr-2"></i> 放弃并返回重新编辑
                        </a>
                    </div>
                </form>
                
                <script>
                function confirmReplace(){
                    return confirm("⚠️ 危险操作警告：\n确定要执行批量替换操作吗？此操作将直接大范围修改数据库中的数据，且不可撤销！");
                }
                </script>
            <?php else: ?>
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm shadow-sm mb-6 flex items-center">
                    <i class="fa fa-exclamation-triangle mr-2 text-lg"></i>
                    没有找到需要替换的内容，或者替换前后的内容无实质变化，请返回检查您的查找替换条件。
                </div>
                <a href="./shopnoo.php" class="inline-flex items-center justify-center px-6 h-11 rounded-xl bg-white border border-slate-200 text-slate-600 font-medium hover:bg-slate-50 transition-colors shadow-sm">
                    <i class="fa fa-reply mr-2"></i> 返回重新编辑
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php
    
}elseif($my == "execute"){
    $search = $_POST['search'] ?? '';
    $replace = $_POST['replace'] ?? '';
    
    if(empty($search)){
        showmsg('查找内容不能为空！', 3);
        exit;
    }
    
    //执行替换
    $result = performReplace($search, $replace, false);
    
    //记录操作日志
    if($result['affected'] > 0){
        $content = "批量替换商品介绍：{$search} → {$replace}，影响 {$result['affected']} 条记录";
        $DB->exec("INSERT INTO `pre_toollogs` (`content`, `date`, `addtime`, `active`) VALUES (:content, CURDATE(), NOW(), 1)",
            [':content'=>$content]
        );
    }
    
    showResult($result);
}
?>

</div>

<?php
include "foot.php";
?>
