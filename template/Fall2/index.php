<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>
        <?php echo $hometitle?>
    </title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    
    <!-- 字体使用中科大镜像 -->
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/fonts.loli.net/css2_6d09b232" rel="stylesheet">
    
    <!-- 原版基础依赖，使用国内字节跳动/七牛云 Staticfile 镜像源 -->
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">

    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/modernizr/2.8.3/modernizr.min.js"></script>
    <!--[if lt IE 9]>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?php echo $background_css?>
    
    <style>
        /* =========================================================
           高级拟态玻璃风主题 (Glassmorphism) - 严格限定作用域，防污染
           ========================================================= */
        body {
            font-family: 'Noto Sans SC', sans-serif;
            background-color: #0f172a; /* 默认深色背景，防背景图失效时刺眼 */
            color: #f8fafc;
        }
        
        .full-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            object-fit: cover; z-index: -1; opacity: 0.85; filter: blur(5px) brightness(0.6);
        }

        /* 基础毛玻璃容器 */
        .glass-box {
            background-color: rgba(25, 25, 30, 0.5) !important; /* 偏暗的半透明背景，适配暗色调 */
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px !important;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            margin-bottom: 15px;
            overflow: hidden;
        }

        /* 覆盖内层区块默认背景，防止遮挡毛玻璃效果 */
        .glass-box .block-content, 
        .glass-box .list-group-item,
        .glass-box .well {
            background-color: transparent !important;
            border-color: rgba(255,255,255,0.05) !important;
            color: inherit;
        }

        /* 顶部导航按钮组美化 */
        .glass-btn-group .btn {
            background: rgba(255, 255, 255, 0.05);
            border: none;
            color: #fff;
            transition: all 0.3s ease;
        }
        .glass-btn-group .btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .glass-btn-group .btn i {
            margin-bottom: 5px;
            font-size: 1.2em;
            display: block;
        }

        /* TAB导航栏美化 */
        .glass-tabs {
            background-color: rgba(25, 25, 30, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            margin-bottom: 15px;
            display: flex;
            justify-content: center;
            border-bottom: none;
            padding: 5px;
        }
        .glass-tabs > li {
            flex: 1;
            text-align: center;
            margin-bottom: 0;
        }
        .glass-tabs > li > a {
            color: #94a3b8;
            border: none !important;
            border-radius: 12px !important;
            margin-right: 0;
            padding: 10px 0;
            font-weight: 500;
            transition: all 0.2s;
        }
        .glass-tabs > li > a:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
            color: #fff;
        }
        .glass-tabs > li.active > a, 
        .glass-tabs > li.active > a:hover, 
        .glass-tabs > li.active > a:focus {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        /* 模态框美化 */
        .modal-content {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: #fff;
        }
        .modal-header { border-bottom: 1px solid rgba(255, 255, 255, 0.1); }
        .modal-footer { border-top: 1px solid rgba(255, 255, 255, 0.1); }
        .close { color: #fff; text-shadow: none; opacity: 0.8;}
        .close:hover { color: #ef4444; opacity: 1;}

        /* 表格样式修正 (仅针对非 shop.inc.php 内的表格) */
        .glass-table { color: #fff; }
        .glass-table th, .glass-table td { border-top: 1px solid rgba(255,255,255,0.05) !important; }
        .glass-table thead th { border-bottom: 2px solid rgba(255,255,255,0.1) !important; }

        /* 文章列表与更多功能块美化 */
        .glass-grid-item {
            border-radius: 12px;
            transition: transform 0.2s;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.05);
        }
        .glass-grid-item:hover {
            transform: translateY(-2px);
            background: rgba(255,255,255,0.1);
        }

        /* shop.inc.php 防护：若原插件使用白色背景输入框，在此强制设定某些文本色以防看不清 */
        #shop .text-muted { color: #cbd5e1 !important; }
    </style>
</head>
<body>
    <?php if($background_image){?>
    <img src="<?php echo $background_image;?>" alt="Full Background" class="full-bg animated pulse " ondragstart="return false;" oncontextmenu="return false;">
    <?php }?>
    
    <div style="padding-top:15px;">
        <!-- 还原原版的居中排版结构，彻底解决排版错乱拉伸的问题 -->
        <div class="col-xs-12 col-sm-10 col-md-8 col-lg-4 center-block" style="float: none;">
            
            <!--弹出公告-->
            <div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel"><?php echo $conf['sitename']?></h4>
                        </div>
                        <div class="modal-body">
                            <?php echo $conf['modal']?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">知道啦</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--顶部公告-->
            <div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">×</span><span class="sr-only">Close</span>
                            </button>
                            <h4 class="modal-title" id="myModalLabel">公告</h4>
                        </div>
                        <div class="modal-body">
                            <?php echo $conf['anounce']?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--顶部头像与导航 (引入毛玻璃盒子)-->
            <div class="block glass-box">
                <div class="block-content block-content-full text-center" style="padding-top: 20px;">
                    <img class="img-avatar img-avatar80 img-avatar-thumb" src="/assets/img/user.png" style="border: 2px solid rgba(255,255,255,0.2); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                </div>
                <div class="block-content block-content-mini block-content-full glass-btn-group">
                    <div class="btn-group btn-group-justified">
                        <a class="btn btn-default" data-toggle="modal" href="#anounce">
                            <i class="fa fa-bullhorn text-info"></i><span style="font-size: 12px;">公告</span>
                        </a>
                        <?php if($conf['appurl']){?>
                        <a href="<?php echo $conf['appurl']; ?>" target="_blank" class="btn btn-default">
                            <i class="fa fa-android text-success"></i><span style="font-size: 12px;">客户端</span>
                        </a>
                        <?php }else{?>
                        <a href="#customerservice" target="_blank" data-toggle="modal" class="btn btn-default">
                            <i class="fa fa-question-circle text-warning"></i><span style="font-size: 12px;">帮助</span>
                        </a>
                        <?php }?>
                        <?php if($islogin2==1){?>
                        <a class="btn btn-default" data-toggle="modal" href="user/">
                            <i class="fa fa-dashboard text-danger"></i><span style="font-size: 12px;">后台</span>
                        </a>
                        <?php }else{?>
                        <a class="btn btn-default" data-toggle="modal" href="user/login.php">
                            <i class="fa fa-user-circle text-danger"></i><span style="font-size: 12px;">登录</span>
                        </a>
                        <?php }?>
                    </div>
                </div>
            </div>

            <!--TAB标签菜单-->
            <ul class="nav nav-tabs glass-tabs animated zoomInLeft" data-toggle="tabs">
                <li class="active">
                    <a href="#shop" data-toggle="tab"><i class="fa fa-shopping-bag fa-fw"></i> 下单</a>
                </li>
                <li>
                    <a href="#search" data-toggle="tab" id="tab-query"><i class="fa fa-search"></i> 查询</a>
                </li>
                <?php if($conf['fenzhan_buy']==1){?>
                <li>
                    <a href="#Substation" data-toggle="tab"><i class="fa fa-coffee fa-fw"></i> 分站</a>
                </li>
                <?php }?>
                <?php if($conf['gift_open']==1&&$conf['fenzhan_buy']==0){?>
                <li>
                    <a href="#gift" data-toggle="tab"><i class="fa fa-gift fa-fw"></i> 抽奖</a>
                </li>
                <?php }?>
                <li>
                    <a href="#more" data-toggle="tab"><i class="fa fa-folder-open"></i> 更多</a>
                </li>
            </ul>

            <!--主内容区域-->
            <div class="block glass-box">
                <div class="block-content tab-content" style="padding: 20px 15px;">
                    
                    <!--在线下单 (原封不动保护)-->
                    <div class="tab-pane active" id="shop">
                        <!-- 这里不修改任何 shop.inc.php 需要的外部样式，原样载入 -->
                        <?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
                        <div class="alert alert-warning alert-dismissable text-center" style="margin-top: 15px; border-radius: 12px; background-color: rgba(240, 173, 78, 0.2); border: 1px solid rgba(240, 173, 78, 0.4);">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true"><i class="fa fa-times-circle"></i></span></button>
                            <a href="user/regsite.php" data-toggle="modal" title="" style="color: #ffd07f; font-weight: bold; text-decoration: none;"><i class="fa fa-arrow-right"></i>&nbsp;免费拥有和我一样的网站：点我拥有</a>
                        </div>
                    </div>
                    
                    <!--查询订单-->
                    <div class="tab-pane" id="search">
                        <ul class="list-group animated bounceIn" style="border-radius: 12px; overflow: hidden; margin-bottom: 15px;">
                            <li class="list-group-item" style="background: rgba(255,255,255,0.05); border: none;">
                                <div class="media">
                                    <span class="pull-left thumb-sm">
                                        <img src="/assets/img/user.png" class="img-circle img-thumbnail img-avatar" style="border-color: rgba(255,255,255,0.2);">
                                    </span>
                                    <div class="pull-right push-15-t">
                                        <a href="#customerservice" target="_blank" data-toggle="modal" class="btn btn-sm btn-info" style="border-radius: 20px;">联系客服</a>
                                    </div>
                                    <div class="pull-left push-10-t">
                                        <div class="font-w600 push-5 text-white">订单售后QQ客服</div>
                                        <div class="text-muted" style="color: #cbd5e1;">
                                            <script>var online = new Array();</script>
                                            <!-- QQ 在线状态外链已移除（离线化） -->
                                            <script>if (online[0] == 0) document.write('<i class="fa fa-clock-o"></i>&nbsp;8:00 - 23:00');else document.write('<i class="fa fa-circle text-success"></i>&nbsp;8:00 - 23:00');</script>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        
                        <div class="col-xs-12 well well-sm animation-pullUp" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }else{?> style="border-radius:12px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff;"<?php }?>>
                            <?php echo $conf['gg_search']?>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-btn">
                                    <select class="form-control custom-select" id="searchtype" style="padding: 6px 4px; width:90px; border-radius: 25px 0px 0px 25px;">
                                        <option value="0">下单账号</option>
                                        <option value="1">订单号</option>
                                    </select>
                                </div>
                                <input type="text" name="qq" id="qq3" value="<?php echo $qq?>" class="form-control" placeholder="请输入要查询的内容（留空显示最新）" onkeydown="if(event.keyCode==13){submit_query.click()}" required style="border-radius: 0;" />
                                <span class="input-group-btn">
                                    <a tabindex="0" class="btn btn-default" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" title="帮助说明" data-content="请输入下单时填写的信息。留空查询则读取浏览器缓存。" style="border-radius: 0px 25px 25px 0px;">
                                        <i class="glyphicon glyphicon-exclamation-sign"></i>
                                    </a>
                                </span>
                            </div>
                        </div>
                        
                        <input type="submit" id="submit_query" class="btn btn-sm btn-success btn-block" style="border-radius: 25px; padding: 10px;" value="查询订单">
                        
                        <div id="result2" class="form-group" style="display:none; margin-top: 15px;">
                            <center><small style="color: #ff8787;">手机用户可以左右滑动</small></center>
                            <div class="table-responsive" style="border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.1);">
                                <table class="table table-vcenter table-condensed glass-table">
                                    <thead>
                                        <tr>
                                            <th>下单账号</th><th>商品名称</th><th>数量</th><th class="hidden-xs">购买时间</th><th>状态</th><th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!--开通分站-->
                    <?php if($conf['fenzhan_buy']==1){?>
                    <div class="tab-pane" id="Substation">
                        <table class="table table-borderless animated bounceIn text-center glass-table">
                            <tbody>
                                <tr style="background: rgba(255,255,255,0.05); border-radius: 12px;">
                                    <td>
                                        <h4>
                                            <span style="font-weight:bold; letter-spacing: 2px;">
                                                <font color="#FF8000">搭</font><font color="#EC6D13">建</font>
                                                <font color="#D95A26">属</font><font color="#C64739">于</font>
                                                <font color="#A0215F">自</font><font color="#8D0E72">己</font>
                                                <font color="#5400AB">的</font><font color="#4100BE">商</font>
                                                <font color="#2E00D1">城</font>
                                            </span>
                                        </h4>
                                    </td>
                                </tr>
                                <tr><td><span style="color: #cbd5e1;">学生/上班族/创业/休闲赚钱必备工具</span></td></tr>
                                <tr><td><strong><span style="color: #4ade80;">网站轻轻松松推广日赚上千元不是梦</span></strong></td></tr>
                                <tr>
                                    <td>
                                        <span class="glyphicon glyphicon-magnet text-info"></span>&nbsp;快加入我们成为大家庭中的一员吧
                                        <hr style="border-color: rgba(255,255,255,0.1);">
                                        <a href="#userjs" data-toggle="modal" class="btn btn-info btn-sm" style="float:left; border-radius: 20px; padding: 5px 15px;">
                                            <span class="glyphicon glyphicon-eye-open"></span>&nbsp;网站详情
                                        </a>
                                        <a href="./user/regsite.php" target="_blank" class="btn btn-success btn-sm" style="float:right; border-radius: 20px; padding: 5px 15px;">
                                            <span class="glyphicon glyphicon-share-alt"></span>&nbsp;免费开通
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php }?>
                    
                    <!--抽奖-->
                    <?php if($conf['gift_open']==1){?>
                    <div class="tab-pane" id="gift">
                        <div class="panel-body text-center">
                            <div id="roll" style="font-size: 20px; font-weight: bold; color: #f87171; margin-bottom: 20px;">点击下方按钮开始抽奖</div>
                            <p>
                                <a class="btn btn-info" id="start" style="display:block; border-radius: 25px; padding: 10px;">开始抽奖</a>
                                <a class="btn btn-danger" id="stop" style="display:none; border-radius: 25px; padding: 10px;">停止</a>
                            </p>
                            <div id="result" style="color: #4ade80; font-size: 16px; margin: 15px 0;"></div>
                            <div class="giftlist text-left" style="display:none; background: rgba(0,0,0,0.1); padding: 10px; border-radius: 10px;">
                                <strong style="color: #94a3b8;"><i class="fa fa-history"></i> 最近中奖记录</strong>
                                <ul id="pst_1" style="color: #cbd5e1; font-size: 13px; margin-top: 10px;"></ul>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                    
                    <!--更多-->
                    <div class="tab-pane fade fade-right" id="more">
                        <div class="row" style="margin: 0 -5px;">
                            <?php 
                            // 为"更多"板块重新编写安全的栅格布局
                            $more_items = [
                                ['cond' => $conf['gift_open']==1, 'href' => '#gift', 'data' => 'data-toggle="tab"', 'icon' => 'fa-gift', 'color' => '#f472b6', 'text' => '抽奖'],
                                ['cond' => !empty($conf['appurl']), 'href' => $conf['appurl'], 'data' => 'target="_blank"', 'icon' => 'fa-cloud-download', 'color' => '#4ade80', 'text' => 'APP下载'],
                                ['cond' => !empty($conf['daiguaurl']), 'href' => './?mod=daigua', 'data' => 'target="_blank"', 'icon' => 'fa-rocket', 'color' => '#60a5fa', 'text' => '等级代挂'],
                                ['cond' => !empty($conf['invite_tid']), 'href' => './?mod=invite', 'data' => 'target="_blank"', 'icon' => 'fa-paper-plane-o', 'color' => '#fbbf24', 'text' => '免费领赞'],
                                ['cond' => !empty($conf['cutshop_open']), 'href' => './?mod=cutshop', 'data' => 'target="_blank"', 'icon' => 'fa-cutlery', 'color' => '#f87171', 'text' => '砍价商城'],
                                ['cond' => !empty($conf['groupshop_open']), 'href' => './?mod=groupshop', 'data' => 'target="_blank"', 'icon' => 'fa-tags', 'color' => '#34d399', 'text' => '团购商城'],
                                ['cond' => !empty($conf['seckill_open']), 'href' => './?mod=seckill', 'data' => 'target="_blank"', 'icon' => 'fa-puzzle-piece', 'color' => '#818cf8', 'text' => '秒杀专场'],
                                ['cond' => !empty($conf['package_open']), 'href' => './?mod=package', 'data' => 'target="_blank"', 'icon' => 'fa-archive', 'color' => '#2dd4bf', 'text' => '团购优惠'],
                                ['cond' => !empty($conf['coupon_open']), 'href' => './?mod=coupon', 'data' => 'target="_blank"', 'icon' => 'fa-credit-card', 'color' => '#fb923c', 'text' => '领取优惠券'],
                                ['cond' => true, 'href' => './user/', 'data' => 'target="_blank"', 'icon' => 'fa-certificate', 'color' => '#c084fc', 'text' => '用户中心'],
                            ];
                            foreach($more_items as $item) {
                                if($item['cond']) {
                            ?>
                            <div class="col-xs-4 col-sm-4" style="padding: 5px;">
                                <a href="<?php echo $item['href']; ?>" <?php echo $item['data']; ?> class="block text-center glass-grid-item" style="text-decoration: none; padding: 15px 5px;">
                                    <i class="fa <?php echo $item['icon']; ?> fa-2x" style="color: <?php echo $item['color']; ?>; margin-bottom: 8px;"></i>
                                    <div style="font-size: 12px; color: #cbd5e1; font-weight: 500;"><?php echo $item['text']; ?></div>
                                </a>
                            </div>
                            <?php } } ?>
                        </div>
                    </div>
                    
                </div>
            </div>

            <!--文章列表-->
            <?php if($conf['articlenum']>0){
            $limit = intval($conf['articlenum']);
            $rs=$DB->query("SELECT id,title FROM pre_article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
            $msgrow=array();
            while($res = $rs->fetch()){
                $msgrow[]=$res;
            }
            $class_arr = ['danger','warning','primary','success','info'];
            $i=0;
            ?>
            <div class="block glass-box">
                <div class="block-header" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding: 15px;">
                    <h3 class="block-title text-white">
                        <i class="fa fa-newspaper-o text-info"></i> 近期动态
                    </h3>
                </div>
                <div class="list-group" style="margin-bottom: 0;">
                    <?php foreach($msgrow as $row){ ?>
                    <a target="_blank" class="list-group-item" href="<?php echo article_url($row['id'])?>" style="border-bottom: 1px solid rgba(255,255,255,0.05); padding: 12px 15px;">
                        <span class="label label-<?php echo $class_arr[($i)%5]; ?>" style="border-radius: 50%; width: 20px; height: 20px; display: inline-block; padding: 4px 0; text-align: center; margin-right: 8px;"><?php echo ++$i; ?></span>
                        <span style="color: #cbd5e1; font-size: 13px;"><?php echo $row['title']?></span>
                    </a>
                    <?php }?>
                </div>
                <a href="<?php echo article_url()?>" target="_blank" class="btn btn-block" style="background: rgba(0,0,0,0.2); color: #94a3b8; border-radius: 0 0 16px 16px; border: none; padding: 12px;">查看全部文章</a>
            </div>
            <?php }?>

            <!--底部统计与版权-->
            <div class="block glass-box" style="padding: 15px;">
                <?php if(!$conf['hide_tongji']){?>
                <div class="row text-center" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 15px;">
                    <div class="col-xs-4">
                        <small style="color: #94a3b8;">订单总数</small><br>
                        <span id="count_orders" style="color: #60a5fa; font-size: 18px; font-weight: bold;"></span>
                    </div>
                    <div class="col-xs-4" style="border-left: 1px solid rgba(255,255,255,0.1); border-right: 1px solid rgba(255,255,255,0.1);">
                        <small style="color: #94a3b8;">今日订单</small><br>
                        <span id="count_orders2" style="color: #4ade80; font-size: 18px; font-weight: bold;"></span>
                    </div>
                    <div class="col-xs-4">
                        <small style="color: #94a3b8;">运营天数</small><br>
                        <span id="count_yxts" style="color: #c084fc; font-size: 18px; font-weight: bold;"></span>
                    </div>
                </div>
                <?php }?>
                
                <div class="text-center">
                    <a href="javascript:void(0);" onclick="AddFavorite('<?php echo $conf['sitename']?>',location.href)" style="color: #94a3b8; text-decoration: none; font-size: 12px;">
                        <i class="fa fa-star" style="color: #fbbf24;"></i> 本站网址：<?php echo $_SERVER['HTTP_HOST'];?> (点击收藏)
                    </a>
                    <div style="margin-top: 10px; font-size: 12px; color: #64748b; line-height: 1.6;">
                        <?php echo $conf['footer']?>
                    </div>
                </div>
            </div>

            <!-- 关于我们与各种隐藏弹窗 (原位置放置，避免样式冲突) -->
            <!--版本介绍-->
            <?php if($conf['fenzhan_buy']==1){?>
            <div class="modal fade" align="left" id="userjs" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">版本介绍</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless table-vcenter glass-table text-center" style="margin-bottom: 0;">
                                <thead style="background: rgba(0,0,0,0.2);">
                                    <tr><th>功能</th><th class="text-center">普及版/专业版</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>专属商城平台</td><td><i class="fa fa-check text-success"></i> / <i class="fa fa-check text-success"></i></td></tr>
                                    <tr><td>赚取用户提成</td><td><i class="fa fa-check text-success"></i> / <i class="fa fa-check text-success"></i></td></tr>
                                    <tr><td>赚取下级分站提成</td><td><i class="fa fa-close text-danger"></i> / <i class="fa fa-check text-success"></i></td></tr>
                                    <tr><td>搭建下级分站</td><td><i class="fa fa-close text-danger"></i> / <i class="fa fa-check text-success"></i></td></tr>
                                    <tr><td>赠送专属精致APP</td><td><i class="fa fa-close text-danger"></i> / <i class="fa fa-check text-success"></i></td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer text-center" style="color: #94a3b8; font-size: 12px;">
                            <em>* 自己的能力决定着你的收入！</em>
                        </div>
                    </div>
                </div>
            </div>
            <?php }?>
            
            <!--帮助说明-->
            <div class="modal fade" align="left" id="customerservice" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">帮助</h4>
                        </div>
                        <div class="modal-body" id="accordion" style="padding: 10px;">
                            <?php 
                            $helps = [
                                ['q' => '为什么订单显示已完成了却一直没到账？', 'a' => '订单显示（已完成）就证明已经提交到服务器内！<br>如果长时间没到账请联系客服处理！<br>订单长时间显示（待处理）请联系客服！', 'in' => true],
                                ['q' => '商品什么时候到账？', 'a' => '请参考商品简介里面，有关于到账时间的说明。'],
                                ['q' => '卡密/CDK没有发送我的邮箱？', 'a' => '没有收到请检查自己邮箱的垃圾箱！也可以去查单区输入邮箱查询。'],
                                ['q' => '已付款了没有查询到我订单？', 'a' => '联系客服处理，请提供（付款详细记录截图）（下单商品名称）（下单账号）。']
                            ];
                            foreach($helps as $i => $h){ $id = "collapse".$i; ?>
                            <div class="panel panel-default" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); margin-bottom: 6px;">
                                <div class="panel-heading" style="background: transparent; border-bottom: none;">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#<?php echo $id; ?>" class="<?php echo empty($h['in'])?'collapsed':''; ?>" style="color: #cbd5e1; text-decoration: none; display: block;">
                                            <i class="fa fa-question-circle text-info"></i> <?php echo $h['q']; ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="<?php echo $id; ?>" class="panel-collapse collapse <?php echo !empty($h['in'])?'in':''; ?>">
                                    <div class="panel-body" style="border-top: 1px solid rgba(255,255,255,0.05); color: #94a3b8; font-size: 13px;">
                                        <?php echo $h['a']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="alert alert-danger" style="margin-top: 10px; margin-bottom: 0; border-radius: 8px; padding: 10px; background: rgba(220, 38, 38, 0.2); border: 1px solid rgba(220, 38, 38, 0.3);">
                                想要快速回答你的问题请讲清楚!<br>下单账号+业务名称+问题，直奔主题。
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- 脚本功能保留区 -->
    <script>
        function AddFavorite(title, url) {
            try { window.external.addFavorite(url, title); }
            catch (e) {
                try { window.sidebar.addPanel(title, url, ""); }
                catch (e) { alert("请您按 Ctrl+D 手动收藏本网址! "); }
            }
        }
    </script>
    <div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
        <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')" style="position: fixed; bottom: 20px; right: 20px; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); border-radius: 50%; width: 40px; height: 40px; text-align: center; line-height: 40px; z-index: 999; border: 1px solid rgba(255,255,255,0.2);">
            <i class="fa fa-music fa-spin" style="color: #fff;"></i>
            <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
        </div>
    </div>

    <!-- 原有核心JS，依然走国内高速节点 -->
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery/1.12.4/jquery.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/clipboard.js/1.7.1/clipboard.min.js"></script>
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/layer/2.3/layer.js"></script>
    <script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>
    
    <script type="text/javascript">
        var isModal = <?php echo empty($conf['modal'])?'false':'true';?>;
        var homepage = true;
        var hashsalt = <?php echo $addsalt_js?>;
        $(function() {
            $("img.lazy").lazyload({effect: "fadeIn"});
        });
    </script>
    <script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
    <?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
</body>
</html>
