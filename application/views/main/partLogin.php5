<?php $this->load->view('elements/common/header_notlogin');?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
<!--
$(function() {

	new cx.vform('.form_register', {
		renderTip: 'renderTips',
        submit: function(data) {
            if(data.agreement != '1'){
                cx.Alert({
                        content: '请选择同意《服务协议》《隐私声明》'
                });
                return false;
            }
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/main/partLogin',
                data: data,
                success: function(response) {
                	if(response.code == '200.0'){
                		location.href =  '/';
                	}
                	else if(response.werror == 1){
                		console.log(response);
                    }
                	else if(response.captcha == 1){
                        self.renderTip('验证码错误', $('.captcha'));
                		$('input[name="captcha"]').focus();
                    }
                	else if(response.code == '300.6'){
                        self.renderTip('此帐号已被注册', $('.username'));
                    }
                	else if(response.code == '300.7'){
                        self.renderTip('此邮箱已被注册，请换一个', $('.email'));
                    }
                	else if(response.code == '300.1'){
                        self.renderTip('此帐号已注册成功，但被锁定 ', $('.username'));
                    }
                	else{
						console.log(response);
                    }
                    $('input[name="captcha"]').val('');
                }
            });
        }
    });

})
-->
</script>

<div class="wrap_in">
	<div class="lay_login partner-login">
		<div class="lay_login_hd">
			<h2>合作账号登录</h2>
			<em>请设置您的购彩用户名</em>
		</div>
		<div class="lay_login_bd">
			<div class="mod_user">			
				<ul class="form form_login form_register">
					<li class="form-item">
						<label class="form-item-label">购彩用户名</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="text" autocomplete="off" data-rule='username' data-ajaxcheck='1'  name="username" value="" />
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con username tip"></span>
                                <s></s>
                            </div>
						</div>
						<input class='vcontent' type='hidden' name='actions' value='bandname' />
					</li>
					<li class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn-confirm-large submit" href="javascript:;" target="_self">立即登录</a>
                        </div>
					</li>
					<li class="form-item form-agree">
                        <div class="form-item-con">
                            <input class="ipt_checkbox vcontent" type="checkbox" id="a" name='agreement' value='1' checked="checked"/> <label for="a">我同意</label><a href="http://login.2345.com/licence.html" target='_blank'>《服务协议》</a><a href="http://login.2345.com/declare.html" target='_blank'>《隐私声明》</a>
                        </div>
					</li>
				</ul>
			</div>
		</div>
		<div class="have-user">
			<span class="go_reg">已有2345账号，<a href="/main/login/part">立即登录</a></span>
		</div>
		<div class="act-total">
			<div class="money-num"><em>8</em><em>4</em><em>7</em><em>3</em>万元</div>
		</div>
	</div>
</div>
<?php $this->load->view('elements/common/footer_short');?>
