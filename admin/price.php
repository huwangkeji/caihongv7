<?php

include "../includes/common.php";
$title = "加价模板";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>
<div class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" id="modal-store" role="dialog" aria-hidden="true">
  <div class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl transform transition-transform duration-300 scale-95 origin-center m-4 overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
      <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 shrink-0">
        <h4 class="text-base font-bold text-slate-800 m-0" id="modal-title">加价模板修改/添加</h4>
        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none close-modal" data-target="#modal-store">
            <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
        </button>
      </div>
      <div class="flex-1 overflow-y-auto p-6 sm:p-8">
        <form id="form-store" class="space-y-5">
            <input type="hidden" id="action"/>
            <input type="hidden" name="prid" id="prid"/>
            
            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 text-sm flex items-start shadow-sm mb-2">
                <i class="fa fa-exclamation-triangle mt-0.5 mr-2 text-lg shrink-0"></i>
                <div class="leading-relaxed">
                    <strong class="font-bold">注意：</strong>设置的加价规则应当严格满足比例：用户加价 &gt;= 普及版加价 &gt;= 专业版加价
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">模板名称</label>
                    <input type="text" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors" name="name" id="name" placeholder="输入模板名称">
                </div>
                
                <div class="space-y-1.5">
                    <label class="text-sm font-semibold text-slate-700">加价方式</label>
                    <select name="kind" id="kind" class="w-full h-11 px-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors" onchange="changeKind()">
                        <option value="0">倍数加价</option>
                        <option value="1">固定金额加价</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700">假设商品成本价 <span class="text-xs text-slate-400 font-normal">(用于下方加价效果展示，无实际意义)</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 text-sm font-semibold">¥</span>
                    <input type="text" class="w-full h-11 pl-8 pr-4 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-slate-50 transition-colors" value="100" id="test_price" onkeyup="changeTest()">
                </div>
            </div>
            
            <div class="border-t border-slate-100 my-2 pt-2"></div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="space-y-1.5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <label class="text-sm font-semibold text-slate-700">专业版加价</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors mb-2" name="p_2" id="p_2" onkeyup="changeTest('p_2')">
                    <div class="text-xs text-slate-500 font-medium">预览价格: <span id="test_p_2" class="text-rose-500 font-bold ml-1 text-sm"></span></div>
                </div>
                
                <div class="space-y-1.5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <label class="text-sm font-semibold text-slate-700">普及版加价</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors mb-2" name="p_1" id="p_1" onkeyup="changeTest('p_1')">
                    <div class="text-xs text-slate-500 font-medium">预览价格: <span id="test_p_1" class="text-rose-500 font-bold ml-1 text-sm"></span></div>
                </div>
                
                <div class="space-y-1.5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <label class="text-sm font-semibold text-slate-700">普通用户加价</label>
                    <input type="text" class="w-full h-10 px-3 text-sm rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white transition-colors mb-2" name="p_0" id="p_0" onkeyup="changeTest('p_0')">
                    <div class="text-xs text-slate-500 font-medium">预览价格: <span id="test_p_0" class="text-rose-500 font-bold ml-1 text-sm"></span></div>
                </div>
            </div>
        </form>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 bg-white flex justify-end gap-3 shrink-0">
        <button type="button" class="px-5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 font-medium hover:bg-slate-200 transition-colors close-modal" data-target="#modal-store">关闭</button>
        <button type="button" class="px-5 py-2 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-colors" id="store" onclick="save()">保存模板设定</button>
      </div>
  </div>
</div>

<div class="w-full max-w-[1200px] mx-auto space-y-6">
<?php 
adminpermission("price", 1);
$numrows = $DB->getColumn("SELECT count(*) from pre_price");
?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-50/50 gap-4">
            <h3 class="text-base sm:text-lg font-bold text-slate-800 m-0 flex items-center">
                <i class="fa fa-line-chart text-brand-500 mr-2 text-lg"></i>加价模板管理
                <span class="ml-3 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium border border-slate-200">共 <?php echo $numrows;?> 个</span>
            </h3>
            
            <a href="javascript:addframe()" class="inline-flex items-center justify-center px-4 h-9 rounded-xl bg-brand-600 text-white font-medium hover:bg-brand-700 shadow-sm transition-all duration-200 text-sm shrink-0">
                <i class="fa fa-plus mr-1.5"></i>新增加价模板
            </a>
        </div>
        
        <form name="form1" id="form1">
            <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-slate-200">
                <table class="w-full text-sm text-left text-slate-600 whitespace-nowrap">
                    <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 font-semibold text-center w-16">
                                <label class="inline-flex items-center cursor-pointer m-0 hover:opacity-80 transition-opacity">
                                    <input type="checkbox" id="chkAll" class="w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer">
                                </label>
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-center w-20">ID</th>
                            <th class="px-6 py-3.5 font-semibold min-w-[200px]">模板名称</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-36">加价类型</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-48 text-sky-600">加价规则 (专|普|通)</th>
                            <th class="px-6 py-3.5 font-semibold text-center w-64">操作</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50" id="table-body">
<?php 
$rs = $DB->query("SELECT * FROM pre_price WHERE 1 order by id desc");
$isEmpty = true;
while ($res = $rs->fetch()) {
    $isEmpty = false;
	echo "<tr class=\"hover:bg-brand-50/40 transition-colors duration-200 cursor-pointer table-row-item\">
            <td class=\"px-6 py-4 text-center\" onclick=\"event.stopPropagation();\">
                <label class=\"inline-flex items-center cursor-pointer m-0\">
                    <input type=\"checkbox\" name=\"checkbox[]\" class=\"list-checkbox w-4 h-4 text-brand-600 bg-white border-slate-300 rounded focus:ring-brand-500 cursor-pointer\" value=\"" . $res["id"] . "\">
                </label>
            </td>
            <td class=\"px-6 py-4 text-center font-bold text-slate-700\">" . $res["id"] . "</td>
            <td class=\"px-6 py-4 font-bold text-slate-800\">" . $res["name"] . "</td>
            <td class=\"px-6 py-4 text-center\">" . display_kind($res["kind"]) . "</td>
            <td class=\"px-6 py-4 text-center font-mono font-bold text-slate-500\">" . $res["p_2"] . " <span class=\"text-slate-300\">|</span> " . $res["p_1"] . " <span class=\"text-slate-300\">|</span> " . $res["p_0"] . "</td>
            <td class=\"px-6 py-4 text-center whitespace-nowrap\" onclick=\"event.stopPropagation();\">
                <div class=\"inline-flex items-center gap-1.5\">
                    <a href=\"./shoplist.php?prid=" . $res["id"] . "\" target=\"_blank\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white font-medium text-xs transition-colors shadow-sm\"><i class=\"fa fa-cubes mr-1\"></i>关联商品</a>
                    <a href=\"javascript:editframe(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-sky-50 text-sky-600 hover:bg-sky-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">编辑</a>
                    <a href=\"javascript:delItem(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-500 hover:text-white font-medium text-xs transition-colors shadow-sm\">删除</a>
                    <a href=\"javascript:change(" . $res["id"] . ")\" class=\"inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-500 hover:text-white font-medium text-xs transition-colors shadow-sm\"><i class=\"fa fa-exchange mr-1\"></i>批量应用</a>
                </div>
            </td>
          </tr>";
}

if($isEmpty) {
    echo '<tr><td colspan="6" class="px-6 py-16 text-center text-slate-400">
            <i class="fa fa-inbox text-5xl mb-3 text-slate-200 block"></i>
            <p class="text-sm font-medium text-slate-500">当前没有配置任何加价模板</p>
          </td></tr>';
}
?>                  
                    </tbody>
                </table>
            </div>
            
            <?php if(!$isEmpty): ?>
            <!-- 批量操作控制区 -->
            <div class="px-6 py-4 bg-slate-50/50 border-t border-b border-slate-100 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <i class="fa fa-level-up fa-rotate-90 text-slate-300 ml-2"></i>
                    <select name="aid" id="batch-action" class="h-10 px-3 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-sm bg-white cursor-pointer transition-colors w-40">
                        <option selected value="0">请选择批量操作</option>
                        <option value="1" class="text-rose-600 font-bold">➡️ 强力删除选中项</option>
                    </select>
                </div>
                <button type="button" onclick="executeBatchOperation()" class="inline-flex items-center justify-center h-10 px-6 rounded-xl bg-brand-600 text-white font-medium text-sm hover:bg-brand-700 shadow-sm transition-all duration-200">
                    确认执行操作
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php 
// 获取所有活跃分类，区分一级和二级
$rs = $DB->query("SELECT * FROM pre_class WHERE active=1 order by sort asc");
$classes = [];
$subClasses = [];

// 先将分类按层级分组
while ($res = $rs->fetch()) {
    if ($res["pid"] == 0) {
        // 一级分类
        $classes[$res["cid"]] = $res;
    } else {
        // 二级分类，按父分类ID分组
        $subClasses[$res["pid"]][] = $res;
    }
}

// 生成带有层级关系的分类下拉选项
$select = "<option value=\"0\" class=\"font-medium text-slate-700 py-1\">未分类商品</option>";
foreach ($classes as $cid => $class) {
    // 一级分类
    $select .= "<option value=\"$cid\" class=\"font-bold text-sky-700 py-1 bg-slate-50\">" . $class["name"] . "</option>";
    // 二级分类（如果有）
    if (isset($subClasses[$cid])) {
        foreach ($subClasses[$cid] as $subClass) {
            $select .= "<option value=\"{$subClass['cid']}\" class=\"pl-5 text-slate-600 py-1\">┗━ " . $subClass["name"] . "</option>";
        }
    }
}
?>
<!-- 供 JS 内部 Layer 调用的隐藏 DOM 结构 -->
<div id="class-select" style="display:none;">
    <div class="p-4 space-y-3">
        <label class="text-sm font-semibold text-slate-700 block">请选择要应用该加价模板的分类：</label>
        <select name="cids" multiple="multiple" class="w-full h-40 text-sm rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 shadow-inner bg-white transition-colors overflow-y-auto outline-none p-2">
            <?php echo $select;?>
        </select>
        <p class="text-xs text-brand-600 font-medium leading-relaxed bg-brand-50 p-2 rounded-lg"><i class="fa fa-info-circle mr-1"></i>快捷操作提示：Windows 按住 Ctrl 键 / Mac 按住 Command 键可实现多选，按住 Shift 键可选择连续的分类范围。</p>
    </div>
</div>

<script src="<?php echo $cdnpublic;?>layer/2.3/layer.js"></script>
<script src="assets/js/price.js?ver=<?php echo VERSION;?>"></script>

<script>
//HuWang V7.1 修复点击新增加价模版无反应的bug
function addframe() {
    document.getElementById('modal-title').innerText = '新增加价模板';
    document.getElementById('action').value = 'add';
    document.getElementById('prid').value = '';
    document.getElementById('name').value = '';
    document.getElementById('kind').value = '0';
    document.getElementById('p_0').value = '';
    document.getElementById('p_1').value = '';
    document.getElementById('p_2').value = '';
    document.getElementById('test_price').value = '100';
    
    if(document.getElementById('test_p_0')) document.getElementById('test_p_0').innerText = '';
    if(document.getElementById('test_p_1')) document.getElementById('test_p_1').innerText = '';
    if(document.getElementById('test_p_2')) document.getElementById('test_p_2').innerText = '';

    const modal = document.getElementById('modal-store');
    if (modal) {
        modal.classList.remove('hidden'); 
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
    }
}

// ----------------------------------------------------
// 界面交互增强与原生复选框逻辑修复
// ----------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    
    // 全选按钮逻辑
    const chkAllBtn = document.getElementById('chkAll');
    if(chkAllBtn) {
        chkAllBtn.addEventListener('change', function() {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.list-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = isChecked;
                updateRowStyle(cb);
            });
        });
    }

    // 绑定行点击与复选框变更联动
    document.querySelectorAll('.table-row-item').forEach(row => {
        row.addEventListener('click', function(e) {
            // 如果点击的是内部按钮或链接，不触发勾选
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON' || e.target.closest('a') || e.target.closest('button')) {
                return;
            }
            
            const checkbox = this.querySelector('.list-checkbox');
            if (checkbox && e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
            }
            if(checkbox) {
                updateRowStyle(checkbox);
                syncCheckAllStatus();
            }
        });
    });

    document.querySelectorAll('.list-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            updateRowStyle(this);
            syncCheckAllStatus();
        });
    });

    function updateRowStyle(checkbox) {
        const row = checkbox.closest('tr');
        if(checkbox.checked) {
            row.classList.add('bg-brand-50/60');
        } else {
            row.classList.remove('bg-brand-50/60');
        }
    }

    function syncCheckAllStatus() {
        if(!chkAllBtn) return;
        const allCheckboxes = document.querySelectorAll('.list-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.list-checkbox:checked');
        chkAllBtn.checked = (allCheckboxes.length > 0 && allCheckboxes.length === checkedCheckboxes.length);
    }
    
    // 原生 Modal 关闭绑定
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const modal = document.querySelector(targetId);
            if(modal) {
                modal.classList.add('opacity-0');
                modal.querySelector('.transform').classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
        });
    });
});

// 重构原本暴露在全局的 batchOperation (现在由按钮直接调用)
function executeBatchOperation() {
    const action = document.getElementById('batch-action').value;
    if(action === '0' || action === '批量操作') {
        layer.msg('请先选择要执行的操作项', {icon:0});
        return;
    }
    
    const checkedBoxes = document.querySelectorAll('.list-checkbox:checked');
    if(checkedBoxes.length === 0) {
        layer.msg('请至少在列表中勾选一个模板', {icon:0});
        return;
    }
    
    if(action === '1') {
        layer.confirm('确定要删除选中的 ' + checkedBoxes.length + ' 个加价模板吗？此操作将不可逆转。', {
            icon: 3, 
            title: '批量删除警告',
            btn: ['确定执行', '取消']
        }, function(index) {
            layer.close(index);
            // 兼容原有老项目的 js 行为，如果原 JS 里有 batchOperation 全局函数则调用
            if(typeof window.batchOperation === 'function') {
                window.batchOperation();
            } else {
                // 如果没有，模拟传统表单提交
                document.getElementById('form1').submit();
            }
        });
    }
}
</script>

</body>
</html>
<?php 
function display_kind($zt)
{
	if ($zt == 1) {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-600 border border-emerald-100\">固定金额加价</span>";
	} else {
		return "<span class=\"inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-50 text-sky-600 border border-sky-100\">倍数比例加价</span>";
	}
}
?>
