<?php $position = $this->config->item('POSITION')?>
<style>
.captcha_div{
	display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
    *display: inline;
    *zoom: 1;
}
</style>
<div class="wrap_in l-concise l-concise-col">
	<!-- 注册 start -->
	<div class="l-concise-bd register">
		<div class="l-concise-main">
	<form class="nform form-register">
	<div class="nform-item">
		<label class="nform-item-label">手机号码：</label>
		<div class="nform-item-con">
			<input class="nform-item-ipt vcontent" type="text" data-rule="phonenum" placeholder="注册后手机号可直接登录" c-placeholder="注册后手机号可直接登录" autocomplete="off" name="phone" data-ajaxcheck='1' value="" />
			<div class="nform-tip hide">
				<i class="icon-tip"></i>
				<span class="nform-tip-con tip phone"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="nform-item">
        <label class="nform-item-label">验证码：</label>
        <div class="nform-item-con">
            <div class="captcha_div"></div>
            <div class="nform-tip hide">
				<i class="icon-tip"></i>
				<span class="nform-tip-con tip captcha"></span>
				<s></s>
			</div>
        </div>
    </div>
	<div class="nform-item nform-vcode">
        <label class="nform-item-label">手机验证码：</label>
        <div class="nform-item-con">
            <input type="text" name="phoneCaptcha" data-rule="checkcode" placeholder="请输入验证码  " c-placeholder="请输入验证码" autocomplete="off" value="" class="nform-item-ipt vyzm vcontent">
            <a href="javascript:;" onclick="_hmt.push(['_trackEvent', 'register', 'get_captcha']);" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer btn-disabled" target="_self">获取验证码</a>
            <span class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
            <div class="nform-tip hide">
                <i class="icon-tip"></i>
                <span class="nform-tip-con tip phoneCaptcha"></span>
                <s></s>
            </div>
        </div>
    </div>
	<div class="nform-item">
		<label class="nform-item-label">密码：</label>
		<div class="nform-item-con">
			<input class="nform-item-ipt pswcheck vcontent" type="password" name="pword" data-rule="password" placeholder="建议使用字母和数字组合" autocomplete="off" data-encrypt="1" value="" />
			<div class="nform-tip hide">
				<i class="icon-tip"></i>
				<div class="nform-tip-con tip pword"></div>
				<s></s>
			</div>
		</div>
	</div>
	<div class="nform-item">
		<label class="nform-item-label">确认密码：</label>
		<div class="nform-item-con">
			<input class="nform-item-ipt vcontent" type="password" name="con_pword" placeholder="请再次输入密码 " autocomplete="off" data-rule="same" data-encrypt="1" data-with="pword" value="" />
			<div class="nform-tip hide">
				<i class="icon-tip"></i>
				<span class="nform-tip-con tip con_pword"></span>
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
			<a class="btn btn-main submit" onclick="_hmt.push(['_trackEvent', 'register', 'register_button']);" href="javascript:;">立即注册</a>
			<span class="go-login">已有账号，<a href="/main/login" class="go-login-lnk">立即登录</a></span>
		</div>
	</div>
	<div class="form-item form-agree">
		<div class="form-item-con">
			<input class="form-item-checkbox vcontent" type="checkbox" name="agreement" value="1" checked id="agree" /> <label for="agree">我同意</label><a href="/main/serviceAgreement" target="_blank">《166彩票网服务协议》</a>
		</div>
	</div>
</form>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<?php $this->load->view('v1.1/elements/netimer');?>
<!-- 注册 end -->
<script type="text/javascript">
var phone, validate = '', verify = function(err, ret){
	phone = $('.form-register [name="phone"]').val();
	if (!/^\d{11}$/.test(phone)) {
		$('.form-register [name="phone"]').trigger('blur');
		initNeFun()
    	return;
	}
    if(!err) {
        validate = ret.validate;
        $('#btn-getYzm').removeClass('btn-disabled');
        $('.captcha').parent().removeClass('nform-tip-error').addClass('hide');
    }
}, initNeFun = function() {
	validate = '';
	initne({}, verify);
}
$(function(){
	initNeFun()
	$('.wrap_in').on('click', '#btn-getYzm', function(){
    	var phoneErr = $('.phone').closest('.nform-tip').hasClass('nform-tip-error');
    	if (!validate) {
	    	$('.captcha').show().html('请先滑动验证码完成校验').parent().addClass('nform-tip-error').removeClass('nform-tip-true hide');
	    	return false
    	}
		if (!phoneErr && phone) {
  	    	$.ajax({
  	            type: 'post',
  	            url:  '/main/getPhcodeNE/registerCaptcha',
  	            data: {'phone':phone, 'position':<?php echo $position['register_captche']?>, 'validate':validate},
  	            dataType: 'json',
  	            success: function(response) {
  	                if(!response.status)
  	                    $('.nform-vcode .nform-tip').addClass('nform-tip-error').removeClass('nform-tip-true hide').find('.nform-tip-con').show().html(response.msg);
  	                else 
  	                	netimer($("#btn-getYzm"));
  	            }
  	        });
  	    }
    })
	var formreg = new cx.vform('.form-register', {
		renderTip: 'renderTips',
		sValidate: 'submitValidate',
	    submit: function(data) {
	        if(data.agreement != '1'){
	            cx.Alert({content: '请同意服务协议'});
	            return false;
	        }
	        var self = this;
	        $.ajax({
	            type: 'post',
	            url:  '/mainajax/register',
	            data: data,
	            success: function(response) {
		            switch (parseInt(response.code, 10)) {
		            	case 0://注册成功
				            location.href =  '/main/welcome';
			            	break;
		            	case 2:
			            	if (response.needfrsh == 1) {
			            		$('.phoneCaptcha').closest('.nform-tip').addClass('nform-tip-error').removeClass('hide');
			                	$('.phoneCaptcha').show().html('请重新获取验证码');
			               	}else {
			               		$('.phoneCaptcha').closest('.nform-tip').removeClass('hide').addClass('nform-tip-error');
			                	$('.phoneCaptcha').show().html('请输入正确的手机验证码');
			               	}
		            		$('input[name="phoneCaptcha"]').val('');	
		            		break;
		            	case 5:
			               	$('.phoneCaptcha').closest('.nform-tip').removeClass('hide').addClass('nform-tip-error');
			                $('.phoneCaptcha').show().html(response.msg);
		            		$('input[name="phoneCaptcha"]').val('');	
		            		break;
	            		default:
		            		cx.Alert({content:response.msg});
		            		break;
		            }
	            }
	        });
	    }
	});
})
</script>
