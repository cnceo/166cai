<?php $position = $this->config->item('POSITION')?>
<!-- 注册 begin -->
<div class="pub-pop pop-register pop-w-max">
	<div class="pop-in">
		<div class="pop-head">
			<h2>166彩账号注册</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<form class="form form-register">
	<div class="form-item">
		<label class="form-item-label">用户名：</label>
		<div class="form-item-con"><input class="form-item-ipt vcontent" type="text" autocomplete="off" data-rule='username' data-ajaxcheck='1' name="username" value="" />
			<div class="form-tip">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip username"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item">
		<label class="form-item-label">密码：</label>
		<div class="form-item-con">
			<input class="form-item-ipt pswcheck vcontent" type="password" name="pword" data-rule="password" data-encrypt="1" value="" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<div class="form-tip-con tip pword"></div>
			</div>
		</div>
	</div>
	<div class="form-item">
		<label class="form-item-label">确认密码：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="password"  name="con_pword" data-rule="same" data-encrypt="1" data-with="pword" value="" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip con_pword"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item email_area">
		<label class="form-item-label">验证手机：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="text" data-rule="phonenum" name="phone" data-ajaxcheck='1' value="" />
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip phone"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item form-vcode vcode-img">
		<label class="form-item-label">图形验证码：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" type="text" name="imgCaptcha" data-rule="checkcode" data-ajaxcheck='1' value="" /><img id="captcha_reg" src="/mainajax/captcha?v=<?php echo time();?>" width="68" height="30" alt="" /><a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha_reg">换一张</a>
			<div class="form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con tip imgCaptcha"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item form-vcode vcode-yuyin">
        <label class="form-item-label">手机验证码：</label>
        <div class="form-item-con">
            <input type="text" name="phoneCaptcha" data-rule="checkcode" value="" class="form-item-ipt vyzm vcontent">
            <a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer" target="_self">获取验证码</a>
            <span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
            <div class="form-tip hide">
                <i class="icon-tip"></i>
                <span class="form-tip-con tip phoneCaptcha"></span>
                <s></s>
            </div>
        </div>
    </div>
    <div class="form-item">
		<div class="form-item-con">
			<div class="form-tip hide">
				<div class="form-tip-con commErr">注册失败</div>
			</div>
		</div>
	</div>
	<div class="form-item btn-group">
		<div class="form-item-con">
			<a class="btn btn-main submit" href="javascript:;" target="_self">立即注册</a>
			<span class="go-login">已有账号，<a href="/main/login" class="go-login-lnk">立即登录</a></span>
		</div>
	</div>
	<div class="form-item form-agree">
		<div class="form-item-con">
			<input class="form-item-checkbox vcontent" type="checkbox" id="agree" name="agreement" value="1" checked="checked" /> <label for="agree">我同意</label><a href="/main/serviceAgreement" target="_blank">《166彩票网服务协议》</a>
		</div>
	</div>
</form>
		</div>
	</div>
</div>
<!-- 注册 end -->
<script type="text/javascript">
$(function() {
	$('#btn-getYzm').click(function(){
		var self = $(this);
        var phone = $('.form-register input[name="phone"]').val();
        if(!phone){
        	$('.phone').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
        	$('.phone').show().html('请输入手机号码');
        	return false;
        }
        var code = $('.form-register input[name="imgCaptcha"]').val();
        if(!code){
        	$('.imgCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
        	$('.imgCaptcha').show().html('请输入图形验证码');
        	return false;
        }
        var captchaErr = $('.imgCaptcha').closest('.form-tip').hasClass('form-tip-error');
        var phoneErr = $('.phone').closest('.form-tip').hasClass('form-tip-error');
        if(!captchaErr && !phoneErr)
        {   
        	timer(self);	
            $.ajax({
                type: 'post',
                url:  '/main/getPhoneCode/registerCaptcha',
                data: {'phone':phone, 'position':<?php echo $position['register_captche']?>, 'code':code},
                dataType: 'json',
                success: function(response) {
                    if(response.status){
                    	//timer(self);
                        //cx.Alert({content:'验证码已发送你的手机！'});
                    }else{
                    	recaptcha_reg();
                        if(response.msg){
                    		cx.Alert({content:response.msg});
                    	}else{
                    		cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                    	} 
                    }
                }
            });
        }
    });
	
	new cx.vform('.form-register', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
        submit: function(data) {
            if(data.agreement != '1'){
                cx.Alert({
                        content: '请同意服务协议'
                });
                return false;
            }
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/mainajax/register',
                data: data,
                success: function(response) {
                	recaptcha_reg();
                	if(response.code == '0'){
                        $('.not-login').removeClass('not-login');
                        $(".pop-mask").hide();
                        cx.PopCom.hide('.pop-register');

                        $.get('/pop/getWelcome?version=' + version, function(data){
                        	$('body').append(data);
                        	cx.Mask.show();
                        	$('.pop-welcome').css({marginTop : (-$('.pop-welcome').height()/2), marginLeft : (-$('.pop-welcome').width()/2) }).show();
                        	//注册移除欢迎弹层
                        	$('.pop-welcome').find('.pop-close').click(function() {
                            	cx.PopCom.hide('.pop-welcome');
                            });
                        });

                        $.get('/mainajax/getLoginAjax?version=' + version, function(data){
                        	$('.top_bar').html(data.topBar);
                        	if( $('.fast-login') ){
                            	$('.fast-login').html(data.fastLogin);
                            }
                        	// 获取其绑定情况
                            if(data.bindPop){
                            	$('.submit').addClass('not-bind');
                                $('.btn-betting').addClass('not-bind');
                            }
                        },'json');
                	}else{
                    	//注册失败
                		self.renderTip(response.msg, $('.commErr'));
                   	}
                    $('input[name="phoneCaptcha"]').val('');
                }
            });
        }
    });

    $("#change_captcha_reg").on('click', function(){
        recaptcha_reg();
        return false;
    });
});
</script>