<?php $position = $this->config->item('POSITION')?>
<div class="pub-pop safe-center bind-form" >
	<div class="pop-in">
		<div class="pop-head">
			<h2>领奖人信息</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<div class="mod_user">	
                <form class="form uc-form-list">
                    <?php if( !$is_id_bind ): ?>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>真实姓名</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" name="real_name" data-rule="chinese">
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con real_name tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>身份证号</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-encrypt='1' data-rule="identification">
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con id_card tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>确认身份证号</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" name="con_id_card" data-encrypt='1' data-rule="same" data-with="id_card">
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con con_id_card tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if( !$is_pay_pwd ): ?>
                    <div class="form-item">
                        <label class="form-item-label">新支付密码</label>
                        <div class="form-item-con">
                            <input type="password" class="form-item-ipt vcontent" value="" data-rule='password' name="pay_pwd">
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
                    <?php endif; ?>
                    
                    <?php if( !$is_phone_bind ): ?>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>手机号码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" data-rule='phonenum' name="phone">
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
							<input class='form-item-ipt inp_s vcontent' type="text" name="imgCaptcha" data-rule='checkcode' value="" /><img id='captcha_reg' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
							<a class="lnk-txt" href="javascript:;" target="_self" id="change_imgCaptcha">换一张</a>
							<div class="form-tip hide">
								<i class="icon-tip"></i>
								<span class="form-tip-con tip imgCaptcha"></span>
								<s></s>
							</div>
						</div>
					</div>
                    <div class="form-item form-vcode">
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkcode' style="width:100px;" value="" name="newphoneyzm">
                            <a class="lnk-getvcode" id='btn-getYzm' data-freeze='phone' href="javascript:;" target="_self">获取语音验证码</a>
                            <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
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
                    <?php endif; ?>
                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" href="javascript:;" target="_self">绑定信息</a>
                        </div>
                    </div>
                </form>
	        </div>
		</div>
	</div>
</div>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/vform.min.js');?>"></script>
<script type="text/javascript">
<!--
	$(function(){
    	$("#change_imgCaptcha").on('click', function(){
    		recaptcha_reg();
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

			if( !$(this).hasClass('disabled') )
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
	               data: {'phone':phone, 'position':<?php echo $position['phone_captche']?>,'code':code},
                   dataType: 'json',
	               success: function(response) {
	            		if(response.status)
		           		{
	            			timer();
							cx.Alert({
								content: '验证码已发送你的手机！'
							});
		           		}
		               	else
		               	{
                        	if(response.msg){
                            	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                            	$('input[name="imgCaptcha"]').val('');
                            	$('.imgCaptcha').show().html(response.msg);
                            	recaptcha_reg();
                            }else{
                            	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                            	cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                            }
				        }
	               }
		        });
			}
		});

		new cx.vform('.bind-form', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
	            $.ajax({
	                type: 'post',
	                url:  '/safe/bind',
	                data: data,
	                success: function(response) {
                        if(response == 1){
                            cx.PopBind.hide();
							cx.Alert({
								content: '绑定成功',
								confirmCb: function() {
                                    $('.not-bind').off('click', showBind);
                                    $('.not-bind').removeClass('not-bind');
								}
							});
                        } else {
                            console.log(response);
                        }
	                }
	            });
	        }
	    });


	})    
//-->
</script>
