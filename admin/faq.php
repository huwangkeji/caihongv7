<?php
include "../includes/common.php";

$title = "常见问题解答";
include "./head.php";
if ($islogin == 1) {
} else {
	exit("<script language='javascript'>window.location.href='./login.php';</script>");
}
?>

<div class="w-full max-w-[1000px] mx-auto space-y-8 pb-10">
<?php 
adminpermission("article", 1);
?>

    <!-- 页面头部信息 -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex items-center bg-slate-50/50">
            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-500 flex items-center justify-center text-xl mr-3 shrink-0">
                <i class="fa fa-question-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 m-0">常见问题解答</h3>
                <p class="text-sm text-slate-500 mt-1 m-0">本页面汇总了系统使用和开发过程中的常见问题及解决方案，帮助您快速解决遇到的困难。</p>
            </div>
        </div>
        <div class="px-6 py-3 bg-slate-50 text-xs text-slate-400 flex justify-end">
            <span>虎王云自助下单系统 V4</span>
        </div>
    </div>

    <!-- 常见问题列表 -->
    <div class="space-y-8">
        
        <!-- ================= 一、系统安装与配置 ================= -->
        <div>
            <div class="flex items-center mb-4">
                <span class="w-1.5 h-5 bg-brand-500 rounded-full mr-2"></span>
                <h4 class="text-lg font-bold text-slate-800 m-0">一、系统安装与配置</h4>
            </div>
            
            <div class="space-y-3">
                <!-- Q1 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 系统安装要求是什么？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">系统安装要求如下：</p>
                        <ul class="list-disc list-inside space-y-1.5 ml-2 mb-4">
                            <li>操作系统：Windows Server 2012+ 或 Linux 系统</li>
                            <li>Web服务器：Nginx 1.22.0+</li>
                            <li>PHP版本：7.4+</li>
                            <li>MySQL版本：5.7+</li>
                            <li>其他：宝塔面板（推荐）</li>
                        </ul>
                        <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 flex items-start shadow-sm">
                            <i class="fa fa-lightbulb-o mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">提示：</strong>建议使用宝塔面板进行环境配置，可大大简化安装过程。</div>
                        </div>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何配置数据库连接？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">数据库连接配置位于系统根目录的 <code class="bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded text-rose-500 font-mono">config.php</code> 文件中，您需要修改以下参数：</p>
                        <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl overflow-x-auto text-sm font-mono leading-relaxed mb-4 whitespace-pre-wrap break-all shadow-inner border border-slate-900">&lt;?php
// 数据库配置
define('DB_HOST', 'localhost'); // 数据库主机
define('DB_USER', 'root'); // 数据库用户名
define('DB_PASS', 'password'); // 数据库密码
define('DB_NAME', 'shua_system'); // 数据库名
define('DB_PORT', '3306'); // 数据库端口
?&gt;</pre>
                        <p class="mb-4">修改完成后保存文件，系统将使用新的数据库连接信息。</p>
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 flex items-start shadow-sm">
                            <i class="fa fa-exclamation-triangle mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">警告：</strong>请确保数据库用户具有足够的权限，同时不要在生产环境中使用 root 用户直接连接数据库。</div>
                        </div>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 安装后无法访问后台怎么办？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">安装后无法访问后台可能有以下几种原因及解决方案：</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2">
                            <li><strong class="text-slate-800">文件权限问题</strong>：确保所有 PHP 文件具有正确的读写权限。</li>
                            <li><strong class="text-slate-800">数据库连接错误</strong>：检查 <code class="bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded text-rose-500 font-mono">config.php</code> 中的数据库配置是否正确。</li>
                            <li><strong class="text-slate-800">Nginx配置问题</strong>：确保 Nginx 正确配置了伪静态规则。
                                <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl overflow-x-auto text-sm font-mono leading-relaxed mt-2 mb-2 whitespace-pre-wrap shadow-inner border border-slate-900">location / {
    if (!-e $request_filename) {
        rewrite  ^(.*)$  /index.php?s=$1  last;
        break;
    }
}</pre>
                            </li>
                            <li><strong class="text-slate-800">PHP扩展缺失</strong>：检查是否安装了必要的 PHP 扩展（如 pdo_mysql、mbstring 等）。</li>
                            <li><strong class="text-slate-800">浏览器缓存问题</strong>：清除浏览器缓存或尝试使用无痕模式访问。</li>
                        </ol>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何修改系统的基本配置（如网站名称、Logo等）？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">您可以通过以下两种方式修改系统基本配置：</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2 mb-4">
                            <li><strong class="text-slate-800">通过后台管理界面</strong>：登录后台后，进入 "系统设置" -> "网站信息配置" 页面进行修改。</li>
                            <li><strong class="text-slate-800">直接修改数据库</strong>：可以直接修改 <code class="bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded text-rose-500 font-mono">pre_config</code> (原 shua_config) 表中的相应配置项。
                                <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl overflow-x-auto text-sm font-mono leading-relaxed mt-2 mb-2 whitespace-pre-wrap shadow-inner border border-slate-900">-- 例如修改网站名称
UPDATE pre_config SET v = '新的网站名称' WHERE k = 'web_name';</pre>
                            </li>
                        </ol>
                        <p>修改完成后，系统会自动应用新的配置，无需重启服务。</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 二、系统使用问题 ================= -->
        <div>
            <div class="flex items-center mb-4">
                <span class="w-1.5 h-5 bg-brand-500 rounded-full mr-2"></span>
                <h4 class="text-lg font-bold text-slate-800 m-0">二、系统使用问题</h4>
            </div>
            
            <div class="space-y-3">
                <!-- Q1 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何添加新的商品？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">添加新商品的步骤如下：</p>
                        <ol class="list-decimal list-inside space-y-1.5 ml-2 mb-4">
                            <li>登录系统后台</li>
                            <li>点击左侧菜单的 "商品管理" -> "商品列表"</li>
                            <li>点击页面顶部的 "添加商品" 按钮</li>
                            <li>填写商品信息，包括商品名称、价格、库存、描述等</li>
                            <li>选择商品分类和相关配置</li>
                            <li>点击 "提交" 按钮完成添加</li>
                        </ol>
                        <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 flex items-start shadow-sm">
                            <i class="fa fa-lightbulb-o mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">提示：</strong>添加商品时，请确保填写清晰的商品描述和准确的价格信息，以便用户更好地了解商品。</div>
                        </div>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何处理用户的订单？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">处理用户订单的步骤如下：</p>
                        <ol class="list-decimal list-inside space-y-1.5 ml-2 mb-4">
                            <li>登录系统后台</li>
                            <li>点击左侧菜单的 "订单管理" -> "订单列表"</li>
                            <li>查看待处理的订单列表</li>
                            <li>点击订单操作列的 "编辑/处理" 按钮进行处理</li>
                            <li>根据订单类型和要求完成相应操作</li>
                            <li>处理完成后，更新订单状态为 "已完成"</li>
                        </ol>
                        <p>对于自动对接处理的订单，系统会根据预设的对接站点规则自动完成处理，无需手动干预。</p>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何设置支付方式？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">设置支付方式的步骤如下：</p>
                        <ol class="list-decimal list-inside space-y-1.5 ml-2 mb-4">
                            <li>登录系统后台</li>
                            <li>点击左侧菜单的 "系统配置" -> "支付接口配置"</li>
                            <li>选择您需要启用的支付方式（如易支付、微信支付、支付宝等）</li>
                            <li>填写相应的支付参数（如 商户ID、商户密钥 等）</li>
                            <li>点击 "保存" 按钮，并启用该支付接口</li>
                        </ol>
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 flex items-start shadow-sm">
                            <i class="fa fa-exclamation-triangle mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">注意：</strong>支付参数涉及资金安全，请妥善保管，不要泄露给他人。建议定期更换对接密钥。</div>
                        </div>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何查看系统日志？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">查看系统日志的方式有两种：</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2 mb-4">
                            <li><strong class="text-slate-800">通过后台管理界面</strong>：
                                <ul class="list-disc list-inside ml-6 mt-1 text-slate-500">
                                    <li>登录系统后台</li>
                                    <li>点击左侧菜单的 "其他功能" -> "系统日志" 或者 "对接日志"</li>
                                    <li>可以查看系统运行过程中的各种订单异常及对接报错记录</li>
                                </ul>
                            </li>
                            <li><strong class="text-slate-800">直接查看日志文件</strong>：
                                <ul class="list-disc list-inside ml-6 mt-1 text-slate-500">
                                    <li>系统底层崩溃日志通常保存在网站环境目录下</li>
                                    <li>可以使用宝塔面板的文件管理或文本编辑器直接查看日志内容</li>
                                </ul>
                            </li>
                        </ol>
                        <p>系统日志有助于站长排查订单对接失败问题和监控系统的运行状态。</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 三、开发相关问题 ================= -->
        <div>
            <div class="flex items-center mb-4">
                <span class="w-1.5 h-5 bg-brand-500 rounded-full mr-2"></span>
                <h4 class="text-lg font-bold text-slate-800 m-0">三、开发相关问题</h4>
            </div>
            
            <div class="space-y-3">
                <!-- Q1 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何扩展系统功能？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">扩展系统功能的方法如下：</p>
                        <ol class="list-decimal list-inside space-y-2 ml-2 mb-4">
                            <li><strong class="text-slate-800">了解系统架构</strong>：查看系统的整体架构和模块划分。</li>
                            <li><strong class="text-slate-800">遵循开发规范</strong>：遵循系统的编码规范和开发流程。</li>
                            <li><strong class="text-slate-800">添加新模块</strong>：
                                <ul class="list-disc list-inside ml-6 mt-1 text-slate-500">
                                    <li>在相应目录下创建新的 PHP 业务文件</li>
                                    <li>实现所需功能</li>
                                    <li>在数据库中添加必要的表或字段</li>
                                    <li>在后台 `head.php` 菜单逻辑中注册新功能栏目</li>
                                </ul>
                            </li>
                            <li><strong class="text-slate-800">修改现有功能</strong>：
                                <ul class="list-disc list-inside ml-6 mt-1 text-slate-500">
                                    <li>找到相应的功能文件 (如 `ajax.php`, `ajax_shop.php` 等)</li>
                                    <li>按照需求进行修改</li>
                                    <li>进行充分的沙盒测试</li>
                                </ul>
                            </li>
                        </ol>
                        <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 flex items-start shadow-sm">
                            <i class="fa fa-lightbulb-o mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">提示：</strong>在扩展系统功能时，建议先在测试环境中进行开发和测试，确认无误后再部署到生产环境，切勿直接在正式站修改核心文件。</div>
                        </div>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何调用系统API？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">调用系统 API 的步骤如下：</p>
                        <ol class="list-decimal list-inside space-y-1.5 ml-2 mb-4">
                            <li><strong class="text-slate-800">获取API文档</strong>：查阅同台提供的官方接口调用文档。</li>
                            <li><strong class="text-slate-800">获取API密钥</strong>：在分站后台或主站对接配置中获取调用秘钥。</li>
                            <li><strong class="text-slate-800">构建API请求</strong>：准备参数，进行签名 MD5 验证，发送 POST 或 GET。</li>
                            <li><strong class="text-slate-800">处理API响应</strong>：接收返回的 JSON，解析并处理业务逻辑。</li>
                        </ol>
                        <p class="mb-2">以下是一个调用 API 的示例代码：</p>
                        <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl overflow-x-auto text-sm font-mono leading-relaxed whitespace-pre-wrap break-all shadow-inner border border-slate-900">&lt;?php
// API调用示例
function call_api($url, $params, $api_token) {
    $params['token'] = $api_token;
    $params['time'] = time();
    // 构建签名
    ksort($params);
    $sign_str = http_build_query($params);
    $params['sign'] = md5($sign_str);
    
    // 发送请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    // 返回解析后的JSON数据
    return json_decode($result, true);
}

// 调用示例
$api_url = 'http://您的域名/api.php';
$api_token = 'your_api_token';
$params = array(
    'action' => 'get_goods_list',
    'page' => 1,
    'page_size' => 10
);

$result = call_api($api_url, $params, $api_token);
if ($result['code'] == 0) {
    // 处理成功
    print_r($result['data']);
} else {
    // 处理失败
    echo 'Error: ' . $result['msg'];
}
?&gt;</pre>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何调试系统问题？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">调试系统问题的方法如下：</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2 mb-4">
                            <li><strong class="text-slate-800">启用调试模式</strong>：
                                <ul class="list-disc list-inside ml-6 mt-1 text-slate-500">
                                    <li>临时开启 PHP 的错误显示（<code class="text-rose-500">display_errors=On</code>）</li>
                                    <li>系统会输出更详细的致命错误信息定位到具体行数</li>
                                </ul>
                            </li>
                            <li><strong class="text-slate-800">使用打印函数</strong>：
                                <pre class="bg-slate-800 text-slate-200 p-4 rounded-xl overflow-x-auto text-sm font-mono leading-relaxed mt-2 mb-2 whitespace-pre-wrap shadow-inner border border-slate-900">// 使用 print_r 或 var_dump 输出变量信息
$data = array('key' => 'value');
var_dump($data); exit; // 打印并强制截断终止程序运行观察</pre>
                            </li>
                            <li><strong class="text-slate-800">检查数据库</strong>：使用 phpMyAdmin 工具检查表结构以及实际入库数据是否符合预期。</li>
                            <li><strong class="text-slate-800">检查网络请求</strong>：使用浏览器 F12 (开发者工具) -> Network (网络) 面板查看 AJAX 请求的状态码和 Response 响应值。</li>
                        </ol>
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 flex items-start shadow-sm">
                            <i class="fa fa-exclamation-triangle mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">注意：</strong>调试模式会输出敏感的服务器路径与数据库信息，排查完毕后请务必在线上生产环境关闭错误输出。</div>
                        </div>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 如何优化系统性能？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">优化系统性能的方法如下：</p>
                        <ol class="list-decimal list-inside space-y-3 ml-2 mb-4">
                            <li><strong class="text-slate-800">数据库优化</strong>：定期在后台执行数据清理（清理日志、无用记录），在宝塔中定期执行表优化（OPTIMIZE TABLE）。</li>
                            <li><strong class="text-slate-800">代码侧优化</strong>：升级至 PHP 7.4 或 PHP 8.1 引擎，并开启 OPcache 脚本缓存加速。</li>
                            <li><strong class="text-slate-800">服务器优化</strong>：配置 Nginx 开启 gzip 压缩；如果流量过大，建议升级服务器 CPU 与内存带宽。</li>
                            <li><strong class="text-slate-800">前端与 CDN 加速</strong>：将系统的公共 JS/CSS 静态资源文件使用免费公共 CDN（本系统默认已集成），或套一层云防御 CDN 隐藏源站 IP 并加速响应。</li>
                        </ol>
                        <div class="p-4 bg-sky-50 border border-sky-100 rounded-xl text-sky-700 flex items-start shadow-sm">
                            <i class="fa fa-lightbulb-o mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">提示：</strong>性能优化是一个持续的过程，建议定期进入系统的“数据清理”菜单瘦身，保证极速访问。</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= 四、故障排除 ================= -->
        <div>
            <div class="flex items-center mb-4">
                <span class="w-1.5 h-5 bg-brand-500 rounded-full mr-2"></span>
                <h4 class="text-lg font-bold text-slate-800 m-0">四、故障排除</h4>
            </div>
            
            <div class="space-y-3">
                <!-- Q1 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 系统出现 500 错误怎么办？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">系统出现 HTTP 500（内部服务器错误）可能有以下原因及解决方案：</p>
                        <ol class="list-decimal list-inside space-y-2 ml-2 mb-4">
                            <li><strong class="text-slate-800">PHP语法错误</strong>：检查您最近手动修改过的 PHP 文件是否少写了分号或括号。</li>
                            <li><strong class="text-slate-800">文件权限问题</strong>：部分服务器面板对防跨站要求严格，需确保 Web 目录拥有 www 用户组 755/644 权限。</li>
                            <li><strong class="text-slate-800">内存或超时拦截</strong>：如果处理极大数据量卡死，请去 PHP 配置中调大 <code class="text-brand-600">memory_limit</code> 和 <code class="text-brand-600">max_execution_time</code>。</li>
                            <li><strong class="text-slate-800">数据库连接断开</strong>：检查数据库服务是否因为超载而宕机停止，可尝试在面板重启 MySQL。</li>
                        </ol>
                        <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl text-amber-700 flex items-start shadow-sm">
                            <i class="fa fa-exclamation-triangle mt-0.5 mr-2 text-lg"></i>
                            <div><strong class="font-bold">注意：</strong>盲目猜测不如看日志，最快的方法是去宝塔面板查看对应的网站错误日志文件。</div>
                        </div>
                    </div>
                </div>

                <!-- Q2 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 订单支付成功但状态未更新（未发货）怎么办？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">俗称“掉单”，可能有以下原因及排查方案：</p>
                        <ol class="list-decimal list-inside space-y-2 ml-2 mb-4">
                            <li><strong class="text-slate-800">异步回调被防火墙拦截</strong>：检查您的宝塔防火墙或外部 CDN 云墙是否拦截了易支付的 notify_url 异步回调请求（通常表现为白名单拦截）。</li>
                            <li><strong class="text-slate-800">支付密钥填写错误</strong>：两边签名 MD5 对不上，系统主动拒绝了非法的伪造回调，请重新核对密钥。</li>
                            <li><strong class="text-slate-800">服务器处于离线断网状态</strong>：虽然付款成功了，但服务器网络阻断，导致处理脚本无法下发指令到对接端。</li>
                        </ol>
                        <p>解决：您可以去后台手动将异常订单标记补单发货，然后去排查防火墙的拦截日志。</p>
                    </div>
                </div>

                <!-- Q3 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 用户无法登录系统怎么办？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">分站/用户无法登录的原因：</p>
                        <ol class="list-decimal list-inside space-y-2 ml-2">
                            <li><strong class="text-slate-800">账号密码错误</strong>：可在后台分站管理处强行修改密码。</li>
                            <li><strong class="text-slate-800">账号已被后台封禁</strong>：请核实对应状态是否已被关闭停用。</li>
                            <li><strong class="text-slate-800">Session/Cookie写盘失败</strong>：检查服务器硬盘是否已满导致 100% 占载，PHP 无法写入 session 文件从而无法保存登录状态。</li>
                        </ol>
                    </div>
                </div>

                <!-- Q4 -->
                <div class="faq-item bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden transition-all duration-200">
                    <div class="faq-question px-6 py-4 cursor-pointer hover:bg-slate-50 flex justify-between items-center transition-colors">
                        <span class="font-bold text-slate-700 text-base"><i class="fa fa-q text-brand-500 mr-2 opacity-60"></i> 系统前台打开非常缓慢怎么办？</span>
                        <i class="fa fa-chevron-down text-slate-400 transition-transform duration-300 transform"></i>
                    </div>
                    <div class="faq-answer hidden px-6 py-5 border-t border-slate-100 bg-slate-50/30 text-sm text-slate-600">
                        <p class="mb-3">首屏加载过慢的终极排查指南：</p>
                        <ol class="list-decimal list-inside space-y-2 ml-2 mb-4">
                            <li><strong class="text-slate-800">外部资源被墙或失效</strong>：按 F12 进入控制台，查看是否有某些引用的外部 js/css（如失效的公共 CDN）由于加载超时卡住了整个页面渲染。</li>
                            <li><strong class="text-slate-800">遭受 CC 并发攻击</strong>：前往后台开启系统的防 CC 模块，或接入免费的云墙防御。</li>
                            <li><strong class="text-slate-800">数据库表过于庞大</strong>：订单表几十万条数据未清理。前往“数据清理”中一键优化。</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="<?php echo $cdnpublic;?>jquery/2.1.4/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Tailwind 现代化交互的折叠控制
        $('.faq-question').click(function() {
            const $item = $(this).parent('.faq-item');
            const $answer = $(this).next('.faq-answer');
            const $icon = $(this).find('.fa-chevron-down');
            
            // 切换答案显示隐藏
            $answer.slideToggle(250);
            
            // 切换箭头方向
            $icon.toggleClass('rotate-180');
            
            // 切换容器高亮边框和阴影
            $item.toggleClass('border-brand-300 ring-1 ring-brand-100 shadow-md');
        });
    });
</script>

</body>
</html>
