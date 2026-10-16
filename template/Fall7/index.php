<?php
if(!defined('IN_CRONLITE'))exit();
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no"/>
  <title><?php echo $hometitle?></title>
  <meta name="keywords" content="<?php echo $conf['keywords']?>">
  <meta name="description" content="<?php echo $conf['description']?>">
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/simple/css/oneui.css">
  <link rel="stylesheet" href="<?php echo $cdnserver?>assets/css/common.css?ver=<?php echo VERSION ?>">
  <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<style>body{ background: linear-gradient(to bottom,#9bc8ee,#9bc8ee) fixed;}</style></head>
<body>
    
    <style>

    .my-text {
        color: #666666;
    }

    .wcnmsbdx-sb {
        background-color: rgba(255, 255, 255, 0.5);
    }
    
    .jumping-text {
        animation: jump 0.5s infinite alternate;
    }
    
    .button-container {
        display: flex;
        justify-content: center;
        gap: 10px; 
        }
    
     .cnmysys {

     font-weight: bold;

     }
    @keyframes jump {
        0% { transform: translateY(0); }
        100% { transform: translateY(-10px); }
    }
    </style>
    
<?php if($background_image){?>
<?php }?>
<div style="padding-top:6px;">
<!--查单说明开始-->
<div class="col-xs-12 col-sm-10 col-md-8 col-lg-4 center-block" style="float: none;">
<!--弹出公告-->
<div class="modal fade" align="left" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><span style="font-family: YourFontName"><?php echo $conf['sitename']?></h4>
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
<!--弹出公告-->
<!--公告-->
<div class="modal fade" align="left" id="anounce" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><span style="font-family: YourFontName">公告</h4>
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
<!--公告-->

<!--顶部导航-->
    <div class="block block-link-hover3" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);">
        <div class="block-content block-content-full text-center bg-image"
             style="background-image: url('D:/wangzhan/127.0.0.2/assets/vendor/pan.suyanw.cn/view.php/8ddfcf343b52fd8808f7bfaa8acf430e.png');background-size: 100% 100%;">
            <div>
                <div>
                    <img class="img-avatar img-avatar80 img-avatar-thumb" src="/assets/img/user.png">
                </div>
            </div>
        </div>
        <div class="block-content block-content-mini block-content-full">
            <div class="btn-group btn-group-justified">
                
                
                <div class="button-container">
                  <a class="button btn-default" style="border:2px solid; background: margin-top:30px;font-size:10px;padding:10px;border-radius:15px;background-color:" data-toggle="modal" href="#anounce"><span style="font-weight:bold"><i class="fa fa-bullhorn"></i> 公告 </button></a>
                  
                   <a class="button btn-default"  style="border:2px solid; background: margin-top:30px;font-size:10px;padding:10px;border-radius:15px;background-color:" data-toggle="modal" href="user/login.php"><span style="font-weight:bold"><i class="fa fa-users fa-1x"></i> 登陆  </button></a>
                  	
                  			<?php if($conf['appurl']){?>
			<a href="<?php echo $conf['appurl']; ?>" target="_blank" class="btn btn-effect-ripple btn-default"><i class="fa fa-android"></i> <span style="font-weight:bold">客户端</span></a>
			<?php }else{?>
			<?php }?>
			<?php if($islogin2==1){?>
			
			<a class="button btn-default" style="border:2px solid; background: margin-top:30px;font-size:10px;padding:10px;border-radius:15px;background-color:"  data-toggle="modal" href="user/"><span style="font-weight:bold"><i class="fa fa-users fa-1x"></i>&nbsp;个人中心 </button></a>
			<div class="btn-group">
                 </div>
			<?php }?>
					</div>
             </div>
        </div>
        
        	<div class="panel-body"><span style="font-weight:bold"><span style="color: #90EE90;"><i class="fa fa-gift"> </i></span> <a class="my-text"><span style="color: #9FB6CD; font-family: YourFontName">开通分站更优惠，签到领现金</a><a class="btn btn-xs btn-danger pull-right" style="border:1px solid #b3cde3; background: ;margin-top:0px;font-size:10px;padding:5px;border-radius:8px;background-color:" href="./user/reg.php" style="background-color:#FFCC33; border-color: #FFCC33;">立即开通</a></div>
         	<div class="panel-body"><span style="font-weight:bold"><span style="color: #90EE90;"><i class="fa fa-qq"> </i></span> <a class="my-text"> <span style="color: #9FB6CD; font-family: YourFontName">售后问题『 7×24 』在线人工客服</a><a class="btn btn-xs btn-danger pull-right" style="border:1px solid #b3cde3; background: ;margin-top:0px;font-size:10px;padding:5px;border-radius:8px;background-color:" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes">联系客服</a></div>
        	<div class="jumping-text" style="text-align:center"><img style="height:30px; width:27px; margin-right:5px;" src="D:/wangzhan/127.0.0.2/assets/vendor/pan.suyanw.cn/view.php/a75358a8a8c196234ece9caed2267d25.gif" > <span style="font-weight:bold"> <font color="#FF8000">开通</font>
              <font color="#EC6D13">分站</font>
              <font color="#D95A26">每日</font>
              <font color="#C64739">净赚</font>
              <font color="#A0215F">500+</font>
              <font color="#8D0E72">欢迎</font>
              <font color="#5400AB">加入</font>
              </span></div>
          </h4>
    </div>
<!--查单说明开始-->
    <script>
    setInterval(changeText, 1000);

        function changeText() {
            var text = document.getElementsByClassName("jumping-text")[0];
            text.innerHTML = randomizeText(title);
        }

        function randomizeText(text) {
            var randomizedText = "";
            for (var i = 0; i < text.length; i++) {
                var randomChar = text.charAt(Math.floor(Math.random() * text.length));
                randomizedText += randomChar;
            }
            return randomizedText;
        }
    </script>
    
<!--顶部导航-->
<div class="block" style="margin-top:0px;font-size:15px;padding:5px;border-radius:15px;">
     <ul class="nav nav-tabs nav-tabs-alt" data-toggle="tabs">
         
          
   <li style="width: 25%; font-size: 13px;" align="center" class="active"><a href="#shop" data-toggle="tab"><span style="font-weight:bold"><img src="D:/wangzhan/127.0.0.2/assets/vendor/pan.suyanw.cn/view.php/a4c308fe41a57c4751b133d9189161b4.gif" width="15%"> 下单</span></a></li>
            <li style="width: 25%; font-size: 13px;" align="center"><a href="#search" data-toggle="tab" id="tab-query"><span style="font-weight:bold"><i class="fa fa-search"></i> 查询</span></a></li>
			<?php if($conf['fenzhan_buy']==1){?><li style="width: 25%; font-size: 13px;" align="center"><a href="#Substation" data-toggle="tab"><span style="font-weight:bold"><font color="#ff0000"><i class="fa fa-coffee fa-fw"></i> 分站</span></font></a></li><?php }?>
			<?php if($conf['gift_open']==1&&$conf['fenzhan_buy']==0){?><li style="width: 25%; font-size: 13px;" align="center"><a href="#gift" data-toggle="tab"><span style="font-weight:bold"><i class="fa fa-gift fa-fw"></i> 抽奖</span></a></li><?php }?>
			<li style="width: 25%; font-size: 13px;" align="center"><a href="#more" data-toggle="tab"><span style="font-weight:bold"><i class="fa fa-folder-open"></i> 更多</span></a></li>
        </ul>
<!--TAB标签-->
    <div class="block-content tab-content">
<!--在线下单-->
    <div class="tab-pane active" id="shop">	
<?php include TEMPLATE_ROOT.'default/shop.inc.php'; ?>
  	</div>
<!--在线下单-->
<!--查询订单-->
    <div class="tab-pane" id="search">
	<ul class="list-group animated bounceIn">
      <li class="list-group-item">
        <div class="media">
          <span class="pull-left thumb-sm"><img src="/assets/img/user.png" class="img-circle img-thumbnail img-avatar"></span>
          <div class="pull-right push-15-t">
            	  <a href="#customerservice" target="_blank" data-toggle="modal" class="btn btn-sm btn-info">联系客服</a>
        </div>
          <div class="pull-left push-10-t">
            <div class="font-w600 push-5">订单售后QQ客服</div>
            <div class="text-muted">
              <script>var online = new Array();</script>
              <!-- QQ 在线状态外链已移除（离线化） -->
              <script>if (online[0] == 0) document.write('<i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;'+ "8:00 - 23:00");else document.write('<i class="fa fa-circle text-success"></i>&nbsp;'+"8:00 - 23:00");</script></div>
          </div>
        </div>
      </li>
    </ul>
		<div class="col-xs-12 well well-sm animation-pullUp" <?php if(empty($conf['gg_search'])){?>style="display:none;"<?php }?>><?php echo $conf['gg_search']?></div>
			<div class="form-group">
				<div class="input-group">
					<div class="input-group-btn">
						<select class="form-control" id="searchtype" style="padding: 6px 4px;width:90px"><option value="0">下单账号</option><option value="1">订单号</option></select>
					</div>
					<input type="text" name="qq" id="qq3" value="<?php echo $qq?>" class="form-control" placeholder="请输入要查询的内容（留空则显示最新订单）" onkeydown="if(event.keyCode==13){submit_query.click()}" required/>
					<span class="input-group-btn"><a tabindex="0" class="btn btn-default" role="button" data-container="body" data-toggle="popover" data-trigger="focus" data-placement="top" title="查询内容是什么？" data-content="请输入您下单时，在第一个输入框内填写的信息。如果您不知道下单账号是什么，可以不填写，直接点击查询，则会根据浏览器缓存查询！"><i class="glyphicon glyphicon-exclamation-sign"></i></a></span>
				</div>
			</div>
			<input type="submit" id="submit_query" class="btn btn-sm btn-success btn-block btn-sm" style="text-shadow: black 1px 1px 1px;" value="查询订单">
			<div id="result2" class="form-group" style="display:none;">
              <center><small><font color="#ff0000">手机用户可以左右滑动</font></small></center>
				<div class="table-responsive">
					<table class="table table-vcenter table-condensed table-striped">
					<thead><tr><th>下单账号</th><th>商品名称</th><th>数量</th><th class="hidden-xs">购买时间</th><th>状态</th><th>操作</th></tr></thead>
					<tbody id="list">
					</tbody>
					</table>
				</div>
			</div><br/>
   </div>
<!--查询订单-->
<!--查单说明开始-->
<!--开通分站-->
    <?php if($conf['fenzhan_buy']==1){?><div class="tab-pane" id="Substation">
	<table class="table table-borderless animated bounceIn" style="text-align: center;">
    <tbody>
      <tr class="active">
        <td>
          <h4>
            <span style="font-weight:bold">
                <span style="font-family: YourFontName">
              <font color="#FF8000">搭</font>
              <font color="#EC6D13">建</font>
              <font color="#D95A26">属</font>
              <font color="#C64739">于</font>
              <font color="#A0215F">自</font>
              <font color="#8D0E72">己</font>
              <font color="#5400AB">的</font>
              <font color="#4100BE">商</font>
              <font color="#2E00D1">城</font></span>
          </h4>
        </td>
      </tr>
      <tr class="active">
        <td>学生/上班族/创业/休闲赚钱必备工具</td></tr>
      <tr class="active">
        <td>
          <strong>
            网站轻轻松松推广日赚上千元不是梦</strong></td>
      </tr>
            <tr class="active">
        <td><span class="glyphicon glyphicon-magnet"></span>&nbsp;快加入我们成为大家庭中的一员吧<hr> <a href="#userjs" data-toggle="modal" class="btn btn-effect-ripple  btn-info btn-sm" style="float:left;overflow: hidden; position: relative;">
            <span class="glyphicon glyphicon-eye-open"></span>&nbsp;网站详情介绍</a>
          <a href="./user/regsite.php" target="_blank" class="btn btn-effect-ripple  btn-success btn-sm" style="float:right;overflow: hidden; position: relative;">
            <span class="glyphicon glyphicon-share-alt"></span>&nbsp;免费开通网站</a></td></tr>
      <tr>
    </tbody>
  </table>
	</div><?php }?>
<!--开通分站-->
<!--查单说明开始-->
<!--抽奖-->
    <?php if($conf['gift_open']==1){?><div class="tab-pane" id="gift">
		<div class="panel-body text-center">
		<div id="roll">点击下方按钮开始抽奖</div>
		<hr>
		<p>
		<a class="btn btn-info" id="start" style="display:block;">开始抽奖</a>
		<a class="btn btn-danger" id="stop" style="display:none;">停止</a>
		</p> 
		<div id="result"></div><br/>
		<div class="giftlist" style="display:none;"><strong>最近中奖记录</strong><ul id="pst_1"></ul></div>
		</div>
	</div><?php }?>
<!--抽奖-->
<!--更多-->
    <div class="tab-pane fade fade-right" id="more">
				<?php if($conf['gift_open']==1){?><div class="col-xs-6 col-sm-4 col-lg-4">
                    <a class="block block-link-hover2 text-center" href="#gift" data-toggle="tab">
                        <div class="block-content block-content-full bg-info">
                            <i class="fa fa-gift fa-3x text-white"></i>
                            <div class="font-w600 text-white-op push-15-t">抽奖</div>
                        </div>
                    </a>
                </div><?php }?>

				<?php if(!empty($conf['appurl'])){?><div class="col-xs-6 col-sm-4 col-lg-4">
                    <a class="block block-link-hover2 text-center" href="<?php echo $conf['appurl']; ?>" target="_blank">
                        <div class="block-content block-content-full bg-success">
                            <i class="fa fa-cloud-download fa-3x text-white"></i>
                            <div class="font-w600 text-white-op push-15-t">APP下载</div>
                        </div>
                    </a>
                </div><?php }?>
         
                <?php if(!empty($conf['daiguaurl'])){?><div class="col-xs-6 col-sm-4 col-lg-4">
                    <a class="block block-link-hover2 text-center" href="./?mod=daigua">
                        <div class="block-content block-content-full bg-primary">
                            <i class="fa fa-rocket fa-3x text-white"></i>
                            <div class="font-w600 text-white-op push-15-t">等级代挂</div>
                        </div>
                    </a>
                </div><?php }?>
				<?php if(!empty($conf['invite_tid'])){?><div class="col-xs-6 col-sm-4 col-lg-4">
					<a class="block block-link-hover2 text-center" href="./?mod=invite" target="_blank">
						<div class="block-content block-content-full bg-warning">
						  <i class="fa fa-paper-plane-o fa-3x text-white"></i>
							<div class="font-w600 text-white-op push-15-t">免费领赞</div>
						  </div>
				
				</div><?php }?>

                <div class="col-xs-6 col-sm-4 col-lg-4">
                    <a class="block block-link-hover2 text-center" href="./user/" target="_blank">
                        <div class="block-content block-content-full bg-city">
                            <i class="fa fa-certificate fa-3x text-white"></i>
                            <div class="font-w600 text-white-op push-15-t">用户中心</div>
                        </div>
                    </a>
                </div>
	</div>
<!--更多-->
<!--查单说明开始-->
<!--版本介绍-->
<?php if($conf['fenzhan_buy']==1){?><div class="modal fade" align="left" id="userjs" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<h4 class="modal-title" id="myModalLabel"><span style="font-family: YourFontName">版本介绍</h4>
		</div>
		<div class="block">
            <div class="table-responsive">
                <table class="table table-borderless table-vcenter">
                    <thead>
                        <tr>
                            <th style="width: 100px;"><span style="font-family: YourFontName">功能</th>
                            <th class="text-center" style="width: 20px;"><span style="font-family: YourFontName">普及版/专业版</th>
                        </tr>
                    </thead>
					<tbody>
						<tr class="active">
                            <td>专属商城平台</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
                        <tr class="">
                            <td>三种在线支付接口</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="success">
                            <td>专属网站域名</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>赚取用户提成</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="info">
                            <td>赚取下级分站提成</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>设置商品价格</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="warning">
                            <td>设置下级分站商品价格</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="">
                            <td>搭建下级分站</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
						<tr class="danger">
                            <td>赠送专属精致APP</td>
                            <td class="text-center">
								<span class="btn btn-effect-ripple btn-xs btn-danger"><i class="fa fa-close"></i></span>
								<span class="btn btn-effect-ripple btn-xs btn-success"><i class="fa fa-check"></i></span>
							</td>
                        </tr>
                    </tbody>
                </table>
            </div>
				<center style="color: #b2b2b2;"><small><em>* 自己的能力决定着你的收入！</em></small></center>
        </div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			</div></div></div>
</div><?php }?>
<!--版本介绍-->
    </div>
</div>

<!--关于我们弹窗-->
<div class="modal fade" align="left" id="customerservice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
			<h4 class="modal-title" id="myModalLabel">客服与帮助</h4>
		</div>
		<div class="modal-body" id="accordion">
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">为什么订单显示已完成了却一直没到账？</a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse in" style="height: auto;">
					<div class="panel-body">
					订单显示（已完成）就证明已经提交到服务器内！<br>
					如果长时间没到账请联系客服处理！<br>
					订单长时间显示（待处理）请联系客服！
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed">商品什么时候到账？</a>
					</h4>
				</div>
				<div id="collapseTwo" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">
					请参考商品简介里面，有关于到账时间的说明。
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed">卡密/CDK没有发送我的邮箱？</a>
					</h4>
				</div>
				<div id="collapseThree" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body">没有收到请检查自己邮箱的垃圾箱！也可以去查单区：输入自己下单时填写的邮箱进行查单。<br>
					查询到订单后点击（详细）就可以看到自己购买的卡密/cdk！
					</div>
				</div>
			</div>
			<div class="panel panel-default" style="margin-bottom: 6px;">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseFourth" class="collapsed">已付款了没有查询到我订单？</a>
					</h4>
				</div>
				<div id="collapseFourth" class="panel-collapse collapse" style="height: 0px;">
					<div class="panel-body" style="margin-bottom: 6px;">联系客服处理，请提供（付款详细记录截图）（下单商品名称）（下单账号）<br>直接把三个信息发给客服，然后等待客服回复处理（请不要发抖动窗口或者QQ电话）！
					</div>
				</div>
			</div>
			<ul class="list-group" style="margin-bottom: 0px;">
			<li class="list-group-item">   
			   <div class="media">
					<span class="pull-left thumb-sm"><img src="/assets/img/user.png" alt="..." class="img-circle img-thumbnail img-avatar"></span>
			   <div class="pull-right push-15-t">
					<a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq'] ?>&site=qq&menu=yes" target="_blank"  class="btn btn-sm btn-info">联系</a>
			   </div>
			   <div class="pull-left push-10-t">
					<div class="font-w600 push-5">订单售后客服</div>
					<div class="text-muted"><b>QQ：<?php echo $conf['kfqq'] ?></b>
					</div>
			   </div>
			   </div>
			</li>
			<li class="list-group-item">
			想要快速回答你的问题就请把问题描述讲清楚!<br>
			下单账号+业务名称+问题，直奔主题，按顺序回复!<br>
			有问题直接留言，请勿抖动语音否则直接无视。<br>			
			</li>
			</ul>
		</div>
    </div>
  </div>
</div>
<!--关于我们弹窗-->

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
<!--文章列表-->
<div class="block block-themed" style="box-shadow:0 5px 10px 0 rgba(0, 0, 0, 0.25);">
	<div class="block-header bg-amethyst" style="background-color: #6a67c7; border-color: #6a67c7; padding: 10px 10px;">
		<h3 class="block-title"><i class="fa fa-newspaper-o"></i> 文章列表</h3>
	</div>
	<?php foreach($msgrow as $row){
	echo '<a target="_blank" class="list-group-item" href="'.article_url($row['id']).'"><span class="btn btn-'.$class_arr[($i++)%5].' btn-xs">'.$i.'</span>&nbsp;'.$row['title'].'</a>';
	}?>
	<a href="<?php echo article_url()?>" title="查看全部文章" class="btn-default btn btn-block" target="_blank">查看全部文章</a>
</div>
<!--文章列表-->
<?php }?>
<!--查单说明开始-->
<div class="block block-content block-content-mini block-content-full" style="box-shadow:0px 5px 10px 0 rgba(0, 0, 0, 0.25);">
	<!--网站日志-->
	<?php if(!$conf['hide_tongji']){?>

	<div class="row text-center">
		
	<a href="./user/regsite.php"><img src="D:/wangzhan/127.0.0.2/assets/vendor/pan.suyanw.cn/view.php/d2982dd3a1cef3a37c447be73f25eaaf.png" width="100%"></a>
	<div class="col-xs-4">
				</div>
</div>
	<?php }?>
	<!--网站日志-->
	<!--底部导航-->
	<div class="block-content text-center ">
	   <a href="javascript:void(0);" onclick="AddFavorite('<?php echo $conf['sitename']?>',location.href)">
  <b style="text-shadow: LightSteelBlue 1px 0px 0px;">
      
      <h5 class="widget-heading"><small><span style="font-family: YourFontName">订单总数</small><br><a href="javascript:void(0)" class="themed-color-flat"><span style="font-family: YourFontName"><span id="count_orders"></small></span></h5>
    </br>
			<h5 class="widget-heading"><small><span style="font-family: YourFontName">运营天数</small><br>
			<a href="javascript:void(0)" class="themed-color-flat"><span style="font-family: YourFontName"><span id="count_yxts"></span>天</a></h5>
	</br>
  本站网址：<?php echo $_SERVER['HTTP_HOST'];?></font>
  </b>
</a>
<br/><?php echo $conf['footer']?>
	</div>
	<!--底部导航-->
</div>
</div>
<!-- 收藏代码开始-->
<script>
    function AddFavorite(title, url) {
  try {
      window.external.addFavorite(url, title);
  }
catch (e) {
     try {
       window.sidebar.addPanel(title, url, "");
    }
     catch (e) {
         alert("手机用户：点击底部 “≡” 添加书签/收藏网址!\n\n电脑用户：请您按 Ctrl+D 手动收藏本网址! ");
     }
  }
}
</script>
<!-- 收藏代码结束-->

<!--音乐代码-->
<div id="audio-play" <?php if(empty($conf['musicurl'])){?>style="display:none;"<?php }?>>
  <div id="audio-btn" class="on" onclick="audio_init.changeClass(this,'media')">
    <audio loop="loop" src="<?php echo $conf['musicurl']?>" id="media" preload="preload"></audio>
  </div>
</div>
<!-- 百度统计脚本已移除（离线化） --></div><script src="/plugins/SeoPlus/assets/main.js?ver=1.0.0"></script><script>(function webpackUniversalModuleDefinition(a,b){if(typeof exports==="object"&&typeof module==="object"){module.exports=b()}else{if(typeof define==="function"&&define.amd){define([],b)}else{if(typeof exports==="object"){exports["POWERMODE"]=b()}else{a["POWERMODE"]=b()}}}})(this,function(){return(function(a){var b={};function c(e){if(b[e]){return b[e].exports}var d=b[e]={exports:{},id:e,loaded:false};a[e].call(d.exports,d,d.exports,c);d.loaded=true;return d.exports}c.m=a;c.c=b;c.p="";return c(0)})([function(c,g,b){var d=document.createElement("canvas");d.width=window.innerWidth;d.height=window.innerHeight;d.style.cssText="position:fixed;top:0;left:0;pointer-events:none;z-index:999999";window.addEventListener("resize",function(){d.width=window.innerWidth;d.height=window.innerHeight});document.body.appendChild(d);var a=d.getContext("2d");var n=[];var j=0;var k=120;var f=k;var p=false;o.shake=true;function l(r,q){return Math.random()*(q-r)+r}function m(r){if(o.colorful){var q=l(0,360);return"hsla("+l(q-10,q+10)+", 100%, "+l(50,80)+"%, "+1+")"}else{return window.getComputedStyle(r).color}}function e(){var t=document.activeElement;var v;if(t.tagName==="TEXTAREA"||(t.tagName==="INPUT"&&t.getAttribute("type")==="text")){var u=b(1)(t,t.selectionStart);v=t.getBoundingClientRect();return{x:u.left+v.left,y:u.top+v.top,color:m(t)}}var s=window.getSelection();if(s.rangeCount){var q=s.getRangeAt(0);var r=q.startContainer;if(r.nodeType===document.TEXT_NODE){r=r.parentNode}v=q.getBoundingClientRect();return{x:v.left,y:v.top,color:m(r)}}return{x:0,y:0,color:"transparent"}}function h(q,s,r){return{x:q,y:s,alpha:1,color:r,velocity:{x:-1+Math.random()*2,y:-3.5+Math.random()*2}}}function o(){var t=e();var s=5+Math.round(Math.random()*10);while(s--){n[j]=h(t.x,t.y,t.color);j=(j+1)%500}f=k;if(!p){requestAnimationFrame(i)}if(o.shake){var r=1+2*Math.random();var q=r*(Math.random()>0.5?-1:1);var u=r*(Math.random()>0.5?-1:1);document.body.style.marginLeft=q+"px";document.body.style.marginTop=u+"px";setTimeout(function(){document.body.style.marginLeft="";document.body.style.marginTop=""},75)}}o.colorful=false;function i(){if(f>0){requestAnimationFrame(i);f--;p=true}else{p=false}a.clearRect(0,0,d.width,d.height);for(var q=0;q<n.length;++q){var r=n[q];if(r.alpha<=0.1){continue}r.velocity.y+=0.075;r.x+=r.velocity.x;r.y+=r.velocity.y;r.alpha*=0.96;a.globalAlpha=r.alpha;a.fillStyle=r.color;a.fillRect(Math.round(r.x-1.5),Math.round(r.y-1.5),3,3)}}requestAnimationFrame(i);c.exports=o},function(b,a){(function(){var d=["direction","boxSizing","width","height","overflowX","overflowY","borderTopWidth","borderRightWidth","borderBottomWidth","borderLeftWidth","borderStyle","paddingTop","paddingRight","paddingBottom","paddingLeft","fontStyle","fontVariant","fontWeight","fontStretch","fontSize","fontSizeAdjust","lineHeight","fontFamily","textAlign","textTransform","textIndent","textDecoration","letterSpacing","wordSpacing","tabSize","MozTabSize"];var e=window.mozInnerScreenX!=null;function c(k,l,o){var h=o&&o.debug||false;if(h){var i=document.querySelector("#input-textarea-caret-position-mirror-div");if(i){i.parentNode.removeChild(i)}}var f=document.createElement("div");f.id="input-textarea-caret-position-mirror-div";document.body.appendChild(f);var g=f.style;var j=window.getComputedStyle?getComputedStyle(k):k.currentStyle;g.whiteSpace="pre-wrap";if(k.nodeName!=="INPUT"){g.wordWrap="break-word"}g.position="absolute";if(!h){g.visibility="hidden"}d.forEach(function(p){g[p]=j[p]});if(e){if(k.scrollHeight>parseInt(j.height)){g.overflowY="scroll"}}else{g.overflow="hidden"}f.textContent=k.value.substring(0,l);if(k.nodeName==="INPUT"){f.textContent=f.textContent.replace(/\s/g,"\u00a0")}var n=document.createElement("span");n.textContent=k.value.substring(l)||".";f.appendChild(n);var m={top:n.offsetTop+parseInt(j["borderTopWidth"]),left:n.offsetLeft+parseInt(j["borderLeftWidth"])};if(h){n.style.backgroundColor="#aaa"}else{document.body.removeChild(f)}return m}if(typeof b!="undefined"&&typeof b.exports!="undefined"){b.exports=c}else{window.getCaretCoordinates=c}}())}])});POWERMODE.colorful=true;POWERMODE.shake=false;document.body.addEventListener("input",POWERMODE);</script>
<!--音乐代码-->

<script src="<?php echo $cdnpublic?>jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery.lazyload/1.9.1/jquery.lazyload.min.js"></script>
<script src="<?php echo $cdnpublic?>twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?php echo $cdnpublic?>jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="<?php echo $cdnpublic?>layer/2.3/layer.js"></script>
<script src="<?php echo $cdnserver?>assets/appui/js/app.js"></script>
<script type="text/javascript">
var isModal=<?php echo empty($conf['modal'])?'false':'true';?>;
var homepage=true;
var hashsalt=<?php echo $addsalt_js?>;
$(function() {
	$("img.lazy").lazyload({effect: "fadeIn"});
});
</script>
<script src="assets/js/main.js?ver=<?php echo VERSION ?>"></script>
<?php if($conf['classblock']==1 || $conf['classblock']==2 && checkmobile()==false)include TEMPLATE_ROOT.'default/classblock.inc.php'; ?>
</body>
</html>