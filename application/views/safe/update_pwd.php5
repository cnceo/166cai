<?php $this->load->view('elements/user/menu'); ?>
<div class="article">    
    <div class="tab-nav">
        <ul class="clearfix">
            <li><a href="/safe/paypwd/"><span><?php if (empty($this->uinfo['pay_pwd'])): ?>修改支付密码<?php else: ?>设置支付密码<?php endif; ?></span></a>
            </li>
            <li class="active"><a href="javascript:;"><span>修改登录密码</span></a></li>
        </ul>
    </div>
    <div class="tab-content">
        <div class="tab-item pt20" style="display: block;">
            <ul class="steps-bar clearfix">
                <li class="cur"><i>1</i><span class="des">验证身份</span></li>
                <li><i>2</i><span class="des">设置密码</span></li>
                <li class="last"><i>3</i><span class="des">操作成功</span></li>
            </ul>
            <div class="safe-item-box">
                <form class="form uc-form-list pl154">
                     <div class="form-item">
                        <label class="form-item-label">验证方式</label>
                        <div class="form-item-con">
                            <dl class="simu-select-med" data-target="modify_mode">
                                <dt>登录密码<i class="arrow"></i></dt>
                                <dd class="select-opt">
                                    <div class="select-opt-in">
                                        <a href="javascript:;" data-value="pwdType">登录密码</a>
                                        <?php if($this->uinfo['phone']): ?>
                                        <a href="javascript:;" data-value="phoneType">手机号码</a>
                                        <?php endif; ?>
                                        <?php if($this->uinfo['email']): ?>
                                        <a href="javascript:;" data-value="emailType">邮箱</a>
                                        <?php endif; ?>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                    <div class="form-item pwd-submit">
                        <label class="form-item-label">确认登录密码</label>
                        <div class="form-item-con"><input type="password" value="" name="pword" autocomplete="off" class="form-item-ipt vcontent"></div>
                    </div>
                    <div class="form-item btn-group pwd-submit">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" href="javascript:;">下一步</a>
                            <input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
                            <input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='pwdType'>
                        </div>
                    </div>
                    <?php if($this->uinfo['phone']): ?>
                    <div class="form-item phone-submit" style="display:none;">
                        <label class="form-item-label">手机号码</label>
                        <div class="form-item-con">
                            <span class="form-item-txt"><?php echo substr_replace($this->uinfo['phone'],'****',3,4); ?></span>
                            <input type='hidden' class="form-item-ipt" id="" name='hide_phone' value='<?php echo $this->uinfo['phone']; ?>'>
                        </div>
                    </div>
                    <!-- 图片验证码 -->
					<div class="form-item form-vcode vcode-img" style="display:none;">
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
                    <div class="form-item phone-submit form-vcode" style="display:none;">
                        <label class="form-item-label">验证码</label>
                        <div class="form-item-con">
                            <input type="text" name="newphoneyzm" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
                            <a href="javascript:;" data-freeze='phone' class="lnk-getvcode _timer" id="btn-getYzm">获取语音验证码</a>
                            <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                            <div class="form-tip hide form_tips_error">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con newphoneyzm tip">请输入验证码</span>
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
                    <div class="form-item phone-submit" style="display:none;">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" href="javascript:;">下一步</a>
                            <input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
                            <input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='phoneType'>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if($this->uinfo['email']): ?>
                    <div class="form-item email-submit" style="display:none;">
                        <label class="form-item-label">邮箱</label>
                        <div class="form-item-con"><span class="form-item-txt bindemail"><?php echo $hide_email; ?></span></div>
                        <input type='hidden' class="form-item-ipt" id="" name='hide_email' value='<?php echo $hide_email; ?>'>
                    </div>
                    <div class="form-item btn-group email-submit" style="display:none;">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" href="javascript:;">发送验证邮件</a>
                            <input type='hidden' class='vcontent' id="actiontype" name='email' value='<?php echo $this->uinfo['email']; ?>'>
                            <input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
                            <input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='emailType'>
                        </div>
                    </div>
                    <?php endif; ?>             
                </form>
            </div>
        </div>
    </div>
    <script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js'); ?>'></script>
    <script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
    <script type="text/javascript">
        $(function () {
        	$("#change_imgCaptcha").on('click', function(){
        		$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
        		return false;
            });
            var selectDt = $('.simu-select-med').find('dt');
            $('.select-opt-in').find('a').on('click', function(){  
                selectDt.attr('data-value', $(this).attr('data-value'));
                selectDt.html($(this).html() + '<i class="arrow"></i>');
                if(selectDt.attr('data-value') == 'pwdType'){
                    $('.phone-submit').hide();
                    $('.email-submit').hide();
                    $('.pwd-submit').show()
                    $(".vcode-img").hide();
                }else if(selectDt.attr('data-value') == 'phoneType'){
                    $('.pwd-submit').hide()
                    $('.email-submit').hide();
                    $('.phone-submit').show();
        	        $(".vcode-img").show();

                }
                else if(selectDt.attr('data-value') == 'emailType'){
                    $('.pwd-submit').hide()
                    $('.phone-submit').hide();
                    $('.email-submit').show();
                    $(".vcode-img").hide();
                }
            })

            $('#btn-getYzm').click(function () {
                var self = $(this);
                var phone = $('input[name="hide_phone"]').val();
                
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
                        url: '/safe/getPhoneCode/newphoneyzm',
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
                                	cx.Alert({
                                        content: '验证码发送失败，请联系我们的客服！'
                                    });
                                }
                            }
                        }
                    });
                }
            });

            new cx.vform('.pwd-submit', {
                renderTip: 'renderTips',
                submit: function (data) {
                    var self = this;
                    $.ajax({
                        type: 'post',
                        url: '/safe/update_password',
                        data: data,
                        success: function (response) {
                            if(response == '001'){
                                cx.Alert({content:'验证身份信息异常！'});
                            }
                            else if(response == '002'){
                                cx.Alert({content:'验证码错误！'});
                            }
                            else if(response == '003'){
                                var hide_email = $('input[name="hide_email"]').val();
                                var email = $('input[name="email"]').val();
                                var patch = email.split("@"); 
                                var email_patch = 'http://mail.'+patch[1];
                                cx.Alert({
                                    confirm:'去邮箱收信',
                                    content:'您的验证链接已发送至邮箱:<span class="cBlue2 yemail show">'+ hide_email +'</span>请登录邮箱查收您的验证信息。',
                                    confirmCb:function(){
                                        window.open(email_patch);
                                        // location.href = ;
                                        return false;
                                }});
                            }
                            else if(response == '004'){
                                cx.Alert({content:'两次密码不匹配！'});
                            }
                            else if(response == '005'){
                                cx.Alert({content:'登录密码不能与支付密码一致！'});
                            }
                            else if(response == '006'){
                                cx.Alert({content:'登录密码不正确！'});
                            }
                            else{
                                $('.tab-content').html(response);
                            }
                        }
                    });
                }
            });

            new cx.vform('.phone-submit', {
                renderTip: 'renderTips',
                submit: function (data) {
                    var self = this;
                    $.ajax({
                        type: 'post',
                        url: '/safe/update_password',
                        data: data,
                        success: function (response) {
                            if(response == '001'){
                                cx.Alert({content:'验证身份信息异常！'});
                            }
                            else if(response == '002'){
                                cx.Alert({content:'验证码错误！'});
                            }
                            else if(response == '003'){
                                var hide_email = $('input[name="hide_email"]').val();
                                var email = $('input[name="email"]').val();
                                var patch = email.split("@"); 
                                var email_patch = 'http://mail.'+patch[1];
                                cx.Alert({
                                    confirm:'去邮箱收信',
                                    content:'您的验证链接已发送至邮箱:<p class="cBlue2 yemail">'+ hide_email +'</p>请登录邮箱查收您的验证信息。',
                                    confirmCb:function(){
                                        window.open(email_patch);
                                        // location.href = ;
                                        return false;
                                }});
                            }
                            else if(response == '004'){
                                cx.Alert({content:'两次密码不匹配！'});
                            }
                            else if(response == '005'){
                                cx.Alert({content:'登录密码不能与支付密码一致！'});
                            }
                            else if(response == '006'){
                                cx.Alert({content:'登录密码不正确！'});
                            }
                            else{
                                $('.tab-content').html(response);
                            }
                        }
                    });
                }
            });

            new cx.vform('.email-submit', {
                renderTip: 'renderTips',
                submit: function (data) {
                    var self = this;
                    $.ajax({
                        type: 'post',
                        url: '/safe/update_password',
                        data: data,
                        success: function (response) {
                            if(response == '001'){
                                cx.Alert({content:'验证身份信息异常！'});
                            }
                            else if(response == '002'){
                                cx.Alert({content:'验证码错误！'});
                            }
                            else if(response == '003'){
                                var hide_email = $('input[name="hide_email"]').val();
                                var email = $('input[name="email"]').val();
                                var patch = email.split("@"); 
                                var email_patch = 'http://mail.'+patch[1];
                                cx.Alert({
                                    confirm:'去邮箱收信',
                                    content:'您的验证链接已发送至邮箱:<p class="cBlue2 yemail">'+ hide_email +'</p>请登录邮箱查收您的验证信息。',
                                    confirmCb:function(){
                                        window.open(email_patch);
                                        // location.href = ;
                                        return false;
                                }});
                            }
                            else if(response == '004'){
                                cx.Alert({content:'两次密码不匹配！'});
                            }
                            else if(response == '005'){
                                cx.Alert({content:'登录密码不能与支付密码一致！'});
                            }
                            else if(response == '006'){
                                cx.Alert({content:'登录密码不正确！'});
                            }
                            else{
                                $('.tab-content').html(response);
                            }
                        }
                    });
                }
            });

            $(".pop-close").on('click', function(){
                $('.pop-alert-success').hide();;
            });

        })
    </script>
</div>

<?php $this->load->view('elements/user/menu_tail'); ?>