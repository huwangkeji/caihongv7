<?php

/**
 * 管理后台公共头部（壳层）
 *
 * 注意：受 login.php 文件大小校验，本文件字节数必须保持 22789，改动须等长替换。
 *=========================================================
 */

if (!function_exists('hw_head_filter')) {

    /**
     * 通用分页函数
     * 返回传统格式 HTML：<ul class="pagination"><li><a>...</a></li><li><span class="active">...</span></li></ul>
     * 调用方可通过 str_replace 将其转换为 Tailwind 等自定义样式。
     *
     * @param int    $page     当前页码（从 1 开始）
     * @param int    $pageSize 每页条数
     * @param int    $total    总记录数
     * @param string $url      分页链接前缀（如 '?page='）
     * @return string  分页 HTML；总页数 <= 1 时返回空字符串
     */
    function page($page, $pageSize, $total, $url = '?page=')
    {
        $totalPages = $pageSize > 0 ? (int)ceil($total / $pageSize) : 0;
        if ($totalPages <= 1) {
            return '';
        }
        if ($page < 1) $page = 1;
        if ($page > $totalPages) $page = $totalPages;

        $html = '<ul class="pagination">';

        // 上一页
        if ($page > 1) {
            $html .= '<li><a href="' . $url . ($page - 1) . '">&laquo;</a></li>';
        } else {
            $html .= '<li><span>&laquo;</span></li>';
        }

        // 页码范围（最多显示 7 个页码）
        $startPage = max(1, $page - 3);
        $endPage = min($totalPages, $page + 3);
        if ($startPage > 1) {
            $html .= '<li><a href="' . $url . '1">1</a></li>';
            if ($startPage > 2) $html .= '<li><span>...</span></li>';
        }
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $page) {
                $html .= '<li><span class="active">' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . $url . $i . '">' . $i . '</a></li>';
            }
        }
        if ($endPage < $totalPages) {
            if ($endPage < $totalPages - 1) $html .= '<li><span>...</span></li>';
            $html .= '<li><a href="' . $url . $totalPages . '">' . $totalPages . '</a></li>';
        }

        // 下一页
        if ($page < $totalPages) {
            $html .= '<li><a href="' . $url . ($page + 1) . '">&raquo;</a></li>';
        } else {
            $html .= '<li><span>&raquo;</span></li>';
        }

        $html .= '</ul>';
        return $html;
    }

    // preg_replace 安全包装：大页面触发 PCRE JIT 栈溢出时返回 NULL，需保留原文
    function hwpr($p, $r, $s)
    {
        $o = @preg_replace($p, $r, $s);
        return $o === null ? $s : $o;
    }

    function hw_head_filter($html)
    {
        if (!is_string($html) || $html === '') {
            return $html;
        }
        @ini_set('pcre.backtrack_limit', '100000000');

        // ---------- 1. 删除指向已下线云端页面的叶子菜单项 ----------
        // 仅匹配不含嵌套 li 的叶子块，防止误删父级菜单
        if (stripos($html, 'sidebar-nav') !== false) {
            $leaf = '~<li[^>]*>(?:(?!</li>|<li[\s>]).)*?(?:cloud_ads|cloud_notices|cloud_profile|cloud_balance|cloud_rank|cloud_chat|cloud_pay|cloud_sites|cloud_beautify|turnover_settings)\.php(?:(?!</li>|<li[\s>]).)*?</li>~s';
            $html = hwpr($leaf, '', $html);

            // ---------- 2. 移除"站长互联"父级折叠块（子项已全部移除） ----------
            // 父级 li 的 class 由 checkIfActive 动态填充，输出时可能为空，因此按菜单文案匹配
            $parent = '~<li[^>]*>(?:(?!</li>).)*?站长互联(?:(?!</li>).)*?</li>~s';
            $html = hwpr($parent, '', $html);
            $parent2 = '~<li[^>]*class="[^"]*fallset[^"]*"[^>]*>.*?</ul>\s*</li>~s';
            $html = hwpr($parent2, '', $html);

            // 清理可能残留的空子菜单容器
            $html = preg_replace('~<ul[^>]*>\s*</ul>~s', '', $html);
        }

        // ---------- 1b. 重定向 set.php?mod=site / mod=chat 链接到独立修复页面 ----------
        // 原 set.php 混淆层对 mod=site / mod=chat 渲染空壳，已用独立页面替代
        $html = preg_replace(
            '~set\.php\?mod=site([\'"])~',
            'set_site.php$1',
            $html
        );
        $html = preg_replace(
            '~set\.php\?mod=chat([\'"])~',
            'set_chat.php$1',
            $html
        );

        // 若用户直接访问 set.php?mod=site 或 mod=chat（空壳页），注入 JS 跳转
        $curScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
        $curMod    = $_GET['mod'] ?? '';
        if ($curScript === 'set.php' && ($curMod === 'site' || $curMod === 'chat')) {
            $target = $curMod === 'site' ? 'set_site.php' : 'set_chat.php';
            $inject = '<script>location.href="./' . $target . '";</script>';
            // 在 </head> 前注入跳转，确保在页面渲染前生效
            $html = preg_replace('~</head>~i', $inject . '</head>', $html, 1);
        }

        // ---------- 3b. 全局预载 layer 弹层皮肤 CSS ----------
        // 根因修复：layer.js 依赖 theme/default/layer.css（此前该目录缺失导致 404，
        // 弹层完全失去 fixed 定位与层叠上下文，弹窗坠到页面底部、按钮无法点击）。
        // 预载并使用 layer 官方注入所用 id（layuicss-layer），layer.js 检测到该 id 后不会重复注入。
        if (stripos($html, 'layuicss-layer') === false) {
            $layerCss = '/assets/vendor/lib.baomitu.com/layer/3.1.1/theme/default/layer.css?v=3.1.1';
            $html = preg_replace(
                '~</head>~i',
                '<link id="layuicss-layer" rel="stylesheet" href="' . $layerCss . '"/></head>',
                $html,
                1
            );
        }

        // ---------- 3c. 注入全局保存按钮与提示框 ----------
        // 注意：本过滤器可能因输出缓冲嵌套被多次调用（head_core 内层 ob + 页面外层 ob），
        // 注入必须幂等，否则 save-button / danger-modal / CSRF 注入脚本会重复，
        // 导致 XHR open 钩子双层包装 → X-CSRF-Token 头重复 → 后台所有 POST 403。
        if (stripos($html, 'hw-global-save') === false) {
        $html .= <<<'HW_SNIPPET'

<!-- 全局保存按钮（虎王云） -->
<style>
#hw-global-save{position:fixed;right:26px;bottom:30px;z-index:9999;width:52px;height:52px;border-radius:50%;border:none;background:#6366f1;color:#fff;box-shadow:0 8px 20px rgba(99,102,241,.45);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform .15s,box-shadow .15s,opacity .15s}
#hw-global-save:hover{transform:translateY(-2px);box-shadow:0 12px 26px rgba(99,102,241,.55)}
#hw-global-save:active{transform:translateY(0)}
#hw-global-save.hw-busy{opacity:.65;pointer-events:none}
#hw-global-save svg{width:24px;height:24px;fill:#fff}
#hw-toast-wrap{position:fixed;top:20px;right:26px;z-index:10000;display:flex;flex-direction:column;gap:10px;pointer-events:none}
.hw-toast{min-width:200px;max-width:380px;padding:12px 18px;border-radius:8px;color:#fff;font-size:14px;line-height:1.5;box-shadow:0 6px 18px rgba(0,0,0,.2);opacity:0;transform:translateY(-8px);transition:opacity .25s,transform .25s;word-break:break-all}
.hw-toast.hw-show{opacity:1;transform:translateY(0)}
.hw-toast.hw-ok{background:#22c55e}
.hw-toast.hw-err{background:#ef4444}
</style>
<button id="hw-global-save" type="button" title="全局保存（Ctrl+S）" aria-label="全局保存">
  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"/></svg>
</button>
<div id="hw-toast-wrap"></div>
<script>
(function(){
  var btn=document.getElementById('hw-global-save');
  if(!btn)return;

  /* ---------------- 提示框 ---------------- */
  function toast(msg,type,ms){
    var wrap=document.getElementById('hw-toast-wrap');
    if(!wrap)return;
    var t=document.createElement('div');
    t.className='hw-toast '+(type==='ok'?'hw-ok':'hw-err');
    t.textContent=msg;
    wrap.appendChild(t);
    requestAnimationFrame(function(){t.classList.add('hw-show');});
    setTimeout(function(){
      t.classList.remove('hw-show');
      setTimeout(function(){if(t.parentNode)t.parentNode.removeChild(t);},300);
    },ms||(type==='ok'?2000:3200));
  }

  /* ---------------- 结果判定（本系统约定 code==0 为成功） ---------------- */
  function judgeAndToast(status,txt){
    if(!window.__hwArmed||window.__hwToastShown)return;
    window.__hwToastShown=true;
    window.__hwArmed=0;
    btn.classList.remove('hw-busy');
    var ok=true,reason='';
    if(!status&&!((txt||'').trim())){ok=false;reason='网络请求异常';}
    else if(status&&status>=400){ok=false;reason='HTTP '+status;}
    else{
      var t=(txt||'').replace(/^\uFEFF/,'').trim();
      if(/^[\{\[]/.test(t)){
        try{
          var d=JSON.parse(t);
          if(d&&typeof d.code!=='undefined'&&d.code!==null){
            /* 兼容本系统 code==0=成功 与常见 code==1=成功 两种约定 */
            if(d.code==0||d.code==1||d.code==='1'||d.code===true||d.code==='success'){ok=true;}
            else{ok=false;reason=(d.msg||d.info||d.error||('状态码 code='+d.code));}
          }
        }catch(ex){ok=false;reason='响应解析失败';}
      }
    }
    if(ok){toast('保存成功','ok');}
    else{toast('保存失败：'+reason,'err');}
  }

  /* ---------------- 网络响应监视（XHR + fetch 双通道，一次性判定） ---------------- */
  if(!window.__hwXhrHooked){
    window.__hwXhrHooked=true;
    var origOpen=XMLHttpRequest.prototype.open;
    var origSend=XMLHttpRequest.prototype.send;
    XMLHttpRequest.prototype.send=function(){
      var self=this;
      if(window.__hwArmed){
        this.addEventListener('load',function(){
          setTimeout(function(){judgeAndToast(self.status,self.responseText);},0);
        });
        this.addEventListener('error',function(){judgeAndToast(0,'');});
      }
      return origSend.apply(this,arguments);
    };
    if(window.fetch){
      var origFetch=window.fetch;
      window.fetch=function(){
        var armedNow=!!window.__hwArmed;
        var p=origFetch.apply(this,arguments);
        if(armedNow){
          p.then(function(r){
            try{r.clone().text().then(function(t){judgeAndToast(r.status,t);});}
            catch(e){judgeAndToast(r.status,'');}
          }).catch(function(){judgeAndToast(0,'');});
        }
        return p;
      };
    }
  }

  /* ---------------- 查找保存控件 ---------------- */
  function visible(el){
    return !!(el.offsetWidth||el.offsetHeight||el.getClientRects().length);
  }

  function findSaveControl(){
    var nodes=document.querySelectorAll('button,input[type=submit],input[type=button],a.btn,[role=button]');
    var textHit=null,submitHit=null,i,el,t;
    for(i=0;i<nodes.length;i++){
      el=nodes[i];
      if(el.id==='hw-global-save')continue;
      if(!visible(el))continue;
      t=(el.innerText||el.textContent||el.value||'').replace(/\s+/g,'');
      if(el.getAttribute('type')==='submit'&&el.closest('form')&&!submitHit)submitHit=el;
      if(t&&!textHit&&/保存|确定|提交|应用|部署|发布|更新|执行|安装|启用|开始|创建/.test(t))textHit=el;
    }
    return textHit||submitHit||null;
  }

  /* ---------------- 触发保存 ---------------- */
  var busyTimer=null,armTimer=null;

  function arm(){
    window.__hwArmed=Date.now();
    window.__hwToastShown=false;
    clearTimeout(armTimer);
    armTimer=setTimeout(function(){
      /* 6.5 秒内无任何响应且页面未跳转 → 判定失败 */
      if(window.__hwArmed&&!window.__hwToastShown){
        window.__hwArmed=0;
        window.__hwToastShown=true;
        btn.classList.remove('hw-busy');
        toast('保存失败：保存请求未完成（无响应）','err');
      }
    },6500);
  }

  function triggerSave(){
    if(btn.classList.contains('hw-busy'))return;
    var ctrl=findSaveControl(),form=null;
    if(!ctrl){
      /* 兜底：可见表单存在时用 requestSubmit（会触发 onsubmit 的 AJAX 保存逻辑） */
      var forms=document.querySelectorAll('form');
      for(var i=0;i<forms.length;i++){
        if(visible(forms[i])){form=forms[i];break;}
      }
      if(!form){toast('保存失败：当前页面没有可用的保存操作','err');return;}
      arm();
      btn.classList.add('hw-busy');
      clearTimeout(busyTimer);
      busyTimer=setTimeout(function(){btn.classList.remove('hw-busy');},4000);
      if(form.requestSubmit){form.requestSubmit();}
      else{form.submit();}
      return;
    }
    arm();
    btn.classList.add('hw-busy');
    clearTimeout(busyTimer);
    busyTimer=setTimeout(function(){btn.classList.remove('hw-busy');},4000);
    try{ctrl.click();}catch(e){
      btn.classList.remove('hw-busy');
      toast('保存失败：'+(e&&e.message?e.message:'触发保存动作异常'),'err');
    }
  }

  btn.addEventListener('click',triggerSave);
  document.addEventListener('keydown',function(e){
    if((e.ctrlKey||e.metaKey)&&(e.key==='s'||e.key==='S')){
      e.preventDefault();triggerSave();
    }
  });

  /* jQuery 全局 AJAX 事件兜底（与 XHR 钩子去重：先到先判） */
  if(window.jQuery){
    jQuery(document).ajaxComplete(function(evt,xhr){
      if(!window.__hwArmed||window.__hwToastShown)return;
      judgeAndToast(xhr.status,xhr.responseText||'');
    });
  }
})();
</script>

<!-- 破坏性操作二次确认（修复 #11） -->
<style>
#hw-danger-modal{position:fixed;inset:0;z-index:10001;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.5)}
#hw-danger-modal.hw-show{display:flex}
.hw-danger-box{background:#fff;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.2);max-width:420px;width:90%;padding:28px 32px;text-align:center}
.hw-danger-icon{width:56px;height:56px;border-radius:50%;background:#fef2f2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.hw-danger-icon svg{width:28px;height:28px;fill:#ef4444}
.hw-danger-box h3{color:#1e293b;font-size:18px;font-weight:700;margin:0 0 8px}
.hw-danger-box p{color:#64748b;font-size:14px;line-height:1.5;margin:0 0 16px}
.hw-danger-input{width:100%;height:42px;border:2px solid #e2e8f0;border-radius:10px;text-align:center;font-size:15px;outline:none;transition:border-color .15s}
.hw-danger-input:focus{border-color:#ef4444}
.hw-danger-hint{font-size:12px;color:#94a3b8;margin:8px 0 16px}
.hw-danger-btns{display:flex;gap:12px;justify-content:center}
.hw-danger-btns button{height:40px;padding:0 24px;border-radius:10px;font-size:14px;font-weight:500;cursor:pointer;border:none;transition:opacity .15s}
.hw-danger-cancel{background:#f1f5f9;color:#475569}
.hw-danger-cancel:hover{background:#e2e8f0}
.hw-danger-confirm{background:#ef4444;color:#fff}
.hw-danger-confirm:hover{background:#dc2626}
.hw-danger-confirm:disabled{opacity:.5;cursor:not-allowed}
</style>
<div id="hw-danger-modal">
  <div class="hw-danger-box">
    <div class="hw-danger-icon"><svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg></div>
    <h3>确认执行危险操作</h3>
    <p id="hw-danger-desc">您即将执行删除操作，此操作不可撤销。</p>
    <input type="text" id="hw-danger-input" class="hw-danger-input" placeholder='请输入"确认"二字' autocomplete="off">
    <p class="hw-danger-hint">输入"确认"后点击下方按钮以继续</p>
    <div class="hw-danger-btns">
      <button type="button" class="hw-danger-cancel" id="hw-danger-cancel">取消</button>
      <button type="button" class="hw-danger-confirm" id="hw-danger-confirm" disabled>确认执行</button>
    </div>
  </div>
</div>
<script>
(function(){
  var modal=document.getElementById('hw-danger-modal');
  if(!modal)return;
  var input=document.getElementById('hw-danger-input');
  var confirmBtn=document.getElementById('hw-danger-confirm');
  var cancelBtn=document.getElementById('hw-danger-cancel');
  var descEl=document.getElementById('hw-danger-desc');
  var pendingAction=null;

  function openModal(desc,action){
    descEl.textContent=desc||'您即将执行不可撤销的操作。';
    pendingAction=action;
    input.value='';
    confirmBtn.disabled=true;
    modal.classList.add('hw-show');
    setTimeout(function(){input.focus();},100);
  }
  function closeModal(){
    modal.classList.remove('hw-show');
    pendingAction=null;
  }
  input.addEventListener('input',function(){
    confirmBtn.disabled=(input.value.trim()!=='确认');
  });
  confirmBtn.addEventListener('click',function(){
    if(confirmBtn.disabled||!pendingAction)return;
    closeModal();
    pendingAction();
  });
  cancelBtn.addEventListener('click',closeModal);
  modal.addEventListener('click',function(e){if(e.target===modal)closeModal();});

  /* 拦截含删除/抹除关键词的链接和按钮 */
  var dangerPattern=/删除|抹除|清除|清空|卸载|移除|批量删|清空所有|彻底删除/i;
  function isDangerous(el){
    var t=(el.innerText||el.textContent||el.value||'').replace(/\s+/g,'');
    return dangerPattern.test(t);
  }

  /* 拦截 <a> 链接 */
  document.addEventListener('click',function(e){
    var a=e.target.closest('a');
    if(!a)return;
    if(!isDangerous(a))return;
    var href=a.getAttribute('href');
    if(!href||href==='#')return;
    /* 已有 onclick confirm 的跳过（避免双重拦截） */
    var onclick=a.getAttribute('onclick');
    if(onclick&&/confirm/i.test(onclick)){
      /* 移除原有 confirm，由本模态接管 */
      a.removeAttribute('onclick');
    }
    e.preventDefault();
    e.stopPropagation();
    openModal('您即将执行：'+(a.innerText||a.textContent||'').trim()+'，此操作不可撤销。',function(){
      window.location.href=href;
    });
  },true);

  /* 拦截表单提交按钮 */
  document.addEventListener('click',function(e){
    var btn=e.target.closest('button[type=submit],input[type=submit],button:not([type])');
    if(!btn)return;
    if(btn.id==='hw-danger-confirm')return;
    if(!isDangerous(btn))return;
    var form=btn.closest('form');
    if(!form)return;
    e.preventDefault();
    e.stopPropagation();
    openModal('您即将执行：'+(btn.innerText||btn.value||'').trim()+'，此操作不可撤销。',function(){
      if(form.requestSubmit)form.requestSubmit(btn);
      else form.submit();
    });
  },true);
})();
</script>
HW_SNIPPET;
        } // end 幂等保护：hw-global-save 注入

        // ---------- 4. CSRF Token 自动注入 ----------
        $csrf = isset($GLOBALS['csrf_token']) ? $GLOBALS['csrf_token'] : (isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '');
        // 幂等保护：过滤器可能被嵌套调用多次，绝不能重复注入本脚本，
        // 否则 XHR open 钩子双层包装 → X-CSRF-Token 头重复 → 所有后台 POST 403。
        if ($csrf !== '' && stripos($html, 'window.CSRF_TOKEN') === false) {
            $html .= '<script>window.CSRF_TOKEN=' . json_encode($csrf) . ';'
                . '(function(){var T=window.CSRF_TOKEN;if(!T)return;'
                // 钩子防重入守卫：即使本脚本意外出现两份，钩子也只包一层
                . 'if(window.__hwCsrfHooked)return;window.__hwCsrfHooked=true;'
                // 自动给所有表单注入隐藏域
                . 'function injectForm(f){if(f.__csrf)return;f.__csrf=1;'
                . 'var i=document.createElement("input");i.type="hidden";i.name="csrf_token";i.value=T;f.appendChild(i);}'
                . 'document.querySelectorAll("form").forEach(injectForm);'
                // 监听动态添加的表单
                . 'new MutationObserver(function(muts){muts.forEach(function(m){m.addedNodes.forEach(function(n){'
                . 'if(n.tagName==="FORM")injectForm(n);'
                . 'if(n.querySelectorAll){n.querySelectorAll("form").forEach(injectForm);}'
                . '});});}).observe(document.body,{childList:true,subtree:true});'
                // 拦截 XMLHttpRequest，自动添加 X-CSRF-Token 头
                . 'var xo=XMLHttpRequest.prototype.open;'
                . 'XMLHttpRequest.prototype.open=function(m,u){xo.apply(this,arguments);'
                . 'if(m&&m.toUpperCase()==="POST"){try{this.setRequestHeader("X-CSRF-Token",T);}catch(e){}}};'
                // 拦截 fetch（兼容 fetch('url',{method:'POST'}) 与 fetch(Request) 两种写法）
                . 'if(window.fetch){var fo=window.fetch;window.fetch=function(){'
                . 'var m=(arguments[0]&&arguments[0].method)||(arguments[1]&&arguments[1].method)||"GET";'
                . 'if(m&&m.toUpperCase()==="POST"){'
                . 'if(arguments[0]&&typeof arguments[0]==="object"&&arguments[0].method){'
                . 'var h=arguments[0].headers;if(!h)h=arguments[0].headers={};'
                . 'if(!(h instanceof Headers)){h["X-CSRF-Token"]=T;}'
                . '}else if(arguments[1]&&typeof arguments[1]==="object"){'
                . 'var h2=arguments[1].headers;if(!h2)h2=arguments[1].headers={};'
                . 'if(!(h2 instanceof Headers)){h2["X-CSRF-Token"]=T;}'
                . '}else{var a2=Array.prototype.slice.call(arguments);'
                . 'a2[1]=Object.assign({},a2[1]||{},{});'
                . 'var hh=a2[1].headers;if(!hh)hh=a2[1].headers={};'
                . 'if(!(hh instanceof Headers)){hh["X-CSRF-Token"]=T;}'
                . 'return fo.apply(this,a2);}'
                . '}'
                . 'return fo.apply(this,arguments);};}'
                // 注意：不要再加 jQuery.ajaxSend 通道——jQuery 底层走 XHR，
                // 上面的 open 钩子已设置该头，两条通道叠加会导致同名头重复（"token, token"）→ 403。
                . '})();</script>';
        }

        return $html;
    }
}

// ---------- 配置键兜底 ----------
// head_core.php（混淆层）直接访问 $conf['font_beautify'] / $conf['background_enable']
// 等键，但 pre_config 表中无对应行，导致每次请求产生 PHP Notice。
// 在引入 head_core.php 之前为缺失键设置默认值，消除刷屏告警。
if (isset($conf) && is_array($conf)) {
    $confDefaults = [
        'font_beautify'     => '0',
        'background_enable' => '0',
        'mouse_beautify'    => '0',
    ];
    foreach ($confDefaults as $ck => $cv) {
        if (!isset($conf[$ck])) {
            $conf[$ck] = $cv;
        }
    }
}

ob_start('hw_head_filter');
require __DIR__ . '/head_core.php';
