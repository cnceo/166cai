<?php $this->load->view('elements/common/header_notlogin'); ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<div class="wrap_in other-container">
    <div class="lay-find">
    	<div class="hd"><h2 class="tit">修改邮箱</h2></div>
    	<div class="bd">
    		<div class="find-form">
    			<?php if($callback=='1'): ?>
    			<div class="find-success">
    				<div class="sc-tip" style="margin-left: -355px; text-align: center;"></i>修改邮箱成功</div>
    				<p style="padding: 0;margin-left: -355px;text-align: center;"><a href="/" class="btn btn-blue-med not-login">重新登录</a></p>
    			</div>
    			<?php else: ?>
    			<div class="find-success">
    				<div class="sc-tip"></i>链接已失效或修改密码信息错误</div>
    				<p><a href="/" class="btn btn-blue-med not-login">重新登录</a></p>
    			</div>
    			<?php endif; ?>
    		</div>
    	</div>
    </div>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<?php $this->load->view('elements/common/footer_short'); ?>