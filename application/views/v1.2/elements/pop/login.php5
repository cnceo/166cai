<!-- 登录 start -->
<div class="pub-pop pop-login">
	<div class="pop-in">
		<div class="pop-head">
			<h2>166彩账号登录</h2>
			<span class="pop-close" title="关闭">&times;</span>
		</div>
		<div class="pop-body">
			<form class="form form-login">
				<div class="form-tip-bar form-tip form-tip-error hide">
					<i class="icon-tip"></i>
					<span class="form-tip-con"></span>
					<s></s>
				</div>
				<div class="form-item">
					<label class="form-item-label" for="username"><i class="icon-font">&#xe636;</i></label>
					<div class="form-item-con">
						<input class="form-item-ipt vcontent" name="username" type="text" autocomplete="off" placeholder="手机号/用户名" c-placeholder="手机号/用户名" value="" />
					</div>
				</div>
				<div class="form-item">
					<label class="form-item-label" for="pword"><i class="icon-font">&#xe635;</i></label>
					<div class="form-item-con">
						<input class="form-item-ipt vcontent" name="pword" type="password" data-encrypt='1' placeholder="密码" value="" />
					</div>
				</div>
				<div class="form-item" id="captcha_area">
				    <div class="form-item-con">
				        <div class="captcha_div"></div>
				    </div>
				</div>
				<div class="form-item lnk-group">
					<a href="/safe/findPword" target="_blank" class="lnk-txt">忘记密码</a>
					<a href="/main/register?dltc" target="_blank" class="lnk-txt fr">立即注册</a>
				</div>
				<div class="form-item btn-group">
					<div class="form-item-con">
						<a class="btn btn-main btn-login submit" href="javascript:;" target="_self">立即登录</a>
					</div>
				</div>
			</form>
			<div class="other-login">
                <div class="other-login-hd">
                    <div class="other-login-title">免注册直接登录</div>
                </div>
                <div class="other-login-bd">
                    <a onclick="_hmt.push(['_trackEvent', 'login', 'weixin_button']);" href="<?php echo $qrbLogin; ?>" target="_blank" title="微信" class="icon-font"></a>
                </div>
            </div>
		</div>
	</div>
</div>
<!-- 登录 end -->
<script>
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
	$('.pop-login').on('click keyup', "input[name='username'],input[name='pword']", function(){
		if ($(this).val().replace(/(^\s*)|(\s*$)/g,'') === '') 
			$(".form-tip-bar").addClass('form-tip-phone').removeClass('hide').html("<i class='icon-tip'></i><span class='form-tip-con'><i class='icon-font'>&#xe61b;</i>支持手机号码登录</span><s></s>");
	})
	showCaptche($.cookie('needCaptcha'));
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
                    	cx.PopCom.hide('.pop-login');
                    	$('.not-login').removeClass('not-login');

                    	if (response.udata.iseml) $('.bind_email').remove();
                    	//在线客服
                    	if ('undefined' != typeof visitor) {
                    		$.cookie('38338', null);
                    		 easemobim.config.visitor = {userNickname:response.udata.uname};
                    	}
                        
                    	$.get('/mainajax/getLoginAjax?version=' + version, function(data){

                        	<?php if ($rfsh) {?>
                            location.reload();
                        	<?php }?>
                        	
                        	$('.top_bar').html(data.topBar);
                        	// 获取其绑定情况
                            if(data.bindPop) $('.submit, .btn-betting, .btn-hemai, .btn-buy').addClass('not-bind');
                            
                            $(".btn-search").removeClass('not-bind');
                            
                            <?php if($tigger):?>
                            $('body').find('.needTigger').trigger('click').removeClass('needTigger');
                            <?php endif;?>
                        },'json');
                    }else{
                        //登录失败
                        $(".form-tip-bar").removeClass('form-tip-phone');
                    	self.renderTip(response.msg, $('.form-tip-con'));
                    }
                }
            });
        }
    });

	

	function showCaptche(flag){
		if(flag){  
			initne({}, verify);
			$('#captcha_area').removeClass('hide');
	   	}else{
			$('#captcha_area').addClass('hide');
		}
	}
	
});

</script>