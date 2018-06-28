<?php $position = $this->config->item('POSITION')?>
<div class="tit-b">
    <h2>提现申请</h2>
</div>
<ul class="steps-bar clearfix">
    <li><i>1</i><span class="des">提现申请</span></li>
    <li class="cur"><i>2</i><span class="des">验证身份</span></li>
    <li class="last"><i>3</i><span class="des">申请完成</span></li>
</ul>
<form class="form uc-form-list cash-form">
    <div class="form-item">
        <input type='hidden' class='vcontent' name='action' value='_2'>
        <label class="form-item-label">手机号码</label>
        <div class="form-item-con">
            <span class="form-item-txt"><?php echo $this->uinfo['phone']; ?></span>
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
            <input type='hidden' name='withdraw' class='vcontent' value='<?php echo $withdraw; ?>'>
            <input type='hidden' name='bank_id' class='vcontent' value='<?php echo $bank_id; ?>'>
            <input type="text" name="captcha" data-rule='checkcode' data-ajaxcheck='0' value="" class="form-item-ipt vyzm vcontent">
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
           <a class="btn btn-main submit" href="javascript:;">下一步</a> 
        </div>
    </div>
</form>
<div class="warm-tip">
    <h3>温馨提示：</h3>
    <p>1、为保障资金安全，每个用户每天仅可申请提现3次，我们将审核您的提现申请后，再转账至您银行账户，本站提现免手续费，提现到账后我们将短信通知您；</p>
    <p>2、为防止恶意提现、洗钱等不法行为，信用卡每笔充值资金100%须用于购彩，储蓄卡每笔充值资金的<?php echo $this->config->item('txed')?>%须用于购彩。</p>
</div>
<script type="text/javascript">
    $(function(){
        var getYzmLook = false;
    	$("#change_imgCaptcha").on('click', function(){
    		closeTimer(1);
    		recaptcha_reg();
    		return false;
        });
        $('#btn-getYzm').click(function(){
            var phone = '<?php echo $this->uinfo['phone']; ?>';
            if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/) && !getYzmLook)
            {
                var code = $('input[name="imgCaptcha"]').val() || false;
                getYzmLook = true;
                if(!code){
            		$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                	$('.imgCaptcha').show().html('请输入图形验证码');
                	return false;
                }
                
                $.ajax({
                    type: 'post',
                    url:  '/wallet/getPhoneCode/phoneCaptcha',
                    data: {'position':<?php echo $position['withdraw_captche']?>, 'code':code},
                    dataType: 'json',
                    success: function(response) {
                    	timer(self);
                        if(response.status) {
                        	$('.cash-form input[name="imgCaptcha"]').attr('data-ajaxcheck', '0');
                        }else {
                        	closeTimer(1);
                        	if(response.msg){
                            	$('.cash-form input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
                            	$('.imgCaptcha').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
                            	$('.imgCaptcha').show().html('验证码有误，请重新输入');
                        	}
                        }
                        getYzmLook = false;
                    }
                });
            }
        });
			
        $('.user-freeze').on('click', function(e){
            cx.Alert({
                content:'您的账户已被冻结'
            });
            e.stopImmediatePropagation();
        });

        // $('.today-withdraw').on('click', function(e){
        //     cx.Alert({
        //         content:'您今天已经申请过提现'
        //     });您今天已经申请过提现
        //     e.stopImmediatePropagation();
        // });

        $('.not-bind-bank').on('click', function (e){
            cx.Alert({
                content:'您尚未绑定银行卡',
                confirm: '去绑银行卡',
                confirmCb: function() {
                    location.href = '/safe/bankcard';
                }
            });
            e.stopImmediatePropagation();
        });

        new cx.vform('.cash-form', {
            renderTip: 'renderTips',
            sValidate: 'submitValidate',
            submit: function(data) {
                var self = this;

                // 检查最大值
                if( $('#withdraw').val() > $('#withDrawMoney').data('money') ) {
                    cx.Alert({
                        content:'您设置的提现金额超出了可提现数量'
                    });
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url:  '/wallet/withdraw',
                    data: data,
                    success: function(response) {
                        if( response == 2 ){
                            cx.Alert({content:'您好，每天申请提现最多3次'});
                        }else if (response == 3 ) {
                            cx.Alert({content:'您的账户已被冻结'});
                        }else if (response == 4 ) {
                            cx.Alert({content:'您设置的提现金额超出了可提现数量'});
                        }else if (response == 6 ) {
                        	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                        	$('.captcha').show().html('验证码不正确');
                        	$('input[name="captcha"]').val('');
                        }else if (response == 5 || response == 61 ) {
                        	recaptcha_reg();
                        	closeTimer(1);
                        	recaptcha_reg();
                        	$('.cash-form input[name="imgCaptcha"]').attr('data-ajaxcheck', '1');
                        	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                        	$('.captcha').show().html('请重新获取验证码');
                        	$('input[name="captcha"]').val('');
                        }else if (response == 7 ) {
                            cx.Alert({content:'网络错误'});
                        }else if (response == 8 ) {
                            cx.Alert({content:'请输入正确的提现金额'});
                        }else if (response == 9 ) {
                            cx.Alert({content:'银行卡不存在或已删除'});
                        }else if (response == 10 ) {
                            showBind();
                        }  else {
                            $('.l-frame-cnt .uc-main').html(response);
                        }
                    }
                });
            }
        });
    });
</script>