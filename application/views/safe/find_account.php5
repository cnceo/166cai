<?php $this->load->view('elements/common/header_notlogin');?>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/other.css'); ?>"/>
<!-- body -->
<div class="wrap_in other-container">
	<div class="lay-find">
		<div class="hd"><h2 class="tit">找回2345帐号</h2></div>
		<div class="bd">
			<div class="find-form">	
				<form actin="" class="form find-form-submit">
					<div class="form-item">
						<label class="form-item-label">验证方式</label>
						<div class="form-item-con">
							<dl class="simu-select-med" data-target="modify_mode">
					            <dt>手机号码<i class="arrow"></i></dt>
					            <dd class="select-opt">
					            	<div class="select-opt-in">
						            	<a href="javascript:;" data-value="phoneType">手机号码</a>
						            	<a href="javascript:;" data-value="emailType" >邮箱</a>
						            </div>
					            </dd>
					        </dl>
						</div>
					</div>					
					<div class="form-item phone-submit" >
						<label class="form-item-label">手机号码</label>
						<div class="form-item-con">
							<input type="text" name="phone" value="" class="form-item-ipt vcontent" style="" data-rule='phonenum' data-freeze='_timer'>
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
					<div class="form-item phone-submit form-vcode">
						<label class="form-item-label">验证码</label>
						<div class="form-item-con">
							<input type="text" name="newphoneyzm" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
							<a href="javascript:;" class="lnk-getvcode _timer" id="btn-getYzm">获取语音验证码</a>
							<span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
							<div class="form-tip form_tips_error hide">
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
					<div class="form-item btn-group phone-submit">
						<div class="form-item-con">
							<a class="btn btn-confirm submit" href="javascript:;">确认</a>
							<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
							<input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='phoneType'>
						</div>
					</div>
					<div class="form-item email-submit" style="display:none;">
						<label class="form-item-label">邮箱</label>
						<div class="form-item-con">
							<input type="text" data-rule='email' name="email" value="" class="form-item-ipt vcontent" style="">
							<div class="form-tip hide">
	    						<i class="icon-tip"></i>
		    					<span class="form-tip-con email tip"></span>
		    					<s></s>
		    				</div>
						</div>
					</div>
					<div class="form-item btn-group email-submit" style="display:none;">
						<div class="form-item-con">
							<a class="btn btn-confirm submit" href="javascript:;">确认</a>
							<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
							<input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='emailType'>
						</div>
					</div>
				</form>
				<p class="spc-tip">不能通过以上验证方式找回？<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes" target="_blank">联系客服</a></p>
			</div>
		</div>
	</div>
	<!-- <div class="pub-pop pop-alert" style="margin-top: -88px; margin-left: -200px; display: none;">
		<div class="pop-in">
			<div class="pop-head">
				<h2>提示</h2>
				<span title="关闭" class="pop-close">关闭</span>
			</div>
			<div class="pop-body">
				<div class="mail-alert ">
					您的帐号已发送至邮箱:
					<p class="cBlue2 fz18 yemail">*******@email.com</p>
					请登录邮箱查收您的帐号。
				</div>
			</div>
			<div class="pop-action-area tac">
				<a class="btn btn-confirm gotoemail" target="_blank" href="javascript:;">去邮箱收信</a>
			</div>
		</div>
	</div> -->
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
<!--[if IE 6]>
<script src="/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->

<script type="text/javascript">
	$(function(){
    	$("#change_imgCaptcha").on('click', function(){
    		$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
    		return false;
        });
		var selectDt = $('.simu-select-med').find('dt');
        $('.select-opt-in').find('a').on('click', function(){  
            selectDt.attr('data-value', $(this).attr('data-value'));
            selectDt.html($(this).html() + '<i class="arrow"></i>');
            if(selectDt.attr('data-value') == 'phoneType'){
                $('.email-submit').hide();
                $('.phone-submit').show();
            }
            else if(selectDt.attr('data-value') == 'emailType'){
                $('.phone-submit').hide();
                $('.email-submit').show();
            }
        })

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
                    url:  '/safe/getPhoneCode/<?php if (!empty($this->uinfo['phone'])): ?>oldphoneyzm<?php else: ?>newphoneyzm<?php endif; ?>',
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
                            	closeTimer(1);
                            }                           
                        }
                    }
                });
            }
        });


		new cx.vform('.phone-submit', {
	        renderTip: 'renderTips',
	        submit: function (data) {
				$.ajax({
		            type: 'post',
		            url: '/safe/find_account',
		            data: data,
		            success: function (response) {
		                if(response == '001'){
		                	cx.Alert({content:'手机号码或者邮箱异常！'});
                        }else if(response == '002'){
                        	$('input[name="newphoneyzm"]').focus();
                        	cx.Alert({content:'验证码不正确！'});
                        }else if(response == '003'){
                        	// var email = $('input[name="email"]').val();
                        	// $(".yemail").text(email);
                         	// var patch = email.split("@"); 
                         	// $(".gotoemail").attr("href",'http://mail.'+patch[1]);
                        	// $('.pop-alert').show();
                        	var email = $('input[name="email"]').val();
                            var patch = email.split("@"); 
                            var email_patch = 'http://mail.'+patch[1];
                        	cx.Alert({
                        		confirm:'去邮箱收信',
                        		content:'<div class="mail-alert ">您的帐号已发送至邮箱:<p class="cBlue2 fz18 yemail">'+ email +'</p>登录邮箱查收您的帐号。</div>',
                        		confirmCb:function(){
                        			window.open(email_patch);
                        			// location.href = ;
                        			return false;
                        	}});
                        }else if(response == '004'){
                        	cx.Alert({content:'手机号码不存在！'});         	
                        }else if(response == '005'){
                        	cx.Alert({content:'邮箱不存在！'}); 
                        }else{
                            $('.other-container').html(response);
                        }
		            }
		        });
	        }
	    });

		new cx.vform('.email-submit', {
	        renderTip: 'renderTips',
	        submit: function (data) {
				$.ajax({
		            type: 'post',
		            url: '/safe/find_account',
		            data: data,
		            success: function (response) {
		                if(response == '001'){
		                	cx.Alert({content:'手机号码或者邮箱异常！'});
                        }else if(response == '002'){
                        	$('input[name="newphoneyzm"]').focus();
                        	cx.Alert({content:'验证码不正确！'});
                        }else if(response == '003'){
                        	var email = $('input[name="email"]').val();
                            var patch = email.split("@"); 
                            var email_patch = 'http://mail.'+patch[1];
                        	cx.Alert({
                        		confirm:'去邮箱收信',
                        		content:'<div class="mail-alert ">您的帐号已发送至邮箱:<p class="cBlue2 fz18 yemail">'+ email +'</p>登录邮箱查收您的帐号。</div>',
                        		confirmCb:function(){
                        			window.open(email_patch);
                        			// location.href = ;
                        			return false;
                        	}});
                        }else if(response == '004'){
                        	cx.Alert({content:'手机号码不存在！'});          	
                        }else if(response == '005'){
                        	cx.Alert({content:'邮箱不存在！'}); 
                        }else{
                            $('.other-container').html(response);
                        }
		            }
		        });
	        }
	    });

	    //刷新验证码
		$("#change_captcha_reg").on('click', function(){
	    	recaptcha_reg();
	    	return false;
		});

		$(".pop-close").on('click', function(){
	    	$('.pop-alert').hide();;
		});

	})
</script>
<!-- body -->
</div>
<?php $this->load->view('elements/common/footer_short');?>

