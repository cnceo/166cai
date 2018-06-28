<div class="wrap_in l-concise l-concise-col">
	<div class="l-concise-bd form-user-info">
		<div class="l-concise-main">
			<div class="form form-bindEamil">
			<div class="form-tips-bar"><i></i>温馨提示：绑定邮箱后可第一时间获取出票成功凭证</div>
				<div class="form-item">
					<label class="form-item-label">邮箱地址：</label>
					<div class="form-item-con">
						<input class="form-item-ipt vcontent" type="text" name="email" data-ajaxcheck='1' data-rule="email" value="" />
						<div class="form-tip">
							<i class="icon-tip"></i>
							<span class="form-tip-con tip email">请输入正确的邮箱地址</span>
							<s></s>
						</div>
					</div>
				</div>
				<div class="form-item form-vcode">
					<label class="form-item-label">验证码：</label>
					<div class="form-item-con">
						<input type="text" value="" class="form-item-ipt vyzm vcontent" data-rule="checkecode" name="emailCaptcha">
						<a href="javascript:;" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer" target="_self">获取验证码</a>
            			<span href="javascript:;" class="lnk-getvcode-disabled hide">重新发送(<em id='_timer'>60</em>秒)</span>
						<div class="form-tip hide">
							<i class="icon-tip"></i>
							<span class="form-tip-con tip emailCaptcha">请输入验证码</span>
							<s></s>
						</div>
					</div>
				</div>
				<div class="form-item email-tips hide">
  					<p class="form-tip form-tip-true"></p>
  				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<input type='hidden' class='vcontent' id="actiontype" name='actiontype' value='_1'>
						<a class="btn btn-main submit" href="javascript:;">下一步</a>
					</div>
				</div>
			</div>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<script type="text/javascript">
$(function() {
	$('#btn-getYzm').click(function(){
		var self = $(this);
        var email = $('.form-bindEamil input[name="email"]').val();
        $('.emailCaptcha').closest('.form-tip').removeClass('form-tip-error');
        $('.email-tips').addClass('hide');
        if(!email){
        	$('.email').closest('.form-tip').removeClass('hide').addClass('form-tip-error');
        	$('.email').show().html('请输入正确的邮箱地址');
        	return false;
        }
        var emailErr = $('.email').closest('.form-tip').hasClass('form-tip-error');
        if(!emailErr)
        {   
        	timer(self);
            $.ajax({
                type: 'post',
                url:  '/safe/getEmailCode/',
                data: {'email':email},
                dataType: 'json',
                success: function(response) {	
                    if(response.status){
                    	var mailObj = {'126.com':'mail.126.com', '163.com':'mail.163.com', 'yeah.net':'yeah.net', 'qq.com':'mail.qq.com', 'sina.com':'mail.sina.com.cn', 'sina.cn':'mail.sina.com.cn', '139.com':'mail.10086.cn', 'google.com':'mail.google.com', 'sohu.com':'mail.sohu.com', '189.cn':'webmail30.189.cn', 'aliyun.com':'mail.aliyun.com', 'outlook.com':'www.outlook.com', 'live.com':'www.live.com', 'msn.com':'mail.msn.com', 'hotmail.com':'www.hotmail.com', 'tom.com':'mail.tom.com', 'sogou.com':'mail.sogou.com', '2980.com':'www.2980.com', '21cn.com':'mail.21cn.com', '188.com':'www.188.com', 'wo.cn':'mail.wo.cn', '263.net':'www.263.net', 'aol.com':'mail.aol.com', 'mail.com':'mail.com'};
                    	mail = email.split("@");
                    	var html;
                    	if(mail[1] in mailObj){
                    		html = '<i class="icon-tip"></i>邮件已发送，立即<a href="http://' + mailObj[mail[1]] +'" target="_blank">前往邮箱</a>，获取验证码';
                        }else{
                        	html = '<i class="icon-tip"></i>邮件已发送，立即前往邮箱，获取验证码（您的邮箱暂不支持快捷登录，请手动前往）';
                        }
                        $('.email-tips p').html(html);
                        $('.email-tips').removeClass('hide');
                    }else{
                    	cx.Alert({content:response.msg});
                    	closeTimer(1);
                    }
                }
            });
        }
    });
	new cx.vform('.form-bindEamil', {
		renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/safe/bindEmail',
                dataType: 'json',
                data: data,
                success: function(response) {
	                if(response.status == '0'){
	                	location.href =  '/safe/bindEmailSucc/';
                    }else if(response.status == '2'){
                    	self.renderTip(response.msg, $('.email'));
                    }else if(response.status == '3'){
                    	self.renderTip(response.msg, $('.emailCaptcha'));
                    }else{
                    	cx.Alert({content:response.msg});
                    }
                }
            });
        }
    });
});
</script>
