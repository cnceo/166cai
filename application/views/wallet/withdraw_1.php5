<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
<div class="tit-b">
    <h2>提款申请</h2>
</div>
<ul class="steps-bar clearfix">
    <li><i>1</i><span class="des">提款申请</span></li>
    <li class="cur"><i>2</i><span class="des">验证身份</span></li>
    <li class="last"><i>3</i><span class="des">申请完成</span></li>
</ul>
<form class="form uc-form-list cash-form">
    <div class="form-item">
        <input type='hidden' class='vcontent' name='action' value='_2'>
        <label class="form-item-label">手机号码</label>
        <div class="form-item-con">
            <input type="hidden" name="phone" value="<?php echo $this->uinfo['phone']; ?>" class="vcontent">
            <span class="form-item-txt"><?php echo $this->uinfo['phone']; ?></span>
        </div>
    </div>
    <!-- 图片验证码 -->
	<div class="form-item form-vcode vcode-img">
		<label for="" class="form-item-label">图形验证码</label>
		<div class="form-item-con">
			<input class='form-item-ipt inp_s vcontent' type="text" name="imgCaptcha" data-rule='checkcode' value="" /><img id='imgCaptcha' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
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
            <input type="text" name="captcha" data-rule='checkcode' value="" class="form-item-ipt vyzm vcontent">
            <a href="javascript:;" id='btn-getYzm' class="lnk-getvcode _timer">获取语音验证码</a>
            <span class="lnk-getvcode-disb hide">重新发送(<em id='_timer'>60</em>秒)</span>
            <div class="form-tip hide">
                <i class="icon-tip"></i>
                <span class="form-tip-con captcha tip"></span>
                <s></s>
            </div>
            <div style="left: 0; top: 46px;" class="ui-poptip ui-poptip-yuyin">
                <div class="ui-poptip-container">
                    <div class="ui-poptip-arrow-top"><i>◆</i><span>◆</span></div>
                    系统将拨打您的手机语音播报验证码，请注意接听。<a target="_blank" href="/help/index/b0-f4">未收到验证码？</a>
                </div>
            </div>
        </div>
    </div>
    <div class="form-item btn-group">
        <div class="form-item-con">
           <a class="btn btn-confirm submit" href="javascript:;">下一步</a> 
        </div>
    </div>
</form>
<div class="warm-tip">
    <h3>温馨提示：</h3>
    <p>1、为保障资金安全，每个用户每天仅可申请提款1次，我们将审核您的提款申请后，再转账至您银行账户，本站提款免手续费，提款到账后我们将短信通知您；</p>
    <p>2、为防止恶意提款、洗钱等不法行为，每笔充值资金的15%须用于实际消费；</p>
    <p>3、提款申请未审核前，可在<a href="/mylottery/withdrawals">提款记录</a>中选择撤销提款；</p>
</div>
<script type="text/javascript">
    $(function(){
    	$("#change_imgCaptcha").on('click', function(){
    		$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
    		return false;
        });
        $('#btn-getYzm').click(function(){
            var phone = $('input[name="phone"]').val();
            if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/))
            {
                var code = $('input[name="imgCaptcha"]').val() || false;
                if(!code){
            		$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                	$('.imgCaptcha').show().html('请输入图形验证码');
                	return false;
                }
                
                $.ajax({
                    type: 'post',
                    url:  '/wallet/getPhoneCode/phoneCaptcha',
                    data: {'phone':phone,'code':code},
                    dataType: 'json',
                    success: function(response) {
                        if(response.status)
                        {
                        	timer();
                        }
                        else
                        {
                        	if(response.msg){
                            	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                            	$('input[name="imgCaptcha"]').val('');
                            	$('.imgCaptcha').show().html(response.msg);
                            	$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
                            }else{
                            	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                            	cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                            }
                        }
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
        //         content:'您今天已经申请过提款'
        //     });您今天已经申请过提款
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
            submit: function(data) {
                var self = this;

                // 检查最大值
                if( $('#withdraw').val() > $('#withDrawMoney').data('money') ) {
                    cx.Alert({
                        content:'您设置的提款金额超出了可提款数量'
                    });
                    return false;
                }
                $.ajax({
                    type: 'post',
                    url:  '/wallet/withdraw',
                    data: data,
                    success: function(response) {
                        if( response == 2 ){
                            cx.Alert({content:'您今天已经申请过提款'});
                        }else if (response == 3 ) {
                            cx.Alert({content:'您的账户已被冻结'});
                        }else if (response == 4 ) {
                            cx.Alert({content:'您设置的提款金额超出了可提款数量'});
                        }else if (response == 5 ) {
                            cx.Alert({content:'验证码为空'});
                        }else if (response == 6 ) {
                            cx.Alert({content:'验证码错误或超时'});
                        }else if (response == 7 ) {
                            cx.Alert({content:'网络错误'});
                        }else if (response == 8 ) {
                            cx.Alert({content:'请输入正确的提款金额'});
                        }else if (response == 9 ) {
                            cx.Alert({content:'银行卡不存在或已删除'});
                        }else if (response == 10 ) {
                            showBind();
                        }  else {
                            $('.article').html(response);
                        }
                    }
                });
            }
        });
    });
</script>