<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover"/>
    <title><?php echo $hometitle?></title>
    <meta name="keywords" content="<?php echo $conf['keywords']?>">
    <meta name="description" content="<?php echo $conf['description']?>">
    
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
    
    <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
    <script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/modernizr/2.8.3/modernizr.min.js"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f3f4f6;
            color: #374151;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* 动态背景图支持 */
        .page-background {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
            filter: blur(8px) brightness(0.9);
            transform: scale(1.05);
        }

        /* 核心容器毛玻璃效果 */
        .glass-container {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 1.5rem;
            box-shadow: var(--glass-shadow);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        /* 顶部导航重塑 */
        .nav-pills-custom .nav-link {
            color: #6b7280;
            font-weight: 600;
            border-radius: 1rem;
            margin: 0 0.2rem;
            padding: 0.75rem 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }
        
        .nav-pills-custom .nav-link:hover {
            background-color: rgba(79, 70, 229, 0.05);
            color: #4F46E5;
        }

        .nav-pills-custom .nav-link.active {
            background: var(--primary-gradient);
            color: #fff;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }

        /* 按钮与卡片悬浮特效 */
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* 数据统计卡片 */
        .stat-card {
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            border-radius: 1.25rem;
            border: 1px solid rgba(0,0,0,0.02);
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.7), 0 4px 6px rgba(0,0,0,0.04);
        }

        /* 更多功能九宫格 */
        .feature-grid .card {
            border-radius: 1.25rem;
            border: none;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        .feature-grid .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: inherit;
            z-index: -1;
            filter: brightness(0.9);
            transition: all 0.3s;
        }
        .feature-grid .card:hover::before {
            filter: brightness(1.1);
            transform: scale(1.05);
        }
        
                .shop-container .form-group {
            margin-bottom: 1rem;
        }
        
        .shop-container .input-group {
            display: flex;
            flex-wrap: nowrap;
            align-items: stretch;
            width: 100%;
        }

        /* 强制兼容旧版 addon 标签，模拟 BS5 的 input-group-text */
        .shop-container .input-group-addon {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
            font-weight: 500;
            line-height: 1.5;
            color: #495057;
            text-align: center;
            white-space: nowrap;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-right: none;
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }

        /* 修正右侧搜索按钮的圆角与边框 */
        .shop-container .form-control + .input-group-addon {
            border-right: 1px solid #dee2e6;
            border-left: none;
            border-radius: 0 0.5rem 0.5rem 0;
        }

        /* 统一输入框和下拉框的视觉风格 */
        .shop-container .form-control {
            flex: 1 1 auto;
            width: 1%;
            min-width: 0;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            color: #495057;
            background-color: #fff;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        
        /* 针对数量加减按钮组合的特殊处理 (input-group-btn) */
        .shop-container .input-group-btn {
            display: flex;
        }
        .shop-container .input-group-btn .btn {
            display: flex;
            align-items: center;
            border-radius: 0;
            padding: 0.5rem 1rem;
        }
        .shop-container .input-group-btn:first-child .btn {
            border-top-left-radius: 0.5rem;
            border-bottom-left-radius: 0.5rem;
        }
        .shop-container .input-group-btn:last-child .btn {
            border-top-right-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }
        
        /* 修复输入框在中间时的直角问题 */
        .shop-container .input-group-btn + .form-control {
            border-radius: 0;
        }
        
        .shop-container .btn-block {
            display: block;
            width: 100%;
        }

        .shop-container .btn-group-justified {
            display: flex;
            width: 100%;
        }
        
        .shop-container .btn-group-justified > .btn,
        .shop-container .btn-group-justified > a {
            flex: 1 1 auto;
            width: 100%;
        }

         .shop-container .btn-rounded {
            border-radius: 50rem !important;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            font-size: 1.1rem;
            font-weight: bold;
            letter-spacing: 2px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            transition: all 0.3s ease;
        }
        
        .shop-container .btn-rounded:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
        }


        /* 头像动效 */
        .avatar-pulse {
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 15px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }

        /* 滚动条美化 */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        <?php echo $background_css?>
    </style>
</head>
<body>

<?php if($background_image){?>
<!-- 动态模糊背景 -->
<div class="page-background" style="background-image: url('<?php echo $background_image;?>');"></div>
<?php }?>

<main class="container py-4 my-auto" style="max-width: 900px;">
    
    <!-- 头部身份卡片 -->
    <div class="glass-container mb-4 p-4 text-center position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: radial-gradient(circle at top right, rgba(79,70,229,0.1), transparent 50%); pointer-events: none;"></div>
        
        <a href="javascript:void(0)" class="d-inline-block mb-3">
            <img src="/assets/img/user.png" alt="Avatar" width="96" height="96" class="rounded-circle shadow-lg border border-3 border-white avatar-pulse">
        </a>
        
        <h2 class="fw-bolder mb-3 text-dark cursor-pointer" onclick="alert('<?php echo $conf['sitename']?>，建议收藏到浏览器书签哦！');" style="letter-spacing: 1px;">
            <?php echo $conf['sitename']?>
        </h2>
        
        <div class="d-flex flex-wrap justify-content-center gap-2 mt-2 position-relative z-index-1">
            <button class="btn btn-light rounded-pill px-4 shadow-sm border" data-bs-toggle="modal" data-bs-target="#anounceModal">
                <i class="fa fa-bell-o text-warning me-1"></i> 最新公告
            </button>
            <?php if($conf['appurl']){?>
                <a href="<?php echo $conf['appurl']; ?>" target="_blank" class="btn btn-primary rounded-pill px-4 shadow-sm" style="background: var(--primary-gradient); border: none;">
                    <i class="fa fa-android me-1"></i> 客户端
                </a>
            <?php }else{?>
                <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#lxkfModal" style="background: var(--primary-gradient); border: none;">
                    <i class="fa fa-headphones me-1"></i> 联系客服
                </button>
            <?php }?>
            <a href="user/login.php" class="btn btn-dark rounded-pill px-4 shadow-sm">
                <i class="fa fa-user-circle-o me-1"></i> 用户中心
            </a>
        </div>
    </div>

    <!-- 主体功能导航 -->
    <div class="glass-container mb-4 p-2">
        <ul class="nav nav-pills nav-fill nav-pills-custom" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active d-flex flex-column align-items-center" data-bs-toggle="pill" data-bs-target="#shop" type="button" role="tab">
                    <i class="fa fa-shopping-bag fa-lg mb-2 mt-1"></i> <span class="small fw-bold">业务下单</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex flex-column align-items-center" data-bs-toggle="pill" data-bs-target="#search" type="button" role="tab">
                    <i class="fa fa-search fa-lg mb-2 mt-1"></i> <span class="small fw-bold">订单查询</span>
                </button>
            </li>
            <?php if($conf['fenzhan_buy']==1){?>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex flex-column align-items-center" data-bs-toggle="pill" data-bs-target="#substation" type="button" role="tab">
                    <i class="fa fa-paper-plane fa-lg mb-2 mt-1 text-danger"></i> <span class="small fw-bold text-danger">加盟分站</span>
                </button>
            </li>
            <?php }?>
            <?php if($conf['gift_open']==1&&$conf['fenzhan_buy']==0){?>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex flex-column align-items-center" data-bs-toggle="pill" data-bs-target="#gift" type="button" role="tab">
                    <i class="fa fa-gift fa-lg mb-2 mt-1 text-success"></i> <span class="small fw-bold">每日抽奖</span>
                </button>
            </li>
            <?php }?>
            <?php if($conf['fenzhan_buy']==0&&$conf['gift_open']==0){?>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex flex-column align-items-center" onclick="window.location.href='./user/'" type="button" role="tab">
                    <i class="fa fa-sign-in fa-lg mb-2 mt-1"></i> <span class="small fw-bold">登录后台</span>
                </button>
            </li>
            <?php }?>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex flex-column align-items-center" data-bs-toggle="pill" data-bs-target="#more" type="button" role="tab">
                    <i class="fa fa-th-large fa-lg mb-2 mt-1"></i> <span class="small fw-bold">探索更多</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- 标签页内容区域 -->
    <div class="tab-content">
        
        <!-- 在线下单模块 -->
        <div class="tab-pane fade show active" id="shop" role="tabpanel">
            <div class="glass-container p-4">
                <div class="alert bg-white border border-danger border-start border-4 rounded-3 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" role="alert">
                    <div>
                        <i class="fa fa-volume-up text-danger me-2 fa-lg"></i>
                        <span class="fw-bold text-dark">［<?=date("m月d号")?>］最新业务通知</span>
                    </div>
                    <button class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#anounceModal">查看详情</button>
                </div>
                <!-- 引用下单核心代码 -->
                <div class="shop-container">
                    <?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
                </div>
            </div>
        </div>

        <!-- 订单查询模块 -->
        <div class="tab-pane fade" id="search" role="tabpanel">
            <div class="glass-container p-4">
                <div class="card border-0 bg-light rounded-4 p-3 mb-4 d-flex flex-row align-items-center shadow-sm">
                    <img src="/assets/img/user.png" class="rounded-circle border border-2 border-white shadow-sm me-3" width="64" height="64" alt="avatar">
                    <div class="flex-grow-1">
                        <h5 class="mb-1 fw-bold text-dark">官方售后</h5>
                        <p class="mb-0 text-muted small"><i class="fa fa-qq text-primary"></i> <?php echo $conf['kfqq'] ?> <span class="mx-1">|</span> 售后订单问题请联系客服</p>
                    </div>
                    <button class="btn btn-primary rounded-circle shadow-sm" style="width: 48px; height: 48px; background: var(--primary-gradient); border: none;" data-bs-toggle="modal" data-bs-target="#lxkfModal">
                        <i class="fa fa-commenting"></i>
                    </button>
                </div>

                <div class="alert alert-secondary rounded-4 border-0" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }?>>
                    <?php echo $conf['gg_search']?>
                </div>

                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <div class="input-group input-group-lg mb-3">
                            <select class="form-select bg-light border-0" id="searchtype" style="max-width: 130px; font-weight: 500;">
                                <option value="0">下单账号</option>
                                <option value="1">订单编号</option>
                            </select>
                            <input type="text" name="qq" id="qq3" class="form-control bg-light border-0 ps-3" placeholder="请输入凭证，留空则查最新订单" onkeydown="if(event.keyCode==13){$('#submit_query').click()}">
                            <button class="btn btn-light bg-light border-0 text-muted" type="button" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="top" data-bs-content="如果您不知道下单账号是什么，可以直接点击查询，系统会根据浏览器本地缓存为您查询最近订单。">
                                <i class="fa fa-question-circle-o"></i>
                            </button>
                        </div>
                        <button type="button" id="submit_query" class="btn btn-primary btn-lg w-100 rounded-pill shadow fw-bold" style="background: var(--primary-gradient); border: none; letter-spacing: 2px;">
                            <i class="fa fa-search me-1"></i> 立即查询
                        </button>
                    </div>
                </div>

                <div id="result2" style="display:none;">
                    <div class="text-center mb-2"><span class="badge bg-danger rounded-pill fw-normal"><i class="fa fa-hand-o-left"></i> 手机端可左右滑动表格查看完整信息</span></div>
                    <div class="table-responsive bg-white rounded-4 shadow-sm border border-light">
                        <table class="table table-hover table-borderless align-middle text-center mb-0 text-nowrap">
                            <thead class="table-light text-muted">
                                <tr><th>下单账号</th><th>商品名称</th><th>数量</th><th class="d-none d-md-table-cell">购买时间</th><th>状态</th><th>操作</th></tr>
                            </thead>
                            <tbody id="list" class="border-top">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- 开通分站模块 -->
        <?php if($conf['fenzhan_buy']==1){?>
        <div class="tab-pane fade" id="substation" role="tabpanel">
            <div class="glass-container overflow-hidden">
                <div class="position-relative text-center text-white py-5" style="background: linear-gradient(135deg, #111827 0%, #374151 100%);">
                    <!-- 装饰元素 -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-25" style="background: radial-gradient(circle, #fff 10%, transparent 10%) 0 0 / 20px 20px;"></div>
                    
                    <h2 class="position-relative z-1 fw-bold display-6 mb-3">分站加盟计划</h2>
                    <div class="position-relative z-1 d-flex justify-content-center align-items-center gap-3">
                        <div class="bg-white bg-opacity-25 rounded-pill px-4 py-2 border border-light border-opacity-25">
                            <i class="fa fa-paper-plane text-info"></i> 入门版 <span class="fw-bold ms-1">￥<?php echo $conf['fenzhan_price']?></span>
                        </div>
                        <div class="bg-warning bg-opacity-25 rounded-pill px-4 py-2 border border-warning border-opacity-50 text-warning">
                            <i class="fa fa-diamond"></i> 旗舰版 <span class="fw-bold ms-1">￥<?php echo $conf['fenzhan_price2']?></span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-white">
                    <div class="row g-4 mb-5 text-center">
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-light h-100 hover-lift">
                                <i class="fa fa-globe fa-2x text-primary mb-2"></i>
                                <h6 class="fw-bold">独立域名网站</h6>
                                <span class="text-muted small">专属你的品牌名片</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-light h-100 hover-lift">
                                <i class="fa fa-dashboard fa-2x text-success mb-2"></i>
                                <h6 class="fw-bold">全功能控制台</h6>
                                <span class="text-muted small">高度自由的站长后台</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-light h-100 hover-lift">
                                <i class="fa fa-cny fa-2x text-warning mb-2"></i>
                                <h6 class="fw-bold">超低拿货密价</h6>
                                <span class="text-muted small">利润拉满轻松变现</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="p-3 rounded-4 bg-light h-100 hover-lift">
                                <i class="fa fa-sitemap fa-2x text-danger mb-2"></i>
                                <h6 class="fw-bold">无限发展下级</h6>
                                <span class="text-muted small">旗舰版专享无限裂变</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success text-center rounded-4 mb-4">
                        <i class="fa fa-info-circle me-1"></i> 账户余额满 <b><?php echo $conf['tixian_min']; ?></b> 元即可申请提现，门槛极低！
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <button class="btn btn-lg btn-light rounded-pill border shadow-sm px-5" data-bs-toggle="modal" data-bs-target="#userjsModal">
                            <i class="fa fa-list-alt text-muted"></i> 详细版本对比
                        </button>
                        <a href="user/regsite.php" target="_blank" class="btn btn-lg btn-dark rounded-pill shadow px-5">
                            开通我的专属分站 <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <!-- 抽奖模块 -->
        <?php if($conf['gift_open']==1){?>
        <div class="tab-pane fade" id="gift" role="tabpanel">
            <div class="glass-container p-5 text-center position-relative overflow-hidden">
                <!-- 庆祝背景装饰 -->
                <div class="position-absolute top-50 start-50 translate-middle w-100 h-100 opacity-10 pointer-events-none" style="background-image: radial-gradient(#F59E0B 2px, transparent 2px); background-size: 30px 30px;"></div>
                
                <i class="fa fa-gift fa-4x text-danger mb-3 position-relative z-1" style="filter: drop-shadow(0 4px 6px rgba(239,68,68,0.3));"></i>
                <h3 id="roll" class="fw-bold mb-4 position-relative z-1 text-dark">点击下方按钮开始抽奖</h3>
                
                <div class="d-flex justify-content-center gap-3 mb-4 position-relative z-1">
                    <button class="btn btn-lg btn-success rounded-pill px-5 shadow border-0" id="start" style="background: linear-gradient(135deg, #10B981 0%, #059669 100%);">开始抽奖</button>
                    <button class="btn btn-lg btn-danger rounded-pill px-5 shadow border-0" id="stop" style="display:none; background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);">停止抽奖</button>
                </div>
                
                <div id="result" class="fs-4 fw-bold text-primary position-relative z-1 mb-4" style="min-height: 36px;"></div>
                
                <div class="giftlist card border-0 bg-white rounded-4 shadow-sm mx-auto position-relative z-1 text-start" style="display:none; max-width: 400px;">
                    <div class="card-header bg-transparent border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0 text-muted"><i class="fa fa-history me-1"></i>最近中奖记录</h6>
                    </div>
                    <div class="card-body">
                        <ul id="pst_1" class="list-unstyled mb-0 small"></ul>
                    </div>
                </div>
            </div>
        </div>
        <?php }?>

        <!-- 探索更多功能矩阵 -->
        <div class="tab-pane fade" id="more" role="tabpanel">
            <div class="feature-grid row g-3">
                <?php
                // 构建高级功能块数组
                $features = [];
                if($conf['gift_open']==1) $features[] = ['href'=>'#gift', 'attr'=>'data-bs-toggle="pill"', 'icon'=>'fa-gift', 'title'=>'福利抽奖', 'desc'=>'在线免费抽好礼', 'color'=>'#F59E0B', 'bg'=>'linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%)'];
                if(!empty($conf['appurl']) && $conf['gift_open']==0) $features[] = ['href'=>$conf['appurl'], 'attr'=>'target="_blank"', 'icon'=>'fa-cloud-download', 'title'=>'APP下载', 'desc'=>'原生体验更丝滑', 'color'=>'#3B82F6', 'bg'=>'linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%)'];
                if(!empty($conf['invite_tid'])) $features[] = ['href'=>'./?mod=invite', 'attr'=>'target="_blank"', 'icon'=>'fa-paper-plane', 'title'=>'推广领赞', 'desc'=>'免费获取名片赞', 'color'=>'#10B981', 'bg'=>'linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%)'];
                if(!empty($conf['cutshop_open'])) $features[] = ['href'=>'./?mod=cutshop', 'attr'=>'target="_blank"', 'icon'=>'fa-handshake-o', 'title'=>'砍价商城', 'desc'=>'好友助力免费拿', 'color'=>'#EF4444', 'bg'=>'linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%)'];
                if(!empty($conf['groupshop_open'])) $features[] = ['href'=>'./?mod=groupshop', 'attr'=>'target="_blank"', 'icon'=>'fa-users', 'title'=>'拼团特惠', 'desc'=>'组团购买更省钱', 'color'=>'#8B5CF6', 'bg'=>'linear-gradient(135deg, #EDE9FE 0%, #DDD6FE 100%)'];
                if(!empty($conf['seckill_open'])) $features[] = ['href'=>'./?mod=seckill', 'attr'=>'target="_blank"', 'icon'=>'fa-bolt', 'title'=>'限时秒杀', 'desc'=>'全网底价手慢无', 'color'=>'#EC4899', 'bg'=>'linear-gradient(135deg, #FCE7F3 0%, #FBCFE8 100%)'];
                if(!empty($conf['package_open'])) $features[] = ['href'=>'./?mod=package', 'attr'=>'target="_blank"', 'icon'=>'fa-archive', 'title'=>'特惠套餐', 'desc'=>'打包购买折上折', 'color'=>'#06B6D4', 'bg'=>'linear-gradient(135deg, #CFFAFE 0%, #A5F3FC 100%)'];
                if(!empty($conf['coupon_open'])) $features[] = ['href'=>'./?mod=coupon', 'attr'=>'target="_blank"', 'icon'=>'fa-ticket', 'title'=>'领券中心', 'desc'=>'先领券后再下单', 'color'=>'#F97316', 'bg'=>'linear-gradient(135deg, #FFEDD5 0%, #FED7AA 100%)'];
                if(!empty($conf['daiguaurl'])) $features[] = ['href'=>'./?mod=daigua', 'attr'=>'', 'icon'=>'fa-rocket', 'title'=>'等级代挂', 'desc'=>'QQ全自动代挂', 'color'=>'#14B8A6', 'bg'=>'linear-gradient(135deg, #CCFBF1 0%, #99F6E4 100%)'];
                
                $features[] = ['href'=>'./user/', 'attr'=>'target="_blank"', 'icon'=>'fa-cogs', 'title'=>'站长后台', 'desc'=>'分站管理控制台', 'color'=>'#4B5563', 'bg'=>'linear-gradient(135deg, #F3F4F6 0%, #E5E7EB 100%)'];

                foreach($features as $f){
                ?>
                <div class="col-6 col-sm-4">
                    <a href="<?php echo $f['href']?>" <?php echo $f['attr']?> class="text-decoration-none">
                        <div class="card h-100 p-3 text-center shadow-sm hover-lift" style="background: <?php echo $f['bg']?>;">
                            <div class="mb-2">
                                <i class="fa <?php echo $f['icon']?> fa-2x" style="color: <?php echo $f['color']?>;"></i>
                            </div>
                            <h6 class="fw-bold mb-1 text-dark"><?php echo $f['title']?></h6>
                            <span class="small text-muted" style="font-size: 0.75rem;"><?php echo $f['desc']?></span>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
        </div>

    </div> <!-- 结束标签页内容 -->

    <!-- 文章列表模块 -->
    <?php if($conf['articlenum']>0){
    $limit = intval($conf['articlenum']);
    $rs=$DB->query("SELECT id,title FROM pre_article WHERE active=1 ORDER BY top DESC,id DESC LIMIT {$limit}");
    $msgrow=array();
    while($res = $rs->fetch()){
        $msgrow[]=$res;
    }
    $i=0;
    ?>
    <div class="glass-container mt-4 overflow-hidden">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light bg-opacity-50">
            <h6 class="mb-0 fw-bold text-dark"><i class="fa fa-newspaper-o text-primary me-2"></i>最新资讯</h6>
            <a href="<?php echo article_url()?>" target="_blank" class="text-decoration-none text-muted small hover-lift">查看全部 <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="list-group list-group-flush">
            <?php foreach($msgrow as $row){ 
                $i++;
                // 前三名特殊高亮徽章
                $badge_class = $i <= 3 ? 'bg-danger' : 'bg-secondary';
            ?>
            <a href="<?php echo article_url($row['id'])?>" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0 border-bottom">
                <span class="badge <?php echo $badge_class?> rounded-pill me-3 shadow-sm" style="width: 24px; height:24px; line-height:14px;"><?php echo $i?></span>
                <span class="text-truncate fw-medium text-dark"><?php echo $row['title']?></span>
                <i class="fa fa-angle-right ms-auto text-muted opacity-50"></i>
            </a>
            <?php }?>
        </div>
    </div>
    <?php }?>

    <!-- 数据统计看板 -->
    <?php if(!$conf['hide_tongji']){?>
    <div class="glass-container mt-4 p-4">
        <h6 class="fw-bold text-dark mb-4"><i class="fa fa-bar-chart text-primary me-2"></i>平台运营数据</h6>
        <div class="row g-3">
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-shield fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_yxts">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">安全运营(天)</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-shopping-bag fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_money">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">累计交易(元)</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-check-circle fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_orders">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">订单总数(笔)</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-sitemap fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_site">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">加盟代理(个)</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-danger bg-opacity-10 text-danger d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-line-chart fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_money1">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">今日交易(元)</div>
                </div>
            </div>
            <div class="col-4 col-md-4">
                <div class="stat-card p-3 text-center h-100">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;">
                        <i class="fa fa-list-alt fa-lg"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1" id="count_orders2">0</h5>
                    <div class="text-muted small" style="font-size: 0.75rem;">今日订单(笔)</div>
                </div>
            </div>
        </div>
    </div>
    <?php }?>

    <!-- 底部页脚 -->
    <footer class="mt-5 mb-4 text-center">
        <div class="glass-container p-4">
            <h6 class="fw-bold text-dark mb-2">
                <?php echo $conf['sitename'] ?> <i class="fa fa-heart text-danger mx-1 heart-beat"></i> <?php echo date("Y")?>
            </h6>
            <p class="mb-2"><a href="./" class="text-decoration-none text-primary fw-medium"><?php echo $_SERVER['HTTP_HOST']?></a></p>
            <div class="small text-muted opacity-75 lh-base"><?php echo $conf['footer']?></div>
        </div>
    </footer>

</main>

<!-- 全局模态框：首页通知弹窗 (myModal) -->
<div class="modal fade" id="myModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white" style="background: var(--primary-gradient);">
                <h5 class="modal-title fw-bold"><i class="fa fa-bullhorn me-2"></i><?php echo $conf['sitename']?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 lh-lg text-dark">
                <?php echo $conf['modal']?>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-primary w-100 rounded-pill" data-bs-dismiss="modal" style="background: var(--primary-gradient); border: none;">我知道了</button>
            </div>
        </div>
    </div>
</div>

<!-- 平台最新公告弹窗 (anounceModal) -->
<div class="modal fade" id="anounceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #10B981 0%, #047857 100%);">
                <h5 class="modal-title fw-bold text-white mx-auto"><i class="fa fa-bell me-2"></i>最新公告通知</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 lh-lg bg-light text-dark">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <?php echo $conf['anounce']?>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-outline-success w-100 rounded-pill" data-bs-dismiss="modal">我明白了</button>
            </div>
        </div>
    </div>
</div>

<!-- 客服与帮助弹窗 (lxkfModal) -->
<div class="modal fade" id="lxkfModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #06B6D4 0%, #3B82F6 100%);">
                <h5 class="modal-title fw-bold text-white mx-auto"><i class="fa fa-support me-2"></i>客服与帮助中心</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item bg-transparent border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button bg-transparent fw-bold text-dark shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                为什么订单显示已完成了却一直没到账？
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-white">
                                订单显示（已完成）就证明已经提交到服务器内！<br>
                                如果长时间没到账请联系客服处理！<br>
                                订单长时间显示（待处理）请联系客服！
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                商品什么时候到账？
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-white">
                                请参考商品简介里面，有关于到账时间的详细说明。
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                卡密没有发送我的邮箱？
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-white">
                                没有收到请检查自己邮箱的垃圾箱！也可以去查单区：输入自己下单时填写的邮箱进行查单。<br>
                                查询到订单后点击（详细）就可以看到自己购买的卡密信息！
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item bg-transparent">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed bg-transparent fw-bold text-dark shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                已付款了没有查询到我订单？
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted small bg-white">
                                联系客服处理，请提供（付款详细记录截图）、（下单商品名称）、（下单账号）。<br>
                                直接把三个信息发给客服，然后等待客服回复处理（请不要发抖动窗口或者QQ电话）！
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-white mt-2">
                    <div class="d-flex align-items-center mb-3 p-3 border rounded-3 bg-light">
                        <img src="/assets/img/user.png" class="rounded-circle me-3" width="50" height="50" alt="...">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">官方订单售后</div>
                            <div class="text-muted small">QQ：<?php echo $conf['kfqq'] ?></div>
                        </div>
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" target="_blank" class="btn btn-sm btn-info text-white rounded-pill px-3 shadow-sm">立即联系</a>
                    </div>
                    <div class="alert alert-warning border-0 small mb-0">
                        <i class="fa fa-info-circle me-1"></i> 想要快速解决问题，请直奔主题：<br>
                        <strong>下单账号 + 业务名称 + 问题描述</strong><br>
                        留言即可，按顺序回复，请勿发送抖动或语音。
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white">
                <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!-- 分站版本介绍弹窗 (userjsModal) -->
<div class="modal fade" id="userjsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);">
                <h5 class="modal-title fw-bold text-white mx-auto"><i class="fa fa-sliders me-2"></i>分站版本权益对比</h5>
                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover align-middle text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-4 text-muted">功能特权</th>
                            <th class="text-muted">入门版</th>
                            <th class="text-warning">旗舰版</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        <tr>
                            <td class="text-start ps-4 fw-medium text-dark">独立网站与专属后台</td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-medium text-dark">低价拿货与自定义售价</td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-medium text-dark">下级代理开通权限</td>
                            <td><i class="fa fa-times-circle text-danger opacity-50 fs-5"></i></td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-medium text-dark">尊享超低密价/高额提成</td>
                            <td><i class="fa fa-times-circle text-danger opacity-50 fs-5"></i></td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                        </tr>
                        <tr>
                            <td class="text-start ps-4 fw-medium text-dark">赠送专属定制APP</td>
                            <td><i class="fa fa-times-circle text-danger opacity-50 fs-5"></i></td>
                            <td><i class="fa fa-check-circle text-success fs-5"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary w-100 rounded-pill" data-bs-dismiss="modal">关闭详情</button>
            </div>
        </div>
    </div>
</div>

<!-- 隐藏的音乐播放器代码 -->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
    <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
        <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
    </div>
</div>

<!-- 境内CDN引入 JS依赖 -->
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/clipboard.js/2.0.11/clipboard.min.js"></script>
<script src="D:/wangzhan/127.0.0.2/assets/vendor/cdn.staticfile.net/layer/3.5.1/layer.js"></script>
<!-- 平台原有JS依赖 (保障功能完好) -->
<script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>

<!-- 核心业务逻辑与初始化 -->
<script type="text/javascript">
var isModal = <?php echo empty($conf['modal'])?'false':'true';?>;
var homepage = true;
var hashsalt = <?php echo $addsalt_js?>;

$(function() {
    // 懒加载初始化
    $("img.lazy").lazyload({
        effect: "fadeIn",
        threshold: 200
    });
    
    // Bootstrap 5 Popover 初始化
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    });
    
    // 首页公告弹窗逻辑
    if(isModal){
        var myModalObj = new bootstrap.Modal(document.getElementById('myModal'));
        myModalObj.show();
    }
});
</script>

<script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
</body>
</html>
