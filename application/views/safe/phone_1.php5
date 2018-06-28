<div class="tit-b">
    <h2>绑定手机</h2>
    <p class="tip cOrange">为保障您的账户安全，请绑定您的真实手机</p>
</div>

<?php if (!empty($this->uinfo['phone'])): ?>
    <ul class="steps-bar clearfix">
        <li><i>1</i><span class="des">验证身份</span></li>
        <li class="cur"><i>2</i><span class="des">手机绑定</span></li>
        <li class="last"><i>3</i><span class="des">绑定完成</span></li>
    </ul>
<?php endif; ?>

<div class="safe-item-box">
    <input type='hidden' class='vcontent' name='action' value='_2'/>
    <input type='hidden' class='vcontent' name='old_phone' value='<?php echo $old_phone; ?>'/>
    <input type='hidden' class='vcontent' name='oldphoneyzm' value='<?php echo $oldphoneyzm; ?>'/>
    <input type='hidden' class='vcontent' name='pword' value='<?php echo $pword; ?>'/>
    <form class="form uc-form-list pl154">
        <div class="form-item">
            <label class="form-item-label">手机号码</label>
            <div class="form-item-con">
                <input type="text" class="form-item-ipt vcontent" data-rule='phonenum' data-ajaxcheck='1' data-freeze='_timer' value="" name="phone">
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con phone tip"></span>
                    <s></s>
                </div>
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
                <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkcode' value="" name="newphoneyzm">
                <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' href="javascript:;">获取语音验证码</a>
                <span class="lnk-getvcode-disb hide">重新发送(<em id='_timer'>60</em>秒)</span>
                <div class="form-tip hide">
                    <i class="icon-tip"></i>
                    <span class="form-tip-con newphoneyzm tip"></span>
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
                <a href="javascript:;" class="btn btn-confirm submit">提交</a>
            </div>
        </div>
    </form>
</div>
<div class="warm-tip mt30">
    <h3>温馨提示：</h3>
    <p>1.通过绑定手机可找回密码</p>
    <p>2.绑定手机让您知悉帐号的每一笔交易信息，购彩更安全</p>
    <p>3.若您无法收到语音验证码，请联系<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2584565084&site=qq&menu=yes">在线客服</a>，或拨打客服电话400-000-2345</p>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
<script type="text/javascript">
    <!--
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
                    url:  '/safe/getPhoneCode/newphoneyzm',
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
		
        new cx.vform('.safe-item-box', {
            renderTip: 'renderTips',
            submit: function(data) {
                var self = this;
                $.ajax({
                    type: 'post',
                    url:  '/safe/phone',
                    data: data,
                    success: function(response) {
                        if(response == 2){
                            self.renderTip('登录密码错误', $('.pword'));
                        }
                        else if(response == 3){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 4){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 5){
                            self.renderTip('请输入正确的手机号', $('.phone'));
                        }
                        else if(response == 6){
                            self.renderTip('此手机号码已被绑定', $('.phone'));
                        }
                        else{
                            $('.article').html(response);
                        }
                    }
                });
            }
        });
    });
    -->
</script>
