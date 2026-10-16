<?php
/**
 * 后台「系统设置」各模块的表单定义（配置驱动）
 *
 * 背景：离线化改造重写 admin/set.php 时只保留了 site / chat / cron / rewrite / cloneset
 * 五个模块，后台菜单里其余 20 多个 set.php?mod=xxx 入口全部落到「跳转 set_site.php」，
 * 导致支付配置、模板设置、邮件、快捷登录、分站等设置项全部无法维护。
 *
 * 这里用一份配置数组描述每个模块有哪些配置项，由 set.php 统一渲染表单与保存，
 * 避免为 20 多个模块各写一个页面。
 *
 * 字段定义：
 *   k        配置键（存 pre_config 表）
 *   name     表单标签
 *   type     text | textarea | number | password | switch | select
 *   options  select 专有：['值' => '显示名']
 *   note     输入框下方说明
 *   ph       placeholder
 */
return [
    // ---------------------------------------------------------------- 支付
    'pay' => [
        'title' => '支付接口配置',
        'icon'  => 'fa-credit-card',
        'desc'  => '选择各支付通道使用的接口，并填写对应易支付（epay）参数。接口编号：2=易支付1，8=易支付2，9=易支付3（仅微信），10=易支付4。',
        'fields' => [
            ['k' => 'alipay_api', 'name' => '支付宝接口', 'type' => 'select', 'options' => ['0' => '关闭', '2' => '易支付接口1', '8' => '易支付接口2', '10' => '易支付接口4']],
            ['k' => 'qqpay_api', 'name' => 'QQ钱包接口', 'type' => 'select', 'options' => ['0' => '关闭', '2' => '易支付接口1', '8' => '易支付接口2', '9' => '易支付接口3', '10' => '易支付接口4']],
            ['k' => 'wxpay_api', 'name' => '微信支付接口', 'type' => 'select', 'options' => ['0' => '关闭', '2' => '易支付接口1', '8' => '易支付接口2', '9' => '易支付接口3', '10' => '易支付接口4']],
            ['k' => 'recharge_api', 'name' => '余额充值接口', 'type' => 'select', 'options' => ['0' => '关闭', '2' => '易支付接口1', '8' => '易支付接口2', '10' => '易支付接口4']],

            ['k' => 'epay_url', 'name' => '易支付接口1 - 接口地址', 'type' => 'text', 'ph' => 'https://pay.xxx.com/', 'note' => '末尾需带斜杠'],
            ['k' => 'epay_pid', 'name' => '易支付接口1 - 商户ID', 'type' => 'text'],
            ['k' => 'epay_key', 'name' => '易支付接口1 - 商户密钥', 'type' => 'text'],

            ['k' => 'epay_url2', 'name' => '易支付接口2 - 接口地址', 'type' => 'text', 'ph' => 'https://pay.xxx.com/'],
            ['k' => 'epay_pid2', 'name' => '易支付接口2 - 商户ID', 'type' => 'text'],
            ['k' => 'epay_key2', 'name' => '易支付接口2 - 商户密钥', 'type' => 'text'],

            ['k' => 'epay_url3', 'name' => '易支付接口3 - 接口地址', 'type' => 'text', 'ph' => 'https://pay.xxx.com/'],
            ['k' => 'epay_pid3', 'name' => '易支付接口3 - 商户ID', 'type' => 'text'],
            ['k' => 'epay_key3', 'name' => '易支付接口3 - 商户密钥', 'type' => 'text'],

            ['k' => 'epay_url4', 'name' => '易支付接口4 - 接口地址', 'type' => 'text', 'ph' => 'https://pay.xxx.com/'],
            ['k' => 'epay_pid4', 'name' => '易支付接口4 - 商户ID', 'type' => 'text'],
            ['k' => 'epay_key4', 'name' => '易支付接口4 - 商户密钥', 'type' => 'text'],

            ['k' => 'recharge_alipay_api', 'name' => '充值专用-支付宝接口', 'type' => 'select', 'options' => ['0' => '跟随通用配置', '2' => '易支付接口1', '8' => '易支付接口2']],
            ['k' => 'recharge_qqpay_api', 'name' => '充值专用-QQ钱包接口', 'type' => 'select', 'options' => ['0' => '跟随通用配置', '2' => '易支付接口1', '8' => '易支付接口2']],
            ['k' => 'recharge_wxpay_api', 'name' => '充值专用-微信接口', 'type' => 'select', 'options' => ['0' => '跟随通用配置', '2' => '易支付接口1', '8' => '易支付接口2']],
            ['k' => 'recharge_epay_url', 'name' => '充值专用易支付 - 接口地址', 'type' => 'text', 'note' => '留空则回退到通用易支付配置'],
            ['k' => 'recharge_epay_pid', 'name' => '充值专用易支付 - 商户ID', 'type' => 'text'],
            ['k' => 'recharge_epay_key', 'name' => '充值专用易支付 - 商户密钥', 'type' => 'text'],
        ],
    ],

    // ---------------------------------------------------------------- 模板
    'template' => [
        'title' => '首页模板设置',
        'icon'  => 'fa-desktop',
        'desc'  => '选择前台使用的模板（读取 template/ 目录），模板自身的参数在该模板的 config.php 中配置。',
        'fields' => [
            ['k' => 'template', 'name' => '前台模板', 'type' => 'select', 'options' => '__TEMPLATE_LIST__'],
            ['k' => 'style', 'name' => '配色风格', 'type' => 'text', 'ph' => '留空使用模板默认'],
            ['k' => 'gg_search', 'name' => '首页搜索框', 'type' => 'switch'],
            ['k' => 'shopdesc_editor', 'name' => '商品详情富文本编辑', 'type' => 'switch'],
        ],
    ],

    // ---------------------------------------------------------------- 邮件
    'mail' => [
        'title' => '邮箱与提醒配置',
        'icon'  => 'fa-envelope-o',
        'desc'  => '配置发信 SMTP，用于发卡邮件、找回密码等场景。',
        'fields' => [
            ['k' => 'mail_smtp', 'name' => 'SMTP 服务器', 'type' => 'text', 'ph' => 'smtp.qq.com'],
            ['k' => 'mail_port', 'name' => 'SMTP 端口', 'type' => 'number', 'ph' => '465 或 25'],
            ['k' => 'mail_user', 'name' => '发信邮箱账号', 'type' => 'text'],
            ['k' => 'mail_pwd', 'name' => '发信邮箱密码/授权码', 'type' => 'password'],
            ['k' => 'mail_name', 'name' => '发件人显示名称', 'type' => 'text'],
        ],
    ],
    'mailcon' => [
        'title' => '发信模板',
        'icon'  => 'fa-file-text-o',
        'desc'  => '自动发卡时发送给用户的邮件正文模板，可用变量：[name] [date] [kmdata] [alert] [domain]',
        'fields' => [
            ['k' => 'faka_mail', 'name' => '发卡邮件模板', 'type' => 'textarea', 'rows' => 10],
        ],
    ],

    // ---------------------------------------------------------------- 快捷登录
    'oauth' => [
        'title' => '快捷登录配置',
        'icon'  => 'fa-sign-in',
        'desc'  => '配置 QQ / 微信快捷登录。此处只维护开关与参数，绑定入口见「扫码登录」。',
        'fields' => [
            ['k' => 'login_qq', 'name' => 'QQ 快捷登录', 'type' => 'switch'],
            ['k' => 'login_wx', 'name' => '微信快捷登录', 'type' => 'switch'],
            ['k' => 'login_appid', 'name' => 'AppID', 'type' => 'text'],
            ['k' => 'login_appkey', 'name' => 'AppKey', 'type' => 'text'],
            ['k' => 'login_apiurl', 'name' => '接口地址', 'type' => 'text', 'ph' => 'https://'],
            ['k' => 'thirdlogin_open', 'name' => '开启第三方登录总开关', 'type' => 'switch'],
        ],
    ],

    // ---------------------------------------------------------------- 验证 / IP
    'captcha' => [
        'title' => '验证与IP配置',
        'icon'  => 'fa-shield',
        'desc'  => '控制各处验证码开关与同 IP 下单频次限制。',
        'fields' => [
            ['k' => 'captcha_open_free', 'name' => '免费下单验证码', 'type' => 'switch'],
            ['k' => 'captcha_open_reg', 'name' => '注册验证码', 'type' => 'switch'],
            ['k' => 'verify_open', 'name' => '开启验证中心', 'type' => 'switch'],
            ['k' => 'cishu', 'name' => '同一IP下单次数限制', 'type' => 'number', 'note' => '0 表示不限制'],
        ],
    ],

    // ---------------------------------------------------------------- 公告 / 推广
    'gonggao' => [
        'title' => '网站公告配置',
        'icon'  => 'fa-bullhorn',
        'desc'  => '首页公告内容，支持 HTML。',
        'fields' => [
            ['k' => 'anounce', 'name' => '首页公告', 'type' => 'textarea', 'rows' => 8],
        ],
    ],
    'invite' => [
        'title' => '推广链接设置',
        'icon'  => 'fa-share-alt',
        'desc'  => '推广/邀请相关文案与开关。',
        'fields' => [
            ['k' => 'invite_content', 'name' => '推广说明文案', 'type' => 'textarea', 'rows' => 6],
            ['k' => 'user_open', 'name' => '开启用户注册', 'type' => 'switch'],
        ],
    ],

    // ---------------------------------------------------------------- 分站
    'fenzhan' => [
        'title' => '分站相关配置',
        'icon'  => 'fa-sitemap',
        'desc'  => '分站开通价格、到期、提现等相关参数。',
        'fields' => [
            ['k' => 'fenzhan_buy', 'name' => '开启分站开通', 'type' => 'switch'],
            ['k' => 'fenzhan_price', 'name' => '分站开通价格（普通版）', 'type' => 'number'],
            ['k' => 'fenzhan_price2', 'name' => '分站开通价格（专业版）', 'type' => 'number'],
            ['k' => 'fenzhan_free', 'name' => '首次开通赠送余额', 'type' => 'number'],
            ['k' => 'fenzhan_pricelimit', 'name' => '分站最低售价限制', 'type' => 'number', 'note' => '0 表示不限制'],
            ['k' => 'fenzhan_expiry', 'name' => '开启分站到期限制', 'type' => 'switch'],
            ['k' => 'fenzhan_edithtml', 'name' => '允许分站自定义HTML', 'type' => 'switch'],
            ['k' => 'fenzhan_kfqq', 'name' => '分站默认客服QQ', 'type' => 'text'],
            ['k' => 'fenzhan_rank', 'name' => '开启分站排行榜', 'type' => 'switch'],
            ['k' => 'fenzhan_tixian', 'name' => '开启分站提现', 'type' => 'switch'],
            ['k' => 'fenzhan_tixian_alipay', 'name' => '提现方式-支付宝', 'type' => 'switch'],
            ['k' => 'fenzhan_tixian_wx', 'name' => '提现方式-微信', 'type' => 'switch'],
            ['k' => 'fenzhan_tixian_qq', 'name' => '提现方式-QQ钱包', 'type' => 'switch'],
        ],
    ],

    // ---------------------------------------------------------------- 图片 / 背景
    'upimg' => [
        'title' => 'Logo与背景设置',
        'icon'  => 'fa-picture-o',
        'desc'  => '站点 Logo 与首页背景图（填写图片地址，可先用「商品图片上传」得到地址）。',
        'fields' => [
            ['k' => 'sitelogo', 'name' => '站点 Logo 地址', 'type' => 'text', 'ph' => 'assets/img/logo.png'],
            // 取值含义见 includes/lib/Template.php：0=平铺 1=横向重复 2=纵向重复 3=不重复铺满
            ['k' => 'ui_background', 'name' => '背景平铺方式', 'type' => 'select', 'options' => ['0' => '平铺重复', '1' => '横向重复', '2' => '纵向重复', '3' => '不重复铺满']],
            ['k' => 'ui_backgroundurl', 'name' => '背景图片地址', 'type' => 'text', 'note' => '留空则使用内置背景'],
        ],
    ],

    // ---------------------------------------------------------------- 签到
    'qiandao' => [
        'title' => '每日签到设置',
        'icon'  => 'fa-calendar-check-o',
        'desc'  => '用户每日签到奖励规则。',
        'fields' => [
            ['k' => 'qiandao_reward', 'name' => '每次签到奖励金额', 'type' => 'number', 'note' => '0 表示关闭签到'],
            ['k' => 'qiandao_day', 'name' => '连续签到天数上限', 'type' => 'number'],
            ['k' => 'qiandao_mult', 'name' => '连续签到奖励倍数', 'type' => 'number', 'note' => '0 表示不按连续天数加成'],
            ['k' => 'qiandao_limitip', 'name' => '限制同一IP只能签到一次', 'type' => 'switch'],
            ['k' => 'qiandao_virtual', 'name' => '开启虚拟签到人数', 'type' => 'switch'],
        ],
    ],

    // ---------------------------------------------------------------- 防红
    'dwz' => [
        'title' => '防红接口设置',
        'icon'  => 'fa-link',
        'desc'  => '配置短链/防红跳转接口，用于生成推广链接。',
        'fields' => [
            ['k' => 'dwz_open', 'name' => '开启短链防红', 'type' => 'switch'],
            ['k' => 'dwz_api', 'name' => '接口地址', 'type' => 'text', 'ph' => 'https://'],
            ['k' => 'dwz_key', 'name' => '接口密钥', 'type' => 'text'],
        ],
    ],

    // ---------------------------------------------------------------- 充值返利
    'rebaterecharge' => [
        'title' => '充值返利设置',
        'icon'  => 'fa-gift',
        'desc'  => '用户充值时按比例额外赠送余额。',
        'fields' => [
            ['k' => 'recharge_rebate_open', 'name' => '开启充值返利', 'type' => 'switch'],
            ['k' => 'recharge_rebate_rate', 'name' => '返利比例（%）', 'type' => 'number', 'note' => '填 5 表示充值 100 元多送 5 元'],
            ['k' => 'recharge_rebate_min', 'name' => '起返金额', 'type' => 'number', 'note' => '充值金额达到该值才返利'],
        ],
    ],

    // ---------------------------------------------------------------- 供货商 / 提现
    'sup' => [
        'title' => '供货商相关配置',
        'icon'  => 'fa-truck',
        'desc'  => '供货商保证金与提现规则。',
        'fields' => [
            ['k' => 'sup_bond', 'name' => '供货商保证金', 'type' => 'number'],
            ['k' => 'tixian_min', 'name' => '最低提现金额', 'type' => 'number'],
            ['k' => 'tixian_rate', 'name' => '提现手续费比例（%）', 'type' => 'number'],
            ['k' => 'tixian_limit', 'name' => '每日提现次数限制', 'type' => 'number', 'note' => '0 表示不限制'],
        ],
    ],

    // ---------------------------------------------------------------- 工单
    'workorder' => [
        'title' => '工单设置',
        'icon'  => 'fa-ticket',
        'desc'  => '工单系统开关与类型。',
        'fields' => [
            ['k' => 'workorder_open', 'name' => '开启工单系统', 'type' => 'switch'],
            ['k' => 'workorder_type', 'name' => '工单类型', 'type' => 'text', 'note' => '多个类型用英文逗号分隔'],
        ],
    ],

    // ---------------------------------------------------------------- 美化
    'beautify' => [
        'title' => '前台美化',
        'icon'  => 'fa-magic',
        'desc'  => '字体、背景、鼠标特效、樱花飘落等前台视觉效果总开关。',
        'fields' => [
            ['k' => 'font_beautify', 'name' => '字体美化', 'type' => 'switch'],
            ['k' => 'font_family', 'name' => '自定义字体', 'type' => 'text', 'ph' => '如：Microsoft YaHei'],
            ['k' => 'background_enable', 'name' => '背景美化', 'type' => 'switch'],
            ['k' => 'mouse_beautify', 'name' => '鼠标特效', 'type' => 'switch'],
            ['k' => 'mouse_style', 'name' => '鼠标特效样式', 'type' => 'select', 'options' => ['1' => '样式一', '2' => '样式二', '3' => '样式三']],
            ['k' => 'sakura_enable', 'name' => '樱花飘落特效', 'type' => 'switch'],
            ['k' => 'sakura_num', 'name' => '樱花数量', 'type' => 'number'],
            ['k' => 'sakura_limit_times', 'name' => '樱花显示次数限制', 'type' => 'number', 'note' => '0 表示不限'],
        ],
    ],
    'beautify_admin' => [
        'title' => '鼠标美化',
        'icon'  => 'fa-mouse-pointer',
        'desc'  => '后台鼠标指针特效。',
        'fields' => [
            ['k' => 'mouse_beautify', 'name' => '开启鼠标特效', 'type' => 'switch'],
            ['k' => 'mouse_style', 'name' => '鼠标特效样式', 'type' => 'select', 'options' => ['1' => '样式一', '2' => '样式二', '3' => '样式三']],
        ],
    ],
    'beautify_sidebar' => [
        'title' => '后台侧边栏框架美化',
        'icon'  => 'fa-columns',
        'desc'  => '后台侧边栏样式。',
        'fields' => [
            ['k' => 'sidebar_theme', 'name' => '侧边栏风格', 'type' => 'select', 'options' => ['0' => '默认（浅色）', '1' => '深色', '2' => '品牌色渐变']],
            ['k' => 'sidebar_compact', 'name' => '紧凑模式', 'type' => 'switch'],
        ],
    ],
    'beautify_font' => [
        'title' => '字体美化',
        'icon'  => 'fa-font',
        'desc'  => '站点字体设置。',
        'fields' => [
            ['k' => 'font_beautify', 'name' => '开启字体美化', 'type' => 'switch'],
            ['k' => 'font_family', 'name' => '字体名称', 'type' => 'text', 'ph' => 'Microsoft YaHei'],
        ],
    ],
    'beautify_background' => [
        'title' => '背景美化',
        'icon'  => 'fa-image',
        'desc'  => '站点背景图片设置。',
        'fields' => [
            ['k' => 'background_enable', 'name' => '开启背景美化', 'type' => 'switch'],
            ['k' => 'ui_background', 'name' => '背景平铺方式', 'type' => 'select', 'options' => ['0' => '平铺重复', '1' => '横向重复', '2' => '纵向重复', '3' => '不重复铺满']],
            ['k' => 'ui_backgroundurl', 'name' => '背景图片地址', 'type' => 'text'],
        ],
    ],
];
