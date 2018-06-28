<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<title>支付等待-166彩票网</title>
<meta content="" name="Description">
<meta content="" name="Keywords">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<?php if($refresh):?>
<meta http-equiv="refresh" content="2">
<?php endif;?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>"/>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js'); ?>" type="text/javascript" ></script>
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
    	<h1 class="header-title">支付等待</h1>
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
            <div class="mod-result">
                <div class="mod-result-bd">
                    <i class="icon-font icon-result" style="background:none;">&#xe648;</i>
                    <div class="result-txt">
                        <h2 class="result-txt-title">等待支付结果反馈中···</h2>
                        <p class="result-txt-tips">如有疑问可直接电话联系客服—4006906760</p>
                    </div>
                </div>
                <div class="mod-result-ft">
                    <div class="btn-group">
                        <a href="/hall" class="btn btn-main">继续预约</a>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('v1.1/elements/common/appdownload');?>
    </div>
</div>
<?php $this->load->view('v1.1/elements/common/footer_short');?>