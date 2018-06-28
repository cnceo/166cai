<?php $this->load->view('elements/common/header_notlogin'); ?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<div class="wrap_in other-container">
    <div class="lay-find">
    	<div class="hd"><h2 class="tit">找回密码</h2></div>
    	<div class="bd">
    		<ul class="steps-bar clearfix">
    			<li><i>1</i><span class="des">输入2345账号</span></li>
    			<li><i>2</i><span class="des">验证身份</span></li>
    			<li><i>3</i><span class="des">设置密码</span></li>
    			<li class="last cur"><i>4</i><span class="des">操作失败</span></li>
    		</ul>
    		<div class="find-form">
    			<div class="find-success">
    				<div class="sc-tip"></i>链接已失效或修改密码信息错误</div>
    				<p><a href="/" class="btn btn-blue-med not-login">重新登录</a></p>
    			</div>
    		</div>
    	</div>
    </div>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<?php $this->load->view('elements/common/footer_short'); ?>


