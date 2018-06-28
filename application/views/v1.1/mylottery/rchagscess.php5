<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title><?php if($statusFlag):?>操作成功<?php else:?>操作失败<?php endif;?>-166彩票网</title>
<meta content="" name="Description">
<meta content="" name="Keywords">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script type="text/javascript">
        	var version = 'v1.1';
        	var visitor = {userNickname:'<?php echo empty($this->uid) ? '未登录用户' : $this->uinfo['uname']?>'};
			window.easemobim = window.easemobim || {};
			easemobim.config = {visitor: visitor};
        </script>
        <script src='//kefu.easemob.com/webim/easemob.js'></script>
</head>
<body>

<!--top begin-->
<?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
<!--top end-->
<!--header begin-->
<div class="header header-short">
  <div class="wrap header-inner">
    <div class="logo">
    	<div class="logo-txt">
			<span class="logo-txt-name"></span>	
    	</div>
    	<a href="/" class="logo-img">
    		<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166.png');?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/logo/logo-166@2x.svg');?> 2x" width="280" height="70" alt="166彩票网">
    	</a>
    	<h1 class="header-title"><?php if($orderId):?>支付<?php else: ?>充值<?php endif;?></h1>
    </div>
    <div class="aside">
    	<a href="javascript:;" onclick="easemobim.bind({tenantId: '38338'})" class="btn-specail online-service" target="_self"><i class="icon-font">&#xe634;</i>在线客服</a>
    	<p class="telphone"><i class="icon-font">&#xe633;</i>客服热线：<em>400-690-6760</em></p>
    </div>
  </div>
</div>
<div class="wrap_in l-concise l-concise-col p-pay-result">

    <div class="l-concise-bd register-resulte">
        <div class="l-concise-main">
            <?php if($orderId):?>
            <div class="mod-result <?php if($statusFlag):?>result-success<?php endif;?>"">
                <div class="mod-result-bd">
                	<?php if($statusFlag):?>
                    <i class="icon-font icon-result">&#xe646;</i>
                    <div class="result-txt">
                        <?php if($orderType!=4){ ?>
                        <h2 class="result-txt-title">恭喜您，<em class="main-color-s">支付成功</em>。我们将尽快为您出票</h2>
                        <?php if(empty($this->uinfo['email'])):?>
                        <p style="font-size: 18px"><i class="icon-font">&#xe64b;</i><a href="/safe/bindEmail/" class="sub-color" target="_blank">绑定邮箱</a>，第一时间获取出票凭证</p>
                        <?php endif;?>
                        <?php }else{?>
                        <h2 class="result-txt-title">恭喜您，<em class="main-color-s">定制成功</em>。您可查看详情进行确认</h2>
                        <?php } ?>
                    </div>
                    <?php else :?>
                    <i class="icon-font icon-result" style="background:none;">&#xe647;</i>
                    <div class="result-txt">
                        <?php if($orderType!=4){ ?>
                        <h2 class="result-txt-title">订单支付失败，如已完成扣款可查看充值记录。</h2>
                        <?php }else{?>
                        <h2 class="result-txt-title">定制失败，如已完成扣款将退款至您的账户。</h2>
                        <?php } ?>
                    </div>
                    <?php endif;?>
                </div>
                <div class="mod-result-ft">
                    <div class="btn-group">
                        <?php if($orderType!=4){ ?>
                        <a href="/hall" class="btn">继续预约</a>
                        <?php }else{?>
                        <a href="/gendan" class="btn">继续预约</a>
                        <?php } ?>
                        <?php switch ($orderType) {
	                        case 3:?>
	                        <a href="/hemai/detail/hm<?php echo $orderId?>" class="btn btn-main">查看详情</a>
	                    <?php break;
	                        case 4:?>
                                <?php if($statusFlag){ ?>
                                <a href="/hemai/gdetail/gd<?php echo $orderId; ?>" class="btn btn-main">查看详情</a>
                                <?php }else{ ?>
	                        <a href="/mylottery/recharge" class="btn btn-main">查看详情</a>
                                <?php } ?>
	                    <?php break;                        
                        	case 1:?>
                        	<a href="/chases/detail/<?php echo $orderId?>" class="btn btn-main">查看详情</a>
                        <?php break;
                        	default:?>
                        	<a href="/orders/detail/<?php echo $orderId?>" class="btn btn-main">查看详情</a>
                        <?php break;
                        }?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="mod-result <?php if($statusFlag):?>result-success<?php endif;?>">
                <div class="mod-result-bd">
                	<?php if($statusFlag):?>
                    <i class="icon-font icon-result">&#xe646;</i>
                    <div class="result-txt">
                        <h2 class="result-txt-title">恭喜您，<em class="main-color-s">成功充值<?php echo $money?>元</em>。请放心购彩</h2>
                    </div>
                    <?php else :?>
                    <i class="icon-font icon-result" style="background:none;">&#xe647;</i>
                    <div class="result-txt">
                        <h2 class="result-txt-title">订单支付失败，如已完成扣款可查看充值记录。</h2>
                    </div>
                    <?php endif;?>
                </div>
                <div class="mod-result-ft">
                    <div class="btn-group">
                        <a href="/mylottery/recharge" class="btn">查看详情</a>
                        <a href="/hall" class="btn btn-main">继续预约</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php $this->load->view('v1.1/elements/common/appdownload');?>
    </div>
</div>
<?php $this->load->view('v1.1/elements/common/footer_short');?>
