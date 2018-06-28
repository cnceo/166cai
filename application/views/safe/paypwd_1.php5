<div class="tit-b">
	<?php if($actiontype=='setPayPwd'):?>
	<h2>设置支付密码</h2>
	<?php else:?>
	<h2>修改支付密码</h2>
	<?php endif;?>
	<p class="tip cOrange">支付密码为购买彩票付款时输入，不等同于登录密码</p>
</div>
<div class="tab-content">
	<input type='hidden' class='vcontent' name='action' value='_2'>
	<input type='hidden' class='vcontent' name='actiontype' value='<?php echo $actiontype;?>'>
	<?php if($actiontype=='setPayPwd'):?>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">设置支付密码</span></li>
		<li class="cur"><i>2</i><span class="des">验证身份</span></li>
		<li class="last"><i>3</i><span class="des">操作成功</span></li>
	</ul>
	<div class="tab-item pt20" style="display:block;">
		<input type='hidden' class='vcontent' name='pay_pwd' value='<?php echo $pay_pwd;?>'>
		<input type='hidden' class='vcontent' name='conpay_pwd' value='<?php echo $conpay_pwd;?>'>
		<div class="safe-item-box">
			<form action="" class="form uc-form-list pl154">
				<?php if( empty( $this->uinfo['phone'] ) ): ?>
				<div class="form-item">
					<label class="form-item-label">手机号码</label>
					<div class="form-item-con">
							<input type="text" name="phone" value="" data-rule='phonenum' data-ajaxcheck='1' data-freeze='_timer' class="form-item-ipt vcontent">
							<div class="form-tip hide">
		                        <i class="icon-tip"></i>
		                        <span class="form-tip-con phone tip"></span>
		                        <s></s>
		                    </div>
						</div>
				</div>
				<div class="form-item form-vcode">
					<label class="form-item-label">验证码</label>
					<div class="form-item-con">
						<input type="text" name="newphoneyzm" value="" data-rule='checkmsgcode' class="form-item-ipt vyzm vcontent">
						<a href="javascript:;" id='btn-getYzm' data-freeze='phone' class="lnk-getvcode _timer">获取短信验证码</a>
						<span class="lnk-getvcode-disb hide">重新发送(<em id='_timer'>60</em>秒)</span>
						<div class="form-tip hide">
	                        <i class="icon-tip"></i>
	                        <span class="form-tip-con newphoneyzm tip"></span>
	                        <s></s>
	                    </div>
					</div>
				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<a class="btn btn-confirm submit" href="javascript:;">提交</a>
					</div>
				</div>
				<?php else: ?>
				<div class="form-item">
					<label class="form-item-label">手机号码</label>
					<input type='hidden' name='phone' value='<?php echo $this->uinfo['phone'];?>'>
						<div class="form-item-con"><span class="form-item-txt"><?php echo $this->uinfo['phone'];?></span></div>
				</div>
				<!-- 图片验证码 -->
				<div class="form-item form-vcode vcode-img">
					<label for="" class="form-item-label"><b>*</b>图形验证码</label>
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
						<input type="text" name="captcha" value="" data-rule='checkcode' class="form-item-ipt vyzm vcontent">
						<a href="javascript:;" id='btn-getYzm' data-freeze='phone' class="lnk-getvcode _timer">获取语音验证码</a>
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
						<a class="btn btn-confirm submit" href="javascript:;">提交</a>
					</div>
				</div>
				<?php endif;?>
			</form>
		</div>
		<div class="warm-tip">
			<h3>温馨提示：</h3>
            <p>1、为了保障您的账户资金安全，建议您定期修改支付密码并牢记，以便安全购彩</p>
            <p>2、若您无法收到语音验证码，请联系<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2584565084&site=qq&menu=yes">在线客服</a>，或拨打客服电话400-000-2345转8彩票业务</p>
		</div>
	</div>
	<?php elseif($actiontype=='resetPayPwd'):?>
	<ul class="steps-bar clearfix">
		<li><i>1</i><span class="des">验证身份</span></li>
		<li class="cur"><i>2</i><span class="des">设置支付密码</span></li>
		<li class="last"><i>3</i><span class="des">设置成功</span></li>
	</ul>
	<div class="tab-item pt20" style="display:block;">
		<input type='hidden' class='vcontent' name='captcha' value='<?php echo $captcha;?>'>
		<?php if(!empty($phone)):?>
			<input type='hidden' class='vcontent' name='phone' value='<?php echo $phone;?>'>
		<?php endif;?>
		<div class="safe-item-box">
			<form action="" class="form uc-form-list pl154">
				<div class="form-item">
					<label class="form-item-label">新支付密码</label>
					<div class="form-item-con">
						<input type="password" class="form-item-ipt vcontent" value="" data-rule='password' data-ajaxcheck='1' name="pay_pwd">
						<div class="form-tip hide">
	                        <i class="icon-tip"></i>
	                        <span class="form-tip-con pay_pwd tip"></span>
	                        <s></s>
	                    </div>
					</div>
				</div>
				<div class="form-item">
					<label class="form-item-label">重复支付密码</label>
					<div class="form-item-con">
						<input type="password" class="form-item-ipt vcontent" data-rule='same' data-with='pay_pwd' value="" name="conpay_pwd">
						<div class="form-tip hide">
	                        <i class="icon-tip"></i>
	                        <span class="form-tip-con conpay_pwd tip"></span>
	                        <s></s>
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
		<div class="warm-tip">
			<p>温馨提示：为了保障您的账户资金安全，建议您定期修改支付密码并牢记，以便安全购彩</p>
		</div>
	</div>
	<?php endif;?>
</div>
<?php if( empty( $this->uinfo['phone'] ) ): ?>
<script type="text/javascript">
	$(function(){
		$('#btn-getYzm').click(function(){
			var phone = $('input[name="phone"]').val();
            if( !phone.match(/^\d{11}$/) ){
                cx.Alert({
                    content: '请填写正确的手机号码'
                });
                return false;
            }

			if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/))
			{			
				$.ajax({
	               type: 'post',
	               url:  'http://login.2345.com/webapi/phone/sendCode',
	               data: {'phone':phone,'mid':'CP','action':'bind'},
                   dataType: 'jsonp',
	               success: function(response) {
                   	   if(response.code == '200.0')
                       {
                		   timer();
                           //cx.Alert({content:'验证码已发送你的手机！'});
                       }
                       else
                       {
                            cx.Alert({content:response.msg});
                       }
	               }
		        });
			}
		});
		
		new cx.vform('.tab-content', {
			renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  '/safe/paypwd',
	                data: data,
	                success: function(response) {
                        if(response == 2){
                            cx.Alert({content:'支付密码为空或两次输入不一致'});
                        }
                        else if(response == 3){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 4){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 5){
                            cx.Alert({content:'支付密码为空或两次输入不一致'});
                        }
                        else if (response == 6 ){
                        	cx.Alert({content:'设置支付密码失败'});
                        }
                        else if (response == 12 ){
                        	cx.Alert({content:'手机号码绑定失败'});
                        }
                        else{
                            $('.article').html(response);
                        }
                    }
	            });
	        }
	    });
	});
</script>
<?php else: ?>
<script type="text/javascript">
	$(function(){
    	$("#change_imgCaptcha").on('click', function(){
    		$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
    		return false;
        });
		$('#btn-getYzm').click(function(){
			var phone = $('input[name="phone"]').val();
            if( !phone.match(/^\d{11}$/) ){
                cx.Alert({
                    content: '请填写正确的手机号码'
                });
                return false;
            }

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
	               url:  '/safe/getPhoneCode/phoneCaptcha',
	               data: {'phone':phone,'code':code},
                   dataType: 'json',
	               success: function(response) {
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
                            	cx.Alert({
                                    content: '验证码发送失败，请联系我们的客服！'
                                });
                            }
                        }
	               }
		        });
			}
		});
		
		new cx.vform('.tab-content', {
			renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  '/safe/paypwd',
	                data: data,
	                success: function(response) {
                        if(response == 2){
                            cx.Alert({content:'支付密码为空或两次输入不一致'});
                        }
                        else if(response == 3){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 4){
                            cx.Alert({content:'验证码错误'});
                        }
                        else if(response == 5){
                            cx.Alert({content:'支付密码为空或两次输入不一致'});
                        }
                        else{
                            $('.article').html(response);
                        }
                    }
	            });
	        }
	    });
	});
</script>
<?php endif; ?>