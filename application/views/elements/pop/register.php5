<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<!-- 注册 begin -->
<div class="pub-pop registerPopWrap">
	<div class="pop-in">
		<div class="pop-head">
			<h2>注册</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body pop-register-form">
			<div class="mod_user">
                <form action="" class="form form_login form_register">
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>2345账号</label>
						<div class="form-item-con">
							<input type="text" class="form-item-ipt vcontent" autocomplete="off" data-rule='username' data-ajaxcheck='1' name="username" value="" />
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con username tip"></span>
                                <s></s>
                            </div>
						</div>
						<input class="vcontent" type="hidden" name="actions" value="default" />
					</div>
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>创建密码</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="password" name="pword" data-rule="password" data-encrypt="1" value="" />
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con pword tip" style="display:none;"></span>
                                <s></s>
                            </div>					
						</div>
					</div>
					<div class="form-item">
						<label for="" class="form-item-label"><b>*</b>确认密码</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="password" name="con_pword" data-rule="same" data-encrypt="1" data-with="pword" value="" />
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con con_pword tip"></span>
                                <s></s>
                            </div>					
						</div>
					</div>
					<!--  <li class="email_area">
						<label class="label_like"><span class="must">*</span>邮箱地址</label>
						<div class="con pos">
							<input class='ipt_text vcontent' data-rule="email" type="text" name="email" value="" />
						</div>
						<div class="form_tips" style='display:none;'>
							<i class="tips_icon"></i>
							<div class="tips_con tip email"></div>
						</div>
					</li>
					-->
                    <div class="form-item form-vcode vcode-img">
						<label for="" class="form-item-label"><b>*</b>图形验证码</label>
						<div class="form-item-con">
							<input class="form-item-ipt vcontent" type="text" name="captcha" data-rule="checkcode" value=""/>
                            <img id="captcha_reg" src="/mainajax/captcha?v=<?php echo time();?>" width="80" height="30" alt="" />
							<a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha_reg">换一张</a>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con tip captcha"></span>
                                <s></s>
                            </div>
						</div>
					</div>
					<!-- 手机语音验证码 -->
                    <div class="form-item form-vcode vcode-yuyin">
                        <label class="form-item-label"><b>*</b>验证码</label>
                        <div class="form-item-con">
                            <input type="text" name="yyCaptcha" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
                            <a href="javascript:;" id='btn-getYzm1' data-freeze='phone' class="lnk-getvcode _timer" target="_self">获取语音验证码</a>
                            <span href="javascript:;" class="lnk-getvcode-disb hide">重新发送(<em class='timer'>60</em>秒)</span>
                            <div class="form-tip hide">
                                <i class="icon-tip"></i>
                                <span class="form-tip-con yyCaptcha tip"></span>
                                <s></s>
                            </div>
                            <div style="left: 0; top: 46px;" class="ui-poptip ui-poptip-yuyin">
                              <div class="ui-poptip-container">
                                <div class="ui-poptip-arrow-top"> <i>◆</i> <span>◆</span> </div>
                                系统将拨打您的手机语音播报验证码，请注意接听。<a href="/help/index/b0-f4">未收到验证码？</a>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" target="_self" href="javascript:;">立即注册</a>
                            <span class="go-reg">已有2345账号，<a href="/main/login">立即登录</a></span>
                        </div>
					</div>
                    <div class="form-item form-agree">
                        <div class="form-item-con">
                            <input class="ipt_checkbox vcontent" type="checkbox" id="a" name="agreement" value="1" checked="checked"/>
                            <label for="a">我同意</label>
                            <a href="http://login.2345.com/licence.html" target="_blank">《服务协议》</a>
                            <a href="http://login.2345.com/declare.html" target="_blank">《隐私声明》</a>
                        </div>
					</div>
                </form>
	        </div>
		</div>
        <div class="pop-other-login">
            <div class="other-login">
                <a class="btn btn-qq" target="_self"
                   href="http://login.2345.com/qq?forward=<?php echo $this->baseUrl; ?>main/loginReCall?rewrite=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>"><i
                        class="icon icon-qq"></i>QQ账号登录</a>
                <a class="btn btn-weibo"
                   href="http://login.2345.com/weibo?forward=<?php echo $this->baseUrl; ?>main/loginReCall?rewrite=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>"><i
                        class="icon icon-weibo"></i>微博登录</a>
            </div>
        </div>
	</div>
</div>
<!-- 注册 end -->
<script type="text/javascript">
$(function() {
    $('.pop-register-form input[name="username"]').blur(function() {
        var pattern = /^1\d{10}$/;
        var pattern1 = /^\w+[\w]*@[\w]+\.[\w]+$/;
        var username = $('.pop-register-form input[name="username"]').val();
        var popRegisterForm = $(this).parents('.pop-register-form');
        var vcodeYuyin = popRegisterForm.find(".vcode-yuyin");
        var vcodeImg = popRegisterForm.find(".vcode-img");
        if(pattern.test(username)){
            vcodeYuyin.show();
            popRegisterForm.find('input[name="actions"]').val('phone');
            vcodeYuyin.find('input[name="captcha"]').val('');
        }else if(pattern1.test(username)){
            vcodeYuyin.hide();
            vcodeImg.show();
            popRegisterForm.find('input[name="actions"]').val('email');
            vcodeYuyin.find('input[name="yyCaptcha"]').val('');
        }else{
            vcodeYuyin.hide();
            vcodeImg.show();
            popRegisterForm.find('input[name="actions"]').val('default');
            vcodeYuyin.find('input[name="yyCaptcha"]').val('');
        }
    });
	$('#btn-getYzm1').click(function(){
		var self = $(this);
        var phone = $('.pop-register-form input[name="username"]').val();
        if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/))
        {
        	if($(".username").parent().hasClass('form-tip-error')){
                return false;
            }
        	var code = $('.pop-register-form input[name="captcha"]').val() || false;
        	if(!code){
        		$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
            	$('.captcha').show().html('请输入图形验证码');
            	return false;
            }
        	
            $.ajax({
                type: 'post',
                url:  '/main/getPhoneCode/phoneCaptcha',
                data: {'phone':phone,'code':code},
                dataType: 'json',
                success: function(response) {
                    if(response.status)
                    {
                    	timer2(self);
                        //cx.Alert({content:'验证码已发送你的手机！'});
                    }
                    else
                    {
                        if(response.msg){
                        	$('.captcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                        	$('input[name="captcha"]').val('');
                        	$('.captcha').show().html(response.msg);
                        	$("#change_captcha_reg").triggerHandler("click");
                        }else{
                        	$('.captcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                        	cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                            closeTimer(1);
                        }
                    }
                }
            });
        }
    });
	$('.not-register').click(function() {
		var $this = $(this);
		if ($this.hasClass('not-register')) {
			cx.PopRegister.show();
		}
	});	

	new cx.vform('.form_register', {
		renderTip: 'renderTips',
        submit: function(data) {
            if(data.agreement != '1'){
                cx.Alert({
                        content: '请同意2345服务协议'
                });
                return false;
            }
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/main/register',
                data: data,
                success: function(response) {
                	recaptcha_reg();
                	if(response.code == '200.0'){
                        $('.not-login').removeClass('not-login');
                        $(".pop-mask").hide();
                        cx.PopRegister.hide();

                        $.get('/main/getWelcome', function(data){
                            $('#pop_welcome').html(data);

                            cx.PopWelcome = (function() {
                                var me = {};
                                var $wrapper = $('.welcomePopWrap');

                                $wrapper.find('.pop-close').click(function() {
                                    $wrapper.hide();
                                    cx.Mask.hide();
                                });

                                me.show = function() {
                                    cx.Mask.show();
                                    $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
                                };

                                me.hide = function() {
                                    $wrapper.hide();
                                    cx.Mask.hide();
                                };

                                return me;
                            })();

                            cx.PopWelcome.show();
                        });

                        $.get('/main/getTopBar', function(data){
                            $('.top_bar').html(data);
                        });

                        if( $('.fast-login') ){
                            $.get('/main/getFastLogin', function(data){
                                if(data){
                                    $('.fast-login').html(data);
                                }
                            });
                        }

                        // 查验是否已经绑定信息
                        $.get('/main/getBindPop', function(data){
                            if(data){
                                $('.submit').addClass('not-bind');
                                $('.do-cast').addClass('not-bind');
                                $('#pop_bind').html(data);
                                cx.PopBind = (function() {
                                    var me = {};
                                    var $wrapper = $('.bind-form');

                                    $wrapper.find('.pop-close').click(function() {
                                        $wrapper.hide();
                                        cx.Mask.hide();
                                    });

                                    $wrapper.find('.cancel').click(function() {
                                        $wrapper.hide();
                                        cx.Mask.hide();
                                    });

                                    me.show = function() {
                                        cx.Mask.show();
                                        $wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
                                        $wrapper.find('input[type="text"],input[type="password"]').val('');
                                        $wrapper.find('input[type="text"]').get(0).focus();
                                    };

                                    me.hide = function() {
                                        $wrapper.hide();
                                        cx.Mask.hide();
                                    };

                                    return me;
                                })();
                            }
                        });

                	}
                	else if(response.werror == 1){
                		console.log(response);
                    }
                	else if(response.captcha == 1){
                		if(response.actions == 'phone'){
                    		self.renderTip('您输入的验证码有误，请重新输入', $('.yyCaptcha'));
                    		$('.vcode-yuyin input[name="yyCaptcha"]').val('');
                    		//$('input[name="yyCaptcha"]').focus();
                        }else{
                        	self.renderTip('您输入的验证码有误，请重新输入', $('.captcha'));
                    		//$('input[name="captcha"]').focus();
                        }
                    }
                	else if(response.code == '300.6'){
                        self.renderTip('此帐号已被注册', $('.username'));
                    }
                	else if(response.code == '300.7'){
                        self.renderTip('此邮箱已被注册，请换一个', $('.email'));
                    }
                	else if(response.code == '300.3'){
                        self.renderTip('此帐号已被注册', $('.username'));
                    }
                	else if(response.field.pcheck == false){
                		self.renderTip('两次输入不一致', $('.con_pword'));
                		$('.captcha').closest('.form-tip').addClass('hide');
                    }
                	else{
						console.log(response);
                    }
                    $('input[name="captcha"]').val('');
                }
            });
        }
    });

    $("#change_captcha_reg").on('click', function(){
        recaptcha_reg();
        return false;
    });
})
</script>