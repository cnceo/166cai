<div class="lay-find">
	<div class="hd"><h2 class="tit">找回密码</h2></div>
	<div class="bd">
		<ul class="steps-bar clearfix">
			<li><i>1</i><span class="des">输入2345账号</span></li>
			<li class="cur"><i>2</i><span class="des">验证身份</span></li>
			<li><i>3</i><span class="des">设置密码</span></li>
			<li class="last"><i>4</i><span class="des">操作成功</span></li>
		</ul>
		<?php if($is_register == 1):?>
		<div class="find-form">	
			<form class="form">
				<div class="form-item">
					<label class="form-item-label">验证方式</label>
					<div class="form-item-con">
						<dl class="simu-select-med" data-target="modify_mode">
				            <dt><?php if($phone): ?>手机号码<?php elseif($email): ?>邮箱<?php endif; ?><i class="arrow"></i></dt>
				            <dd class="select-opt">
				            	<div class="select-opt-in">
				            		<?php if($phone): ?>
					            	<a href="javascript:;" data-value="phoneType">手机号码</a>
					            	<?php endif; ?>
					            	<?php if($email): ?>
					            	<a href="javascript:;" data-value="emailType" >邮箱</a>
					            	<?php endif; ?>
					            </div>
				            </dd>
				        </dl>
					</div>
				</div>
				<?php if($phone): ?>
				<div class="form-item phone-submit" >
					<label class="form-item-label">手机号码</label>
					<div class="form-item-con"><span class="form-item-txt"><?php echo $phone; ?></span></div>
					<input type='hidden' class='' id="" name='real_phone' value='<?php echo $real_phone; ?>'>
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
				<div class="form-item phone-submit form-vcode">
					<label class="form-item-label">验证码</label>
					<div class="form-item-con">
						<input type="text" name="newphoneyzm" value="" data-rule="checkcode" class="form-item-ipt vyzm vcontent">
						<a href="javascript:;" class="lnk-getvcode _timer" id="btn-getYzm">获取语音验证码</a>
                        <span class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                        <div class="form-tip hiede form-tip-error">
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
						<a class="btn btn-confirm submit" id="change-submit-name" href="javascript:;">确认</a>
						<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_2'>
						<input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='phoneType'>
					</div>
				</div>				
				<?php endif; ?>
				<?php if($email): ?>
				<div class="form-item email-submit" <?php if($phone): ?>style="display:none;"<?php endif; ?>>
					<label class="form-item-label">邮箱</label>
					<div class="form-item-con"><span class="form-item-txt"><?php echo $email; ?></span></div>
					<input type='hidden' class='' id="" name='hide_email' value='<?php echo $email; ?>'>
					<input type='hidden' class='' id="" name='email' value='<?php echo $real_email; ?>'>
				</div>
				<div class="form-item btn-group email-submit" <?php if($phone): ?>style="display:none;"<?php endif; ?>>
					<div class="form-item-con">
						<a class="btn btn-confirm submit" id="change-submit-name" href="javascript:;"><?php if($phone): ?>确认<?php elseif($email): ?>发送验证邮件<?php endif; ?></a>
						<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_2'>
						<input type='hidden' class='vcontent' id="channeltype" name='channeltype' value='emailType'>
					</div>
				</div>				
				<?php endif; ?>		
			</form>
			<p class="spc-tip">不能通过以上验证方式找回？<a href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes" target="_blank">联系客服</a></p>
		</div>
		<?php elseif($is_register == 0):?>
		<div class="find-form">
			<div class="find-success" style="text-align:center;padding-left:0px">
				<div class="sc-tip">此帐号尚未绑定任何个人信息，您可以联系客服提供其他信息找回密码！</div>
				<p style="padding-left:0px"><a href="http://wpa.qq.com/msgrd?v=3&amp;uin=2584565084&amp;site=qq&amp;menu=yes" class="btn btn-blue-med">联系客服</a></p>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/base.js');?>'></script>
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
		
		//发送验证码
		$('#btn-getYzm').click(function(){
			var phone = $('input[name="real_phone"]').val();
			if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/) )
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

		//验证提交
		new cx.vform('.phone-submit', {
	        renderTip: 'renderTips',
	        submit: function (data) {
				before_check(data);
				$.ajax({
		            type: 'post',
		            url: '/safe/find_password',
		            data: data,
		            success: function (response) {
		                if(response == '001'){
		                	cx.Alert({content:'系统异常！'});
                        }
                        else if(response == '002'){
                        	recaptcha_reg();
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
                        	cx.Alert({content:'2345账号不存在！'});
                        }
                        else if(response == '005'){
                        	cx.Alert({content:'登录密码不能与支付密码一致！'});
                        }
                        else if(response == '006'){
	                        cx.Alert({content:'登录密码不正确！'});
	                    }
                        else{
                            $('.other-container').html(response);
                        }
		            }
		        });
	        }
	    });

		new cx.vform('.email-submit', {
	        renderTip: 'renderTips',
	        submit: function (data) {
				before_check(data);
				$.ajax({
		            type: 'post',
		            url: '/safe/find_password',
		            data: data,
		            success: function (response) {
		                if(response == '001'){
		                	cx.Alert({content:'系统异常！'});
                        }
                        else if(response == '002'){
                        	recaptcha_reg();
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
                        	cx.Alert({content:'2345账号不存在！'});
                        }
                        else if(response == '005'){
                        	cx.Alert({content:'登录密码不能与支付密码一致！'});
                        }
                        else if(response == '006'){
	                        cx.Alert({content:'登录密码不正确！'});
	                    }
                        else{
                            $('.other-container').html(response);
                        }
		            }
		        });
	        }
	    });

	    $(".pop-close").on('click', function(){
	    	$('.pop-alert-success').hide();;
		});

	    //data check before post
		function before_check(data){
			if(data.actiontype == '_1'){
				if(data.username == ''){
					return false;
				}
				if(data.yz == ''){
					return false;
				}
				checkname(data.username);
			}
		}

	})    

</script>


