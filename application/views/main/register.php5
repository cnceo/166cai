<?php $this->load->view('elements/common/header_notlogin');?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
<!--
$(function() {

	var userRegForm = new cx.vform('.form_register', {
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
                url:  '/main/register',
                data: data,
                success: function(response) {
                	recaptcha();
                	if(response.code == '200.0'){
                		location.href =  '/main/welcome';
                	}
                	else if(response.werror == 1){
                		console.log(response);
                    }
                	else if(response.captcha == 1){
                    	if(response.actions == 'phone'){
                    		self.renderTip('验证码错误', $('.yyCaptcha'));
                    		$('.vcode-yuyin input[name="yyCaptcha"]').val('');
                    		//$('input[name="yyCaptcha"]').focus();
                        }else{
                        	self.renderTip('验证码错误', $('.captcha'));
                    		//$('input[name="captcha"]').focus();
                        }
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
                	else if(response.code == '300.3'){
                        self.renderTip('此帐号已被注册', $('.username'));
                    }
                    else if(response.field.pcheck == false){
                		self.renderTip('两次输入不一致', $('.con_pword'));
                		$('.captcha').closest('.form-tip').addClass('hide');
                    }
                	else{
						console.log(response);
                    }
                    $('input[name="captcha"]').val('');
                }
            });
        }
    });

    $("#change_captcha").on('click', function(){
        recaptcha();
        return false;
    })
})
-->
</script>
<div class="wrap_in">
	<div class="lay_login">
		<div class="lay_login_hd">
			<h2>注册</h2>
		</div>
		<div class="lay_login_bd">
			<div class="mod_user">			
				<form action="" class="form form_login form_register">
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>2345账号</label>
						<div class="form-item-con">
							<input type="text" class="form-item-ipt vcontent" autocomplete="off" data-rule='username' data-ajaxcheck='1' name="username" value="" />
							<div class="form-tip hide">
								<i class="icon-tip"></i>
								<span class="form-tip-con username tip"></span>
								<s></s>
							</div>
						</div>
						<input class='vcontent' type='hidden' name='actions' value='default' />
					</div>
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>创建密码</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="password" name="pword" data-encrypt='1' data-rule="password" value="" />
							<div class="form-tip hide">
	                            <i class="icon-tip"></i>
								<span class="form-tip-con pword tip"></span>
								<s></s>
							</div>						
						</div>
					</div>
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>确认密码</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="password" name="con_pword" data-encrypt='1' data-rule="same" data-with="pword" value="" />	
							<div class="form-tip hide">
							<i class="icon-tip"></i>
							<span class="form-tip-con con_pword tip"></span>
							<s></s>
						</div>					
						</div>
					</div>
					<!-- <li class="email_area">
						<label class="label_like"><span class="must">*</span>邮箱地址</label>
						<div class="con pos">
							<input class='ipt_text vcontent' data-rule="email" type="text" name="email" value="" />
						</div>
						<div class="form_tips" style="display:none;">
							<i class="tips_icon"></i>
							<div class="tips_con tip email"></div>
						</div>
					</li> -->
					<!-- 图片验证码 -->
					<div class="form-item form-vcode vcode-img">
						<label for="" class="form-item-label"><b>*</b>图形验证码</label>
						<div class="form-item-con">
							<input class='form-item-ipt inp_s vcontent' type="text" name="captcha" data-rule='checkcode' value="" /><img id='captcha' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
							<a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha">换一张</a>
							<div class="form-tip hide">
								<i class="icon-tip"></i>
								<span class="form-tip-con tip captcha"></span>
								<s></s>
							</div>
						</div>
					</div>

					<!-- 手机语音验证码 -->
                    <div class="form-item form-vcode vcode-yuyin">
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" name="yyCaptcha" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
                            <a href="javascript:;" id='btn-getYzm' data-freeze='phone' class="lnk-getvcode _timer">获取语音验证码</a>
                            <span href="javascript:;" class="lnk-getvcode-disb hide">重新发送(<em id='_timer'>60</em>秒)</span>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con yyCaptcha tip"></span>
                                <s></s>
                            </div>
                            <div style="left: 0; top: 46px;" class="ui-poptip ui-poptip-yuyin">
                              <div class="ui-poptip-container">
                                <div class="ui-poptip-arrow-top"> <i>◆</i> <span>◆</span> </div>
                                系统将拨打您的手机语音播报验证码，请注意接听。<a href="/help/index/b0-f4">未收到验证码？</a>
                              </div>
                            </div>
                        </div>
                    </div>
					<div class="form-item btn-group">
						<div class="form-item-con">
							<a class="btn btn-confirm-large submit" href="javascript:;" target="_self">立即注册</a>
							<span class="go-reg">已有2345账号，<a href="/main/login">立即登录</a></span>
						</div>
					</div>
					<div class="form-item form-agree">
						<div class="form-item-con">
							<input class="ipt_checkbox vcontent" type="checkbox" id="a" name='agreement' value='1' checked="checked"/> <label for="a">我同意</label><a href="http://login.2345.com/licence.html" target='_blank'>《服务协议》</a><a href="http://login.2345.com/declare.html" target='_blank'>《隐私声明》</a>
						</div>
					</div>
                    <div class="form-item other-login">
                    	<div class="form-item-con">
                    		<a class="btn btn-qq" target="_self" href="http://login.2345.com/qq?forward=<?php echo $this->baseUrl; ?>main/loginReCall"><i class="icon icon-qq"></i>QQ账号登录</a>
					    	<a class="btn btn-weibo" href="http://login.2345.com/weibo?forward=<?php echo $this->baseUrl; ?>main/loginReCall"><i class="icon icon-weibo"></i>微博登录</a>
                    	</div>
				    </div>
				</form>
			</div>
		</div>
		<div class="act-total">
			<div class="money-num"><em><?php echo implode('</em><em>', $total_win);?></em>万元</div>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function() {
	$('input[name="username"]').blur(function() {
		var pattern = /^1\d{10}$/;
		var pattern1 = /^\w+[\w]*@[\w]+\.[\w]+$/;
		var username = $('input[name="username"]').val();
		if(pattern.test(username)){
	        $(".vcode-yuyin").show();
	        $('input[name="actions"]').val('phone');
	        $('.vcode-yuyin input[name="captcha"]').val('');
		}else if(pattern1.test(username)){
			$(".vcode-yuyin").hide();
	        $(".vcode-img").show();
	        $('input[name="actions"]').val('email');
	        $('.vcode-yuyin input[name="yyCaptcha"]').val('');
		}else{
			$(".vcode-yuyin").hide();
	        $(".vcode-img").show();
	        $('input[name="actions"]').val('default');
	        $('.vcode-yuyin input[name="yyCaptcha"]').val('');
		}
	});
	$('#btn-getYzm').click(function(){
        var phone = $('input[name="username"]').val();
        if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/))
        {
            if($(".username").parent().hasClass('form-tip-error')){
                return false;
            }
            var code = $('input[name="captcha"]').val() || false;
            if(!code){
        		$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
            	$('.captcha').show().html('请输入图形验证码');
            	return false;
            }
            
            $.ajax({
                type: 'post',
                url:  '/main/getPhoneCode/phoneCaptcha',
                data: {'phone':phone,'code':code},
                dataType: 'json',
                success: function(response) {
                    if(response.status)
                    {
                    	timer();
                        //cx.Alert({content:'验证码已发送你的手机！'});
                    }
                    else
                    {
                        if(response.msg){
                        	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                        	$('input[name="captcha"]').val('');
                        	$('.captcha').show().html(response.msg);
                        	recaptcha();
                        }else{
                        	$('.captcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                        	cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                            closeTimer(1);
                        }
                    }
                }
            });
        }
    });
})
</script>
<?php $this->load->view('elements/common/footer_short');?>
