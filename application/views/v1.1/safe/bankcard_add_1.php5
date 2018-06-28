<?php $position = $this->config->item('POSITION')?>
<div class="tit-b">
    <h2>添加提现银行卡</h2>
    <p class="tip cOrange">为了保证你的快速提现，请保证银行卡开户姓名与绑定真实姓名一致</p>
</div>
<ul class="steps-bar clearfix">
    <li><i>1</i><span class="des">填写银行信息</span></li>
    <li class="cur"><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">验证完成</span></li>
</ul>
<div class="safe-item-box">
    <div class="form uc-form-list pl154" id="add-bank-list">
        <div class="form-item">
            <label class="form-item-label">手机号码</label>
            <div class="form-item-con">
                <span class="form-item-txt"><?php echo $this->uinfo['phone'];?></span>
            </div>
        </div>
        <!-- 图片验证码 -->
		<div class="form-item form-vcode vcode-img">
			<label for="" class="form-item-label">图形验证码</label>
			<div class="form-item-con">
				<input class='form-item-ipt inp_s vcontent' type="text" name="imgCaptcha" data-rule='checkcode' data-ajaxcheck='1' value="" /><img id='captcha_reg' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
				<a class="lnk-txt" href="javascript:;" target="_self" id="change_imgCaptcha">换一张</a>
				<div class="form-tip hide">
					<i class="icon-tip"></i>
					<span class="form-tip-con tip imgCaptcha"></span>
					<s></s>
				</div>
			</div>
		</div>
        <div class="form-item form-vcode">
            <label class="form-item-label">验证码</label>
            <div class="form-item-con">
                <input type="text" name="captcha" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
                <input type='hidden' class='vcontent' name='action' value='_6'>
                <input type='hidden' class='vcontent' name='bank_province' value='<?php echo $bank_province; ?>'>
                <input type='hidden' class='vcontent' name='bank_city' value='<?php echo $bank_city; ?>'>
                <input type='hidden' class='vcontent' name='bank_type' value='<?php echo $bank_type; ?>'>
                <input type='hidden' class='vcontent' name='bank_id' value='<?php echo $bank_id; ?>'>
                <input type='hidden' name='phone' value='<?php echo $this->uinfo['phone']; ?>'>
                <a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer" target="_self">获取验证码</a>
            	<span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con captcha tip"></span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a href="javascript:;" class="btn btn-main submit">确定</a>
            </div>
        </div>
    </div>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>1.提现账户开户姓名必须与您在166彩填写的真实姓名一致，否则将提现失败。</p>
</div>
<script>
var getYzmLook = false;
	$(function(){
    	$("#change_imgCaptcha").on('click', function(){
    		recaptcha_reg();
    		closeTimer(1);
    		return false;
        });
	});
    $('#btn-getYzm').click(function () {
        var self = $(this);
        var phone = $('.safe-item-box').find('input[name="phone"]').val();
        
        if( !phone.match(/^\d{11}$/) ){
            cx.Alert({
                content: '请填写正确的手机号码'
            });
            return false;
        }

        if (!$(this).hasClass('disabled') && !getYzmLook) {
        	getYzmLook = true;
            var code = $('input[name="imgCaptcha"]').val() || false;
            if(!code){
        		$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
            	$('.imgCaptcha').show().html('请输入图形验证码');
            	return false;
            }
            $.ajax({
                type: 'post',
                url: '/safe/getPhoneCode/phoneCaptcha',
                data: {'position':<?php echo $position['withdraw_captche']?>,'code':code},
                dataType: 'json',
                success: function (response) {
                	timer(self);
                    if (response.status) {
                    	$('#add-bank-list input[name="imgCaptcha"]').attr('data-ajaxcheck', '0');
                    } else {
                    	recaptcha_reg();
                    	if(response.msg){
                        	$('#add-bank-list input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
                        	$('.imgCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
                        	$('.imgCaptcha').show().html('验证码有误，请重新输入');
                    	}            
                        closeTimer(1);
                    }
                    getYzmLook = false;
                }
            });
        }
    });

    new cx.vform('#add-bank-list', {
        renderTip: 'renderTips',
        sValidate: 'submitValidate',
        checklogin: true,
        submit: function (data) {
            $.ajax({
                type: 'post',
                url: '/safe/bankcard',
                data: data,
                success: function (response) {
                    if( response == 2 ){
                        cx.Alert({content:'开户地区不能为空'});
                    }else if (response == 3 ) {
                        cx.Alert({content:'开户行不能为空'});
                    }else if (response == 4 ) {
                        cx.Alert({content:'银行卡格式不正确'});
                    }else if (response == 5 ) {
                        cx.Alert({content:'银行卡格式不正确'});
                    }else if (response == 6 ) {
                        cx.Alert({content:'已绑定过此银行卡'});
                    }else if (response == 7 ) {
                    	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.captcha').show().html('验证码不正确');
                    	$('input[name="captcha"]').val('');
                    }else if (response == 71 ) {
                    	recaptcha_reg();
                    	closeTimer(1);
                    	$('#add-bank-list input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
                    	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                    	$('.captcha').show().html('请重新获取验证码');
                    	$('input[name="captcha"]').val('');
                    }else if (response) {
                        $('.l-frame-cnt .uc-main').html(response);
                    } else {
                        cx.Alert({content:'系统异常'});
                    }
                }
            });
        }
    });


</script>

