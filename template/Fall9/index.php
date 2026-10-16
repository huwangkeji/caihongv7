<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <title><?php echo $hometitle?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    
    <!-- 境内CDN引入 Bulma CSS 框架 & FontAwesome -->
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/bulma/0.9.4/css/bulma.min.css" rel="stylesheet"/>
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/modernizr/2.8.3/modernizr.min.js"></script>

    <style>
        /* 针对 Bulma 的原创高级现代化UI风格 */
        :root {
            --bulma-primary: #485fc7;
            --bulma-gradient: linear-gradient(135deg, #485fc7 0%, #7b61ff 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            --border-radius: 1.25rem;
        }

        body {
            background-color: #f4f6f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            min-height: 100vh;
        }

        /* 动态背景图支持 */
        .page-background {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            filter: blur(10px) brightness(0.85);
            transform: scale(1.1);
        }

        /* 核心容器 */
        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .glass-box {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: var(--glass-shadow);
            border: 1px solid rgba(255, 255, 255, 0.6);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        /* 头部卡片 */
        .hero-profile {
            padding: 2.5rem 1.5rem;
            text-align: center;
            position: relative;
        }
        .hero-profile::before {
            content: '';
            position: absolute;
            top: 0; right: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at top right, rgba(72, 95, 199, 0.1), transparent 60%);
            pointer-events: none;
        }
        .avatar-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 8px 20px rgba(72, 95, 199, 0.2);
            transition: transform 0.3s;
        }
        .avatar-img:hover {
            transform: scale(1.05);
        }

        /* Bulma Tabs 美化 */
        .tabs.is-toggle li.is-active a {
            background: var(--bulma-gradient);
            border-color: transparent;
            box-shadow: 0 4px 15px rgba(72, 95, 199, 0.3);
            color: #fff;
        }
        .tabs.is-toggle li a {
            border-radius: 0.75rem;
            border: none;
            margin: 0 0.25rem;
            font-weight: 600;
            color: #606c76;
            transition: all 0.3s;
        }
        .tabs.is-toggle li a:hover {
            background-color: rgba(72, 95, 199, 0.05);
            color: var(--bulma-primary);
        }

        /* 悬浮动效 */
        .hover-lift {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .tab-pane { display: none; animation: fadeIn 0.4s ease; }
        .tab-pane.is-active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* ==============================================================
           针对 shop.inc.php 的 Bootstrap -> Bulma 样式映射兼容层
           确保底层逻辑和UI不受损，完美接管
           ============================================================== */
        .shop-container .form-group {
            margin-bottom: 1.25rem;
        }
        .shop-container .input-group {
            display: flex;
            width: 100%;
            align-items: stretch;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            border-radius: 0.5rem;
        }
        .shop-container .input-group-addon {
            background-color: #f5f5f5;
            border: 1px solid #dbdbdb;
            border-right: none;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            color: #363636;
            font-weight: 500;
            white-space: nowrap;
        }
        .shop-container .form-control {
            flex: 1;
            width: 1%;
            border: 1px solid #dbdbdb;
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .shop-container .form-control:focus {
            border-color: var(--bulma-primary);
        }
        .shop-container .input-group-btn { display: flex; }
        .shop-container .input-group-btn .btn {
            border-radius: 0;
            padding: 0.75rem 1rem;
            border: 1px solid #dbdbdb;
            background: #f5f5f5;
            cursor: pointer;
        }
        .shop-container .input-group-btn:first-child .btn {
            border-top-left-radius: 0.5rem; border-bottom-left-radius: 0.5rem; border-right: none;
        }
        .shop-container .input-group-btn:last-child .btn {
            border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-left: none;
        }
        .shop-container .input-group-btn + .form-control { border-radius: 0; }
        
        .shop-container .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .shop-container .btn-info { background-color: #3e8ed0; color: #fff; }
        .shop-container .btn-danger { background-color: #f14668; color: #fff; }
        .shop-container .btn-default { background-color: #f5f5f5; color: #363636; }
        
        .shop-container .btn-block { display: block; width: 100%; }
        .shop-container .btn-group-justified { display: flex; width: 100%; gap: 10px; }
        .shop-container .btn-group-justified > * { flex: 1 1 auto; width: 100%; }
        
        .shop-container .btn-rounded {
            border-radius: 50rem !important;
            background: linear-gradient(135deg, #f14668 0%, #ff8a9f 100%) !important;
            box-shadow: 0 4px 15px rgba(241, 70, 104, 0.3);
            font-size: 1.1rem;
            letter-spacing: 2px;
        }
        .shop-container .btn-rounded:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(241, 70, 104, 0.4);
        }

        .shop-container .alert-danger {
            background-color: #feecf0;
            color: #cc0f35;
            padding: 1.25rem;
            border-radius: 0.5rem;
            border-left: 5px solid #f14668 !important;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .shop-container .alert-success {
            background-color: #effaf3;
            color: #257942;
            padding: 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        <?php echo $background_css?>
    </style>
</head>
<body>

<?php if($background_image){?>
<div class="page-background" style="background-image: url('<?php echo $background_image;?>');"></div>
<?php }?>

<div class="main-container">
    
    <!-- 头部模块 -->
    <div class="glass-box hero-profile">
        <a href="javascript:void(0)" class="is-inline-block mb-3">
            <img src="/assets/img/user.png" alt="Avatar" class="avatar-img">
        </a>
        <h2 class="title is-4 has-text-weight-bold mb-4">
            <a class="has-text-dark" href="javascript:void(alert('<?php echo $conf['sitename']?>，建议收藏到浏览器书签哦！'));">
                <?php echo $conf['sitename']?>
            </a>
        </h2>
        
        <div class="buttons is-centered mt-2">
            <button class="button is-light is-rounded hover-lift" data-toggle="modal" data-target="#anounce">
                <span class="icon has-text-warning"><i class="fa fa-bullhorn"></i></span>
                <span>最新公告</span>
            </button>
            <?php if($conf['appurl']){?>
                <a href="<?php echo $conf['appurl']; ?>" target="_blank" class="button is-primary is-rounded hover-lift">
                    <span class="icon"><i class="fa fa-android"></i></span>
                    <span>客户端下载</span>
                </a>
            <?php }else{?>
                <button class="button is-primary is-rounded hover-lift" data-toggle="modal" data-target="#lxkf">
                    <span class="icon"><i class="fa fa-qq"></i></span>
                    <span>联系客服</span>
                </button>
            <?php }?>
            <a href="user/login.php" class="button is-dark is-rounded hover-lift">
                <span class="icon"><i class="fa fa-user-circle"></i></span>
                <span>用户登录</span>
            </a>
        </div>
    </div>

    <!-- 导航模块 -->
    <div class="glass-box p-3">
        <div class="tabs is-toggle is-fullwidth is-small m-0" id="mainTabs">
            <ul>
                <li class="is-active"><a href="#shop" data-toggle="tab"><span class="icon"><i class="fa fa-shopping-bag"></i></span><span>下单</span></a></li>
                <li><a href="#search" data-toggle="tab"><span class="icon"><i class="fa fa-search"></i></span><span>查询</span></a></li>
                <?php if($conf['fenzhan_buy']==1){?>
                <li><a href="#Substation" data-toggle="tab"><span class="icon has-text-danger"><i class="fa fa-paper-plane"></i></span><span class="has-text-danger">分站</span></a></li>
                <?php }?>
                <?php if($conf['gift_open']==1&&$conf['fenzhan_buy']==0){?>
                <li><a href="#gift" data-toggle="tab"><span class="icon has-text-success"><i class="fa fa-gift"></i></span><span>抽奖</span></a></li>
                <?php }?>
                <?php if($conf['fenzhan_buy']==0&&$conf['gift_open']==0){?>
                <li><a onclick="window.location.href='./user/'"><span class="icon"><i class="fa fa-sign-in"></i></span><span>登录</span></a></li>
                <?php }?>
                <li><a href="#more" data-toggle="tab"><span class="icon"><i class="fa fa-th-large"></i></span><span>更多</span></a></li>
            </ul>
        </div>
    </div>

    <!-- 内容区域 -->
    <div class="tab-content">
        
        <!-- 在线下单 -->
        <div class="tab-pane is-active" id="shop">
            <div class="glass-box p-5">
                <div class="notification is-danger is-light has-text-centered p-3 mb-5" style="border-radius: 0.75rem;">
                    <span class="icon"><i class="fa fa-volume-up"></i></span>
                    <b>［<?=date("m月d号")?>］最新业务通知</b> 
                    <a href="#anounce" data-toggle="modal" data-target="#anounce" class="button is-danger is-small is-rounded ml-2">点击查看</a>
                </div>
                <!-- 核心业务隔离层，接管 shop.inc.php -->
                <div class="shop-container">
                    <?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
                </div>
            </div>
        </div>

        <!-- 查询订单 -->
        <div class="tab-pane" id="search">
            <div class="glass-box p-5">
                <div class="box has-background-light is-shadowless border p-4 mb-5 is-flex is-align-items-center" style="border-radius:1rem;">
                    <figure class="image is-64x64 mr-4">
                        <img class="is-rounded" src="/assets/img/user.png" alt="avatar">
                    </figure>
                    <div class="content mb-0 flex-1" style="flex:1;">
                        <h5 class="mb-1 has-text-weight-bold">售后服务</h5>
                        <p class="is-size-7 has-text-grey mb-0"><i class="fa fa-qq has-text-info"></i> <?php echo $conf['kfqq'] ?> <br> 售后订单问题请联系客服</p>
                    </div>
                    <button class="button is-info is-rounded" data-toggle="modal" data-target="#lxkf">联系客服</button>
                </div>
                
                <div class="notification is-light mb-5" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }?>>
                    <?php echo $conf['gg_search']?>
                </div>

                <div class="field has-addons mb-4">
                    <p class="control">
                        <span class="select is-medium">
                            <select id="searchtype" class="has-background-light border-0">
                                <option value="0">下单账号</option>
                                <option value="1">订单号</option>
                            </select>
                        </span>
                    </p>
                    <p class="control is-expanded">
                        <input class="input is-medium has-background-light border-0" type="text" name="qq" id="qq3" placeholder="请输入凭证(留空查最新)" onkeydown="if(event.keyCode==13){$('#submit_query').click()}">
                    </p>
                </div>
                
                <button class="button is-primary is-medium is-fullwidth is-rounded mb-5" id="submit_query" style="background: var(--bulma-gradient);">
                    <span class="icon"><i class="fa fa-search"></i></span>
                    <span>立即查询</span>
                </button>
                
                <div id="result2" style="display:none;">
                    <div class="has-text-centered mb-2"><span class="tag is-danger is-light is-rounded">手机用户可左右滑动表格</span></div>
                    <div class="table-container box is-shadowless p-0 has-background-white" style="border: 1px solid #eee;">
                        <table class="table is-fullwidth is-hoverable is-striped has-text-centered mb-0">
                            <thead><tr><th class="has-text-centered">下单账号</th><th class="has-text-centered">商品名称</th><th class="has-text-centered">数量</th><th class="is-hidden-mobile has-text-centered">购买时间</th><th class="has-text-centered">状态</th><th class="has-text-centered">操作</th></tr></thead>
                            <tbody id="list"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 开通分站 -->
        <?php if($conf['fenzhan_buy']==1){?>
        <div class="tab-pane" id="Substation">
            <div class="glass-box overflow-hidden">
                <div class="hero is-dark is-bold has-text-centered py-6" style="background: linear-gradient(135deg, #2c3e50, #3498db);">
                    <div class="hero-body p-0">
                        <h2 class="title is-3 mb-4"><i class="fa fa-paper-plane mr-2"></i>分站加盟计划</h2>
                        <div class="tags is-centered are-medium">
                            <span class="tag is-info is-light"><i class="fa fa-user-o mr-1"></i>入门级 ￥<?php echo $conf['fenzhan_price']?></span>
                            <span class="tag is-warning is-light"><i class="fa fa-diamond mr-1"></i>旗舰级 ￥<?php echo $conf['fenzhan_price2']?></span>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <div class="columns is-multiline is-mobile has-text-centered mb-5">
                        <div class="column is-6">
                            <div class="box is-shadowless has-background-light h-100 hover-lift">
                                <i class="fa fa-globe fa-2x has-text-primary mb-2"></i>
                                <h6 class="has-text-weight-bold">独立网站</h6>
                                <p class="is-size-7 has-text-grey">一模一样独立门面</p>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="box is-shadowless has-background-light h-100 hover-lift">
                                <i class="fa fa-cogs fa-2x has-text-success mb-2"></i>
                                <h6 class="has-text-weight-bold">专属后台</h6>
                                <p class="is-size-7 has-text-grey">站长专享控制面板</p>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="box is-shadowless has-background-light h-100 hover-lift">
                                <i class="fa fa-money fa-2x has-text-warning mb-2"></i>
                                <h6 class="has-text-weight-bold">超低密价</h6>
                                <p class="is-size-7 has-text-grey">利润空间拉到最大</p>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="box is-shadowless has-background-light h-100 hover-lift">
                                <i class="fa fa-users fa-2x has-text-danger mb-2"></i>
                                <h6 class="has-text-weight-bold">发展下级</h6>
                                <p class="is-size-7 has-text-grey">旗舰版享下级提成</p>
                            </div>
                        </div>
                    </div>
                    <div class="notification is-success is-light has-text-centered mb-5">
                        <i class="fa fa-info-circle mr-1"></i> 余额提成满 <b><?php echo $conf['tixian_min']; ?></b> 元即可申请提现
                    </div>
                    <div class="buttons is-centered">
                        <button class="button is-light is-rounded px-5" data-toggle="modal" data-target="#userjs">版本对比</button>
                        <a href="user/regsite.php" target="_blank" class="button is-danger is-rounded px-5">马上开通</a>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <!-- 抽奖 -->
        <?php if($conf['gift_open']==1){?>
        <div class="tab-pane" id="gift">
            <div class="glass-box p-6 has-text-centered position-relative">
                <i class="fa fa-gift fa-4x has-text-warning mb-4" style="filter: drop-shadow(0 4px 6px rgba(255,200,0,0.4));"></i>
                <h3 id="roll" class="title is-4 mb-5">点击下方按钮开始抽奖</h3>
                
                <div class="buttons is-centered mb-5">
                    <button class="button is-success is-large is-rounded px-6 hover-lift" id="start">开始抽奖</button>
                    <button class="button is-danger is-large is-rounded px-6 hover-lift" id="stop" style="display:none;">停止</button>
                </div>
                
                <div id="result" class="title is-5 has-text-primary mb-5" style="min-height:30px;"></div>
                
                <div class="giftlist box is-shadowless has-background-light p-4 mx-auto text-left" style="display:none; max-width: 400px; border-radius: 1rem;">
                    <h6 class="has-text-weight-bold mb-3"><i class="fa fa-history mr-2"></i>最近中奖记录</h6>
                    <ul id="pst_1" class="is-size-7 has-text-grey"></ul>
                </div>
            </div>
        </div>
        <?php }?>

        <!-- 更多模块 -->
        <div class="tab-pane" id="more">
            <div class="columns is-multiline is-mobile">
                <?php
                $features = [];
                if($conf['gift_open']==1) $features[] = ['href'=>'#gift', 'attr'=>'data-toggle="tab"', 'icon'=>'fa-gift', 'title'=>'福利抽奖', 'desc'=>'在线领免费商品', 'color'=>'has-text-warning', 'bg'=>'has-background-warning-light'];
                if(!empty($conf['appurl']) && $conf['gift_open']==0) $features[] = ['href'=>$conf['appurl'], 'attr'=>'target="_blank"', 'icon'=>'fa-cloud-download', 'title'=>'APP下载', 'desc'=>'下单更方便快捷', 'color'=>'has-text-info', 'bg'=>'has-background-info-light'];
                if(!empty($conf['invite_tid'])) $features[] = ['href'=>'./?mod=invite', 'attr'=>'target="_blank"', 'icon'=>'fa-paper-plane', 'title'=>'推广领赞', 'desc'=>'免费获取名片赞', 'color'=>'has-text-success', 'bg'=>'has-background-success-light'];
                if(!empty($conf['cutshop_open'])) $features[] = ['href'=>'./?mod=cutshop', 'attr'=>'target="_blank"', 'icon'=>'fa-handshake-o', 'title'=>'砍价商城', 'desc'=>'邀请好友免费拿', 'color'=>'has-text-danger', 'bg'=>'has-background-danger-light'];
                if(!empty($conf['groupshop_open'])) $features[] = ['href'=>'./?mod=groupshop', 'attr'=>'target="_blank"', 'icon'=>'fa-users', 'title'=>'团购特惠', 'desc'=>'一起组团省更多', 'color'=>'has-text-primary', 'bg'=>'has-background-primary-light'];
                if(!empty($conf['seckill_open'])) $features[] = ['href'=>'./?mod=seckill', 'attr'=>'target="_blank"', 'icon'=>'fa-bolt', 'title'=>'限时秒杀', 'desc'=>'低价秒杀好物', 'color'=>'has-text-link', 'bg'=>'has-background-link-light'];
                if(!empty($conf['package_open'])) $features[] = ['href'=>'./?mod=package', 'attr'=>'target="_blank"', 'icon'=>'fa-archive', 'title'=>'套餐优惠', 'desc'=>'打包下单更划算', 'color'=>'has-text-info', 'bg'=>'has-background-info-light'];
                if(!empty($conf['coupon_open'])) $features[] = ['href'=>'./?mod=coupon', 'attr'=>'target="_blank"', 'icon'=>'fa-ticket', 'title'=>'领券中心', 'desc'=>'各种满减优惠券', 'color'=>'has-text-warning', 'bg'=>'has-background-warning-light'];
                if(!empty($conf['daiguaurl'])) $features[] = ['href'=>'./?mod=daigua', 'attr'=>'', 'icon'=>'fa-rocket', 'title'=>'等级代挂', 'desc'=>'全自动QQ代挂', 'color'=>'has-text-success', 'bg'=>'has-background-success-light'];
                $features[] = ['href'=>'./user/', 'attr'=>'target="_blank"', 'icon'=>'fa-cogs', 'title'=>'站长后台', 'desc'=>'分站管理控制台', 'color'=>'has-text-grey-dark', 'bg'=>'has-background-light'];

                foreach($features as $f){
                ?>
                <div class="column is-6-mobile is-4-tablet">
                    <a href="<?php echo $f['href']?>" <?php echo $f['attr']?> class="box is-shadowless <?php echo $f['bg']?> h-100 hover-lift has-text-centered p-4" style="border-radius: 1rem; display: block;">
                        <i class="fa <?php echo $f['icon']?> fa-2x <?php echo $f['color']?> mb-2"></i>
                        <h6 class="has-text-weight-bold has-text-dark mb-1 is-size-6"><?php echo $f['title']?></h6>
                        <p class="is-size-7 has-text-grey"><?php echo $f['desc']?></p>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>
    </div> <!-- 结束 Tab-content -->

    <!-- 文章列表 -->
    <?php if($conf['articlenum']>0){
    $limit = intval($conf['articlenum']);
    $rs=$DB->query("SELECT id,title FROM pre_article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
    $msgrow=array();
    while($res = $rs->fetch()){ $msgrow[]=$res; }
    $i=0;
    ?>
    <div class="glass-box mt-5">
        <div class="px-5 py-4 border-bottom is-flex is-justify-content-space-between is-align-items-center" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
            <h6 class="title is-6 mb-0"><i class="fa fa-newspaper-o has-text-primary mr-2"></i>最新资讯</h6>
            <a href="<?php echo article_url()?>" target="_blank" class="is-size-7 has-text-grey">查看全部 <i class="fa fa-angle-right"></i></a>
        </div>
        <div>
            <?php foreach($msgrow as $row){ $i++; $color = $i<=3 ? 'is-danger' : 'is-light'; ?>
            <a href="<?php echo article_url($row['id'])?>" target="_blank" class="is-flex is-align-items-center px-5 py-3 hover-lift has-text-dark" style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                <span class="tag is-rounded <?php echo $color?> mr-3"><?php echo $i?></span>
                <span class="is-flex-grow-1 is-clipped" style="white-space:nowrap; text-overflow:ellipsis;"><?php echo $row['title']?></span>
                <i class="fa fa-angle-right has-text-grey-light ml-2"></i>
            </a>
            <?php }?>
        </div>
    </div>
    <?php }?>

    <!-- 数据统计 -->
    <?php if(!$conf['hide_tongji']){?>
    <div class="glass-box mt-5 p-5">
        <h6 class="title is-6 mb-4"><i class="fa fa-bar-chart has-text-primary mr-2"></i>平台运营数据</h6>
        <div class="columns is-mobile is-multiline has-text-centered">
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-shield fa-2x has-text-success mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_yxts">0</p>
                    <p class="is-size-7 has-text-grey">安全运营(天)</p>
                </div>
            </div>
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-shopping-bag fa-2x has-text-info mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_money">0</p>
                    <p class="is-size-7 has-text-grey">累计交易(元)</p>
                </div>
            </div>
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-check-circle fa-2x has-text-primary mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_orders">0</p>
                    <p class="is-size-7 has-text-grey">订单总数(笔)</p>
                </div>
            </div>
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-sitemap fa-2x has-text-warning mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_site">0</p>
                    <p class="is-size-7 has-text-grey">加盟代理(个)</p>
                </div>
            </div>
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-line-chart fa-2x has-text-danger mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_money1">0</p>
                    <p class="is-size-7 has-text-grey">今日交易(元)</p>
                </div>
            </div>
            <div class="column is-4">
                <div class="box is-shadowless has-background-light p-3" style="border-radius: 1rem;">
                    <i class="fa fa-list-alt fa-2x has-text-grey mb-2 opacity-75"></i>
                    <p class="title is-5 mb-1" id="count_orders2">0</p>
                    <p class="is-size-7 has-text-grey">今日订单(笔)</p>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <!-- 底部 -->
    <footer class="mt-6 mb-4 has-text-centered">
        <div class="glass-box p-4">
            <h6 class="has-text-weight-bold has-text-dark mb-2">
                <?php echo $conf['sitename'] ?> <i class="fa fa-heart has-text-danger mx-1"></i> <?php echo date("Y")?>
            </h6>
            <p class="mb-2"><a href="./" class="has-text-primary has-text-weight-medium"><?php echo $_SERVER['HTTP_HOST']?></a></p>
            <div class="is-size-7 has-text-grey"><?php echo $conf['footer']?></div>
        </div>
    </footer>

</div> <!-- main-container -->

<!-- Bulma Modals 适配层 -->
<!-- 1. 全局公告 (myModal) -->
<div class="modal" id="myModal">
    <div class="modal-background" data-dismiss="modal"></div>
    <div class="modal-card">
        <header class="modal-card-head" style="background: var(--bulma-gradient);">
            <p class="modal-card-title has-text-white has-text-weight-bold"><i class="fa fa-bullhorn mr-2"></i><?php echo $conf['sitename']?></p>
            <button class="delete" aria-label="close" data-dismiss="modal"></button>
        </header>
        <section class="modal-card-body content">
            <?php echo $conf['modal']?>
        </section>
        <footer class="modal-card-foot is-justify-content-flex-end">
            <button class="button is-primary is-rounded px-5" data-dismiss="modal">知道啦</button>
        </footer>
    </div>
</div>

<!-- 2. 最新公告 (anounce) -->
<div class="modal" id="anounce">
    <div class="modal-background" data-dismiss="modal"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-success">
            <p class="modal-card-title has-text-white has-text-centered has-text-weight-bold"><i class="fa fa-bell mr-2"></i>最新公告</p>
            <button class="delete" aria-label="close" data-dismiss="modal"></button>
        </header>
        <section class="modal-card-body content has-background-light">
            <div class="box is-shadowless">
                <?php echo $conf['anounce']?>
            </div>
        </section>
        <footer class="modal-card-foot is-justify-content-center">
            <button class="button is-success is-rounded px-6" data-dismiss="modal">我明白了</button>
        </footer>
    </div>
</div>

<!-- 3. 客服帮助 (lxkf) -->
<div class="modal" id="lxkf">
    <div class="modal-background" data-dismiss="modal"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-info">
            <p class="modal-card-title has-text-white has-text-centered has-text-weight-bold"><i class="fa fa-headphones mr-2"></i>客服与帮助</p>
            <button class="delete" aria-label="close" data-dismiss="modal"></button>
        </header>
        <section class="modal-card-body p-0">
            <!-- Bulma 风格的 Accordion -->
            <div class="box is-shadowless m-0" style="border-radius:0;">
                <article class="message is-small mb-2">
                    <div class="message-header"><p>为什么订单显示已完成了却一直没到账？</p></div>
                    <div class="message-body has-background-white">订单显示（已完成）证明已经提交到服务器内！<br>如果长时间没到账请联系客服处理！<br>订单长时间显示（待处理）请联系客服！</div>
                </article>
                <article class="message is-small mb-2">
                    <div class="message-header"><p>商品什么时候到账？</p></div>
                    <div class="message-body has-background-white">请参考商品简介里面，有关于到账时间的说明。</div>
                </article>
                <article class="message is-small mb-2">
                    <div class="message-header"><p>卡密没有发送我的邮箱？</p></div>
                    <div class="message-body has-background-white">没有收到请检查垃圾箱！也可以去查单区：输入自己下单时填写的邮箱进行查单。</div>
                </article>
                <article class="message is-small mb-0">
                    <div class="message-header"><p>已付款了没有查询到我订单？</p></div>
                    <div class="message-body has-background-white">联系客服处理，请提供（付款详细记录截图）（下单商品名称）（下单账号）。</div>
                </article>
            </div>
            
            <div class="p-4 has-background-light">
                <div class="box is-shadowless is-flex is-align-items-center p-3 mb-3">
                    <figure class="image is-48x48 mr-3">
                        <img class="is-rounded" src="/assets/img/user.png" alt="Avatar">
                    </figure>
                    <div class="flex-1" style="flex:1;">
                        <p class="has-text-weight-bold mb-0">官方售后客服</p>
                        <p class="is-size-7 has-text-grey">QQ: <?php echo $conf['kfqq'] ?></p>
                    </div>
                    <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" target="_blank" class="button is-info is-small is-rounded">联系</a>
                </div>
                <div class="notification is-warning is-light is-size-7 mb-0">
                    提问格式：<b>下单账号 + 业务名称 + 问题</b>。直奔主题，切勿发送抖动或语音。
                </div>
            </div>
        </section>
        <footer class="modal-card-foot is-justify-content-center">
            <button class="button is-rounded px-6" data-dismiss="modal">关闭</button>
        </footer>
    </div>
</div>

<!-- 4. 版本介绍 (userjs) -->
<div class="modal" id="userjs">
    <div class="modal-background" data-dismiss="modal"></div>
    <div class="modal-card">
        <header class="modal-card-head has-background-danger">
            <p class="modal-card-title has-text-white has-text-centered has-text-weight-bold"><i class="fa fa-sliders mr-2"></i>分站版本特权</p>
            <button class="delete" aria-label="close" data-dismiss="modal"></button>
        </header>
        <section class="modal-card-body p-0">
            <table class="table is-fullwidth is-striped has-text-centered mb-0">
                <thead>
                    <tr><th class="has-text-left pl-5">特权功能</th><th>入门版</th><th class="has-text-warning-dark">旗舰版</th></tr>
                </thead>
                <tbody>
                    <tr><td class="has-text-left pl-5">独立网站/专属后台</td><td><i class="fa fa-check has-text-success"></i></td><td><i class="fa fa-check has-text-success"></i></td></tr>
                    <tr><td class="has-text-left pl-5">低价拿货/调整价格</td><td><i class="fa fa-check has-text-success"></i></td><td><i class="fa fa-check has-text-success"></i></td></tr>
                    <tr><td class="has-text-left pl-5">搭建分站/管理分站</td><td><i class="fa fa-times has-text-danger opacity-50"></i></td><td><i class="fa fa-check has-text-success"></i></td></tr>
                    <tr><td class="has-text-left pl-5">超低密价/高额提成</td><td><i class="fa fa-times has-text-danger opacity-50"></i></td><td><i class="fa fa-check has-text-success"></i></td></tr>
                    <tr><td class="has-text-left pl-5">赠送专属APP</td><td><i class="fa fa-times has-text-danger opacity-50"></i></td><td><i class="fa fa-check has-text-success"></i></td></tr>
                </tbody>
            </table>
        </section>
        <footer class="modal-card-foot is-justify-content-center">
            <button class="button is-rounded px-6" data-dismiss="modal">关闭详情</button>
        </footer>
    </div>
</div>

<!-- 音乐代码 -->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>

<!-- 引入 JS (保留 jQuery 和 Bootstrap bundle 仅仅为了提供底层 JS API 给 app.js，并不引入 Bootstrap CSS 避免污染 Bulma) -->
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/clipboard.js/2.0.11/clipboard.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/layer/3.5.1/layer.js"></script>

<!-- JS 桥接层：将原版 JS 的逻辑无缝衔接到 Bulma 的 UI 上 -->
<script>
    // 拦截 Modal 事件，映射给 Bulma
    $(document).on('click', '[data-toggle="modal"]', function(e){
        e.preventDefault();
        var target = $(this).attr('href') || $(this).data('target');
        $(target).addClass('is-active');
    });
    $(document).on('click', '[data-dismiss="modal"], .modal-background, .delete', function(){
        $(this).closest('.modal').removeClass('is-active');
    });

    // 拦截 Tab 事件，映射给 Bulma
    $(document).on('click', '[data-toggle="tab"]', function(e){
        e.preventDefault();
        var target = $(this).attr('href') || $(this).data('target');
        $('.tab-pane').removeClass('is-active');
        $(target).addClass('is-active');
        $(this).closest('ul').find('li').removeClass('is-active');
        $(this).closest('li').addClass('is-active');
    });
    
    // 初始化懒加载
    $(function() {
        $("img.lazy").lazyload({ effect: "fadeIn" });
        
        // 系统弹窗逻辑
        var isModal = <?php echo empty($conf['modal'])?'false':'true';?>;
        if(isModal){
            $('#myModal').addClass('is-active');
        }
    });

    var homepage = true;
    var hashsalt = <?php echo $addsalt_js?>;
</script>

<!-- 保留原版核心逻辑 JS -->
<script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>
<script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
</body>
</html>
