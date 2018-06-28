<?php if(empty($status)): ?>
<?php $this->load->view('elements/common/header_notlogin'); ?>
<?php endif; ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<!-- body -->
<div class="wrap_in other-container">
	<div class="lay-find">
	<div class="hd"><h2 class="tit">找回2345帐号</h2></div>
	<div class="bd">
		<div class="find-form">
			<div class="find-success">
				<div class="sc-tip"></i>您的2345帐号为：<?php echo $uname ;?></div>
				<p><a href="/main/login" class="btn btn-blue-med not-login">立即登录</a></p>
			</div>
		</div>
	</div>
</div>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>

<!--[if IE 6]>
<script src="/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<?php if(empty($status)): ?>
<?php $this->load->view('elements/common/footer_short'); ?>
<?php endif; ?>








