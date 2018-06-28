<div class="tit-b">
    <h2>修改银行卡</h2>
    <p class="tip cOrange">为了保证你的快速提现，请保证银行卡开户姓名与绑定真实姓名一致</p>
</div>
<ul class="steps-bar clearfix">
    <li><i>1</i><span class="des">填写银行信息</span></li>
    <li class="cur"><i>2</i><span class="des">核对信息</span></li>
    <li class="last"><i>3</i><span class="des">验证完成</span></li>
</ul>
<div class="safe-item-box">
    <form action="" class="form uc-form-list pl154" id="update-bank-list">
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
                <input type="text" name="captcha" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
                <input type='hidden' class='vcontent' name='action' value='_7'>
                <input type='hidden' class='vcontent' name='id' value='<?php echo $id; ?>'>
                <input type='hidden' class='vcontent' name='bank_province' value='<?php echo $bank_province; ?>'>
                <input type='hidden' class='vcontent' name='bank_city' value='<?php echo $bank_city; ?>'>
                <input type='hidden' class='vcontent' name='bank_type' value='<?php echo $bank_type; ?>'>
                <input type='hidden' class='vcontent' name='bank_id' value='<?php echo $bank_id; ?>'>
                <input type='hidden' name='phone' value='<?php echo $this->uinfo['phone']; ?>'>
                <a href="javascript:;" id='btn-getYzm' data-freeze='phone' class="lnk-getvcode _timer">获取语音验证码</a>
                <span class="lnk-getvcode-disb hide">重新发送(<em id='_timer'>60</em>秒)</span>
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con captcha tip"></span>
                    <s></s>
                </div>
                <div class="ui-poptip ui-poptip-yuyin" style="left: 0; top: 46px;">
                  <div class="ui-poptip-container">
                    <div class="ui-poptip-arrow-top"> <i>◆</i> <span>◆</span> </div>
                    系统将拨打您的手机语音播报验证码，请注意接听。<a href="/help/index/b0-f4">未收到验证码？</a>
                  </div>
                </div>
            </div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
               <a href="javascript:;" class="btn btn-confirm submit">确定</a> 
            </div>
        </div>
    </form>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>提款账户开户姓名必须与您在2345填写的真实姓名一致，否则将提款失败。</p>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
<script>
	$(function(){
		$("#change_imgCaptcha").on('click', function(){
			$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
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

        if (!$(this).hasClass('disabled')) {
            var code = $('input[name="imgCaptcha"]').val() || false;
            if(!code){
        		$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
            	$('.imgCaptcha').show().html('请输入图形验证码');
            	return false;
            }
            
            $.ajax({
                type: 'post',
                url: '/safe/getPhoneCode/phoneCaptcha',
                data: {'phone':phone,'code':code},
                dataType: 'json',
                success: function (response) {
                    if (response.status) {
                    	timer();
                    } else {
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

    new cx.vform('#update-bank-list', {
        renderTip: 'renderTips',
        submit: function (data) {
            $.ajax({
                type: 'post',
                //url: '/safe/paypwd',
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
                        cx.Alert({content:'验证码不正确'});
                    }else if (response) {
                        $('.article').html(response);
                    } else {
                        cx.Alert({content:'系统异常'});
                    }
                }
            });
        }
    });


</script>