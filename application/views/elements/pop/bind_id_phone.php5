<div class="pub-pop safe-center bind-form" >
	<div class="pop-in">
		<div class="pop-head">
			<h2>完善信息</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="mod_user">	
                <form class="form uc-form-list">
                    <?php if( !$is_id_bind ): ?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>真实身份信息是您领奖提款的依据，请如实填写</span></h3>
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
                                <input type="text" class="form-item-ipt vcontent" value="" name="id_card" data-ajaxcheck='1' data-rule="identification">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php elseif($is_id_bind && $this->con == 'safe' && $this->act == 'bankcard'):?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>真实身份信息是您领奖提款的依据，请如实填写</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>真实姓名</label>
                            <div class="form-item-con">
                                <span class="form-item-txt"><?php echo cutstr($this->uinfo['real_name'], 0, 1);?></span>
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
                                <span class="form-item-txt"><?php echo cutstr($this->uinfo['id_card'], 0, 12);?></span>
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con id_card tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>
                    
                    <?php if( !$is_phone_bind ): ?>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>手机号码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vcontent" value="" data-rule='phonenum' data-ajaxcheck='1' data-freeze='_timer' name="phone">
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con phone tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <div class="form-item form-vcode">
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkmsgcode' value="" name="newphoneyzm">
                            <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' target="_self" href="javascript:;">获取短信验证码</a>
                            <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con newphoneyzm tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <?php elseif( $is_phone_bind && $this->con == 'safe' && $this->act == 'bankcard' ):?>
                    <div class="form-item">
                        <label class="form-item-label"><b>*</b>手机号码</label>
                        <input type="hidden" class="form-item-ipt" value="<?php echo $this->uinfo['phone'];?>" data-freeze='_timer' name="phone">
                        <div class="form-item-con">
                            <span class="form-item-txt" id="textPhone"><?php echo $this->uinfo['phone'];?></span>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con phone tip"></span>
                                <s></s>
                            </div>
                        </div>
                    </div>
                    <!-- 图片验证码 -->
                    <div class="form-item form-vcode vcode-img">
                        <label for="" class="form-item-label"><b>*</b>验证码</label>
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
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" class="form-item-ipt vyzm vcontent" data-rule='checkcode' value="" name="newphoneyzm">
                            <a class="lnk-getvcode _timer" data-freeze='phone' id='btn-getYzm' target="_self" href="javascript:;">获取语音验证码</a>
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
                    <?php if( !$is_pay_pwd ): ?>
                    <fieldset>
                        <h3><span class="form-tip"><i class="icon-tip"></i>请设置支付密码，用于账户交易时输入</span></h3>
                        <div class="form-item">
                            <label class="form-item-label"><b>*</b>支付密码</label>
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
                            <label class="form-item-label"><b>*</b>重复一次</label>
                            <div class="form-item-con">
                                <input type="password" class="form-item-ipt vcontent" data-rule='same' data-with='pay_pwd' value="" name="conpay_pwd">
                                <div class="form-tip hide">
                                    <i class="icon-tip"></i>
                                    <span class="form-tip-con conpay_pwd tip"></span>
                                    <s></s>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <?php endif; ?>
                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" target="_self" href="javascript:;">提交</a>
                            <a class="btn btn-default cancel" target="_self" href="javascript:;">取消</a>
                        </div>
                    </div>
                </form>
	        </div>
		</div>
	</div>
</div>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<?php if( !$is_phone_bind ): ?>
<script type="text/javascript">
	$(function(){
		$('#btn-getYzm').click(function(){
			var phone = $('input[name="phone"]').val() || $('#textPhone').text();
            if(phone){
            	if( !phone.match(/^\d{11}$/) ){
                    cx.Alert({
                        content: '请填写正确的手机号码'
                    });
                    return false;
                }
            }
            
			if( !$(this).hasClass('disabled') )
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
                        }
                        else
                        {
                            cx.Alert({content:response.msg});
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
                        } else if (response == 2 ) {
                            self.renderTip('支付密码为空或两次输入不一致', $('.pay_pwd'));
                        } else if (response == 3 ) {
                            self.renderTip('身份证号为空或两次输入不一致', $('.id_card'));
                        } else if (response == 4 ) {
                            self.renderTip('验证码错误', $('.newphoneyzm'));
                        } else if (response == 5 ) {
                            self.renderTip('身份证已绑定', $('.id_card'));
                        } else if (response == 6 ) {
                            self.renderTip('手机已绑定', $('.phone'));
                        } else if(response == 7){
                            self.renderTip('身份证格式错误', $('.id_card'));
                        } else if(response == 12){
                            self.renderTip('手机号码绑定失败', $('.phone'));
                        } else {
                        }
	                }
	            });
	        }
	    });


	})    
</script>
<?php else: ?>
<script type="text/javascript">
<!--
    $(function(){
        $("#change_imgCaptcha").on('click', function(){
            $('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
            return false;
        });
        $('#btn-getYzm').click(function(){
            var phone = $('input[name="phone"]').val() || $('#textPhone').text();
            if(phone){
                if( !phone.match(/^\d{11}$/) ){
                    cx.Alert({
                        content: '请填写正确的手机号码'
                    });
                    return false;
                }
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
                        } else if (response == 2 ) {
                            self.renderTip('支付密码为空或两次输入不一致', $('.pay_pwd'));
                        } else if (response == 3 ) {
                            self.renderTip('身份证号为空或两次输入不一致', $('.id_card'));
                        } else if (response == 4 ) {
                            self.renderTip('验证码错误', $('.newphoneyzm'));
                        } else if (response == 5 ) {
                            self.renderTip('身份证已绑定', $('.id_card'));
                        } else if (response == 6 ) {
                            self.renderTip('手机已绑定', $('.phone'));
                        } else if(response == 7){
                            self.renderTip('身份证格式错误', $('.id_card'));
                        } else {
                        }
                    }
                });
            }
        });


    })    
//-->
</script>
<?php endif;?>
