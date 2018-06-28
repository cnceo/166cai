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
	<div class="form-tips-bar">温馨提示：手机号码将用于中奖通知，请填写本人手机号</div>
	<div class="nform-item email_area">
		<label class="nform-item-label">手机号码：</label>
		<div class="nform-item-con">
			<input class="nform-item-ipt vcontent" type="text" data-rule="wechat" placeholder="绑定后手机号可直接登录" c-placeholder="绑定后手机号可直接登录" autocomplete="off" name="wechatPhone" data-ajaxcheck='1' value="" />
			<div class="nform-tip hide">
				<i class="icon-tip"></i>
				<span class="nform-tip-con tip wechatPhone"></span>
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
            <a href="javascript:;" onclick="_hmt.push(['_trackEvent', 'binduserphone', 'get_captcha']);" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer btn-disabled" target="_self">获取验证码</a>
            <span class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
            <div class="nform-tip hide">
                <i class="icon-tip"></i>
                <span class="nform-tip-con tip phoneCaptcha"></span>
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
			<a class="btn btn-main submit" onclick="_hmt.push(['_trackEvent', 'binduserphone', 'finish']);" href="javascript:;">完成</a>
		</div>
	</div>
    <input type="hidden" name="unionid" value="<?php echo $unionid; ?>" class="nform-item-ipt vcontent">
    <input type="hidden" name="codeStr" value="<?php echo $codeStr; ?>" class="nform-item-ipt vcontent">
    <input type="hidden" name="sign" value="<?php echo $sign; ?>" class="nform-item-ipt vcontent">
    <input type="hidden" name="token1" value="<?php echo $token1; ?>" class="nform-item-ipt vcontent">
    <input type="hidden" name="token2" value="<?php echo $token1; ?>" class="nform-item-ipt vcontent">
</form>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<?php $this->load->view('v1.1/elements/netimer');?>
<!-- 注册 end -->
<script type="text/javascript">
var phone, validate, verify = function(err, ret){
	phone = $('.form-register [name="wechatPhone"]').val();
	if (!/^\d{11}$/.test(phone)) {
		$('.form-register [name="wechatPhone"]').trigger('blur');
		initNeFun();
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
};
$(function(){
	initNeFun();
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
	        var self = this;
	        $.ajax({
	            type: 'post',
	            url:  '/wechat/wechatRegister',
	            data: data,
	            success: function(response) {
	            	if(response.code == '1'){
	                	// 注册成功
			            location.href =  '/main/welcome';
	            	}else if (response.code == '2'){
                        // 绑定成功
                        location.href =  '/';
                    }else if (response.needfrsh == 1) {
	            		$('.phoneCaptcha').closest('.nform-tip').addClass('nform-tip-error').removeClass('hide');
	                	$('.phoneCaptcha').show().html('请重新获取验证码');
	                	//注册失败
	               	}else {
	               		$('.phoneCaptcha').closest('.nform-tip').removeClass('hide').addClass('nform-tip-error');
	                	$('.phoneCaptcha').show().html(response.msg);
	               	}
	                $('input[name="phoneCaptcha"]').val('');
	            }
	        });
	        
	    }
	});
})
</script>
