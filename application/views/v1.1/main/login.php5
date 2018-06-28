<div class="wrap_in l-concise l-concise-col">
	<div class="l-concise-bd login">
		<div class="l-concise-main">
			<form class="form form-login">
	<div class="form-item">
		<label class="form-item-label" for="username">手机号/用户名：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" id="username" name="username" name="username" type="text" autocomplete="off" value="" />
		</div>
	</div>
	<div class="form-item">
		<label class="form-item-label" for="pword">密码：</label>
		<div class="form-item-con">
			<input class="form-item-ipt vcontent" id="pword" name="pword" data-encrypt='1' type="password" value="" />
			<a href="/safe/findPword" class="lnk-txt">忘记密码？</a>
		</div>
	</div>
	<div class="form-item" id="captcha_area">
		<div class="form-item-con">
			<div class="captcha_div"></div>
		</div>
	</div>
	<div class="form-item">
		<div class="form-item-con">
			<div class="form-tip-bar form-tip hide">
				<i class="icon-tip"></i>
				<span class="form-tip-con"></span>
				<s></s>
			</div>
		</div>
	</div>
	<div class="form-item btn-group">
		<div class="form-item-con">
			<a class="btn btn-main btn-register submit" href="javascript:;">立即登录</a>
			<a href="/main/register" class="lnk-txt">立即注册</a>
		</div>
	</div>
</form>
		</div>
		<?php $this->load->view('v1.1/elements/common/appdownload');?>
	</div>
</div>
<script type="text/javascript">
var validate = '',  verify = function(err, ret){
	username = $('.form-login [name="username"]').val();
	if (username === '') {
		$('.form-register [name="username"]').trigger('blur');
		initne({}, verify);
    	return;
	}
    if(!err) {
        validate = ret.validate;
        $('#btn-getYzm').removeClass('btn-disabled');
    }
}
$(function(){
	showCaptche($.cookie('needCaptcha'));
	console.log(cx);
	new cx.vform('.form-login', {
		renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            if(data.username == '' || data.pword == ''){
            	self.renderTip('请输入用户名或密码', $('.form-tip-con'));
                return false;
            }
            data.validate = validate;
            $.ajax({
                type: 'post',
                url:  '/mainajax/login',
                data: data,
                success: function(response) {
                	showCaptche($.cookie('needCaptcha'));
                	if(response.code == 0){
                        //登录成功
                		location.href =  response.url;
                    }else{
                        //登录失败
                        if(response.code ==1){
                        	self.renderTip('用户名或密码错误', $('.form-tip-con'));
                        }else{
                        	self.renderTip(response.msg, $('.form-tip-con'));
                        }
                    }
                    $('input[name="captcha"]').val('');
                }
            });
        }
    });
    showCaptche($.cookie('needCaptcha'));
})
function showCaptche(flag) {
	if(flag) {
		initne({}, verify);
		$('#captcha_area').removeClass('hide');
   	} else{
   		$('#captcha_area').addClass('hide');
	}
}
</script>