<?php $this->load->view('elements/common/header_notlogin');?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
function showCaptche(flag)
{
	if(flag) {
		$('#captcha_area').show();
   	} else{
		$('#captcha_area').hide();
	}
}

$(function(){
	new cx.vform('#form_login', {
		renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/main/login',
                data: data,
                success: function(response) {
            		showCaptche($.cookie('needCaptcha'));
                	recaptcha();
                	if(response.werror == 1){
                		console.log(response);
                    }
                	else if(response.captcha == 1){
                        self.renderTip('验证码错误', $('.form-tip-error'));
                		$('input[name="captcha"]').focus();
                    }
                	else if(response.code){
                        if(response.code == 1){
                		    location.href =  response.locat;
                	    } else if( response.code == 3 ){
                            $('#captcha_area').show();
                        } else if( response.code == -1 ){
                            self.renderTip('2345账号或密码错误', $('.form-tip-error'));
                            $('input[name="username"]').focus();
                        } else if( response.code == -2 ){
                        	self.renderTip('请输入用户名或密码', $('.form-tip-error'));
                        } else if( response.code == 4 ){
                            alert('非法ip');
                        } else if( response.code == 5 ){
                            alert('非法域名');
                        } else if( response.code == 6 ){
                            alert('登录太频繁，IP被限制');
                        }
                    }

                    $('input[name="captcha"]').val('');
                }
            });
        }
    });
    
    $('#change_captcha').on('click', function(){
        recaptcha();
        return false;
    });
    showCaptche($.cookie('needCaptcha'));
})
</script>
<div class="wrap_in">
	<div class="lay_login">
		<div class="lay_login_hd">
			<h2>登录</h2>
		</div>
		<div class="lay_login_bd">
			<div class="mod_user">			
					<form action="" class="form form_login form_register" id="form_login">
						<div class="form-item">
							<input class='vcontent' type='hidden' name='actions' value='1' />
							<label for="" class="form-item-label">2345账号</label>
							<div class="form-item-con">
								<input  tabindex="1" type="text" class='form-item-ipt vcontent' autocomplete="off" name="username" value="" />
							</div>
						</div>
						<div class="form-item">
							<label for="" class="form-item-label">密码</label>
							<div class="form-item-con">
								<input tabindex="2" type="password" class='form-item-ipt vcontent' name="pword" data-encrypt='1' value="" />
								<a href="http://login.2345.com/find?type=password&forward=http://caipiao.2345.com" target="_blank" class="lnk-txt">忘记密码？</a>
							</div>
						</div>
						<div class="form-item form-vcode" id="captcha_area">
							<label for="" class="form-item-label">验证码</label>
							<div class="form-item-con">
								<input tabindex="3" class="form-item-ipt vcontent inp_s" type="text" name="captcha" value="" /><img id='captcha' src="/mainajax/captcha?v=<?php echo time();?>" alt="" />
								<a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha">换一张</a>
							</div>
						</div>
						<div class="form-item">
	                        <div class="form-item-con form-tip-error"></div>
	                    </div>
						<div class="form-item btn-group">
							<div class="form-item-con">
								<a class="btn btn-confirm-large submit" href="javascript:;" target="_self">立即登录</a>
								<span class="go-reg">还没有2345账号，<a href="/main/register">立即注册</a></span>
							</div>
						</div>
                        <div class="form-item other-login">
                        	<div class="form-item-con">
                        		<a class="btn btn-qq" target="_self" href="http://login.2345.com/qq?forward=<?php echo $this->pagesUrl; ?>main/loginReCall"><i class="icon icon-qq"></i>QQ账号登录</a>
                        		<a class="btn btn-weibo" href="http://login.2345.com/weibo?forward=<?php echo $this->pagesUrl;?>main/loginReCall"><i class="icon icon-weibo"></i>微博登录</a>
                        	</div>
                        </div>
					</form>
			</div>
		</div>
		<div class="act-total">
			<div class="money-num"><em><?php echo implode('</em><em>', $total_win);?></em>万元</div>
		</div>
	</div>
</div>
<?php $this->load->view('elements/common/footer_short');?>
