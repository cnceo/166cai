<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<!-- 登录 begin -->
<div class="pub-pop loginPopWrap">
	<div class="pop-in">
		<div class="pop-head">
			<h2>登录</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body pop-login-form">
			<div class="mod_user">
                <form action="" class="form form_login">
                    <div class="form-item">
                        <input class="vcontent" type="hidden" name="actions" value="1" />
                        <label for="" class="form-item-label">2345账号</label>
                        <div class="form-item-con">
                            <input tabindex="1" type="text" class="form-item-ipt vcontent" autocomplete="off" name="username" value="" />
                        </div>
                    </div>
                    <div class="form-item">
                        <label for="" class="form-item-label">密码</label>
                        <div class="form-item-con">
                            <input tabindex="2" type="password" class="form-item-ipt vcontent" autocomplete="off"  name="pword" data-encrypt="1" value="" />
                            <a href="http://login.2345.com/find?type=password&forward=http://caipiao.2345.com" target="_blank" class="lnk-txt">忘记密码？</a>				
                        </div>
                    </div>
                    <div class="form-item form-vcode yz_area" style="display:none;" id="captcha_area">
                        <label for="" class="form-item-label">验证码</label>
                        <div class="form-item-con">
                            <input tabindex="3" class="form-item-ipt vcontent inp_s" type="text" name="captcha" value="" /><img id='captcha' src="/mainajax/captcha" alt="" />
                            <a class="lnk-txt" href="javascript:;" target="_self" id="change_captcha">换一张</a>
                        </div>
                    </div>
                    <div class="form-item">
                        <div class="form-item-con form-tip-error"></div>
                    </div>
                    <div class="form-item btn-group">
                        <div class="form-item-con">
                            <a class="btn btn-confirm submit" href="javascript:;" target="_self">立即登录</a>
                            <span class="go-reg">还没有2345账号，<a href="/main/register">立即注册</a></span>
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
<!-- 登录 end -->
<script>

function showCaptche(flag)
{
	if(flag)
	{  
		$('#captcha_area').show();
   	}
	else{
		$('#captcha_area').hide();
	}
}

$(function() {

	$('.not-login').click(function(e) {
		var $this = $(this);
		if ($this.hasClass('not-login')) {
			cx.PopLogin.show();
            e.stopImmediatePropagation();
		}
        e.preventDefault();
	});	

	new cx.vform('.pop-login-form', {
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
                            cx.PopLogin.hide();
                            $('.not-login').removeClass('not-login');
                            $.get('/mainajax/getLoginAjax', function(data){
                            	$('.top_bar').html(data.topBar);
                            	if( $('.fast-login') ){
                                	$('.fast-login').html(data.fastLogin);
                                }
                            	// 获取其绑定情况
                                if(data.bindPop){
                                	$('.lotteryBetArea .submit').addClass('not-bind');
                                    $('.lotteryBetArea .do-cast').addClass('not-bind');
                                    $('#pop_bind').html(data.bindPop);
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
                            },'json');
                	    } else if( response.code == 3 ){
                            $('#captcha_area').show();
                        } else if( response.code == -1 ){
                            self.renderTip('2345账号或密码输入错误', $('.form-tip-error'));
                            $('input[name="username"]').focus();
                        } else if( response.code == -2 ){
                        	self.renderTip('请输入用户名或密码', $('.form-tip-error'));
                        }else if( response.code == 4 ){
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
});
</script>